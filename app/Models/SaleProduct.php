<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sale_id', 'product_id', 'barcode', 'product_name', 'quantity',
        'list_price', 'final_unit_price', 'line_total', 'total_discount_amount',
        'price_type', 'discount_info', 'pricing_data'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}