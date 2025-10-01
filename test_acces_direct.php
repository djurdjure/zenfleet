<?php

/**
 * Test d'accès DIRECT aux pages - Simule une vraie requête HTTP
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST D'ACCÈS DIRECT AUX PAGES\n";
echo str_repeat("=", 80) . "\n\n";

// Se connecter en tant qu'admin
$admin = App\Models\User::where('email', 'admin@faderco.dz')->first();

if (!$admin) {
    echo "❌ Admin introuvable\n";
    exit(1);
}

Auth::login($admin);

echo "✅ Connecté en tant que: {$admin->email}\n";
echo "   Organisation: {$admin->organization->name}\n";
echo "   Rôle: " . $admin->getRoleNames()->implode(', ') . "\n\n";

// Tester l'accès aux contrôleurs
echo "📋 TEST DES CONTRÔLEURS\n";
echo str_repeat("-", 80) . "\n\n";

$tests = [
    [
        'nom' => '🚗 Véhicules',
        'controller' => App\Http\Controllers\Admin\VehicleController::class,
        'method' => 'index',
    ],
    [
        'nom' => '👤 Chauffeurs',
        'controller' => App\Http\Controllers\Admin\DriverController::class,
        'method' => 'index',
    ],
    [
        'nom' => '🏢 Fournisseurs',
        'controller' => App\Http\Controllers\Admin\SupplierController::class,
        'method' => 'index',
    ],
    [
        'nom' => '📋 Affectations',
        'controller' => App\Http\Controllers\Admin\AssignmentController::class,
        'method' => 'index',
    ],
];

foreach ($tests as $test) {
    echo "{$test['nom']}\n";

    try {
        // Créer une requête simulée
        $request = Illuminate\Http\Request::create('/' . strtolower($test['nom']), 'GET');
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        // Instancier le contrôleur
        $controller = app($test['controller']);

        // Tenter d'appeler la méthode
        $response = $controller->{$test['method']}($request);

        echo "  ✅ Accès autorisé - Type de réponse: " . get_class($response) . "\n";

    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        echo "  ❌ ACCÈS REFUSÉ: {$e->getMessage()}\n";
        echo "     Ability testé: " . ($e->ability ?? 'inconnu') . "\n";
    } catch (\Exception $e) {
        echo "  ⚠️  Erreur: " . get_class($e) . " - {$e->getMessage()}\n";
        echo "     Fichier: {$e->getFile()}:{$e->getLine()}\n";
    }

    echo "\n";
}

// Tester les permissions directement
echo "🔑 TEST DES PERMISSIONS DIRECTES\n";
echo str_repeat("-", 80) . "\n\n";

$permissions = [
    'view vehicles',
    'create vehicles',
    'view drivers',
    'create drivers',
    'view suppliers',
    'create suppliers',
    'view assignments',
    'create assignments',
];

foreach ($permissions as $perm) {
    $has = $admin->can($perm);
    echo "  " . ($has ? "✅" : "❌") . " {$perm}\n";
}

echo "\n";

// Tester les policies directement
echo "🛡️ TEST DES POLICIES DIRECTES\n";
echo str_repeat("-", 80) . "\n\n";

$policyTests = [
    ['model' => App\Models\Vehicle::class, 'method' => 'viewAny'],
    ['model' => App\Models\Driver::class, 'method' => 'viewAny'],
    ['model' => App\Models\Supplier::class, 'method' => 'viewAny'],
    ['model' => App\Models\Assignment::class, 'method' => 'viewAny'],
];

foreach ($policyTests as $test) {
    $modelName = class_basename($test['model']);

    try {
        $can = Gate::allows($test['method'], $test['model']);
        echo "  " . ($can ? "✅" : "❌") . " {$modelName}::{$test['method']}()\n";
    } catch (\Exception $e) {
        echo "  ⚠️  {$modelName}::{$test['method']}() - Erreur: {$e->getMessage()}\n";
    }
}

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "✨ TEST TERMINÉ\n\n";
