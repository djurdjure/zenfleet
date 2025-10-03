#!/usr/bin/env php
<?php

/**
 * 🔐 SCRIPT DE TEST - Permissions Utilisateur Admin
 *
 * Vérifie les permissions de l'utilisateur admin@faderco.dz
 * Compatible Docker et CLI standard
 *
 * @version 1.0-Enterprise
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  🔐 TEST DES PERMISSIONS - UTILISATEUR ADMIN               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    // Rechercher l'utilisateur admin
    echo "🔍 Recherche de l'utilisateur admin@faderco.dz...\n\n";

    $admin = \App\Models\User::where('email', 'admin@faderco.dz')->first();

    if (!$admin) {
        echo "❌ ERREUR: Utilisateur admin@faderco.dz non trouvé!\n";
        echo "\n";
        echo "💡 Vérifiez que l'utilisateur existe:\n";
        echo "   php artisan tinker\n";
        echo "   >>> App\\Models\\User::where('email', 'like', '%admin%')->get(['id', 'email']);\n";
        echo "\n";
        exit(1);
    }

    echo "✅ Utilisateur trouvé:\n";
    echo "   ID:             {$admin->id}\n";
    echo "   Nom:            {$admin->name}\n";
    echo "   Email:          {$admin->email}\n";
    echo "   Organisation:   " . ($admin->organization_id ?? 'N/A') . "\n";
    echo "\n";

    // Récupérer les rôles
    echo "─────────────────────────────────────────────────────────────\n";
    echo "👑 RÔLES ASSIGNÉS\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $roles = $admin->getRoleNames();

    if ($roles->isEmpty()) {
        echo "   ⚠️  Aucun rôle assigné\n";
    } else {
        foreach ($roles as $role) {
            echo "   ✅ {$role}\n";
        }
    }

    echo "\n";

    // Récupérer toutes les permissions
    echo "─────────────────────────────────────────────────────────────\n";
    echo "🔑 PERMISSIONS DÉTAILLÉES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $allPermissions = $admin->getAllPermissions();

    if ($allPermissions->isEmpty()) {
        echo "   ⚠️  Aucune permission assignée\n\n";
    } else {
        echo "   Total: {$allPermissions->count()} permission(s)\n\n";

        // Grouper par catégorie
        $permissionsByCategory = [];

        foreach ($allPermissions as $permission) {
            $parts = explode(' ', $permission->name);
            $category = count($parts) > 1 ? $parts[1] : 'Autres';
            $action = $parts[0] ?? $permission->name;

            if (!isset($permissionsByCategory[$category])) {
                $permissionsByCategory[$category] = [];
            }

            $permissionsByCategory[$category][] = [
                'name' => $permission->name,
                'action' => $action
            ];
        }

        foreach ($permissionsByCategory as $category => $permissions) {
            echo "   📁 " . ucfirst($category) . " (" . count($permissions) . "):\n";

            foreach ($permissions as $perm) {
                echo "      • {$perm['name']}\n";
            }

            echo "\n";
        }
    }

    // Vérifier les permissions critiques
    echo "─────────────────────────────────────────────────────────────\n";
    echo "🎯 VÉRIFICATION DES PERMISSIONS CRITIQUES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $criticalPermissions = [
        'create vehicles' => '📦 Création de véhicules',
        'edit vehicles' => '✏️  Modification de véhicules',
        'delete vehicles' => '🗑️  Suppression de véhicules',
        'view vehicles' => '👁️  Consultation de véhicules',
        'create drivers' => '👤 Création de chauffeurs',
        'edit drivers' => '✏️  Modification de chauffeurs',
        'delete drivers' => '🗑️  Suppression de chauffeurs',
        'view drivers' => '👁️  Consultation de chauffeurs',
        'create assignments' => '🔄 Création d\'affectations',
        'view assignments' => '👁️  Consultation d\'affectations',
        'manage roles' => '👑 Gestion des rôles',
    ];

    $missingPermissions = [];
    $hasPermissions = [];

    foreach ($criticalPermissions as $permission => $description) {
        if ($admin->can($permission)) {
            $hasPermissions[] = $permission;
            echo "   ✅ {$description}: OUI\n";
        } else {
            $missingPermissions[] = $permission;
            echo "   ❌ {$description}: NON\n";
        }
    }

    echo "\n";

    // Statistiques
    echo "─────────────────────────────────────────────────────────────\n";
    echo "📊 STATISTIQUES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $totalCritical = count($criticalPermissions);
    $totalHas = count($hasPermissions);
    $percentage = $totalCritical > 0 ? round(($totalHas / $totalCritical) * 100, 1) : 0;

    echo "   Permissions critiques:    {$totalHas} / {$totalCritical} ({$percentage}%)\n";
    echo "   Total permissions:        {$allPermissions->count()}\n";
    echo "   Rôles actifs:             {$roles->count()}\n";

    echo "\n";

    // Test spécifique d'import
    echo "─────────────────────────────────────────────────────────────\n";
    echo "🚗 TEST SPÉCIFIQUE: IMPORT DE VÉHICULES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $canCreateVehicles = $admin->can('create vehicles');

    if ($canCreateVehicles) {
        echo "   ✅ L'utilisateur peut importer des véhicules\n";
        echo "   ✅ L'erreur 403 devrait être résolue\n";
    } else {
        echo "   ❌ L'utilisateur NE PEUT PAS importer des véhicules\n";
        echo "   ❌ L'erreur 403 persistera\n\n";
        echo "   💡 SOLUTION:\n";
        echo "      1. Aller sur: /admin/roles\n";
        echo "      2. Trouver le rôle de l'admin (ex: Admin, Super Admin)\n";
        echo "      3. Assigner la permission 'create vehicles'\n";
        echo "      OU exécuter:\n";
        echo "      php artisan tinker\n";
        echo "      >>> \$admin = User::find({$admin->id});\n";
        echo "      >>> \$admin->givePermissionTo('create vehicles');\n";
    }

    echo "\n";

    // Conclusion
    echo "─────────────────────────────────────────────────────────────\n";

    if ($canCreateVehicles && !$missingPermissions) {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║  ✅ TOUTES LES PERMISSIONS SONT CORRECTES!                 ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "🎉 L'utilisateur admin@faderco.dz a toutes les permissions nécessaires.\n";
        echo "   L'importation de véhicules devrait fonctionner sans erreur 403.\n";
    } elseif ($canCreateVehicles) {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║  ⚠️  PERMISSIONS PARTIELLES                                 ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "✅ L'import de véhicules fonctionnera (permission 'create vehicles' OK)\n";
        echo "⚠️  Mais {" . count($missingPermissions) . "} permission(s) critique(s) manquante(s):\n";
        foreach ($missingPermissions as $perm) {
            echo "   • {$perm}\n";
        }
    } else {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║  ❌ PERMISSIONS INSUFFISANTES                               ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "❌ L'erreur 403 persistera pour l'import de véhicules\n";
        echo "💡 Assignez la permission 'create vehicles' à cet utilisateur\n";
    }

    echo "\n";

    exit($canCreateVehicles ? 0 : 1);

} catch (\Exception $e) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERREUR CRITIQUE                                         ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "❌ Message: {$e->getMessage()}\n";
    echo "📁 Fichier:  {$e->getFile()}:{$e->getLine()}\n";
    echo "\n";

    exit(1);
}
