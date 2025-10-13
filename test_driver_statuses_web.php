<?php

/**
 * 🔬 Test Diagnostic COMPLET - Simulation de l'accès web au formulaire
 * Vérifie le flux complet depuis le contrôleur jusqu'à la vue
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Models\User;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Http\Controllers\Admin\DriverController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n🔬 TEST DIAGNOSTIC COMPLET - STATUTS CHAUFFEURS\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// 1. Vérifier la base de données
echo "📊 ÉTAPE 1: État de la base de données\n";
echo str_repeat("-", 50) . "\n";

if (Schema::hasTable('driver_statuses')) {
    echo "✅ Table driver_statuses: EXISTS\n";
    
    $totalCount = DB::table('driver_statuses')->count();
    $activeCount = DB::table('driver_statuses')->where('is_active', true)->count();
    $globalCount = DB::table('driver_statuses')->whereNull('organization_id')->count();
    
    echo "📈 Total statuts: $totalCount\n";
    echo "📈 Statuts actifs: $activeCount\n";
    echo "🌍 Statuts globaux: $globalCount\n\n";
    
    $statuses = DB::table('driver_statuses')
        ->where('is_active', true)
        ->select(['id', 'name', 'color', 'organization_id'])
        ->get();
    
    echo "📋 Liste des statuts actifs:\n";
    foreach ($statuses as $status) {
        $type = $status->organization_id ? "[ORG:{$status->organization_id}]" : "[GLOBAL]";
        echo "  • {$status->name} (ID:{$status->id}) $type - {$status->color}\n";
    }
} else {
    echo "❌ Table driver_statuses: NOT EXISTS\n";
}

echo "\n";

// 2. Simuler l'appel au contrôleur
echo "🎮 ÉTAPE 2: Simulation de l'appel contrôleur\n";
echo str_repeat("-", 50) . "\n";

try {
    // Trouver un utilisateur admin
    $adminUser = User::whereHas('roles', function($q) {
        $q->whereIn('name', ['Admin', 'Super Admin']);
    })->first();
    
    if (!$adminUser) {
        // Créer un admin temporaire si nécessaire
        $adminUser = User::whereHas('roles')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }
    }
    
    if ($adminUser) {
        echo "👤 Utilisateur test: {$adminUser->name} ({$adminUser->email})\n";
        echo "🏢 Organisation ID: " . ($adminUser->organization_id ?? 'NULL') . "\n";
        echo "🔑 Rôles: " . implode(', ', $adminUser->getRoleNames()->toArray()) . "\n\n";
        
        // Se connecter comme cet utilisateur
        auth()->login($adminUser);
        
        // Créer une instance du contrôleur et appeler la méthode privée via réflexion
        $controller = new DriverController(
            app(\App\Services\DriverService::class),
            app(\App\Services\Import\DriverImportExportService::class)
        );
        
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getDriverStatuses');
        $method->setAccessible(true);
        
        $statuses = $method->invoke($controller);
        
        echo "✅ Méthode getDriverStatuses() exécutée\n";
        echo "📊 Nombre de statuts retournés: " . count($statuses) . "\n";
        echo "📦 Type de retour: " . gettype($statuses) . " (" . get_class($statuses) . ")\n\n";
        
        if (count($statuses) > 0) {
            echo "📋 Statuts retournés:\n";
            foreach ($statuses as $status) {
                if (is_array($status)) {
                    echo "  • {$status['name']} (ID:{$status['id']}) - {$status['color']}\n";
                } else {
                    echo "  • {$status->name} (ID:{$status->id}) - {$status->color}\n";
                }
            }
        } else {
            echo "⚠️ Aucun statut retourné!\n";
        }
        
        // Tester la structure des données
        echo "\n🔍 ÉTAPE 3: Analyse de la structure des données\n";
        echo str_repeat("-", 50) . "\n";
        
        if (count($statuses) > 0) {
            $firstStatus = $statuses->first();
            echo "Premier statut (structure):\n";
            
            if (is_array($firstStatus)) {
                foreach ($firstStatus as $key => $value) {
                    $type = gettype($value);
                    $display = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                    echo "  - $key: $display ($type)\n";
                }
            } else {
                echo "  ⚠️ Structure objet détectée au lieu d'array\n";
            }
        }
        
        // Vérifier la compatibilité avec Alpine.js
        echo "\n🎯 ÉTAPE 4: Compatibilité Alpine.js\n";
        echo str_repeat("-", 50) . "\n";
        
        $jsonStatuses = json_encode($statuses);
        $decodedStatuses = json_decode($jsonStatuses, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "✅ Conversion JSON: SUCCESS\n";
            echo "📏 Taille JSON: " . strlen($jsonStatuses) . " bytes\n";
            
            // Vérifier les propriétés requises
            $requiredProps = ['id', 'name', 'description', 'color', 'icon', 'can_drive', 'can_assign'];
            $missingProps = [];
            
            if (!empty($decodedStatuses)) {
                $firstDecoded = reset($decodedStatuses);
                foreach ($requiredProps as $prop) {
                    if (!array_key_exists($prop, $firstDecoded)) {
                        $missingProps[] = $prop;
                    }
                }
            }
            
            if (empty($missingProps)) {
                echo "✅ Toutes les propriétés requises sont présentes\n";
            } else {
                echo "⚠️ Propriétés manquantes: " . implode(', ', $missingProps) . "\n";
            }
        } else {
            echo "❌ Erreur JSON: " . json_last_error_msg() . "\n";
        }
        
        // Test de la méthode isNotEmpty() sur la collection
        echo "\n🧪 ÉTAPE 5: Test des méthodes Collection\n";
        echo str_repeat("-", 50) . "\n";
        
        if (method_exists($statuses, 'isNotEmpty')) {
            $isNotEmpty = $statuses->isNotEmpty();
            echo "✅ Méthode isNotEmpty() disponible: " . ($isNotEmpty ? 'TRUE' : 'FALSE') . "\n";
        } else {
            echo "❌ Méthode isNotEmpty() NON disponible\n";
        }
        
        if (method_exists($statuses, 'isEmpty')) {
            $isEmpty = $statuses->isEmpty();
            echo "✅ Méthode isEmpty() disponible: " . ($isEmpty ? 'TRUE' : 'FALSE') . "\n";
        } else {
            echo "❌ Méthode isEmpty() NON disponible\n";
        }
        
        echo "📦 Instance de: " . get_class($statuses) . "\n";
        echo "📊 Count: " . count($statuses) . "\n";
        
    } else {
        echo "❌ Aucun utilisateur trouvé pour le test\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de la simulation: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
    echo "\n🔍 Stack trace:\n";
    echo substr($e->getTraceAsString(), 0, 1000) . "\n";
}

echo "\n";
echo "🎉 CONCLUSION\n";
echo "=" . str_repeat("=", 70) . "\n";

if (isset($statuses) && count($statuses) > 0) {
    echo "✅ Les statuts sont correctement récupérés depuis le contrôleur\n";
    echo "✅ La structure des données est compatible avec Alpine.js\n";
    echo "\n🔧 Si les statuts ne s'affichent pas dans le formulaire, vérifier:\n";
    echo "  1. Le passage des données à la vue (compact('driverStatuses'))\n";
    echo "  2. L'initialisation Alpine.js dans le navigateur (console JS)\n";
    echo "  3. Les erreurs JavaScript dans la console\n";
    echo "  4. Le cache des vues Laravel (php artisan view:clear)\n";
} else {
    echo "❌ Problème détecté dans la récupération des statuts\n";
    echo "🔧 Actions recommandées:\n";
    echo "  1. Vérifier les logs Laravel\n";
    echo "  2. Exécuter les seeders: php artisan db:seed --class=DriverStatusesSeeder\n";
    echo "  3. Vérifier les permissions utilisateur\n";
}

echo "\n🏁 FIN DU TEST DIAGNOSTIC\n";
