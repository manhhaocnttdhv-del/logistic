<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryAudit extends Model
{
    protected $fillable = [
        'code',
        'warehouse_id',
        'created_by',
        'assigned_to',
        'audit_date',
        'type',
        'status',
        'notes',
        'staff_notes',
        'total_items',
        'matched_items',
        'mismatched_items',
        'confirmed_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    // Relationships
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryAuditItem::class);
    }

    // Helper methods
    public function generateCode(): string
    {
        $count = self::count() + 1;
        return 'KT' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public function calculateStatistics(): void
    {
        $this->total_items = $this->items()->count();
        $this->matched_items = $this->items()->where('difference', 0)->count();
        $this->mismatched_items = $this->items()->where('difference', '!=', 0)->count();
        $this->save();
    }
}
