<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layaway extends Model
{
    protected $fillable = [
        'folio', 'customer_id', 'branch_id', 'user_id', 'delivery_user_id',
        'total', 'total_paid', 'layaway_date', 'pickup_date',
        'delivery_date', 'status', 'notes', 'ticket_data'
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
        return $this->hasMany(LayawayProduct::class);
    }

    public function payments()
    {
        return $this->hasMany(LayawayPayment::class);
    }
}