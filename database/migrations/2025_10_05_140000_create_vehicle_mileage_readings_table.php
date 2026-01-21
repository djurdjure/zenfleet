<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Create vehicle_mileage_readings table
 *
 * Table pour le suivi des relevés kilométriques des véhicules.
 * Supporte les relevés manuels et automatiques avec audit complet.
 *
 * Architecture Multi-Tenant:
 * - organization_id avec cascade delete pour isolation stricte
 * - Index composites pour performance optimale
 *
 * @version 1.0-Enterprise
 * @date 2025-10-05
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_mileage_readings', function (Blueprint $table) {
            // ===================================================================
            // PRIMARY KEY
            // ===================================================================
            $table->id();

            // ===================================================================
            // FOREIGN KEYS - Multi-Tenant Architecture
            // ===================================================================

            // Organization (Multi-tenant isolation)
            $table->unsignedBigInteger('organization_id');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade')
                ->name('fk_mileage_readings_organization');

            // Vehicle
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id')
                ->on('vehicles')
                ->onDelete('cascade')
                ->name('fk_mileage_readings_vehicle');

            // Recorded by (User) - Nullable pour relevés automatiques
            $table->unsignedBigInteger('recorded_by_id')->nullable();
            $table->foreign('recorded_by_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->name('fk_mileage_readings_recorded_by');

            // ===================================================================
            // DATA FIELDS
            // ===================================================================

            // Date et heure du relevé
            $table->timestamp('recorded_at')
                ->comment('Date et heure du relevé kilométrique');

            // Valeur du kilométrage
            $table->unsignedBigInteger('mileage')
                ->comment('Valeur du kilométrage en km');

            // Méthode d'enregistrement
            $table->enum('recording_method', ['manual', 'automatic'])
                ->default('manual')
                ->comment('Méthode d\'enregistrement du relevé');

            // Notes additionnelles
            $table->text('notes')->nullable()
                ->comment('Commentaires ou observations sur le relevé');

            // ===================================================================
            // TIMESTAMPS
            // ===================================================================
            $table->timestamps();

            // ===================================================================
            // INDEXES - Performance Optimization
            // ===================================================================

            // Index pour isolation multi-tenant et requêtes fréquentes
            $table->index('organization_id', 'idx_mileage_readings_organization');

            // Index pour recherche par véhicule (très fréquent)
            $table->index('vehicle_id', 'idx_mileage_readings_vehicle');

            // Index pour tri chronologique et filtres par date
            $table->index('recorded_at', 'idx_mileage_readings_recorded_at');

            // Index pour filtrer par méthode d'enregistrement
            $table->index('recording_method', 'idx_mileage_readings_method');

            // Index composite pour requêtes multi-tenant par véhicule
            // Très utilisé pour: "tous les relevés d'un véhicule dans une org"
            $table->index(
                ['organization_id', 'vehicle_id', 'recorded_at'],
                'idx_mileage_readings_org_vehicle_date'
            );

            // Index composite pour détection des anomalies kilométriques
            // Utilisé pour: vérifier cohérence des relevés par véhicule
            $table->index(
                ['vehicle_id', 'recorded_at', 'mileage'],
                'idx_mileage_readings_vehicle_chronology'
            );

            // Index pour audit et traçabilité
            $table->index('recorded_by_id', 'idx_mileage_readings_recorded_by');
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }

        // ===================================================================
        // POST-CREATION: CONSTRAINTS & COMMENTS
        // ===================================================================

        // PostgreSQL: Check constraint pour cohérence kilométrique
        // Le kilométrage doit être positif
        DB::statement('ALTER TABLE vehicle_mileage_readings ADD CONSTRAINT chk_mileage_positive CHECK (mileage >= 0)');

        // ===================================================================
        // TABLE COMMENTS (PostgreSQL)
        // ===================================================================
        DB::statement("COMMENT ON TABLE vehicle_mileage_readings IS 'Relevés kilométriques des véhicules - Supporte relevés manuels et automatiques avec audit complet'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.organization_id IS 'Organisation propriétaire (multi-tenant isolation)'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.vehicle_id IS 'Véhicule concerné par le relevé'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.recorded_at IS 'Date et heure exacte du relevé'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.mileage IS 'Valeur du kilométrage en kilomètres'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.recorded_by_id IS 'Utilisateur ayant enregistré le relevé (NULL si automatique)'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.recording_method IS 'Méthode: manual (saisie utilisateur) ou automatic (système)'");
        DB::statement("COMMENT ON COLUMN vehicle_mileage_readings.notes IS 'Observations ou commentaires sur le relevé'");

        // ===================================================================
        // POST-CREATION: Trigger pour validation (optionnel)
        // ===================================================================

        // Trigger PostgreSQL pour empêcher les relevés kilométriques incohérents
        // (kilométrage inférieur au dernier relevé pour le même véhicule)
        DB::unprepared("
            CREATE OR REPLACE FUNCTION check_mileage_consistency()
            RETURNS TRIGGER AS $$
            DECLARE
                last_mileage BIGINT;
            BEGIN
                -- Récupérer le dernier kilométrage enregistré pour ce véhicule
                SELECT mileage INTO last_mileage
                FROM vehicle_mileage_readings
                WHERE vehicle_id = NEW.vehicle_id
                  AND recorded_at < NEW.recorded_at
                ORDER BY recorded_at DESC
                LIMIT 1;

                -- Si un relevé précédent existe et que le nouveau kilométrage est inférieur
                -- (en dehors des corrections manuelles), lever une exception
                IF last_mileage IS NOT NULL AND NEW.mileage < last_mileage AND NEW.recording_method = 'automatic' THEN
                    RAISE EXCEPTION 'Mileage consistency error: New mileage (%) is less than previous mileage (%) for vehicle_id %',
                        NEW.mileage, last_mileage, NEW.vehicle_id;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trg_check_mileage_consistency
            BEFORE INSERT ON vehicle_mileage_readings
            FOR EACH ROW
            EXECUTE FUNCTION check_mileage_consistency();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            // Supprimer le trigger et la fonction
            DB::unprepared('DROP TRIGGER IF EXISTS trg_check_mileage_consistency ON vehicle_mileage_readings');
            DB::unprepared('DROP FUNCTION IF EXISTS check_mileage_consistency()');
        }

        // Supprimer la table
        Schema::dropIfExists('vehicle_mileage_readings');
    }
};
