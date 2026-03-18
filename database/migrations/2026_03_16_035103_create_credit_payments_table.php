<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 255)->unique()->nullable();
            $table->integer('credit_id')->nullable()->index();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('amount_paid')->default(0);
            $table->string('payment_method', 500)->nullable();
            $table->timestamp('payment_date')->nullable()->index();
            $table->string('cash_close_folio', 255)->nullable()->index();
            $table->string('notes', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payments');
    }
};