<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutputProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'output_id', 'product_id', 'barcode', 'product_name',
        'quantity', 'unit_cost', 'line_total'
    ];

    public function output()
    {
        return $this->belongsTo(StockOutput::class, 'output_id');
    }
}