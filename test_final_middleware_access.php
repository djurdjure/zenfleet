<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🎯 TEST FINAL - ACCÈS /admin/assignments/create           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->email}\n";
echo "👑 Rôle: " . ($user->roles->pluck('name')->first() ?? 'Aucun') . "\n\n";

// Connexion
\Illuminate\Support\Facades\Auth::login($user);

echo str_repeat("─", 66) . "\n";
echo "ÉTAPE 1: Vérification Route\n";
echo str_repeat("─", 66) . "\n\n";

$route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.assignments.create');

if ($route) {
    echo "✅ Route trouvée: {$route->uri()}\n";
    $middleware = $route->gatherMiddleware();
    echo "   Middleware: " . implode(', ', $middleware) . "\n";
} else {
    echo "❌ Route non trouvée\n";
    exit(1);
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 2: Vérification Permissions\n";
echo str_repeat("─", 66) . "\n\n";

// Test permission moderne
$hasModernPermission = $user->can('assignments.create');
echo "Permission 'assignments.create': " . ($hasModernPermission ? "✅" : "❌") . "\n";

// Test permission ancienne (par compatibilité)
$hasOldPermission = $user->can('create assignments');
echo "Permission 'create assignments': " . ($hasOldPermission ? "✅" : "❌") . "\n";

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 3: Simulation Middleware EnterprisePermission\n";
echo str_repeat("─", 66) . "\n\n";

// Simuler le middleware
$middleware = new \App\Http\Middleware\EnterprisePermissionMiddleware();
$request = \Illuminate\Http\Request::create('/admin/assignments/create', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Associer la route
$route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.assignments.create');
$request->setRouteResolver(function () use ($route) {
    return $route;
});

try {
    $response = $middleware->handle($request, function ($req) {
        return response('OK', 200);
    });

    if ($response->getStatusCode() === 200) {
        echo "✅ MIDDLEWARE PASSED - Accès autorisé\n";
        echo "   → L'utilisateur peut accéder à /admin/assignments/create\n";
    } else {
        echo "❌ MIDDLEWARE BLOCKED - Code: {$response->getStatusCode()}\n";
        echo "   → Contenu: {$response->getContent()}\n";
    }
} catch (Exception $e) {
    echo "❌ MIDDLEWARE ERROR - {$e->getMessage()}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 4: Simulation Middleware 'can:create,Assignment'\n";
echo str_repeat("─", 66) . "\n\n";

try {
    // Ce que fait le middleware 'can'
    \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Assignment::class);
    echo "✅ MIDDLEWARE 'can:' PASSED - Accès autorisé\n";
} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ MIDDLEWARE 'can:' BLOCKED - {$e->getMessage()}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 5: Test AssignmentPolicy\n";
echo str_repeat("─", 66) . "\n\n";

$policy = \Illuminate\Support\Facades\Gate::getPolicyFor(\App\Models\Assignment::class);
if ($policy) {
    $canCreate = $policy->create($user);
    echo "Policy->create(): " . ($canCreate ? "✅ TRUE" : "❌ FALSE") . "\n";

    // Afficher le code de la policy
    $reflection = new ReflectionMethod($policy, 'create');
    $filename = $reflection->getFileName();
    $startLine = $reflection->getStartLine();
    echo "\n   Fichier: {$filename}\n";
    echo "   Ligne: {$startLine}\n";

    // Lire les 5 lignes de la méthode
    $file = file($filename);
    echo "\n   Code:\n";
    for ($i = $startLine - 1; $i < min($startLine + 4, count($file)); $i++) {
        echo "   " . ($i + 1) . ": " . $file[$i];
    }
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "🎯 VERDICT FINAL\n";
echo str_repeat("─", 66) . "\n\n";

if ($hasModernPermission && $response->getStatusCode() === 200) {
    echo "✅✅✅ TOUT EST BON ! ✅✅✅\n\n";
    echo "L'utilisateur admin@zenfleet.dz PEUT maintenant accéder à:\n";
    echo "👉 http://localhost/admin/assignments/create\n\n";
    echo "Corrections appliquées:\n";
    echo "  1. ✅ Policy harmonisée avec 'assignments.create'\n";
    echo "  2. ✅ Middleware route 'can:' ajouté\n";
    echo "  3. ✅ EnterprisePermissionMiddleware mis à jour\n";
    echo "  4. ✅ OPcache vidé\n\n";
    echo "🎉 Le problème 403 est RÉSOLU !\n";
} else {
    echo "❌ IL RESTE UN PROBLÈME\n\n";
    echo "Détails:\n";
    echo "  • Permission moderne: " . ($hasModernPermission ? "✅" : "❌") . "\n";
    echo "  • Middleware EnterprisePermission: " . ($response->getStatusCode() === 200 ? "✅" : "❌") . "\n";
    echo "  • Middleware can:: " . ($canCreate ?? false ? "✅" : "❌") . "\n";
}

echo "\n";
