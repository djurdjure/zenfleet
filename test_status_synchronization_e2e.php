<?php

/**
 * 🧪 TESTS END-TO-END : SYNCHRONISATION DES STATUTS
 *
 * Ce script teste l'ensemble du système de synchronisation des statuts
 * dans différents scénarios réels d'utilisation.
 *
 * UTILISATION :
 * php test_status_synchronization_e2e.php
 *
 * @version 1.0.0
 * @date 2025-11-14
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🧪 TESTS END-TO-END : SYNCHRONISATION DES STATUTS\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

$testsPassed = 0;
$testsFailed = 0;

/**
 * Helper pour vérifier un test
 */
function assertTest(string $testName, bool $condition, string $expected, string $actual): void
{
    global $testsPassed, $testsFailed;

    if ($condition) {
        echo "✅ {$testName}\n";
        $testsPassed++;
    } else {
        echo "❌ {$testName}\n";
        echo "   Attendu : {$expected}\n";
        echo "   Obtenu  : {$actual}\n";
        $testsFailed++;
    }
}

try {
    // Récupérer une ressource disponible pour les tests
    $vehicle = Vehicle::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', 8)
        ->first();

    $driver = Driver::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', 7)
        ->first();

    if (!$vehicle || !$driver) {
        echo "⚠️ Aucune ressource disponible pour les tests. Veuillez exécuter le script de correction d'abord.\n";
        exit(1);
    }

    echo "📦 Ressources de test:\n";
    echo "   Véhicule: {$vehicle->registration_plate} (ID {$vehicle->id})\n";
    echo "   Chauffeur: {$driver->first_name} {$driver->last_name} (ID {$driver->id})\n";
    echo "\n";

    // ═══════════════════════════════════════════════════════════════
    // TEST 1 : Création d'affectation future (SCHEDULED)
    // ═══════════════════════════════════════════════════════════════
    echo "TEST 1 : Création d'affectation future (SCHEDULED)\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $futureStart = Carbon::now()->addDays(2);
    $futureEnd = Carbon::now()->addDays(5);

    $assignment1 = Assignment::create([
        'organization_id' => $vehicle->organization_id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => $futureStart,
        'end_datetime' => $futureEnd,
        'reason' => 'Test affectation future',
    ]);

    // Rafraîchir les ressources
    $vehicle->refresh();
    $driver->refresh();

    // Vérifications
    assertTest(
        'Affectation créée avec statut SCHEDULED',
        $assignment1->status === Assignment::STATUS_SCHEDULED,
        Assignment::STATUS_SCHEDULED,
        $assignment1->status
    );

    assertTest(
        'Véhicule verrouillé (is_available = false)',
        $vehicle->is_available === false,
        'false',
        $vehicle->is_available ? 'true' : 'false'
    );

    assertTest(
        'Véhicule avec status_id = 9 (Affecté)',
        $vehicle->status_id === 9,
        '9',
        (string)$vehicle->status_id
    );

    assertTest(
        'Chauffeur verrouillé (is_available = false)',
        $driver->is_available === false,
        'false',
        $driver->is_available ? 'true' : 'false'
    );

    assertTest(
        'Chauffeur avec status_id = 8 (En mission)',
        $driver->status_id === 8,
        '8',
        (string)$driver->status_id
    );

    echo "\n";

    // ═══════════════════════════════════════════════════════════════
    // TEST 2 : Suppression de l'affectation (libération des ressources)
    // ═══════════════════════════════════════════════════════════════
    echo "TEST 2 : Suppression de l'affectation (libération des ressources)\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $assignment1->delete();

    // Rafraîchir les ressources
    $vehicle->refresh();
    $driver->refresh();

    // Vérifications
    assertTest(
        'Véhicule libéré (is_available = true)',
        $vehicle->is_available === true,
        'true',
        $vehicle->is_available ? 'true' : 'false'
    );

    assertTest(
        'Véhicule avec status_id = 8 (Parking)',
        $vehicle->status_id === 8,
        '8',
        (string)$vehicle->status_id
    );

    assertTest(
        'Chauffeur libéré (is_available = true)',
        $driver->is_available === true,
        'true',
        $driver->is_available ? 'true' : 'false'
    );

    assertTest(
        'Chauffeur avec status_id = 7 (Disponible)',
        $driver->status_id === 7,
        '7',
        (string)$driver->status_id
    );

    echo "\n";

    // ═══════════════════════════════════════════════════════════════
    // TEST 3 : Création d'affectation passée (COMPLETED)
    // ═══════════════════════════════════════════════════════════════
    echo "TEST 3 : Création d'affectation passée (COMPLETED)\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $pastStart = Carbon::now()->subDays(10);
    $pastEnd = Carbon::now()->subDays(5);

    $assignment2 = Assignment::create([
        'organization_id' => $vehicle->organization_id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => $pastStart,
        'end_datetime' => $pastEnd,
        'reason' => 'Test affectation historique',
    ]);

    // Rafraîchir les ressources
    $vehicle->refresh();
    $driver->refresh();

    // Vérifications
    assertTest(
        'Affectation créée avec statut COMPLETED',
        $assignment2->status === Assignment::STATUS_COMPLETED,
        Assignment::STATUS_COMPLETED,
        $assignment2->status
    );

    assertTest(
        'Véhicule RESTE libéré (is_available = true)',
        $vehicle->is_available === true,
        'true',
        $vehicle->is_available ? 'true' : 'false'
    );

    assertTest(
        'Véhicule RESTE avec status_id = 8 (Parking) - PAS DE ZOMBIE',
        $vehicle->status_id === 8,
        '8',
        (string)$vehicle->status_id
    );

    assertTest(
        'Chauffeur RESTE libéré (is_available = true)',
        $driver->is_available === true,
        'true',
        $driver->is_available ? 'true' : 'false'
    );

    assertTest(
        'Chauffeur RESTE avec status_id = 7 (Disponible) - PAS DE ZOMBIE',
        $driver->status_id === 7,
        '7',
        (string)$driver->status_id
    );

    echo "\n";

    // Nettoyer
    $assignment2->delete();

    // ═══════════════════════════════════════════════════════════════
    // TEST 4 : Création et terminaison manuelle d'affectation active
    // ═══════════════════════════════════════════════════════════════
    echo "TEST 4 : Création et terminaison manuelle d'affectation active\n";
    echo "─────────────────────────────────────────────────────────────\n";

    $activeStart = Carbon::now()->subHours(2);

    $assignment3 = Assignment::create([
        'organization_id' => $vehicle->organization_id,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => $activeStart,
        'end_datetime' => null, // Durée indéterminée
        'reason' => 'Test affectation active',
    ]);

    // Vérifier statut ACTIVE
    assertTest(
        'Affectation créée avec statut ACTIVE',
        $assignment3->status === Assignment::STATUS_ACTIVE,
        Assignment::STATUS_ACTIVE,
        $assignment3->status
    );

    // Terminer manuellement
    $assignment3->end(Carbon::now());

    // Rafraîchir les ressources
    $vehicle->refresh();
    $driver->refresh();

    // Vérifications après terminaison manuelle
    assertTest(
        'Véhicule libéré après terminaison manuelle (is_available = true)',
        $vehicle->is_available === true,
        'true',
        $vehicle->is_available ? 'true' : 'false'
    );

    assertTest(
        'Véhicule avec status_id = 8 (Parking) après terminaison manuelle',
        $vehicle->status_id === 8,
        '8',
        (string)$vehicle->status_id
    );

    assertTest(
        'Chauffeur libéré après terminaison manuelle (is_available = true)',
        $driver->is_available === true,
        'true',
        $driver->is_available ? 'true' : 'false'
    );

    assertTest(
        'Chauffeur avec status_id = 7 (Disponible) après terminaison manuelle',
        $driver->status_id === 7,
        '7',
        (string)$driver->status_id
    );

    echo "\n";

    // Nettoyer
    $assignment3->delete();

    // ═══════════════════════════════════════════════════════════════
    // RÉSUMÉ FINAL
    // ═══════════════════════════════════════════════════════════════
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "📊 RÉSUMÉ DES TESTS\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "   Tests réussis : {$testsPassed}\n";
    echo "   Tests échoués : {$testsFailed}\n";
    echo "   Total         : " . ($testsPassed + $testsFailed) . "\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\n";

    if ($testsFailed === 0) {
        echo "🎉 TOUS LES TESTS ONT RÉUSSI !\n";
        echo "Le système de synchronisation des statuts fonctionne parfaitement.\n";
        exit(0);
    } else {
        echo "⚠️ CERTAINS TESTS ONT ÉCHOUÉ\n";
        echo "Veuillez vérifier les logs ci-dessus.\n";
        exit(1);
    }

} catch (\Exception $e) {
    echo "\n";
    echo "❌ ERREUR LORS DES TESTS\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n";
    exit(1);
}
