<?php

/**
 * 🔧 SCRIPT DE CORRECTION ENTERPRISE-GRADE : Cohérence des données de kilométrage
 *
 * Ce script corrige les incohérences de kilométrage identifiées dans le système.
 *
 * PROBLÈME RÉSOLU :
 * - Les affectations enregistraient le kilométrage uniquement dans la table assignments
 * - Le kilométrage du véhicule (current_mileage) n'était pas mis à jour
 * - Aucun historique n'était créé dans vehicle_mileage_readings
 *
 * ACTIONS DU SCRIPT :
 * 1. Identifier toutes les affectations passées sans entrées dans vehicle_mileage_readings
 * 2. Créer les entrées manquantes pour start_mileage et end_mileage
 * 3. Mettre à jour le current_mileage des véhicules avec le dernier relevé
 * 4. Générer un rapport détaillé des corrections
 *
 * UTILISATION :
 * php fix_mileage_data_consistency.php [--dry-run] [--vehicle-id=X]
 *
 * OPTIONS :
 * --dry-run : Affiche les corrections sans les appliquer
 * --vehicle-id=X : Traite uniquement le véhicule X
 * --assignment-id=X : Traite uniquement l'affectation X
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-16
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Models\MileageHistory;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Parse des arguments
$options = [
    'dry_run' => in_array('--dry-run', $argv),
    'vehicle_id' => null,
    'assignment_id' => null,
];

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--vehicle-id=')) {
        $options['vehicle_id'] = (int) substr($arg, 13);
    }
    if (str_starts_with($arg, '--assignment-id=')) {
        $options['assignment_id'] = (int) substr($arg, 16);
    }
}

echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║  🔧 CORRECTION ENTERPRISE - COHÉRENCE KILOMÉTRAGE VÉHICULES       ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

if ($options['dry_run']) {
    echo "⚠️  MODE DRY-RUN : Aucune modification ne sera appliquée\n\n";
}

$stats = [
    'assignments_processed' => 0,
    'mileage_readings_created' => 0,
    'vehicles_updated' => 0,
    'errors' => 0,
    'skipped' => 0,
];

try {
    // 1. RÉCUPÉRER LES AFFECTATIONS À TRAITER
    echo "📊 Analyse des affectations...\n";

    $query = Assignment::with(['vehicle', 'driver'])
        ->whereNotNull('start_mileage')
        ->orderBy('start_datetime', 'asc');

    if ($options['vehicle_id']) {
        $query->where('vehicle_id', $options['vehicle_id']);
        echo "   Filtrage : Véhicule #{$options['vehicle_id']}\n";
    }

    if ($options['assignment_id']) {
        $query->where('id', $options['assignment_id']);
        echo "   Filtrage : Affectation #{$options['assignment_id']}\n";
    }

    $assignments = $query->get();
    echo "   ✓ {$assignments->count()} affectations trouvées\n\n";

    if ($assignments->isEmpty()) {
        echo "✅ Aucune affectation à traiter\n";
        exit(0);
    }

    // 2. TRAITER CHAQUE AFFECTATION
    echo "🔄 Traitement des affectations...\n\n";

    DB::beginTransaction();

    foreach ($assignments as $assignment) {
        $stats['assignments_processed']++;

        echo "───────────────────────────────────────────────────────────────────\n";
        echo "Affectation #{$assignment->id}\n";
        echo "   Véhicule : {$assignment->vehicle->registration_plate} (ID: {$assignment->vehicle_id})\n";
        echo "   Chauffeur : {$assignment->driver->first_name} {$assignment->driver->last_name}\n";
        echo "   Période : " . $assignment->start_datetime->format('d/m/Y H:i');
        
        if ($assignment->end_datetime) {
            echo " → " . $assignment->end_datetime->format('d/m/Y H:i') . "\n";
        } else {
            echo " → En cours\n";
        }

        try {
            // 2.1. Vérifier si un relevé existe déjà pour le start_mileage
            $startReadingExists = VehicleMileageReading::where('vehicle_id', $assignment->vehicle_id)
                ->where('mileage', $assignment->start_mileage)
                ->where('recorded_at', '>=', $assignment->start_datetime->copy()->subHours(1))
                ->where('recorded_at', '<=', $assignment->start_datetime->copy()->addHours(1))
                ->exists();

            if (!$startReadingExists) {
                echo "   📝 Création relevé de DÉBUT : {$assignment->start_mileage} km\n";

                if (!$options['dry_run']) {
                    VehicleMileageReading::create([
                        'organization_id' => $assignment->organization_id,
                        'vehicle_id' => $assignment->vehicle_id,
                        'recorded_at' => $assignment->start_datetime,
                        'mileage' => $assignment->start_mileage,
                        'recorded_by_id' => null,
                        'recording_method' => 'automatic',
                        'notes' => "Migration : Kilométrage de début d'affectation #{$assignment->id}",
                    ]);

                    $stats['mileage_readings_created']++;
                }
            } else {
                echo "   ⏭️  Relevé de début déjà existant\n";
                $stats['skipped']++;
            }

            // 2.2. Traiter le end_mileage si l'affectation est terminée
            if ($assignment->end_mileage && $assignment->end_datetime) {
                $endReadingExists = VehicleMileageReading::where('vehicle_id', $assignment->vehicle_id)
                    ->where('mileage', $assignment->end_mileage)
                    ->where('recorded_at', '>=', $assignment->end_datetime->copy()->subHours(1))
                    ->where('recorded_at', '<=', $assignment->end_datetime->copy()->addHours(1))
                    ->exists();

                if (!$endReadingExists) {
                    echo "   📝 Création relevé de FIN : {$assignment->end_mileage} km\n";

                    if (!$options['dry_run']) {
                        VehicleMileageReading::create([
                            'organization_id' => $assignment->organization_id,
                            'vehicle_id' => $assignment->vehicle_id,
                            'recorded_at' => $assignment->end_datetime,
                            'mileage' => $assignment->end_mileage,
                            'recorded_by_id' => null,
                            'recording_method' => 'automatic',
                            'notes' => "Migration : Kilométrage de fin d'affectation #{$assignment->id}",
                        ]);

                        $stats['mileage_readings_created']++;
                    }
                } else {
                    echo "   ⏭️  Relevé de fin déjà existant\n";
                    $stats['skipped']++;
                }
            }

            echo "   ✅ Affectation traitée avec succès\n";

        } catch (\Exception $e) {
            echo "   ❌ ERREUR : {$e->getMessage()}\n";
            $stats['errors']++;
        }
    }

    echo "\n───────────────────────────────────────────────────────────────────\n\n";

    // 3. SYNCHRONISER LE KILOMÉTRAGE DE CHAQUE VÉHICULE
    echo "🔄 Synchronisation des kilométrages véhicules...\n\n";

    $vehicleIds = $assignments->pluck('vehicle_id')->unique();

    foreach ($vehicleIds as $vehicleId) {
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            continue;
        }

        // Récupérer le dernier relevé kilométrique
        $lastReading = VehicleMileageReading::where('vehicle_id', $vehicleId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if ($lastReading && $lastReading->mileage !== $vehicle->current_mileage) {
            echo "   Véhicule {$vehicle->registration_plate} :\n";
            echo "      Ancien kilométrage : " . number_format($vehicle->current_mileage) . " km\n";
            echo "      Nouveau kilométrage : " . number_format($lastReading->mileage) . " km\n";
            echo "      Différence : " . number_format($lastReading->mileage - $vehicle->current_mileage) . " km\n";

            if (!$options['dry_run']) {
                $vehicle->current_mileage = $lastReading->mileage;
                $vehicle->save();
                $stats['vehicles_updated']++;
            }

            echo "      ✅ Synchronisé\n\n";
        }
    }

    // 4. COMMIT OU ROLLBACK
    if ($options['dry_run']) {
        DB::rollBack();
        echo "⚠️  ROLLBACK : Aucune modification appliquée (mode dry-run)\n\n";
    } else {
        DB::commit();
        echo "✅ COMMIT : Toutes les modifications ont été appliquées\n\n";
    }

    // 5. RAPPORT FINAL
    echo "╔════════════════════════════════════════════════════════════════════╗\n";
    echo "║                     📊 RAPPORT DE CORRECTION                       ║\n";
    echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

    echo "Affectations traitées        : {$stats['assignments_processed']}\n";
    echo "Relevés créés                : {$stats['mileage_readings_created']}\n";
    echo "Véhicules mis à jour         : {$stats['vehicles_updated']}\n";
    echo "Relevés déjà existants       : {$stats['skipped']}\n";
    echo "Erreurs rencontrées          : {$stats['errors']}\n\n";

    if ($stats['errors'] > 0) {
        echo "⚠️  Des erreurs ont été rencontrées. Consultez les logs ci-dessus.\n\n";
        exit(1);
    }

    if ($options['dry_run']) {
        echo "💡 Pour appliquer les corrections, relancez sans l'option --dry-run\n\n";
    } else {
        echo "✅ Correction terminée avec succès !\n\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    
    echo "\n❌ ERREUR CRITIQUE : {$e->getMessage()}\n";
    echo "Trace : {$e->getTraceAsString()}\n\n";
    
    exit(1);
}
