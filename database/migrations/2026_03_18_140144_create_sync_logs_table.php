<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id')->index();
            $table->string('direction', 10);
            $table->string('entity', 50);
            $table->integer('records_sent')->default(0);
            $table->integer('records_accepted')->default(0);
            $table->integer('records_rejected')->default(0);
            $table->string('status', 20);
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['branch_id', 'created_at']);
            $table->index(['entity', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};