<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🏢 MIGRATION ENTERPRISE - Contraintes d'Unicité Multi-Tenant pour Véhicules
 *
 * PROBLÈME RÉSOLU:
 * - Contrainte unique globale sur registration_plate empêche le même véhicule
 *   d'exister dans plusieurs organisations (cas d'usage: vente entre orgs)
 * - Messages d'erreur SQL bruts pour les admins sans accès au véhicule dupliqué
 *
 * SOLUTION ENTERPRISE:
 * - Contrainte unique SCOPED par organisation (registration_plate + organization_id)
 * - Même chose pour VIN (vin + organization_id)
 * - Permet qu'un véhicule existe dans Org A et Org B simultanément
 * - Empêche les doublons au sein d'une même organisation
 *
 * CAS D'USAGE:
 * ✅ Org A vend véhicule "AB-123-CD" à Org B
 * ✅ Org A garde l'historique (soft delete ou conservation)
 * ✅ Org B peut enregistrer "AB-123-CD" sans conflit
 * ❌ Org A ne peut pas avoir 2x "AB-123-CD" actifs
 *
 * @version 1.0-Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-10-03
 */
return new class extends Migration
{
    /**
     * Run the migrations - Passage en contraintes multi-tenant
     */
    public function up(): void
    {
        // ============================================================
        // ÉTAPE 1: SUPPRESSION DES CONTRAINTES UNIQUES GLOBALES
        // ============================================================

        Schema::table('vehicles', function (Blueprint $table) {
            // Supprimer les contraintes uniques globales existantes
            $table->dropUnique(['registration_plate']);
            $table->dropUnique(['vin']);
        });

        // ============================================================
        // ÉTAPE 2: CRÉATION DES CONTRAINTES UNIQUES COMPOSITES
        // ============================================================

        Schema::table('vehicles', function (Blueprint $table) {
            // Contrainte unique composite: (registration_plate + organization_id)
            // Un véhicule avec la même plaque peut exister dans différentes organisations
            $table->unique(
                ['registration_plate', 'organization_id'],
                'vehicles_registration_plate_organization_unique'
            );

            // Contrainte unique composite: (vin + organization_id)
            // Un VIN peut exister dans différentes organisations (historique de vente)
            $table->unique(
                ['vin', 'organization_id'],
                'vehicles_vin_organization_unique'
            );
        });

        // ============================================================
        // ÉTAPE 3: INDEX DE PERFORMANCE POUR RECHERCHES
        // ============================================================

        // Index pour recherche rapide par plaque (toutes organisations)
        DB::statement('CREATE INDEX IF NOT EXISTS idx_vehicles_registration_plate ON vehicles (registration_plate)');

        // Index pour recherche rapide par VIN (toutes organisations)
        DB::statement('CREATE INDEX IF NOT EXISTS idx_vehicles_vin ON vehicles (vin) WHERE vin IS NOT NULL');

        // Index composite pour recherches fréquentes organisation + plaque
        DB::statement('CREATE INDEX IF NOT EXISTS idx_vehicles_org_plate ON vehicles (organization_id, registration_plate) WHERE deleted_at IS NULL');

        // ============================================================
        // LOGS DE MIGRATION
        // ============================================================

        \Log::info('Migration enterprise: Contraintes d\'unicité multi-tenant appliquées sur vehicles', [
            'migration' => '2025_10_03_140000_fix_vehicles_unique_constraints_multitenant',
            'changes' => [
                'removed_constraints' => [
                    'vehicles_registration_plate_unique (global)',
                    'vehicles_vin_unique (global)'
                ],
                'added_constraints' => [
                    'vehicles_registration_plate_organization_unique (composite)',
                    'vehicles_vin_organization_unique (composite)'
                ],
                'added_indexes' => [
                    'idx_vehicles_registration_plate',
                    'idx_vehicles_vin',
                    'idx_vehicles_org_plate'
                ],
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Reverse the migrations - Retour aux contraintes globales
     *
     * ⚠️  ATTENTION: Le rollback peut échouer si des doublons existent
     *     entre organisations après migration
     */
    public function down(): void
    {
        // ============================================================
        // ÉTAPE 1: VÉRIFICATION DES DOUBLONS INTER-ORGANISATIONS
        // ============================================================

        // Vérifier s'il y a des doublons de plaques entre organisations
        $duplicatePlates = DB::select("
            SELECT registration_plate, COUNT(DISTINCT organization_id) as org_count
            FROM vehicles
            WHERE deleted_at IS NULL
            GROUP BY registration_plate
            HAVING COUNT(DISTINCT organization_id) > 1
        ");

        $duplicateVins = DB::select("
            SELECT vin, COUNT(DISTINCT organization_id) as org_count
            FROM vehicles
            WHERE vin IS NOT NULL AND deleted_at IS NULL
            GROUP BY vin
            HAVING COUNT(DISTINCT organization_id) > 1
        ");

        if (!empty($duplicatePlates) || !empty($duplicateVins)) {
            \Log::warning('Rollback migration: Doublons inter-organisations détectés', [
                'duplicate_plates_count' => count($duplicatePlates),
                'duplicate_vins_count' => count($duplicateVins),
                'action' => 'Rollback bloqué pour préserver l\'intégrité des données'
            ]);

            throw new \Exception(
                "Impossible de revenir aux contraintes globales: " .
                count($duplicatePlates) . " plaque(s) et " .
                count($duplicateVins) . " VIN(s) existent dans plusieurs organisations. " .
                "Résolvez ces doublons avant le rollback."
            );
        }

        // ============================================================
        // ÉTAPE 2: SUPPRESSION DES INDEX DE PERFORMANCE
        // ============================================================

        DB::statement('DROP INDEX IF EXISTS idx_vehicles_registration_plate');
        DB::statement('DROP INDEX IF EXISTS idx_vehicles_vin');
        DB::statement('DROP INDEX IF EXISTS idx_vehicles_org_plate');

        // ============================================================
        // ÉTAPE 3: SUPPRESSION DES CONTRAINTES COMPOSITES
        // ============================================================

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropUnique('vehicles_registration_plate_organization_unique');
            $table->dropUnique('vehicles_vin_organization_unique');
        });

        // ============================================================
        // ÉTAPE 4: RECRÉATION DES CONTRAINTES GLOBALES
        // ============================================================

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unique('registration_plate');
            $table->unique('vin');
        });

        \Log::info('Migration enterprise: Rollback vers contraintes globales effectué', [
            'migration' => '2025_10_03_140000_fix_vehicles_unique_constraints_multitenant',
            'timestamp' => now()->toISOString()
        ]);
    }
};
