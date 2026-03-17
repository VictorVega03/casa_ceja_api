<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LayawayPayment extends Model
{
    protected $fillable = [
        'folio', 'layaway_id', 'user_id', 'amount_paid', 'payment_method',
        'payment_date', 'cash_close_folio', 'notes'
    ];

    public function layaway()
    {
        return $this->belongsTo(Layaway::class);
    }
}