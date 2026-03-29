<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode', 'name', 'category_id', 'unit_id', 'presentation',
        'iva', 'price_retail', 'price_wholesale', 'wholesale_quantity',
        'price_special', 'price_dealer', 'active'
    ];

    protected $casts = [
        'active'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}