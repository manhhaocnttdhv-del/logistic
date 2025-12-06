<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('product.supplier');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('low_stock')) {
            $query->whereHas('product', function($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.min_stock');
            });
        }
        
        $inventories = $query->latest('last_updated_date')->paginate(15);
        
        return view('staff.inventory.index', compact('inventories'));
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $product = Product::with('inventory')->findOrFail($validated['product_id']);
        
        $inventory = Inventory::firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 0, 'last_updated_date' => now()]
        );
        
        $inventory->update([
            'quantity' => $validated['quantity'],
            'last_updated_date' => now(),
            'updated_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);
        
        return redirect()->route('staff.inventory.index')
            ->with('success', "Đã cập nhật tồn kho cho sản phẩm {$product->name}.");
    }
}
