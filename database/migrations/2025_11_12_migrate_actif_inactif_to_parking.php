<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 🔄 MIGRATION ENTERPRISE: Suppression des statuts ACTIF et INACTIF
     *
     * Migre tous les véhicules avec statut "Actif" vers "Parking"
     * Migre tous les véhicules avec statut "Inactif" vers "Réformé"
     * Supprime les statuts ACTIF et INACTIF de la table vehicle_statuses
     *
     * Justification métier:
     * - "Actif" est trop générique et couvert par "Parking" (disponible)
     * - "Inactif" est couvert par "Réformé" (hors service)
     * - Les 5 statuts restants couvrent tous les cas d'usage:
     *   1. PARKING: Disponible au parking
     *   2. AFFECTE: Assigné à un chauffeur
     *   3. EN_PANNE: Nécessite réparation
     *   4. EN_MAINTENANCE: En cours de réparation
     *   5. REFORME: Hors service définitif
     */
    public function up(): void
    {
        echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
        echo "║  🔄 MIGRATION: Suppression des statuts ACTIF et INACTIF                    ║\n";
        echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

        // ========================================================================
        // ÉTAPE 1: Identifier les statuts à migrer
        // ========================================================================
        echo "📋 ÉTAPE 1: Identification des statuts à migrer\n";
        echo str_repeat("─", 80) . "\n";

        $statutActif = DB::table('vehicle_statuses')->where('slug', 'actif')->first();
        $statutInactif = DB::table('vehicle_statuses')->where('slug', 'inactif')->first();
        $statutParking = DB::table('vehicle_statuses')->where('slug', 'parking')->first();
        $statutReforme = DB::table('vehicle_statuses')->where('slug', 'reforme')->first();

        if (!$statutActif) {
            echo "⚠️  Statut 'actif' non trouvé - ignoré\n";
        } else {
            echo "✅ Statut 'actif' trouvé (ID: {$statutActif->id})\n";
        }

        if (!$statutInactif) {
            echo "⚠️  Statut 'inactif' non trouvé - ignoré\n";
        } else {
            echo "✅ Statut 'inactif' trouvé (ID: {$statutInactif->id})\n";
        }

        if (!$statutParking) {
            throw new \Exception("❌ ERREUR CRITIQUE: Statut 'parking' non trouvé!");
        }
        echo "✅ Statut cible 'parking' trouvé (ID: {$statutParking->id})\n";

        if (!$statutReforme) {
            throw new \Exception("❌ ERREUR CRITIQUE: Statut 'reforme' non trouvé!");
        }
        echo "✅ Statut cible 'reforme' trouvé (ID: {$statutReforme->id})\n\n";

        // ========================================================================
        // ÉTAPE 2: Compter les véhicules à migrer
        // ========================================================================
        echo "📋 ÉTAPE 2: Comptage des véhicules à migrer\n";
        echo str_repeat("─", 80) . "\n";

        $countActif = 0;
        $countInactif = 0;

        if ($statutActif) {
            $countActif = DB::table('vehicles')->where('status_id', $statutActif->id)->count();
            echo "📊 Véhicules avec statut 'actif': {$countActif}\n";
        }

        if ($statutInactif) {
            $countInactif = DB::table('vehicles')->where('status_id', $statutInactif->id)->count();
            echo "📊 Véhicules avec statut 'inactif': {$countInactif}\n";
        }

        $totalToMigrate = $countActif + $countInactif;
        echo "📊 Total à migrer: {$totalToMigrate}\n\n";

        if ($totalToMigrate === 0) {
            echo "✅ Aucun véhicule à migrer - la base est déjà propre\n\n";
        } else {
            // ====================================================================
            // ÉTAPE 3: Migration des véhicules ACTIF → PARKING
            // ====================================================================
            if ($countActif > 0) {
                echo "📋 ÉTAPE 3: Migration ACTIF → PARKING\n";
                echo str_repeat("─", 80) . "\n";

                $updated = DB::table('vehicles')
                    ->where('status_id', $statutActif->id)
                    ->update([
                        'status_id' => $statutParking->id,
                        'updated_at' => now(),
                    ]);

                echo "✅ {$updated} véhicules migrés de 'actif' vers 'parking'\n\n";
            }

            // ====================================================================
            // ÉTAPE 4: Migration des véhicules INACTIF → REFORME
            // ====================================================================
            if ($countInactif > 0) {
                echo "📋 ÉTAPE 4: Migration INACTIF → REFORME\n";
                echo str_repeat("─", 80) . "\n";

                $updated = DB::table('vehicles')
                    ->where('status_id', $statutInactif->id)
                    ->update([
                        'status_id' => $statutReforme->id,
                        'updated_at' => now(),
                    ]);

                echo "✅ {$updated} véhicules migrés de 'inactif' vers 'reforme'\n\n";
            }
        }

        // ========================================================================
        // ÉTAPE 5: Supprimer les statuts obsolètes
        // ========================================================================
        echo "📋 ÉTAPE 5: Suppression des statuts obsolètes\n";
        echo str_repeat("─", 80) . "\n";

        if ($statutActif) {
            DB::table('vehicle_statuses')->where('id', $statutActif->id)->delete();
            echo "✅ Statut 'actif' (ID: {$statutActif->id}) supprimé\n";
        }

        if ($statutInactif) {
            DB::table('vehicle_statuses')->where('id', $statutInactif->id)->delete();
            echo "✅ Statut 'inactif' (ID: {$statutInactif->id}) supprimé\n";
        }

        echo "\n";

        // ========================================================================
        // RÉSUMÉ FINAL
        // ========================================================================
        echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✅ MIGRATION TERMINÉE AVEC SUCCÈS                       ║\n";
        echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

        echo "📊 RÉSUMÉ:\n";
        echo "   - Véhicules migrés 'actif' → 'parking': {$countActif}\n";
        echo "   - Véhicules migrés 'inactif' → 'reforme': {$countInactif}\n";
        echo "   - Statuts supprimés: " . (($statutActif ? 1 : 0) + ($statutInactif ? 1 : 0)) . "\n";
        echo "   - Statuts restants: 5 (parking, affecte, en_panne, en_maintenance, reforme)\n\n";
    }

    /**
     * Rollback de la migration
     */
    public function down(): void
    {
        echo "\n⚠️  ROLLBACK: Cette migration ne peut pas être annulée automatiquement\n";
        echo "   car les données ont été transformées de manière irréversible.\n";
        echo "   Si vous devez restaurer les anciens statuts, faites-le manuellement.\n\n";
    }
};
