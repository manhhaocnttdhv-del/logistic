<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportOrder;
use App\Models\ImportOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Task;
use App\Services\StaffAssignmentService;
use App\Exports\ImportOrdersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ImportOrderController extends Controller
{
    
    private function generateCode()
    {
        $lastOrder = ImportOrder::latest()->first();
        $number = $lastOrder ? (int)substr($lastOrder->code, -6) + 1 : 1;
        return 'PN' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = ImportOrder::with('supplier', 'creator', 'assignedStaff');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(15);
        return view('admin.import-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        return view('admin.import-orders.create', compact('suppliers', 'staff', 'products', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'source' => 'required|in:supplier,transfer,return,other',
            'from_warehouse' => 'nullable|string|max:255',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'assigned_to' => 'nullable|exists:users,id',
            'import_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);
        
        // Validate supplier_id if source is supplier
        if ($validated['source'] === 'supplier' && empty($validated['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Vui lòng chọn nhà cung cấp khi nguồn nhập là từ nhà cung cấp.'])->withInput();
        }
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            
            // Nếu chọn position cụ thể, tìm nhân viên có position đó
            if (!empty($validated['position'])) {
                $preferredPositions = [$validated['position']];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'import', 2);
            } else {
                // Tự động phân công theo logic mặc định
                $assignedUser = $assignmentService->assignStaffForImport();
            }
            
            if ($assignedUser) {
                $assignedTo = $assignedUser->id;
            }
        }
        
        $order = ImportOrder::create([
            'code' => $this->generateCode(),
            'supplier_id' => $validated['supplier_id'] ?? null,
            'source' => $validated['source'],
            'from_warehouse' => $validated['from_warehouse'] ?? null,
            'created_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'import_date' => $validated['import_date'],
            'status' => $assignedTo ? 'pending' : 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);
        
        foreach ($validated['items'] as $item) {
            ImportOrderItem::create([
                'import_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('admin.import-orders.show', $order)
            ->with('success', 'Tạo phiếu nhập thành công.' . ($assignedTo ? ' Đã tự động phân công cho nhân viên.' : ''));
    }

    public function show(ImportOrder $importOrder)
    {
        $importOrder->load('supplier', 'creator', 'assignedStaff', 'confirmer', 'items.product');
        return view('admin.import-orders.show', compact('importOrder'));
    }

    public function edit(ImportOrder $importOrder)
    {
        if ($importOrder->status !== 'pending') {
            return redirect()->route('admin.import-orders.show', $importOrder)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu nhập ở trạng thái chờ xử lý.');
        }
        
        $suppliers = Supplier::where('is_active', true)->get();
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        $importOrder->load('items.product');
        
        return view('admin.import-orders.edit', compact('importOrder', 'suppliers', 'staff', 'products', 'positions'));
    }

    public function update(Request $request, ImportOrder $importOrder)
    {
        if ($importOrder->status !== 'pending') {
            return redirect()->route('admin.import-orders.show', $importOrder)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu nhập ở trạng thái chờ xử lý.');
        }
        
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'source' => 'required|in:supplier,transfer,return,other',
            'from_warehouse' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'import_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);
        
        // Validate supplier_id if source is supplier
        if ($validated['source'] === 'supplier' && empty($validated['supplier_id'])) {
            return back()->withErrors(['supplier_id' => 'Vui lòng chọn nhà cung cấp khi nguồn nhập là từ nhà cung cấp.'])->withInput();
        }
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            
            // Nếu chọn position cụ thể, tìm nhân viên có position đó
            if (!empty($validated['position'])) {
                $preferredPositions = [$validated['position']];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'import', 2);
            } else {
                // Tự động phân công theo logic mặc định
                $assignedUser = $assignmentService->assignStaffForImport();
            }
            
            if ($assignedUser) {
                $assignedTo = $assignedUser->id;
            }
        }
        
        $importOrder->update([
            'supplier_id' => $validated['supplier_id'] ?? null,
            'source' => $validated['source'],
            'from_warehouse' => $validated['from_warehouse'] ?? null,
            'assigned_to' => $assignedTo,
            'import_date' => $validated['import_date'],
            'notes' => $validated['notes'] ?? null,
        ]);
        
        $importOrder->items()->delete();
        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $totalAmount += $itemTotal;
            ImportOrderItem::create([
                'import_order_id' => $importOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $itemTotal,
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        // Update total amount
        $importOrder->update(['total_amount' => $totalAmount]);
        
        return redirect()->route('admin.import-orders.show', $importOrder)
            ->with('success', 'Cập nhật phiếu nhập thành công.');
    }

    public function destroy(ImportOrder $importOrder)
    {
        if ($importOrder->status !== 'pending') {
            return redirect()->route('admin.import-orders.index')
                ->with('error', 'Chỉ có thể xóa phiếu nhập ở trạng thái chờ xử lý.');
        }
        
        $importOrder->items()->delete();
        $importOrder->delete();
        
        return redirect()->route('admin.import-orders.index')
            ->with('success', 'Xóa phiếu nhập thành công.');
    }

    public function confirm(ImportOrder $importOrder)
    {
        // Admin có thể xác nhận phiếu đã hoàn thành bởi staff hoặc xác nhận trực tiếp
        if ($importOrder->status === 'completed') {
            return back()->with('info', 'Phiếu nhập đã được hoàn thành.');
        }
        
        if (!in_array($importOrder->status, ['pending', 'processing'])) {
            return back()->with('error', 'Chỉ có thể xác nhận phiếu nhập ở trạng thái chờ xử lý hoặc đang xử lý.');
        }
        
        DB::transaction(function() use ($importOrder) {
            foreach ($importOrder->items as $item) {
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $item->product_id],
                    ['quantity' => 0, 'last_updated_date' => now(), 'updated_by' => auth()->id()]
                );
                $inventory->increase($item->quantity, auth()->id());
            }
            
            $importOrder->update([
                'status' => 'completed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);
        });
        
        return redirect()->route('admin.import-orders.show', $importOrder)
            ->with('success', 'Xác nhận phiếu nhập thành công. Tồn kho đã được cập nhật.');
    }

    public function pdf(ImportOrder $importOrder)
    {
        // Load relationships
        $importOrder->load('supplier', 'creator', 'assignedStaff', 'confirmer', 'items.product');
        
        // Ensure import_date is set
        if (!$importOrder->import_date) {
            $importOrder->import_date = now()->toDateString();
        }
        
        // Debug: Log data to ensure it's loaded
        \Log::info('PDF Generation', [
            'order_id' => $importOrder->id,
            'code' => $importOrder->code,
            'items_count' => $importOrder->items->count(),
            'has_supplier' => $importOrder->supplier ? true : false,
            'has_creator' => $importOrder->creator ? true : false,
        ]);
        
        $pdf = Pdf::loadView('admin.import-orders.pdf', compact('importOrder'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        
        return $pdf->download('phieu-nhap-' . $importOrder->code . '.pdf');
    }

    public function export()
    {
        return Excel::download(new ImportOrdersExport, 'danh_sach_phieu_nhap_' . date('Y-m-d') . '.xlsx');
    }
}
