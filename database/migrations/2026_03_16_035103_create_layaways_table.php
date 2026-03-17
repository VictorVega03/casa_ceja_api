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
        Schema::create('layaways', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique()->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('delivery_user_id')->nullable();
            $table->float('total')->default(0);
            $table->float('total_paid')->default(0);
            $table->timestamp('layaway_date')->nullable();
            $table->timestamp('pickup_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->integer('status')->default(0);
            $table->string('notes', 500)->nullable();
            $table->binary('ticket_data')->nullable();
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
        Schema::dropIfExists('layaways');
    }
};
