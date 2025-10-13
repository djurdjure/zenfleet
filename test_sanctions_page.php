<?php
/**
 * Script de test pour vérifier le module Sanctions
 * 
 * Ce script vérifie :
 * 1. Les permissions sont bien créées
 * 2. L'utilisateur admin a accès
 * 3. La route fonctionne
 * 4. Le composant Livewire est accessible
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->bootstrap();

// Définir l'environnement
app()->detectEnvironment(function () {
    return 'local';
});

use App\Models\User;
use App\Models\DriverSanction;
use Spatie\Permission\Models\Permission;

echo "🔍 TEST DU MODULE SANCTIONS\n";
echo str_repeat("=", 50) . "\n\n";

// 1. Vérifier les permissions
echo "1️⃣ PERMISSIONS DRIVER SANCTIONS:\n";
$permissions = Permission::where('name', 'like', '%driver sanction%')->pluck('name');
if ($permissions->isEmpty()) {
    echo "   ❌ Aucune permission trouvée\n";
    echo "   ⚠️  Exécutez: php artisan db:seed --class=DriverSanctionPermissionsSeeder\n";
} else {
    echo "   ✅ " . $permissions->count() . " permissions trouvées:\n";
    foreach ($permissions as $perm) {
        echo "      - $perm\n";
    }
}
echo "\n";

// 2. Vérifier l'utilisateur admin
echo "2️⃣ UTILISATEUR ADMIN:\n";
$admin = User::whereHas('roles', function($q) {
    $q->where('name', 'Admin');
})->first();

if (!$admin) {
    echo "   ❌ Aucun utilisateur avec le rôle Admin trouvé\n";
} else {
    echo "   ✅ Admin trouvé: {$admin->name} (ID: {$admin->id})\n";
    
    // Vérifier les permissions de l'admin
    $sanctionPerms = $admin->getAllPermissions()->filter(function($p) {
        return str_contains($p->name, 'driver sanction');
    });
    
    if ($sanctionPerms->isEmpty()) {
        echo "   ❌ L'admin n'a aucune permission sur les sanctions\n";
    } else {
        echo "   ✅ L'admin a " . $sanctionPerms->count() . " permissions sanctions\n";
    }
}
echo "\n";

// 3. Vérifier la route
echo "3️⃣ ROUTE SANCTIONS:\n";
$route = app('router')->getRoutes()->getByName('admin.sanctions.index');
if (!$route) {
    echo "   ❌ Route admin.sanctions.index non trouvée\n";
} else {
    echo "   ✅ Route trouvée: " . $route->uri() . "\n";
    echo "   📍 Action: " . $route->getActionName() . "\n";
}
echo "\n";

// 4. Vérifier le composant Livewire
echo "4️⃣ COMPOSANT LIVEWIRE:\n";
if (class_exists(\App\Livewire\Admin\DriverSanctionIndex::class)) {
    echo "   ✅ Classe DriverSanctionIndex existe\n";
    
    // Vérifier si Livewire est enregistré
    $componentName = app('livewire')->getAlias(\App\Livewire\Admin\DriverSanctionIndex::class);
    if ($componentName) {
        echo "   ✅ Composant enregistré dans Livewire: $componentName\n";
    } else {
        echo "   ⚠️  Composant non enregistré dans Livewire\n";
    }
} else {
    echo "   ❌ Classe DriverSanctionIndex introuvable\n";
}
echo "\n";

// 5. Vérifier les données
echo "5️⃣ DONNÉES SANCTIONS:\n";
$sanctionsCount = DriverSanction::count();
echo "   📊 Nombre total de sanctions: $sanctionsCount\n";
if ($admin && $admin->organization_id) {
    $orgSanctions = DriverSanction::where('organization_id', $admin->organization_id)->count();
    echo "   📊 Sanctions de l'organisation: $orgSanctions\n";
}
echo "\n";

// 6. Test de rendu du composant
echo "6️⃣ TEST DE RENDU LIVEWIRE:\n";
if ($admin && class_exists(\App\Livewire\Admin\DriverSanctionIndex::class)) {
    try {
        // Simuler l'authentification
        auth()->login($admin);
        
        // Créer une instance du composant
        $component = new \App\Livewire\Admin\DriverSanctionIndex();
        
        echo "   ✅ Composant instancié avec succès\n";
        
        // Tester l'autorisation
        try {
            $canView = $admin->can('viewAny', DriverSanction::class);
            if ($canView) {
                echo "   ✅ L'admin peut voir les sanctions\n";
            } else {
                echo "   ❌ L'admin ne peut PAS voir les sanctions\n";
            }
        } catch (\Exception $e) {
            echo "   ❌ Erreur d'autorisation: " . $e->getMessage() . "\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Erreur lors de l'instanciation: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// 7. Recommandations
echo "📋 RECOMMANDATIONS:\n";
echo str_repeat("-", 50) . "\n";

$issues = [];

if ($permissions->isEmpty()) {
    $issues[] = "Exécuter le seeder: php artisan db:seed --class=DriverSanctionPermissionsSeeder";
}

if (!$admin) {
    $issues[] = "Créer un utilisateur admin: php artisan db:seed --class=AdminTestSeeder";
}

if ($admin && $sanctionPerms->isEmpty()) {
    $issues[] = "Assigner les permissions à l'admin";
}

if (empty($issues)) {
    echo "✅ Tout semble configuré correctement!\n";
    echo "   Accédez à: http://localhost/admin/sanctions\n";
} else {
    echo "⚠️  Actions requises:\n";
    foreach ($issues as $i => $issue) {
        echo "   " . ($i + 1) . ". $issue\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✨ Test terminé\n";
