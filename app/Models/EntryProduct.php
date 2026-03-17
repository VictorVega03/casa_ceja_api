<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'entry_id', 'product_id', 'barcode', 'product_name',
        'quantity', 'unit_cost', 'line_total'
    ];

    public function entry()
    {
        return $this->belongsTo(StockEntry::class, 'entry_id');
    }
}