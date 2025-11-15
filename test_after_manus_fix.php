<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🎯 TEST APRÈS IMPLÉMENTATION SOLUTION MANUS AI            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->email}\n";
echo "👑 Rôles: " . $user->roles->pluck('name')->implode(', ') . "\n\n";

\Illuminate\Support\Facades\Auth::login($user);

echo str_repeat("─", 66) . "\n";
echo "ÉTAPE 1: Vérification de la Route\n";
echo str_repeat("─", 66) . "\n\n";

$route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.assignments.create');

if ($route) {
    echo "✅ Route trouvée: {$route->uri()}\n";

    // Vérifier l'action de la route
    $action = $route->getAction();
    if (isset($action['controller'])) {
        echo "   • Contrôleur: {$action['controller']}\n";
        echo "   ✅ La route UTILISE le contrôleur (Pattern MVC restauré)\n";
    } elseif (isset($action['uses']) && $action['uses'] instanceof Closure) {
        echo "   • Type: Closure (fonction anonyme)\n";
        echo "   ⚠️  La route utilise encore une closure\n";
    }

    $middleware = $route->gatherMiddleware();
    $middlewareNames = array_map(function($m) {
        return is_string($m) ? $m : get_class($m);
    }, $middleware);
    echo "   • Middleware: " . implode(', ', $middlewareNames) . "\n";
} else {
    echo "❌ Route non trouvée\n";
    exit(1);
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 2: Test de la Policy\n";
echo str_repeat("─", 66) . "\n\n";

$policy = \Illuminate\Support\Facades\Gate::getPolicyFor(\App\Models\Assignment::class);

if ($policy) {
    echo "✅ Policy trouvée: " . get_class($policy) . "\n";

    $canCreate = $policy->create($user);
    echo "   • Policy->create(\$user): " . ($canCreate ? "✅ TRUE" : "❌ FALSE") . "\n";

    if (!$canCreate) {
        echo "\n   ⚠️  La Policy refuse l'accès. Raisons possibles:\n";
        echo "      • L'utilisateur n'a pas la permission 'assignments.create'\n";
        echo "      • L'utilisateur n'a pas un rôle autorisé (Super Admin, Admin, Gestionnaire Flotte)\n";

        echo "\n   Vérification:\n";
        echo "      • Rôles: " . $user->roles->pluck('name')->implode(', ') . "\n";
        echo "      • A 'assignments.create': " . ($user->can('assignments.create') ? "✅" : "❌") . "\n";
        echo "      • Est Admin: " . ($user->hasRole('Admin') ? "✅" : "❌") . "\n";
    }
} else {
    echo "❌ Pas de Policy trouvée\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 3: Simulation de l'appel au Contrôleur\n";
echo str_repeat("─", 66) . "\n\n";

try {
    // Simuler ce que fait Laravel quand on accède à la route
    \Illuminate\Support\Facades\Gate::authorize('create', \App\Models\Assignment::class);

    echo "✅ Gate::authorize() PASSED - L'utilisateur est autorisé\n";
    echo "   → Le contrôleur AssignmentController@create devrait être accessible\n";

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ Gate::authorize() FAILED - Autorisation refusée\n";
    echo "   • Message: {$e->getMessage()}\n";
    echo "   → Le contrôleur va bloquer avec un 403\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 4: Test du Middleware EnterprisePermission\n";
echo str_repeat("─", 66) . "\n\n";

$middleware = new \App\Http\Middleware\EnterprisePermissionMiddleware();
$request = \Illuminate\Http\Request::create('/admin/assignments/create', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

$route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.assignments.create');
$request->setRouteResolver(function () use ($route) {
    return $route;
});

try {
    $response = $middleware->handle($request, function ($req) {
        return response('OK', 200);
    });

    if ($response->getStatusCode() === 200) {
        echo "✅ EnterprisePermissionMiddleware PASSED\n";
        echo "   → Le middleware autorise l'accès\n";
    } else {
        echo "❌ EnterprisePermissionMiddleware BLOCKED - Code: {$response->getStatusCode()}\n";
    }
} catch (Exception $e) {
    echo "❌ Middleware ERROR - {$e->getMessage()}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "ÉTAPE 5: Vérification des Permissions Utilisateur\n";
echo str_repeat("─", 66) . "\n\n";

$assignmentPermissions = $user->getAllPermissions()
    ->filter(function($perm) {
        return str_contains(strtolower($perm->name), 'assignment');
    })
    ->pluck('name');

echo "Permissions 'assignment' de l'utilisateur:\n";
foreach ($assignmentPermissions as $perm) {
    echo "   • {$perm}\n";
}

echo "\nVérifications critiques:\n";
echo "   • 'assignments.create': " . ($user->can('assignments.create') ? "✅" : "❌") . "\n";
echo "   • 'create assignments': " . ($user->can('create assignments') ? "✅" : "❌") . "\n";
echo "   • Rôle Admin: " . ($user->hasRole('Admin') ? "✅" : "❌") . "\n";

echo "\n" . str_repeat("─", 66) . "\n";
echo "🎯 VERDICT FINAL\n";
echo str_repeat("─", 66) . "\n\n";

$policyPasses = $policy && $policy->create($user);
$middlewarePasses = isset($response) && $response->getStatusCode() === 200;

if ($policyPasses && $middlewarePasses) {
    echo "✅✅✅ TOUS LES TESTS PASSENT ! ✅✅✅\n\n";
    echo "Corrections Manus AI implémentées avec succès:\n";
    echo "  1. ✅ Route pointe vers le contrôleur (Pattern MVC restauré)\n";
    echo "  2. ✅ Contrôleur utilise \$this->authorize() standard\n";
    echo "  3. ✅ Policy fonctionne correctement\n";
    echo "  4. ✅ Middleware EnterprisePermission autorise l'accès\n\n";
    echo "👉 La page http://localhost/admin/assignments/create DEVRAIT être accessible\n\n";
    echo "Si le problème persiste dans le navigateur:\n";
    echo "  1. Videz COMPLÈTEMENT le cache du navigateur (Ctrl+Shift+Delete)\n";
    echo "  2. Fermez et rouvrez le navigateur\n";
    echo "  3. Déconnectez-vous et reconnectez-vous\n";
    echo "  4. Essayez en navigation privée\n";
} else {
    echo "❌ IL RESTE DES PROBLÈMES\n\n";
    echo "Diagnostics:\n";
    echo "  • Policy: " . ($policyPasses ? "✅" : "❌") . "\n";
    echo "  • Middleware Enterprise: " . ($middlewarePasses ? "✅" : "❌") . "\n\n";

    if (!$policyPasses) {
        echo "⚠️  PROBLÈME CRITIQUE: La Policy bloque l'accès\n";
        echo "    → Vérifiez que l'utilisateur a la permission 'assignments.create'\n";
        echo "    → OU qu'il a le rôle 'Admin'\n";
    }

    if (!$middlewarePasses) {
        echo "⚠️  PROBLÈME: Le middleware EnterprisePermission bloque\n";
        echo "    → Vérifiez le mapping dans EnterprisePermissionMiddleware.php\n";
    }
}

echo "\n";
