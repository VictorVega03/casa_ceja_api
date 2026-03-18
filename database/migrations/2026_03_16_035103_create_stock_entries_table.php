<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique()->nullable();
            $table->string('folio_output', 50)->nullable();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('supplier_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->float('total_amount')->default(0);
            $table->timestamp('entry_date')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};