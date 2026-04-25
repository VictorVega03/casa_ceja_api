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
    Schema::table('cash_closes', function (Blueprint $table) {
        $table->decimal('expenses', 10, 2)->default(0)->change();
        $table->decimal('income', 10, 2)->default(0)->change();
    });
}
};
