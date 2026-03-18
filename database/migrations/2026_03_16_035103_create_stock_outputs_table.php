<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique()->nullable();
            $table->integer('origin_branch_id')->nullable()->index();
            $table->integer('destination_branch_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('type', 20)->default('OTHER');
            $table->float('total_amount')->default(0);
            $table->timestamp('output_date')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outputs');
    }
};