<?php

use App\Models\DriverStatus;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$status = DriverStatus::where('slug', 'en_formation')->first();

if ($status) {
    echo "✅ DB Record for 'en_formation' exists.\n";
} else {
    echo "❌ DB Record for 'en_formation' MISSING. You should run the seeder.\n";
}
