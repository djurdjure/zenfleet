<?php
/**
 * Test de restauration de chauffeur
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Driver;
use Illuminate\Support\Facades\Route;

echo "=== TEST RESTAURATION CHAUFFEUR ===\n\n";

// Test 1 : Vérifier la route
echo "1. Vérification de la route restore\n";
echo "─────────────────────────────────────────────────\n";

$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->getName() ?? '', 'drivers.restore')) {
        echo "✅ Route trouvée : " . $route->getName() . "\n";
        echo "   URI : " . $route->uri() . "\n";
        echo "   Méthode : " . implode('|', $route->methods()) . "\n";
        echo "   Action : " . $route->getActionName() . "\n\n";
    }
}

// Test 2 : Trouver un chauffeur archivé
echo "2. Recherche d'un chauffeur archivé\n";
echo "─────────────────────────────────────────────────\n";

$archivedDriver = Driver::onlyTrashed()->first();

if ($archivedDriver) {
    echo "✅ Chauffeur archivé trouvé :\n";
    echo "   ID : {$archivedDriver->id}\n";
    echo "   Nom : {$archivedDriver->first_name} {$archivedDriver->last_name}\n";
    echo "   Matricule : {$archivedDriver->employee_number}\n";
    echo "   Archivé le : {$archivedDriver->deleted_at}\n\n";
    
    // Test 3 : Vérifier si on peut restaurer
    echo "3. Test de restauration\n";
    echo "─────────────────────────────────────────────────\n";
    
    try {
        // Tester la restauration
        $result = $archivedDriver->restore();
        
        if ($result) {
            echo "✅ Restauration réussie !\n";
            echo "   Le chauffeur est maintenant actif\n\n";
            
            // Vérifier qu'il est bien actif
            $activeDriver = Driver::find($archivedDriver->id);
            if ($activeDriver && !$activeDriver->deleted_at) {
                echo "✅ Vérification : Le chauffeur est bien actif\n";
                echo "   deleted_at : " . ($activeDriver->deleted_at ?? 'NULL') . "\n\n";
                
                // Re-archiver pour les prochains tests
                $activeDriver->delete();
                echo "ℹ️  Chauffeur ré-archivé pour les prochains tests\n";
            }
        } else {
            echo "❌ La restauration a échoué\n";
        }
    } catch (\Exception $e) {
        echo "❌ Erreur lors de la restauration :\n";
        echo "   " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠️  Aucun chauffeur archivé trouvé dans la base\n";
    echo "   Créez un chauffeur et archivenez-le pour tester\n";
}

echo "\n=== FIN DES TESTS ===\n";
