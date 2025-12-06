<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('supplier', 'inventory');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $products = $query->latest()->paginate(15);
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('admin.products.index', compact('products', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $product = Product::create($validated);
        
        // Create inventory record
        $product->inventory()->create([
            'quantity' => 0,
            'last_updated_date' => now(),
        ]);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Thêm hàng hóa thành công.');
    }

    public function show(Product $product)
    {
        $product->load('supplier', 'inventory');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => 'required|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $product->update($validated);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Cập nhật hàng hóa thành công.');
    }

    public function destroy(Product $product)
    {
        if ($product->importOrderItems()->exists() || $product->exportOrderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Không thể xóa hàng hóa đã có trong phiếu nhập/xuất.');
        }
        
        $product->inventory()->delete();
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Xóa hàng hóa thành công.');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'danh_sach_san_pham_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        $headers = [
            ['Mã sản phẩm', 'Tên sản phẩm', 'Đơn vị tính', 'Nhà cung cấp', 'Mô tả', 'Giá', 'Tồn kho tối thiểu', 'Trạng thái'],
        ];
        
        return Excel::download(new class($headers) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function array(): array { return $this->data; }
            public function headings(): array { return []; }
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
                return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '667eea']]]];
            }
        }, 'mau_nhap_san_pham.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new \App\Imports\ProductsImport;
            Excel::import($import, $request->file('file'));
            
            $errors = $import->getErrors();
            $failures = $import->getFailures();
            
            $message = 'Nhập dữ liệu từ Excel thành công.';
            if (!empty($errors) || !empty($failures)) {
                $errorCount = count($errors) + count($failures);
                $message .= " Có {$errorCount} dòng bị lỗi và đã được bỏ qua.";
            }
            
            return redirect()->route('admin.products.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Dòng {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('admin.products.index')
                ->with('error', 'Lỗi khi nhập dữ liệu: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            \Log::error('Product import error: ' . $e->getMessage());
            return redirect()->route('admin.products.index')
                ->with('error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}
