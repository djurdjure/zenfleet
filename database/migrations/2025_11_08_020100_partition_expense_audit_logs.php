<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 🚀 PARTITIONNEMENT EXPENSE_AUDIT_LOGS - ENTERPRISE OPTIMIZATION
 *
 * Transforme la table expense_audit_logs en table partitionnée par mois
 * pour gérer la croissance exponentielle des données d'audit de dépenses.
 *
 * Stratégie:
 * - Partitionnement par RANGE sur created_at
 * - Partitions mensuelles avec création automatique
 * - Migration des données existantes
 * - Index optimisés par partition
 *
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-08
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip si PostgreSQL n'est pas utilisé
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // ===== VÉRIFICATION TABLE EXISTANTE =====
        if (!Schema::hasTable('expense_audit_logs')) {
            echo "⚠️  Table expense_audit_logs n'existe pas, skip\n";
            return;
        }

        // ===== COMPTAGE DONNÉES EXISTANTES =====
        $existingCount = DB::table('expense_audit_logs')->count();
        echo "📊 Données existantes: {$existingCount} lignes\n";

        // ===== RENOMMAGE TABLE EXISTANTE =====
        DB::statement('ALTER TABLE expense_audit_logs RENAME TO expense_audit_logs_old');
        echo "✅ Table renommée: expense_audit_logs → expense_audit_logs_old\n";

        // ===== CRÉATION TABLE PARTITIONNÉE =====
        DB::statement('
            CREATE TABLE expense_audit_logs (
                id BIGSERIAL,
                organization_id BIGINT NOT NULL,
                vehicle_expense_id BIGINT NOT NULL,
                user_id BIGINT NOT NULL,
                action VARCHAR(50) NOT NULL,
                action_category VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                old_values JSON,
                new_values JSON,
                changed_fields JSON NOT NULL DEFAULT \'[]\'::json,
                ip_address VARCHAR(45),
                user_agent VARCHAR(255),
                session_id VARCHAR(255),
                request_id VARCHAR(255),
                previous_status VARCHAR(50),
                new_status VARCHAR(50),
                previous_amount NUMERIC(15,2),
                new_amount NUMERIC(15,2),
                is_sensitive BOOLEAN NOT NULL DEFAULT false,
                requires_review BOOLEAN NOT NULL DEFAULT false,
                reviewed BOOLEAN NOT NULL DEFAULT false,
                reviewed_by BIGINT,
                reviewed_at TIMESTAMP(0),
                review_notes TEXT,
                is_anomaly BOOLEAN NOT NULL DEFAULT false,
                anomaly_details TEXT,
                risk_level VARCHAR(20),
                metadata JSON NOT NULL DEFAULT \'{}\'::json,
                tags JSON NOT NULL DEFAULT \'[]\'::json,
                created_at TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,

                -- Contraintes
                CONSTRAINT pk_expense_audit_logs PRIMARY KEY (id, created_at),
                CONSTRAINT fk_expense_audit_org FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
                CONSTRAINT fk_expense_audit_expense FOREIGN KEY (vehicle_expense_id) REFERENCES vehicle_expenses(id) ON DELETE CASCADE,
                CONSTRAINT fk_expense_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_expense_audit_reviewer FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
            ) PARTITION BY RANGE (created_at)
        ');
        echo "✅ Table partitionnée créée\n";

        // ===== CRÉATION DES PARTITIONS INITIALES =====
        $this->createInitialPartitions();

        // ===== MIGRATION DES DONNÉES EXISTANTES =====
        if ($existingCount > 0) {
            DB::statement('
                INSERT INTO expense_audit_logs
                SELECT * FROM expense_audit_logs_old
            ');
            echo "✅ {$existingCount} lignes migrées vers la table partitionnée\n";
        }

        // ===== CRÉATION DES INDEX OPTIMISÉS =====
        $this->createOptimizedIndexes();

        // ===== CRÉATION FONCTION AUTO-PARTITION =====
        $this->createAutoPartitionFunction();

        // ===== SUPPRESSION TABLE ANCIENNE =====
        DB::statement('DROP TABLE expense_audit_logs_old CASCADE');
        echo "✅ Ancienne table supprimée\n";

        echo "✅ Partitionnement expense_audit_logs terminé avec succès\n";
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
            $partitionName = 'expense_audit_logs_' . $date->format('Y_m');

            DB::statement("
                CREATE TABLE {$partitionName} PARTITION OF expense_audit_logs
                FOR VALUES FROM ('{$startDate}') TO ('{$endDate}')
            ");

            echo "  ✅ Partition {$partitionName} créée\n";
        }
    }

    /**
     * Index optimisés pour chaque partition
     */
    private function createOptimizedIndexes(): void
    {
        $indexes = [
            // Index principaux
            'CREATE INDEX idx_expense_audit_org_created ON expense_audit_logs (organization_id, created_at DESC)',
            'CREATE INDEX idx_expense_audit_expense ON expense_audit_logs (vehicle_expense_id, created_at DESC)',
            'CREATE INDEX idx_expense_audit_user ON expense_audit_logs (user_id, created_at DESC)',

            // Index de recherche
            'CREATE INDEX idx_expense_audit_action ON expense_audit_logs (action, action_category)',
            'CREATE INDEX idx_expense_audit_review ON expense_audit_logs (requires_review, reviewed) WHERE requires_review = true',
            'CREATE INDEX idx_expense_audit_anomaly ON expense_audit_logs (is_anomaly, risk_level) WHERE is_anomaly = true',

            // Index pour sessions
            'CREATE INDEX idx_expense_audit_session ON expense_audit_logs (session_id) WHERE session_id IS NOT NULL',
            'CREATE INDEX idx_expense_audit_ip ON expense_audit_logs (ip_address, created_at DESC)'
        ];

        foreach ($indexes as $indexSql) {
            DB::statement($indexSql);
        }

        echo "✅ Index optimisés créés\n";
    }

    /**
     * Fonction de création automatique de partitions futures
     */
    private function createAutoPartitionFunction(): void
    {
        DB::statement("
            CREATE OR REPLACE FUNCTION expense_audit_create_monthly_partition()
            RETURNS void AS \$func\$
            DECLARE
                next_month DATE;
                partition_name TEXT;
                start_date TEXT;
                end_date TEXT;
                sql_command TEXT;
            BEGIN
                next_month := DATE_TRUNC('month', CURRENT_DATE + INTERVAL '2 months');
                partition_name := 'expense_audit_logs_' || to_char(next_month, 'YYYY_MM');
                start_date := to_char(next_month, 'YYYY-MM-DD');
                end_date := to_char(next_month + INTERVAL '1 month', 'YYYY-MM-DD');

                -- Vérifie si la partition existe déjà
                IF NOT EXISTS (SELECT 1 FROM pg_tables WHERE tablename = partition_name) THEN
                    sql_command := 'CREATE TABLE ' || partition_name ||
                                  ' PARTITION OF expense_audit_logs FOR VALUES FROM (''' ||
                                  start_date || ''') TO (''' || end_date || ''')';
                    EXECUTE sql_command;
                    RAISE NOTICE 'Created partition: %', partition_name;
                END IF;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        echo "✅ Fonction auto-partition créée\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Supprime la fonction
        DB::statement('DROP FUNCTION IF EXISTS expense_audit_create_monthly_partition()');

        // Récupère toutes les partitions
        $partitions = DB::select("
            SELECT schemaname||'.'||tablename as full_name
            FROM pg_tables
            WHERE tablename LIKE 'expense_audit_logs_%'
        ");

        // Supprime toutes les partitions
        foreach ($partitions as $partition) {
            DB::statement("DROP TABLE IF EXISTS {$partition->full_name} CASCADE");
        }

        // Supprime la table principale
        Schema::dropIfExists('expense_audit_logs');

        echo "✅ Partitionnement expense_audit_logs annulé\n";
    }
};
