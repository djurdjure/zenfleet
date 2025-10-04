<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_request_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['repair_request_id', 'created_at'], 'idx_repair_history_request_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_request_history');
    }
};
