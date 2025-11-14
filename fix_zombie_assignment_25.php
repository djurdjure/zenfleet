<?php

/**
 * 🔧 CORRECTION IMMÉDIATE : AFFECTATION ZOMBIE ID 25
 *
 * Ce script corrige l'affectation ID 25 qui est dans un état incohérent :
 * - Status 'active' mais ressources marquées 'available'
 *
 * UTILISATION :
 * php fix_zombie_assignment_25.php
 *
 * @version 1.0.0
 * @date 2025-11-14
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assignment;
use App\Services\ResourceStatusSynchronizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🔧 CORRECTION AFFECTATION ZOMBIE ID 25\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

try {
    $assignment = Assignment::find(25);

    if (!$assignment) {
        echo "❌ Affectation ID 25 non trouvée\n";
        exit(1);
    }

    echo "📋 État actuel de l'affectation:\n";
    echo "   ID: {$assignment->id}\n";
    echo "   Status: {$assignment->status}\n";
    echo "   Start: {$assignment->start_datetime->format('Y-m-d H:i:s')}\n";
    echo "   End: " . ($assignment->end_datetime ? $assignment->end_datetime->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "   ended_at: " . ($assignment->ended_at ? $assignment->ended_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "   Vehicle: {$assignment->vehicle->registration_plate}\n";
    echo "   Driver: {$assignment->driver->first_name} {$assignment->driver->last_name}\n";
    echo "\n";

    echo "🔍 Détection du problème:\n";
    echo "   canBeEnded(): " . ($assignment->canBeEnded() ? 'TRUE' : 'FALSE') . "\n";
    echo "   is_ongoing: " . ($assignment->is_ongoing ? 'TRUE' : 'FALSE') . "\n";
    echo "\n";

    // Vérifier l'incohérence
    $vehicleInconsistent = $assignment->vehicle->is_available === true && $assignment->vehicle->assignment_status === 'available';
    $driverInconsistent = $assignment->driver->is_available === true && $assignment->driver->assignment_status === 'available';

    if ($vehicleInconsistent || $driverInconsistent) {
        echo "⚠️ INCOHÉRENCE DÉTECTÉE:\n";
        if ($vehicleInconsistent) {
            echo "   - Véhicule marqué 'available' mais affectation 'active'\n";
        }
        if ($driverInconsistent) {
            echo "   - Chauffeur marqué 'available' mais affectation 'active'\n";
        }
        echo "\n";

        echo "🔧 OPTIONS DE CORRECTION:\n";
        echo "   1. Terminer l'affectation maintenant\n";
        echo "   2. Verrouiller les ressources (si l'affectation doit rester active)\n";
        echo "\n";

        // Décision : Si start_datetime est dans le passé depuis plus de 24h, terminer
        $hoursSinceStart = now()->diffInHours($assignment->start_datetime, false);

        if ($hoursSinceStart > 24) {
            echo "💡 Décision: Terminer l'affectation (démarrée il y a " . abs($hoursSinceStart) . " heures)\n";
            echo "\n";

            DB::transaction(function () use ($assignment) {
                // Terminer l'affectation
                $assignment->end_datetime = now();
                $assignment->ended_at = now();
                $assignment->ended_by_user_id = 1; // Admin
                $assignment->save();

                // Synchroniser les statuts
                $synchronizer = app(ResourceStatusSynchronizer::class);
                $synchronizer->syncVehicleStatus($assignment->vehicle->fresh());
                $synchronizer->syncDriverStatus($assignment->driver->fresh());

                echo "✅ Affectation terminée avec succès\n";
                echo "✅ Ressources libérées et synchronisées\n";
            });
        } else {
            echo "💡 Décision: Verrouiller les ressources (affectation récente)\n";
            echo "\n";

            DB::transaction(function () use ($assignment) {
                // Verrouiller les ressources
                $assignment->vehicle->update([
                    'is_available' => false,
                    'current_driver_id' => $assignment->driver_id,
                    'assignment_status' => 'assigned',
                ]);

                $assignment->driver->update([
                    'is_available' => false,
                    'current_vehicle_id' => $assignment->vehicle_id,
                    'assignment_status' => 'assigned',
                ]);

                // Synchroniser les statuts
                $synchronizer = app(ResourceStatusSynchronizer::class);
                $synchronizer->syncVehicleStatus($assignment->vehicle->fresh());
                $synchronizer->syncDriverStatus($assignment->driver->fresh());

                echo "✅ Ressources verrouillées avec succès\n";
                echo "✅ Statuts synchronisés\n";
            });
        }
    } else {
        echo "✅ Aucune incohérence détectée\n";
    }

    echo "\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "📊 ÉTAT FINAL\n";
    echo "═══════════════════════════════════════════════════════════════\n";

    // Rafraîchir
    $assignment->refresh();
    $assignment->vehicle->refresh();
    $assignment->driver->refresh();

    echo "Affectation:\n";
    echo "  Status: {$assignment->status}\n";
    echo "  ended_at: " . ($assignment->ended_at ? $assignment->ended_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "\n";

    echo "Véhicule {$assignment->vehicle->registration_plate}:\n";
    echo "  is_available: " . ($assignment->vehicle->is_available ? 'true' : 'false') . "\n";
    echo "  assignment_status: {$assignment->vehicle->assignment_status}\n";
    echo "  status_id: {$assignment->vehicle->status_id}\n";
    echo "\n";

    echo "Chauffeur {$assignment->driver->first_name} {$assignment->driver->last_name}:\n";
    echo "  is_available: " . ($assignment->driver->is_available ? 'true' : 'false') . "\n";
    echo "  assignment_status: {$assignment->driver->assignment_status}\n";
    echo "  status_id: {$assignment->driver->status_id}\n";
    echo "\n";

    echo "🎉 CORRECTION TERMINÉE AVEC SUCCÈS\n";

    Log::info('[FIX_ZOMBIE_25] Affectation zombie corrigée', [
        'assignment_id' => 25,
        'action' => $hoursSinceStart > 24 ? 'terminated' : 'locked',
    ]);

    exit(0);

} catch (\Exception $e) {
    echo "\n";
    echo "❌ ERREUR LORS DE LA CORRECTION\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n";

    Log::error('[FIX_ZOMBIE_25] Erreur lors de la correction', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);

    exit(1);
}
