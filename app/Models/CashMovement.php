<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cash_close_id', 'type', 'concept', 'amount', 'user_id'
    ];

    public function cashClose()
    {
        return $this->belongsTo(CashClose::class);
    }
}