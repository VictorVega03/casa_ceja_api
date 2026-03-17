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
       Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 255)->unique()->nullable();
            $table->integer('credit_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('amount_paid')->default(0);
            $table->string('payment_method', 500)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('cash_close_folio', 255)->nullable();
            $table->string('notes', 300)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();           
                       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};
