<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 🚀 INDEX STRATÉGIQUES SUPPLÉMENTAIRES - ENTERPRISE OPTIMIZATION
 *
 * Ajoute des index ciblés pour optimiser les requêtes critiques identifiées
 * lors de l'analyse de performance PostgreSQL 18.
 *
 * Index créés:
 * - Index de recherche sur registration_number (vehicles)
 * - Index de recherche sur license_number (drivers)
 * - Index BRIN pour données temporelles volumineuses
 * - Index composites optimisés pour dashboards
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

        // ===== INDEX VÉHICULES =====

        // Note: idx_vehicles_registration_plate existe déjà
        // Nous ajoutons un index composite pour vin uniquement si utile

        // Index composite pour filtrage véhicules par statut et type
        if (!$this->indexExists('idx_vehicles_type_status')) {
            DB::statement('
                CREATE INDEX idx_vehicles_type_status
                ON vehicles(organization_id, vehicle_type_id, status_id)
                WHERE deleted_at IS NULL
            ');
            echo "✅ Index idx_vehicles_type_status créé\n";
        }

        // ===== INDEX CHAUFFEURS =====

        // Note: idx_drivers_license existe déjà (license_number, organization_id)
        // Nous ajoutons un index pour recherche par téléphone personnel

        if (!$this->indexExists('idx_drivers_phone')) {
            DB::statement('
                CREATE INDEX idx_drivers_phone
                ON drivers(personal_phone)
                WHERE deleted_at IS NULL AND personal_phone IS NOT NULL
            ');
            echo "✅ Index idx_drivers_phone créé\n";
        }

        // ===== INDEX MAINTENANCE =====

        // Index BRIN pour maintenance_operations (données temporelles volumineuses)
        if (Schema::hasColumn('maintenance_operations', 'completed_at')) {
            if (!$this->indexExists('idx_maintenance_ops_brin')) {
                DB::statement('
                    CREATE INDEX idx_maintenance_ops_brin
                    ON maintenance_operations USING BRIN (completed_at, created_at)
                    WITH (pages_per_range = 128)
                ');
                echo "✅ Index idx_maintenance_ops_brin créé\n";
            }
        }

        // Index composite pour recherches maintenance par véhicule
        if (Schema::hasColumn('maintenance_operations', 'vehicle_id')) {
            if (!$this->indexExists('idx_maintenance_ops_vehicle')) {
                DB::statement('
                    CREATE INDEX idx_maintenance_ops_vehicle
                    ON maintenance_operations(vehicle_id, created_at DESC)
                    WHERE deleted_at IS NULL
                ');
                echo "✅ Index idx_maintenance_ops_vehicle créé\n";
            }
        }

        // ===== INDEX ASSIGNMENTS =====

        // Index BRIN pour assignments (données temporelles)
        if (!$this->indexExists('idx_assignments_brin')) {
            DB::statement('
                CREATE INDEX idx_assignments_brin
                ON assignments USING BRIN (start_datetime, end_datetime)
                WITH (pages_per_range = 64)
            ');
            echo "✅ Index idx_assignments_brin créé\n";
        }

        // Index pour recherche assignments actifs par organisation
        if (!$this->indexExists('idx_assignments_active')) {
            DB::statement('
                CREATE INDEX idx_assignments_active
                ON assignments(organization_id, status)
                WHERE status IN (\'active\', \'pending\')
            ');
            echo "✅ Index idx_assignments_active créé\n";
        }

        // ===== INDEX DOCUMENTS =====

        // Index GIN optimisé pour Full-Text Search avec configuration française
        if (Schema::hasColumn('documents', 'search_vector')) {
            if (!$this->indexExists('idx_documents_fts_optimized')) {
                DB::statement('
                    CREATE INDEX idx_documents_fts_optimized
                    ON documents USING GIN (search_vector)
                    WITH (fastupdate = off, gin_pending_list_limit = 4096)
                ');
                echo "✅ Index idx_documents_fts_optimized créé\n";
            }
        }

        // Index composite pour recherche documents par type et date
        if (Schema::hasColumn('documents', 'organization_id') && Schema::hasColumn('documents', 'document_type')) {
            if (!$this->indexExists('idx_documents_type_date')) {
                $whereClause = Schema::hasColumn('documents', 'deleted_at') ? 'WHERE deleted_at IS NULL' : '';
                DB::statement("
                    CREATE INDEX idx_documents_type_date
                    ON documents(organization_id, document_type, created_at DESC)
                    {$whereClause}
                ");
                echo "✅ Index idx_documents_type_date créé\n";
            }
        }

        // ===== INDEX INCIDENTS =====

        if (Schema::hasTable('incidents')) {
            // Index pour tableau de bord incidents
            if (Schema::hasColumn('incidents', 'organization_id') && Schema::hasColumn('incidents', 'occurred_at')) {
                if (!$this->indexExists('idx_incidents_dashboard')) {
                    // Construit l'index seulement avec les colonnes existantes
                    $columns = ['organization_id'];
                    if (Schema::hasColumn('incidents', 'status_id')) $columns[] = 'status_id';
                    if (Schema::hasColumn('incidents', 'severity')) $columns[] = 'severity';
                    $columns[] = 'occurred_at DESC';

                    $columnList = implode(', ', $columns);
                    $whereClause = Schema::hasColumn('incidents', 'deleted_at') ? 'WHERE deleted_at IS NULL' : '';

                    DB::statement("
                        CREATE INDEX idx_incidents_dashboard
                        ON incidents({$columnList})
                        {$whereClause}
                    ");
                    echo "✅ Index idx_incidents_dashboard créé\n";
                }
            }
        }

        // ===== INDEX VEHICLE_MILEAGE_READINGS =====

        // Index BRIN pour relevés kilométriques (données IoT volumineuses)
        if (Schema::hasColumn('vehicle_mileage_readings', 'recorded_at')) {
            if (!$this->indexExists('idx_mileage_readings_brin')) {
                DB::statement('
                    CREATE INDEX idx_mileage_readings_brin
                    ON vehicle_mileage_readings USING BRIN (recorded_at, created_at)
                    WITH (pages_per_range = 64)
                ');
                echo "✅ Index idx_mileage_readings_brin créé\n";
            }
        }

        // Index composite pour dernière lecture par véhicule
        if (Schema::hasColumn('vehicle_mileage_readings', 'vehicle_id') && Schema::hasColumn('vehicle_mileage_readings', 'recorded_at')) {
            if (!$this->indexExists('idx_mileage_readings_latest')) {
                DB::statement('
                    CREATE INDEX idx_mileage_readings_latest
                    ON vehicle_mileage_readings(vehicle_id, recorded_at DESC, created_at DESC)
                ');
                echo "✅ Index idx_mileage_readings_latest créé\n";
            }
        }

        // ===== COMPRESSION TOAST (PostgreSQL 14+) =====
        // Compression LZ4 pour colonnes volumineuses

        if (Schema::hasColumn('documents', 'content')) {
            DB::statement('ALTER TABLE documents ALTER COLUMN content SET COMPRESSION lz4');
            echo "✅ Compression LZ4 activée sur documents.content\n";
        }

        if (Schema::hasColumn('maintenance_operations', 'notes')) {
            DB::statement('ALTER TABLE maintenance_operations ALTER COLUMN notes SET COMPRESSION lz4');
            echo "✅ Compression LZ4 activée sur maintenance_operations.notes\n";
        }

        if (Schema::hasColumn('repair_requests', 'description')) {
            DB::statement('ALTER TABLE repair_requests ALTER COLUMN description SET COMPRESSION lz4');
            echo "✅ Compression LZ4 activée sur repair_requests.description\n";
        }

        // ===== ANALYSE DES TABLES MODIFIÉES =====
        $tables = [
            'vehicles',
            'drivers',
            'maintenance_operations',
            'assignments',
            'documents',
            'vehicle_mileage_readings'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ANALYZE {$table}");
            }
        }

        echo "✅ Index stratégiques créés et tables analysées avec succès\n";
    }

    /**
     * Vérifie si un index existe déjà
     */
    private function indexExists(string $indexName): bool
    {
        $result = DB::select("
            SELECT 1
            FROM pg_indexes
            WHERE indexname = ?
        ", [$indexName]);

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

        $indexes = [
            'idx_vehicles_type_status',
            'idx_drivers_phone',
            'idx_maintenance_ops_brin',
            'idx_maintenance_ops_vehicle',
            'idx_assignments_brin',
            'idx_assignments_active',
            'idx_documents_fts_optimized',
            'idx_documents_type_date',
            'idx_incidents_dashboard',
            'idx_mileage_readings_brin',
            'idx_mileage_readings_latest'
        ];

        foreach ($indexes as $index) {
            DB::statement("DROP INDEX IF EXISTS {$index}");
        }

        // Désactive compression (retour à pglz par défaut)
        if (Schema::hasColumn('documents', 'content')) {
            DB::statement('ALTER TABLE documents ALTER COLUMN content SET COMPRESSION pglz');
        }

        if (Schema::hasColumn('maintenance_operations', 'notes')) {
            DB::statement('ALTER TABLE maintenance_operations ALTER COLUMN notes SET COMPRESSION pglz');
        }

        if (Schema::hasColumn('repair_requests', 'description')) {
            DB::statement('ALTER TABLE repair_requests ALTER COLUMN description SET COMPRESSION pglz');
        }

        echo "✅ Index stratégiques supprimés\n";
    }
};
