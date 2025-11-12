<?php

/**
 * Script pour créer une assignment de test active
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Assignment;
use Carbon\Carbon;

try {
    // Récupérer un véhicule et un driver existant
    $vehicle = Vehicle::where('registration_plate', '118910-16')->first();
    $driver = Driver::whereHas('user')->first();
    
    if (!$vehicle || !$driver) {
        echo "Véhicule ou driver non trouvé\n";
        exit(1);
    }
    
    // Mettre à jour l'assignment existante pour qu'elle soit active
    $assignment = Assignment::where('vehicle_id', $vehicle->id)->first();
    
    if ($assignment) {
        $assignment->update([
            'status' => 'active',
            'start_datetime' => Carbon::now()->subDays(1),
            'end_datetime' => Carbon::now()->addDays(30),
            'driver_id' => $driver->id
        ]);
        echo "✅ Assignment mise à jour:\n";
    } else {
        // Créer une nouvelle assignment
        $assignment = Assignment::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'organization_id' => $vehicle->organization_id,
            'status' => 'active',
            'start_datetime' => Carbon::now()->subDays(1),
            'end_datetime' => Carbon::now()->addDays(30),
            'reason' => 'Test affichage chauffeur'
        ]);
        echo "✅ Nouvelle assignment créée:\n";
    }
    
    echo "  Véhicule: {$vehicle->registration_plate}\n";
    echo "  Chauffeur: {$driver->user->name}\n";
    echo "  Status: {$assignment->status}\n";
    echo "  Début: {$assignment->start_datetime}\n";
    echo "  Fin: {$assignment->end_datetime}\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
