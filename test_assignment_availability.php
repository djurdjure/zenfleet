#!/usr/bin/env php
<?php

/**
 * 🧪 TEST DE DISPONIBILITÉ DES RESSOURCES POUR AFFECTATIONS
 * 
 * Vérifie que les véhicules et chauffeurs disponibles apparaissent
 * correctement dans le formulaire de création d'affectations.
 * 
 * UTILISATION:
 * docker exec zenfleet_php php test_assignment_availability.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Assignment;
use App\Traits\ResourceAvailability;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simuler un utilisateur authentifié
auth()->loginUsingId(1); // Admin

echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST DISPONIBILITÉ RESSOURCES - ZENFLEET            ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// 1. TEST TRAIT ResourceAvailability
echo "📊 TEST 1: TRAIT ResourceAvailability\n";
echo "────────────────────────────────────\n";

$testController = new class {
    use ResourceAvailability;
    
    public function testAvailability() {
        $vehicles = $this->getAvailableVehicles(1);
        $drivers = $this->getAvailableDrivers(1);
        
        echo "• Véhicules disponibles (trait): " . $vehicles->count() . "\n";
        if ($vehicles->count() > 0) {
            echo "  Exemples: " . $vehicles->take(3)->pluck('registration_plate')->implode(', ') . "\n";
        }
        
        echo "• Chauffeurs disponibles (trait): " . $drivers->count() . "\n";
        if ($drivers->count() > 0) {
            echo "  Exemples: " . $drivers->take(3)->map(fn($d) => $d->first_name . ' ' . $d->last_name)->implode(', ') . "\n";
        }
        
        return ['vehicles' => $vehicles->count(), 'drivers' => $drivers->count()];
    }
};

$traitResults = $testController->testAvailability();

// 2. TEST REQUÊTES FORMULAIRE (CORRIGÉES)
echo "\n📊 TEST 2: REQUÊTES FORMULAIRE CORRIGÉES\n";
echo "────────────────────────────────────────\n";

$organizationId = 1;

// Test requête véhicules corrigée
$vehicleQuery = Vehicle::where('organization_id', $organizationId)
    ->where(function($query) {
        $query->where('status_id', 8) // Parking
              ->orWhere(function($q) {
                  $q->where('is_available', true)
                    ->where('assignment_status', 'available')
                    ->whereNull('current_driver_id');
              });
    })
    ->where('is_archived', false);

$vehiclesAvailable = $vehicleQuery->get();
echo "• Véhicules disponibles (requête corrigée): " . $vehiclesAvailable->count() . "\n";

// Test requête chauffeurs corrigée
$driverQuery = Driver::where('organization_id', $organizationId)
    ->where(function($query) {
        $query->whereIn('status_id', [1, 7]) // Actif ou Disponible
              ->orWhere(function($q) {
                  $q->where('is_available', true)
                    ->where('assignment_status', 'available')
                    ->whereNull('current_vehicle_id');
              });
    });

$driversAvailable = $driverQuery->get();
echo "• Chauffeurs disponibles (requête corrigée): " . $driversAvailable->count() . "\n";

// 3. TEST ANCIENNE REQUÊTE (POUR COMPARAISON)
echo "\n📊 TEST 3: ANCIENNE REQUÊTE (PROBLÉMATIQUE)\n";
echo "────────────────────────────────────────────\n";

$oldVehicleQuery = Vehicle::where('organization_id', $organizationId)
    ->where('status_id', 1); // ERREUR: status_id=1 n'existe pas

$oldVehicles = $oldVehicleQuery->get();
echo "• Véhicules avec ancienne requête (status_id=1): " . $oldVehicles->count() . " ❌\n";

$oldDriverQuery = Driver::where('organization_id', $organizationId)
    ->where('status_id', 1); // Seulement statut "Actif"

$oldDrivers = $oldDriverQuery->get();
echo "• Chauffeurs avec ancienne requête (status_id=1): " . $oldDrivers->count() . " ⚠️\n";

// 4. ANALYSE DES STATUTS
echo "\n📊 TEST 4: ANALYSE DES STATUTS\n";
echo "────────────────────────────\n";

// Véhicules par statut
$vehicleStatuses = Vehicle::where('organization_id', $organizationId)
    ->selectRaw('status_id, COUNT(*) as count')
    ->groupBy('status_id')
    ->get();

echo "• Distribution des statuts véhicules:\n";
foreach ($vehicleStatuses as $status) {
    $statusName = \DB::table('vehicle_statuses')->where('id', $status->status_id)->value('name') ?? 'N/A';
    echo "  - Status ID {$status->status_id} ({$statusName}): {$status->count}\n";
}

// Chauffeurs par statut
$driverStatuses = Driver::where('organization_id', $organizationId)
    ->selectRaw('status_id, COUNT(*) as count')
    ->groupBy('status_id')
    ->get();

echo "\n• Distribution des statuts chauffeurs:\n";
foreach ($driverStatuses as $status) {
    $statusName = \DB::table('driver_statuses')->where('id', $status->status_id)->value('name') ?? 'N/A';
    echo "  - Status ID {$status->status_id} ({$statusName}): {$status->count}\n";
}

// 5. VÉHICULES ET CHAUFFEURS SPÉCIFIQUES
echo "\n📊 TEST 5: RESSOURCES SPÉCIFIQUES\n";
echo "─────────────────────────────\n";

$specificVehicles = ['105790-16', '118910-16'];
foreach ($specificVehicles as $plate) {
    $vehicle = Vehicle::where('registration_plate', $plate)->first();
    if ($vehicle) {
        echo "• Véhicule {$plate}:\n";
        echo "  - status_id: {$vehicle->status_id}\n";
        echo "  - is_available: " . ($vehicle->is_available ? 'true' : 'false') . "\n";
        echo "  - assignment_status: {$vehicle->assignment_status}\n";
        echo "  - current_driver_id: " . ($vehicle->current_driver_id ?? 'NULL') . "\n";
        echo "  - Disponible pour affectation: " . 
             (in_array($vehicle->id, $vehiclesAvailable->pluck('id')->toArray()) ? '✅ OUI' : '❌ NON') . "\n";
    }
}

$driver = Driver::where('first_name', 'Said')->where('last_name', 'merbouhi')->first();
if ($driver) {
    echo "\n• Chauffeur {$driver->first_name} {$driver->last_name}:\n";
    echo "  - status_id: {$driver->status_id}\n";
    echo "  - is_available: " . ($driver->is_available ? 'true' : 'false') . "\n";
    echo "  - assignment_status: {$driver->assignment_status}\n";
    echo "  - current_vehicle_id: " . ($driver->current_vehicle_id ?? 'NULL') . "\n";
    echo "  - Disponible pour affectation: " . 
         (in_array($driver->id, $driversAvailable->pluck('id')->toArray()) ? '✅ OUI' : '❌ NON') . "\n";
}

// 6. RÉSUMÉ
echo "\n╔══════════════════════════════════════════════════════════╗\n";
echo "║                         RÉSUMÉ                            ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

if ($vehiclesAvailable->count() > 0) {
    echo "✅ Véhicules disponibles trouvés: {$vehiclesAvailable->count()}\n";
} else {
    echo "❌ ERREUR: Aucun véhicule disponible trouvé\n";
    $allTestsPassed = false;
}

if ($driversAvailable->count() > 0) {
    echo "✅ Chauffeurs disponibles trouvés: {$driversAvailable->count()}\n";
} else {
    echo "❌ ERREUR: Aucun chauffeur disponible trouvé\n";
    $allTestsPassed = false;
}

if ($traitResults['vehicles'] == $vehiclesAvailable->count()) {
    echo "✅ Cohérence trait/requête pour véhicules\n";
} else {
    echo "⚠️  Incohérence trait/requête pour véhicules: {$traitResults['vehicles']} vs {$vehiclesAvailable->count()}\n";
}

echo "\n";
if ($allTestsPassed) {
    echo "🎉 TOUS LES TESTS PASSENT - Le système est opérationnel !\n";
} else {
    echo "⚠️  Des problèmes ont été détectés. Vérifiez les corrections.\n";
}

echo "\n";
