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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('exterior_number', 20)->nullable();
            $table->string('interior_number', 20)->nullable();
            $table->string('neighborhood', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('active')->default(true);            
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
        Schema::dropIfExists('customers');
    }
};
