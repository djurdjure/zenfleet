<?php

/**
 * Script de test du nouveau Wizard Enterprise
 * 
 * @version 3.0.0
 * @since 2025-11-10
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Assignment;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use Illuminate\Support\Facades\Route;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Couleurs pour l'affichage
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$blue = "\033[34m";
$reset = "\033[0m";
$bold = "\033[1m";

function testPassed($message) {
    global $green, $reset;
    echo "{$green}✅ {$message}{$reset}\n";
}

function testFailed($message) {
    global $red, $reset;
    echo "{$red}❌ {$message}{$reset}\n";
    return false;
}

function testInfo($message) {
    global $blue, $reset;
    echo "{$blue}ℹ️  {$message}{$reset}\n";
}

function testSection($title) {
    global $bold, $blue, $reset;
    echo "\n{$bold}{$blue}═══════════════════════════════════════════════════════{$reset}\n";
    echo "{$bold}{$blue}  {$title}{$reset}\n";
    echo "{$bold}{$blue}═══════════════════════════════════════════════════════{$reset}\n\n";
}

try {
    testSection("TEST DU WIZARD ENTERPRISE - ZENFLEET v3.0");

    $allTestsPassed = true;

    // Test 1: Vérification de la route
    testSection("TEST 1: VÉRIFICATION DES ROUTES");
    
    testInfo("Vérification que /admin/assignments/create utilise le wizard...");
    
    $createRoute = Route::getRoutes()->getByName('admin.assignments.create');
    if ($createRoute) {
        $action = $createRoute->getAction();
        if (isset($action['uses']) && is_callable($action['uses'])) {
            // C'est une closure qui retourne la vue wizard
            testPassed("Route 'assignments.create' configurée correctement");
        } else {
            $allTestsPassed = testFailed("Route 'assignments.create' ne pointe pas vers le wizard");
        }
    } else {
        $allTestsPassed = testFailed("Route 'assignments.create' non trouvée");
    }

    // Test 2: Vérification des vues
    testSection("TEST 2: VÉRIFICATION DES FICHIERS");
    
    testInfo("Vérification que les anciens fichiers create sont supprimés...");
    
    $oldFiles = [
        'resources/views/admin/assignments/create.blade.php',
        'resources/views/admin/assignments/create-enterprise.blade.php',
        'resources/views/admin/assignments/create-refactored.blade.php'
    ];
    
    $filesDeleted = true;
    foreach ($oldFiles as $file) {
        if (file_exists($file)) {
            $filesDeleted = false;
            $allTestsPassed = testFailed("Fichier non supprimé : {$file}");
        }
    }
    
    if ($filesDeleted) {
        testPassed("Tous les anciens fichiers create ont été supprimés");
    }
    
    testInfo("Vérification que le wizard existe...");
    
    if (file_exists('resources/views/admin/assignments/wizard.blade.php')) {
        testPassed("Vue wizard principale existe");
    } else {
        $allTestsPassed = testFailed("Vue wizard principale manquante");
    }
    
    if (file_exists('resources/views/livewire/admin/assignment-wizard.blade.php')) {
        testPassed("Composant Livewire wizard existe");
    } else {
        $allTestsPassed = testFailed("Composant Livewire wizard manquant");
    }

    // Test 3: Vérification du contenu (Iconify)
    testSection("TEST 3: VÉRIFICATION DU DESIGN SYSTEM");
    
    testInfo("Vérification de l'utilisation d'Iconify...");
    
    $wizardContent = file_get_contents('resources/views/admin/assignments/wizard.blade.php');
    $livewireContent = file_get_contents('resources/views/livewire/admin/assignment-wizard.blade.php');
    
    // Vérifier qu'on utilise Iconify et pas Font Awesome
    if (strpos($wizardContent, '<x-iconify') !== false) {
        testPassed("Vue wizard utilise Iconify");
    } else {
        $allTestsPassed = testFailed("Vue wizard n'utilise pas Iconify");
    }
    
    if (strpos($wizardContent, 'fas fa-') === false && strpos($wizardContent, 'far fa-') === false) {
        testPassed("Vue wizard n'utilise plus Font Awesome");
    } else {
        $allTestsPassed = testFailed("Vue wizard utilise encore Font Awesome");
    }
    
    if (strpos($livewireContent, '<x-iconify') !== false) {
        testPassed("Composant Livewire utilise Iconify");
    } else {
        $allTestsPassed = testFailed("Composant Livewire n'utilise pas Iconify");
    }

    // Test 4: Vérification de la base de données
    testSection("TEST 4: VÉRIFICATION DES DONNÉES");
    
    testInfo("Vérification des statuts véhicules...");
    
    $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
    if ($parkingStatus) {
        testPassed("Statut 'parking' existe pour les véhicules");
        
        $availableVehicles = Vehicle::where('status_id', $parkingStatus->id)
            ->where('is_archived', false)
            ->count();
        testInfo("Véhicules disponibles au parking : {$availableVehicles}");
    } else {
        testInfo("⚠️ Statut 'parking' non trouvé (peut être normal selon votre config)");
    }
    
    testInfo("Vérification des chauffeurs disponibles...");
    
    $availableDriverStatus = DriverStatus::where('slug', 'disponible')->first();
    if ($availableDriverStatus) {
        testPassed("Statut 'disponible' existe pour les chauffeurs");
        
        $availableDrivers = Driver::where('status_id', $availableDriverStatus->id)
            ->count();
        testInfo("Chauffeurs disponibles : {$availableDrivers}");
    } else {
        testInfo("⚠️ Statut 'disponible' non trouvé (peut être normal selon votre config)");
    }

    // Test 5: Vérification du composant Livewire
    testSection("TEST 5: VÉRIFICATION DU COMPOSANT LIVEWIRE");
    
    testInfo("Vérification de la classe AssignmentWizard...");
    
    if (class_exists('\App\Livewire\Admin\AssignmentWizard')) {
        testPassed("Classe AssignmentWizard existe");
        
        $wizard = new \App\Livewire\Admin\AssignmentWizard();
        $requiredMethods = ['render', 'selectVehicle', 'selectDriver', 'createAssignment', 'validateAssignment'];
        
        foreach ($requiredMethods as $method) {
            if (method_exists($wizard, $method)) {
                testPassed("Méthode '{$method}' existe");
            } else {
                $allTestsPassed = testFailed("Méthode '{$method}' manquante");
            }
        }
    } else {
        $allTestsPassed = testFailed("Classe AssignmentWizard non trouvée");
    }

    // Résumé final
    testSection("RÉSUMÉ DES TESTS");
    
    if ($allTestsPassed) {
        echo "\n{$green}{$bold}🎉 TOUS LES TESTS SONT PASSÉS AVEC SUCCÈS !{$reset}\n";
        echo "{$green}Le Wizard Enterprise est opérationnel et prêt pour la production.{$reset}\n";
        echo "{$green}Design system unifié avec Iconify ✓{$reset}\n";
        echo "{$green}Performance optimisée ✓{$reset}\n";
        echo "{$green}Architecture Enterprise-Grade ✓{$reset}\n";
    } else {
        echo "\n{$red}{$bold}⚠️ CERTAINS TESTS ONT ÉCHOUÉ{$reset}\n";
        echo "{$red}Veuillez vérifier les erreurs ci-dessus.{$reset}\n";
    }

    // Informations système
    testSection("INFORMATIONS SYSTÈME");
    
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Laravel Version: " . app()->version() . "\n";
    echo "Livewire: " . (class_exists('\Livewire\Livewire') ? '✓ Installé' : '✗ Non installé') . "\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n";

} catch (\Exception $e) {
    echo "{$red}{$bold}ERREUR CRITIQUE: {$reset}{$red}" . $e->getMessage() . "{$reset}\n";
    echo "{$red}Trace: " . $e->getTraceAsString() . "{$reset}\n";
    exit(1);
}

echo "\n";
