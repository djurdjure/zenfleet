<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // --- Index sur les clés étrangères d'organisation ---
        // Essentiel pour la performance du multi-tenant.
        // Chaque requête étant filtrée par organization_id, cet index est capital.
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('organization_id', 'idx_vehicles_organization');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->index('organization_id', 'idx_drivers_organization');
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->index('organization_id', 'idx_assignments_organization');
        });
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->index('organization_id', 'idx_maintenance_logs_organization');
        });
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->index('organization_id', 'idx_maintenance_plans_organization');
        });

        // --- Index composites pour les recherches et filtres fréquents ---
        // Un index sur plusieurs colonnes est plus performant qu'un index par colonne
        // lorsque les recherches utilisent ces colonnes conjointement.

        // Pour filtrer les véhicules par statut au sein d'une organisation.
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index(['status_id', 'organization_id'], 'idx_vehicles_status_org');
        });

        // Pour rechercher des affectations dans une plage de dates pour une organisation.
        Schema::table('assignments', function (Blueprint $table) {
            $table->index(['start_datetime', 'end_datetime', 'organization_id'], 'idx_assignments_dates_org');
        });

        // Pour trouver rapidement les prochaines maintenances dues pour une organisation.
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->index(['next_due_date', 'organization_id'], 'idx_maintenance_plans_next_due_date');
            $table->index(['next_due_mileage', 'organization_id'], 'idx_maintenance_plans_next_due_mileage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // La méthode down() est cruciale pour pouvoir annuler la migration si besoin.
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('idx_vehicles_organization');
            $table->dropIndex('idx_vehicles_status_org');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex('idx_drivers_organization');
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex('idx_assignments_organization');
            $table->dropIndex('idx_assignments_dates_org');
        });
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropIndex('idx_maintenance_logs_organization');
        });
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->dropIndex('idx_maintenance_plans_organization');
            $table->dropIndex('idx_maintenance_plans_next_due_date');
            $table->dropIndex('idx_maintenance_plans_next_due_mileage');
        });
    }
};
