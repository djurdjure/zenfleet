<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Support\PermissionAliases;
use Illuminate\Support\Facades\Gate;

// Hardcoded user email for 'Admin' - assumed based on context or found via query
$adminUser = User::whereHas('roles', function ($q) {
    $q->where('name', 'Admin');
})->first();

if (!$adminUser) {
    echo "No Admin user found.\n";
    exit;
}

echo "Analyzing User: " . $adminUser->name . " (ID: " . $adminUser->id . ")\n";
echo "Organization: " . $adminUser->organization->name . "\n";
echo "Roles: " . $adminUser->roles->pluck('name')->implode(', ') . "\n\n";

// Login as user to test Gates
Auth::login($adminUser);

$checks = [
    'depots.view',
    'depots.create',
    'vehicles.export',
    'drivers.export'
];

echo "--- Permission Checks ---\n";
foreach ($checks as $ability) {
    $can = $adminUser->can($ability) ? 'YES' : 'NO';
    echo "User can '$ability': $can\n";

    // Check aliases
    $aliases = PermissionAliases::resolve($ability);
    echo "  Aliases: " . implode(', ', $aliases) . "\n";
}

echo "\n--- All Assigned Permissions (Direct & via Roles) ---\n";
$allPermissions = $adminUser->getAllPermissions()->pluck('name');
foreach ($allPermissions as $perm) {
    echo "- $perm\n";
}

echo "\n--- Gate::before Debug ---\n";
// Manually check the logic in AuthServiceProvider
$permissionNames = $adminUser->getAllPermissions()->pluck('name');
foreach ($checks as $ability) {
    if (!PermissionAliases::isRelevant($ability)) {
        continue;
    }
    foreach (PermissionAliases::resolve($ability) as $permission) {
        if ($permissionNames->contains($permission)) {
            echo "[$ability] GRANTED via alias '$permission'\n";
        }
    }
}
