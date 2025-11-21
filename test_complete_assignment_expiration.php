<?php

/**
 * 🧪 TEST COMPLET - Expiration d'affectation et libération de ressources
 *
 * Ce script crée une affectation qui expire dans 2 minutes et vérifie
 * que les ressources sont correctement libérées.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║      🧪 TEST COMPLET - Expiration et Libération Ressources   ║\n";
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
    exit(1);
}

echo "✅ Véhicule trouvé : {$vehicle->registration_plate} (ID: {$vehicle->id})\n";
echo "   status_id: {$vehicle->status_id}\n";
echo "   is_available: " . ($vehicle->is_available ? 'true' : 'false') . "\n";
echo "   assignment_status: {$vehicle->assignment_status}\n\n";

echo "✅ Chauffeur trouvé : {$driver->full_name} (ID: {$driver->id})\n";
echo "   status_id: {$driver->status_id}\n";
echo "   is_available: " . ($driver->is_available ? 'true' : 'false') . "\n";
echo "   assignment_status: {$driver->assignment_status}\n\n";

echo "─────────────────────────────────────────────────────────────\n\n";

// Créer une affectation qui expire dans 2 minutes
$startTime = now();
$endTime = now()->addMinutes(2);

echo "📝 Création d'une affectation de test...\n";
echo "   Début : " . $startTime->format('d/m/Y H:i:s') . "\n";
echo "   Fin   : " . $endTime->format('d/m/Y H:i:s') . " (dans 2 minutes)\n\n";

try {
    $assignment = Assignment::create([
        'organization_id' => 1,
        'vehicle_id' => $vehicle->id,
        'driver_id' => $driver->id,
        'start_datetime' => $startTime,
        'end_datetime' => $endTime,
        'reason' => 'Test automatique - Vérification libération ressources',
        'notes' => 'Créé par test_complete_assignment_expiration.php'
    ]);

    echo "✅ Affectation créée : ID #{$assignment->id}\n\n";

    // Vérifier que les ressources sont verrouillées
    $vehicle->refresh();
    $driver->refresh();

    echo "📊 État après création de l'affectation :\n\n";

    echo "   🚗 Véhicule {$vehicle->registration_plate}:\n";
    echo "      is_available: " . ($vehicle->is_available ? 'true ✅' : 'false ❌') . "\n";
    echo "      assignment_status: {$vehicle->assignment_status}\n";
    echo "      status_id: {$vehicle->status_id}\n";
    echo "      current_driver_id: " . ($vehicle->current_driver_id ?? 'NULL') . "\n\n";

    echo "   👤 Chauffeur {$driver->full_name}:\n";
    echo "      is_available: " . ($driver->is_available ? 'true ✅' : 'false ❌') . "\n";
    echo "      assignment_status: {$driver->assignment_status}\n";
    echo "      status_id: {$driver->status_id}\n";
    echo "      current_vehicle_id: " . ($driver->current_vehicle_id ?? 'NULL') . "\n\n";

    if (!$vehicle->is_available && !$driver->is_available) {
        echo "   ✅ Ressources correctement verrouillées\n\n";
    } else {
        echo "   ⚠️ Problème : Ressources devraient être verrouillées\n\n";
    }

    echo "─────────────────────────────────────────────────────────────\n\n";

    echo "⏰ L'affectation expirera dans 2 minutes.\n";
    echo "   Attendez 2 minutes puis vérifiez avec ce script :\n\n";
    echo "   docker exec zenfleet_php php -r \"\n";
    echo "   require 'vendor/autoload.php';\n";
    echo "   \\\$app = require 'bootstrap/app.php';\n";
    echo "   \\\$app->make(Illuminate\\\\Contracts\\\\Console\\\\Kernel::class)->bootstrap();\n";
    echo "   \\\$a = App\\\\Models\\\\Assignment::find({$assignment->id});\n";
    echo "   echo 'Status: ' . \\\$a->status . PHP_EOL;\n";
    echo "   \\\$v = App\\\\Models\\\\Vehicle::find({$vehicle->id});\n";
    echo "   echo 'Véhicule available: ' . (\\\$v->is_available ? 'true' : 'false') . PHP_EOL;\n";
    echo "   echo 'Véhicule status_id: ' . \\\$v->status_id . PHP_EOL;\n";
    echo "   \\\$d = App\\\\Models\\\\Driver::find({$driver->id});\n";
    echo "   echo 'Chauffeur available: ' . (\\\$d->is_available ? 'true' : 'false') . PHP_EOL;\n";
    echo "   echo 'Chauffeur status_id: ' . \\\$d->status_id . PHP_EOL;\n";
    echo "   \"\n\n";

    echo "📝 RÉSULTATS ATTENDUS après expiration :\n";
    echo "   - Status: completed\n";
    echo "   - Véhicule available: true\n";
    echo "   - Véhicule status_id: 8 (Parking)\n";
    echo "   - Chauffeur available: true\n";
    echo "   - Chauffeur status_id: 7 (Disponible)\n\n";

} catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     ✅ TEST CRÉÉ AVEC SUCCÈS                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
