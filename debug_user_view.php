<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SIMULATING COMPTABLE USER VEHICLE QUERY ===\n\n";

// Login as Comptable
$comptable = App\Models\User::where('email', 'comptable@zenfleet.dz')->first();

if (!$comptable) {
    echo "ERROR: Comptable not found\n";
    exit(1);
}

echo "User: {$comptable->name} (ID: {$comptable->id})\n";
echo "Organization ID: {$comptable->organization_id}\n";
echo "Roles: " . $comptable->roles->pluck('name')->implode(', ') . "\n\n";

// Simulate login
Auth::login($comptable);

echo "--- Querying vehicles AS IF the user is logged in ---\n\n";

// This should apply the UserVehicleAccessScope
$vehicles = App\Models\Vehicle::all();

echo "Total vehicles visible to user: " . $vehicles->count() . "\n\n";

$targetPlates = ['795626-16', '869897-16', '611824-16', '301401-16', '377545-16', '130672-16'];
echo "Checking target vehicles:\n";
foreach ($targetPlates as $plate) {
    $found = $vehicles->firstWhere('registration_plate', $plate);
    echo "- {$plate}: " . ($found ? "✅ VISIBLE (ID: {$found->id})" : "❌ NOT VISIBLE") . "\n";
}

echo "\n--- Checking Vehicle->users relationship ---\n";
$testVehicle = App\Models\Vehicle::withoutGlobalScopes()->where('registration_plate', '795626-16')->first();
if ($testVehicle) {
    echo "Vehicle: {$testVehicle->registration_plate} (ID: {$testVehicle->id})\n";
    
    // Check if users relationship exists
    if (method_exists($testVehicle, 'users')) {
        $users = $testVehicle->users;
        echo "Users relation exists. Count: " . $users->count() . "\n";
        foreach ($users as $u) {
            echo "  - User: {$u->name} (ID: {$u->id})\n";
        }
    } else {
        echo "❌ ERROR: Vehicle model does NOT have 'users' relationship!\n";
    }
}

echo "\n--- Direct pivot check ---\n";
$pivotCount = DB::table('user_vehicle')
    ->where('user_id', $comptable->id)
    ->count();
echo "Pivot records for user: {$pivotCount}\n";

$missingPlates = ['795626-16', '869897-16', '611824-16'];
foreach ($missingPlates as $plate) {
    $vehicle = App\Models\Vehicle::withoutGlobalScopes()->where('registration_plate', $plate)->first();
    if ($vehicle) {
        $exists = DB::table('user_vehicle')
            ->where('user_id', $comptable->id)
            ->where('vehicle_id', $vehicle->id)
            ->exists();
        echo "- {$plate} (ID: {$vehicle->id}): Pivot exists = " . ($exists ? 'YES' : 'NO') . "\n";
    }
}
