<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar tabla anterior
        Schema::dropIfExists('branch_tokens');

        // Crear tabla nueva
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('token', 64)->unique();
            $table->timestamp('last_used')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');

        Schema::create('branch_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->string('token', 64)->unique();
            $table->string('terminal_id', 20)->nullable();
            $table->timestamp('last_used')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }
};