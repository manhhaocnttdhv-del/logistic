<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAudit::with('warehouse', 'assignedStaff', 'creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        $audits = $query->latest()->paginate(15);
        $warehouses = Warehouse::where('is_active', true)->get();
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        
        return view('admin.inventory-audits.index', compact('audits', 'warehouses', 'staff'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $positions = StaffAssignmentService::getPositions();
        
        return view('admin.inventory-audits.create', compact('warehouses', 'staff', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'position' => 'nullable|in:import_staff,export_staff,warehouse_staff,inventory_staff,general_staff',
            'assigned_to' => 'nullable|exists:users,id',
            'audit_date' => 'required|date',
            'type' => 'required|in:full,partial,spot',
            'notes' => 'nullable|string',
            'products' => 'required_if:type,partial,spot|array',
            'products.*' => 'exists:products,id',
        ]);
        
        // Tự động phân công nếu không chỉ định nhân viên cụ thể
        $assignedTo = $validated['assigned_to'];
        if (!$assignedTo) {
            $assignmentService = new StaffAssignmentService();
            
            if (!empty($validated['position'])) {
                $preferredPositions = [$validated['position']];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'task', 2);
            } else {
                // Ưu tiên inventory_staff cho kiểm toán
                $preferredPositions = ['inventory_staff', 'warehouse_staff', 'general_staff'];
                $assignedUser = $assignmentService->assignStaffByPositions($preferredPositions, 'task', 2);
            }
            
            if ($assignedUser) {
                $assignedTo = $assignedUser->id;
            }
        }
        
        $audit = InventoryAudit::create([
            'code' => (new InventoryAudit())->generateCode(),
            'warehouse_id' => $validated['warehouse_id'] ?? null,
            'created_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'audit_date' => $validated['audit_date'],
            'type' => $validated['type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Tạo items dựa trên loại kiểm toán
        if ($validated['type'] === 'full') {
            // Kiểm toán toàn bộ: Lấy tất cả sản phẩm có tồn kho trong kho đó
            $inventories = Inventory::where('warehouse_id', $audit->warehouse_id)
                ->with('product')
                ->get();
            
            foreach ($inventories as $inventory) {
                InventoryAuditItem::create([
                    'inventory_audit_id' => $audit->id,
                    'product_id' => $inventory->product_id,
                    'system_quantity' => $inventory->quantity,
                    'actual_quantity' => 0, // Nhân viên sẽ nhập sau
                    'difference' => 0,
                ]);
            }
        } elseif ($validated['type'] === 'partial' || $validated['type'] === 'spot') {
            // Kiểm toán một phần/đột xuất: Chỉ các sản phẩm được chọn
            foreach ($validated['products'] as $productId) {
                $inventory = Inventory::where('product_id', $productId)
                    ->where('warehouse_id', $audit->warehouse_id)
                    ->first();
                
                InventoryAuditItem::create([
                    'inventory_audit_id' => $audit->id,
                    'product_id' => $productId,
                    'system_quantity' => $inventory ? $inventory->quantity : 0,
                    'actual_quantity' => 0, // Nhân viên sẽ nhập sau
                    'difference' => 0,
                ]);
            }
        }
        
        $audit->calculateStatistics();
        
        return redirect()->route('admin.inventory-audits.show', $audit)
            ->with('success', 'Tạo phiếu kiểm toán thành công.' . ($assignedTo ? ' Đã tự động phân công cho nhân viên.' : ''));
    }

    public function show(InventoryAudit $inventoryAudit)
    {
        $inventoryAudit->load('warehouse', 'creator', 'assignedStaff', 'confirmer', 'items.product');
        return view('admin.inventory-audits.show', compact('inventoryAudit'));
    }

    public function edit(InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status !== 'pending') {
            return redirect()->route('admin.inventory-audits.show', $inventoryAudit)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu kiểm toán ở trạng thái chờ xử lý.');
        }
        
        $warehouses = Warehouse::where('is_active', true)->get();
        $staff = User::where('role', 'staff')->where('is_active', true)->get();
        $positions = StaffAssignmentService::getPositions();
        
        return view('admin.inventory-audits.edit', compact('inventoryAudit', 'warehouses', 'staff', 'positions'));
    }

    public function update(Request $request, InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status !== 'pending') {
            return redirect()->route('admin.inventory-audits.show', $inventoryAudit)
                ->with('error', 'Chỉ có thể chỉnh sửa phiếu kiểm toán ở trạng thái chờ xử lý.');
        }
        
        $validated = $request->validate([
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'audit_date' => 'required|date',
            'type' => 'required|in:full,partial,spot',
            'notes' => 'nullable|string',
        ]);
        
        $inventoryAudit->update($validated);
        
        return redirect()->route('admin.inventory-audits.show', $inventoryAudit)
            ->with('success', 'Cập nhật phiếu kiểm toán thành công.');
    }

    public function destroy(InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status !== 'pending') {
            return redirect()->route('admin.inventory-audits.index')
                ->with('error', 'Chỉ có thể xóa phiếu kiểm toán ở trạng thái chờ xử lý.');
        }
        
        $inventoryAudit->items()->delete();
        $inventoryAudit->delete();
        
        return redirect()->route('admin.inventory-audits.index')
            ->with('success', 'Xóa phiếu kiểm toán thành công.');
    }

    public function confirm(InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status !== 'completed') {
            return back()->with('error', 'Chỉ có thể xác nhận phiếu kiểm toán đã hoàn thành.');
        }
        
        if ($inventoryAudit->confirmed_by) {
            return back()->with('info', 'Phiếu kiểm toán đã được xác nhận.');
        }
        
        $inventoryAudit->update([
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);
        
        return redirect()->route('admin.inventory-audits.show', $inventoryAudit)
            ->with('success', 'Xác nhận phiếu kiểm toán thành công.');
    }

    public function adjust(InventoryAudit $inventoryAudit)
    {
        if ($inventoryAudit->status !== 'completed') {
            return back()->with('error', 'Chỉ có thể điều chỉnh tồn kho khi phiếu kiểm toán đã hoàn thành.');
        }
        
        try {
            DB::transaction(function() use ($inventoryAudit) {
                foreach ($inventoryAudit->items as $item) {
                    if ($item->is_adjusted) {
                        continue; // Đã điều chỉnh rồi
                    }
                    
                    $item->adjustInventory();
                }
            });
            
            return redirect()->route('admin.inventory-audits.show', $inventoryAudit)
                ->with('success', 'Điều chỉnh tồn kho thành công.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
