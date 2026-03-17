<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address',
        'active', 'sync_status', 'last_sync'
    ];

    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }
}