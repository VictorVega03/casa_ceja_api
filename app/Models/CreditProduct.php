<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'credit_id', 'product_id', 'barcode', 'product_name',
        'quantity', 'unit_price', 'line_total', 'pricing_data'
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }
}