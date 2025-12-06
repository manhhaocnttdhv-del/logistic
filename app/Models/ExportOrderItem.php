<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportOrderItem extends Model
{
    protected $fillable = [
        'export_order_id',
        'product_id',
        'quantity',
        'notes',
    ];
    
    // Relationships
    public function exportOrder(): BelongsTo
    {
        return $this->belongsTo(ExportOrder::class);
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
