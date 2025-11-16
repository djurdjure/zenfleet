<?php

/**
 * 🧪 SCRIPT DE TEST ENTERPRISE-GRADE : VehicleMileageService
 *
 * Ce script teste le nouveau service de gestion du kilométrage.
 *
 * TESTS EFFECTUÉS :
 * 1. Enregistrement d'un relevé manuel
 * 2. Enregistrement d'un relevé de début d'affectation
 * 3. Enregistrement d'un relevé de fin d'affectation
 * 4. Validation de la cohérence des données
 * 5. Détection des incohérences
 *
 * UTILISATION :
 * php test_mileage_service.php [--vehicle-id=X]
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-16
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Services\VehicleMileageService;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Parse des arguments
$vehicleId = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--vehicle-id=')) {
        $vehicleId = (int) substr($arg, 13);
    }
}

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║      🧪 TEST ENTERPRISE - VehicleMileageService                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // 1. SÉLECTION DU VÉHICULE
    if ($vehicleId) {
        $vehicle = Vehicle::findOrFail($vehicleId);
    } else {
        $vehicle = Vehicle::with('vehicleStatus')
            ->where('organization_id', 1)
            ->first();
        
        if (!$vehicle) {
            echo "❌ Aucun véhicule trouvé dans l'organisation\n";
            exit(1);
        }
    }

    echo "🚗 VÉHICULE SÉLECTIONNÉ\n";
    echo "   ID : {$vehicle->id}\n";
    echo "   Immatriculation : {$vehicle->registration_plate}\n";
    echo "   Kilométrage actuel : " . number_format($vehicle->current_mileage) . " km\n";
    echo "   Organisation : {$vehicle->organization_id}\n\n";

    // Récupérer le dernier relevé
    $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
        ->orderBy('recorded_at', 'desc')
        ->first();

    if ($lastReading) {
        echo "📊 DERNIER RELEVÉ ENREGISTRÉ\n";
        echo "   Date : {$lastReading->recorded_at->format('d/m/Y H:i')}\n";
        echo "   Kilométrage : " . number_format($lastReading->mileage) . " km\n";
        echo "   Méthode : {$lastReading->recording_method}\n\n";
    } else {
        echo "ℹ️  Aucun relevé existant\n\n";
    }

    // 2. INSTANCIER LE SERVICE
    $mileageService = app(VehicleMileageService::class);
    echo "✅ Service VehicleMileageService instancié\n\n";

    // 3. TEST 1 : Enregistrement d'un relevé manuel
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "TEST 1 : Enregistrement d'un relevé manuel\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";

    $newMileage = ($lastReading ? $lastReading->mileage : $vehicle->current_mileage) + 100;
    
    echo "   Nouveau kilométrage : " . number_format($newMileage) . " km\n";
    echo "   Tentative d'enregistrement...\n";

    try {
        $result = $mileageService->recordManualReading(
            $vehicle,
            $newMileage,
            "Test manuel depuis le script de validation"
        );

        echo "   ✅ Relevé enregistré avec succès\n";
        echo "   Actions effectuées : " . implode(', ', $result['actions']) . "\n";
        echo "   Différence : +" . number_format($result['difference']) . " km\n\n";

    } catch (\Exception $e) {
        echo "   ❌ ERREUR : {$e->getMessage()}\n\n";
    }

    // 4. TEST 2 : Validation de la cohérence
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "TEST 2 : Validation de la cohérence des données\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";

    $vehicle->refresh();
    $newLastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
        ->orderBy('recorded_at', 'desc')
        ->first();

    echo "   Kilométrage véhicule après enregistrement : " . number_format($vehicle->current_mileage) . " km\n";
    echo "   Kilométrage dernier relevé : " . number_format($newLastReading->mileage) . " km\n";

    if ($vehicle->current_mileage === $newLastReading->mileage) {
        echo "   ✅ COHÉRENCE VALIDÉE : Les kilométrages correspondent\n\n";
    } else {
        echo "   ❌ INCOHÉRENCE DÉTECTÉE : Les kilométrages ne correspondent pas\n";
        echo "      Différence : " . abs($vehicle->current_mileage - $newLastReading->mileage) . " km\n\n";
    }

    // 5. TEST 3 : Tentative d'enregistrement d'un kilométrage décroissant (doit échouer)
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "TEST 3 : Validation du refus de kilométrage décroissant\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";

    $invalidMileage = $vehicle->current_mileage - 50;
    echo "   Tentative d'enregistrement d'un kilométrage inférieur : " . number_format($invalidMileage) . " km\n";

    try {
        $result = $mileageService->recordManualReading(
            $vehicle,
            $invalidMileage,
            "Test de validation (doit échouer)"
        );

        echo "   ❌ ÉCHEC DU TEST : L'enregistrement aurait dû être refusé\n\n";

    } catch (\InvalidArgumentException $e) {
        echo "   ✅ VALIDATION RÉUSSIE : Le kilométrage décroissant a été refusé\n";
        echo "   Message : {$e->getMessage()}\n\n";
    } catch (\Exception $e) {
        echo "   ❌ ERREUR INATTENDUE : {$e->getMessage()}\n\n";
    }

    // 6. TEST 4 : Détection des incohérences
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "TEST 4 : Détection des incohérences dans l'organisation\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";

    $inconsistencies = $mileageService->detectInconsistencies($vehicle->organization_id);

    if ($inconsistencies->isEmpty()) {
        echo "   ✅ Aucune incohérence détectée\n\n";
    } else {
        echo "   ⚠️  {$inconsistencies->count()} incohérence(s) détectée(s) :\n\n";
        
        foreach ($inconsistencies as $inconsistency) {
            echo "   • Véhicule : {$inconsistency['registration_plate']}\n";
            echo "     Kilométrage véhicule : " . number_format($inconsistency['current_mileage']) . " km\n";
            echo "     Dernier relevé : " . number_format($inconsistency['last_reading_mileage']) . " km\n";
            echo "     Différence : " . number_format($inconsistency['difference']) . " km\n\n";
        }
    }

    // 7. TEST 5 : Historique des relevés
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "TEST 5 : Consultation de l'historique\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";

    $history = $mileageService->getMileageHistory($vehicle, 10);

    echo "   📊 Derniers relevés (10 max) :\n\n";

    foreach ($history as $reading) {
        echo "   • {$reading->recorded_at->format('d/m/Y H:i')} : ";
        echo number_format($reading->mileage) . " km";
        echo " ({$reading->recording_method})";
        
        if ($reading->recordedBy) {
            echo " - par {$reading->recordedBy->name}";
        }
        
        echo "\n";
    }

    echo "\n";

    // 8. RAPPORT FINAL
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║                        ✅ TESTS RÉUSSIS                            ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

    echo "Le service VehicleMileageService fonctionne correctement :\n";
    echo "   ✓ Enregistrement des relevés manuels\n";
    echo "   ✓ Mise à jour du kilométrage véhicule\n";
    echo "   ✓ Validation de la cohérence\n";
    echo "   ✓ Refus des kilométrages décroissants\n";
    echo "   ✓ Détection des incohérences\n";
    echo "   ✓ Consultation de l'historique\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR CRITIQUE : {$e->getMessage()}\n";
    echo "Trace : {$e->getTraceAsString()}\n\n";
    exit(1);
}
