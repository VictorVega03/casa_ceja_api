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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('subtotal')->default(0);
            $table->float('discount')->default(0);
            $table->float('total')->default(0);
            $table->float('amount_paid')->default(0);
            $table->float('change_given')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_summary', 100)->nullable();
            $table->binary('ticket_data')->nullable();
            $table->timestamp('sale_date')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->string('cash_close_folio', 50)->nullable();
                       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
