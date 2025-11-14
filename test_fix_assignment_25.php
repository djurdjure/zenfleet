<?php

/**
 * 🧪 TEST DE CORRECTION : AFFECTATION ID 25
 *
 * Ce script teste la correction de l'affectation zombie ID 25
 * en utilisant le nouveau AssignmentTerminationService.
 *
 * UTILISATION :
 * php test_fix_assignment_25.php
 *
 * @version 1.0.0
 * @date 2025-11-14
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assignment;
use App\Services\AssignmentTerminationService;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🧪 TEST DE CORRECTION : AFFECTATION ID 25\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

try {
    $assignment = Assignment::with(['vehicle', 'driver'])->find(25);

    if (!$assignment) {
        echo "❌ Affectation ID 25 non trouvée\n";
        exit(1);
    }

    echo "📋 ÉTAT INITIAL DE L'AFFECTATION\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "Assignment:\n";
    echo "  ID: {$assignment->id}\n";
    echo "  Status: {$assignment->status}\n";
    echo "  Start: {$assignment->start_datetime->format('Y-m-d H:i:s')}\n";
    echo "  End: " . ($assignment->end_datetime ? $assignment->end_datetime->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "  ended_at: " . ($assignment->ended_at ? $assignment->ended_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "  canBeEnded(): " . ($assignment->canBeEnded() ? 'TRUE' : 'FALSE') . "\n";
    echo "\n";

    if ($assignment->vehicle) {
        echo "Véhicule {$assignment->vehicle->registration_plate} (ID {$assignment->vehicle_id}):\n";
        echo "  is_available: " . ($assignment->vehicle->is_available ? 'true' : 'false') . "\n";
        echo "  assignment_status: {$assignment->vehicle->assignment_status}\n";
        echo "  status_id: {$assignment->vehicle->status_id}\n";
        echo "  current_driver_id: " . ($assignment->vehicle->current_driver_id ?? 'NULL') . "\n";
    } else {
        echo "⚠️ Aucun véhicule associé\n";
    }
    echo "\n";

    if ($assignment->driver) {
        echo "Chauffeur {$assignment->driver->first_name} {$assignment->driver->last_name} (ID {$assignment->driver_id}):\n";
        echo "  is_available: " . ($assignment->driver->is_available ? 'true' : 'false') . "\n";
        echo "  assignment_status: {$assignment->driver->assignment_status}\n";
        echo "  status_id: {$assignment->driver->status_id}\n";
        echo "  current_vehicle_id: " . ($assignment->driver->current_vehicle_id ?? 'NULL') . "\n";
    } else {
        echo "⚠️ Aucun chauffeur associé\n";
    }
    echo "\n";

    // Détecter le type de problème
    $isZombie = false;
    $problemType = '';

    if ($assignment->status === 'active' &&
        $assignment->vehicle &&
        $assignment->vehicle->is_available === true &&
        $assignment->vehicle->assignment_status === 'available') {
        $isZombie = true;
        $problemType = 'ZOMBIE (affectation active mais ressources libérées)';
    } elseif ($assignment->status === 'active' && !$assignment->ended_at) {
        $problemType = 'ACTIVE (affectation en cours)';
    } elseif ($assignment->ended_at) {
        $problemType = 'TERMINÉE (affectation déjà terminée)';
    }

    echo "🔍 DIAGNOSTIC\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "Type de problème: {$problemType}\n";
    echo "Est un zombie: " . ($isZombie ? 'OUI' : 'NON') . "\n";
    echo "\n";

    if ($isZombie) {
        echo "⚠️ ZOMBIE DÉTECTÉ - Correction nécessaire\n";
        echo "\n";
        echo "🔧 APPLICATION DE LA CORRECTION\n";
        echo "─────────────────────────────────────────────────────────────\n";

        // Utiliser le service de terminaison
        $service = app(AssignmentTerminationService::class);

        echo "Méthode 1: Utilisation de AssignmentTerminationService::terminateAssignment()\n";

        try {
            $result = $service->terminateAssignment($assignment);

            echo "✅ Terminaison réussie\n";
            echo "Actions effectuées:\n";
            foreach ($result['actions'] as $action) {
                echo "  - {$action}\n";
            }
            echo "\n";
        } catch (\Exception $e) {
            echo "❌ Erreur lors de la terminaison: {$e->getMessage()}\n";
            echo "\n";

            echo "Tentative de correction avec forceReleaseResources()...\n";
            $result = $service->forceReleaseResources($assignment);
            echo "✅ Force release réussie\n";
            echo "Actions effectuées:\n";
            foreach ($result['actions'] as $action) {
                echo "  - {$action}\n";
            }
            echo "\n";
        }
    } elseif ($assignment->status === 'active' && !$assignment->ended_at) {
        echo "💡 Affectation active normale - Terminaison possible\n";
        echo "\n";

        // Demander confirmation (simulation)
        echo "Voulez-vous terminer cette affectation ? (Simulation: OUI)\n";
        echo "\n";
        echo "🔧 TERMINAISON DE L'AFFECTATION\n";
        echo "─────────────────────────────────────────────────────────────\n";

        // Utiliser la méthode Assignment::end() qui utilise maintenant le service
        $success = $assignment->end();

        if ($success) {
            echo "✅ Affectation terminée avec succès via Assignment::end()\n";
        } else {
            echo "❌ Échec de la terminaison\n";
        }
        echo "\n";
    } else {
        echo "✅ Aucune action nécessaire - L'affectation est déjà terminée\n";
        echo "\n";
    }

    // Vérification finale
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "📊 ÉTAT FINAL\n";
    echo "═══════════════════════════════════════════════════════════════\n";

    $assignment->refresh();
    if ($assignment->vehicle) {
        $assignment->vehicle->refresh();
    }
    if ($assignment->driver) {
        $assignment->driver->refresh();
    }

    echo "Assignment:\n";
    echo "  Status: {$assignment->status}\n";
    echo "  ended_at: " . ($assignment->ended_at ? $assignment->ended_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "\n";

    if ($assignment->vehicle) {
        echo "Véhicule {$assignment->vehicle->registration_plate}:\n";
        echo "  is_available: " . ($assignment->vehicle->is_available ? 'true' : 'false') . "\n";
        echo "  assignment_status: {$assignment->vehicle->assignment_status}\n";
        echo "  status_id: {$assignment->vehicle->status_id}\n";
        echo "  current_driver_id: " . ($assignment->vehicle->current_driver_id ?? 'NULL') . "\n";
    }
    echo "\n";

    if ($assignment->driver) {
        echo "Chauffeur {$assignment->driver->first_name} {$assignment->driver->last_name}:\n";
        echo "  is_available: " . ($assignment->driver->is_available ? 'true' : 'false') . "\n";
        echo "  assignment_status: {$assignment->driver->assignment_status}\n";
        echo "  status_id: {$assignment->driver->status_id}\n";
        echo "  current_vehicle_id: " . ($assignment->driver->current_vehicle_id ?? 'NULL') . "\n";
    }
    echo "\n";

    // Vérification de cohérence finale
    $isFinallyConsistent = true;
    $inconsistencies = [];

    if ($assignment->ended_at) {
        // Affectation terminée, les ressources doivent être libérées
        if ($assignment->vehicle &&
            ($assignment->vehicle->is_available !== true ||
             $assignment->vehicle->assignment_status !== 'available' ||
             $assignment->vehicle->status_id !== 8)) {
            $isFinallyConsistent = false;
            $inconsistencies[] = "Véhicule non libéré correctement";
        }

        if ($assignment->driver &&
            ($assignment->driver->is_available !== true ||
             $assignment->driver->assignment_status !== 'available' ||
             $assignment->driver->status_id !== 7)) {
            $isFinallyConsistent = false;
            $inconsistencies[] = "Chauffeur non libéré correctement";
        }
    }

    echo "═══════════════════════════════════════════════════════════════\n";
    echo "🎯 RÉSULTAT FINAL\n";
    echo "═══════════════════════════════════════════════════════════════\n";

    if ($isFinallyConsistent) {
        echo "✅ SYSTÈME COHÉRENT\n";
        echo "Toutes les ressources sont dans un état cohérent.\n";
        echo "La correction a été appliquée avec succès.\n";
    } else {
        echo "❌ INCOHÉRENCES RESTANTES\n";
        foreach ($inconsistencies as $inconsistency) {
            echo "  - {$inconsistency}\n";
        }
    }
    echo "\n";

    Log::info('[TEST_FIX_25] Test de correction terminé', [
        'assignment_id' => 25,
        'consistent' => $isFinallyConsistent,
        'inconsistencies' => $inconsistencies,
    ]);

    exit($isFinallyConsistent ? 0 : 1);

} catch (\Exception $e) {
    echo "\n";
    echo "❌ ERREUR LORS DU TEST\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nTrace:\n{$e->getTraceAsString()}\n";
    echo "\n";

    Log::error('[TEST_FIX_25] Erreur lors du test', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    exit(1);
}
