<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚀 ZENFLEET ENTERPRISE OPTIMIZATIONS FINAL
 *
 * Version simplifiée des optimisations enterprise:
 * - Index critiques seulement
 * - Fonctions utilitaires de base
 * - Métriques performance essentielles
 *
 * @version 1.0 Final
 * @author ZenFleet Architecture Team
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ===== INDEX CRITIQUES PERFORMANCE =====
        $this->addCriticalIndexes();

        // ===== FONCTIONS UTILITAIRES DE BASE =====
        $this->createBasicUtilityFunctions();

        // ===== MÉTRIQUES ESSENTIELLES =====
        $this->createBasicMetrics();

        echo "✅ Optimisations enterprise finales appliquées\n";
    }

    /**
     * Index critiques pour performance
     */
    private function addCriticalIndexes(): void
    {
        $indexes = [
            // Index véhicules essentiels
            'CREATE INDEX IF NOT EXISTS idx_vehicles_org_active ON vehicles (organization_id, status_id) WHERE deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_vehicles_mileage ON vehicles (current_mileage)',

            // Index chauffeurs essentiels
            'CREATE INDEX IF NOT EXISTS idx_drivers_org_active ON drivers (organization_id, status_id) WHERE deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_drivers_license_expiry ON drivers (license_expiry_date) WHERE license_expiry_date IS NOT NULL',

            // Index assignments critiques
            'CREATE INDEX IF NOT EXISTS idx_assignments_active ON assignments (vehicle_id, driver_id, end_datetime) WHERE end_datetime IS NULL AND deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_assignments_date_range ON assignments (organization_id, start_datetime, end_datetime)',

            // Index audit essentiels
            'CREATE INDEX IF NOT EXISTS idx_audit_org_recent ON comprehensive_audit_logs (organization_id, occurred_at DESC)',
            'CREATE INDEX IF NOT EXISTS idx_audit_high_risk ON comprehensive_audit_logs (risk_level, occurred_at DESC) WHERE risk_level IN (\'high\', \'critical\')',

            // Index multi-tenant
            'CREATE INDEX IF NOT EXISTS idx_user_org_active ON user_organizations (user_id, is_active, organization_id) WHERE is_active = TRUE'
        ];

        foreach ($indexes as $indexSql) {
            try {
                DB::statement($indexSql);
            } catch (\Exception $e) {
                echo "⚠️  Index existe déjà: " . substr($indexSql, 0, 50) . "...\n";
            }
        }

        echo "✅ Index critiques créés\n";
    }

    /**
     * Fonctions utilitaires de base
     */
    private function createBasicUtilityFunctions(): void
    {
        // Fonction de nettoyage global simple
        DB::statement("
            CREATE OR REPLACE FUNCTION basic_system_cleanup()
            RETURNS void AS \$func\$
            BEGIN
                -- Nettoie les sessions expirées (plus de 7 jours)
                DELETE FROM sessions WHERE last_activity < EXTRACT(EPOCH FROM NOW() - INTERVAL '7 days');

                -- Nettoie les tokens expirés
                DELETE FROM personal_access_tokens WHERE expires_at IS NOT NULL AND expires_at < NOW();

                RAISE NOTICE 'Basic system cleanup completed at %', NOW();
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        // Fonction de statistiques véhicules simple
        DB::statement("
            CREATE OR REPLACE FUNCTION get_vehicle_stats(p_organization_id BIGINT DEFAULT NULL)
            RETURNS TABLE(
                total_vehicles INTEGER,
                active_vehicles INTEGER,
                total_drivers INTEGER,
                active_drivers INTEGER,
                active_assignments INTEGER
            ) AS \$func\$
            BEGIN
                RETURN QUERY
                SELECT
                    COUNT(DISTINCT v.id)::INTEGER as total_vehicles,
                    COUNT(DISTINCT v.id) FILTER (WHERE vs.status = 'active')::INTEGER as active_vehicles,
                    COUNT(DISTINCT d.id)::INTEGER as total_drivers,
                    COUNT(DISTINCT d.id) FILTER (WHERE ds.status = 'active')::INTEGER as active_drivers,
                    COUNT(DISTINCT a.id) FILTER (WHERE a.end_datetime IS NULL)::INTEGER as active_assignments
                FROM organizations o
                LEFT JOIN vehicles v ON o.id = v.organization_id AND v.deleted_at IS NULL
                LEFT JOIN vehicle_statuses vs ON v.status_id = vs.id
                LEFT JOIN drivers d ON o.id = d.organization_id AND d.deleted_at IS NULL
                LEFT JOIN driver_statuses ds ON d.status_id = ds.id
                LEFT JOIN assignments a ON v.id = a.vehicle_id AND a.deleted_at IS NULL
                WHERE (p_organization_id IS NULL OR o.id = p_organization_id)
                AND o.status = 'active';
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        echo "✅ Fonctions utilitaires de base créées\n";
    }

    /**
     * Métriques performance essentielles
     */
    private function createBasicMetrics(): void
    {
        // Table simple pour métriques quotidiennes
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // Métriques de base
            $table->integer('total_vehicles')->default(0);
            $table->integer('active_vehicles')->default(0);
            $table->integer('total_drivers')->default(0);
            $table->integer('active_drivers')->default(0);
            $table->integer('daily_assignments')->default(0);
            $table->decimal('total_mileage', 12, 2)->default(0);

            // Métriques coûts
            $table->decimal('daily_maintenance_cost', 10, 2)->default(0);
            $table->decimal('daily_fuel_cost', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['metric_date', 'organization_id']);
            $table->index(['organization_id', 'metric_date']);
        });

        // Fonction de calcul métriques quotidiennes
        DB::statement("
            CREATE OR REPLACE FUNCTION calculate_daily_metrics(p_date DATE DEFAULT CURRENT_DATE)
            RETURNS void AS \$func\$
            DECLARE
                org_record RECORD;
                stats RECORD;
            BEGIN
                FOR org_record IN SELECT id FROM organizations WHERE status = 'active'
                LOOP
                    -- Calcule les stats pour cette organisation
                    SELECT * INTO stats FROM get_vehicle_stats(org_record.id);

                    -- Insert ou update métriques
                    INSERT INTO daily_metrics (
                        metric_date, organization_id, total_vehicles, active_vehicles,
                        total_drivers, active_drivers, daily_assignments
                    ) VALUES (
                        p_date, org_record.id, stats.total_vehicles, stats.active_vehicles,
                        stats.total_drivers, stats.active_drivers, stats.active_assignments
                    )
                    ON CONFLICT (metric_date, organization_id)
                    DO UPDATE SET
                        total_vehicles = EXCLUDED.total_vehicles,
                        active_vehicles = EXCLUDED.active_vehicles,
                        total_drivers = EXCLUDED.total_drivers,
                        active_drivers = EXCLUDED.active_drivers,
                        daily_assignments = EXCLUDED.daily_assignments,
                        updated_at = NOW();
                END LOOP;

                RAISE NOTICE 'Daily metrics calculated for %', p_date;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        echo "✅ Métriques essentielles créées\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprime les fonctions
        DB::statement('DROP FUNCTION IF EXISTS calculate_daily_metrics(DATE)');
        DB::statement('DROP FUNCTION IF EXISTS get_vehicle_stats(BIGINT)');
        DB::statement('DROP FUNCTION IF EXISTS basic_system_cleanup()');

        // Supprime la table métriques
        Schema::dropIfExists('daily_metrics');

        echo "✅ Optimisations enterprise supprimées\n";
    }
};