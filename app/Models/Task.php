<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    protected $fillable = [
        'assigned_to',
        'assigned_by',
        'type',
        'priority',
        'title',
        'description',
        'related_order_id',
        'related_order_type',
        'status',
        'due_date',
        'started_at',
        'completed_at',
        'completion_notes',
    ];
    
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
    
    // Accessors
    public function getDueDateAttribute($value)
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
    
    public function getStartedAtAttribute($value)
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
    
    public function getCompletedAtAttribute($value)
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
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
    
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
    
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }
    
    public function markAsCompleted(string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $notes,
        ]);
    }
}
