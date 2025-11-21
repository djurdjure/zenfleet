<?php

/**
 * 🧪 TEST END-TO-END - Création Assignment 20/11/2025
 *
 * Simule exactement le processus du formulaire Livewire pour créer
 * une affectation avec les dates problématiques.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST END-TO-END - Création Assignment 20/11/2025        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Trouver un véhicule et un chauffeur disponibles
echo "📊 Recherche de ressources disponibles...\n\n";

$vehicle = Vehicle::where('is_available', true)
    ->where('assignment_status', 'available')
    ->first();

$driver = Driver::where('is_available', true)
    ->where('assignment_status', 'available')
    ->first();

if (!$vehicle || !$driver) {
    echo "❌ Pas de ressources disponibles pour le test\n";
    echo "   Créez au moins un véhicule et un chauffeur disponibles\n";
    exit(1);
}

echo "✅ Véhicule trouvé : {$vehicle->registration_plate} (ID: {$vehicle->id})\n";
echo "✅ Chauffeur trouvé : {$driver->full_name} (ID: {$driver->id})\n\n";

echo "─────────────────────────────────────────────────────────────\n\n";

// Simuler exactement ce que fait AssignmentForm.php

echo "📋 ÉTAPE 1 : Préparation des données (comme AssignmentForm)\n";
echo "─────────────────────────────────────────────────────────────\n";

// Format français comme saisi par l'utilisateur
$start_date = '20/11/2025';
$start_time = '18:30';
$end_date = '20/11/2025';
$end_time = '22:00';

echo "Données formulaire:\n";
echo "  start_date: $start_date\n";
echo "  start_time: $start_time\n";
echo "  end_date:   $end_date\n";
echo "  end_time:   $end_time\n\n";

// Conversion ISO (méthode convertToISO du formulaire)
function convertToISO(string $date): string
{
    if (empty($date)) {
        return '';
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return $date;
    }

    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];

        if (checkdate((int)$month, (int)$day, (int)$year)) {
            return "$year-$month-$day";
        }
    }

    return $date;
}

$startDateISO = convertToISO($start_date);
$endDateISO = convertToISO($end_date);

$start_datetime = $startDateISO . ' ' . $start_time;
$end_datetime = $endDateISO . ' ' . $end_time;

echo "Après combineDateTime():\n";
echo "  start_datetime: $start_datetime\n";
echo "  end_datetime:   $end_datetime\n\n";

echo "📋 ÉTAPE 2 : Création des objets Carbon (comme saveAssignment)\n";
echo "─────────────────────────────────────────────────────────────\n";

$data = [
    'organization_id' => 1, // Hardcoded pour le test
    'vehicle_id' => (int) $vehicle->id,
    'driver_id' => (int) $driver->id,
    'start_datetime' => Carbon::parse($start_datetime),
    'end_datetime' => $end_datetime ? Carbon::parse($end_datetime) : null,
    'reason' => 'Test automatique - Validation dates 20/11/2025',
    'notes' => 'Créé par test_end_to_end_assignment_20nov.php'
];

echo "Data array préparé:\n";
echo "  start_datetime: " . $data['start_datetime']->toIso8601String() . "\n";
echo "  end_datetime:   " . $data['end_datetime']->toIso8601String() . "\n";
echo "  Comparaison: " . ($data['start_datetime'] < $data['end_datetime'] ? '✅ start < end' : '❌ start >= end') . "\n\n";

echo "📋 ÉTAPE 3 : Tentative de création de l'Assignment\n";
echo "─────────────────────────────────────────────────────────────\n";

try {
    \Log::info('[TEST] 🧪 Tentative création Assignment avec dates 20/11/2025', [
        'start_datetime' => $data['start_datetime']->toIso8601String(),
        'end_datetime' => $data['end_datetime']->toIso8601String(),
        'vehicle_id' => $data['vehicle_id'],
        'driver_id' => $data['driver_id']
    ]);

    $assignment = Assignment::create($data);

    echo "✅ Assignment créée avec succès !\n";
    echo "   ID: {$assignment->id}\n";
    echo "   Status: {$assignment->status}\n";
    echo "   Start: " . $assignment->start_datetime->format('d/m/Y H:i') . "\n";
    echo "   End:   " . $assignment->end_datetime->format('d/m/Y H:i') . "\n\n";

    // Vérifier l'état des ressources
    $vehicle->refresh();
    $driver->refresh();

    echo "📊 État des ressources après création:\n\n";

    echo "   🚗 Véhicule {$vehicle->registration_plate}:\n";
    echo "      is_available: " . ($vehicle->is_available ? 'true ✅' : 'false ❌') . "\n";
    echo "      assignment_status: {$vehicle->assignment_status}\n";
    echo "      status_id: {$vehicle->status_id}\n\n";

    echo "   👤 Chauffeur {$driver->full_name}:\n";
    echo "      is_available: " . ($driver->is_available ? 'true ✅' : 'false ❌') . "\n";
    echo "      assignment_status: {$driver->assignment_status}\n";
    echo "      status_id: {$driver->status_id}\n\n";

    echo "─────────────────────────────────────────────────────────────\n\n";

    echo "✅ TEST RÉUSSI : L'Assignment a été créée sans erreur\n";
    echo "   Cela signifie que le code de validation fonctionne correctement\n\n";

    echo "💡 NETTOYAGE : Suppression de l'affectation de test...\n";
    $assignment->delete();
    echo "   ✅ Assignment #{$assignment->id} supprimée\n\n";

} catch (\InvalidArgumentException $e) {
    echo "❌ ERREUR DE VALIDATION !\n\n";
    echo "Message d'erreur:\n";
    echo "   " . $e->getMessage() . "\n\n";

    echo "Stack trace:\n";
    foreach (explode("\n", $e->getTraceAsString()) as $line) {
        echo "   $line\n";
    }
    echo "\n";

    echo "🔍 ANALYSE:\n";
    echo "   Cette erreur confirme que le problème persiste\n";
    echo "   Il faut investiguer plus en profondeur\n\n";

    \Log::error('[TEST] ❌ Échec création Assignment', [
        'error' => $e->getMessage(),
        'start_datetime' => $data['start_datetime']->toIso8601String(),
        'end_datetime' => $data['end_datetime']->toIso8601String()
    ]);

    exit(1);

} catch (\Exception $e) {
    echo "❌ ERREUR INATTENDUE !\n\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";

    \Log::error('[TEST] ❌ Erreur inattendue', [
        'error_type' => get_class($e),
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);

    exit(1);
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     ✅ TEST TERMINÉ                          ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 CONCLUSION:\n";
echo "   Si ce test passe, le problème n'est PAS dans le code backend\n";
echo "   Il faut alors vérifier:\n";
echo "   - Cache du navigateur\n";
echo "   - Validation JavaScript côté client\n";
echo "   - Autre middleware ou validation\n\n";
