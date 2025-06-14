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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('restrict');
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('restrict');
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->unsignedBigInteger('start_mileage')->nullable();
            $table->unsignedBigInteger('end_mileage')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
