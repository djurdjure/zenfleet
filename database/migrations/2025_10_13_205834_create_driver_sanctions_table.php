<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table driver_sanctions
 *
 * Cette table gère les sanctions disciplinaires appliquées aux chauffeurs.
 * Elle est scopée par organization_id pour garantir l'isolation multi-tenant.
 *
 * @author ZenFleet Enterprise Team
 * @version 1.0.0
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_sanctions', function (Blueprint $table) {
            // Clés primaires et timestamps
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            // Multi-tenant: isolation par organisation
            $table->foreignId('organization_id')
                ->constrained('organizations')
                ->onDelete('cascade')
                ->comment('Organisation propriétaire de cette sanction');

            // Relation avec le chauffeur sanctionné
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade')
                ->comment('Chauffeur ayant reçu la sanction');

            // Superviseur ayant émis la sanction
            $table->foreignId('supervisor_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('Superviseur ayant émis la sanction');

            // Type de sanction (enum)
            $table->enum('sanction_type', [
                'avertissement_verbal',
                'avertissement_ecrit',
                'mise_a_pied',
                'mise_en_demeure'
            ])->comment('Type de sanction disciplinaire');

            // Description détaillée des faits
            $table->text('reason')
                ->comment('Description détaillée des faits ayant conduit à la sanction');

            // Date de la sanction
            $table->date('sanction_date')
                ->comment('Date à laquelle la sanction a été prononcée');

            // Pièce jointe (lettre, document officiel, etc.)
            $table->string('attachment_path', 500)->nullable()
                ->comment('Chemin vers le document attaché (lettre officielle, PV, etc.)');

            // Archivage
            $table->timestamp('archived_at')->nullable()
                ->comment('Date d\'archivage de la sanction (pour historique)');

            // Index pour optimiser les requêtes
            $table->index('organization_id', 'idx_sanctions_organization');
            $table->index('driver_id', 'idx_sanctions_driver');
            $table->index('supervisor_id', 'idx_sanctions_supervisor');
            $table->index('sanction_type', 'idx_sanctions_type');
            $table->index('sanction_date', 'idx_sanctions_date');
            $table->index('archived_at', 'idx_sanctions_archived');

            // Index composé pour requêtes multi-tenant
            $table->index(['organization_id', 'driver_id'], 'idx_sanctions_org_driver');
            $table->index(['organization_id', 'archived_at'], 'idx_sanctions_org_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_sanctions');
    }
};
