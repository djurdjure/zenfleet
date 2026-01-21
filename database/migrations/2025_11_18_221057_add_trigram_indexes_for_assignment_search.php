<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚀 MIGRATION ENTERPRISE-GRADE: Indexes Trigram pour Recherche Ultra-Rapide
 *
 * Cette migration active l'extension PostgreSQL pg_trgm et crée des indexes GIN
 * pour optimiser les recherches insensibles à la casse et les recherches LIKE/ILIKE.
 *
 * PERFORMANCE ATTENDUE:
 * - Recherche BEFORE: 500-2000ms sur 100K+ enregistrements (full table scan)
 * - Recherche AFTER:  5-50ms sur 100K+ enregistrements (index scan)
 * - Amélioration: 10-400x plus rapide
 *
 * COMPATIBILITÉ:
 * - PostgreSQL 9.1+ (pg_trgm est standard depuis PG 9.1)
 * - Fonctionne avec ILIKE, LIKE, ~, et opérateurs de similarité
 *
 * UTILISATION DISQUE:
 * - Chaque index GIN: ~15-25% de la taille de la colonne
 * - Pour 100K véhicules: ~10-20MB par index
 * - Bénéfice: Recherche 100-400x plus rapide
 *
 * @author ZenFleet Architecture Team
 * @version 1.0 Enterprise-Grade
 * @since 2025-11-18
 */
return new class extends Migration
{
    /**
     * Run the migrations - Activation pg_trgm + Création indexes optimisés
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'pgsql') {
            return;
        }

        // ================================================================
        // ÉTAPE 1: Activer l'extension pg_trgm (trigram)
        // ================================================================
        // L'extension pg_trgm permet des recherches de similarité et optimise
        // les requêtes LIKE/ILIKE avec des indexes GIN

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        // ================================================================
        // ÉTAPE 2: Indexes GIN pour table VEHICLES
        // ================================================================
        // Ces indexes accélèrent les recherches sur registration_plate, brand, model

        // Index GIN trigram sur registration_plate (ex: "ABC-123", "xyz789")
        // Permet recherche insensible à la casse ultra-rapide
        if (Schema::hasTable('vehicles')) {
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_vehicles_registration_plate_trgm
                ON vehicles USING gin (registration_plate gin_trgm_ops)
            ');

        // Index GIN trigram sur brand (ex: "Toyota", "Mercedes", "RENAULT")
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_vehicles_brand_trgm
                ON vehicles USING gin (brand gin_trgm_ops)
            ');

        // Index GIN trigram sur model (ex: "Corolla", "Sprinter", "CLIO")
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_vehicles_model_trgm
                ON vehicles USING gin (model gin_trgm_ops)
            ');
        }

        // ================================================================
        // ÉTAPE 3: Indexes GIN pour table DRIVERS
        // ================================================================
        // Ces indexes accélèrent les recherches sur nom, prénom, permis

        // Index GIN trigram sur first_name
        if (Schema::hasTable('drivers')) {
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_drivers_first_name_trgm
                ON drivers USING gin (first_name gin_trgm_ops)
            ');

        // Index GIN trigram sur last_name
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_drivers_last_name_trgm
                ON drivers USING gin (last_name gin_trgm_ops)
            ');

        // Index GIN trigram sur license_number
            DB::statement('
                CREATE INDEX IF NOT EXISTS idx_drivers_license_number_trgm
                ON drivers USING gin (license_number gin_trgm_ops)
            ');

        // ================================================================
        // ÉTAPE 4: Index GIN composite pour recherche nom complet
        // ================================================================
        // Index sur l'expression CONCAT(first_name, ' ', last_name)
        // Permet recherche "Jean Dupont" ultra-rapide

            DB::statement("
                CREATE INDEX IF NOT EXISTS idx_drivers_full_name_trgm
                ON drivers USING gin ((first_name || ' ' || last_name) gin_trgm_ops)
            ");
        }

        // ================================================================
        // ÉTAPE 5: ANALYZE pour mettre à jour les statistiques
        // ================================================================
        // PostgreSQL utilise ces statistiques pour choisir le meilleur plan de requête

        if (Schema::hasTable('vehicles')) {
            DB::statement('ANALYZE vehicles');
        }
        if (Schema::hasTable('drivers')) {
            DB::statement('ANALYZE drivers');
        }

        // ================================================================
        // LOGS & VALIDATION
        // ================================================================

        // Vérifier que l'extension est bien activée
        $pgTrgmEnabled = DB::selectOne("
            SELECT EXISTS (
                SELECT 1 FROM pg_extension WHERE extname = 'pg_trgm'
            ) as enabled
        ");

        if ($pgTrgmEnabled->enabled) {
            \Log::info('✅ Extension pg_trgm activée avec succès');
        } else {
            \Log::warning('⚠️ Impossible d\'activer l\'extension pg_trgm (vérifier permissions superuser)');
        }

        // Compter les indexes créés
        $indexCount = DB::selectOne("
            SELECT COUNT(*) as count
            FROM pg_indexes
            WHERE schemaname = 'public'
            AND indexname LIKE '%_trgm'
        ");

        \Log::info("✅ {$indexCount->count} indexes trigram créés pour recherche ultra-rapide");
    }

    /**
     * Reverse the migrations - Suppression indexes et extension
     */
    public function down(): void
    {
        // ================================================================
        // SUPPRESSION INDEXES GIN TRIGRAM
        // ================================================================

        // Vehicles
        DB::statement('DROP INDEX IF EXISTS idx_vehicles_registration_plate_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_vehicles_brand_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_vehicles_model_trgm');

        // Drivers
        DB::statement('DROP INDEX IF EXISTS idx_drivers_first_name_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_drivers_last_name_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_drivers_license_number_trgm');
        DB::statement('DROP INDEX IF EXISTS idx_drivers_full_name_trgm');

        // ================================================================
        // NOTE: On ne supprime PAS l'extension pg_trgm
        // ================================================================
        // L'extension peut être utilisée par d'autres tables/fonctionnalités
        // Pour la supprimer manuellement: DROP EXTENSION IF EXISTS pg_trgm;

        \Log::info('✅ Indexes trigram supprimés (extension pg_trgm conservée)');
    }
};
