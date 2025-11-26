<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Restoring access for missing vehicles...\n\n";

$comptable = App\Models\User::where('email', 'comptable@zenfleet.dz')->first();
$missingPlates = ['795626-16', '611824-16', '869897-16'];

$missingVehicles = App\Models\Vehicle::withoutGlobalScopes()
    ->whereIn('registration_plate', $missingPlates)
    ->get();

echo "Found " . $missingVehicles->count() . " missing vehicles to add:\n";

foreach ($missingVehicles as $vehicle) {
    echo "- Adding access to: {$vehicle->registration_plate} (ID: {$vehicle->id})\n";
    
    DB::table('user_vehicle')->insert([
        'user_id' => $comptable->id,
        'vehicle_id' => $vehicle->id,
        'granted_at' => now(),
        'granted_by' => 1, // Assuming admin user ID = 1
        'access_type' => 'manual',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo "\n✅ Access restored successfully!\n";

// Verify
$count = DB::table('user_vehicle')
    ->where('user_id', $comptable->id)
    ->where('access_type', 'manual')
    ->count();
echo "Total manual access count for {$comptable->name}: {$count}\n";
