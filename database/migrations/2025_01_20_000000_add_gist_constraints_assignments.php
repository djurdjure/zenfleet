<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🔒 Migration PostgreSQL GIST - Contraintes d'exclusion temporelle
 *
 * Implémente les contraintes enterprise-grade au niveau base de données:
 * - Exclusion automatique des chevauchements véhicule
 * - Exclusion automatique des chevauchements chauffeur
 * - Support des intervalles indéterminés (end_datetime = NULL)
 * - Performance optimisée avec index GIST
 */
return new class extends Migration
{
    /**
     * Disable transactions for this migration (required for CREATE INDEX CONCURRENTLY)
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip si PostgreSQL n'est pas utilisé (ex: tests avec SQLite)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            // Les contraintes GIST sont spécifiques à PostgreSQL
            // En test SQLite, on s'appuie sur la validation Livewire uniquement
            return;
        }

        // Activer l'extension btree_gist si pas déjà fait
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist;');

        // Fonction PL/pgSQL pour calculer l'intervalle effectif
        DB::statement("
            CREATE OR REPLACE FUNCTION assignment_interval(start_dt timestamp, end_dt timestamp)
            RETURNS tstzrange
            LANGUAGE plpgsql
            IMMUTABLE
            AS \$\$
            BEGIN
                -- Si end_dt est NULL, utiliser une date très future (2099-12-31)
                IF end_dt IS NULL THEN
                    RETURN tstzrange(start_dt, '2099-12-31 23:59:59'::timestamp);
                ELSE
                    RETURN tstzrange(start_dt, end_dt);
                END IF;
            END;
            \$\$;
        ");

        // Index GIST pour optimiser les requêtes de chevauchement
        DB::statement("
            CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_assignments_vehicle_temporal
            ON assignments USING GIST (
                organization_id,
                vehicle_id,
                assignment_interval(start_datetime, end_datetime)
            )
            WHERE deleted_at IS NULL;
        ");

        DB::statement("
            CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_assignments_driver_temporal
            ON assignments USING GIST (
                organization_id,
                driver_id,
                assignment_interval(start_datetime, end_datetime)
            )
            WHERE deleted_at IS NULL;
        ");

        // Contrainte d'exclusion pour véhicules
        // Empêche deux affectations du même véhicule qui se chevauchent
        DB::statement("
            DO \$\$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'assignments_vehicle_no_overlap'
                ) THEN
                    ALTER TABLE assignments
                    ADD CONSTRAINT assignments_vehicle_no_overlap
                    EXCLUDE USING GIST (
                        organization_id WITH =,
                        vehicle_id WITH =,
                        assignment_interval(start_datetime, end_datetime) WITH &&
                    )
                    WHERE (deleted_at IS NULL)
                    DEFERRABLE INITIALLY DEFERRED;
                END IF;
            END \$\$;
        ");

        // Contrainte d'exclusion pour chauffeurs
        // Empêche deux affectations du même chauffeur qui se chevauchent
        DB::statement("
            DO \$\$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'assignments_driver_no_overlap'
                ) THEN
                    ALTER TABLE assignments
                    ADD CONSTRAINT assignments_driver_no_overlap
                    EXCLUDE USING GIST (
                        organization_id WITH =,
                        driver_id WITH =,
                        assignment_interval(start_datetime, end_datetime) WITH &&
                    )
                    WHERE (deleted_at IS NULL)
                    DEFERRABLE INITIALLY DEFERRED;
                END IF;
            END \$\$;
        ");

        // Fonction pour calculer le statut (requis pour index)
        DB::statement("
            CREATE OR REPLACE FUNCTION assignment_computed_status(start_dt timestamp, end_dt timestamp)
            RETURNS text
            LANGUAGE plpgsql
            IMMUTABLE
            AS \$\$
            BEGIN
                IF start_dt > NOW() THEN
                    RETURN 'scheduled';
                ELSIF end_dt IS NULL OR end_dt > NOW() THEN
                    RETURN 'active';
                ELSE
                    RETURN 'completed';
                END IF;
            END;
            \$\$;
        ");

        // Index pour performance des requêtes courantes
        DB::statement("
            CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_assignments_status_computed
            ON assignments (
                organization_id,
                assignment_computed_status(start_datetime, end_datetime)
            )
            WHERE deleted_at IS NULL;
        ");

        // Index pour les requêtes temporelles
        DB::statement("
            CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_assignments_time_range
            ON assignments (organization_id, start_datetime, end_datetime)
            WHERE deleted_at IS NULL;
        ");

        // Vue matérialisée pour dashboard (optionnel, pour performance)
        DB::statement("
            CREATE MATERIALIZED VIEW IF NOT EXISTS assignment_stats_daily AS
            SELECT
                organization_id,
                DATE(start_datetime) as assignment_date,
                COUNT(*) as total_assignments,
                COUNT(*) FILTER (WHERE end_datetime IS NULL) as ongoing_assignments,
                COUNT(DISTINCT vehicle_id) as vehicles_used,
                COUNT(DISTINCT driver_id) as drivers_used,
                AVG(EXTRACT(EPOCH FROM (COALESCE(end_datetime, NOW()) - start_datetime))/3600) as avg_duration_hours
            FROM assignments
            WHERE deleted_at IS NULL
            GROUP BY organization_id, DATE(start_datetime)
            ORDER BY organization_id, assignment_date;
        ");

        DB::statement("
            CREATE UNIQUE INDEX ON assignment_stats_daily (organization_id, assignment_date);
        ");

        // Trigger pour refresh automatique des stats (optionnel)
        DB::statement("
            CREATE OR REPLACE FUNCTION refresh_assignment_stats()
            RETURNS TRIGGER AS \$\$
            BEGIN
                REFRESH MATERIALIZED VIEW CONCURRENTLY assignment_stats_daily;
                RETURN NULL;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("DROP TRIGGER IF EXISTS assignment_stats_refresh ON assignments;");

        DB::statement("
            CREATE TRIGGER assignment_stats_refresh
            AFTER INSERT OR UPDATE OR DELETE ON assignments
            FOR EACH STATEMENT
            EXECUTE FUNCTION refresh_assignment_stats();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Supprimer les contraintes d'exclusion
        DB::statement("ALTER TABLE assignments DROP CONSTRAINT IF EXISTS assignments_vehicle_no_overlap;");
        DB::statement("ALTER TABLE assignments DROP CONSTRAINT IF EXISTS assignments_driver_no_overlap;");

        // Supprimer les index
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS idx_assignments_vehicle_temporal;");
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS idx_assignments_driver_temporal;");
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS idx_assignments_status_computed;");
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS idx_assignments_time_range;");

        // Supprimer la vue matérialisée et triggers
        DB::statement("DROP TRIGGER IF EXISTS assignment_stats_refresh ON assignments;");
        DB::statement("DROP FUNCTION IF EXISTS refresh_assignment_stats();");
        DB::statement("DROP MATERIALIZED VIEW IF EXISTS assignment_stats_daily;");

        // Supprimer les fonctions
        DB::statement("DROP FUNCTION IF EXISTS assignment_interval(timestamp, timestamp);");
        DB::statement("DROP FUNCTION IF EXISTS assignment_computed_status(timestamp, timestamp);");

        // Note: On ne supprime pas l'extension btree_gist car elle peut être utilisée ailleurs
    }
};