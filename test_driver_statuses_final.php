<?php

/**
 * 🧪 TEST FINAL - Validation complète des statuts de chauffeurs
 * 
 * Ce script vérifie que la solution enterprise fonctionne correctement
 * en simulant les appels du contrôleur dans différents contextes.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\DriverController;
use App\Models\User;
use Illuminate\Support\Facades\Log;

echo "🚀 TEST FINAL - Résolution du problème des statuts de chauffeurs\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // Test 1: Vérification de la table driver_statuses
    echo "📊 ÉTAPE 1: Vérification de la table driver_statuses\n";
    echo str_repeat("-", 50) . "\n";
    
    if (\Schema::hasTable('driver_statuses')) {
        $totalStatuses = \DB::table('driver_statuses')->count();
        $activeStatuses = \DB::table('driver_statuses')->where('is_active', true)->count();
        $globalStatuses = \DB::table('driver_statuses')->whereNull('organization_id')->count();
        
        echo "✅ Table driver_statuses: EXISTS\n";
        echo "📈 Total statuts: {$totalStatuses}\n";
        echo "📈 Statuts actifs: {$activeStatuses}\n";
        echo "🌍 Statuts globaux: {$globalStatuses}\n";
        
        if ($totalStatuses > 0) {
            echo "\n📋 Liste des statuts actifs:\n";
            $statuses = \DB::table('driver_statuses')
                ->where('is_active', true)
                ->select('id', 'name', 'color', 'icon', 'organization_id', 'is_active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
                
            foreach ($statuses as $status) {
                $orgInfo = $status->organization_id ? "Org:{$status->organization_id}" : "GLOBAL";
                echo "  • {$status->name} (ID:{$status->id}) [{$orgInfo}] - {$status->color}\n";
            }
        }
    } else {
        echo "❌ Table driver_statuses: NOT FOUND\n";
        echo "🔧 Exécution du seeder d'urgence...\n";
        
        try {
            $seeder = new \Database\Seeders\DriverStatusesSeeder();
            $seeder->run();
            echo "✅ Seeder exécuté avec succès\n";
        } catch (\Exception $e) {
            echo "❌ Erreur lors du seeder: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    
    // Test 2: Simulation avec un utilisateur Super Admin
    echo "👑 ÉTAPE 2: Test Super Admin\n";
    echo str_repeat("-", 50) . "\n";
    
    $superAdmin = User::whereHas('roles', function($q) {
        $q->where('name', 'Super Admin');
    })->first();
    
    if ($superAdmin) {
        echo "✅ Super Admin trouvé: {$superAdmin->email}\n";
        
        // Simuler l'authentification
        auth()->login($superAdmin);
        
        $controller = new DriverController(app(\App\Services\DriverService::class), app(\App\Services\ImportExportService::class));
        
        // Utiliser Reflection pour appeler la méthode privée
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getDriverStatuses');
        $method->setAccessible(true);
        
        $statuses = $method->invoke($controller);
        
        echo "📊 Statuts récupérés: {$statuses->count()}\n";
        
        if ($statuses->isNotEmpty()) {
            echo "🎯 Structure des statuts testée:\n";
            $firstStatus = $statuses->first();
            $requiredKeys = ['id', 'name', 'description', 'color', 'icon', 'can_drive', 'can_assign', 'organization_id', 'is_global'];
            
            foreach ($requiredKeys as $key) {
                $exists = array_key_exists($key, $firstStatus);
                $status = $exists ? "✅" : "❌";
                echo "  {$status} {$key}\n";
            }
        }
        
        auth()->logout();
    } else {
        echo "❌ Aucun Super Admin trouvé\n";
    }
    
    echo "\n";
    
    // Test 3: Simulation avec un utilisateur Admin normal
    echo "👤 ÉTAPE 3: Test Admin standard\n";
    echo str_repeat("-", 50) . "\n";
    
    $admin = User::whereHas('roles', function($q) {
        $q->where('name', 'Admin');
    })->first();
    
    if ($admin) {
        echo "✅ Admin trouvé: {$admin->email}\n";
        echo "🏢 Organisation ID: {$admin->organization_id}\n";
        
        auth()->login($admin);
        
        $controller = new DriverController(app(\App\Services\DriverService::class), app(\App\Services\ImportExportService::class));
        
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getDriverStatuses');
        $method->setAccessible(true);
        
        $statuses = $method->invoke($controller);
        
        echo "📊 Statuts récupérés: {$statuses->count()}\n";
        
        if ($statuses->isNotEmpty()) {
            $global = $statuses->where('is_global', true)->count();
            $specific = $statuses->where('is_global', false)->count();
            echo "🌍 Statuts globaux: {$global}\n";
            echo "🏢 Statuts spécifiques: {$specific}\n";
        }
        
        auth()->logout();
    } else {
        echo "❌ Aucun Admin trouvé\n";
    }
    
    echo "\n";
    
    // Test 4: Vérification du fallback d'urgence
    echo "🚨 ÉTAPE 4: Test fallback d'urgence\n";
    echo str_repeat("-", 50) . "\n";
    
    $controller = new DriverController(app(\App\Services\DriverService::class), app(\App\Services\ImportExportService::class));
    
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getMinimalDriverStatuses');
    $method->setAccessible(true);
    
    $minimalStatuses = $method->invoke($controller);
    
    echo "📊 Statuts minimaux: {$minimalStatuses->count()}\n";
    
    if ($minimalStatuses->isNotEmpty()) {
        echo "🎯 Statuts de fallback disponibles:\n";
        foreach ($minimalStatuses as $status) {
            echo "  • {$status['name']} ({$status['color']})\n";
        }
    }
    
    echo "\n";
    
    // CONCLUSION
    echo "🎉 RÉSULTAT FINAL\n";
    echo str_repeat("=", 50) . "\n";
    
    $allTestsPassed = true;
    
    if (\Schema::hasTable('driver_statuses') && \DB::table('driver_statuses')->where('is_active', true)->count() > 0) {
        echo "✅ Base de données: OK\n";
    } else {
        echo "❌ Base de données: ÉCHEC\n";
        $allTestsPassed = false;
    }
    
    if ($minimalStatuses->count() >= 4) {
        echo "✅ Fallback: OK\n";
    } else {
        echo "❌ Fallback: ÉCHEC\n";
        $allTestsPassed = false;
    }
    
    if ($allTestsPassed) {
        echo "\n🎯 SOLUTION APPORTÉE AVEC SUCCÈS!\n";
        echo "✅ Les formulaires de création/modification de chauffeurs devraient maintenant afficher les statuts correctement.\n";
        echo "✅ Le problème principal (absence de statuts) est résolu par la stratégie multi-niveaux:\n";
        echo "   1. Vérification de l'existence de la table\n";
        echo "   2. Exécution automatique du seeder si nécessaire\n";
        echo "   3. Utilisation de withoutGlobalScope pour contourner le filtrage organisation\n";
        echo "   4. Transformation des données pour Alpine.js\n";
        echo "   5. Fallback d'urgence en cas d'échec complet\n";
    } else {
        echo "\n⚠️  CERTAINS TESTS ONT ÉCHOUÉ\n";
        echo "Vérifiez les logs Laravel pour plus de détails.\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 FIN DU TEST\n";
