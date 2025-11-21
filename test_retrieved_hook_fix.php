<?php

/**
 * 🧪 TEST CORRECTION - Hook retrieved() libère maintenant les ressources
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   🧪 TEST CORRECTION - Hook retrieved() libère ressources    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Chercher des affectations expirées qui pourraient être des zombies
echo "📊 Recherche d'affectations expirées (zombies potentiels)...\n\n";

$expiredAssignments = Assignment::whereNotNull('end_datetime')
    ->where('end_datetime', '<=', now())
    ->with(['vehicle', 'driver'])
    ->get();

echo "   Affectations expirées trouvées : " . $expiredAssignments->count() . "\n\n";

foreach ($expiredAssignments as $assignment) {
    $storedStatus = \DB::table('assignments')->where('id', $assignment->id)->value('status');
    $calculatedStatus = $assignment->status; // Récupéré via accessor/observer

    echo "─────────────────────────────────────────────────────────────\n";
    echo "📋 Affectation #{$assignment->id}\n";
    echo "   Fin prévue : " . $assignment->end_datetime->format('d/m/Y H:i') . "\n";
    echo "   Statut DB  : {$storedStatus}\n";
    echo "   Statut calculé : {$calculatedStatus}\n";

    if ($storedStatus !== $calculatedStatus) {
        echo "   🧟 ZOMBIE DÉTECTÉ !\n";
        echo "   ⚠️ L'observer devrait avoir auto-corrigé\n";
    } else {
        echo "   ✅ Cohérent\n";
    }

    // Vérifier l'état des ressources
    if ($assignment->vehicle) {
        $vehicle = \DB::table('vehicles')->where('id', $assignment->vehicle_id)->first();
        echo "   🚗 Véhicule {$assignment->vehicle->registration_plate}:\n";
        echo "      is_available: " . ($vehicle->is_available ? 'true ✅' : 'false ❌') . "\n";
        echo "      assignment_status: {$vehicle->assignment_status}\n";
        echo "      status_id: {$vehicle->status_id} " . ($vehicle->status_id == 8 ? '(Parking ✅)' : '(Affecté ❌)') . "\n";

        if ($calculatedStatus === 'completed' && !$vehicle->is_available) {
            echo "      ⚠️ PROBLÈME : Véhicule devrait être disponible !\n";
        }
    }

    if ($assignment->driver) {
        $driver = \DB::table('drivers')->where('id', $assignment->driver_id)->first();
        echo "   👤 Chauffeur {$assignment->driver->full_name}:\n";
        echo "      is_available: " . ($driver->is_available ? 'true ✅' : 'false ❌') . "\n";
        echo "      assignment_status: {$driver->assignment_status}\n";
        echo "      status_id: {$driver->status_id} " . ($driver->status_id == 7 ? '(Disponible ✅)' : '(En mission ❌)') . "\n";

        if ($calculatedStatus === 'completed' && !$driver->is_available) {
            echo "      ⚠️ PROBLÈME : Chauffeur devrait être disponible !\n";
        }
    }

    echo "\n";
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     ✅ VÉRIFICATION TERMINÉE                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 RÉSULTAT:\n";
echo "   Avec la correction, le hook retrieved() devrait maintenant\n";
echo "   libérer automatiquement les ressources quand il détecte un\n";
echo "   zombie et le corrige en 'completed'.\n\n";
