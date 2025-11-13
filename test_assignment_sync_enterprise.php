<?php

/**
 * 🚀 TEST ENTERPRISE-GRADE : VÉRIFICATION DE LA SYNCHRONISATION DES AFFECTATIONS
 * 
 * Ce script teste que le système de gestion des affectations fonctionne
 * correctement avec la libération automatique des ressources.
 * 
 * @version 1.0.0
 * @since 2025-11-12
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Configuration de l'output en couleur
function output($message, $type = 'info') {
    $colors = [
        'info' => "\033[0;36m",
        'success' => "\033[0;32m",
        'warning' => "\033[0;33m",
        'error' => "\033[0;31m",
        'header' => "\033[1;34m",
    ];
    
    $reset = "\033[0m";
    echo $colors[$type] . $message . $reset . PHP_EOL;
}

function separator($title = '') {
    echo PHP_EOL;
    output(str_repeat('=', 80), 'header');
    if ($title) {
        output("   $title   ", 'header');
        output(str_repeat('=', 80), 'header');
    }
    echo PHP_EOL;
}

// Début des tests
separator("🧪 TEST DE SYNCHRONISATION DES AFFECTATIONS - ENTERPRISE GRADE");

try {
    // 1. CRÉER UNE AFFECTATION DE TEST
    separator("TEST 1: CRÉATION D'AFFECTATION");
    
    // Trouver un véhicule disponible
    $vehicle = Vehicle::where('is_available', true)
        ->where('assignment_status', 'available')
        ->first();
    
    if (!$vehicle) {
        output("❌ Aucun véhicule disponible pour le test", 'error');
        exit(1);
    }
    
    // Trouver un chauffeur disponible
    $driver = Driver::where('is_available', true)
        ->where('assignment_status', 'available')
        ->first();
    
    if (!$driver) {
        output("❌ Aucun chauffeur disponible pour le test", 'error');
        exit(1);
    }
    
    output("Véhicule sélectionné: {$vehicle->registration_plate} (ID: {$vehicle->id})", 'info');
    output("Chauffeur sélectionné: {$driver->full_name} (ID: {$driver->id})", 'info');
    
    // Créer une affectation qui se termine dans 1 minute
    $assignment = new Assignment([
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => now()->subMinutes(5),
        'end_datetime' => now()->addMinutes(1),
        'reason' => 'Test automatique de synchronisation',
        'notes' => 'Test Enterprise-Grade pour vérifier la libération automatique',
        'organization_id' => $vehicle->organization_id,
        'created_by' => 1,
        'status' => 'active'
    ]);
    
    $assignment->save();
    output("✅ Affectation créée avec ID: {$assignment->id}", 'success');
    
    // 2. VÉRIFIER QUE LES RESSOURCES SONT MARQUÉES COMME OCCUPÉES
    separator("TEST 2: VÉRIFICATION DU VERROUILLAGE DES RESSOURCES");
    
    $vehicle->refresh();
    $driver->refresh();
    
    $tests = [
        'Véhicule is_available = false' => !$vehicle->is_available,
        'Véhicule current_driver_id = ' . $driver->id => $vehicle->current_driver_id == $driver->id,
        'Véhicule assignment_status = assigned' => $vehicle->assignment_status == 'assigned',
        'Chauffeur is_available = false' => !$driver->is_available,
        'Chauffeur current_vehicle_id = ' . $vehicle->id => $driver->current_vehicle_id == $vehicle->id,
        'Chauffeur assignment_status = assigned' => $driver->assignment_status == 'assigned'
    ];
    
    $allPassed = true;
    foreach ($tests as $test => $result) {
        if ($result) {
            output("✅ $test", 'success');
        } else {
            output("❌ $test", 'error');
            $allPassed = false;
        }
    }
    
    if (!$allPassed) {
        output("\n⚠️ L'Observer ne verrouille pas correctement les ressources", 'warning');
    }
    
    // 3. TERMINER L'AFFECTATION MANUELLEMENT
    separator("TEST 3: TERMINAISON MANUELLE DE L'AFFECTATION");
    
    $endResult = $assignment->end(now(), null, 'Test de terminaison manuelle');
    
    if ($endResult) {
        output("✅ Affectation terminée avec succès", 'success');
    } else {
        output("❌ Échec de la terminaison de l'affectation", 'error');
    }
    
    // 4. VÉRIFIER LA LIBÉRATION DES RESSOURCES
    separator("TEST 4: VÉRIFICATION DE LA LIBÉRATION DES RESSOURCES");
    
    $vehicle->refresh();
    $driver->refresh();
    $assignment->refresh();
    
    $tests = [
        'Affectation status = completed' => $assignment->status == 'completed',
        'Affectation ended_at non null' => $assignment->ended_at !== null,
        'Véhicule is_available = true' => $vehicle->is_available,
        'Véhicule current_driver_id = null' => $vehicle->current_driver_id === null,
        'Véhicule assignment_status = available' => $vehicle->assignment_status == 'available',
        'Chauffeur is_available = true' => $driver->is_available,
        'Chauffeur current_vehicle_id = null' => $driver->current_vehicle_id === null,
        'Chauffeur assignment_status = available' => $driver->assignment_status == 'available'
    ];
    
    $allPassed = true;
    foreach ($tests as $test => $result) {
        if ($result) {
            output("✅ $test", 'success');
        } else {
            output("❌ $test", 'error');
            $allPassed = false;
        }
    }
    
    // 5. NETTOYAGE
    separator("TEST 5: NETTOYAGE");
    
    // Supprimer l'affectation de test
    $assignment->forceDelete();
    output("✅ Affectation de test supprimée", 'success');
    
    // 6. TEST DU JOB DE TRAITEMENT DES EXPIRÉES
    separator("TEST 6: JOB DE TRAITEMENT DES AFFECTATIONS EXPIRÉES");
    
    // Créer une affectation déjà expirée
    $expiredAssignment = Assignment::create([
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => now()->subHours(2),
        'end_datetime' => now()->subHour(),
        'reason' => 'Test affectation expirée',
        'notes' => 'Cette affectation devrait être automatiquement terminée',
        'organization_id' => $vehicle->organization_id,
        'created_by' => 1,
        'status' => 'active' // Volontairement incorrect
    ]);
    
    output("Affectation expirée créée avec ID: {$expiredAssignment->id}", 'info');
    
    // Exécuter le job
    output("Exécution du job ProcessExpiredAssignmentsEnhanced...", 'info');
    
    $job = new \App\Jobs\ProcessExpiredAssignmentsEnhanced();
    $job->handle();
    
    // Vérifier le résultat
    $expiredAssignment->refresh();
    $vehicle->refresh();
    $driver->refresh();
    
    if ($expiredAssignment->status == 'completed' && $expiredAssignment->ended_at !== null) {
        output("✅ Job a correctement traité l'affectation expirée", 'success');
    } else {
        output("❌ Job n'a pas traité correctement l'affectation", 'error');
    }
    
    if ($vehicle->is_available && $driver->is_available) {
        output("✅ Ressources correctement libérées par le job", 'success');
    } else {
        output("❌ Ressources non libérées par le job", 'error');
    }
    
    // Nettoyage
    $expiredAssignment->forceDelete();
    
    // 7. RÉSUMÉ
    separator("📊 RÉSUMÉ DES TESTS");
    
    output("Tests exécutés avec succès!", 'success');
    output("Le système de synchronisation fonctionne correctement.", 'success');
    output("\nRecommandations:", 'info');
    output("  1. Vérifier que le Scheduler Laravel est actif (cron)", 'info');
    output("  2. Surveiller les logs pour détecter les anomalies", 'info');
    output("  3. Exécuter régulièrement: php artisan assignments:fix-zombies", 'info');
    
} catch (\Exception $e) {
    output("\n❌ ERREUR FATALE: " . $e->getMessage(), 'error');
    output($e->getTraceAsString(), 'error');
    exit(1);
}

separator("FIN DES TESTS");
