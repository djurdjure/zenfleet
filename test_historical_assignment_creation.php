#!/usr/bin/env php
<?php

/**
 * 🧪 TEST DE CRÉATION D'AFFECTATION HISTORIQUE
 * 
 * Vérifie que les ressources sont automatiquement libérées
 * lors de la création d'une affectation avec dates passées.
 * 
 * UTILISATION:
 * docker exec zenfleet_php php test_historical_assignment_creation.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simuler un utilisateur authentifié
auth()->loginUsingId(1); // Admin

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST CRÉATION AFFECTATION HISTORIQUE - ZENFLEET         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// 1. Sélectionner des ressources disponibles pour le test
echo "📊 ÉTAPE 1: SÉLECTION DES RESSOURCES DE TEST\n";
echo "──────────────────────────────────────────\n";

$testVehicle = Vehicle::where('organization_id', 1)
    ->where('is_available', true)
    ->where('status_id', 8) // Parking
    ->first();

$testDriver = Driver::where('organization_id', 1)
    ->where('is_available', true)
    ->whereIn('status_id', [1, 7]) // Actif ou Disponible
    ->first();

if (!$testVehicle || !$testDriver) {
    echo "❌ ERREUR: Pas assez de ressources disponibles pour le test\n";
    exit(1);
}

echo "• Véhicule sélectionné: {$testVehicle->registration_plate} (ID: {$testVehicle->id})\n";
echo "  - status_id actuel: {$testVehicle->status_id}\n";
echo "  - is_available: " . ($testVehicle->is_available ? 'true' : 'false') . "\n\n";

echo "• Chauffeur sélectionné: {$testDriver->first_name} {$testDriver->last_name} (ID: {$testDriver->id})\n";
echo "  - status_id actuel: {$testDriver->status_id}\n";
echo "  - is_available: " . ($testDriver->is_available ? 'true' : 'false') . "\n\n";

// 2. Créer une affectation avec dates dans le passé
echo "📊 ÉTAPE 2: CRÉATION D'AFFECTATION HISTORIQUE\n";
echo "──────────────────────────────────────────\n";

$startDate = Carbon::now()->subDays(10); // Il y a 10 jours
$endDate = Carbon::now()->subDays(5);    // Il y a 5 jours

echo "• Période: {$startDate->format('d/m/Y H:i')} → {$endDate->format('d/m/Y H:i')}\n";
echo "• Statut attendu: completed\n\n";

$assignment = Assignment::create([
    'organization_id' => 1,
    'vehicle_id' => $testVehicle->id,
    'driver_id' => $testDriver->id,
    'start_datetime' => $startDate,
    'end_datetime' => $endDate,
    'reason' => 'Test affectation historique',
    'notes' => 'Test automatique - ' . now()->format('Y-m-d H:i:s'),
    'created_by' => 1
]);

echo "✅ Affectation #{$assignment->id} créée\n";
echo "• Statut calculé: {$assignment->status}\n";
echo "• ended_at: " . ($assignment->ended_at ? $assignment->ended_at->format('d/m/Y H:i') : 'NULL') . "\n\n";

// 3. Vérifier l'état des ressources après création
echo "📊 ÉTAPE 3: VÉRIFICATION DES RESSOURCES\n";
echo "────────────────────────────────────\n";

// Recharger les ressources depuis la base
$testVehicle->refresh();
$testDriver->refresh();

$vehicleTests = [
    'is_available' => ['expected' => true, 'actual' => $testVehicle->is_available],
    'assignment_status' => ['expected' => 'available', 'actual' => $testVehicle->assignment_status],
    'status_id' => ['expected' => 8, 'actual' => $testVehicle->status_id],
    'current_driver_id' => ['expected' => null, 'actual' => $testVehicle->current_driver_id]
];

$driverTests = [
    'is_available' => ['expected' => true, 'actual' => $testDriver->is_available],
    'assignment_status' => ['expected' => 'available', 'actual' => $testDriver->assignment_status],
    'status_id' => ['expected' => in_array($testDriver->status_id, [1, 7]), 'actual' => true],
    'current_vehicle_id' => ['expected' => null, 'actual' => $testDriver->current_vehicle_id]
];

$allTestsPassed = true;

echo "🚗 VÉHICULE {$testVehicle->registration_plate}:\n";
foreach ($vehicleTests as $field => $test) {
    $passed = ($field === 'current_driver_id' || $field === 'current_vehicle_id') 
        ? $test['actual'] === $test['expected']
        : $test['actual'] == $test['expected'];
    
    $icon = $passed ? '✅' : '❌';
    echo "  {$icon} {$field}: ";
    
    if ($field === 'current_driver_id' || $field === 'current_vehicle_id') {
        echo ($test['actual'] === null ? 'NULL' : $test['actual']);
        echo " (attendu: " . ($test['expected'] === null ? 'NULL' : $test['expected']) . ")\n";
    } else {
        echo "{$test['actual']} (attendu: {$test['expected']})\n";
    }
    
    if (!$passed) $allTestsPassed = false;
}

echo "\n👤 CHAUFFEUR {$testDriver->first_name} {$testDriver->last_name}:\n";
foreach ($driverTests as $field => $test) {
    if ($field === 'status_id') {
        $passed = in_array($testDriver->status_id, [1, 7]);
        $icon = $passed ? '✅' : '❌';
        echo "  {$icon} {$field}: {$testDriver->status_id} (attendu: 1 ou 7)\n";
        if (!$passed) $allTestsPassed = false;
    } else {
        $passed = ($field === 'current_vehicle_id') 
            ? $test['actual'] === $test['expected']
            : $test['actual'] == $test['expected'];
        
        $icon = $passed ? '✅' : '❌';
        echo "  {$icon} {$field}: ";
        
        if ($field === 'current_vehicle_id') {
            echo ($test['actual'] === null ? 'NULL' : $test['actual']);
            echo " (attendu: " . ($test['expected'] === null ? 'NULL' : $test['expected']) . ")\n";
        } else {
            echo "{$test['actual']} (attendu: {$test['expected']})\n";
        }
        
        if (!$passed) $allTestsPassed = false;
    }
}

// 4. Nettoyer (supprimer l'affectation de test)
echo "\n📊 ÉTAPE 4: NETTOYAGE\n";
echo "───────────────────\n";

try {
    $assignment->forceDelete();
    echo "✅ Affectation de test supprimée\n";
} catch (\Exception $e) {
    echo "⚠️  Erreur lors de la suppression: {$e->getMessage()}\n";
}

// 5. Résultat final
echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                         RÉSULTAT                              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

if ($allTestsPassed) {
    echo "🎉 SUCCÈS: Toutes les ressources ont été correctement libérées !\n";
    echo "La correction de l'Observer fonctionne parfaitement.\n";
} else {
    echo "❌ ÉCHEC: Les ressources n'ont pas été libérées correctement.\n";
    echo "L'Observer nécessite encore des corrections.\n";
}

echo "\n";
