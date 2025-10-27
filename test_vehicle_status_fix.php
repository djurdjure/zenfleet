#!/usr/bin/env php
<?php

/**
 * Test et Validation - Correction de l'erreur "column status does not exist"
 * 
 * Ce script vérifie que la correction du problème de colonne 'status' 
 * dans la table vehicles est correctement appliquée.
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🔧 TEST DE CORRECTION - Erreur 'column status does not exist'\n";
echo str_repeat("=", 80) . "\n\n";

$testResults = [
    'passed' => 0,
    'failed' => 0,
    'warnings' => 0,
];

try {
    // ====================================================================
    // TEST 1: Vérification de la structure de la table vehicles
    // ====================================================================
    echo "📋 TEST 1: Structure de la table vehicles\n";
    echo str_repeat("-", 40) . "\n";
    
    $columns = Schema::getColumnListing('vehicles');
    
    // Vérifier que la colonne 'status' n'existe PAS
    if (!in_array('status', $columns)) {
        echo "✅ Colonne 'status' n'existe pas (attendu)\n";
        $testResults['passed']++;
    } else {
        echo "❌ Colonne 'status' existe (inattendu)\n";
        $testResults['failed']++;
    }
    
    // Vérifier que la colonne 'status_id' existe
    if (in_array('status_id', $columns)) {
        echo "✅ Colonne 'status_id' existe\n";
        $testResults['passed']++;
    } else {
        echo "❌ Colonne 'status_id' manquante\n";
        $testResults['failed']++;
    }
    
    // Vérifier que la colonne 'is_archived' existe
    if (in_array('is_archived', $columns)) {
        echo "✅ Colonne 'is_archived' existe\n";
        $testResults['passed']++;
    } else {
        echo "❌ Colonne 'is_archived' manquante\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // TEST 2: Vérification de la table vehicle_statuses
    // ====================================================================
    echo "\n📋 TEST 2: Table vehicle_statuses\n";
    echo str_repeat("-", 40) . "\n";
    
    if (Schema::hasTable('vehicle_statuses')) {
        echo "✅ Table vehicle_statuses existe\n";
        $testResults['passed']++;
        
        // Vérifier les statuts
        $statuses = DB::table('vehicle_statuses')->pluck('name', 'id');
        echo "   Statuts disponibles:\n";
        foreach ($statuses as $id => $name) {
            echo "   • ID {$id}: {$name}\n";
        }
        
        if (isset($statuses[1]) && in_array(strtolower($statuses[1]), ['actif', 'active'])) {
            echo "✅ Statut 'Actif' (ID=1) existe\n";
            $testResults['passed']++;
        } else {
            echo "⚠️  Statut 'Actif' (ID=1) non trouvé\n";
            $testResults['warnings']++;
        }
        
    } else {
        echo "❌ Table vehicle_statuses manquante\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // TEST 3: Vérification des scopes du modèle Vehicle
    // ====================================================================
    echo "\n📋 TEST 3: Scopes du modèle Vehicle\n";
    echo str_repeat("-", 40) . "\n";
    
    $vehicleReflection = new ReflectionClass(Vehicle::class);
    
    // Vérifier les scopes importants
    $requiredScopes = ['scopeActive', 'scopeVisible', 'scopeArchived', 'scopeWithArchived'];
    
    foreach ($requiredScopes as $scope) {
        if ($vehicleReflection->hasMethod($scope)) {
            echo "✅ Méthode {$scope} existe\n";
            $testResults['passed']++;
        } else {
            echo "❌ Méthode {$scope} manquante\n";
            $testResults['failed']++;
        }
    }
    
    // ====================================================================
    // TEST 4: Test des requêtes Vehicle avec les bons filtres
    // ====================================================================
    echo "\n📋 TEST 4: Test des requêtes avec scopes\n";
    echo str_repeat("-", 40) . "\n";
    
    $user = User::first();
    if ($user) {
        auth()->login($user);
        
        try {
            // Test avec active()
            $query1 = Vehicle::where('organization_id', $user->organization_id)
                ->active()
                ->toSql();
            
            if (strpos($query1, 'status_id') !== false) {
                echo "✅ Scope active() utilise status_id\n";
                $testResults['passed']++;
            } else {
                echo "⚠️  Scope active() ne semble pas utiliser status_id\n";
                $testResults['warnings']++;
            }
            
            // Test avec visible()
            $query2 = Vehicle::where('organization_id', $user->organization_id)
                ->visible()
                ->toSql();
            
            if (strpos($query2, 'is_archived') !== false) {
                echo "✅ Scope visible() utilise is_archived\n";
                $testResults['passed']++;
            } else {
                echo "⚠️  Scope visible() ne semble pas utiliser is_archived\n";
                $testResults['warnings']++;
            }
            
            // Test combiné
            $vehicles = Vehicle::where('organization_id', $user->organization_id)
                ->active()
                ->visible()
                ->limit(5)
                ->get();
                
            echo "✅ Requête combinée active()->visible() exécutée avec succès\n";
            echo "   Véhicules trouvés: " . $vehicles->count() . "\n";
            $testResults['passed']++;
            
        } catch (Exception $e) {
            echo "❌ Erreur lors des requêtes: " . $e->getMessage() . "\n";
            $testResults['failed']++;
        }
        
    } else {
        echo "⚠️  Aucun utilisateur pour les tests\n";
        $testResults['warnings']++;
    }
    
    // ====================================================================
    // TEST 5: Vérification du composant UpdateVehicleMileage
    // ====================================================================
    echo "\n📋 TEST 5: Composant UpdateVehicleMileage\n";
    echo str_repeat("-", 40) . "\n";
    
    $filePath = app_path('Livewire/Admin/UpdateVehicleMileage.php');
    $content = file_get_contents($filePath);
    
    // Vérifier qu'on n'utilise plus where('status', 'active')
    if (strpos($content, "->where('status', 'active')") === false &&
        strpos($content, '->where("status", "active")') === false) {
        echo "✅ Plus de référence à where('status', 'active')\n";
        $testResults['passed']++;
    } else {
        echo "❌ Référence à where('status', 'active') trouvée\n";
        $testResults['failed']++;
    }
    
    // Vérifier qu'on utilise les scopes
    if (strpos($content, '->active()') !== false && strpos($content, '->visible()') !== false) {
        echo "✅ Utilisation des scopes active() et visible()\n";
        $testResults['passed']++;
    } else {
        echo "⚠️  Scopes active() ou visible() non trouvés\n";
        $testResults['warnings']++;
    }
    
    // ====================================================================
    // TEST 6: Test d'intégration complète
    // ====================================================================
    echo "\n📋 TEST 6: Test d'intégration\n";
    echo str_repeat("-", 40) . "\n";
    
    if ($user) {
        try {
            // Simuler le comportement de getAvailableVehiclesProperty
            $vehicles = Vehicle::where('organization_id', $user->organization_id)
                ->active()
                ->visible()
                ->with(['category', 'depot'])
                ->orderBy('registration_plate')
                ->get();
                
            echo "✅ Requête getAvailableVehicles simulée avec succès\n";
            echo "   Nombre de véhicules: " . $vehicles->count() . "\n";
            
            if ($vehicles->count() > 0) {
                $vehicle = $vehicles->first();
                echo "   Premier véhicule: {$vehicle->registration_plate}\n";
                echo "   • Status ID: " . ($vehicle->status_id ?? 'null') . "\n";
                echo "   • Is Archived: " . ($vehicle->is_archived ? 'Oui' : 'Non') . "\n";
                echo "   • Catégorie: " . ($vehicle->category->name ?? 'Aucune') . "\n";
                echo "   • Dépôt: " . ($vehicle->depot->name ?? 'Aucun') . "\n";
            }
            
            $testResults['passed']++;
            
        } catch (\Illuminate\Database\QueryException $e) {
            if (strpos($e->getMessage(), 'column "status" does not exist') !== false) {
                echo "❌ ERREUR: La colonne 'status' est encore référencée!\n";
                echo "   Message: " . $e->getMessage() . "\n";
                $testResults['failed']++;
            } else {
                echo "❌ Erreur SQL: " . $e->getMessage() . "\n";
                $testResults['failed']++;
            }
        } catch (Exception $e) {
            echo "❌ Erreur: " . $e->getMessage() . "\n";
            $testResults['failed']++;
        }
    }
    
    // ====================================================================
    // RÉSUMÉ DES TESTS
    // ====================================================================
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "📊 RÉSUMÉ DES TESTS\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "✅ Tests réussis:    {$testResults['passed']}\n";
    echo "❌ Tests échoués:    {$testResults['failed']}\n";
    echo "⚠️  Avertissements:  {$testResults['warnings']}\n";
    
    $total = $testResults['passed'] + $testResults['failed'];
    if ($total > 0) {
        $successRate = round(($testResults['passed'] / $total) * 100, 2);
        echo "\n📈 Taux de réussite: {$successRate}%\n";
    }
    
    if ($testResults['failed'] === 0) {
        echo "\n🎉 SUCCÈS! L'erreur 'column status does not exist' est corrigée.\n";
        echo "   Les véhicules utilisent maintenant:\n";
        echo "   • status_id pour le statut (Actif/En maintenance/Inactif)\n";
        echo "   • is_archived pour l'archivage\n";
        echo "   • Les scopes active() et visible() pour les filtres\n";
    } else {
        echo "\n⚠️  ATTENTION: Des corrections supplémentaires sont nécessaires.\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    
    // ====================================================================
    // RECOMMANDATIONS
    // ====================================================================
    echo "\n📝 RECOMMANDATIONS D'ARCHITECTURE\n";
    echo str_repeat("=", 80) . "\n";
    echo "
Pour une architecture Enterprise-Grade optimale:

1. **Utilisation des Scopes** (✅ Implémenté)
   - Vehicle::active() → Véhicules avec status_id = 1
   - Vehicle::visible() → Véhicules non archivés
   - Vehicle::withArchived() → Inclure les archivés

2. **Structure de la Table**
   - status_id: Référence vehicle_statuses (1=Actif, 2=En maintenance, 3=Inactif)
   - is_archived: Boolean pour l'archivage logique
   - deleted_at: Soft delete Laravel

3. **Bonnes Pratiques**
   - Toujours utiliser les scopes au lieu de where() direct
   - Combiner active()->visible() pour les véhicules disponibles
   - Utiliser with() pour eager loading des relations

4. **Performance**
   - Index sur (organization_id, status_id, is_archived)
   - Index sur (organization_id, is_archived)
   - Eager loading des relations fréquentes

5. **Sécurité Multi-Tenant**
   - Toujours filtrer par organization_id en premier
   - Utiliser le trait BelongsToOrganization
   - Vérifier les permissions par rôle
";

} catch (Exception $e) {
    echo "\n❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
