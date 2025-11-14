<?php

/**
 * 🛡️ CLI INTERACTIF DE GESTION DES PERMISSIONS UTILISATEUR
 * 
 * Script entreprise-grade pour gérer les permissions des utilisateurs
 * de manière simple et sécurisée.
 *
 * @author ZenFleet Architecture Team
 * @version 2.0.0
 */

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🛡️  GESTION DES PERMISSIONS UTILISATEUR - ZENFLEET                  ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";

// Menu principal
function showMainMenu() {
    echo "\n📋 MENU PRINCIPAL\n";
    echo str_repeat("─", 70) . "\n";
    echo "  1. 👤 Gérer les permissions d'un utilisateur\n";
    echo "  2. 👥 Attribuer un rôle à un utilisateur\n";
    echo "  3. 📊 Voir les permissions d'un utilisateur\n";
    echo "  4. 🔄 Synchroniser les permissions des affectations pour tous les admins\n";
    echo "  5. 📝 Lister tous les utilisateurs et leurs rôles\n";
    echo "  6. 🚀 Quick Fix: Donner toutes les permissions affectations à un utilisateur\n";
    echo "  0. ❌ Quitter\n";
    echo "\nVotre choix: ";
    
    $choice = trim(fgets(STDIN));
    return $choice;
}

// Fonction pour sélectionner un utilisateur
function selectUser($organizationId = null) {
    $query = User::query();
    
    if ($organizationId) {
        $query->where('organization_id', $organizationId);
    }
    
    $users = $query->orderBy('name')->get();
    
    echo "\n👥 SÉLECTION D'UTILISATEUR\n";
    echo str_repeat("─", 70) . "\n";
    
    foreach ($users as $index => $user) {
        $roles = $user->roles->pluck('name')->implode(', ') ?: 'Aucun rôle';
        echo sprintf("  %2d. %s (%s) - Rôles: %s\n", 
            $index + 1, 
            $user->name, 
            $user->email,
            $roles
        );
    }
    
    echo "\nSélectionnez un utilisateur (numéro): ";
    $choice = (int) trim(fgets(STDIN));
    
    if ($choice > 0 && $choice <= $users->count()) {
        return $users[$choice - 1];
    }
    
    echo "❌ Sélection invalide\n";
    return null;
}

// Fonction pour gérer les permissions d'un utilisateur
function manageUserPermissions() {
    $user = selectUser();
    if (!$user) return;
    
    echo "\n🔧 GESTION DES PERMISSIONS - {$user->name}\n";
    echo str_repeat("─", 70) . "\n";
    
    // Afficher les permissions actuelles
    $currentPermissions = $user->getAllPermissions();
    $assignmentPermissions = $currentPermissions->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    
    echo "\n📋 Permissions actuelles sur les affectations:\n";
    if ($assignmentPermissions->isEmpty()) {
        echo "  ❌ Aucune permission sur les affectations\n";
    } else {
        foreach ($assignmentPermissions as $perm) {
            echo "  ✅ {$perm->name}\n";
        }
    }
    
    echo "\n📦 Permissions disponibles pour les affectations:\n";
    $availablePermissions = Permission::where('name', 'LIKE', '%assignment%')
        ->orderBy('name')
        ->get();
    
    foreach ($availablePermissions as $index => $perm) {
        $hasIt = $user->hasPermissionTo($perm->name) ? '✅' : '⬜';
        echo sprintf("  %s %2d. %s\n", $hasIt, $index + 1, $perm->name);
    }
    
    echo "\nActions:\n";
    echo "  1. Ajouter une permission\n";
    echo "  2. Retirer une permission\n";
    echo "  3. Ajouter TOUTES les permissions affectations\n";
    echo "  4. Retour\n";
    echo "\nVotre choix: ";
    
    $action = trim(fgets(STDIN));
    
    switch ($action) {
        case '1':
            echo "Numéro de la permission à ajouter: ";
            $permIndex = (int) trim(fgets(STDIN)) - 1;
            if (isset($availablePermissions[$permIndex])) {
                $user->givePermissionTo($availablePermissions[$permIndex]->name);
                echo "✅ Permission ajoutée: {$availablePermissions[$permIndex]->name}\n";
            }
            break;
            
        case '2':
            echo "Numéro de la permission à retirer: ";
            $permIndex = (int) trim(fgets(STDIN)) - 1;
            if (isset($availablePermissions[$permIndex])) {
                $user->revokePermissionTo($availablePermissions[$permIndex]->name);
                echo "✅ Permission retirée: {$availablePermissions[$permIndex]->name}\n";
            }
            break;
            
        case '3':
            foreach ($availablePermissions as $perm) {
                $user->givePermissionTo($perm->name);
            }
            echo "✅ Toutes les permissions affectations ont été ajoutées\n";
            break;
    }
    
    // Nettoyer le cache
    Cache::forget('spatie.permission.cache');
}

// Fonction pour attribuer un rôle
function assignRole() {
    $user = selectUser();
    if (!$user) return;
    
    echo "\n👥 ATTRIBUTION DE RÔLE - {$user->name}\n";
    echo str_repeat("─", 70) . "\n";
    
    $currentRoles = $user->roles->pluck('name')->toArray();
    echo "\n📋 Rôles actuels: " . (empty($currentRoles) ? 'Aucun' : implode(', ', $currentRoles)) . "\n";
    
    $roles = Role::orderBy('name')->get();
    echo "\n📦 Rôles disponibles:\n";
    
    foreach ($roles as $index => $role) {
        $hasIt = in_array($role->name, $currentRoles) ? '✅' : '⬜';
        $permCount = $role->permissions->count();
        echo sprintf("  %s %2d. %s (%d permissions)\n", 
            $hasIt, 
            $index + 1, 
            $role->name,
            $permCount
        );
    }
    
    echo "\nActions:\n";
    echo "  1. Ajouter un rôle\n";
    echo "  2. Retirer un rôle\n";
    echo "  3. Remplacer tous les rôles\n";
    echo "  4. Retour\n";
    echo "\nVotre choix: ";
    
    $action = trim(fgets(STDIN));
    
    switch ($action) {
        case '1':
            echo "Numéro du rôle à ajouter: ";
            $roleIndex = (int) trim(fgets(STDIN)) - 1;
            if (isset($roles[$roleIndex])) {
                $user->assignRole($roles[$roleIndex]->name);
                echo "✅ Rôle ajouté: {$roles[$roleIndex]->name}\n";
            }
            break;
            
        case '2':
            echo "Numéro du rôle à retirer: ";
            $roleIndex = (int) trim(fgets(STDIN)) - 1;
            if (isset($roles[$roleIndex])) {
                $user->removeRole($roles[$roleIndex]->name);
                echo "✅ Rôle retiré: {$roles[$roleIndex]->name}\n";
            }
            break;
            
        case '3':
            echo "Numéro du rôle à attribuer (remplacera tous les autres): ";
            $roleIndex = (int) trim(fgets(STDIN)) - 1;
            if (isset($roles[$roleIndex])) {
                $user->syncRoles([$roles[$roleIndex]->name]);
                echo "✅ Rôles remplacés par: {$roles[$roleIndex]->name}\n";
            }
            break;
    }
    
    // Nettoyer le cache
    Cache::forget('spatie.permission.cache');
}

// Fonction pour voir les permissions d'un utilisateur
function viewUserPermissions() {
    $user = selectUser();
    if (!$user) return;
    
    echo "\n📊 PERMISSIONS DE {$user->name}\n";
    echo str_repeat("═", 70) . "\n";
    
    // Rôles
    echo "\n👥 RÔLES:\n";
    $roles = $user->roles;
    if ($roles->isEmpty()) {
        echo "  ❌ Aucun rôle\n";
    } else {
        foreach ($roles as $role) {
            echo "  • {$role->name}\n";
        }
    }
    
    // Permissions via rôles
    echo "\n🔐 PERMISSIONS VIA RÔLES:\n";
    $rolePermissions = $user->getPermissionsViaRoles();
    $roleAssignmentPerms = $rolePermissions->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    
    if ($roleAssignmentPerms->isEmpty()) {
        echo "  ❌ Aucune permission affectations via les rôles\n";
    } else {
        foreach ($roleAssignmentPerms as $perm) {
            echo "  • {$perm->name}\n";
        }
    }
    
    // Permissions directes
    echo "\n🔑 PERMISSIONS DIRECTES:\n";
    $directPermissions = $user->getDirectPermissions();
    $directAssignmentPerms = $directPermissions->filter(function($p) {
        return str_contains($p->name, 'assignment');
    });
    
    if ($directAssignmentPerms->isEmpty()) {
        echo "  ❌ Aucune permission directe sur les affectations\n";
    } else {
        foreach ($directAssignmentPerms as $perm) {
            echo "  • {$perm->name}\n";
        }
    }
    
    // Test des permissions critiques
    echo "\n✅ PERMISSIONS EFFECTIVES (test):\n";
    $testPermissions = [
        'view assignments',
        'create assignments',
        'edit assignments',
        'end assignments',
        'delete assignments'
    ];
    
    foreach ($testPermissions as $perm) {
        $hasIt = $user->can($perm);
        $icon = $hasIt ? '✅' : '❌';
        echo "  {$icon} {$perm}\n";
    }
    
    echo "\nAppuyez sur Entrée pour continuer...";
    fgets(STDIN);
}

// Fonction pour synchroniser les permissions des admins
function syncAdminPermissions() {
    echo "\n🔄 SYNCHRONISATION DES PERMISSIONS POUR TOUS LES ADMINS\n";
    echo str_repeat("─", 70) . "\n";
    
    $admins = User::role('Admin')->get();
    
    if ($admins->isEmpty()) {
        echo "❌ Aucun utilisateur avec le rôle Admin trouvé\n";
        return;
    }
    
    $assignmentPermissions = [
        'view assignments',
        'create assignments',
        'edit assignments',
        'end assignments',
        'delete assignments',
        'export assignments',
        'view assignment calendar',
        'view assignment gantt',
        'view assignment statistics'
    ];
    
    foreach ($admins as $admin) {
        echo "\n👤 {$admin->name} ({$admin->email}):\n";
        
        foreach ($assignmentPermissions as $perm) {
            if (!$admin->hasPermissionTo($perm)) {
                $admin->givePermissionTo($perm);
                echo "  ✅ Permission ajoutée: {$perm}\n";
            } else {
                echo "  ✓ Déjà présente: {$perm}\n";
            }
        }
    }
    
    // Nettoyer le cache
    Cache::forget('spatie.permission.cache');
    
    echo "\n✅ Synchronisation terminée pour " . $admins->count() . " admin(s)\n";
    echo "Appuyez sur Entrée pour continuer...";
    fgets(STDIN);
}

// Fonction pour lister tous les utilisateurs
function listAllUsers() {
    echo "\n📝 LISTE DES UTILISATEURS ET LEURS RÔLES\n";
    echo str_repeat("═", 70) . "\n";
    
    $users = User::with('roles')->orderBy('name')->get();
    
    foreach ($users as $user) {
        $roles = $user->roles->pluck('name')->implode(', ') ?: 'Aucun rôle';
        $assignmentPerms = $user->getAllPermissions()->filter(function($p) {
            return str_contains($p->name, 'assignment');
        })->count();
        
        echo sprintf("\n👤 %s (%s)\n", $user->name, $user->email);
        echo sprintf("   Rôles: %s\n", $roles);
        echo sprintf("   Permissions affectations: %d\n", $assignmentPerms);
    }
    
    echo "\nAppuyez sur Entrée pour continuer...";
    fgets(STDIN);
}

// Quick Fix pour un utilisateur
function quickFixUser() {
    $user = selectUser();
    if (!$user) return;
    
    echo "\n🚀 QUICK FIX - Attribution complète des permissions affectations\n";
    echo str_repeat("─", 70) . "\n";
    
    $permissions = [
        'view assignments',
        'create assignments',
        'edit assignments',
        'end assignments',
        'delete assignments',
        'export assignments',
        'extend assignments',
        'view assignment calendar',
        'view assignment gantt',
        'view assignment statistics',
        'assignments.view',
        'assignments.create',
        'assignments.edit',
        'assignments.delete',
        'assignments.end',
        'assignments.extend',
        'assignments.export',
        'assignments.view.calendar',
        'assignments.view.gantt',
        'assignments.view.statistics',
        'assignments.view.conflicts',
        'assignments.bulk.create',
        'assignments.bulk.update',
        'assignments.bulk.delete',
        'assignments.restore',
        'assignments.manage-all'
    ];
    
    echo "Attribution des permissions à {$user->name}:\n";
    
    foreach ($permissions as $perm) {
        if (Permission::where('name', $perm)->exists()) {
            $user->givePermissionTo($perm);
            echo "  ✅ {$perm}\n";
        }
    }
    
    // Nettoyer le cache
    Cache::forget('spatie.permission.cache');
    
    echo "\n✅ Toutes les permissions affectations ont été attribuées à {$user->name}\n";
    echo "L'utilisateur peut maintenant:\n";
    echo "  • Créer des affectations\n";
    echo "  • Modifier et terminer des affectations\n";
    echo "  • Voir toutes les vues (calendrier, Gantt, statistiques)\n";
    echo "  • Gérer les affectations en lot\n";
    
    echo "\nAppuyez sur Entrée pour continuer...";
    fgets(STDIN);
}

// Boucle principale
while (true) {
    $choice = showMainMenu();
    
    switch ($choice) {
        case '1':
            manageUserPermissions();
            break;
            
        case '2':
            assignRole();
            break;
            
        case '3':
            viewUserPermissions();
            break;
            
        case '4':
            syncAdminPermissions();
            break;
            
        case '5':
            listAllUsers();
            break;
            
        case '6':
            quickFixUser();
            break;
            
        case '0':
            echo "\n👋 Au revoir !\n\n";
            exit(0);
            
        default:
            echo "❌ Choix invalide\n";
    }
}
