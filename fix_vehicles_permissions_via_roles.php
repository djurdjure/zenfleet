<?php

/*
 * 🔧 Script de Correction des Permissions Véhicules via Rôles
 * Pour système multi-tenant avec organization_id
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "\n🔧 CORRECTION DES PERMISSIONS VÉHICULES (VIA RÔLES)\n";
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
    echo "   ✅ $permissionName (ID: {$permission->id})\n";
}

echo "\n";

// Récupérer ou créer les rôles
echo "🏷️  Configuration des rôles et permissions...\n\n";

$rolePermissions = [
    'Super Admin' => ['view vehicles', 'create vehicles', 'update vehicles', 'delete vehicles'],
    'Admin' => ['view vehicles', 'create vehicles', 'update vehicles', 'delete vehicles'],
    'Gestionnaire Flotte' => ['view vehicles', 'create vehicles', 'update vehicles'],
    'Superviseur' => ['view vehicles'],
];

foreach ($rolePermissions as $roleName => $permissions) {
    $role = Role::where('name', $roleName)->first();
    
    if ($role) {
        echo "   📌 Rôle '$roleName' trouvé (ID: {$role->id})\n";
        
        // Attribuer les permissions au rôle
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$role->hasPermissionTo($permissionName)) {
                try {
                    $role->givePermissionTo($permissionName);
                    echo "      ✅ Permission '$permissionName' ajoutée\n";
                } catch (\Exception $e) {
                    echo "      ⚠️  Erreur: {$e->getMessage()}\n";
                }
            } else {
                echo "      ℹ️  Permission '$permissionName' déjà présente\n";
            }
        }
        
        echo "\n";
    } else {
        echo "   ⚠️  Rôle '$roleName' introuvable\n\n";
    }
}

// Assigner les rôles aux utilisateurs clés
echo "👤 Attribution des rôles aux utilisateurs...\n\n";

$userRoles = [
    'superadmin@zenfleet.dz' => 'Super Admin',
    'admin@zenfleet.dz' => 'Admin',
];

foreach ($userRoles as $email => $roleName) {
    $user = User::where('email', $email)->first();
    $role = Role::where('name', $roleName)->first();
    
    if ($user && $role) {
        if (!$user->hasRole($roleName)) {
            $user->assignRole($role);
            echo "   ✅ Rôle '$roleName' assigné à $email\n";
        } else {
            echo "   ℹ️  $email a déjà le rôle '$roleName'\n";
        }
    } else {
        if (!$user) echo "   ⚠️  Utilisateur $email introuvable\n";
        if (!$role) echo "   ⚠️  Rôle '$roleName' introuvable\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "🔄 Nettoyage du cache des permissions...\n";

\Artisan::call('permission:cache-reset');

echo "✅ Cache nettoyé\n\n";

// Vérification finale
echo "🔍 Vérification finale:\n\n";

$testUsers = [
    'superadmin@zenfleet.dz',
    'admin@zenfleet.dz',
];

foreach ($testUsers as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "👤 $email:\n";
        echo "   Rôles: " . $user->roles->pluck('name')->join(', ') . "\n";
        foreach ($requiredPermissions as $permission) {
            $hasPermission = $user->can($permission);
            $status = $hasPermission ? "✅" : "❌";
            echo "   $status $permission\n";
        }
        echo "\n";
    }
}

echo "✅ Configuration terminée !\n\n";
