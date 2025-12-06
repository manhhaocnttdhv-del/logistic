<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Product;
use App\Models\ExportOrder;
use App\Models\ExportOrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    private function generateCode()
    {
        $maxAttempts = 10;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            $lastRequest = MaterialRequest::latest()->first();
            $number = $lastRequest ? (int)substr($lastRequest->code, -6) + 1 : 1;
            $code = 'YC' . str_pad($number, 6, '0', STR_PAD_LEFT);
            
            // Check if code already exists
            if (!MaterialRequest::where('code', $code)->exists()) {
                return $code;
            }
            
            // If code exists, try next number
            $attempt++;
            $number++;
        }
        
        // Fallback: use timestamp-based code if all attempts fail
        return 'YC' . date('Ymd') . str_pad(MaterialRequest::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $query = MaterialRequest::with('requester', 'approver', 'exportOrder');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('project', 'like', "%{$search}%");
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $requests = $query->latest()->paginate(15);
        return view('admin.material-requests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->with('inventory')->get();
        $users = User::where('is_active', true)->get();
        return view('admin.material-requests.create', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by' => 'required|exists:users,id',
            'department' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        try {
            $materialRequest = DB::transaction(function() use ($validated) {
                $maxAttempts = 10;
                $attempt = 0;
                $materialRequest = null;
                
                while ($attempt < $maxAttempts) {
                    $code = $this->generateCode();
                    
                    try {
                        $materialRequest = MaterialRequest::create([
                            'code' => $code,
                            'requested_by' => $validated['requested_by'],
                            'department' => $validated['department'] ?? null,
                            'project' => $validated['project'] ?? null,
                            'status' => 'pending',
                            'notes' => $validated['notes'] ?? null,
                        ]);
                        break; // Success, exit loop
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Check if it's a duplicate key error
                        if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                            $attempt++;
                            if ($attempt >= $maxAttempts) {
                                throw new \Exception('Không thể tạo mã yêu cầu duy nhất. Vui lòng thử lại.');
                            }
                            // Wait a bit before retrying (microseconds)
                            usleep(100000); // 0.1 second
                            continue;
                        }
                        throw $e; // Re-throw if it's a different error
                    }
                }
                
                if (!$materialRequest) {
                    throw new \Exception('Không thể tạo yêu cầu vật tư.');
                }
                
                foreach ($validated['items'] as $item) {
                    MaterialRequestItem::create([
                        'material_request_id' => $materialRequest->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
                
                return $materialRequest;
            });
            
            return redirect()->route('admin.material-requests.show', $materialRequest)
                ->with('success', 'Tạo yêu cầu vật tư thành công.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra khi tạo yêu cầu: ' . $e->getMessage()]);
        }
    }

    public function show(MaterialRequest $materialRequest)
    {
        $materialRequest->load('requester', 'approver', 'exportOrder', 'items.product');
        return view('admin.material-requests.show', compact('materialRequest'));
    }

    public function edit(MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'pending') {
            return redirect()->route('admin.material-requests.show', $materialRequest)
                ->with('error', 'Chỉ có thể chỉnh sửa yêu cầu ở trạng thái chờ duyệt.');
        }
        
        $products = Product::where('is_active', true)->with('inventory')->get();
        $users = User::where('is_active', true)->get();
        $materialRequest->load('items.product');
        
        return view('admin.material-requests.edit', compact('materialRequest', 'products', 'users'));
    }

    public function update(Request $request, MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'pending') {
            return redirect()->route('admin.material-requests.show', $materialRequest)
                ->with('error', 'Chỉ có thể chỉnh sửa yêu cầu ở trạng thái chờ duyệt.');
        }
        
        $validated = $request->validate([
            'requested_by' => 'required|exists:users,id',
            'department' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);
        
        $materialRequest->update([
            'requested_by' => $validated['requested_by'],
            'department' => $validated['department'] ?? null,
            'project' => $validated['project'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        $materialRequest->items()->delete();
        foreach ($validated['items'] as $item) {
            MaterialRequestItem::create([
                'material_request_id' => $materialRequest->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'notes' => $item['notes'] ?? null,
            ]);
        }
        
        return redirect()->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Cập nhật yêu cầu vật tư thành công.');
    }

    public function destroy(MaterialRequest $materialRequest)
    {
        if ($materialRequest->status !== 'pending') {
            return redirect()->route('admin.material-requests.index')
                ->with('error', 'Chỉ có thể xóa yêu cầu ở trạng thái chờ duyệt.');
        }
        
        $materialRequest->items()->delete();
        $materialRequest->delete();
        
        return redirect()->route('admin.material-requests.index')
            ->with('success', 'Xóa yêu cầu vật tư thành công.');
    }

    public function approve(Request $request, $id)
    {
        // Get material request by ID
        $materialRequest = MaterialRequest::findOrFail($id);
        
        // Debug: Log current status
        \Log::info('Material request approve attempt', [
            'id' => $materialRequest->id,
            'code' => $materialRequest->code,
            'status' => $materialRequest->status,
            'status_type' => gettype($materialRequest->status),
        ]);
        
        // Check status - handle null or empty status as pending
        $currentStatus = $materialRequest->status ?? 'pending';
        if (empty($currentStatus)) {
            $currentStatus = 'pending';
        }
        
        if ($currentStatus !== 'pending') {
            \Log::warning('Material request approve failed - wrong status', [
                'id' => $materialRequest->id,
                'current_status' => $currentStatus,
                'expected' => 'pending',
            ]);
            return back()->with('error', 'Chỉ có thể duyệt yêu cầu ở trạng thái chờ duyệt. Trạng thái hiện tại: ' . ($currentStatus ?: 'Không xác định'));
        }
        
        // Query items directly from database to ensure we get them
        $items = MaterialRequestItem::where('material_request_id', $materialRequest->id)
            ->with('product')
            ->get();
        
        \Log::info('Material request items check', [
            'id' => $materialRequest->id,
            'items_count' => $items->count(),
            'items' => $items->pluck('id')->toArray(),
        ]);
        
        if ($items->isEmpty()) {
            return back()->with('error', 'Yêu cầu vật tư không có chi tiết vật tư.');
        }
        
        // Load requester
        $materialRequest->load('requester');
        
        // Check inventory availability
        foreach ($items as $item) {
            if (!$item->product) {
                \Log::warning('Material request item missing product', [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                ]);
                continue;
            }
            
            $currentStock = $item->product->current_stock ?? 0;
            if ($currentStock < $item->quantity) {
                return back()->withErrors(['items' => "Sản phẩm {$item->product->name} chỉ còn {$currentStock} trong kho."]);
            }
        }
        
        try {
            // Store items data from queried items
            $itemsData = $items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'notes' => $item->notes,
                ];
            })->toArray();
            
            DB::transaction(function() use ($materialRequest, $itemsData) {
                // Generate export order code
                $lastOrder = ExportOrder::latest()->first();
                $number = $lastOrder ? (int)substr($lastOrder->code, -6) + 1 : 1;
                $exportCode = 'PX' . str_pad($number, 6, '0', STR_PAD_LEFT);
                
                // Reload requester to ensure we have the name
                $materialRequest->load('requester');
                
                // Create export order from material request
                $exportOrder = ExportOrder::create([
                    'code' => $exportCode,
                    'created_by' => auth()->id(),
                    'export_date' => now()->toDateString(),
                    'recipient' => $materialRequest->requester->name,
                    'reason' => 'internal',
                    'reason_detail' => "Xuất từ yêu cầu vật tư {$materialRequest->code}",
                    'status' => 'pending',
                    'notes' => $materialRequest->notes,
                    'material_request_id' => $materialRequest->id,
                    'department' => $materialRequest->department,
                    'project' => $materialRequest->project,
                ]);
                
                // Create export order items from stored data
                foreach ($itemsData as $itemData) {
                    ExportOrderItem::create([
                        'export_order_id' => $exportOrder->id,
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'notes' => $itemData['notes'],
                    ]);
                }
                
                // Update material request with export order and status
                $materialRequest->update([
                    'export_order_id' => $exportOrder->id,
                    'status' => 'converted',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Material request approve error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi duyệt yêu cầu: ' . $e->getMessage());
        }
        
        return redirect()->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Đã duyệt yêu cầu và tạo phiếu xuất tự động.');
    }

    public function reject(Request $request, $id)
    {
        $materialRequest = MaterialRequest::findOrFail($id);
        
        if ($materialRequest->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối yêu cầu ở trạng thái chờ duyệt.');
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $materialRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        
        return redirect()->route('admin.material-requests.show', $materialRequest)
            ->with('success', 'Đã từ chối yêu cầu vật tư.');
    }
}
