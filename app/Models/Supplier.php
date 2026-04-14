<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'active'
    ];

    protected $casts = [
        'active'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }
}