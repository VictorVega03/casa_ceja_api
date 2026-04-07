<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayawayProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'layaway_id', 'product_id', 'barcode', 'product_name',
        'quantity', 'unit_price', 'line_total'
    ];

    public function layaway()
    {
        return $this->belongsTo(Layaway::class);
    }
}