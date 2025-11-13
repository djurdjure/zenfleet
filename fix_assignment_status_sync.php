#!/usr/bin/env php
<?php

/**
 * 🔧 SCRIPT DE CORRECTION ENTERPRISE-GRADE
 * 
 * Corrige la désynchronisation des status_id pour les véhicules et chauffeurs
 * après terminaison des affectations.
 * 
 * UTILISATION:
 * docker exec zenfleet_php php fix_assignment_status_sync.php [--dry-run]
 * 
 * @version 1.0.0
 * @date 2025-11-13
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Assignment;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Vérifier les arguments
$isDryRun = in_array('--dry-run', $argv);

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔧 CORRECTION DES STATUTS D'AFFECTATIONS - ZENFLEET        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
echo "Mode: " . ($isDryRun ? "🧪 DRY-RUN (simulation)\n" : "✅ PRODUCTION\n");
echo "─────────────────────────────────────────────────────────────\n\n";

try {
    // 1. Analyser l'état actuel
    echo "📊 ANALYSE DE L'ÉTAT ACTUEL\n";
    echo "─────────────────────────\n";
    
    // Véhicules avec incohérence
    $vehiclesWithWrongStatus = Vehicle::where('is_available', true)
        ->where('assignment_status', 'available')
        ->whereNull('current_driver_id')
        ->where('status_id', '!=', 8) // Devrait être Parking (8)
        ->get();
    
    echo "• Véhicules avec status_id incorrect: " . $vehiclesWithWrongStatus->count() . "\n";
    
    if ($vehiclesWithWrongStatus->count() > 0) {
        echo "  Détails:\n";
        foreach ($vehiclesWithWrongStatus as $vehicle) {
            $statusName = DB::table('vehicle_statuses')
                ->where('id', $vehicle->status_id)
                ->value('name') ?? 'N/A';
            echo "    - {$vehicle->registration_plate}: status_id={$vehicle->status_id} ({$statusName}) → Devrait être 8 (Parking)\n";
        }
    }
    
    // Chauffeurs avec incohérence
    $driversWithWrongStatus = Driver::where('is_available', true)
        ->where('assignment_status', 'available')
        ->whereNull('current_vehicle_id')
        ->whereNotIn('status_id', [1, 7]) // Devrait être Actif (1) ou Disponible (7)
        ->get();
    
    echo "\n• Chauffeurs avec status_id incorrect: " . $driversWithWrongStatus->count() . "\n";
    
    if ($driversWithWrongStatus->count() > 0) {
        echo "  Détails:\n";
        foreach ($driversWithWrongStatus as $driver) {
            $statusName = DB::table('driver_statuses')
                ->where('id', $driver->status_id)
                ->value('name') ?? 'N/A';
            echo "    - {$driver->first_name} {$driver->last_name}: status_id={$driver->status_id} ({$statusName}) → Devrait être 7 (Disponible)\n";
        }
    }
    
    if ($vehiclesWithWrongStatus->count() == 0 && $driversWithWrongStatus->count() == 0) {
        echo "\n✅ Aucune incohérence détectée ! Le système est sain.\n";
        exit(0);
    }
    
    echo "\n";
    
    // 2. Demander confirmation si pas en dry-run
    if (!$isDryRun) {
        echo "⚠️  ATTENTION: Cette opération va modifier la base de données.\n";
        echo "Voulez-vous continuer? (yes/no) [no]: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) != 'yes') {
            echo "❌ Opération annulée.\n";
            exit(0);
        }
        fclose($handle);
    }
    
    // 3. Appliquer les corrections
    echo "\n🔧 APPLICATION DES CORRECTIONS\n";
    echo "─────────────────────────────\n";
    
    DB::transaction(function () use ($isDryRun, $vehiclesWithWrongStatus, $driversWithWrongStatus) {
        
        // Corriger les véhicules
        if ($vehiclesWithWrongStatus->count() > 0) {
            echo "• Correction des véhicules...\n";
            
            foreach ($vehiclesWithWrongStatus as $vehicle) {
                if (!$isDryRun) {
                    $vehicle->update(['status_id' => 8]); // Parking
                    echo "  ✅ {$vehicle->registration_plate}: status_id mis à jour → 8 (Parking)\n";
                } else {
                    echo "  [DRY-RUN] {$vehicle->registration_plate}: status_id serait mis à jour → 8 (Parking)\n";
                }
            }
        }
        
        // Corriger les chauffeurs
        if ($driversWithWrongStatus->count() > 0) {
            echo "\n• Correction des chauffeurs...\n";
            
            foreach ($driversWithWrongStatus as $driver) {
                if (!$isDryRun) {
                    $driver->update(['status_id' => 7]); // Disponible
                    echo "  ✅ {$driver->first_name} {$driver->last_name}: status_id mis à jour → 7 (Disponible)\n";
                } else {
                    echo "  [DRY-RUN] {$driver->first_name} {$driver->last_name}: status_id serait mis à jour → 7 (Disponible)\n";
                }
            }
        }
        
        if ($isDryRun) {
            // En dry-run, rollback la transaction
            throw new \Exception("DRY-RUN: Rollback de la transaction");
        }
    });
    
} catch (\Exception $e) {
    if (!$isDryRun || !str_contains($e->getMessage(), 'DRY-RUN')) {
        echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// 4. Vérification finale
echo "\n📊 VÉRIFICATION FINALE\n";
echo "───────────────────\n";

$remainingVehicleIssues = Vehicle::where('is_available', true)
    ->where('assignment_status', 'available')
    ->whereNull('current_driver_id')
    ->where('status_id', '!=', 8)
    ->count();

$remainingDriverIssues = Driver::where('is_available', true)
    ->where('assignment_status', 'available')
    ->whereNull('current_vehicle_id')
    ->whereNotIn('status_id', [1, 7])
    ->count();

if (!$isDryRun) {
    echo "• Véhicules avec incohérence restante: {$remainingVehicleIssues}\n";
    echo "• Chauffeurs avec incohérence restante: {$remainingDriverIssues}\n";
    
    if ($remainingVehicleIssues == 0 && $remainingDriverIssues == 0) {
        echo "\n🎉 SUCCÈS: Toutes les incohérences ont été corrigées !\n";
        
        // Afficher les ressources maintenant disponibles
        $availableVehicles = Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->where('status_id', 8)
            ->count();
            
        $availableDrivers = Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereIn('status_id', [1, 7])
            ->count();
            
        echo "\n📈 RESSOURCES DISPONIBLES:\n";
        echo "• Véhicules disponibles: {$availableVehicles}\n";
        echo "• Chauffeurs disponibles: {$availableDrivers}\n";
    } else {
        echo "\n⚠️  Certaines incohérences persistent. Veuillez vérifier manuellement.\n";
    }
} else {
    echo "• Mode DRY-RUN: Aucune modification effectuée\n";
    echo "\n💡 Pour appliquer les corrections, relancez sans --dry-run\n";
}

echo "\n✅ Script terminé.\n";
