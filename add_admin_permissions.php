<?php

/**
 * Script pour ajouter les permissions manquantes au rôle Admin
 * Usage: docker compose exec -u zenfleet_user php php add_admin_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 AJOUT DES PERMISSIONS AU RÔLE ADMIN\n";
echo str_repeat("=", 70) . "\n\n";

$adminRole = Spatie\Permission\Models\Role::where('name', 'Admin')->first();

if (!$adminRole) {
    echo "❌ Rôle Admin introuvable\n";
    exit(1);
}

echo "✅ Rôle Admin trouvé (ID: {$adminRole->id})\n";
echo "   Permissions actuelles: {$adminRole->permissions->count()}\n\n";

// Permissions à ajouter pour un Admin d'organisation
$permissionsToAdd = [
    'end assignments',       // Terminer les affectations
    'export suppliers',      // Exporter les fournisseurs
    'view audit logs',       // Voir les logs d'audit de son organisation
    // Note: view/edit organizations NON ajouté car un Admin ne devrait voir QUE sa propre org
];

echo "📋 Ajout des permissions:\n";
echo str_repeat("-", 70) . "\n";

$added = 0;
$skipped = 0;

foreach ($permissionsToAdd as $permName) {
    $permission = Spatie\Permission\Models\Permission::where('name', $permName)->first();

    if (!$permission) {
        echo "  ⚠️  Permission '{$permName}' n'existe pas, création...\n";
        $permission = Spatie\Permission\Models\Permission::create(['name' => $permName]);
    }

    if (!$adminRole->hasPermissionTo($permission)) {
        $adminRole->givePermissionTo($permission);
        echo "  ✅ Ajouté: {$permName}\n";
        $added++;
    } else {
        echo "  ⏭️  Déjà présent: {$permName}\n";
        $skipped++;
    }
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 RÉSUMÉ:\n";
echo str_repeat("=", 70) . "\n";
echo "Permissions ajoutées: {$added}\n";
echo "Permissions déjà présentes: {$skipped}\n";
echo "Total permissions Admin: {$adminRole->permissions()->count()}\n\n";

// Maintenant, faisons la même chose pour Gestionnaire Flotte et Superviseur
echo "\n🔧 MISE À JOUR DES AUTRES RÔLES\n";
echo str_repeat("=", 70) . "\n\n";

// Gestionnaire Flotte - devrait avoir les mêmes droits qu'Admin sauf users
$gestionnaireRole = Spatie\Permission\Models\Role::where('name', 'Gestionnaire Flotte')->first();
if ($gestionnaireRole) {
    echo "✅ Rôle Gestionnaire Flotte trouvé\n";
    echo "   Permissions actuelles: {$gestionnaireRole->permissions->count()}\n";

    $gestionnairePerms = [
        'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'import vehicles',
        'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'import drivers',
        'view assignments', 'create assignments', 'edit assignments', 'delete assignments', 'end assignments',
        'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers', 'export suppliers',
        'view dashboard', 'view reports', 'view assignment statistics',
    ];

    foreach ($gestionnairePerms as $permName) {
        $perm = Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName]);
        if (!$gestionnaireRole->hasPermissionTo($perm)) {
            $gestionnaireRole->givePermissionTo($perm);
        }
    }
    echo "   ✅ Permissions mises à jour: {$gestionnaireRole->permissions()->count()}\n\n";
}

// Superviseur - droits en lecture principalement
$superviseurRole = Spatie\Permission\Models\Role::where('name', 'Superviseur')->first();
if ($superviseurRole) {
    echo "✅ Rôle Superviseur trouvé\n";
    echo "   Permissions actuelles: {$superviseurRole->permissions->count()}\n";

    $superviseurPerms = [
        'view vehicles', 'view drivers', 'view assignments', 'view suppliers',
        'view dashboard', 'view reports', 'view assignment statistics',
        'create assignments', 'edit assignments', 'end assignments', // Peut gérer les affectations
    ];

    foreach ($superviseurPerms as $permName) {
        $perm = Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName]);
        if (!$superviseurRole->hasPermissionTo($perm)) {
            $superviseurRole->givePermissionTo($perm);
        }
    }
    echo "   ✅ Permissions mises à jour: {$superviseurRole->permissions()->count()}\n\n";
}

echo "\n✨ TERMINÉ!\n\n";
