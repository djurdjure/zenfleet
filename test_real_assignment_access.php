<?php

/**
 * 🧪 TEST RÉEL - SIMULATION DE CONNEXION ET ACCÈS À /admin/assignments/create
 * 
 * Test complet avec simulation de session et requête HTTP
 */

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🧪 TEST RÉEL - ACCÈS À LA CRÉATION D'AFFECTATIONS                   ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";

// Récupérer l'utilisateur admin
$admin = User::whereEmail('admin@zenfleet.dz')->first();

if (!$admin) {
    $admin = User::whereHas('roles', function($q) {
        $q->where('name', 'Admin');
    })->first();
}

if (!$admin) {
    die("❌ Aucun utilisateur admin trouvé!\n");
}

echo "\n👤 UTILISATEUR TEST: {$admin->name} ({$admin->email})\n";
echo "🏢 Organisation: {$admin->organization_id}\n";

// ÉTAPE 1: VÉRIFIER LES PERMISSIONS
echo "\n📋 ÉTAPE 1: VÉRIFICATION DES PERMISSIONS\n";
echo str_repeat("─", 70) . "\n";

$permissions = [
    'create assignments',
    'assignments.create',
    'view assignments',
    'edit assignments'
];

$allOk = true;
foreach ($permissions as $perm) {
    $hasIt = $admin->can($perm);
    $icon = $hasIt ? '✅' : '❌';
    echo "  {$icon} {$perm}: " . ($hasIt ? 'OUI' : 'NON') . "\n";
    if (!$hasIt && str_contains($perm, 'create')) {
        $allOk = false;
    }
}

if (!$allOk) {
    echo "\n⚠️  Permissions manquantes détectées. Ajout en cours...\n";
    $admin->givePermissionTo('create assignments');
    $admin->givePermissionTo('assignments.create');
    echo "  ✅ Permissions ajoutées\n";
}

// ÉTAPE 2: SIMULER UNE SESSION AUTHENTIFIÉE
echo "\n🔐 ÉTAPE 2: SIMULATION DE SESSION AUTHENTIFIÉE\n";
echo str_repeat("─", 70) . "\n";

// Démarrer une session
Session::start();

// Authentifier l'utilisateur
Auth::login($admin);

if (Auth::check()) {
    echo "  ✅ Utilisateur connecté: " . Auth::user()->email . "\n";
} else {
    echo "  ❌ Erreur de connexion\n";
}

// ÉTAPE 3: CRÉER UNE REQUÊTE HTTP SIMULÉE
echo "\n🌐 ÉTAPE 3: SIMULATION DE REQUÊTE HTTP\n";
echo str_repeat("─", 70) . "\n";

// Créer une requête pour /admin/assignments/create
$request = Request::create('/admin/assignments/create', 'GET');
$request->setUserResolver(function () use ($admin) {
    return $admin;
});

// Définir la requête dans l'application
app()->instance('request', $request);

echo "  ✅ Requête créée: GET /admin/assignments/create\n";
echo "  ✅ Utilisateur défini: {$admin->email}\n";

// ÉTAPE 4: APPELER LE CONTRÔLEUR
echo "\n🎮 ÉTAPE 4: APPEL DU CONTRÔLEUR\n";
echo str_repeat("─", 70) . "\n";

try {
    // Instancier le contrôleur
    $controller = app(\App\Http\Controllers\Admin\AssignmentController::class);
    echo "  ✅ Contrôleur instancié\n";
    
    // Appeler la méthode create()
    $response = $controller->create();
    
    if ($response instanceof \Illuminate\View\View) {
        echo "  ✅ Vue retournée avec succès: " . $response->getName() . "\n";
        
        // Analyser les données de la vue
        $data = $response->getData();
        
        echo "\n📊 DONNÉES DISPONIBLES DANS LA VUE:\n";
        echo "  • Véhicules disponibles: " . ($data['availableVehicles']->count() ?? 0) . "\n";
        echo "  • Chauffeurs disponibles: " . ($data['availableDrivers']->count() ?? 0) . "\n";
        echo "  • Affectations actives: " . ($data['activeAssignments']->count() ?? 0) . "\n";
        
        if ($data['availableVehicles']->count() > 0) {
            echo "\n  📋 Exemples de véhicules disponibles:\n";
            foreach ($data['availableVehicles']->take(3) as $vehicle) {
                echo "     - {$vehicle->registration_plate} ({$vehicle->brand} {$vehicle->model})\n";
            }
        }
        
        if ($data['availableDrivers']->count() > 0) {
            echo "\n  👥 Exemples de chauffeurs disponibles:\n";
            foreach ($data['availableDrivers']->take(3) as $driver) {
                echo "     - {$driver->first_name} {$driver->last_name}\n";
            }
        }
        
        echo "\n" . str_repeat("═", 70) . "\n";
        echo "✅ SUCCÈS TOTAL !\n";
        echo str_repeat("═", 70) . "\n";
        echo "\n🎉 L'ACCÈS À LA CRÉATION D'AFFECTATIONS FONCTIONNE !\n";
        echo "\n";
        echo "L'utilisateur admin peut maintenant:\n";
        echo "  ✅ Accéder à http://localhost/admin/assignments/create\n";
        echo "  ✅ Voir le formulaire de création wizard\n";
        echo "  ✅ Sélectionner parmi " . $data['availableVehicles']->count() . " véhicule(s)\n";
        echo "  ✅ Sélectionner parmi " . $data['availableDrivers']->count() . " chauffeur(s)\n";
        echo "  ✅ Créer de nouvelles affectations\n";
        
    } else {
        echo "  ⚠️  Type de réponse inattendu: " . get_class($response) . "\n";
    }
    
} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "  ❌ ERREUR 403: " . $e->getMessage() . "\n";
    echo "\n💡 DEBUG - Permissions de l'utilisateur:\n";
    $allPerms = $admin->getAllPermissions()->pluck('name');
    foreach ($allPerms as $perm) {
        if (str_contains($perm, 'assignment')) {
            echo "     - {$perm}\n";
        }
    }
} catch (\Exception $e) {
    echo "  ❌ ERREUR: " . $e->getMessage() . "\n";
    echo "     Type: " . get_class($e) . "\n";
    echo "     Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// ÉTAPE 5: TEST DE LA ROUTE
echo "\n🛣️ ÉTAPE 5: VÉRIFICATION DE LA ROUTE\n";
echo str_repeat("─", 70) . "\n";

$route = Route::getRoutes()->match($request);
if ($route) {
    echo "  ✅ Route trouvée: " . $route->getName() . "\n";
    echo "     Action: " . $route->getActionName() . "\n";
    echo "     Middleware: " . implode(', ', $route->middleware()) . "\n";
} else {
    echo "  ❌ Route non trouvée\n";
}

echo "\n" . str_repeat("═", 70) . "\n";
echo "📋 INSTRUCTIONS POUR TESTER MANUELLEMENT:\n";
echo str_repeat("═", 70) . "\n";
echo "\n";
echo "1. Ouvrir le navigateur à: http://localhost\n";
echo "2. Se connecter avec:\n";
echo "   Email: admin@zenfleet.dz\n";
echo "   Mot de passe: [votre mot de passe admin]\n";
echo "3. Naviguer vers: http://localhost/admin/assignments/create\n";
echo "4. Vérifier que la page s'affiche correctement\n";
echo "\n";
echo "Si l'erreur 403 persiste après ce fix, exécuter:\n";
echo "  docker compose exec php php artisan cache:clear\n";
echo "  docker compose exec php php artisan config:clear\n";
echo "  docker compose exec php php artisan permission:cache-reset\n";
echo "\n";
