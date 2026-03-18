<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_facturas', function (Blueprint $table) {
            $table->id();
            $table->string('rfc', 13)->index();
            $table->string('razon_social', 200);
            $table->string('regimen_fiscal', 100)->nullable();
            $table->string('calle', 200)->nullable();
            $table->string('numero_exterior', 20)->nullable();
            $table->string('numero_interior', 20)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->float('monto_compra')->nullable();
            $table->timestamp('fecha_compra')->nullable();
            $table->string('folio_ticket', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_facturas');
    }
};