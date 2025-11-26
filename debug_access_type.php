<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking access_type column in user_vehicle table...\n\n";

// Get user ID
$comptable = App\Models\User::where('email', 'comptable@zenfleet.dz')->first();

if (!$comptable) {
    echo "ERROR: Comptable not found\n";
    exit(1);
}

echo "User: " . $comptable->name . " (ID: " . $comptable->id . ")\n\n";

// Check all pivot records for this user
$allPivot = DB::table('user_vehicle')
    ->where('user_id', $comptable->id)
    ->get();

echo "Total pivot records for this user: " . $allPivot->count() . "\n\n";

foreach ($allPivot as $record) {
    $vehicle = App\Models\Vehicle::withoutGlobalScopes()->find($record->vehicle_id);
    echo "Vehicle ID: {$record->vehicle_id} (Plate: {$vehicle->registration_plate}), Access Type: " . ($record->access_type ?? 'NULL') . "\n";
}

// Check if access_type column even exists
$columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'user_vehicle'");
echo "\n\nColumns in user_vehicle table:\n";
foreach ($columns as $col) {
    echo "- " . $col->column_name . "\n";
}
