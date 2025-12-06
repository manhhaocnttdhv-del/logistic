<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'is_active',
        'phone',
    ];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    
    // Relationships
    public function importOrdersCreated()
    {
        return $this->hasMany(ImportOrder::class, 'created_by');
    }
    
    public function importOrdersAssigned()
    {
        return $this->hasMany(ImportOrder::class, 'assigned_to');
    }
    
    public function exportOrdersCreated()
    {
        return $this->hasMany(ExportOrder::class, 'created_by');
    }
    
    public function exportOrdersAssigned()
    {
        return $this->hasMany(ExportOrder::class, 'assigned_to');
    }
    
    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    
    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }
    
    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
    
    /**
     * Lấy tên chức vụ
     */
    public function getPositionNameAttribute(): ?string
    {
        $positions = \App\Services\StaffAssignmentService::getPositions();
        return $this->position ? ($positions[$this->position] ?? null) : null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
