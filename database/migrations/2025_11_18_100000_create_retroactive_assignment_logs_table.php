<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🚀 ENTERPRISE-GRADE: Table de log pour affectations rétroactives
     * 
     * Cette table permet de :
     * - Tracer toutes les affectations créées dans le passé
     * - Conserver l'historique des validations et warnings
     * - Auditer les modifications rétroactives
     * - Analyser les patterns d'utilisation
     * 
     * @return void
     */
    public function up(): void
    {
        Schema::create('retroactive_assignment_logs', function (Blueprint $table) {
            $table->id();
            
            // Référence à l'affectation
            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->onDelete('cascade');
            
            // Qui a créé l'affectation rétroactive
            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('restrict');
            
            // Métadonnées temporelles
            $table->integer('days_in_past')
                ->comment('Nombre de jours dans le passé');
            
            $table->integer('confidence_score')
                ->default(0)
                ->comment('Score de confiance 0-100');
            
            // Données de validation (JSON)
            $table->json('warnings')
                ->nullable()
                ->comment('Warnings générés lors de la validation');
            
            $table->json('historical_data')
                ->nullable()
                ->comment('Données historiques complètes (statuts, kilométrage, etc.)');
            
            // Justification
            $table->text('justification')
                ->nullable()
                ->comment('Raison de la saisie rétroactive');
            
            // Timestamps
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            
            // Index pour performance
            $table->index('assignment_id');
            $table->index('created_by');
            $table->index('created_at');
            $table->index(['days_in_past', 'confidence_score']); // Pour analyses
        });

        // Commentaire sur la table
        DB::statement("COMMENT ON TABLE retroactive_assignment_logs IS 'Log d''audit pour toutes les affectations créées dans le passé - Enterprise-grade traceability'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retroactive_assignment_logs');
    }
};
