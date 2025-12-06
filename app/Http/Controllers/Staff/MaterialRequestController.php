<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    private function generateCode()
    {
        $lastRequest = MaterialRequest::latest()->first();
        $number = $lastRequest ? (int)substr($lastRequest->code, -6) + 1 : 1;
        return 'YC' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        // Staff chỉ xem yêu cầu của chính họ
        $query = MaterialRequest::where('requested_by', auth()->id())
            ->with('approver', 'exportOrder');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('project', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $requests = $query->latest()->paginate(15);
        return view('staff.material-requests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->with('inventory')->get();
        return view('staff.material-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
                $materialRequest = MaterialRequest::create([
                    'code' => $this->generateCode(),
                    'requested_by' => auth()->id(),
                    'department' => $validated['department'] ?? null,
                    'project' => $validated['project'] ?? null,
                    'status' => 'pending',
                    'notes' => $validated['notes'] ?? null,
                ]);
                
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
            
            return redirect()->route('staff.material-requests.show', $materialRequest)
                ->with('success', 'Tạo yêu cầu vật tư thành công. Yêu cầu đang chờ Admin duyệt.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        $request = MaterialRequest::where('requested_by', auth()->id())
            ->with('requester', 'approver', 'exportOrder', 'items.product')
            ->findOrFail($id);
        
        return view('staff.material-requests.show', compact('request'));
    }
}

