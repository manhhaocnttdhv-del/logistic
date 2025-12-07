<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ExportOrder extends Model
{
    protected $fillable = [
        'code',
        'created_by',
        'assigned_to',
        'export_date',
        'recipient',
        'reason',
        'reason_detail',
        'status',
        'notes',
        'staff_notes',
        'confirmed_by',
        'confirmed_at',
        'material_request_id',
        'department',
        'project',
    ];
    
    protected function casts(): array
    {
        return [
            'export_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }
    
    // Relationships
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
    
    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(ExportOrderItem::class);
    }
    
    // Accessors
    public function getExportDateAttribute($value)
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
    
    public function getConfirmedAtAttribute($value)
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
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }
    
    public function canBeConfirmed(): bool
    {
        return $this->status === 'processing' || $this->status === 'pending';
    }
}
