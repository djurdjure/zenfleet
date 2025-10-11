<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚗 ZENFLEET ASSIGNMENTS ENTERPRISE MODULE
 *
 * Table d'affectations véhicule ↔ chauffeur avec:
 * - Anti-chevauchement GIST exclusion constraints
 * - Support durées indéterminées (end_datetime NULL)
 * - Audit trail complet
 * - Index optimisés pour performance
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
        // Vérifier que les tables dépendantes existent
        if (!Schema::hasTable('vehicles') || !Schema::hasTable('drivers')) {
            echo "⚠️  Tables vehicles/drivers n'existent pas encore, skip assignments creation\n";
            return;
        }

        // Vérifier si la table existe déjà
        if (Schema::hasTable('assignments')) {
            echo "⚠️  Table assignments existe déjà, extension avec nouvelles colonnes\n";
            $this->extendExistingTable();
        } else {
            $this->createNewTable();
        }

        // Ajouter les contraintes d'exclusion anti-chevauchement
        $this->addOverlapPrevention();

        // Index optimisés pour performance
        $this->addPerformanceIndexes();

        echo "✅ Module Assignments Enterprise créé\n";
    }

    /**
     * Création nouvelle table assignments
     */
    private function createNewTable(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // Relations principales
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');

            // Période d'affectation
            $table->timestamp('start_datetime'); // NOT NULL - début obligatoire
            $table->timestamp('end_datetime')->nullable(); // NULL = durée indéterminée

            // Métadonnées business
            $table->text('reason')->nullable(); // Motif de l'affectation
            $table->text('notes')->nullable(); // Notes libres

            // Kilométrage (optionnel)
            $table->bigInteger('start_mileage')->nullable();
            $table->bigInteger('end_mileage')->nullable();

            // Audit trail
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('ended_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('ended_at')->nullable();

            // Statut calculé et business
            $table->string('status')->default('active'); // active, scheduled, completed, cancelled
            $table->decimal('estimated_duration_hours', 8, 2)->nullable();
            $table->decimal('actual_duration_hours', 8, 2)->nullable();

            // Géolocalisation de départ/fin (optionnel)
            $table->decimal('start_latitude', 10, 8)->nullable();
            $table->decimal('start_longitude', 11, 8)->nullable();
            $table->decimal('end_latitude', 10, 8)->nullable();
            $table->decimal('end_longitude', 11, 8)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Contraintes business
            $table->index(['organization_id', 'status']);
            $table->index(['vehicle_id', 'start_datetime']);
            $table->index(['driver_id', 'start_datetime']);
            $table->index(['start_datetime', 'end_datetime']);
        });

        echo "✅ Table assignments créée\n";
    }

    /**
     * Extension table existante
     */
    private function extendExistingTable(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Ajouter colonnes manquantes si elles n'existent pas
            if (!Schema::hasColumn('assignments', 'reason')) {
                $table->text('reason')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('assignments', 'updated_by_user_id')) {
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('assignments', 'ended_by_user_id')) {
                $table->foreignId('ended_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('assignments', 'ended_at')) {
                $table->timestamp('ended_at')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('assignments', 'estimated_duration_hours')) {
                $table->decimal('estimated_duration_hours', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('assignments', 'actual_duration_hours')) {
                $table->decimal('actual_duration_hours', 8, 2)->nullable();
            }
        });

        echo "✅ Table assignments étendue\n";
    }

    /**
     * Contraintes anti-chevauchement GIST
     */
    private function addOverlapPrevention(): void
    {
        // Skip si PostgreSQL n'est pas utilisé (ex: tests avec SQLite)
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        // Vérifier si btree_gist est disponible
        try {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "btree_gist"');
        } catch (\Exception $e) {
            echo "⚠️  Extension btree_gist non disponible, utilisation de triggers\n";
            $this->addTriggerBasedPrevention();
            return;
        }

        // Contrainte d'exclusion pour véhicules
        try {
            DB::statement('
                ALTER TABLE assignments
                ADD CONSTRAINT assignments_vehicle_no_overlap
                EXCLUDE USING GIST (
                    vehicle_id WITH =,
                    organization_id WITH =,
                    tsrange(start_datetime, COALESCE(end_datetime, \'infinity\'::timestamp), \'[)\') WITH &&
                )
                WHERE (deleted_at IS NULL)
            ');
            echo "✅ Contrainte anti-chevauchement véhicules (GIST)\n";
        } catch (\Exception $e) {
            echo "⚠️  Contrainte GIST véhicules échouée: " . $e->getMessage() . "\n";
        }

        // Contrainte d'exclusion pour chauffeurs
        try {
            DB::statement('
                ALTER TABLE assignments
                ADD CONSTRAINT assignments_driver_no_overlap
                EXCLUDE USING GIST (
                    driver_id WITH =,
                    organization_id WITH =,
                    tsrange(start_datetime, COALESCE(end_datetime, \'infinity\'::timestamp), \'[)\') WITH &&
                )
                WHERE (deleted_at IS NULL)
            ');
            echo "✅ Contrainte anti-chevauchement chauffeurs (GIST)\n";
        } catch (\Exception $e) {
            echo "⚠️  Contrainte GIST chauffeurs échouée: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Alternative avec triggers si GIST non disponible
     */
    private function addTriggerBasedPrevention(): void
    {
        // Fonction de validation des chevauchements
        DB::statement("
            CREATE OR REPLACE FUNCTION check_assignment_overlaps()
            RETURNS TRIGGER AS \$func\$
            DECLARE
                conflict_count INTEGER;
            BEGIN
                -- Vérifier chevauchement véhicule
                SELECT COUNT(*) INTO conflict_count
                FROM assignments a
                WHERE a.id != COALESCE(NEW.id, 0)
                AND a.vehicle_id = NEW.vehicle_id
                AND a.organization_id = NEW.organization_id
                AND a.deleted_at IS NULL
                AND (
                    -- Cas 1: Nouvelle affectation commence avant fin existante
                    (a.end_datetime IS NOT NULL AND NEW.start_datetime < a.end_datetime AND
                     (NEW.end_datetime IS NULL OR NEW.end_datetime > a.start_datetime))
                    OR
                    -- Cas 2: Affectation existante indéterminée
                    (a.end_datetime IS NULL AND NEW.start_datetime >= a.start_datetime)
                    OR
                    -- Cas 3: Nouvelle affectation indéterminée
                    (NEW.end_datetime IS NULL AND a.start_datetime >= NEW.start_datetime)
                );

                IF conflict_count > 0 THEN
                    RAISE EXCEPTION 'Chevauchement detecte pour vehicule ID % entre % et %',
                        NEW.vehicle_id, NEW.start_datetime, COALESCE(NEW.end_datetime::text, 'indetermine');
                END IF;

                -- Vérifier chevauchement chauffeur
                SELECT COUNT(*) INTO conflict_count
                FROM assignments a
                WHERE a.id != COALESCE(NEW.id, 0)
                AND a.driver_id = NEW.driver_id
                AND a.organization_id = NEW.organization_id
                AND a.deleted_at IS NULL
                AND (
                    (a.end_datetime IS NOT NULL AND NEW.start_datetime < a.end_datetime AND
                     (NEW.end_datetime IS NULL OR NEW.end_datetime > a.start_datetime))
                    OR
                    (a.end_datetime IS NULL AND NEW.start_datetime >= a.start_datetime)
                    OR
                    (NEW.end_datetime IS NULL AND a.start_datetime >= NEW.start_datetime)
                );

                IF conflict_count > 0 THEN
                    RAISE EXCEPTION 'Chevauchement detecte pour chauffeur ID % entre % et %',
                        NEW.driver_id, NEW.start_datetime, COALESCE(NEW.end_datetime::text, 'indetermine');
                END IF;

                RETURN NEW;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        // Trigger avant insertion/mise à jour
        DB::statement('
            CREATE TRIGGER assignments_overlap_check
            BEFORE INSERT OR UPDATE ON assignments
            FOR EACH ROW
            EXECUTE FUNCTION check_assignment_overlaps()
        ');

        echo "✅ Validation anti-chevauchement par triggers\n";
    }

    /**
     * Index optimisés pour performance
     */
    private function addPerformanceIndexes(): void
    {
        $indexes = [
            // Index composites pour requêtes fréquentes
            'CREATE INDEX IF NOT EXISTS idx_assignments_vehicle_period ON assignments (vehicle_id, start_datetime, end_datetime) WHERE deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_assignments_driver_period ON assignments (driver_id, start_datetime, end_datetime) WHERE deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_assignments_org_status ON assignments (organization_id, status) WHERE deleted_at IS NULL',

            // Index pour Gantt (fenêtres temporelles)
            'CREATE INDEX IF NOT EXISTS idx_assignments_period_gantt ON assignments (start_datetime, end_datetime, organization_id) WHERE deleted_at IS NULL',

            // Index pour affectations actives
            'CREATE INDEX IF NOT EXISTS idx_assignments_active ON assignments (organization_id, vehicle_id, driver_id) WHERE end_datetime IS NULL AND deleted_at IS NULL',

            // Index pour recherche par utilisateur
            'CREATE INDEX IF NOT EXISTS idx_assignments_created_by ON assignments (created_by_user_id, created_at)',

            // Index GIN pour recherche textuelle (si besoin)
            'CREATE INDEX IF NOT EXISTS idx_assignments_search ON assignments USING GIN (to_tsvector(\'french\', COALESCE(reason, \'\') || \' \' || COALESCE(notes, \'\')))'
        ];

        foreach ($indexes as $indexSql) {
            try {
                DB::statement($indexSql);
            } catch (\Exception $e) {
                echo "⚠️  Index existe: " . substr($indexSql, 0, 50) . "...\n";
            }
        }

        echo "✅ Index de performance créés\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip PostgreSQL-specific cleanup si pas PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Supprimer triggers et fonctions
            DB::statement('DROP TRIGGER IF EXISTS assignments_overlap_check ON assignments');
            DB::statement('DROP FUNCTION IF EXISTS check_assignment_overlaps()');

            // Supprimer contraintes GIST
            DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS assignments_vehicle_no_overlap');
            DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS assignments_driver_no_overlap');
        }

        // Si table créée par cette migration, la supprimer
        // Sinon, garder la table existante
        echo "✅ Contraintes anti-chevauchement supprimées\n";
    }
};