<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking user_vehicle pivot table...\n\n";

// Find the Comptable user
$comptable = App\Models\User::where('email', 'like', '%comptable%')
    ->orWhere('name', 'like', '%comptable%')
    ->first();

if (!$comptable) {
    echo "ERROR: Comptable user not found!\n";
    exit(1);
}

echo "Found user: " . $comptable->name . " (ID: " . $comptable->id . ", Email: " . $comptable->email . ")\n";
echo "Organization ID: " . $comptable->organization_id . "\n";
echo "Roles: " . $comptable->roles->pluck('name')->implode(', ') . "\n\n";

// Get vehicle IDs the user should care about
$targetPlates = ['795626-16', '869897-16', '611824-16', '301401-16', '377545-16', '130672-16', '613014-16', '284139-16', '835292-16'];
$targetVehicles = App\Models\Vehicle::withoutGlobalScopes()
    ->whereIn('registration_plate', $targetPlates)
    ->get(['id', 'registration_plate']);

echo "Target vehicles (from provided list):\n";
foreach ($targetVehicles as $v) {
    echo "- ID: {$v->id}, Plate: {$v->registration_plate}\n";
}

// Check pivot table
echo "\n--- Checking user_vehicle pivot table ---\n";
$pivotRecords = DB::table('user_vehicle')
    ->where('user_id', $comptable->id)
    ->whereIn('vehicle_id', $targetVehicles->pluck('id'))
    ->get();

echo "Found " . $pivotRecords->count() . " pivot records for this user and target vehicles:\n";
foreach ($pivotRecords as $record) {
    $vehicle = $targetVehicles->firstWhere('id', $record->vehicle_id);
    echo "- Vehicle ID: {$record->vehicle_id} (Plate: {$vehicle->registration_plate})\n";
}

// Find missing ones
$assignedVehicleIds = $pivotRecords->pluck('vehicle_id')->toArray();
$missingVehicles = $targetVehicles->whereNotIn('id', $assignedVehicleIds);
echo "\n--- MISSING from pivot table (NOT ASSIGNED) ---\n";
foreach ($missingVehicles as $v) {
    echo "- ID: {$v->id}, Plate: {$v->registration_plate}\n";
}
