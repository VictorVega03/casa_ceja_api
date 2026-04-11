<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'folio', 'folio_output', 'entry_type',
        'branch_id', 'supplier_id', 'user_id',
        'total_amount', 'entry_date', 'notes',
        'confirmed_by_user_id', 'confirmed_at',
    ];

    protected $casts = [
        'entry_date'   => 'datetime',
        'confirmed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // Tipos de entrada — constantes para no usar strings sueltos
    const TYPE_PURCHASE = 'PURCHASE'; // compra a proveedor, registrada offline
    const TYPE_TRANSFER = 'TRANSFER'; // traspaso recibido desde otra sucursal vía servidor

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

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function isPending(): bool
    {
        return is_null($this->confirmed_at);
    }

    public function isTransfer(): bool
    {
        return $this->entry_type === self::TYPE_TRANSFER;
    }
}