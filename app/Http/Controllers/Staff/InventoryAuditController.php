<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryAudit::where('assigned_to', auth()->id())
            ->with('warehouse', 'creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $audits = $query->latest()->paginate(15);
        
        return view('staff.inventory-audits.index', compact('audits'));
    }

    public function show($id)
    {
        $audit = InventoryAudit::where('assigned_to', auth()->id())
            ->with('warehouse', 'creator', 'items.product')
            ->findOrFail($id);
        
        return view('staff.inventory-audits.show', compact('audit'));
    }

    public function process(Request $request, $id)
    {
        $audit = InventoryAudit::where('assigned_to', auth()->id())
            ->with('items.product')
            ->findOrFail($id);
        
        if (!in_array($audit->status, ['pending', 'in_progress'])) {
            return back()->with('error', 'Chỉ có thể xử lý phiếu ở trạng thái chờ xử lý hoặc đang xử lý.');
        }
        
        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:1000',
            'action' => 'required|in:start,complete',
        ]);
        
        if ($validated['action'] === 'start') {
            $audit->update([
                'status' => 'in_progress',
                'staff_notes' => $validated['staff_notes'] ?? null,
            ]);
            
            return redirect()->route('staff.inventory-audits.show', $audit)
                ->with('success', 'Đã bắt đầu kiểm toán. Vui lòng nhập số lượng thực tế cho từng sản phẩm.');
        } else {
            // Complete - tính thống kê
            $audit->calculateStatistics();
            $audit->update([
                'status' => 'completed',
                'staff_notes' => $validated['staff_notes'] ?? $audit->staff_notes,
            ]);
            
            return redirect()->route('staff.inventory-audits.show', $audit)
                ->with('success', 'Đã hoàn thành kiểm toán. Hệ thống đã tính thống kê. Vui lòng chờ Admin xác nhận.');
        }
    }

    public function updateItems(Request $request, $id)
    {
        $audit = InventoryAudit::where('assigned_to', auth()->id())
            ->findOrFail($id);
        
        if ($audit->status !== 'in_progress') {
            return back()->with('error', 'Chỉ có thể cập nhật số lượng khi đang kiểm toán.');
        }
        
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory_audit_items,id',
            'items.*.actual_quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::transaction(function() use ($audit, $validated) {
                foreach ($validated['items'] as $itemData) {
                    $item = InventoryAuditItem::where('inventory_audit_id', $audit->id)
                        ->findOrFail($itemData['id']);
                    
                    $item->update([
                        'actual_quantity' => $itemData['actual_quantity'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                    
                    // Tính chênh lệch
                    $item->calculateDifference();
                }
                
                // Tính lại thống kê
                $audit->calculateStatistics();
            });
            
            return redirect()->route('staff.inventory-audits.show', $audit)
                ->with('success', 'Đã cập nhật số lượng thực tế. Hệ thống đã tính chênh lệch.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
