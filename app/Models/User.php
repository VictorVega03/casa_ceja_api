<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'username', 'password',
        'user_type', 'branch_id', 'active', 'sync_status', 'last_sync'
    ];

    protected $hidden = [
        'password'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}