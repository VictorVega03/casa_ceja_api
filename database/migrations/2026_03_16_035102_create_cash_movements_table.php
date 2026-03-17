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
       Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('cash_close_id')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('concept', 200)->nullable();
            $table->float('amount')->default(0);
            $table->integer('user_id')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
