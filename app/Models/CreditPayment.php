<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $fillable = [
        'folio', 'credit_id', 'branch_id', 'user_id',
        'amount_paid', 'payment_method', 'payment_date',
        'cash_close_folio', 'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }
}