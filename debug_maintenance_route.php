<?php

/**
 * 🕵️ DEBUG ROUTE MAINTENANCE - TRACEUR D'EXÉCUTION
 *
 * Script pour tracer exactement quel fichier est exécuté
 * quand on accède à /admin/maintenance
 *
 * @version 1.0-Debug
 * @author Expert Laravel 20+ ans d'expérience
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "🕵️ DEBUG ROUTE MAINTENANCE - TRACEUR D'EXÉCUTION\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "Traçage précis du routage /admin/maintenance\n\n";

try {
    echo "📋 Phase 1: Test de Correspondance de Route\n";
    echo "-" . str_repeat("-", 50) . "\n";

    // Créer une requête pour /admin/maintenance
    $request = \Illuminate\Http\Request::create('/admin/maintenance', 'GET');

    // Trouver la route correspondante
    $routes = \Route::getRoutes();
    $matchedRoute = $routes->match($request);

    if ($matchedRoute) {
        $routeName = $matchedRoute->getName();
        $action = $matchedRoute->getActionName();
        $uri = $matchedRoute->uri();

        echo "✅ Route correspondante trouvée:\n";
        echo "   URI: {$uri}\n";
        echo "   Nom: {$routeName}\n";
        echo "   Action: {$action}\n";

        // Analyser l'action pour extraire le contrôleur et la méthode
        if (strpos($action, '@') !== false) {
            [$controllerClass, $method] = explode('@', $action);
            echo "   Contrôleur: {$controllerClass}\n";
            echo "   Méthode: {$method}\n";

            // Vérifier si le fichier contrôleur existe
            $reflection = new ReflectionClass($controllerClass);
            $filePath = $reflection->getFileName();
            echo "   Fichier: {$filePath}\n";
            echo "   Dernière modification: " . date('Y-m-d H:i:s', filemtime($filePath)) . "\n";

        } else {
            echo "   Type: Closure ou autre\n";
        }

    } else {
        echo "❌ Aucune route correspondante trouvée pour /admin/maintenance\n";
    }

    echo "\n📋 Phase 2: Liste de Toutes les Routes Maintenance\n";
    echo "-" . str_repeat("-", 50) . "\n";

    $maintenanceRoutes = [];
    foreach ($routes as $route) {
        $name = $route->getName();
        $uri = $route->uri();

        if (strpos($name, 'maintenance') !== false || strpos($uri, 'maintenance') !== false) {
            $maintenanceRoutes[] = [
                'name' => $name,
                'uri' => $uri,
                'action' => $route->getActionName(),
                'methods' => implode('|', $route->methods())
            ];
        }
    }

    echo "Trouvées " . count($maintenanceRoutes) . " routes liées à maintenance:\n\n";

    foreach ($maintenanceRoutes as $i => $route) {
        echo "🔸 Route #" . ($i + 1) . ":\n";
        echo "   Nom: {$route['name']}\n";
        echo "   URI: {$route['uri']}\n";
        echo "   Action: {$route['action']}\n";
        echo "   Méthodes: {$route['methods']}\n\n";

        if ($i >= 9) {  // Limiter l'affichage aux 10 premières
            echo "   ... et " . (count($maintenanceRoutes) - 10) . " autres routes\n";
            break;
        }
    }

    echo "\n📋 Phase 3: Test de Résolution de Route par Nom\n";
    echo "-" . str_repeat("-", 50) . "\n";

    $testRoutes = [
        'admin.maintenance.dashboard',
        'maintenance.dashboard',
        'admin.maintenance.index'
    ];

    foreach ($testRoutes as $routeName) {
        try {
            $route = $routes->getByName($routeName);
            if ($route) {
                echo "✅ {$routeName}: {$route->getActionName()}\n";
            }
        } catch (Exception $e) {
            echo "❌ {$routeName}: Non trouvée\n";
        }
    }

    echo "\n📋 Phase 4: Vérification de Fichiers de Contrôleurs\n";
    echo "-" . str_repeat("-", 50) . "\n";

    $controllerPaths = [
        'MaintenanceController' => 'app/Http/Controllers/Admin/MaintenanceController.php',
        'DashboardController' => 'app/Http/Controllers/Admin/DashboardController.php',
        'Maintenance\DashboardController' => 'app/Http/Controllers/Admin/Maintenance/DashboardController.php'
    ];

    foreach ($controllerPaths as $name => $path) {
        $fullPath = __DIR__ . '/' . $path;
        if (file_exists($fullPath)) {
            $modTime = date('Y-m-d H:i:s', filemtime($fullPath));
            echo "✅ {$name}: Existe (modifié: {$modTime})\n";
        } else {
            echo "❌ {$name}: Absent ({$path})\n";
        }
    }

    echo "\n🎯 CONCLUSION DU DEBUG\n";
    echo "=" . str_repeat("=", 70) . "\n";

    if (isset($routeName) && isset($action)) {
        echo "🎯 ROUTE RÉELLEMENT EXÉCUTÉE:\n";
        echo "   Route: {$routeName}\n";
        echo "   Action: {$action}\n";
        echo "   URI: {$uri}\n\n";

        echo "💡 INSTRUCTION:\n";
        echo "   Cette route est celle qui sera exécutée quand vous accédez à\n";
        echo "   http://localhost/admin/maintenance\n\n";

        if (strpos($action, 'MaintenanceController') !== false) {
            echo "✅ Le bon contrôleur est utilisé (MaintenanceController)\n";
        } else {
            echo "⚠️ Un autre contrôleur est utilisé: {$action}\n";
            echo "   Il faut corriger cette route ou ce contrôleur\n";
        }
    }

} catch (Exception $e) {
    echo "\n❌ ERREUR CRITIQUE:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . "\n";
    echo "   Ligne: " . $e->getLine() . "\n";
}

echo "\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "🕵️ DEBUG TERMINÉ - ROUTE TRACÉE\n";
echo "=" . str_repeat("=", 70) . "\n\n";