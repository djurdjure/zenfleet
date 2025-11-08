<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 🚀 OPTIMISATION POSTGRESQL 18 - CONFIGURATION ENTERPRISE
 *
 * Cette migration documente les configurations PostgreSQL optimales
 * à appliquer via docker-compose.yml pour performance enterprise-grade.
 *
 * IMPORTANT: Les configurations PostgreSQL ne peuvent pas être modifiées
 * via SQL au runtime. Elles doivent être appliquées via:
 * - docker-compose.yml (command parameters)
 * - postgresql.conf (fichier de configuration)
 *
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-08
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cette migration:
     * 1. Crée une vue de monitoring de santé DB
     * 2. Configure les extensions nécessaires
     * 3. Optimise les statistiques de tables critiques
     */
    public function up(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // ===== EXTENSIONS POSTGRESQL =====
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_stat_statements');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        // ===== SUPPRESSION DES VUES EXISTANTES =====
        DB::statement('DROP VIEW IF EXISTS v_database_health CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_slow_queries CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_table_sizes CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_inefficient_indexes CASCADE');

        // ===== VUE DE MONITORING DB HEALTH =====
        DB::statement('
            CREATE OR REPLACE VIEW v_database_health AS
            SELECT
                pg_database_size(current_database()) as db_size_bytes,
                pg_size_pretty(pg_database_size(current_database())) as db_size,
                (SELECT count(*) FROM pg_stat_activity) as total_connections,
                (SELECT count(*) FROM pg_stat_activity WHERE state = \'active\') as active_queries,
                (SELECT COALESCE(ROUND(AVG(EXTRACT(epoch FROM (now() - query_start)))::numeric, 2), 0)
                 FROM pg_stat_activity WHERE state = \'active\') as avg_query_duration_sec,
                ROUND((blks_hit::numeric / NULLIF(blks_hit + blks_read, 0) * 100)::numeric, 2) as cache_hit_ratio,
                xact_commit + xact_rollback as total_transactions,
                xact_commit as committed_transactions,
                xact_rollback as rolled_back_transactions,
                ROUND((xact_commit::numeric / NULLIF(xact_commit + xact_rollback, 0) * 100)::numeric, 2) as commit_ratio,
                conflicts as total_conflicts,
                temp_files as temp_files_created,
                pg_size_pretty(temp_bytes) as temp_bytes_used,
                deadlocks as total_deadlocks,
                blk_read_time as block_read_time_ms,
                blk_write_time as block_write_time_ms,
                stats_reset as stats_reset_at
            FROM pg_stat_database
            WHERE datname = current_database()
        ');

        // ===== VUE TOP QUERIES LENTES =====
        DB::statement('
            CREATE OR REPLACE VIEW v_slow_queries AS
            SELECT
                query,
                calls,
                ROUND(total_exec_time::numeric / calls, 2) as avg_time_ms,
                ROUND(total_exec_time::numeric, 2) as total_time_ms,
                ROUND((100 * total_exec_time / SUM(total_exec_time) OVER ())::numeric, 2) as pct_total_time,
                rows,
                ROUND((100 * (shared_blks_hit::numeric) / NULLIF(shared_blks_hit + shared_blks_read, 0))::numeric, 2) as cache_hit_pct
            FROM pg_stat_statements
            WHERE calls > 10
            ORDER BY total_exec_time DESC
            LIMIT 50
        ');

        // ===== VUE TAILLE DES TABLES =====
        DB::statement('
            CREATE OR REPLACE VIEW v_table_sizes AS
            SELECT
                schemaname,
                tablename,
                pg_size_pretty(pg_total_relation_size(schemaname||\'.\'||tablename)) AS total_size,
                pg_size_pretty(pg_relation_size(schemaname||\'.\'||tablename)) AS table_size,
                pg_size_pretty(pg_total_relation_size(schemaname||\'.\'||tablename) - pg_relation_size(schemaname||\'.\'||tablename)) AS indexes_size,
                pg_total_relation_size(schemaname||\'.\'||tablename) AS total_size_bytes
            FROM pg_tables
            WHERE schemaname = \'public\'
            ORDER BY pg_total_relation_size(schemaname||\'.\'||tablename) DESC
        ');

        // ===== VUE INEFFICIENT INDEXES =====
        DB::statement('
            CREATE OR REPLACE VIEW v_inefficient_indexes AS
            SELECT
                schemaname,
                relname as tablename,
                indexrelname as indexname,
                idx_scan as index_scans,
                idx_tup_read as tuples_read,
                idx_tup_fetch as tuples_fetched,
                pg_size_pretty(pg_relation_size(indexrelid)) as index_size,
                pg_relation_size(indexrelid) as index_size_bytes
            FROM pg_stat_user_indexes
            WHERE idx_scan < 50  -- Index utilisé moins de 50 fois
            AND pg_relation_size(indexrelid) > 65536  -- Plus de 64KB
            ORDER BY pg_relation_size(indexrelid) DESC
        ');

        // ===== STATISTIQUES ÉTENDUES MULTI-COLONNES =====
        // Améliore le query planning pour colonnes corrélées

        if (!$this->statisticsExists('stat_vehicles_org_status')) {
            DB::statement('
                CREATE STATISTICS stat_vehicles_org_status
                ON organization_id, status_id
                FROM vehicles
            ');
        }

        if (!$this->statisticsExists('stat_vehicles_org_depot')) {
            DB::statement('
                CREATE STATISTICS stat_vehicles_org_depot
                ON organization_id, depot_id, status_id
                FROM vehicles
            ');
        }

        if (!$this->statisticsExists('stat_expenses_org_date')) {
            DB::statement('
                CREATE STATISTICS stat_expenses_org_date
                ON organization_id, expense_date, approval_status
                FROM vehicle_expenses
            ');
        }

        if (!$this->statisticsExists('stat_repairs_status_priority')) {
            DB::statement('
                CREATE STATISTICS stat_repairs_status_priority
                ON status, priority, urgency
                FROM repair_requests
            ');
        }

        // ===== ANALYSE DES TABLES CRITIQUES =====
        $criticalTables = [
            'vehicles',
            'drivers',
            'vehicle_expenses',
            'repair_requests',
            'assignments',
            'maintenance_schedules',
            'comprehensive_audit_logs'
        ];

        foreach ($criticalTables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ANALYZE {$table}");
            }
        }

        echo "✅ Configuration PostgreSQL documentée et monitoring activé\n";
        echo "⚠️  Appliquer les paramètres PostgreSQL via docker-compose.yml\n";
    }

    /**
     * Vérifie si une statistique étendue existe déjà
     */
    private function statisticsExists(string $statsName): bool
    {
        $result = DB::select("
            SELECT 1
            FROM pg_statistic_ext
            WHERE stxname = ?
        ", [$statsName]);

        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Supprime les vues de monitoring
        DB::statement('DROP VIEW IF EXISTS v_database_health CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_slow_queries CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_table_sizes CASCADE');
        DB::statement('DROP VIEW IF EXISTS v_inefficient_indexes CASCADE');

        // Supprime les statistiques étendues
        DB::statement('DROP STATISTICS IF EXISTS stat_vehicles_org_status');
        DB::statement('DROP STATISTICS IF EXISTS stat_vehicles_org_depot');
        DB::statement('DROP STATISTICS IF EXISTS stat_expenses_org_date');
        DB::statement('DROP STATISTICS IF EXISTS stat_repairs_status_priority');

        echo "✅ Optimisations PostgreSQL supprimées\n";
    }
};
