<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }
        
        $warehouses = $query->latest()->paginate(15);
        
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        Warehouse::create($validated);
        
        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Tạo kho thành công.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('inventories.product', 'inventoryAudits');
        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $warehouse->update($validated);
        
        return redirect()->route('admin.warehouses.show', $warehouse)
            ->with('success', 'Cập nhật kho thành công.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->inventories()->exists()) {
            return redirect()->route('admin.warehouses.index')
                ->with('error', 'Không thể xóa kho vì còn tồn kho.');
        }
        
        $warehouse->delete();
        
        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Xóa kho thành công.');
    }
}
