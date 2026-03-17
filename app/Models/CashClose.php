<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClose extends Model
{
    protected $fillable = [
        'folio', 'branch_id', 'user_id', 'opening_cash', 'total_cash',
        'total_debit_card', 'total_credit_card', 'total_checks', 'total_transfers',
        'layaway_cash', 'credit_cash', 'credit_total_created', 'layaway_total_created',
        'expenses', 'income', 'surplus', 'expected_cash', 'total_sales',
        'notes', 'opening_date', 'close_date'
    ];

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}