<?php
/**
 * Script de test CLI pour vérifier le module Sanctions
 * Version corrigée pour éviter les erreurs de session
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DriverSanction;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "🔍 TEST DU MODULE SANCTIONS (CLI)\n";
echo str_repeat("=", 50) . "\n\n";

try {
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
    $admins = DB::table('model_has_roles')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->join('users', 'model_has_roles.model_id', '=', 'users.id')
        ->where('roles.name', 'Admin')
        ->where('model_has_roles.model_type', 'App\\Models\\User')
        ->select('users.*')
        ->get();

    if ($admins->isEmpty()) {
        echo "   ❌ Aucun utilisateur avec le rôle Admin trouvé\n";
    } else {
        echo "   ✅ " . $admins->count() . " Admin(s) trouvé(s):\n";
        foreach ($admins as $admin) {
            echo "      - {$admin->name} (ID: {$admin->id})\n";
            
            // Vérifier les permissions
            $adminUser = User::find($admin->id);
            $sanctionPerms = $adminUser->getAllPermissions()->filter(function($p) {
                return str_contains($p->name, 'driver sanction');
            });
            
            if ($sanctionPerms->isNotEmpty()) {
                echo "        ✅ " . $sanctionPerms->count() . " permissions sanctions\n";
            } else {
                echo "        ❌ Aucune permission sanctions\n";
            }
        }
    }
    echo "\n";

    // 3. Vérifier la route
    echo "3️⃣ ROUTE SANCTIONS:\n";
    try {
        $route = app('router')->getRoutes()->getByName('admin.sanctions.index');
        if (!$route) {
            echo "   ❌ Route admin.sanctions.index non trouvée\n";
        } else {
            echo "   ✅ Route trouvée: " . $route->uri() . "\n";
            echo "   📍 Action: " . $route->getActionName() . "\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Erreur lors de la vérification de la route: " . $e->getMessage() . "\n";
    }
    echo "\n";

    // 4. Vérifier le composant Livewire
    echo "4️⃣ COMPOSANT LIVEWIRE:\n";
    if (class_exists(\App\Livewire\Admin\DriverSanctionIndex::class)) {
        echo "   ✅ Classe DriverSanctionIndex existe\n";
        
        // Vérifier si le fichier de vue existe
        $viewPath = resource_path('views/livewire/admin/driver-sanction-index.blade.php');
        if (file_exists($viewPath)) {
            echo "   ✅ Vue Blade existe: " . basename($viewPath) . "\n";
        } else {
            echo "   ❌ Vue Blade introuvable\n";
        }
    } else {
        echo "   ❌ Classe DriverSanctionIndex introuvable\n";
    }
    echo "\n";

    // 5. Vérifier les données
    echo "5️⃣ DONNÉES SANCTIONS:\n";
    $sanctionsCount = DriverSanction::count();
    echo "   📊 Nombre total de sanctions: $sanctionsCount\n";
    
    $organizations = DB::table('driver_sanctions')
        ->select('organization_id', DB::raw('count(*) as count'))
        ->groupBy('organization_id')
        ->get();
    
    if ($organizations->isNotEmpty()) {
        echo "   📊 Répartition par organisation:\n";
        foreach ($organizations as $org) {
            echo "      - Organisation {$org->organization_id}: {$org->count} sanctions\n";
        }
    }
    echo "\n";

    // 6. Vérifier les chauffeurs
    echo "6️⃣ CHAUFFEURS:\n";
    $driversCount = DB::table('drivers')->count();
    echo "   📊 Nombre total de chauffeurs: $driversCount\n";
    echo "\n";

    // 7. Vérifier le layout
    echo "7️⃣ LAYOUTS:\n";
    $layoutFiles = [
        'catalyst.blade.php' => resource_path('views/layouts/admin/catalyst.blade.php'),
        'catalyst-enterprise.blade.php' => resource_path('views/layouts/admin/catalyst-enterprise.blade.php'),
        'app.blade.php' => resource_path('views/layouts/admin/app.blade.php')
    ];
    
    foreach ($layoutFiles as $name => $path) {
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $hasDriversMenu = str_contains($content, 'Chauffeurs');
            $hasSanctionsMenu = str_contains($content, 'Sanctions');
            $hasDropdown = str_contains($content, 'x-data="{ open:');
            
            echo "   📄 $name:\n";
            echo "      " . (file_exists($path) ? "✅ Existe" : "❌ N'existe pas") . "\n";
            echo "      " . ($hasDriversMenu ? "✅ Menu Chauffeurs" : "❌ Pas de menu Chauffeurs") . "\n";
            echo "      " . ($hasSanctionsMenu ? "✅ Menu Sanctions" : "❌ Pas de menu Sanctions") . "\n";
            echo "      " . ($hasDropdown ? "✅ Dropdown Alpine.js" : "❌ Pas de dropdown") . "\n";
        } else {
            echo "   📄 $name: ❌ Fichier introuvable\n";
        }
    }
    echo "\n";

    // 8. Recommandations
    echo "📋 RECOMMANDATIONS:\n";
    echo str_repeat("-", 50) . "\n";

    $issues = [];

    if ($permissions->isEmpty()) {
        $issues[] = "Exécuter le seeder: php artisan db:seed --class=DriverSanctionPermissionsSeeder";
    }

    if ($admins->isEmpty()) {
        $issues[] = "Créer un utilisateur admin: php artisan db:seed --class=AdminTestSeeder";
    }

    if ($driversCount == 0) {
        $issues[] = "Créer des chauffeurs de test: php artisan db:seed --class=DriverSeeder";
    }

    // Vérifier catalyst.blade.php spécifiquement
    $catalystPath = resource_path('views/layouts/admin/catalyst.blade.php');
    if (file_exists($catalystPath)) {
        $content = file_get_contents($catalystPath);
        if (!str_contains($content, 'admin.sanctions')) {
            $issues[] = "Le layout catalyst.blade.php ne contient pas le lien vers les sanctions";
        }
    }

    if (empty($issues)) {
        echo "✅ Tout semble configuré correctement!\n";
        echo "   1. Videz le cache: php artisan cache:clear && php artisan view:clear\n";
        echo "   2. Recompilez les assets: npm run build\n";
        echo "   3. Accédez à: http://localhost/admin/sanctions\n";
    } else {
        echo "⚠️  Actions requises:\n";
        foreach ($issues as $i => $issue) {
            echo "   " . ($i + 1) . ". $issue\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✨ Test CLI terminé\n";
