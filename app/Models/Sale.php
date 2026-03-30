<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'folio', 'branch_id', 'user_id', 'subtotal', 'discount',
        'total', 'amount_paid', 'change_given', 'payment_method',
        'payment_summary', 'ticket_data', 'sale_date', 'cash_close_folio'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany(SaleProduct::class);
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