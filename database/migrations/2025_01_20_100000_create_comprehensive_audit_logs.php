<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🛡️ ZENFLEET ENTERPRISE AUDIT SYSTEM
 *
 * Système d'audit ultra-professionnel avec:
 * - Partitioning automatique par mois
 * - Row Level Security
 * - Performance optimisée
 * - Compliance enterprise
 *
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip si PostgreSQL n'est pas utilisé (ex: tests avec SQLite)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // ===== EXTENSIONS POSTGRESQL REQUISES =====
        DB::statement('CREATE EXTENSION IF NOT EXISTS "btree_gist"');

        // ===== TABLE AUDIT LOGS PRINCIPALE (PARTITIONED) =====
        DB::statement('
            CREATE TABLE comprehensive_audit_logs (
                id BIGSERIAL,
                uuid UUID DEFAULT gen_random_uuid(),

                -- Références organisationnelles
                organization_id BIGINT NOT NULL,
                user_id BIGINT,

                -- Classification des événements
                event_category VARCHAR(50) NOT NULL,
                event_type VARCHAR(50) NOT NULL,
                event_action VARCHAR(50) NOT NULL,

                -- Ressource ciblée
                resource_type VARCHAR(100),
                resource_id BIGINT,
                resource_identifier VARCHAR(255),

                -- Données de changement
                old_values JSONB,
                new_values JSONB,
                changes_summary TEXT,

                -- Contexte technique
                ip_address INET,
                user_agent TEXT,
                request_id UUID,
                session_id VARCHAR(255),

                -- Métadonnées business
                business_context JSONB,
                risk_level VARCHAR(20) DEFAULT \'low\',
                compliance_tags TEXT[],

                -- Timestamp partitioning key
                occurred_at TIMESTAMPTZ DEFAULT NOW(),
                created_at TIMESTAMPTZ DEFAULT NOW(),

                -- Contraintes
                CONSTRAINT pk_audit_logs PRIMARY KEY (id, occurred_at),
                CONSTRAINT fk_audit_organization FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
                CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                CONSTRAINT chk_event_category CHECK (event_category IN (
                    \'authentication\', \'authorization\', \'data_access\', \'data_modification\',
                    \'system_configuration\', \'user_management\', \'fleet_operations\',
                    \'financial\', \'maintenance\', \'compliance\', \'security\', \'integration\'
                )),
                CONSTRAINT chk_risk_level CHECK (risk_level IN (\'low\', \'medium\', \'high\', \'critical\'))
            ) PARTITION BY RANGE (occurred_at)
        ');

        // ===== CRÉATION DES PARTITIONS INITIALES =====
        $this->createInitialPartitions();

        // ===== INDEX OPTIMISÉS ENTERPRISE =====
        $this->createOptimizedIndexes();

        // ===== TRIGGERS AUTOMATIQUES =====
        $this->createAutomationTriggers();

        // ===== ROW LEVEL SECURITY =====
        $this->enableRowLevelSecurity();

        echo "✅ Système d'audit enterprise créé avec succès\n";
    }

    /**
     * Création des partitions initiales (12 mois)
     */
    private function createInitialPartitions(): void
    {
        for ($i = -6; $i <= 6; $i++) {
            $date = now()->addMonths($i);
            $startDate = $date->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->addMonth()->startOfMonth()->format('Y-m-d');
            $partitionName = 'audit_logs_' . $date->format('Y_m');

            DB::statement("
                CREATE TABLE {$partitionName} PARTITION OF comprehensive_audit_logs
                FOR VALUES FROM ('{$startDate}') TO ('{$endDate}')
            ");

            echo "✅ Partition {$partitionName} créée\n";
        }
    }

    /**
     * Index optimisés pour performance enterprise
     */
    private function createOptimizedIndexes(): void
    {
        $indexes = [
            // Index principaux
            'CREATE INDEX idx_audit_org_occurred ON comprehensive_audit_logs (organization_id, occurred_at DESC)',
            'CREATE INDEX idx_audit_user_occurred ON comprehensive_audit_logs (user_id, occurred_at DESC) WHERE user_id IS NOT NULL',
            'CREATE INDEX idx_audit_resource ON comprehensive_audit_logs (resource_type, resource_id, occurred_at DESC)',

            // Index de recherche
            'CREATE INDEX idx_audit_events ON comprehensive_audit_logs (event_category, event_type, event_action)',
            'CREATE INDEX idx_audit_risk ON comprehensive_audit_logs (risk_level, occurred_at DESC) WHERE risk_level IN (\'high\', \'critical\')',

            // Index pour compliance
            'CREATE INDEX idx_audit_compliance ON comprehensive_audit_logs USING GIN (compliance_tags)',
            'CREATE INDEX idx_audit_business ON comprehensive_audit_logs USING GIN (business_context)',

            // Index UUID pour recherches rapides
            'CREATE INDEX idx_audit_uuid ON comprehensive_audit_logs (uuid)',
            'CREATE INDEX idx_audit_request ON comprehensive_audit_logs (request_id) WHERE request_id IS NOT NULL'
        ];

        foreach ($indexes as $indexSql) {
            DB::statement($indexSql);
        }

        echo "✅ Index optimisés créés\n";
    }

    /**
     * Triggers pour automation enterprise
     */
    private function createAutomationTriggers(): void
    {
        // Fonction de nettoyage automatique
        DB::statement('
            CREATE OR REPLACE FUNCTION audit_cleanup_old_partitions()
            RETURNS void AS $$
            DECLARE
                retention_months INTEGER;
                cutoff_date DATE;
                partition_name TEXT;
            BEGIN
                -- Récupère la rétention depuis organizations (par défaut 24 mois)
                SELECT COALESCE(MIN(data_retention_period), 24) INTO retention_months
                FROM organizations WHERE data_retention_period IS NOT NULL;

                cutoff_date := CURRENT_DATE - (retention_months || \' months\')::INTERVAL;

                -- Supprime les partitions trop anciennes
                FOR partition_name IN
                    SELECT schemaname||\'.\'||tablename
                    FROM pg_tables
                    WHERE tablename LIKE \'audit_logs_%\'
                    AND tablename < \'audit_logs_\' || to_char(cutoff_date, \'YYYY_MM\')
                LOOP
                    EXECUTE \'DROP TABLE IF EXISTS \' || partition_name || \' CASCADE\';
                    RAISE NOTICE \'Dropped partition: %\', partition_name;
                END LOOP;
            END;
            $$ LANGUAGE plpgsql;
        ');

        // Fonction de création automatique de partitions
        DB::statement("
            CREATE OR REPLACE FUNCTION audit_create_monthly_partition()
            RETURNS void AS \$func\$
            DECLARE
                next_month DATE;
                partition_name TEXT;
                start_date TEXT;
                end_date TEXT;
                sql_command TEXT;
            BEGIN
                next_month := DATE_TRUNC('month', CURRENT_DATE + INTERVAL '2 months');
                partition_name := 'audit_logs_' || to_char(next_month, 'YYYY_MM');
                start_date := to_char(next_month, 'YYYY-MM-DD');
                end_date := to_char(next_month + INTERVAL '1 month', 'YYYY-MM-DD');

                -- Vérifie si la partition existe déjà
                IF NOT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = partition_name) THEN
                    sql_command := 'CREATE TABLE ' || partition_name ||
                                  ' PARTITION OF comprehensive_audit_logs FOR VALUES FROM (''' ||
                                  start_date || ''') TO (''' || end_date || ''')';
                    EXECUTE sql_command;
                    RAISE NOTICE 'Created partition: %', partition_name;
                END IF;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        echo "✅ Triggers d'automation créés\n";
    }

    /**
     * Configuration Row Level Security
     */
    private function enableRowLevelSecurity(): void
    {
        // Active RLS sur la table
        DB::statement('ALTER TABLE comprehensive_audit_logs ENABLE ROW LEVEL SECURITY');

        // Policy pour isolation par organisation
        DB::statement('
            CREATE POLICY audit_organization_isolation
            ON comprehensive_audit_logs
            USING (
                organization_id = COALESCE(
                    current_setting(\'app.current_organization_id\', true)::BIGINT,
                    (SELECT organization_id FROM users WHERE id = current_setting(\'app.current_user_id\', true)::BIGINT)
                )
            )
        ');

        // Policy pour Super Admins (accès complet)
        DB::statement('
            CREATE POLICY audit_super_admin_access
            ON comprehensive_audit_logs
            USING (
                EXISTS (
                    SELECT 1 FROM users u
                    JOIN model_has_roles mhr ON u.id = mhr.model_id
                    JOIN roles r ON mhr.role_id = r.id
                    WHERE u.id = current_setting(\'app.current_user_id\', true)::BIGINT
                    AND r.name = \'Super Admin\'
                    AND mhr.model_type = \'App\\\\Models\\\\User\'
                )
            )
        ');

        echo "✅ Row Level Security configuré\n";
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

        // Supprime les fonctions
        DB::statement('DROP FUNCTION IF EXISTS audit_cleanup_old_partitions()');
        DB::statement('DROP FUNCTION IF EXISTS audit_create_monthly_partition()');

        // Supprime toutes les partitions
        $partitions = DB::select("
            SELECT schemaname||'.'||tablename as full_name
            FROM pg_tables
            WHERE tablename LIKE 'audit_logs_%'
        ");

        foreach ($partitions as $partition) {
            DB::statement("DROP TABLE IF EXISTS {$partition->full_name} CASCADE");
        }

        // Supprime la table principale
        Schema::dropIfExists('comprehensive_audit_logs');

        echo "✅ Système d'audit supprimé\n";
    }
};