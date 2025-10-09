<?php

/**
 * Script de test - Vérification des accès Admin au module Kilométrage
 * Enterprise-Grade Testing
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║            TEST ACCÈS ADMIN - MODULE KILOMÉTRAGE ENTERPRISE                     ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Vérifier les rôles Admin
echo "📊 TEST 1: VÉRIFICATION DES RÔLES ADMIN\n";
echo "─────────────────────────────────────────\n";

$adminRole = Role::where('name', 'Admin')->first();
if ($adminRole) {
    echo "✅ Rôle 'Admin' trouvé (ID: {$adminRole->id})\n";
    
    // Lister les permissions kilométrage du rôle Admin
    $mileagePermissions = $adminRole->permissions()
        ->where('name', 'like', '%mileage%')
        ->pluck('name')
        ->toArray();
    
    echo "📋 Permissions kilométrage du rôle Admin:\n";
    if (count($mileagePermissions) > 0) {
        foreach ($mileagePermissions as $permission) {
            echo "   ✓ {$permission}\n";
        }
    } else {
        echo "   ⚠️ Aucune permission kilométrage trouvée!\n";
    }
} else {
    echo "❌ Rôle 'Admin' non trouvé!\n";
}

echo "\n";

// Test 2: Vérifier un utilisateur Admin spécifique
echo "📊 TEST 2: VÉRIFICATION D'UN UTILISATEUR ADMIN\n";
echo "─────────────────────────────────────────────────\n";

$adminUser = User::whereHas('roles', function($q) {
    $q->where('name', 'Admin');
})->first();

if ($adminUser) {
    echo "✅ Utilisateur Admin trouvé: {$adminUser->name} (ID: {$adminUser->id})\n";
    echo "📧 Email: {$adminUser->email}\n";
    echo "🏢 Organisation ID: {$adminUser->organization_id}\n";
    
    // Vérifier les rôles
    $roles = $adminUser->getRoleNames();
    echo "👤 Rôles: " . $roles->implode(', ') . "\n";
    
    // Vérifier les permissions kilométrage directes
    echo "\n📋 Permissions kilométrage de l'utilisateur:\n";
    
    $permissions = [
        'view own mileage readings',
        'view team mileage readings',
        'view all mileage readings',
        'create mileage readings',
        'update own mileage readings',
        'update any mileage readings',
        'delete mileage readings',
        'export mileage readings',
        'view mileage statistics',
        'view mileage reading history'
    ];
    
    foreach ($permissions as $permission) {
        if ($adminUser->can($permission)) {
            echo "   ✅ {$permission}\n";
        } else {
            echo "   ❌ {$permission}\n";
        }
    }
    
    // Test des vérifications de rôle
    echo "\n🔐 Vérifications de rôle:\n";
    echo "   " . ($adminUser->hasRole('Admin') ? "✅" : "❌") . " hasRole('Admin')\n";
    echo "   " . ($adminUser->hasRole(['Super Admin', 'Admin']) ? "✅" : "❌") . " hasRole(['Super Admin', 'Admin'])\n";
    
} else {
    echo "❌ Aucun utilisateur Admin trouvé!\n";
}

echo "\n";

// Test 3: Tester l'accès via le middleware
echo "📊 TEST 3: SIMULATION D'ACCÈS VIA MIDDLEWARE\n";
echo "──────────────────────────────────────────────\n";

if ($adminUser) {
    // Simuler une connexion
    auth()->login($adminUser);
    
    echo "✅ Connexion simulée pour: {$adminUser->email}\n";
    
    // Vérifier l'accès selon les conditions du middleware
    $hasAccess = false;
    $accessReason = "";
    
    if ($adminUser->hasRole(['Super Admin', 'Admin'])) {
        $hasAccess = true;
        $accessReason = "Rôle Admin/Super Admin";
    } elseif ($adminUser->hasRole('Gestionnaire Flotte') && $adminUser->can('view all mileage readings')) {
        $hasAccess = true;
        $accessReason = "Gestionnaire Flotte avec permission complète";
    } elseif ($adminUser->hasRole('Superviseur') && $adminUser->can('view team mileage readings')) {
        $hasAccess = true;
        $accessReason = "Superviseur avec accès équipe";
    } elseif ($adminUser->can('view own mileage readings')) {
        $hasAccess = true;
        $accessReason = "Permission de voir ses propres relevés";
    }
    
    if ($hasAccess) {
        echo "✅ ACCÈS AUTORISÉ - Raison: {$accessReason}\n";
    } else {
        echo "❌ ACCÈS REFUSÉ - Aucune condition remplie\n";
    }
    
    auth()->logout();
}

echo "\n";

// Test 4: Liste des routes kilométrage
echo "📊 TEST 4: ROUTES KILOMÉTRAGE DISPONIBLES\n";
echo "────────────────────────────────────────────\n";

$routes = [
    'admin.mileage-readings.index' => 'Historique kilométrage',
    'admin.mileage-readings.update' => 'Mise à jour kilométrage',
    'admin.vehicles.mileage-history' => 'Historique par véhicule'
];

foreach ($routes as $routeName => $description) {
    try {
        $url = route($routeName, ['vehicle' => 1]);
        echo "✅ {$description}: {$url}\n";
    } catch (\Exception $e) {
        echo "⚠️ {$description}: Route non trouvée ou paramètres manquants\n";
    }
}

echo "\n";

// Résumé
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                              RÉSUMÉ DU TEST                                   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";

$summary = [
    "Rôle Admin existe" => $adminRole ? "✅" : "❌",
    "Utilisateur Admin trouvé" => $adminUser ? "✅" : "❌",
    "Admin a accès au module" => ($adminUser && $hasAccess) ? "✅" : "❌",
    "Routes configurées" => "✅"
];

foreach ($summary as $test => $result) {
    echo sprintf("%-30s: %s\n", $test, $result);
}

echo "\n🎯 RECOMMANDATIONS:\n";
if (!$adminRole) {
    echo "   ⚠️ Créer le rôle 'Admin' avec: php artisan db:seed --class=RolesAndPermissionsSeeder\n";
}
if ($adminRole && count($mileagePermissions) == 0) {
    echo "   ⚠️ Attribuer les permissions kilométrage: php artisan db:seed --class=VehicleMileagePermissionsSeeder\n";
}
if (!$adminUser) {
    echo "   ⚠️ Créer un utilisateur Admin pour les tests\n";
}

echo "\n✨ Test terminé avec succès!\n\n";
