<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'folio', 'folio_output', 'branch_id', 'supplier_id', 'user_id',
        'total_amount', 'entry_date', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->hasMany(EntryProduct::class, 'entry_id');
    }
}