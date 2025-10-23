<?php

/*
 * 🔧 Script de Correction des Permissions Véhicules
 * Attribue toutes les permissions véhicules au Super Admin
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "\n🔧 CORRECTION DES PERMISSIONS VÉHICULES\n";
echo str_repeat("=", 60) . "\n\n";

// Permissions requises
$requiredPermissions = [
    'view vehicles',
    'create vehicles',
    'update vehicles',
    'delete vehicles',
];

// Créer les permissions si elles n'existent pas
echo "📋 Création/vérification des permissions...\n";
foreach ($requiredPermissions as $permissionName) {
    $permission = Permission::firstOrCreate([
        'name' => $permissionName,
        'guard_name' => 'web'
    ]);
    echo "   ✅ $permissionName\n";
}

echo "\n";

// Attribuer au Super Admin
$superAdmin = User::where('email', 'superadmin@zenfleet.dz')->first();
if ($superAdmin) {
    echo "👤 Attribution au Super Administrateur (ID: {$superAdmin->id})...\n";
    foreach ($requiredPermissions as $permissionName) {
        $superAdmin->givePermissionTo($permissionName);
        echo "   ✅ $permissionName attribuée\n";
    }
    echo "\n";
} else {
    echo "⚠️  Super Admin non trouvé\n\n";
}

// Attribuer à admin@zenfleet.dz
$admin = User::where('email', 'admin@zenfleet.dz')->first();
if ($admin) {
    echo "👤 Attribution à admin@zenfleet.dz (ID: {$admin->id})...\n";
    foreach ($requiredPermissions as $permissionName) {
        $admin->givePermissionTo($permissionName);
        echo "   ✅ $permissionName attribuée\n";
    }
    echo "\n";
} else {
    echo "⚠️  admin@zenfleet.dz non trouvé\n\n";
}

// Créer/vérifier les rôles et attribuer les permissions
echo "🏷️  Configuration des rôles...\n";

$roles = [
    'Super Admin' => ['view vehicles', 'create vehicles', 'update vehicles', 'delete vehicles'],
    'Admin' => ['view vehicles', 'create vehicles', 'update vehicles', 'delete vehicles'],
    'Gestionnaire Flotte' => ['view vehicles', 'create vehicles', 'update vehicles'],
    'Superviseur Transport' => ['view vehicles'],
];

foreach ($roles as $roleName => $permissions) {
    $role = Role::firstOrCreate([
        'name' => $roleName,
        'guard_name' => 'web'
    ]);
    
    $role->syncPermissions($permissions);
    echo "   ✅ $roleName: " . implode(', ', $permissions) . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Permissions corrigées avec succès !\n";
echo "🔄 Nettoyage du cache des permissions...\n";

// Nettoyer le cache des permissions
\Artisan::call('permission:cache-reset');

echo "✅ Cache nettoyé\n\n";

// Vérification finale
echo "🔍 Vérification finale:\n";
if ($superAdmin) {
    foreach ($requiredPermissions as $permission) {
        $hasPermission = $superAdmin->can($permission);
        $status = $hasPermission ? "✅" : "❌";
        echo "   $status Super Admin → $permission\n";
    }
}

echo "\n✅ Configuration terminée !\n\n";
