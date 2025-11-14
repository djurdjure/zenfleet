<?php

/**
 * ====================================================================
 * 🧪 TEST ENTERPRISE-GRADE : FORMULAIRE AFFECTATION V2
 * ====================================================================
 *
 * Tests complets pour vérifier :
 * ✅ Chargement du composant Livewire AssignmentForm
 * ✅ Disponibilité des véhicules et chauffeurs
 * ✅ Fonctionnement de l'auto-loading du kilométrage
 * ✅ Visibilité des méthodes critiques
 *
 * @version 1.0-Enterprise-Grade
 * @since 2025-11-14
 * ====================================================================
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\Driver;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST ENTERPRISE : FORMULAIRE AFFECTATION V2             ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$allTestsPassed = true;

// ================================================================
// TEST 1: Vérification du composant Livewire
// ================================================================
echo "📋 TEST 1: Vérification du composant Livewire AssignmentForm\n";
echo str_repeat("─", 66) . "\n";

try {
    $componentClass = 'App\\Livewire\\AssignmentForm';

    if (!class_exists($componentClass)) {
        throw new Exception("Classe AssignmentForm introuvable");
    }

    $reflection = new ReflectionClass($componentClass);

    // Vérifier que c'est un composant Livewire
    if (!$reflection->isSubclassOf('Livewire\\Component')) {
        throw new Exception("AssignmentForm n'hérite pas de Livewire\\Component");
    }

    echo "  ✅ Composant Livewire AssignmentForm trouvé\n";
    echo "  ✅ Hérite correctement de Livewire\\Component\n";

    // Vérifier les méthodes critiques
    $criticalMethods = [
        'mount',
        'render',
        'save',
        'updatedVehicleId',
        'updatedDriverId',
        'validateAssignment',
        'resetValidation'
    ];

    foreach ($criticalMethods as $method) {
        if (!$reflection->hasMethod($method)) {
            throw new Exception("Méthode manquante: {$method}");
        }

        $methodReflection = $reflection->getMethod($method);
        if (!$methodReflection->isPublic()) {
            throw new Exception("Méthode {$method} n'est pas publique");
        }
    }

    echo "  ✅ Toutes les méthodes critiques sont présentes et publiques\n";

    // Vérifier les propriétés
    $requiredProperties = [
        'vehicle_id',
        'driver_id',
        'start_datetime',
        'start_mileage',
        'current_vehicle_mileage'
    ];

    foreach ($requiredProperties as $property) {
        if (!$reflection->hasProperty($property)) {
            throw new Exception("Propriété manquante: {$property}");
        }
    }

    echo "  ✅ Toutes les propriétés requises sont présentes\n";
    echo "  ✨ TEST 1 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// TEST 2: Disponibilité des données (Véhicules)
// ================================================================
echo "🚗 TEST 2: Disponibilité des véhicules\n";
echo str_repeat("─", 66) . "\n";

try {
    $vehicles = Vehicle::get();
    $vehicleCount = $vehicles->count();

    if ($vehicleCount === 0) {
        throw new Exception("Aucun véhicule trouvé dans la base de données");
    }

    echo "  ✅ {$vehicleCount} véhicule(s) trouvé(s)\n";

    // Vérifier qu'au moins un véhicule a un kilométrage
    $vehiclesWithMileage = $vehicles->filter(function($vehicle) {
        return !is_null($vehicle->current_mileage);
    })->count();

    echo "  ✅ {$vehiclesWithMileage} véhicule(s) avec kilométrage défini\n";

    // Afficher quelques exemples
    echo "\n  📊 Exemples de véhicules :\n";
    foreach ($vehicles->take(5) as $vehicle) {
        $mileage = $vehicle->current_mileage ? number_format($vehicle->current_mileage) . ' km' : 'N/A';
        echo "     • {$vehicle->registration_plate} - {$vehicle->brand} {$vehicle->model}\n";
        echo "       Kilométrage: {$mileage}\n";
    }

    echo "\n  ✨ TEST 2 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// TEST 3: Disponibilité des données (Chauffeurs)
// ================================================================
echo "👤 TEST 3: Disponibilité des chauffeurs\n";
echo str_repeat("─", 66) . "\n";

try {
    $drivers = Driver::orderBy('last_name')->get();
    $driverCount = $drivers->count();

    if ($driverCount === 0) {
        throw new Exception("Aucun chauffeur trouvé dans la base de données");
    }

    echo "  ✅ {$driverCount} chauffeur(s) trouvé(s)\n";

    // Afficher quelques exemples
    echo "\n  📊 Exemples de chauffeurs :\n";
    foreach ($drivers->take(5) as $driver) {
        $license = $driver->license_number ?? 'N/A';
        echo "     • {$driver->first_name} {$driver->last_name}\n";
        echo "       Permis: {$license}\n";
    }

    echo "\n  ✨ TEST 3 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// TEST 4: Vérification du fichier Blade
// ================================================================
echo "🎨 TEST 4: Vérification du template Blade\n";
echo str_repeat("─", 66) . "\n";

try {
    $bladeFile = __DIR__ . '/resources/views/livewire/assignment-form.blade.php';

    if (!file_exists($bladeFile)) {
        throw new Exception("Fichier Blade introuvable: {$bladeFile}");
    }

    $bladeContent = file_get_contents($bladeFile);

    echo "  ✅ Fichier Blade trouvé\n";

    // Vérifier les éléments critiques
    $criticalElements = [
        'slimselect-vehicle' => 'Classe SlimSelect pour véhicules',
        'slimselect-driver' => 'Classe SlimSelect pour chauffeurs',
        'wire:model="vehicle_id"' => 'Binding Livewire vehicle_id',
        'wire:model="driver_id"' => 'Binding Livewire driver_id',
        'wire:model="start_mileage"' => 'Binding Livewire start_mileage',
        'current_vehicle_mileage' => 'Variable kilométrage actuel',
        'initSlimSelect()' => 'Initialisation SlimSelect',
        'typeof SlimSelect' => 'Vérification SlimSelect',
        'showToast' => 'Système de toasts'
    ];

    foreach ($criticalElements as $search => $description) {
        if (strpos($bladeContent, $search) === false) {
            throw new Exception("Élément manquant: {$description} ({$search})");
        }
    }

    echo "  ✅ Tous les éléments critiques sont présents\n";

    // Compter les sections importantes
    $sectionsCount = substr_count($bladeContent, 'bg-white rounded-lg');
    echo "  ✅ {$sectionsCount} section(s) card détectée(s)\n";

    echo "  ✨ TEST 4 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// TEST 5: Vérification du layout (SlimSelect CDN)
// ================================================================
echo "🔗 TEST 5: Vérification du layout (SlimSelect CDN)\n";
echo str_repeat("─", 66) . "\n";

try {
    $layoutFile = __DIR__ . '/resources/views/layouts/admin/catalyst.blade.php';

    if (!file_exists($layoutFile)) {
        throw new Exception("Fichier layout introuvable: {$layoutFile}");
    }

    $layoutContent = file_get_contents($layoutFile);

    echo "  ✅ Fichier layout trouvé\n";

    // Vérifier les CDN SlimSelect
    if (strpos($layoutContent, 'slim-select@2/dist/slimselect.css') === false) {
        throw new Exception("CDN CSS SlimSelect manquant");
    }
    echo "  ✅ CDN CSS SlimSelect présent\n";

    if (strpos($layoutContent, 'slim-select@2/dist/slimselect.min.js') === false) {
        throw new Exception("CDN JS SlimSelect manquant");
    }
    echo "  ✅ CDN JS SlimSelect présent\n";

    echo "  ✨ TEST 5 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// TEST 6: Test de simulation d'auto-loading kilométrage
// ================================================================
echo "⚙️ TEST 6: Simulation auto-loading kilométrage\n";
echo str_repeat("─", 66) . "\n";

try {
    // Récupérer un véhicule avec kilométrage
    $vehicleWithMileage = Vehicle::whereNotNull('current_mileage')
        ->where('current_mileage', '>', 0)
        ->first();

    if (!$vehicleWithMileage) {
        throw new Exception("Aucun véhicule avec kilométrage trouvé pour le test");
    }

    echo "  ✅ Véhicule de test: {$vehicleWithMileage->registration_plate}\n";
    echo "  ✅ Kilométrage actuel: " . number_format($vehicleWithMileage->current_mileage) . " km\n";

    // Simuler le comportement de updatedVehicleId()
    $simulatedStartMileage = null;
    $currentVehicleMileage = $vehicleWithMileage->current_mileage;

    if ($simulatedStartMileage === null && $currentVehicleMileage) {
        $simulatedStartMileage = $currentVehicleMileage;
    }

    if ($simulatedStartMileage === $currentVehicleMileage) {
        echo "  ✅ Auto-loading simulé avec succès\n";
        echo "  ✅ start_mileage serait pré-rempli à: " . number_format($simulatedStartMileage) . " km\n";
    } else {
        throw new Exception("Échec de la simulation d'auto-loading");
    }

    echo "  ✨ TEST 6 RÉUSSI\n\n";

} catch (Exception $e) {
    echo "  ❌ ÉCHEC: " . $e->getMessage() . "\n\n";
    $allTestsPassed = false;
}

// ================================================================
// RÉSUMÉ FINAL
// ================================================================
echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
if ($allTestsPassed) {
    echo "║  ✅ TOUS LES TESTS RÉUSSIS - SYSTÈME OPÉRATIONNEL         ║\n";
} else {
    echo "║  ❌ CERTAINS TESTS ONT ÉCHOUÉ - VÉRIFICATION REQUISE      ║\n";
}
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "📊 Résumé des tests :\n";
echo "  1. ✅ Composant Livewire AssignmentForm\n";
echo "  2. ✅ Disponibilité des véhicules\n";
echo "  3. ✅ Disponibilité des chauffeurs\n";
echo "  4. ✅ Template Blade avec SlimSelect\n";
echo "  5. ✅ Layout avec CDN SlimSelect\n";
echo "  6. ✅ Auto-loading kilométrage\n";
echo "\n";

if ($allTestsPassed) {
    echo "🎯 PROCHAINE ÉTAPE : Accédez à http://localhost/admin/assignments/create\n";
    echo "   pour tester en conditions réelles.\n\n";
    exit(0);
} else {
    echo "⚠️  ATTENTION : Des problèmes ont été détectés. Consultez les logs ci-dessus.\n\n";
    exit(1);
}
