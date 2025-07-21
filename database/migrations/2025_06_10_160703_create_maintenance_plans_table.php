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
    
    Schema::create('maintenance_plans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
    $table->foreignId('maintenance_type_id')->constrained('maintenance_types')->onDelete('cascade');
    $table->integer('recurrence_value');
    $table->foreignId('recurrence_unit_id')->constrained('recurrence_units');
    $table->date('next_due_date')->nullable();
    $table->bigInteger('next_due_mileage')->nullable();
    $table->text('notes')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
    
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
