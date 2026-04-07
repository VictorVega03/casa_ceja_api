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
    // Agregar cash_close_folio a credits
    Schema::table('credits', function (Blueprint $table) {
        $table->string('cash_close_folio', 50)->nullable()->index()->after('notes');
    });

    // Agregar cash_close_folio a layaways
    Schema::table('layaways', function (Blueprint $table) {
        $table->string('cash_close_folio', 50)->nullable()->index()->after('notes');
    });

    // Quitar pricing_data de credit_products
    Schema::table('credit_products', function (Blueprint $table) {
        $table->dropColumn('pricing_data');
    });

    // Quitar pricing_data de layaway_products
    Schema::table('layaway_products', function (Blueprint $table) {
        $table->dropColumn('pricing_data');
    });
}

public function down(): void
{
    Schema::table('credits', function (Blueprint $table) {
        $table->dropColumn('cash_close_folio');
    });
    Schema::table('layaways', function (Blueprint $table) {
        $table->dropColumn('cash_close_folio');
    });
    Schema::table('credit_products', function (Blueprint $table) {
        $table->binary('pricing_data')->nullable();
    });
    Schema::table('layaway_products', function (Blueprint $table) {
        $table->binary('pricing_data')->nullable();
    });
}
};
