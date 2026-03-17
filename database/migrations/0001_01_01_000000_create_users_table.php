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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone', 20);
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->integer(('user_type'))->default(2); // 1: admin, 2: employee
            $table->integer('branch_id')->nullable();
            $table->boolean('active')->default(true);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->integer('sync_status')->default(1); // 1: pending, 2: synced           
            $table->dateTime('last_sync')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');        
    }
};
