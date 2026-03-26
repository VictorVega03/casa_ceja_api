<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
   protected $fillable = [
        'name', 'discount', 'has_discount', 'active'
    ];

    protected $casts = [
        'active'       => 'boolean',
        'has_discount' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
