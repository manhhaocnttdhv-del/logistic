<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ImportOrder;
use App\Models\ImportOrderItem;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Staff có thể xem cả phiếu được giao và phiếu tự tạo
        $query = ImportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('supplier', 'creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(15);
        
        return view('staff.import-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        return view('staff.import-orders.create', compact('suppliers', 'products', 'positions'));
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
        // Nếu staff tự tạo, mặc định gán cho chính họ
        $assignedTo = $validated['assigned_to'] ?? auth()->id();
        
        $order = ImportOrder::create([
            'code' => $this->generateCode(),
            'supplier_id' => $validated['supplier_id'] ?? null,
            'source' => $validated['source'],
            'from_warehouse' => $validated['from_warehouse'] ?? null,
            'created_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'import_date' => $validated['import_date'],
            'status' => 'pending',
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
        
        return redirect()->route('staff.import-orders.show', $order)
            ->with('success', 'Tạo phiếu nhập thành công.');
    }

    public function show($id)
    {
        $order = ImportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('supplier', 'creator', 'items.product')
        ->findOrFail($id);
        
        return view('staff.import-orders.show', compact('order'));
    }

    public function process(Request $request, $id)
    {
        $order = ImportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('items.product')
        ->findOrFail($id);
        
        // Staff chỉ có thể xử lý phiếu ở trạng thái pending hoặc processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Chỉ có thể xử lý phiếu ở trạng thái chờ xử lý hoặc đang xử lý.');
        }
        
        $validated = $request->validate([
            'staff_notes' => 'nullable|string|max:1000',
            'action' => 'required|in:start,complete',
        ]);
        
        if ($validated['action'] === 'start') {
            $order->update([
                'status' => 'processing',
                'staff_notes' => $validated['staff_notes'] ?? null,
            ]);
            
            return redirect()->route('staff.import-orders.show', $order)
                ->with('success', 'Đã bắt đầu xử lý phiếu nhập. Vui lòng kiểm tra hàng hóa và nhập kho khi đã nhận đủ.');
        } else {
            // Complete - update inventory
            try {
                DB::transaction(function() use ($order, $validated) {
                    foreach ($order->items as $item) {
                        $inventory = Inventory::firstOrCreate(
                            ['product_id' => $item->product_id],
                            ['quantity' => 0, 'last_updated_date' => now(), 'updated_by' => auth()->id()]
                        );
                        $inventory->increase($item->quantity, auth()->id());
                    }
                    
                    $order->update([
                        'status' => 'completed',
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now(),
                        'staff_notes' => $validated['staff_notes'] ?? $order->staff_notes,
                    ]);
                });
                
                return redirect()->route('staff.import-orders.show', $order)
                    ->with('success', 'Đã hoàn thành xử lý phiếu nhập. Tồn kho đã được cập nhật.');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
        }
    }
}
