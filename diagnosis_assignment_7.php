<?php

/**
 * 🔍 SCRIPT DE DIAGNOSTIC ENTERPRISE-GRADE ULTRA-PRO
 * 
 * Analyse complète de l'affectation #7 et détection des anomalies
 * de synchronisation des statuts véhicule/chauffeur
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
use Illuminate\Support\Facades\Log;

// Configuration de l'output en couleur
function output($message, $type = 'info') {
    $colors = [
        'info' => "\033[0;36m",     // Cyan
        'success' => "\033[0;32m",  // Vert
        'warning' => "\033[0;33m",  // Jaune
        'error' => "\033[0;31m",    // Rouge
        'header' => "\033[1;34m",   // Bleu gras
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

// Début du diagnostic
separator("🔬 DIAGNOSTIC ASSIGNMENT #7 - ENTERPRISE GRADE");

try {
    // 1. RÉCUPÉRATION DE L'AFFECTATION
    output("📋 Récupération de l'affectation #7...", 'info');
    
    $assignment = Assignment::with(['vehicle', 'driver'])->find(7);
    
    if (!$assignment) {
        output("❌ Affectation #7 introuvable!", 'error');
        exit(1);
    }
    
    output("✅ Affectation trouvée", 'success');
    
    // 2. ANALYSE DES DONNÉES DE L'AFFECTATION
    separator("📊 DONNÉES DE L'AFFECTATION");
    
    $assignmentData = [
        'ID' => $assignment->id,
        'Véhicule' => $assignment->vehicle ? 
            $assignment->vehicle->registration_plate . ' (ID: ' . $assignment->vehicle_id . ')' : 
            'Aucun',
        'Chauffeur' => $assignment->driver ? 
            $assignment->driver->full_name . ' (ID: ' . $assignment->driver_id . ')' : 
            'Aucun',
        'Date début' => $assignment->start_datetime ? $assignment->start_datetime->format('d/m/Y H:i:s') : 'NULL',
        'Date fin' => $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y H:i:s') : 'NULL',
        'Ended at' => $assignment->ended_at ? (is_string($assignment->ended_at) ? $assignment->ended_at : $assignment->ended_at->format('d/m/Y H:i:s')) : 'NULL',
        'Status (DB)' => $assignment->getAttributes()['status'] ?? 'NULL',
        'Status (calculé)' => $assignment->calculateStatus(),
        'Created by' => $assignment->created_by,
        'Updated by' => $assignment->updated_by,
        'Ended by' => $assignment->ended_by_user_id ?? 'NULL',
    ];
    
    foreach ($assignmentData as $label => $value) {
        $type = strpos($label, 'NULL') !== false ? 'warning' : 'info';
        output(sprintf("  %-20s : %s", $label, $value), $type);
    }
    
    // 3. DÉTECTION DES ANOMALIES
    separator("⚠️ DÉTECTION DES ANOMALIES");
    
    $anomalies = [];
    
    // Vérification du statut
    $calculatedStatus = $assignment->calculateStatus();
    $storedStatus = $assignment->getAttributes()['status'] ?? null;
    
    if ($storedStatus !== $calculatedStatus) {
        $anomalies[] = "❗ Incohérence de statut: DB='$storedStatus', Calculé='$calculatedStatus'";
    }
    
    // Vérification si terminée mais pas de ended_at
    if ($calculatedStatus === 'completed' && !$assignment->ended_at) {
        $anomalies[] = "❗ Affectation terminée mais ended_at est NULL";
    }
    
    // Vérification si end_datetime passée mais status != completed
    if ($assignment->end_datetime && $assignment->end_datetime <= now() && $storedStatus !== 'completed') {
        $anomalies[] = "❗ Date de fin passée mais statut != 'completed'";
    }
    
    if (empty($anomalies)) {
        output("✅ Aucune anomalie détectée dans l'affectation", 'success');
    } else {
        foreach ($anomalies as $anomaly) {
            output($anomaly, 'error');
        }
    }
    
    // 4. ANALYSE DU VÉHICULE
    separator("🚗 ANALYSE DU VÉHICULE");
    
    if ($assignment->vehicle) {
        $vehicle = $assignment->vehicle;
        
        // Récupération directe depuis la DB pour avoir les vraies valeurs
        $vehicleRaw = DB::table('vehicles')->where('id', $vehicle->id)->first();
        
        $vehicleData = [
            'ID' => $vehicle->id,
            'Immatriculation' => $vehicle->registration_plate,
            'is_available (DB)' => $vehicleRaw->is_available ? 'OUI' : 'NON',
            'current_driver_id (DB)' => $vehicleRaw->current_driver_id ?? 'NULL',
            'assignment_status (DB)' => $vehicleRaw->assignment_status ?? 'NULL',
            'last_assignment_end' => $vehicleRaw->last_assignment_end ?? 'NULL',
        ];
        
        foreach ($vehicleData as $label => $value) {
            $type = 'info';
            if ($label === 'is_available (DB)' && $value === 'NON' && $calculatedStatus === 'completed') {
                $type = 'error';
            }
            if ($label === 'current_driver_id (DB)' && $value !== 'NULL' && $calculatedStatus === 'completed') {
                $type = 'error';
            }
            output(sprintf("  %-25s : %s", $label, $value), $type);
        }
        
        // Vérifier autres affectations actives pour ce véhicule
        output("\n  🔍 Recherche d'autres affectations actives pour ce véhicule...", 'info');
        
        $activeAssignments = Assignment::where('vehicle_id', $vehicle->id)
            ->where('id', '!=', 7)
            ->where(function($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->get();
        
        if ($activeAssignments->count() > 0) {
            output("  ⚠️ Autres affectations actives trouvées: " . $activeAssignments->count(), 'warning');
            foreach ($activeAssignments as $aa) {
                output("    - Affectation #{$aa->id}: " . 
                    $aa->start_datetime->format('d/m/Y H:i') . 
                    " → " . 
                    ($aa->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé'), 'warning');
            }
        } else {
            output("  ✅ Aucune autre affectation active", 'success');
            
            if (!$vehicleRaw->is_available) {
                $anomalies[] = "❗ PROBLÈME: Le véhicule devrait être disponible mais is_available=false";
            }
            if ($vehicleRaw->current_driver_id) {
                $anomalies[] = "❗ PROBLÈME: Le véhicule devrait être libre mais current_driver_id=" . $vehicleRaw->current_driver_id;
            }
        }
    }
    
    // 5. ANALYSE DU CHAUFFEUR
    separator("👤 ANALYSE DU CHAUFFEUR");
    
    if ($assignment->driver) {
        $driver = $assignment->driver;
        
        // Récupération directe depuis la DB pour avoir les vraies valeurs
        $driverRaw = DB::table('drivers')->where('id', $driver->id)->first();
        
        $driverData = [
            'ID' => $driver->id,
            'Nom complet' => $driver->full_name,
            'is_available (DB)' => $driverRaw->is_available ? 'OUI' : 'NON',
            'current_vehicle_id (DB)' => $driverRaw->current_vehicle_id ?? 'NULL',
            'assignment_status (DB)' => $driverRaw->assignment_status ?? 'NULL',
            'last_assignment_end' => $driverRaw->last_assignment_end ?? 'NULL',
        ];
        
        foreach ($driverData as $label => $value) {
            $type = 'info';
            if ($label === 'is_available (DB)' && $value === 'NON' && $calculatedStatus === 'completed') {
                $type = 'error';
            }
            if ($label === 'current_vehicle_id (DB)' && $value !== 'NULL' && $calculatedStatus === 'completed') {
                $type = 'error';
            }
            output(sprintf("  %-25s : %s", $label, $value), $type);
        }
        
        // Vérifier autres affectations actives pour ce chauffeur
        output("\n  🔍 Recherche d'autres affectations actives pour ce chauffeur...", 'info');
        
        $activeAssignments = Assignment::where('driver_id', $driver->id)
            ->where('id', '!=', 7)
            ->where(function($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->get();
        
        if ($activeAssignments->count() > 0) {
            output("  ⚠️ Autres affectations actives trouvées: " . $activeAssignments->count(), 'warning');
            foreach ($activeAssignments as $aa) {
                output("    - Affectation #{$aa->id}: " . 
                    $aa->start_datetime->format('d/m/Y H:i') . 
                    " → " . 
                    ($aa->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé'), 'warning');
            }
        } else {
            output("  ✅ Aucune autre affectation active", 'success');
            
            if (!$driverRaw->is_available) {
                $anomalies[] = "❗ PROBLÈME: Le chauffeur devrait être disponible mais is_available=false";
            }
            if ($driverRaw->current_vehicle_id) {
                $anomalies[] = "❗ PROBLÈME: Le chauffeur devrait être libre mais current_vehicle_id=" . $driverRaw->current_vehicle_id;
            }
        }
    }
    
    // 6. RÉSUMÉ DU DIAGNOSTIC
    separator("📝 RÉSUMÉ DU DIAGNOSTIC");
    
    if (empty($anomalies)) {
        output("✅ SYSTÈME OK - Aucun problème détecté", 'success');
    } else {
        output("❌ PROBLÈMES DÉTECTÉS:", 'error');
        foreach ($anomalies as $anomaly) {
            output($anomaly, 'error');
        }
        
        // 7. PROPOSITION DE CORRECTION
        separator("🔧 CORRECTION AUTOMATIQUE PROPOSÉE");
        
        output("Voulez-vous appliquer les corrections automatiques? (yes/no) [no]: ", 'warning');
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        
        if (trim($line) === 'yes') {
            output("\n🚀 Application des corrections...", 'info');
            
            DB::beginTransaction();
            try {
                // Correction du statut de l'affectation
                if ($storedStatus !== $calculatedStatus) {
                    DB::table('assignments')
                        ->where('id', 7)
                        ->update([
                            'status' => $calculatedStatus,
                            'updated_at' => now()
                        ]);
                    output("  ✅ Statut de l'affectation corrigé: $calculatedStatus", 'success');
                }
                
                // Si l'affectation est terminée et qu'il n'y a pas d'autres affectations actives
                if ($calculatedStatus === 'completed') {
                    // Correction de ended_at si nécessaire
                    if (!$assignment->ended_at) {
                        DB::table('assignments')
                            ->where('id', 7)
                            ->update([
                                'ended_at' => $assignment->end_datetime ?? now(),
                                'updated_at' => now()
                            ]);
                        output("  ✅ ended_at mis à jour", 'success');
                    }
                    
                    // Libération du véhicule
                    if ($vehicle && !$vehicleRaw->is_available) {
                        // Vérifier qu'il n'y a pas d'autre affectation active
                        $hasOtherActive = Assignment::where('vehicle_id', $vehicle->id)
                            ->where('id', '!=', 7)
                            ->where(function($q) {
                                $q->whereNull('end_datetime')
                                  ->orWhere('end_datetime', '>', now());
                            })
                            ->where('start_datetime', '<=', now())
                            ->exists();
                        
                        if (!$hasOtherActive) {
                            DB::table('vehicles')
                                ->where('id', $vehicle->id)
                                ->update([
                                    'is_available' => true,
                                    'current_driver_id' => null,
                                    'assignment_status' => 'available',
                                    'last_assignment_end' => $assignment->end_datetime ?? now(),
                                    'updated_at' => now()
                                ]);
                            output("  ✅ Véhicule libéré", 'success');
                        }
                    }
                    
                    // Libération du chauffeur
                    if ($driver && !$driverRaw->is_available) {
                        // Vérifier qu'il n'y a pas d'autre affectation active
                        $hasOtherActive = Assignment::where('driver_id', $driver->id)
                            ->where('id', '!=', 7)
                            ->where(function($q) {
                                $q->whereNull('end_datetime')
                                  ->orWhere('end_datetime', '>', now());
                            })
                            ->where('start_datetime', '<=', now())
                            ->exists();
                        
                        if (!$hasOtherActive) {
                            DB::table('drivers')
                                ->where('id', $driver->id)
                                ->update([
                                    'is_available' => true,
                                    'current_vehicle_id' => null,
                                    'assignment_status' => 'available',
                                    'last_assignment_end' => $assignment->end_datetime ?? now(),
                                    'updated_at' => now()
                                ]);
                            output("  ✅ Chauffeur libéré", 'success');
                        }
                    }
                }
                
                DB::commit();
                output("\n✅ CORRECTIONS APPLIQUÉES AVEC SUCCÈS!", 'success');
                
                // Log pour audit trail
                Log::info('[DIAGNOSTIC] Corrections appliquées sur affectation #7', [
                    'assignment_id' => 7,
                    'corrections' => $anomalies,
                    'executed_by' => 'diagnostic_script',
                    'timestamp' => now()
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                output("\n❌ ERREUR lors de l'application des corrections: " . $e->getMessage(), 'error');
            }
        } else {
            output("\n⚠️ Corrections non appliquées. Pour corriger manuellement:", 'warning');
            output("  1. Mettre à jour le statut de l'affectation", 'info');
            output("  2. Libérer le véhicule si nécessaire", 'info');
            output("  3. Libérer le chauffeur si nécessaire", 'info');
        }
    }
    
    // 8. RECOMMANDATIONS
    separator("💡 RECOMMANDATIONS ENTERPRISE-GRADE");
    
    output("Pour éviter ce problème à l'avenir:", 'info');
    output("  1. Exécuter régulièrement: php artisan assignments:sync-statuses", 'info');
    output("  2. Activer le job ProcessExpiredAssignments dans le scheduler", 'info');
    output("  3. Vérifier que l'Observer AssignmentObserver est bien actif", 'info');
    output("  4. Monitorer les logs pour détecter les zombies", 'info');
    
} catch (\Exception $e) {
    output("\n❌ ERREUR FATALE: " . $e->getMessage(), 'error');
    output($e->getTraceAsString(), 'error');
    exit(1);
}

separator("FIN DU DIAGNOSTIC");
