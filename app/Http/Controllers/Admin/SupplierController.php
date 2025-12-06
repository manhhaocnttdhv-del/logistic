<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Exports\SuppliersExport;
use App\Imports\SuppliersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $suppliers = $query->latest()->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:suppliers,code',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        Supplier::create($validated);
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Thêm nhà cung cấp thành công.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('products');
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:suppliers,code,' . $supplier->id,
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $supplier->update($validated);
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Cập nhật nhà cung cấp thành công.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->exists() || $supplier->importOrders()->exists()) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Không thể xóa nhà cung cấp đã có dữ liệu liên quan.');
        }
        
        $supplier->delete();
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Xóa nhà cung cấp thành công.');
    }

    public function export()
    {
        return Excel::download(new SuppliersExport, 'danh_sach_nha_cung_cap_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            ['Mã nhà cung cấp', 'Tên nhà cung cấp', 'Người liên hệ', 'Số điện thoại', 'Email', 'Địa chỉ', 'Ghi chú', 'Trạng thái'],
        ];
        
        return Excel::download(new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return []; }
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
                return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']]]];
            }
        }, 'mau_nhap_nha_cung_cap.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new \App\Imports\SuppliersImport;
            
            // Import with batch insert for better performance
            Excel::import($import, $request->file('file'));
            
            $errors = $import->getErrors();
            $failures = $import->getFailures();
            
            // Count how many suppliers were actually created/updated
            $totalSuppliers = \App\Models\Supplier::count();
            
            $message = 'Nhập dữ liệu từ Excel thành công.';
            if (!empty($errors) || !empty($failures)) {
                $errorCount = count($errors) + count($failures);
                $message .= " Có {$errorCount} dòng bị lỗi và đã được bỏ qua.";
            }
            
            \Log::info('Import completed', [
                'errors' => count($errors),
                'failures' => count($failures),
                'total_suppliers' => $totalSuppliers
            ]);
            
            return redirect()->route('admin.suppliers.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Dòng {$failure->row()}: " . implode(', ', $failure->errors());
            }
            \Log::error('Validation error during import', ['failures' => $errorMessages]);
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Lỗi khi nhập dữ liệu: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            \Log::error('Supplier import error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}
