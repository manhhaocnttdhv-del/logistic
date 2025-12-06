<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'last_updated_date',
        'updated_by',
        'notes',
    ];
    
    protected function casts(): array
    {
        return [
            'last_updated_date' => 'date',
        ];
    }
    
    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    // Helper methods
    public function increase(int $quantity, int $userId = null): void
    {
        $this->update([
            'quantity' => $this->quantity + $quantity,
            'last_updated_date' => now(),
            'updated_by' => $userId ?? auth()->id(),
        ]);
    }
    
    public function decrease(int $quantity, int $userId = null): void
    {
        $this->update([
            'quantity' => max(0, $this->quantity - $quantity),
            'last_updated_date' => now(),
            'updated_by' => $userId ?? auth()->id(),
        ]);
    }
}
