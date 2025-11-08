<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 📊 MIGRATION: Table d'historique des changements de statuts - Enterprise Grade
 *
 * Objectif: Traçabilité complète de tous les changements de statuts pour véhicules et chauffeurs
 *
 * Fonctionnalités:
 * - Audit trail complet avec Event Sourcing léger
 * - Support multi-entités (vehicles, drivers, extensible)
 * - Métadonnées enrichies (reason, metadata JSON)
 * - Traçabilité utilisateur (qui a fait le changement)
 * - Timestamps précis pour analyses temporelles
 * - Support multi-tenant
 *
 * Cas d'usage:
 * - Audit RH / Conformité réglementaire
 * - Analyses prédictives (temps moyen en maintenance, etc.)
 * - Détection d'anomalies
 * - Reporting avancé
 *
 * @version 2.0-Enterprise
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('status_history', function (Blueprint $table) {
            $table->id();

            // ================================================================
            // POLYMORPHIC RELATIONS - Support multi-entités
            // ================================================================

            /**
             * Entité concernée par le changement de statut
             * - vehicles
             * - drivers
             * - (extensible à d'autres entités)
             */
            $table->morphs('statusable'); // Crée: statusable_type, statusable_id

            // ================================================================
            // STATUS TRACKING - Avant/Après
            // ================================================================

            /**
             * Statut précédent (nullable pour création initiale)
             */
            $table->string('from_status', 100)->nullable()
                  ->comment('Statut précédent (null si création)');

            /**
             * Nouveau statut
             */
            $table->string('to_status', 100)
                  ->comment('Nouveau statut appliqué');

            // ================================================================
            // BUSINESS CONTEXT - Métadonnées
            // ================================================================

            /**
             * Raison du changement de statut
             *
             * Exemples:
             * - "Affectation au chauffeur John Doe"
             * - "Panne moteur détectée"
             * - "Maintenance préventive planifiée"
             * - "Retour de congé"
             */
            $table->text('reason')->nullable()
                  ->comment('Raison ou description du changement');

            /**
             * Métadonnées JSON additionnelles
             *
             * Exemples de contenu:
             * {
             *   "assignment_id": 123,
             *   "maintenance_operation_id": 456,
             *   "repair_request_id": 789,
             *   "estimated_duration_days": 7,
             *   "cost_estimate": 1500.50,
             *   "priority": "high",
             *   "triggered_by": "automatic_system"
             * }
             */
            $table->json('metadata')->nullable()
                  ->comment('Métadonnées additionnelles au format JSON');

            // ================================================================
            // USER TRACKING - Traçabilité
            // ================================================================

            /**
             * Utilisateur qui a effectué le changement
             * (nullable pour changements automatiques)
             */
            $table->foreignId('changed_by_user_id')->nullable()
                  ->constrained('users')->onDelete('set null')
                  ->comment('Utilisateur ayant effectué le changement');

            /**
             * Type de changement
             * - manual: Changement manuel par utilisateur
             * - automatic: Changement automatique (workflow, cron, event)
             * - system: Changement système (migration, import, etc.)
             */
            $table->enum('change_type', ['manual', 'automatic', 'system'])
                  ->default('manual')
                  ->comment('Type de changement (manual/automatic/system)');

            /**
             * IP de l'utilisateur (pour audit de sécurité)
             */
            $table->string('ip_address', 45)->nullable()
                  ->comment('Adresse IP de l\'utilisateur');

            /**
             * User-Agent du navigateur (pour traçabilité complète)
             */
            $table->string('user_agent', 512)->nullable()
                  ->comment('User-Agent du navigateur');

            // ================================================================
            // MULTI-TENANT & TIMESTAMPS
            // ================================================================

            /**
             * Organisation propriétaire (multi-tenant)
             */
            $table->foreignId('organization_id')->nullable()
                  ->constrained()->onDelete('cascade')
                  ->comment('Organisation propriétaire');

            /**
             * Horodatage de la création de l'entrée historique
             */
            $table->timestamp('changed_at')->useCurrent()
                  ->comment('Date et heure du changement de statut');

            /**
             * Timestamps Laravel standard
             */
            $table->timestamps();

            // ================================================================
            // INDEXES - Performance & Queries
            // ================================================================

            // Index pour requêtes polymorphiques rapides
            $table->index(['statusable_type', 'statusable_id', 'changed_at'], 'idx_statusable_changed');

            // Index pour recherche par statut
            $table->index(['to_status', 'changed_at'], 'idx_to_status_changed');
            $table->index(['from_status', 'changed_at'], 'idx_from_status_changed');

            // Index pour recherche par utilisateur
            $table->index(['changed_by_user_id', 'changed_at'], 'idx_user_changed');

            // Index multi-tenant
            $table->index(['organization_id', 'changed_at'], 'idx_org_changed');

            // Index pour analytics par type de changement
            $table->index(['change_type', 'changed_at'], 'idx_change_type');

            // Index composite pour dashboard queries
            $table->index(
                ['statusable_type', 'to_status', 'organization_id', 'changed_at'],
                'idx_dashboard_analytics'
            );
        });

        echo "   ✅ Table status_history créée avec succès - Enterprise Grade\n";
        echo "   📊 Indexes créés pour performance optimale\n";
        echo "   🔍 Support polymorphique activé (vehicles, drivers, extensible)\n";
        echo "   🔐 Traçabilité utilisateur complète activée\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_history');

        echo "   ⚠️  Table status_history supprimée\n";
    }
};
