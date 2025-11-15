<?php

/**
 * ====================================================================
 * 🔧 FIX PERMISSIONS - assignments.create
 * ====================================================================
 *
 * Corrige l'incohérence de permission entre 'create assignments' et
 * 'assignments.create' en s'assurant que la permission granulaire
 * existe et est assignée aux bons rôles.
 *
 * @version 1.0-Enterprise-Grade
 * @since 2025-11-14
 * ====================================================================
 */

require __DIR__ . '/vendor/autoload.php';

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔧 FIX PERMISSIONS - assignments.create                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$fixed = false;

// ================================================================
// ÉTAPE 1: Vérifier si la permission existe
// ================================================================
echo "📋 ÉTAPE 1: Vérification permission 'assignments.create'\n";
echo str_repeat("─", 66) . "\n";

$permission = Permission::where('name', 'assignments.create')->first();

if (!$permission) {
    echo "❌ Permission 'assignments.create' N'EXISTE PAS\n";
    echo "✅ Création de la permission...\n";

    $permission = Permission::create([
        'name' => 'assignments.create',
        'guard_name' => 'web'
    ]);

    echo "✅ Permission 'assignments.create' créée (ID: {$permission->id})\n";
    $fixed = true;
} else {
    echo "✅ Permission 'assignments.create' existe (ID: {$permission->id})\n";
}
echo "\n";

// ================================================================
// ÉTAPE 2: Assigner aux rôles appropriés
// ================================================================
echo "👑 ÉTAPE 2: Attribution aux rôles\n";
echo str_repeat("─", 66) . "\n";

$rolesToAssign = ['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Superviseur'];

foreach ($rolesToAssign as $roleName) {
    $role = Role::where('name', $roleName)->first();

    if (!$role) {
        echo "⚠️  Rôle '{$roleName}' introuvable - SKIP\n";
        continue;
    }

    if ($role->hasPermissionTo('assignments.create')) {
        echo "✅ Rôle '{$roleName}' a déjà la permission\n";
    } else {
        $role->givePermissionTo('assignments.create');
        echo "✅ Permission assignée au rôle '{$roleName}'\n";
        $fixed = true;
    }
}
echo "\n";

// ================================================================
// ÉTAPE 3: Vérifier l'utilisateur Admin
// ================================================================
echo "👤 ÉTAPE 3: Vérification utilisateur Admin\n";
echo str_repeat("─", 66) . "\n";

$user = \App\Models\User::find(4); // admin@zenfleet.dz

if ($user) {
    echo "✅ Utilisateur: {$user->name} ({$user->email})\n";

    $hasOldPermission = $user->can('create assignments');
    $hasNewPermission = $user->can('assignments.create');

    echo "  • Permission 'create assignments': " . ($hasOldPermission ? '✅' : '❌') . "\n";
    echo "  • Permission 'assignments.create': " . ($hasNewPermission ? '✅' : '❌') . "\n";

    if (!$hasNewPermission) {
        echo "\n⚠️  L'utilisateur n'a pas encore 'assignments.create'\n";
        echo "  → Cela sera corrigé après le cache clear\n";
    }
} else {
    echo "⚠️  Utilisateur ID 4 introuvable\n";
}
echo "\n";

// ================================================================
// ÉTAPE 4: Nettoyer les caches
// ================================================================
if ($fixed) {
    echo "🧹 ÉTAPE 4: Nettoyage des caches\n";
    echo str_repeat("─", 66) . "\n";

    Artisan::call('permission:cache-reset');
    echo "✅ Cache des permissions réinitialisé\n";

    Artisan::call('cache:clear');
    echo "✅ Cache applicatif nettoyé\n";

    echo "\n";
}

// ================================================================
// RÉSUMÉ
// ================================================================
echo "╔══════════════════════════════════════════════════════════════╗\n";
if ($fixed) {
    echo "║  ✅ CORRECTIONS APPLIQUÉES AVEC SUCCÈS                     ║\n";
} else {
    echo "║  ✅ AUCUNE CORRECTION NÉCESSAIRE - DÉJÀ OK                 ║\n";
}
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "📊 Actions effectuées:\n";
if ($fixed) {
    echo "  • Permission 'assignments.create' créée ou vérifiée\n";
    echo "  • Assignée aux rôles: " . implode(', ', $rolesToAssign) . "\n";
    echo "  • Caches nettoyés\n\n";

    echo "⚠️  IMPORTANT: Reconnexion requise pour l'utilisateur\n";
    echo "  → Déconnectez-vous et reconnectez-vous à http://localhost/login\n\n";
} else {
    echo "  • Toutes les permissions déjà configurées correctement\n\n";
}

echo "🎯 PROCHAINE ÉTAPE:\n";
echo "  1. Reconnectez-vous à l'application\n";
echo "  2. Testez: http://localhost/admin/assignments/create\n";
echo "  3. La page devrait se charger sans erreur 403\n\n";

exit(0);
