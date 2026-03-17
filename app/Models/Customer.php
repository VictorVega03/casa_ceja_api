<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'rfc', 'street', 'exterior_number', 'interior_number',
        'neighborhood', 'postal_code', 'city', 'email', 'phone', 'active'
    ];

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function layaways()
    {
        return $this->hasMany(Layaway::class);
    }
}