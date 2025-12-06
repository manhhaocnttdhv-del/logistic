<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAuditItem extends Model
{
    protected $fillable = [
        'inventory_audit_id',
        'product_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'notes',
        'is_adjusted',
    ];

    protected function casts(): array
    {
        return [
            'is_adjusted' => 'boolean',
        ];
    }

    // Relationships
    public function inventoryAudit(): BelongsTo
    {
        return $this->belongsTo(InventoryAudit::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper methods
    public function calculateDifference(): void
    {
        $this->difference = $this->actual_quantity - $this->system_quantity;
        $this->save();
    }

    public function adjustInventory(): void
    {
        if ($this->is_adjusted) {
            return;
        }

        $inventory = Inventory::where('product_id', $this->product_id)
            ->where('warehouse_id', $this->inventoryAudit->warehouse_id)
            ->first();

        if ($inventory) {
            $inventory->update([
                'quantity' => $this->actual_quantity,
                'last_updated_date' => now(),
                'updated_by' => auth()->id(),
                'notes' => "Điều chỉnh từ kiểm toán {$this->inventoryAudit->code}: {$this->notes}",
            ]);
        } else {
            Inventory::create([
                'product_id' => $this->product_id,
                'warehouse_id' => $this->inventoryAudit->warehouse_id,
                'quantity' => $this->actual_quantity,
                'last_updated_date' => now(),
                'updated_by' => auth()->id(),
                'notes' => "Tạo mới từ kiểm toán {$this->inventoryAudit->code}",
            ]);
        }

        $this->is_adjusted = true;
        $this->save();
    }
}
