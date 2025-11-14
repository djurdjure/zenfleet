<?php

/**
 * ====================================================================
 * 🔍 DIAGNOSTIC PERMISSIONS 403 - ENTERPRISE GRADE
 * ====================================================================
 *
 * Analyse complète des permissions pour résoudre l'erreur 403
 * sur /admin/assignments/create
 *
 * @version 1.0-Enterprise-Grade
 * @since 2025-11-14
 * ====================================================================
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Assignment;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DIAGNOSTIC PERMISSIONS 403 - ASSIGNMENTS/CREATE        ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ================================================================
// ÉTAPE 1: Identifier l'utilisateur connecté (logs récents)
// ================================================================
echo "📋 ÉTAPE 1: Identification de l'utilisateur\n";
echo str_repeat("─", 66) . "\n";

// L'utilisateur ID 4 était dans les logs Laravel
$userId = 4;
$user = User::with('roles.permissions', 'permissions')->find($userId);

if (!$user) {
    echo "⚠️  Utilisateur ID {$userId} introuvable\n";
    echo "Recherche d'un utilisateur admin...\n\n";

    $user = User::whereHas('roles', function($q) {
        $q->where('name', 'Admin');
    })->with('roles.permissions', 'permissions')->first();

    if (!$user) {
        $user = User::with('roles.permissions', 'permissions')->first();
    }
}

if (!$user) {
    echo "❌ Aucun utilisateur trouvé dans la base de données\n";
    exit(1);
}

echo "✅ Utilisateur identifié:\n";
echo "  • ID: {$user->id}\n";
echo "  • Nom: {$user->name}\n";
echo "  • Email: {$user->email}\n";
echo "  • Organisation ID: {$user->organization_id}\n";
echo "\n";

// ================================================================
// ÉTAPE 2: Analyser les rôles
// ================================================================
echo "👑 ÉTAPE 2: Analyse des rôles\n";
echo str_repeat("─", 66) . "\n";

$userRoles = $user->roles;

if ($userRoles->isEmpty()) {
    echo "❌ PROBLÈME: Aucun rôle assigné à cet utilisateur!\n\n";
} else {
    echo "✅ Rôles assignés ({$userRoles->count()}):\n";
    foreach ($userRoles as $role) {
        echo "  • {$role->name} (ID: {$role->id})\n";
    }
    echo "\n";
}

// ================================================================
// ÉTAPE 3: Vérifier la permission 'create assignments'
// ================================================================
echo "🔐 ÉTAPE 3: Vérification permission 'create assignments'\n";
echo str_repeat("─", 66) . "\n";

$hasPermission = $user->can('create assignments');

if ($hasPermission) {
    echo "✅ L'utilisateur POSSÈDE la permission 'create assignments'\n";
    echo "  → L'erreur 403 ne devrait PAS se produire\n\n";
} else {
    echo "❌ L'utilisateur N'A PAS la permission 'create assignments'\n";
    echo "  → C'est LA CAUSE de l'erreur 403\n\n";
}

// ================================================================
// ÉTAPE 4: Lister TOUTES les permissions de l'utilisateur
// ================================================================
echo "📜 ÉTAPE 4: Permissions disponibles\n";
echo str_repeat("─", 66) . "\n";

$allPermissions = $user->getAllPermissions();

if ($allPermissions->isEmpty()) {
    echo "❌ Aucune permission disponible pour cet utilisateur\n\n";
} else {
    echo "✅ Permissions disponibles ({$allPermissions->count()}):\n";

    // Grouper par catégorie
    $byCategory = [];
    foreach ($allPermissions as $perm) {
        $parts = explode(' ', $perm->name);
        $action = $parts[0] ?? 'other';

        if (!isset($byCategory[$action])) {
            $byCategory[$action] = [];
        }
        $byCategory[$action][] = $perm->name;
    }

    foreach ($byCategory as $action => $perms) {
        echo "\n  📁 {$action}:\n";
        foreach ($perms as $p) {
            echo "     • {$p}\n";
        }
    }
    echo "\n";
}

// ================================================================
// ÉTAPE 5: Vérifier si la permission existe dans le système
// ================================================================
echo "🔎 ÉTAPE 5: Vérification existence de la permission\n";
echo str_repeat("─", 66) . "\n";

$permission = Permission::where('name', 'create assignments')->first();

if ($permission) {
    echo "✅ La permission 'create assignments' existe dans le système\n";
    echo "  • ID: {$permission->id}\n";
    echo "  • Guard: {$permission->guard_name}\n";

    // Vérifier quels rôles ont cette permission
    $rolesWithPerm = Role::whereHas('permissions', function($q) use ($permission) {
        $q->where('permissions.id', $permission->id);
    })->get();

    if ($rolesWithPerm->isNotEmpty()) {
        echo "\n  📋 Rôles ayant cette permission:\n";
        foreach ($rolesWithPerm as $role) {
            echo "     • {$role->name}\n";
        }
    } else {
        echo "\n  ⚠️  AUCUN rôle n'a cette permission!\n";
    }
    echo "\n";
} else {
    echo "❌ La permission 'create assignments' N'EXISTE PAS dans le système\n";
    echo "  → Elle doit être créée\n\n";
}

// ================================================================
// ÉTAPE 6: Analyser toutes les permissions liées aux assignments
// ================================================================
echo "📊 ÉTAPE 6: Permissions liées aux assignments\n";
echo str_repeat("─", 66) . "\n";

$assignmentPerms = Permission::where('name', 'like', '%assignment%')->get();

if ($assignmentPerms->isEmpty()) {
    echo "❌ Aucune permission 'assignment' trouvée\n\n";
} else {
    echo "✅ Permissions 'assignment' dans le système ({$assignmentPerms->count()}):\n";
    foreach ($assignmentPerms as $perm) {
        $hasIt = $user->can($perm->name) ? '✅' : '❌';
        echo "  {$hasIt} {$perm->name}\n";
    }
    echo "\n";
}

// ================================================================
// ÉTAPE 7: Vérifier la Policy
// ================================================================
echo "🛡️ ÉTAPE 7: Vérification Policy\n";
echo str_repeat("─", 66) . "\n";

try {
    $policyClass = 'App\Policies\AssignmentPolicy';

    if (class_exists($policyClass)) {
        echo "✅ AssignmentPolicy existe\n";

        $reflection = new ReflectionClass($policyClass);
        $createMethod = $reflection->getMethod('create');

        echo "  • Méthode create() présente: ✅\n";
        echo "  • Ligne: {$createMethod->getStartLine()}\n";

        // Lire le code source de la méthode
        $file = file($reflection->getFileName());
        $startLine = $createMethod->getStartLine() - 1;
        $endLine = $createMethod->getEndLine();
        $methodCode = implode('', array_slice($file, $startLine, $endLine - $startLine));

        echo "\n  📄 Code de la méthode:\n";
        echo "  " . str_replace("\n", "\n  ", trim($methodCode)) . "\n\n";
    } else {
        echo "❌ AssignmentPolicy introuvable\n\n";
    }
} catch (Exception $e) {
    echo "⚠️  Erreur lors de la vérification: {$e->getMessage()}\n\n";
}

// ================================================================
// RÉSUMÉ ET RECOMMANDATIONS
// ================================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  📊 RÉSUMÉ DIAGNOSTIC                                       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$issues = [];
$solutions = [];

if ($userRoles->isEmpty()) {
    $issues[] = "L'utilisateur n'a AUCUN rôle";
    $solutions[] = "Assigner un rôle (Admin, Gestionnaire Flotte, etc.)";
}

if (!$hasPermission) {
    $issues[] = "Permission 'create assignments' manquante";

    if (!$permission) {
        $solutions[] = "1. Créer la permission 'create assignments'";
        $solutions[] = "2. L'assigner aux rôles appropriés";
    } else {
        $solutions[] = "Assigner la permission 'create assignments' au rôle de l'utilisateur";
    }
}

if (!empty($issues)) {
    echo "❌ PROBLÈMES IDENTIFIÉS:\n";
    foreach ($issues as $i => $issue) {
        echo "  " . ($i + 1) . ". {$issue}\n";
    }
    echo "\n";

    echo "✅ SOLUTIONS RECOMMANDÉES:\n";
    foreach ($solutions as $i => $solution) {
        echo "  " . ($i + 1) . ". {$solution}\n";
    }
    echo "\n";
} else {
    echo "✅ Aucun problème détecté - l'utilisateur DEVRAIT avoir accès\n";
    echo "  → Vérifier le cache des permissions ou la session\n\n";
}

// ================================================================
// SCRIPT DE CORRECTION AUTOMATIQUE
// ================================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔧 PROPOSITION DE CORRECTION AUTOMATIQUE                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (!$hasPermission) {
    echo "Je peux corriger automatiquement le problème:\n\n";
    echo "  1. Créer la permission 'create assignments' (si manquante)\n";
    echo "  2. L'assigner au rôle de l'utilisateur\n";
    echo "  3. Nettoyer le cache des permissions\n\n";

    echo "Voulez-vous exécuter la correction? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim($line) === 'y' || trim($line) === 'Y') {
        echo "\n🔧 Exécution de la correction...\n";
        echo str_repeat("─", 66) . "\n";

        // Créer la permission si manquante
        if (!$permission) {
            $permission = Permission::create([
                'name' => 'create assignments',
                'guard_name' => 'web'
            ]);
            echo "✅ Permission 'create assignments' créée\n";
        }

        // Assigner au rôle de l'utilisateur
        foreach ($userRoles as $role) {
            if (!$role->hasPermissionTo('create assignments')) {
                $role->givePermissionTo('create assignments');
                echo "✅ Permission assignée au rôle '{$role->name}'\n";
            }
        }

        // Nettoyer le cache
        Artisan::call('cache:clear');
        Artisan::call('permission:cache-reset');
        echo "✅ Cache des permissions nettoyé\n\n";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║  ✅ CORRECTION TERMINÉE AVEC SUCCÈS                        ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "🎯 Essayez maintenant d'accéder à:\n";
        echo "   http://localhost/admin/assignments/create\n\n";
    } else {
        echo "\n⏭️  Correction annulée\n\n";
    }
} else {
    echo "✅ L'utilisateur a déjà la permission nécessaire\n";
    echo "   Le problème peut être lié au cache.\n\n";
    echo "Commandes à exécuter:\n";
    echo "  docker exec zenfleet_php php artisan cache:clear\n";
    echo "  docker exec zenfleet_php php artisan permission:cache-reset\n\n";
}

exit(0);
