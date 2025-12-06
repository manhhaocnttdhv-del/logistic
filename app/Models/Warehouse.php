<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'manager_name',
        'phone',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryAudits(): HasMany
    {
        return $this->hasMany(InventoryAudit::class);
    }

    // Helper methods
    public function getTotalProductsAttribute(): int
    {
        return $this->inventories()->count();
    }

    public function getTotalValueAttribute(): float
    {
        return $this->inventories()
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventory.quantity * products.price) as total')
            ->value('total') ?? 0;
    }
}
