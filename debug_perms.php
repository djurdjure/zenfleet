<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Depot Permissions...\n";
$perms = Spatie\Permission\Models\Permission::where('name', 'like', '%depot%')->get();
echo "Found " . $perms->count() . " permissions matching 'depot':\n";
foreach ($perms as $p) {
    echo "- " . $p->name . " (guard: " . $p->guard_name . ")\n";
}

echo "\nChecking Admin Role...\n";
$role = Spatie\Permission\Models\Role::where('name', 'Admin')->first();
if ($role) {
    echo "Admin Role ID: " . $role->id . "\n";
    $adminPerms = $role->permissions->filter(fn($p) => str_contains($p->name, 'depot'));
    echo "Admin has " . $adminPerms->count() . " depot permissions:\n";
    foreach ($adminPerms as $p) {
        echo "- " . $p->name . "\n";
    }
} else {
    echo "Admin role NOT FOUND!\n";
}
