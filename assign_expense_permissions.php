#!/usr/bin/env php
<?php

/**
 * Script pour attribuer les permissions du module de dépenses aux rôles
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🔐 ATTRIBUTION DES PERMISSIONS DU MODULE DE DÉPENSES\n";
echo str_repeat("=", 80) . "\n\n";

DB::beginTransaction();

try {
    // Créer les permissions si elles n'existent pas
    $permissions = [
        'view expenses',
        'create expenses',
        'edit expenses',
        'delete expenses',
        'approve expenses',
        'export expenses',
        'view expense analytics',
    ];
    
    echo "📋 Création/vérification des permissions:\n";
    foreach ($permissions as $permission) {
        $perm = Permission::firstOrCreate(['name' => $permission]);
        echo "✅ Permission '$permission' " . ($perm->wasRecentlyCreated ? 'créée' : 'existante') . "\n";
    }
    echo "\n";
    
    // Attribuer au rôle Super Admin
    $superAdminRole = Role::where('name', 'Super Admin')->first();
    if ($superAdminRole) {
        $superAdminRole->syncPermissions(Permission::all());
        echo "✅ Toutes les permissions attribuées au rôle 'Super Admin'\n";
    }
    
    // Attribuer au rôle Admin
    $adminRole = Role::where('name', 'Admin')->first();
    if ($adminRole) {
        $adminRole->givePermissionTo($permissions);
        echo "✅ Permissions de dépenses attribuées au rôle 'Admin'\n";
    }
    
    // Attribuer au rôle Fleet Manager
    $fleetManagerRole = Role::where('name', 'Fleet Manager')->first();
    if ($fleetManagerRole) {
        $fleetManagerRole->givePermissionTo([
            'view expenses',
            'create expenses',
            'edit expenses',
            'approve expenses',
            'export expenses',
            'view expense analytics'
        ]);
        echo "✅ Permissions de dépenses attribuées au rôle 'Fleet Manager'\n";
    }
    
    // Attribuer au rôle Finance
    $financeRole = Role::where('name', 'Finance')->first();
    if (!$financeRole) {
        $financeRole = Role::create(['name' => 'Finance']);
        echo "✅ Rôle 'Finance' créé\n";
    }
    $financeRole->givePermissionTo($permissions);
    echo "✅ Permissions de dépenses attribuées au rôle 'Finance'\n\n";
    
    // Attribuer le rôle Super Admin à l'utilisateur admin principal
    $adminUser = User::where('email', 'admin@zenfleet.dz')->first();
    if ($adminUser) {
        if (!$adminUser->hasRole('Super Admin')) {
            // Définir l'organisation_id pour l'attribution du rôle
            if ($adminUser->organization_id) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $superAdminRole->id,
                    'model_type' => User::class,
                    'model_id' => $adminUser->id,
                    'organization_id' => $adminUser->organization_id
                ]);
                echo "✅ Rôle 'Super Admin' attribué à " . $adminUser->email . "\n";
            } else {
                // Si pas d'organization_id, attribuer directement les permissions
                $adminUser->givePermissionTo($permissions);
                echo "✅ Permissions directement attribuées à " . $adminUser->email . " (pas d'organisation)\n";
            }
        } else {
            echo "ℹ️ L'utilisateur " . $adminUser->email . " a déjà le rôle 'Super Admin'\n";
        }
    }
    
    // Vérification des permissions de l'admin
    if ($adminUser) {
        echo "\n📊 Permissions de l'utilisateur admin:\n";
        foreach ($permissions as $permission) {
            if ($adminUser->can($permission)) {
                echo "✅ $permission\n";
            } else {
                echo "❌ $permission\n";
            }
        }
    }
    
    DB::commit();
    echo "\n🎉 SUCCÈS! Les permissions ont été attribuées avec succès.\n";
    
} catch (\Exception $e) {
    DB::rollback();
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}

echo str_repeat("=", 80) . "\n\n";
