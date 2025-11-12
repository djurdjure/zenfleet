<?php

/**
 * 🚗 Test d'affichage de la liste des véhicules - Enterprise Ultra-Pro
 * 
 * Ce script teste l'affichage des véhicules avec leurs chauffeurs assignés
 * et vérifie que toutes les données sont correctement récupérées.
 * 
 * @version 1.0-Ultra-Pro
 * @date 2025-11-11
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Vehicle;
use App\Models\Assignment;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║     🚗 TEST AFFICHAGE VÉHICULES - ENTERPRISE ULTRA-PRO 🚗            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // 1. Récupérer les véhicules avec leurs affectations actives
    echo "📊 Analyse des véhicules et affectations...\n";
    echo "─────────────────────────────────────────\n";
    
    $vehicles = Vehicle::with([
        'vehicleType',
        'depot',
        'vehicleStatus',
        'assignments' => function ($query) {
            $query->where('status', 'active')
                  ->where('start_datetime', '<=', now())
                  ->where(function($q) {
                      $q->whereNull('end_datetime')
                        ->orWhere('end_datetime', '>=', now());
                  })
                  ->with('driver.user')
                  ->limit(1);
        }
    ])
    ->where('is_archived', false)
    ->limit(10)
    ->get();
    
    echo "✅ Nombre de véhicules récupérés: " . $vehicles->count() . "\n\n";
    
    // 2. Afficher les détails de chaque véhicule
    foreach ($vehicles as $vehicle) {
        echo "🚙 VÉHICULE: {$vehicle->registration_plate}\n";
        echo "   Marque/Modèle: {$vehicle->brand} {$vehicle->model}\n";
        echo "   Type: " . ($vehicle->vehicleType ? $vehicle->vehicleType->name : 'N/A') . "\n";
        echo "   Dépôt: " . ($vehicle->depot ? $vehicle->depot->name : 'Non assigné') . "\n";
        echo "   Kilométrage: " . number_format($vehicle->current_mileage, 0, ',', ' ') . " km\n";
        
        // Vérifier l'affectation active
        $activeAssignment = $vehicle->assignments ? $vehicle->assignments->first() : null;
        
        if ($activeAssignment) {
            $driver = $activeAssignment->driver;
            $user = $driver ? $driver->user : null;
            
            if ($user) {
                echo "   👤 CHAUFFEUR ASSIGNÉ:\n";
                echo "      - Nom: {$user->name} " . ($user->last_name ?? '') . "\n";
                echo "      - Email: {$user->email}\n";
                echo "      - Téléphone: " . ($driver->personal_phone ?? $user->phone ?? 'N/A') . "\n";
                echo "      - Date début affectation: " . $activeAssignment->start_datetime->format('d/m/Y H:i') . "\n";
                if ($activeAssignment->end_datetime) {
                    echo "      - Date fin prévue: " . $activeAssignment->end_datetime->format('d/m/Y H:i') . "\n";
                } else {
                    echo "      - Date fin: Indéterminée\n";
                }
            } else {
                echo "   ⚠️ Driver trouvé mais pas d'utilisateur associé\n";
            }
        } else {
            echo "   ❌ Aucun chauffeur assigné\n";
        }
        
        echo "   ────────────────────────────────────\n";
    }
    
    // 3. Statistiques globales
    echo "\n📈 STATISTIQUES GLOBALES:\n";
    echo "─────────────────────────────────────────\n";
    
    $totalVehicles = Vehicle::where('is_archived', false)->count();
    $vehiclesWithDrivers = Vehicle::whereHas('assignments', function ($query) {
        $query->where('status', 'active')
              ->where('start_datetime', '<=', now())
              ->where(function($q) {
                  $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>=', now());
              });
    })->count();
    
    echo "Total véhicules actifs: {$totalVehicles}\n";
    echo "Véhicules avec chauffeur: {$vehiclesWithDrivers}\n";
    echo "Véhicules sans chauffeur: " . ($totalVehicles - $vehiclesWithDrivers) . "\n";
    echo "Taux d'affectation: " . ($totalVehicles > 0 ? round(($vehiclesWithDrivers / $totalVehicles) * 100, 1) : 0) . "%\n";
    
    // 4. Vérifier les problèmes potentiels
    echo "\n🔍 DIAGNOSTIC DES PROBLÈMES POTENTIELS:\n";
    echo "─────────────────────────────────────────\n";
    
    // Vérifier les assignments sans driver
    $assignmentsWithoutDriver = Assignment::whereNull('driver_id')
        ->where('status', 'active')
        ->count();
    
    if ($assignmentsWithoutDriver > 0) {
        echo "⚠️ {$assignmentsWithoutDriver} affectation(s) active(s) sans chauffeur!\n";
    }
    
    // Vérifier les drivers sans user
    $driversWithoutUser = Driver::whereNull('user_id')->count();
    
    if ($driversWithoutUser > 0) {
        echo "⚠️ {$driversWithoutUser} chauffeur(s) sans utilisateur associé!\n";
    }
    
    // Vérifier les véhicules avec plusieurs affectations actives (problème)
    $vehiclesWithMultipleAssignments = Vehicle::whereHas('assignments', function ($query) {
        $query->where('status', 'active')
              ->where('start_datetime', '<=', now())
              ->where(function($q) {
                  $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>=', now());
              });
    }, '>', 1)->count();
    
    if ($vehiclesWithMultipleAssignments > 0) {
        echo "⚠️ {$vehiclesWithMultipleAssignments} véhicule(s) avec plusieurs affectations actives (conflit)!\n";
    }
    
    echo "\n✅ Test terminé avec succès!\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
