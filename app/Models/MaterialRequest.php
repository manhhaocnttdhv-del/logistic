<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class MaterialRequest extends Model
{
    protected $fillable = [
        'code',
        'requested_by',
        'department',
        'project',
        'status',
        'notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'export_order_id',
    ];
    
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }
    
    // Accessors
    public function getApprovedAtAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        if ($value instanceof Carbon) {
            return $value;
        }
        
        if (is_string($value)) {
            try {
                return Carbon::parse($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return $value;
    }
    
    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function exportOrder(): BelongsTo
    {
        return $this->belongsTo(ExportOrder::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(MaterialRequestItem::class);
    }
    
    // Helper methods
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    
    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }
}
