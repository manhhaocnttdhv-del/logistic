<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ExportOrder;
use App\Models\ExportOrderItem;
use App\Models\Inventory;
use App\Models\Product;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Staff có thể xem cả phiếu được giao và phiếu tự tạo
        $query = ExportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->latest()->paginate(15);
        
        return view('staff.export-orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->with('inventory')->get();
        $positions = StaffAssignmentService::getPositions();
        return view('staff.export-orders.create', compact('products', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
        
        // Check inventory availability
        foreach ($validated['items'] as $item) {
            $product = Product::with('inventory')->find($item['product_id']);
            $currentStock = $product->current_stock;
            if ($currentStock < $item['quantity']) {
                return back()->withErrors(['items' => "Sản phẩm {$product->name} chỉ còn {$currentStock} trong kho."])->withInput();
            }
        }
        
        // Nếu staff tự tạo, mặc định gán cho chính họ
        $assignedTo = auth()->id();
        
        $order = ExportOrder::create([
            'code' => $this->generateCode(),
            'created_by' => auth()->id(),
            'assigned_to' => $assignedTo,
            'export_date' => $validated['export_date'],
            'recipient' => $validated['recipient'] ?? null,
            'reason' => $validated['reason'],
            'reason_detail' => $validated['reason_detail'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
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
        
        return redirect()->route('staff.export-orders.show', $order)
            ->with('success', 'Tạo phiếu xuất thành công.');
    }

    public function show($id)
    {
        $order = ExportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('creator', 'items.product')
        ->findOrFail($id);
        
        return view('staff.export-orders.show', compact('order'));
    }

    public function process(Request $request, $id)
    {
        $order = ExportOrder::where(function($q) {
            $q->where('assigned_to', auth()->id())
              ->orWhere('created_by', auth()->id());
        })
        ->with('items.product.inventory')
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
            // Check inventory before starting
            foreach ($order->items as $item) {
                $product = $item->product;
                $currentStock = $product->current_stock;
                if ($currentStock < $item->quantity) {
                    return back()->withErrors(['items' => "Sản phẩm {$product->name} chỉ còn {$currentStock} {$product->unit} trong kho. Không đủ để xuất {$item->quantity} {$product->unit}."])->withInput();
                }
            }
            
            $order->update([
                'status' => 'processing',
                'staff_notes' => $validated['staff_notes'] ?? null,
            ]);
            
            return redirect()->route('staff.export-orders.show', $order)
                ->with('success', 'Đã bắt đầu xử lý phiếu xuất. Vui lòng kiểm tra hàng hóa và hoàn thành khi đã xuất xong.');
        } else {
            // Complete - decrease inventory
            try {
                DB::transaction(function() use ($order, $validated) {
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        $inventory = Inventory::where('product_id', $item->product_id)->first();
                        
                        if (!$inventory) {
                            throw new \Exception("Sản phẩm {$product->name} chưa có trong kho.");
                        }
                        
                        if ($inventory->quantity < $item->quantity) {
                            throw new \Exception("Sản phẩm {$product->name} chỉ còn {$inventory->quantity} {$product->unit} trong kho. Không đủ để xuất {$item->quantity} {$product->unit}.");
                        }
                        
                        $inventory->decrease($item->quantity, auth()->id());
                    }
                    
                    $order->update([
                        'status' => 'completed',
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => now(),
                        'staff_notes' => $validated['staff_notes'] ?? $order->staff_notes,
                    ]);
                });
                
                return redirect()->route('staff.export-orders.show', $order)
                    ->with('success', 'Đã hoàn thành xử lý phiếu xuất. Tồn kho đã được cập nhật.');
            } catch (\Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()])->withInput();
            }
        }
    }
}
