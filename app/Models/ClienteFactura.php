<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFactura extends Model
{
    protected $fillable = [
        'rfc', 'razon_social', 'regimen_fiscal',
        'calle', 'numero_exterior', 'numero_interior',
        'colonia', 'municipio', 'estado', 'cp',
        'email', 'telefono', 'monto_compra',
        'fecha_compra', 'folio_ticket'
    ];
}