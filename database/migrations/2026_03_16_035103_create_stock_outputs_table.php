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
        Schema::create('stock_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique()->nullable();
            $table->integer('origin_branch_id')->nullable();
            $table->integer('destination_branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->float('total_amount')->default(0);
            $table->timestamp('output_date')->nullable();
            $table->string('notes', 500)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
                       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outputs');
    }
};
