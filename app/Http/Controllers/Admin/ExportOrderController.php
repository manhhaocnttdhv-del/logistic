<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExportOrder;
use App\Models\ExportOrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Task;
use App\Services\StaffAssignmentService;
use App\Exports\ExportOrdersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportOrderController extends Controller
{
    
    private function generateCode()
    {
        $lastOrder = ExportOrder::latest()->first();
        $number = $lastOrder ? (int)substr($lastOrder->code, -6) + 1 : 1;
        return 'PX' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = ExportOrder::with('creator', 'assignedStaff');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(15);
        return view('admin.export-orders.index', compact('orders'));
    }

    public function create()
    {
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        return view('admin.export-orders.create', compact('staff', 'products', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'assigned_to' => 'nullable|exists:users,id',
            'export_date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'reason' => 'required|in:sale,internal,transfer,return_supplier,return,other',
            'reason_detail' => 'nullable|string',
            'notes' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'material_request_id' => 'nullable|exists:material_requests,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        // Check inventory availability
        foreach ($validated['items'] as $item) {
            $product = Product::with('inventory')->find($item['product_id']);
            $currentStock = $product->current_stock;
            if ($currentStock < $item['quantity']) {
                return back()->withErrors(['items' => "Sản phẩm {$product->name} chỉ còn {$currentStock} trong kho."])->withInput();
            }
        }
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            
            // Nếu chọn position cụ thể, tìm nhân viên có position đó
            if (!empty($validated['position'])) {
                $preferredPositions = [$validated['position']];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'export', 2);
            } else {
                // Tự động phân công theo logic mặc định
                $assignedUser = $assignmentService->assignStaffForExport();
            }
            
            if ($assignedUser) {
                $assignedTo = $assignedUser->id;
            }
        }
        
        $order = ExportOrder::create([
            'code' => $this->generateCode(),
            'created_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'export_date' => $validated['export_date'],
            'recipient' => $validated['recipient'] ?? null,
            'reason' => $validated['reason'],
            'reason_detail' => $validated['reason_detail'] ?? null,
            'status' => $assignedTo ? 'pending' : 'pending',
            'notes' => $validated['notes'] ?? null,
            'material_request_id' => $validated['material_request_id'] ?? null,
            'department' => $validated['department'] ?? null,
            'project' => $validated['project'] ?? null,
        ]);
        
        foreach ($validated['items'] as $item) {
            ExportOrderItem::create([
                'export_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('admin.export-orders.show', $order)
            ->with('success', 'Tạo phiếu xuất thành công.' . ($assignedTo ? ' Đã tự động phân công cho nhân viên.' : ''));
    }

    public function show(ExportOrder $exportOrder)
    {
        $exportOrder->load('creator', 'assignedStaff', 'confirmer', 'items.product');
        return view('admin.export-orders.show', compact('exportOrder'));
    }

    public function edit(ExportOrder $exportOrder)
    {
        if ($exportOrder->status !== 'pending') {
            return redirect()->route('admin.export-orders.show', $exportOrder)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu xuất ở trạng thái chờ xử lý.');
        }
        
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        $exportOrder->load('items.product');
        
        return view('admin.export-orders.edit', compact('exportOrder', 'staff', 'products', 'positions'));
    }

    public function update(Request $request, ExportOrder $exportOrder)
    {
        if ($exportOrder->status !== 'pending') {
            return redirect()->route('admin.export-orders.show', $exportOrder)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu xuất ở trạng thái chờ xử lý.');
        }
        
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'export_date' => 'required|date',
            'recipient' => 'nullable|string|max:255',
            'reason' => 'required|in:sale,internal,transfer,return_supplier,return,other',
            'reason_detail' => 'nullable|string',
            'notes' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            
            // Nếu chọn position cụ thể, tìm nhân viên có position đó
            if (!empty($validated['position'])) {
                $preferredPositions = [$validated['position']];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'export', 2);
            } else {
                // Tự động phân công theo logic mặc định
                $assignedUser = $assignmentService->assignStaffForExport();
            }
            
            if ($assignedUser) {
                $assignedTo = $assignedUser->id;
            }
        }
        
        $exportOrder->update([
            'assigned_to' => $assignedTo,
            'export_date' => $validated['export_date'],
            'recipient' => $validated['recipient'] ?? null,
            'reason' => $validated['reason'],
            'reason_detail' => $validated['reason_detail'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'department' => $validated['department'] ?? null,
            'project' => $validated['project'] ?? null,
        ]);
        
        $exportOrder->items()->delete();
        foreach ($validated['items'] as $item) {
            ExportOrderItem::create([
                'export_order_id' => $exportOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('admin.export-orders.show', $exportOrder)
            ->with('success', 'Cập nhật phiếu xuất thành công.');
    }

    public function destroy(ExportOrder $exportOrder)
    {
        if ($exportOrder->status !== 'pending') {
            return redirect()->route('admin.export-orders.index')
                ->with('error', 'Chỉ có thể xóa phiếu xuất ở trạng thái chờ xử lý.');
        }
        
        $exportOrder->items()->delete();
        $exportOrder->delete();
        
        return redirect()->route('admin.export-orders.index')
            ->with('success', 'Xóa phiếu xuất thành công.');
    }

    public function confirm(ExportOrder $exportOrder)
    {
        // Admin có thể xác nhận phiếu đã hoàn thành bởi staff hoặc xác nhận trực tiếp
        if ($exportOrder->status === 'completed') {
            return back()->with('info', 'Phiếu xuất đã được hoàn thành.');
        }
        
        if (!in_array($exportOrder->status, ['pending', 'processing'])) {
            return back()->with('error', 'Chỉ có thể xác nhận phiếu xuất ở trạng thái chờ xử lý hoặc đang xử lý.');
        }
        
        // Check inventory before confirming
        foreach ($exportOrder->items as $item) {
            $currentStock = $item->product->current_stock;
            if ($currentStock < $item->quantity) {
                return back()->withErrors(['items' => "Sản phẩm {$item->product->name} chỉ còn {$currentStock} trong kho."]);
            }
        }
        
        try {
            DB::transaction(function() use ($exportOrder) {
                foreach ($exportOrder->items as $item) {
                    $inventory = Inventory::where('product_id', $item->product_id)->first();
                    if (!$inventory || $inventory->quantity < $item->quantity) {
                        throw new \Exception("Sản phẩm {$item->product->name} không đủ tồn kho.");
                    }
                    $inventory->decrease($item->quantity, auth()->id());
                }
                
                $exportOrder->update([
                    'status' => 'completed',
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => now(),
                ]);
            });
            
            return redirect()->route('admin.export-orders.show', $exportOrder)
                ->with('success', 'Xác nhận phiếu xuất thành công. Tồn kho đã được cập nhật.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function export()
    {
        return Excel::download(new ExportOrdersExport, 'danh_sach_phieu_xuat_' . date('Y-m-d') . '.xlsx');
    }
    
    private function getReasonText($reason)
    {
        return match($reason) {
            'sale' => 'Bán hàng',
            'internal' => 'Sử dụng nội bộ',
            'transfer' => 'Điều chuyển',
            'return_supplier' => 'Trả nhà cung cấp',
            'return' => 'Trả hàng',
            'other' => 'Khác',
            default => $reason,
        };
    }
}
