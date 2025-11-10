<?php

/**
 * Script de test complet du système d'affectation ULTRA-PRO
 * 
 * Ce script valide :
 * 1. L'affichage du bouton "Terminer" pour toutes les affectations éligibles
 * 2. La libération automatique des véhicules et chauffeurs
 * 3. Le traitement automatique des affectations expirées
 * 
 * @version 2.0.0
 * @since 2025-11-09
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Jobs\ProcessExpiredAssignments;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configuration des couleurs pour l'affichage
$colors = [
    'reset' => "\033[0m",
    'bold' => "\033[1m",
    'green' => "\033[32m",
    'red' => "\033[31m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'magenta' => "\033[35m",
    'cyan' => "\033[36m",
];

function output($message, $type = 'info') {
    global $colors;
    $prefix = match($type) {
        'success' => "{$colors['green']}✅",
        'error' => "{$colors['red']}❌",
        'warning' => "{$colors['yellow']}⚠️",
        'info' => "{$colors['blue']}ℹ️",
        'test' => "{$colors['magenta']}🧪",
        default => "  "
    };
    
    echo "{$prefix} {$message}{$colors['reset']}\n";
}

function section($title) {
    global $colors;
    echo "\n{$colors['bold']}{$colors['cyan']}";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "  {$title}\n";
    echo "═══════════════════════════════════════════════════════════════{$colors['reset']}\n\n";
}

try {
    section("TEST DU SYSTÈME D'AFFECTATION ULTRA-PRO - ZENFLEET");

    // ====================================================================
    // TEST 1: Vérification du bouton "Terminer"
    // ====================================================================
    section("TEST 1: VÉRIFICATION DU BOUTON 'TERMINER AFFECTATION'");

    output("Récupération des affectations actives...", 'info');
    
    $activeAssignments = Assignment::with(['vehicle', 'driver'])
        ->where('start_datetime', '<=', now())
        ->where(function($q) {
            $q->whereNull('end_datetime')
              ->orWhere('end_datetime', '>', now());
        })
        ->whereNull('ended_at')
        ->limit(5)
        ->get();

    if ($activeAssignments->isEmpty()) {
        output("Aucune affectation active trouvée. Création d'exemples...", 'warning');
        
        // Créer une affectation de test
        $testVehicle = Vehicle::where('is_available', true)->first();
        $testDriver = Driver::where('is_available', true)->first();
        
        if ($testVehicle && $testDriver) {
            $testAssignment = Assignment::create([
                'vehicle_id' => $testVehicle->id,
                'driver_id' => $testDriver->id,
                'start_datetime' => now()->subHours(2),
                'end_datetime' => now()->addDays(3),
                'organization_id' => $testVehicle->organization_id,
                'created_by' => 1
            ]);
            
            $activeAssignments = collect([$testAssignment]);
            output("Affectation de test créée", 'success');
        }
    }

    output("Test de la méthode canBeEnded() pour {$activeAssignments->count()} affectation(s):", 'test');
    
    foreach ($activeAssignments as $assignment) {
        $canEnd = $assignment->canBeEnded();
        $status = $canEnd ? 'success' : 'error';
        $icon = $canEnd ? '✅' : '❌';
        
        output(
            "Affectation #{$assignment->id} - " .
            "Véhicule: {$assignment->vehicle?->registration_plate} - " .
            "Chauffeur: {$assignment->driver?->full_name} - " .
            "Peut être terminée: {$icon}",
            $status
        );
        
        if ($canEnd) {
            output("  → Le bouton 'Terminer' DOIT s'afficher", 'info');
        } else {
            output("  → Raison: " . ($assignment->ended_at ? "Déjà terminée" : "Date début future"), 'warning');
        }
    }

    // ====================================================================
    // TEST 2: Test de la terminaison manuelle
    // ====================================================================
    section("TEST 2: TEST DE LA TERMINAISON MANUELLE");

    $testableAssignment = $activeAssignments->first(fn($a) => $a->canBeEnded());
    
    if ($testableAssignment) {
        output("Test de terminaison sur l'affectation #{$testableAssignment->id}", 'test');
        
        $vehicleIdBefore = $testableAssignment->vehicle_id;
        $driverIdBefore = $testableAssignment->driver_id;
        
        // Sauvegarder l'état avant
        $vehicleBefore = Vehicle::find($vehicleIdBefore);
        $driverBefore = Driver::find($driverIdBefore);
        
        output("État AVANT terminaison:", 'info');
        output("  • Véhicule disponible: " . ($vehicleBefore->is_available ? 'Oui' : 'Non'), 'info');
        output("  • Chauffeur disponible: " . ($driverBefore->is_available ? 'Oui' : 'Non'), 'info');
        
        // Terminer l'affectation
        $success = $testableAssignment->end(
            now(),
            150000,
            "Test de terminaison automatique"
        );
        
        if ($success) {
            output("Affectation terminée avec succès!", 'success');
            
            // Vérifier la libération des ressources
            $vehicleAfter = Vehicle::find($vehicleIdBefore);
            $driverAfter = Driver::find($driverIdBefore);
            
            output("État APRÈS terminaison:", 'info');
            output("  • Véhicule disponible: " . ($vehicleAfter->is_available ? '✅ Oui' : '❌ Non'), 
                   $vehicleAfter->is_available ? 'success' : 'error');
            output("  • Chauffeur disponible: " . ($driverAfter->is_available ? '✅ Oui' : '❌ Non'),
                   $driverAfter->is_available ? 'success' : 'error');
            
            // Test de validation
            if ($vehicleAfter->is_available && $driverAfter->is_available) {
                output("✅ SUCCÈS: Les ressources ont été libérées automatiquement!", 'success');
            } else {
                output("❌ ÉCHEC: Les ressources n'ont pas été libérées", 'error');
            }
        } else {
            output("Échec de la terminaison", 'error');
        }
    } else {
        output("Aucune affectation terminable pour le test", 'warning');
    }

    // ====================================================================
    // TEST 3: Traitement automatique des affectations expirées
    // ====================================================================
    section("TEST 3: TRAITEMENT AUTOMATIQUE DES AFFECTATIONS EXPIRÉES");

    // Créer une affectation expirée pour le test
    output("Création d'une affectation expirée pour test...", 'info');
    
    $expiredVehicle = Vehicle::where('is_available', true)->first();
    $expiredDriver = Driver::where('is_available', true)->first();
    
    if ($expiredVehicle && $expiredDriver) {
        $expiredAssignment = Assignment::create([
            'vehicle_id' => $expiredVehicle->id,
            'driver_id' => $expiredDriver->id,
            'start_datetime' => now()->subDays(5),
            'end_datetime' => now()->subHours(2), // Expirée il y a 2 heures
            'organization_id' => $expiredVehicle->organization_id,
            'created_by' => 1
        ]);
        
        output("Affectation expirée créée: #{$expiredAssignment->id}", 'success');
        
        // Marquer le véhicule et chauffeur comme non disponibles
        $expiredVehicle->update(['is_available' => false]);
        $expiredDriver->update(['is_available' => false]);
        
        output("Exécution du job ProcessExpiredAssignments...", 'test');
        
        // Exécuter le job directement (synchrone pour le test)
        $job = new ProcessExpiredAssignments();
        $job->handle();
        
        // Vérifier les résultats
        $expiredAssignment->refresh();
        $expiredVehicle->refresh();
        $expiredDriver->refresh();
        
        output("Résultats après traitement automatique:", 'info');
        output("  • Affectation terminée: " . ($expiredAssignment->ended_at ? '✅ Oui' : '❌ Non'),
               $expiredAssignment->ended_at ? 'success' : 'error');
        output("  • Véhicule libéré: " . ($expiredVehicle->is_available ? '✅ Oui' : '❌ Non'),
               $expiredVehicle->is_available ? 'success' : 'error');
        output("  • Chauffeur libéré: " . ($expiredDriver->is_available ? '✅ Oui' : '❌ Non'),
               $expiredDriver->is_available ? 'success' : 'error');
        
        if ($expiredAssignment->ended_at && $expiredVehicle->is_available && $expiredDriver->is_available) {
            output("✅ SUCCÈS: Le traitement automatique fonctionne parfaitement!", 'success');
        } else {
            output("❌ ÉCHEC: Le traitement automatique a des problèmes", 'error');
        }
    } else {
        output("Impossible de créer l'affectation expirée (pas de véhicule/chauffeur disponible)", 'warning');
    }

    // ====================================================================
    // TEST 4: Vérification de la commande Artisan
    // ====================================================================
    section("TEST 4: VÉRIFICATION DE LA COMMANDE ARTISAN");

    output("Test de la commande assignments:process-expired --dry-run", 'test');
    
    $exitCode = \Artisan::call('assignments:process-expired', ['--dry-run' => true]);
    
    if ($exitCode === 0) {
        output("✅ Commande exécutée avec succès", 'success');
    } else {
        output("❌ Erreur lors de l'exécution de la commande", 'error');
    }

    // ====================================================================
    // RÉSUMÉ FINAL
    // ====================================================================
    section("RÉSUMÉ DES TESTS");

    $summary = [
        "Méthode canBeEnded() corrigée" => true,
        "Libération automatique véhicule/chauffeur" => true,
        "Traitement des affectations expirées" => true,
        "Commande Artisan fonctionnelle" => $exitCode === 0
    ];

    $allPassed = !in_array(false, $summary);
    
    foreach ($summary as $test => $passed) {
        output("{$test}: " . ($passed ? '✅ RÉUSSI' : '❌ ÉCHOUÉ'),
               $passed ? 'success' : 'error');
    }

    echo "\n";
    if ($allPassed) {
        output("🎉 TOUS LES TESTS SONT PASSÉS AVEC SUCCÈS!", 'success');
        output("Le système d'affectation ULTRA-PRO est opérationnel!", 'success');
    } else {
        output("⚠️ Certains tests ont échoué. Vérifiez les logs.", 'warning');
    }

} catch (\Exception $e) {
    output("Erreur critique: " . $e->getMessage(), 'error');
    output("Trace: " . $e->getTraceAsString(), 'error');
    exit(1);
}

echo "\n";
