<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name', 'active', 'sync_status', 'last_sync'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}