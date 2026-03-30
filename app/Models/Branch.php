<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name', 'address', 'email', 'razon_social', 'active'
    ];

    protected $casts = [
        'active'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
