<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$plates = ['795626-16', '869897-16', '611824-16', '301401-16'];
echo "Checking vehicles: " . implode(', ', $plates) . "\n";

$vehicles = App\Models\Vehicle::withoutGlobalScopes()->whereIn('registration_plate', $plates)->get();

foreach($vehicles as $v) {
    echo "--------------------------------\n";
    echo "Plate: " . $v->registration_plate . "\n";
    echo "ID: " . $v->id . "\n";
    echo "Org ID: " . $v->organization_id . "\n";
    echo "Status: " . ($v->status instanceof \BackedEnum ? $v->status->value : $v->status) . "\n";
    echo "Deleted At: " . ($v->deleted_at ? $v->deleted_at : 'NULL') . "\n";
}
echo "--------------------------------\n";
echo "Total found: " . $vehicles->count() . "\n";
