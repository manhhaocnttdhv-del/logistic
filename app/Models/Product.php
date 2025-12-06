<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'unit',
        'supplier_id',
        'description',
        'price',
        'min_stock',
        'is_active',
    ];
    
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
    
    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    
    public function importOrderItems(): HasMany
    {
        return $this->hasMany(ImportOrderItem::class);
    }
    
    public function exportOrderItems(): HasMany
    {
        return $this->hasMany(ExportOrderItem::class);
    }
    
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
    
    // Helper methods
    public function getCurrentStockAttribute(): int
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }
    
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }
}
