<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutput extends Model
{
    protected $fillable = [
        'folio', 'origin_branch_id', 'destination_branch_id',
        'user_id', 'type', 'status',
        'total_amount', 'output_date', 'notes',
        'confirmed_by_user_id', 'confirmed_at',
    ];

    protected $casts = [
        'output_date'  => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Estados del traspaso
    const STATUS_PENDING   = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';

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

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}