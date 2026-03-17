<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name', 'address', 'email', 'razon_social',
        'active', 'sync_status', 'last_sync'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
