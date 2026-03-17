<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $fillable = [
        'folio', 'customer_id', 'branch_id', 'user_id', 'total', 'total_paid',
        'months_to_pay', 'credit_date', 'due_date', 'status',
        'notes', 'ticket_data', 'sync_status', 'last_sync'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function products()
    {
        return $this->hasMany(CreditProduct::class);
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}