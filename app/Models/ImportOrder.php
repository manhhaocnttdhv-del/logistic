<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ImportOrder extends Model
{
    protected $fillable = [
        'code',
        'supplier_id',
        'source',
        'from_warehouse',
        'created_by',
        'assigned_to',
        'import_date',
        'status',
        'notes',
        'staff_notes',
        'confirmed_by',
        'confirmed_at',
    ];
    
    protected function casts(): array
    {
        return [
            'import_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }
    
    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
        return $this->hasMany(ImportOrderItem::class);
    }
    
    // Accessors
    public function getImportDateAttribute($value)
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
    
    // Helper methods
    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum('total_price');
    }
    
    public function canBeConfirmed(): bool
    {
        return $this->status === 'processing' || $this->status === 'pending';
    }
}
