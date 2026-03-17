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
      Schema::create('cash_closes', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('opening_cash')->default(0);
            $table->float('total_cash')->default(0);
            $table->float('total_debit_card')->default(0);
            $table->float('total_credit_card')->default(0);
            $table->float('total_checks')->default(0);
            $table->float('total_transfers')->default(0);
            $table->float('layaway_cash')->default(0);
            $table->float('credit_cash')->default(0);
            $table->float('credit_total_created')->default(0);
            $table->float('layaway_total_created')->default(0);
            $table->text('expenses')->nullable();
            $table->text('income')->nullable();
            $table->float('surplus')->default(0);
            $table->float('expected_cash')->default(0);
            $table->float('total_sales')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('opening_date')->nullable();
            $table->timestamp('close_date')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();           
            $table->integer('sync_status')->default(1);
            $table->timestamp('last_sync')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_closes');
    }
};
