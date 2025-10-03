<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚀 ZENFLEET TEMPORAL CONSTRAINTS ENTERPRISE
 *
 * Anti-chevauchement temporal ultra-professionnel pour assignments:
 * - Prévention conflits véhicule/chauffeur
 * - Contraintes d'exclusion PostgreSQL GIST
 * - Validation métier automatique
 * - Performance optimisée
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
        // ===== EXTENSION POSTGRESQL REQUISE =====
        DB::statement('CREATE EXTENSION IF NOT EXISTS "btree_gist"');

        // ===== AJOUT organization_id MANQUANT =====
        if (!Schema::hasColumn('assignments', 'organization_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('organization_id')
                    ->after('id')
                    ->constrained('organizations')
                    ->onDelete('cascade');
            });
        }

        // ===== CONTRAINTES D'EXCLUSION TEMPORAL =====
        $this->addTemporalExclusionConstraints();

        // ===== INDEX OPTIMISÉS POUR PERFORMANCE =====
        $this->addOptimizedIndexes();

        // ===== FONCTIONS DE VALIDATION BUSINESS =====
        $this->createBusinessValidationFunctions();

        // ===== TRIGGERS DE VALIDATION =====
        $this->createValidationTriggers();

        // ===== CONTRAINTES DE COHÉRENCE MÉTIER =====
        $this->addBusinessConstraints();

        echo "✅ Contraintes temporales enterprise ajoutées aux assignments\n";
    }

    /**
     * Contraintes d'exclusion temporales ultra-robustes
     */
    private function addTemporalExclusionConstraints(): void
    {
        // ===== ANTI-CHEVAUCHEMENT PAR VÉHICULE =====
        DB::statement("
            DO \$\$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'ex_vehicle_period'
                ) THEN
                    ALTER TABLE assignments
                    ADD CONSTRAINT ex_vehicle_period
                    EXCLUDE USING GIST (
                        vehicle_id WITH =,
                        organization_id WITH =,
                        tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp), '[)') WITH &&
                    )
                    WHERE (deleted_at IS NULL);
                END IF;
            END \$\$;
        ");

        // ===== ANTI-CHEVAUCHEMENT PAR CHAUFFEUR =====
        DB::statement("
            DO \$\$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_constraint WHERE conname = 'ex_driver_period'
                ) THEN
                    ALTER TABLE assignments
                    ADD CONSTRAINT ex_driver_period
                    EXCLUDE USING GIST (
                        driver_id WITH =,
                        organization_id WITH =,
                        tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp), '[)') WITH &&
                    )
                    WHERE (deleted_at IS NULL AND driver_id IS NOT NULL);
                END IF;
            END \$\$;
        ");

        echo "✅ Contraintes d\'exclusion temporales créées\n";
    }

    /**
     * Index optimisés pour performance enterprise
     */
    private function addOptimizedIndexes(): void
    {
        $indexes = [
            // Index de recherche temporelle optimisé
            'CREATE INDEX IF NOT EXISTS idx_assignments_vehicle_temporal ON assignments (vehicle_id, start_datetime, end_datetime) WHERE deleted_at IS NULL',
            'CREATE INDEX IF NOT EXISTS idx_assignments_driver_temporal ON assignments (driver_id, start_datetime, end_datetime) WHERE deleted_at IS NULL AND driver_id IS NOT NULL',

            // Index pour organisation
            'CREATE INDEX IF NOT EXISTS idx_assignments_org_temporal ON assignments (organization_id, start_datetime DESC)',

            // Index pour recherches actives
            'CREATE INDEX IF NOT EXISTS idx_assignments_active ON assignments (vehicle_id, driver_id) WHERE end_datetime IS NULL AND deleted_at IS NULL',

            // Index pour reporting
            'CREATE INDEX IF NOT EXISTS idx_assignments_period_reporting ON assignments (start_datetime, end_datetime) WHERE deleted_at IS NULL',

            // Index composite optimisé
            'CREATE INDEX IF NOT EXISTS idx_assignments_composite ON assignments (organization_id, vehicle_id, start_datetime, end_datetime) WHERE deleted_at IS NULL'
        ];

        foreach ($indexes as $indexSql) {
            DB::statement($indexSql);
        }

        echo "✅ Index optimisés créés\n";
    }

    /**
     * Fonctions de validation business ultra-robustes
     */
    private function createBusinessValidationFunctions(): void
    {
        // ===== FONCTION DE VALIDATION KILOMÉTRAGE =====
        DB::statement("
            CREATE OR REPLACE FUNCTION validate_assignment_mileage()
            RETURNS TRIGGER AS \$func\$
            BEGIN
                -- Validation kilometrage croissant
                IF NEW.start_mileage IS NOT NULL AND NEW.end_mileage IS NOT NULL THEN
                    IF NEW.end_mileage < NEW.start_mileage THEN
                        RAISE EXCEPTION 'Kilometrage de fin ne peut pas etre inferieur au kilometrage de debut';
                    END IF;
                END IF;

                -- Validation coherence avec vehicule
                IF NEW.start_mileage IS NOT NULL THEN
                    IF EXISTS (
                        SELECT 1 FROM vehicles v
                        WHERE v.id = NEW.vehicle_id
                        AND v.current_mileage > NEW.start_mileage + 10000 -- Tolerance 10k km
                    ) THEN
                        RAISE EXCEPTION 'Kilometrage de debut incoherent avec kilometrage actuel du vehicule';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        // ===== FONCTION DE VALIDATION TEMPORELLE BUSINESS =====
        DB::statement("
            CREATE OR REPLACE FUNCTION validate_assignment_business_rules()
            RETURNS TRIGGER AS \$func\$
            BEGIN
                -- Validation: end_datetime > start_datetime
                IF NEW.end_datetime IS NOT NULL AND NEW.end_datetime <= NEW.start_datetime THEN
                    RAISE EXCEPTION 'Date de fin doit etre posterieure a date de debut';
                END IF;

                -- Validation: pas affectation dans le futur lointain
                IF NEW.start_datetime > NOW() + INTERVAL '1 year' THEN
                    RAISE EXCEPTION 'Impossible de creer affectation plus un an dans le futur';
                END IF;

                -- Validation: vehicule et chauffeur dans la meme organisation
                IF NEW.driver_id IS NOT NULL THEN
                    IF NOT EXISTS (
                        SELECT 1 FROM drivers d
                        JOIN vehicles v ON v.organization_id = d.organization_id
                        WHERE d.id = NEW.driver_id
                        AND v.id = NEW.vehicle_id
                        AND d.organization_id = NEW.organization_id
                    ) THEN
                        RAISE EXCEPTION 'Vehicule et chauffeur doivent appartenir a la meme organisation';
                    END IF;
                END IF;

                RETURN NEW;
            END;
            \$func\$ LANGUAGE plpgsql;
        ");

        echo "✅ Fonctions de validation business créées\n";
    }

    /**
     * Triggers de validation enterprise
     */
    private function createValidationTriggers(): void
    {
        // Trigger validation kilométrage
        DB::statement('
            CREATE TRIGGER trg_validate_assignment_mileage
            BEFORE INSERT OR UPDATE ON assignments
            FOR EACH ROW
            EXECUTE FUNCTION validate_assignment_mileage()
        ');

        // Trigger validation business rules
        DB::statement('
            CREATE TRIGGER trg_validate_assignment_business
            BEFORE INSERT OR UPDATE ON assignments
            FOR EACH ROW
            EXECUTE FUNCTION validate_assignment_business_rules()
        ');

        echo "✅ Triggers de validation créés\n";
    }

    /**
     * Contraintes de cohérence métier
     */
    private function addBusinessConstraints(): void
    {
        // Contrainte: end_mileage >= start_mileage
        DB::statement('
            ALTER TABLE assignments
            ADD CONSTRAINT chk_mileage_progression
            CHECK (
                (start_mileage IS NULL AND end_mileage IS NULL) OR
                (start_mileage IS NOT NULL AND end_mileage IS NULL) OR
                (start_mileage IS NOT NULL AND end_mileage IS NOT NULL AND end_mileage >= start_mileage)
            )
        ');

        // Contrainte: dates cohérentes
        DB::statement('
            ALTER TABLE assignments
            ADD CONSTRAINT chk_datetime_coherence
            CHECK (
                (end_datetime IS NULL) OR
                (end_datetime > start_datetime)
            )
        ');

        // Contrainte: kilométrage réaliste
        DB::statement('
            ALTER TABLE assignments
            ADD CONSTRAINT chk_realistic_mileage
            CHECK (
                (start_mileage IS NULL OR start_mileage >= 0) AND
                (end_mileage IS NULL OR end_mileage >= 0) AND
                (start_mileage IS NULL OR start_mileage <= 9999999) AND
                (end_mileage IS NULL OR end_mileage <= 9999999)
            )
        ');

        echo "✅ Contraintes de cohérence métier ajoutées\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprime les triggers
        DB::statement('DROP TRIGGER IF EXISTS trg_validate_assignment_mileage ON assignments');
        DB::statement('DROP TRIGGER IF EXISTS trg_validate_assignment_business ON assignments');

        // Supprime les fonctions
        DB::statement('DROP FUNCTION IF EXISTS validate_assignment_mileage()');
        DB::statement('DROP FUNCTION IF EXISTS validate_assignment_business_rules()');

        // Supprime les contraintes
        DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS ex_vehicle_period');
        DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS ex_driver_period');
        DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS chk_mileage_progression');
        DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS chk_datetime_coherence');
        DB::statement('ALTER TABLE assignments DROP CONSTRAINT IF EXISTS chk_realistic_mileage');

        // Supprime les index
        $indexes = [
            'idx_assignments_vehicle_temporal',
            'idx_assignments_driver_temporal',
            'idx_assignments_org_temporal',
            'idx_assignments_active',
            'idx_assignments_period_reporting',
            'idx_assignments_composite'
        ];

        foreach ($indexes as $index) {
            DB::statement("DROP INDEX IF EXISTS {$index}");
        }

        // Supprime organization_id si ajouté
        if (Schema::hasColumn('assignments', 'organization_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            });
        }

        echo "✅ Contraintes temporales supprimées\n";
    }
};