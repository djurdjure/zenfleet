<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Force DB host to localhost (since we are running outside Docker but port is forwarded)
config(['database.connections.pgsql.host' => '127.0.0.1']);

echo "Updating drivers...\n";
App\Models\Driver::all()->each(function ($d) {
    if (!is_array($d->license_categories) || count($d->license_categories) > 1) {
        $d->license_categories = ['B'];
        $d->save();
        echo "Updated driver {$d->id}\n";
    }
});
echo "Done.\n";
