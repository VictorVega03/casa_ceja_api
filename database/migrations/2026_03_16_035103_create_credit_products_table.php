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
        Schema::create('credit_products', function (Blueprint $table) {
            $table->id();
            $table->integer('credit_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('barcode', 50)->nullable();
            $table->string('product_name', 200)->nullable();
            $table->integer('quantity')->default(0);
            $table->float('unit_price')->default(0);
            $table->float('line_total')->default(0);
            $table->binary('pricing_data')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_products');
    }
};
