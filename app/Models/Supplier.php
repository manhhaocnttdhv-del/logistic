<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];
    
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
    
    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    public function importOrders(): HasMany
    {
        return $this->hasMany(ImportOrder::class);
    }
}
