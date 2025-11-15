<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 TEST ACCÈS ROUTE assignments.create VIA MIDDLEWARE     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Connexion avec l'utilisateur admin
$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur admin@zenfleet.dz non trouvé\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->first_name} {$user->last_name} ({$user->email})\n";
echo "👑 Rôle: " . ($user->roles->pluck('name')->first() ?? 'Aucun') . "\n\n";

// Connexion via Auth facade
\Illuminate\Support\Facades\Auth::login($user);

echo "✅ Utilisateur connecté via Auth::login()\n\n";

echo str_repeat("─", 66) . "\n";
echo "TEST 1: Vérification de la Policy\n";
echo str_repeat("─", 66) . "\n\n";

$assignmentClass = \App\Models\Assignment::class;

// Test via Gate
$canCreateViaGate = \Illuminate\Support\Facades\Gate::allows('create', $assignmentClass);
echo "Gate::allows('create', Assignment::class): " . ($canCreateViaGate ? "✅ TRUE" : "❌ FALSE") . "\n";

// Test via Policy directement
$policy = \Illuminate\Support\Facades\Gate::getPolicyFor($assignmentClass);
if ($policy) {
    $canCreateViaPolicy = $policy->create($user);
    echo "Policy->create(\$user): " . ($canCreateViaPolicy ? "✅ TRUE" : "❌ FALSE") . "\n";
} else {
    echo "❌ Pas de Policy trouvée\n";
}

// Test via $user->can()
$canCreateViaUser = $user->can('create', $assignmentClass);
echo "\$user->can('create', Assignment::class): " . ($canCreateViaUser ? "✅ TRUE" : "❌ FALSE") . "\n";

echo "\n" . str_repeat("─", 66) . "\n";
echo "TEST 2: Simulation du Middleware 'can:create,App\\Models\\Assignment'\n";
echo str_repeat("─", 66) . "\n\n";

try {
    // Simuler ce que fait le middleware 'can'
    $request = \Illuminate\Http\Request::create('/admin/assignments/create', 'GET');
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    // Le middleware 'can' utilise Gate::authorize() en interne
    \Illuminate\Support\Facades\Gate::authorize('create', $assignmentClass);

    echo "✅ MIDDLEWARE PASSED - L'utilisateur est autorisé\n";
    echo "   → La route /admin/assignments/create DEVRAIT être accessible\n";

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ MIDDLEWARE BLOCKED - Autorisation refusée\n";
    echo "   → Message: {$e->getMessage()}\n";
    echo "   → La route /admin/assignments/create sera bloquée avec 403\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "TEST 3: Inspection de la Route\n";
echo str_repeat("─", 66) . "\n\n";

try {
    $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.assignments.create');

    if ($route) {
        echo "✅ Route trouvée: admin.assignments.create\n";
        echo "   • URI: {$route->uri()}\n";
        echo "   • Méthode: " . implode('|', $route->methods()) . "\n";

        $middleware = $route->gatherMiddleware();
        echo "   • Middleware: " . (empty($middleware) ? 'Aucun' : implode(', ', $middleware)) . "\n";

        // Vérifier si 'can' est dans les middlewares
        $hasCanMiddleware = collect($middleware)->contains(function($m) {
            return str_contains($m, 'can:');
        });

        if ($hasCanMiddleware) {
            echo "   ✅ Middleware 'can:' détecté dans la route\n";
        } else {
            echo "   ⚠️  Middleware 'can:' NON détecté (cache peut-être ?)\n";
        }
    } else {
        echo "❌ Route 'admin.assignments.create' non trouvée\n";
    }

} catch (Exception $e) {
    echo "⚠️  Erreur lors de l'inspection: {$e->getMessage()}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "🎯 CONCLUSION\n";
echo str_repeat("─", 66) . "\n\n";

if ($canCreateViaGate && $canCreateViaUser) {
    echo "✅ L'utilisateur admin@zenfleet.dz a la permission 'create' sur Assignment\n";
    echo "✅ La Policy fonctionne correctement\n";
    echo "✅ Le middleware devrait laisser passer la requête\n\n";
    echo "👉 La page http://localhost/admin/assignments/create DEVRAIT être accessible\n";
    echo "   Si elle ne l'est pas, vérifiez:\n";
    echo "   1. Le cache des routes: php artisan route:clear\n";
    echo "   2. Le cache de config: php artisan config:clear\n";
    echo "   3. Redémarrer PHP: docker restart zenfleet_php\n";
} else {
    echo "❌ L'utilisateur n'a PAS la permission requise\n";
    echo "❌ La page restera inaccessible (403)\n";
}

echo "\n";
