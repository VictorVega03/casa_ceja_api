<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique()->nullable();
            $table->string('name', 200)->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->string('presentation', 100)->nullable();
            $table->float('iva')->default(0);
            $table->float('price_retail')->default(0);
            $table->float('price_wholesale')->default(0);
            $table->integer('wholesale_quantity')->default(0);
            $table->float('price_special')->default(0);
            $table->float('price_dealer')->default(0);
            $table->boolean('active')->default(true);            
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
