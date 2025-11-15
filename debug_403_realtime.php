<?php

/**
 * ====================================================================
 * 🔍 DEBUG 403 EN TEMPS RÉEL - DIAGNOSTIC ULTRA-DÉTAILLÉ
 * ====================================================================
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Assignment;
use App\Policies\AssignmentPolicy;
use Spatie\Permission\Models\Permission;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DEBUG 403 EN TEMPS RÉEL                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ================================================================
// ÉTAPE 1: Trouver l'utilisateur qui se connecte
// ================================================================
echo "👤 ÉTAPE 1: Identification utilisateur\n";
echo str_repeat("─", 66) . "\n";

$user = User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur admin@zenfleet.dz INTROUVABLE\n";
    exit(1);
}

echo "✅ Utilisateur trouvé:\n";
echo "  • ID: {$user->id}\n";
echo "  • Nom: {$user->name}\n";
echo "  • Email: {$user->email}\n";
echo "  • Organization ID: {$user->organization_id}\n";
echo "\n";

// ================================================================
// ÉTAPE 2: Vérifier les rôles
// ================================================================
echo "👑 ÉTAPE 2: Rôles de l'utilisateur\n";
echo str_repeat("─", 66) . "\n";

$roles = $user->roles;
if ($roles->isEmpty()) {
    echo "❌ AUCUN RÔLE ASSIGNÉ !\n\n";
} else {
    echo "✅ Rôles ({$roles->count()}):\n";
    foreach ($roles as $role) {
        echo "  • {$role->name}\n";
    }
    echo "\n";
}

// ================================================================
// ÉTAPE 3: Test EXACT de la Policy
// ================================================================
echo "🛡️ ÉTAPE 3: Test EXACT de la Policy create()\n";
echo str_repeat("─", 66) . "\n";

$policy = new AssignmentPolicy();

echo "📝 Code actuel de la Policy (ligne 45-46):\n";
echo "  return \$user->can('assignments.create') ||\n";
echo "         \$user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']);\n\n";

// Test 1: Vérifier la permission
echo "TEST 1: \$user->can('assignments.create')\n";
$hasPermission = $user->can('assignments.create');
echo "  Résultat: " . ($hasPermission ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// Test 2: Vérifier les rôles
echo "TEST 2: \$user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte'])\n";
$hasRole = $user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']);
echo "  Résultat: " . ($hasRole ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// Test 3: Résultat combiné (OR)
$policyResult = $hasPermission || $hasRole;
echo "TEST 3: Résultat combiné (permission OR rôle)\n";
echo "  Résultat: " . ($policyResult ? '✅ TRUE' : '❌ FALSE') . "\n\n";

// Test 4: Appeler directement la Policy
echo "TEST 4: Appel direct Policy->create(\$user)\n";
try {
    $directResult = $policy->create($user);
    echo "  Résultat: " . ($directResult ? '✅ TRUE' : '❌ FALSE') . "\n";
} catch (Exception $e) {
    echo "  ❌ ERREUR: {$e->getMessage()}\n";
}
echo "\n";

// ================================================================
// ÉTAPE 4: Vérifier les permissions en détail
// ================================================================
echo "🔐 ÉTAPE 4: Analyse détaillée des permissions\n";
echo str_repeat("─", 66) . "\n";

$perm1 = Permission::where('name', 'create assignments')->first();
$perm2 = Permission::where('name', 'assignments.create')->first();

echo "Permission 'create assignments':\n";
if ($perm1) {
    $has1 = $user->hasPermissionTo($perm1);
    echo "  • Existe: ✅ (ID: {$perm1->id})\n";
    echo "  • User l'a: " . ($has1 ? '✅' : '❌') . "\n";
} else {
    echo "  • Existe: ❌\n";
}
echo "\n";

echo "Permission 'assignments.create':\n";
if ($perm2) {
    $has2 = $user->hasPermissionTo($perm2);
    echo "  • Existe: ✅ (ID: {$perm2->id})\n";
    echo "  • User l'a: " . ($has2 ? '✅' : '❌') . "\n";
} else {
    echo "  • Existe: ❌\n";
}
echo "\n";

// ================================================================
// ÉTAPE 5: Vérifier les rôles en détail
// ================================================================
echo "👥 ÉTAPE 5: Détail des rôles\n";
echo str_repeat("─", 66) . "\n";

$rolesToCheck = ['Super Admin', 'Admin', 'Gestionnaire Flotte'];

foreach ($rolesToCheck as $roleName) {
    $hasThisRole = $user->hasRole($roleName);
    echo "  • Rôle '{$roleName}': " . ($hasThisRole ? '✅ OUI' : '❌ NON') . "\n";
}
echo "\n";

// ================================================================
// ÉTAPE 6: Simuler l'autorisation Livewire
// ================================================================
echo "⚡ ÉTAPE 6: Simulation authorize() Livewire\n";
echo str_repeat("─", 66) . "\n";

echo "Code Livewire (ligne 84):\n";
echo "  \$this->authorize('create', Assignment::class);\n\n";

try {
    // Simuler ce que fait Livewire
    $gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
    $gate->forUser($user);

    $canCreate = $gate->allows('create', Assignment::class);

    echo "Résultat Gate::allows('create', Assignment::class):\n";
    echo "  " . ($canCreate ? '✅ AUTORISÉ' : '❌ REFUSÉ') . "\n";

    if (!$canCreate) {
        echo "\n❌ C'EST ICI QUE L'ERREUR 403 SE PRODUIT\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR: {$e->getMessage()}\n";
}
echo "\n";

// ================================================================
// RÉSUMÉ ET DIAGNOSTIC
// ================================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  📊 RÉSUMÉ DIAGNOSTIC                                       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($policyResult) {
    echo "✅ La Policy retourne TRUE\n";
    echo "✅ L'utilisateur DEVRAIT avoir accès\n\n";

    echo "🔍 Mais l'erreur 403 persiste, cela signifie:\n";
    echo "  1. Cache OPcache PHP non vidé\n";
    echo "  2. Plusieurs instances PHP (PHP-FPM workers)\n";
    echo "  3. Problème de session/authentification\n";
    echo "  4. Middleware bloquant avant la Policy\n\n";
} else {
    echo "❌ La Policy retourne FALSE\n";
    echo "❌ C'est NORMAL que l'erreur 403 se produise\n\n";

    echo "🔧 SOLUTION:\n";
    if (!$hasPermission && !$hasRole) {
        echo "  • Ni la permission ni le rôle ne sont présents\n";
        echo "  • Assigner le rôle 'Admin' ou la permission 'assignments.create'\n";
    } elseif (!$hasPermission) {
        echo "  • La permission 'assignments.create' manque\n";
        echo "  • Mais le fallback rôle devrait fonctionner...\n";
        echo "  • Problème de cache ou de vérification hasRole()\n";
    } elseif (!$hasRole) {
        echo "  • Le rôle Admin/Gestionnaire manque\n";
        echo "  • Mais la permission devrait fonctionner...\n";
        echo "  • Problème de cache ou de vérification can()\n";
    }
}

exit(0);
