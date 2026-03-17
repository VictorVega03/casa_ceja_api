<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'key', 'access_level',
        'description', 'active'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'user_type');
    }
}