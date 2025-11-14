<?php

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n🔍 ANALYSE DES PERMISSIONS ADMIN POUR LES AFFECTATIONS\n";
echo "=" . str_repeat("=", 60) . "\n";

// Récupérer l'utilisateur admin principal
$admin = User::whereEmail('admin@zenfleet.com')->first();

if (!$admin) {
    echo "❌ Utilisateur admin@zenfleet.com non trouvé !\n";
    
    // Chercher d'autres admins
    $admins = User::whereHas('roles', function($q) {
        $q->where('name', 'Admin');
    })->get();
    
    echo "\n📋 Utilisateurs avec le rôle Admin:\n";
    foreach ($admins as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
    
    if ($admins->isNotEmpty()) {
        $admin = $admins->first();
        echo "\n✅ Utilisation de {$admin->email} comme admin de référence\n";
    }
}

if ($admin) {
    echo "\n👤 Utilisateur Admin: {$admin->name} ({$admin->email})\n";
    echo "🏢 Organisation ID: {$admin->organization_id}\n";
    
    // Vérifier les rôles
    echo "\n📋 Rôles de l'utilisateur:\n";
    $roles = $admin->roles;
    foreach ($roles as $role) {
        echo "  - {$role->name} (ID: {$role->id})\n";
        
        // Permissions du rôle
        $rolePermissions = $role->permissions;
        $assignmentPerms = $rolePermissions->filter(function($p) {
            return str_contains($p->name, 'assignment');
        });
        
        if ($assignmentPerms->isNotEmpty()) {
            echo "    Permissions affectations du rôle:\n";
            foreach ($assignmentPerms as $perm) {
                echo "      • {$perm->name}\n";
            }
        }
    }
    
    // Permissions directes de l'utilisateur
    echo "\n📋 Permissions directes de l'utilisateur:\n";
    $directPerms = $admin->getDirectPermissions();
    $assignmentDirectPerms = $directPerms->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    
    if ($assignmentDirectPerms->isNotEmpty()) {
        foreach ($assignmentDirectPerms as $perm) {
            echo "  - {$perm->name}\n";
        }
    } else {
        echo "  Aucune permission directe sur les affectations\n";
    }
    
    // Toutes les permissions (rôles + directes)
    echo "\n📋 Toutes les permissions (combinées):\n";
    $allPerms = $admin->getAllPermissions();
    $allAssignmentPerms = $allPerms->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    
    if ($allAssignmentPerms->isNotEmpty()) {
        foreach ($allAssignmentPerms as $perm) {
            echo "  - {$perm->name}\n";
        }
    } else {
        echo "  ❌ AUCUNE permission sur les affectations !\n";
    }
    
    // Test des permissions spécifiques
    echo "\n🧪 Test des permissions critiques:\n";
    $criticalPerms = [
        'view assignments',
        'create assignments',
        'edit assignments',
        'end assignments',
        'delete assignments',
        'view assignment statistics'
    ];
    
    foreach ($criticalPerms as $perm) {
        $hasPermission = $admin->can($perm);
        $icon = $hasPermission ? '✅' : '❌';
        echo "  {$icon} {$perm}: " . ($hasPermission ? 'OUI' : 'NON') . "\n";
    }
}

// Vérifier les permissions existantes dans la DB
echo "\n📦 Permissions d'affectations dans la base de données:\n";
$assignmentPerms = Permission::where('name', 'LIKE', '%assignment%')
    ->orWhere('guard_name', 'LIKE', '%assignment%')
    ->orderBy('name')
    ->get();

if ($assignmentPerms->isEmpty()) {
    echo "  ❌ Aucune permission d'affectation trouvée dans la DB !\n";
} else {
    foreach ($assignmentPerms as $perm) {
        echo "  - {$perm->name} (guard: {$perm->guard_name})\n";
    }
}

// Vérifier la table spatie permissions
echo "\n🔍 Analyse de la table permissions:\n";
$count = DB::table('permissions')->count();
echo "  Total permissions: {$count}\n";

// Vérifier les rôles avec permissions d'affectations
echo "\n👥 Rôles avec permissions d'affectations:\n";
$rolesWithAssignmentPerms = Role::whereHas('permissions', function($q) {
    $q->where('name', 'LIKE', '%assignment%');
})->get();

foreach ($rolesWithAssignmentPerms as $role) {
    $assignmentPerms = $role->permissions->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    echo "  - {$role->name}: " . $assignmentPerms->count() . " permissions\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
