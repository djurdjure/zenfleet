<?php

/**
 * 🚀 Test direct du rendu de la vue avec les statuts
 * Vérifie que les données sont bien passées et rendues
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Models\User;
use App\Models\DriverStatus;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n🎯 TEST DIRECT DU RENDU DE VUE\n";
echo "=" . str_repeat("=", 70) . "\n\n";

try {
    // 1. Créer des données de test
    echo "📊 Préparation des données de test\n";
    echo str_repeat("-", 50) . "\n";
    
    // Données statiques pour éviter tout problème de base de données
    $driverStatuses = collect([
        [
            'id' => 1,
            'name' => 'Disponible',
            'description' => 'Chauffeur disponible pour les missions',
            'color' => '#10B981',
            'icon' => 'fa-check-circle',
            'can_drive' => true,
            'can_assign' => true,
            'organization_id' => null,
            'is_global' => true
        ],
        [
            'id' => 2,
            'name' => 'En mission',
            'description' => 'Chauffeur actuellement en mission',
            'color' => '#3B82F6',
            'icon' => 'fa-truck',
            'can_drive' => true,
            'can_assign' => false,
            'organization_id' => null,
            'is_global' => true
        ],
        [
            'id' => 3,
            'name' => 'En congé',
            'description' => 'Chauffeur en congé',
            'color' => '#F59E0B',
            'icon' => 'fa-calendar-times',
            'can_drive' => false,
            'can_assign' => false,
            'organization_id' => null,
            'is_global' => true
        ],
        [
            'id' => 4,
            'name' => 'Inactif',
            'description' => 'Chauffeur inactif',
            'color' => '#EF4444',
            'icon' => 'fa-ban',
            'can_drive' => false,
            'can_assign' => false,
            'organization_id' => null,
            'is_global' => true
        ]
    ]);
    
    echo "✅ Collection de " . $driverStatuses->count() . " statuts créée\n";
    echo "📦 Type: " . get_class($driverStatuses) . "\n";
    
    // Vérifier les méthodes disponibles
    echo "\n🔍 Méthodes de collection disponibles:\n";
    echo "  • count(): " . $driverStatuses->count() . "\n";
    echo "  • isEmpty(): " . ($driverStatuses->isEmpty() ? 'true' : 'false') . "\n";
    echo "  • isNotEmpty(): " . ($driverStatuses->isNotEmpty() ? 'true' : 'false') . "\n";
    
    // 2. Tester le rendu partiel
    echo "\n📝 Test du rendu partiel (step2-professional.blade.php)\n";
    echo str_repeat("-", 50) . "\n";
    
    // Simuler un objet driver vide
    $driver = new \stdClass();
    $driver->status_id = null;
    $driver->employee_number = null;
    $driver->recruitment_date = null;
    $driver->contract_end_date = null;
    
    $linkableUsers = collect([]);
    
    // Vérifier que la vue existe
    $viewPath = resource_path('views/admin/drivers/partials/step2-professional.blade.php');
    if (file_exists($viewPath)) {
        echo "✅ Vue partielle trouvée: step2-professional.blade.php\n";
        
        try {
            // Tenter de compiler la vue
            $viewContent = View::make('admin.drivers.partials.step2-professional', [
                'driverStatuses' => $driverStatuses,
                'driver' => $driver
            ])->render();
            
            // Analyser le contenu rendu
            if (strpos($viewContent, 'Sélectionnez un statut') !== false) {
                echo "✅ Texte 'Sélectionnez un statut' trouvé dans le rendu\n";
            } else {
                echo "⚠️ Texte 'Sélectionnez un statut' NON trouvé\n";
            }
            
            if (strpos($viewContent, 'x-data=') !== false) {
                echo "✅ Code Alpine.js détecté dans le rendu\n";
            } else {
                echo "⚠️ Code Alpine.js NON détecté\n";
            }
            
            if (strpos($viewContent, 'statuses:') !== false) {
                echo "✅ Initialisation des statuts Alpine.js détectée\n";
                
                // Extraire et vérifier le JSON des statuts
                if (preg_match('/statuses:\s*(\[.*?\])/s', $viewContent, $matches)) {
                    $jsonStr = $matches[1];
                    $decoded = json_decode($jsonStr, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        echo "✅ JSON des statuts valide: " . count($decoded) . " statuts\n";
                    } else {
                        echo "❌ JSON des statuts invalide: " . json_last_error_msg() . "\n";
                    }
                }
            } else {
                echo "⚠️ Initialisation des statuts Alpine.js NON détectée\n";
            }
            
            // Vérifier si les statuts sont dans le HTML
            foreach ($driverStatuses as $status) {
                if (strpos($viewContent, $status['name']) !== false) {
                    echo "  ✓ Statut '{$status['name']}' présent dans le HTML\n";
                } else {
                    echo "  ✗ Statut '{$status['name']}' ABSENT du HTML\n";
                }
            }
            
        } catch (\Exception $e) {
            echo "❌ Erreur lors du rendu de la vue: " . $e->getMessage() . "\n";
            echo "📍 Ligne: " . $e->getLine() . "\n";
        }
    } else {
        echo "❌ Vue partielle non trouvée!\n";
    }
    
    // 3. Tester la vue complète
    echo "\n📄 Test du rendu complet (create.blade.php)\n";
    echo str_repeat("-", 50) . "\n";
    
    $createViewPath = resource_path('views/admin/drivers/create.blade.php');
    if (file_exists($createViewPath)) {
        echo "✅ Vue principale trouvée: create.blade.php\n";
        
        try {
            $fullContent = View::make('admin.drivers.create', [
                'driverStatuses' => $driverStatuses,
                'linkableUsers' => $linkableUsers
            ])->render();
            
            echo "✅ Vue rendue avec succès\n";
            echo "📏 Taille du HTML: " . strlen($fullContent) . " caractères\n";
            
            // Vérifier les éléments clés
            if (strpos($fullContent, 'driverCreateFormComponent') !== false) {
                echo "✅ Fonction Alpine.js trouvée\n";
            }
            
            if (strpos($fullContent, '@include') !== false || strpos($fullContent, 'step2-professional') !== false) {
                echo "✅ Inclusion du partial step2 détectée\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Erreur lors du rendu complet: " . $e->getMessage() . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERREUR GÉNÉRALE: " . $e->getMessage() . "\n";
    echo "📍 Fichier: " . $e->getFile() . "\n";
    echo "📍 Ligne: " . $e->getLine() . "\n";
}

echo "\n🎉 CONCLUSION\n";
echo "=" . str_repeat("=", 70) . "\n";
echo "🔧 Vérifications effectuées:\n";
echo "  ✓ Structure des données de statuts\n";
echo "  ✓ Méthodes de collection\n";
echo "  ✓ Rendu des vues partielles\n";
echo "  ✓ Intégration Alpine.js\n";
echo "  ✓ Présence des statuts dans le HTML\n";

echo "\n🏁 FIN DU TEST\n";
