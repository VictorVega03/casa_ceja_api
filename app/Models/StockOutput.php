<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutput extends Model
{
    protected $fillable = [
        'folio', 'origin_branch_id', 'destination_branch_id', 'user_id',
        'total_amount', 'output_date', 'notes', 'sync_status', 'last_sync'
    ];

    public function originBranch()
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function products()
    {
        return $this->hasMany(OutputProduct::class, 'output_id');
    }
}