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
        Schema::table('user_vehicle', function (Blueprint $table) {
            // Timestamp pour tracer quand l'accès a été accordé
            $table->timestamp('granted_at')->useCurrent()->after('vehicle_id');
            
            // Utilisateur qui a accordé l'accès (nullable pour les accès automatiques)
            $table->foreignId('granted_by')->nullable()->after('granted_at')->constrained('users')->nullOnDelete();
            
            // Type d'accès: manual (via UI) ou auto_driver (via assignment)
            $table->enum('access_type', ['manual', 'auto_driver'])->default('manual')->after('granted_by');
            
            // Index pour améliorer les performances des requêtes
            $table->index(['user_id', 'access_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_vehicle', function (Blueprint $table) {
            $table->dropForeign(['granted_by']);
            $table->dropIndex(['user_id', 'access_type']);
            $table->dropColumn(['granted_at', 'granted_by', 'access_type']);
        });
    }
};
