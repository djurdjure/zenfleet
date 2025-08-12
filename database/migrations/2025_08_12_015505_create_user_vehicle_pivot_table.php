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
        // Crée la table pivot pour la relation N-N entre les utilisateurs et les véhicules.
        Schema::create('user_vehicle', function (Blueprint $table) {
            // Clé primaire composite pour garantir l'unicité de la paire user/vehicle.
            $table->primary(['user_id', 'vehicle_id']);

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vehicle');
    }
};
