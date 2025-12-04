// Verify Vehicle Status Change Logic
$vehicle = \App\Models\Vehicle::first();
if (!$vehicle) {
    echo "No vehicle found.\n";
} else {
    echo "Testing Vehicle Status Change...\n";
    $originalStatus = $vehicle->status_id;
    $newStatus = \App\Models\VehicleStatus::where('id', '!=', $originalStatus)->first();
    
    if ($newStatus) {
        $vehicle->update(['status_id' => $newStatus->id]);
        echo "Vehicle status changed from $originalStatus to {$newStatus->id}. Current: {$vehicle->fresh()->status_id}\n";
        // Revert
        $vehicle->update(['status_id' => $originalStatus]);
        echo "Reverted to $originalStatus.\n";
    } else {
        echo "No other status found to test.\n";
    }
}

// Verify Assignment Deletion Logic
echo "\nTesting Assignment Deletion Logic...\n";
$driver = \App\Models\Driver::first();
$vehicle = \App\Models\Vehicle::first();

if ($driver && $vehicle) {
    $assignment = \App\Models\Assignment::create([
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'start_datetime' => now(),
        'status' => 'active',
        'organization_id' => 1 // Assuming org 1 exists
    ]);
    
    echo "Assignment created: {$assignment->id}\n";
    
    // Simulate deleteAssignment logic
    $v = $assignment->vehicle;
    $d = $assignment->driver;
    
    $parkingStatus = \App\Models\VehicleStatus::where('slug', 'parking')->first();
    $availableStatus = \App\Models\DriverStatus::where('slug', 'available')->first();
    
    if ($v && $parkingStatus) {
        $v->update(['status_id' => $parkingStatus->id]);
        echo "Vehicle status updated to Parking ({$parkingStatus->id})\n";
    }
    
    if ($d && $availableStatus) {
        $d->update(['status_id' => $availableStatus->id]);
        echo "Driver status updated to Available ({$availableStatus->id})\n";
    }
    
    $assignment->delete();
    echo "Assignment deleted.\n";
    
    echo "Final Vehicle Status: " . $v->fresh()->status->name . "\n";
    echo "Final Driver Status: " . $d->fresh()->driverStatus->name . "\n";
    
} else {
    echo "Missing driver or vehicle for assignment test.\n";
}
