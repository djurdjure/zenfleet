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
    Schema::create('vehicle_handover_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('handover_form_id')->constrained('vehicle_handover_forms')->onDelete('cascade');
    $table->string('category');
    $table->string('item');
    $table->enum('status', ['Bon', 'Moyen', 'Mauvais', 'N/A']);
    $table->timestamps();
    });
    
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_handover_details');
    }
};
