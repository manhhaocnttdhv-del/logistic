<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportOrderItem extends Model
{
    protected $fillable = [
        'import_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
    ];
    
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }
    
    // Relationships
    public function importOrder(): BelongsTo
    {
        return $this->belongsTo(ImportOrder::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
