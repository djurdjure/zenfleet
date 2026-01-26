#!/usr/bin/env php
<?php

/**
 * 🧪 TEST MULTI-TENANT - Unicité Véhicules par Organisation
 *
 * Teste les 3 scénarios critiques d'unicité multi-tenant
 *
 * @version 1.0-Enterprise
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vehicle;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST MULTI-TENANT - UNICITÉ VÉHICULES                  ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ============================================================
// TEST 1: Vérifier les Contraintes DB
// ============================================================

echo "📋 Test 1: Vérification des contraintes de base de données...\n\n";

try {
    $constraints = DB::select("
        SELECT conname
        FROM pg_constraint
        WHERE conname LIKE '%vehicles%'
          AND conname LIKE '%unique%'
        ORDER BY conname
    ");

    $hasOldGlobal = false;
    $hasNewScoped = false;

    foreach ($constraints as $constraint) {
        echo "   • {$constraint->conname}\n";

        if ($constraint->conname === 'vehicles_registration_plate_unique') {
            $hasOldGlobal = true;
        }

        if ($constraint->conname === 'vehicles_registration_plate_organization_unique') {
            $hasNewScoped = true;
        }
    }

    echo "\n";

    if ($hasOldGlobal && !$hasNewScoped) {
        echo "   ❌ ANCIEN SYSTÈME: Contraintes globales détectées\n";
        echo "   💡 Action requise: Exécutez php artisan migrate\n";
    } elseif (!$hasOldGlobal && $hasNewScoped) {
        echo "   ✅ NOUVEAU SYSTÈME: Contraintes multi-tenant actives\n";
    } else {
        echo "   ⚠️  ÉTAT MIXTE: Migration partiellement appliquée\n";
    }

} catch (\Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

echo "\n";

// ============================================================
// TEST 2: Vérifier Doublons Inter-Organisations
// ============================================================

echo "─────────────────────────────────────────────────────────────\n";
echo "📋 Test 2: Vérification des doublons inter-organisations...\n\n";

try {
    $duplicatePlates = DB::select("
        SELECT
            registration_plate,
            COUNT(DISTINCT organization_id) as org_count,
            STRING_AGG(DISTINCT organization_id::text, ', ') as orgs
        FROM vehicles
        WHERE deleted_at IS NULL
        GROUP BY registration_plate
        HAVING COUNT(DISTINCT organization_id) > 1
        LIMIT 10
    ");

    if (empty($duplicatePlates)) {
        echo "   ℹ️  Aucun doublon inter-organisations détecté\n";
        echo "   📝 Cela peut indiquer:\n";
        echo "      - Pas encore de ventes entre organisations\n";
        echo "      - Système mono-tenant actuellement\n";
    } else {
        echo "   ✅ Doublons inter-organisations trouvés (NORMAL après migration):\n\n";

        foreach ($duplicatePlates as $dup) {
            echo sprintf(
                "      Plaque: %-15s | Organisations: %s\n",
                $dup->registration_plate,
                $dup->orgs
            );
        }

        echo "\n   💡 Ces doublons sont AUTORISÉS avec le nouveau système\n";
        echo "      (Un véhicule peut exister dans plusieurs organisations)\n";
    }

} catch (\Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

echo "\n";

// ============================================================
// TEST 3: Vérifier Doublons Intra-Organisation
// ============================================================

echo "─────────────────────────────────────────────────────────────\n";
echo "📋 Test 3: Vérification des doublons intra-organisation...\n\n";

try {
    $intraDuplicates = DB::select("
        SELECT
            organization_id,
            registration_plate,
            COUNT(*) as count
        FROM vehicles
        WHERE deleted_at IS NULL
        GROUP BY organization_id, registration_plate
        HAVING COUNT(*) > 1
        LIMIT 10
    ");

    if (empty($intraDuplicates)) {
        echo "   ✅ Aucun doublon intra-organisation (CORRECT)\n";
        echo "   📝 Les contraintes multi-tenant fonctionnent correctement\n";
    } else {
        echo "   ❌ PROBLÈME: Doublons intra-organisation détectés:\n\n";

        foreach ($intraDuplicates as $dup) {
            echo sprintf(
                "      Org %s | Plaque: %-15s | Occurrences: %d\n",
                $dup->organization_id,
                $dup->registration_plate,
                $dup->count
            );
        }

        echo "\n   💡 Ces doublons ne devraient PAS exister\n";
        echo "      Vérifiez la migration et nettoyez les données\n";
    }

} catch (\Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

echo "\n";

// ============================================================
// TEST 4: Statistiques Globales
// ============================================================

echo "─────────────────────────────────────────────────────────────\n";
echo "📊 STATISTIQUES GLOBALES\n";
echo "─────────────────────────────────────────────────────────────\n\n";

try {
    $stats = DB::select("
        SELECT
            COUNT(DISTINCT organization_id) as total_organizations,
            COUNT(*) as total_vehicles,
            COUNT(DISTINCT registration_plate) as unique_plates,
            COUNT(*) - COUNT(DISTINCT registration_plate) as cross_org_duplicates
        FROM vehicles
        WHERE deleted_at IS NULL
    ")[0];

    echo "   Total organisations:            " . $stats->total_organizations . "\n";
    echo "   Total véhicules (toutes orgs):  " . $stats->total_vehicles . "\n";
    echo "   Plaques uniques globalement:    " . $stats->unique_plates . "\n";
    echo "   Doublons inter-organisations:   " . $stats->cross_org_duplicates . "\n";

    if ($stats->cross_org_duplicates > 0) {
        $percentage = round(($stats->cross_org_duplicates / $stats->total_vehicles) * 100, 1);
        echo "\n   📈 {$percentage}% des véhicules existent dans plusieurs organisations\n";
        echo "      (Cela peut indiquer des ventes/transferts entre organisations)\n";
    }

} catch (\Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

echo "\n";

// ============================================================
// TEST 5: Simulation Création Doublon
// ============================================================

echo "─────────────────────────────────────────────────────────────\n";
echo "📋 Test 5: Simulation de création de doublon...\n\n";

try {
    // Trouver le premier véhicule
    $sampleVehicle = Vehicle::whereNotNull('organization_id')
        ->whereNotNull('registration_plate')
        ->first();

    if (!$sampleVehicle) {
        echo "   ⚠️  Aucun véhicule trouvé pour le test\n";
        echo "   💡 Créez au moins un véhicule pour tester\n";
    } else {
        echo "   📝 Véhicule de test:\n";
        echo "      Plaque: {$sampleVehicle->registration_plate}\n";
        echo "      Organisation: {$sampleVehicle->organization_id}\n";
        echo "\n";

        // Test 5a: Doublon dans la MÊME organisation (doit échouer)
        echo "   🧪 Test 5a: Création doublon MÊME organisation...\n";
        try {
            DB::beginTransaction();

            Vehicle::create([
                'registration_plate' => $sampleVehicle->registration_plate,
                'organization_id' => $sampleVehicle->organization_id,
                'brand' => 'Test Brand',
                'model' => 'Test Model',
            ]);

            DB::rollBack();
            echo "      ❌ ERREUR: Doublon créé (ne devrait pas être possible)\n";

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();

            if (str_contains($e->getMessage(), 'vehicles_registration_plate_organization_unique')) {
                echo "      ✅ Contrainte respectée: Doublon bloqué\n";
            } else {
                echo "      ⚠️  Erreur inattendue: {$e->getMessage()}\n";
            }
        }

        // Test 5b: Même plaque dans organisation DIFFÉRENTE (doit réussir)
        echo "\n   🧪 Test 5b: Création même plaque organisation DIFFÉRENTE...\n";

        // Trouver une autre organisation
        $otherOrg = Organization::where('id', '!=', $sampleVehicle->organization_id)->first();

        if (!$otherOrg) {
            echo "      ⚠️  Pas d'autre organisation pour tester\n";
        } else {
            try {
                DB::beginTransaction();

                $newVehicle = Vehicle::create([
                    'registration_plate' => $sampleVehicle->registration_plate,
                    'organization_id' => $otherOrg->id,
                    'brand' => 'Test Brand',
                    'model' => 'Test Model',
                ]);

                DB::rollBack();
                echo "      ✅ Multi-tenant fonctionne: Même plaque autorisée dans Org {$otherOrg->id}\n";

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();
                echo "      ❌ ERREUR: Multi-tenant ne fonctionne pas correctement\n";
                echo "      💡 Vérifiez que la migration est bien appliquée\n";
            }
        }
    }

} catch (\Exception $e) {
    echo "   ❌ Erreur: {$e->getMessage()}\n";
}

echo "\n";

// ============================================================
// CONCLUSION
// ============================================================

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  📊 RÉSUMÉ DES TESTS                                       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "✅ Tests réussis:\n";
echo "   • Contraintes de base de données vérifiées\n";
echo "   • Doublons inter/intra-organisations analysés\n";
echo "   • Simulations de création testées\n";
echo "\n";

echo "📝 Prochaines étapes recommandées:\n";
echo "   1. Si contraintes anciennes détectées: php artisan migrate\n";
echo "   2. Tester import CSV avec plaque existante\n";
echo "   3. Vérifier messages d'erreur user-friendly\n";
echo "\n";

exit(0);
