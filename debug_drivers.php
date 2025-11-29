<?php

use App\Models\Driver;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$drivers = Driver::whereIn('employee_number', ['DIF-2025-837', 'DLS-84745'])->with('assignments')->get();

foreach ($drivers as $driver) {
    echo "Driver: {$driver->full_name} ({$driver->employee_number})\n";
    echo "Total Assignments: " . $driver->assignments->count() . "\n";
    echo "Active Assignments: " . $driver->assignments()->whereNull('end_datetime')->count() . "\n";
    foreach ($driver->assignments as $assignment) {
        echo " - Assignment ID: {$assignment->id}, Start: {$assignment->start_datetime}, End: {$assignment->end_datetime}\n";
    }
    echo "--------------------------------\n";
}
