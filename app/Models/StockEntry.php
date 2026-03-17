<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'folio', 'branch_id', 'supplier_id', 'user_id', 'total_cost',
        'entry_date', 'notes', 'sync_status', 'last_sync'
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