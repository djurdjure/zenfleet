<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$logFile = 'verification_result.txt';
file_put_contents($logFile, "--- START VERIFICATION ---\n");

function logMsg($msg) {
    global $logFile;
    file_put_contents($logFile, $msg . "\n", FILE_APPEND);
    echo $msg . "\n";
}

// Ensure statuses exist
$parkingStatus = \App\Models\VehicleStatus::firstOrCreate(['slug' => 'parking'], ['name' => 'Parking', 'color' => 'blue']);
$availableStatus = \App\Models\VehicleStatus::firstOrCreate(['slug' => 'available'], ['name' => 'Available', 'color' => 'green']);
$driverAvailableStatus = \App\Models\DriverStatus::firstOrCreate(['slug' => 'available'], ['name' => 'Available', 'color' => 'green']);

// Verify Vehicle Status Change Logic
logMsg("Testing Vehicle Status Change...");
$vehicle = \App\Models\Vehicle::inRandomOrder()->first();

if (!$vehicle) {
    logMsg("No vehicle found in database.");
} else {
    $originalStatus = $vehicle->status_id;
    $newStatus = \App\Models\VehicleStatus::where('id', '!=', $originalStatus)->first();

    if ($newStatus) {
        $vehicle->update(['status_id' => $newStatus->id]);
        logMsg("Vehicle status changed from $originalStatus to {$newStatus->id}. Current: {$vehicle->fresh()->status_id}");
        // Revert
        $vehicle->update(['status_id' => $originalStatus]);
        logMsg("Reverted to $originalStatus.");
    } else {
        logMsg("No other status found to test.");
    }
}

// Verify Assignment Deletion Logic
logMsg("\nTesting Assignment Deletion Logic...");
// Find a driver and vehicle that are NOT currently assigned if possible, or just create a new assignment for them
$driver = \App\Models\Driver::inRandomOrder()->first();
$vehicle = \App\Models\Vehicle::inRandomOrder()->first();

if ($driver && $vehicle) {
    $assignment = \App\Models\Assignment::create([
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'start_datetime' => now(),
        'status' => 'active',
        'organization_id' => 1
    ]);
    
    logMsg("Assignment created: {$assignment->id}");
    
    // Simulate deleteAssignment logic
    $v = $assignment->vehicle;
    $d = $assignment->driver;
    
    // Manually set statuses to something else to verify the change
    if ($v) $v->update(['status_id' => $availableStatus->id]); // Not parking
    if ($d) $d->update(['status_id' => $driverAvailableStatus->id]); // Available
    
    logMsg("Pre-delete Vehicle Status: " . ($v ? $v->fresh()->status->name : 'N/A'));
    logMsg("Pre-delete Driver Status: " . ($d ? $d->fresh()->driverStatus->name : 'N/A'));
    
    // Execute the logic we added to AssignmentTable (simulated)
    if ($v) {
        $pStatus = \App\Models\VehicleStatus::where('slug', 'parking')->first();
        if (!$pStatus) $pStatus = \App\Models\VehicleStatus::where('slug', 'available')->first();
        if ($pStatus) $v->update(['status_id' => $pStatus->id]);
    }
    
    if ($d) {
        $aStatus = \App\Models\DriverStatus::where('slug', 'available')->first();
        if ($aStatus) $d->update(['status_id' => $aStatus->id]);
    }
    
    $assignment->delete();
    logMsg("Assignment deleted.");
    
    logMsg("Final Vehicle Status: " . ($v ? $v->fresh()->status->name : 'N/A'));
    logMsg("Final Driver Status: " . ($d ? $d->fresh()->driverStatus->name : 'N/A'));
    
} else {
    logMsg("Missing driver or vehicle for assignment test.");
}

logMsg("--- END VERIFICATION ---");
