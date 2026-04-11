<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campos nuevos en stock_entries
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->string('entry_type', 20)->default('PURCHASE')->after('folio_output');
            $table->unsignedBigInteger('confirmed_by_user_id')->nullable()->after('notes');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by_user_id');
        });

        // Campos nuevos en stock_outputs
        Schema::table('stock_outputs', function (Blueprint $table) {
            $table->string('status', 20)->default('PENDING')->after('type');
            $table->unsignedBigInteger('confirmed_by_user_id')->nullable()->after('notes');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropColumn(['entry_type', 'confirmed_by_user_id', 'confirmed_at']);
        });

        Schema::table('stock_outputs', function (Blueprint $table) {
            $table->dropColumn(['status', 'confirmed_by_user_id', 'confirmed_at']);
        });
    }
};