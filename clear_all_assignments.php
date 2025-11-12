<?php

/**
 * 🗑️ SCRIPT DE SUPPRESSION DES AFFECTATIONS
 * 
 * Script Enterprise-Grade pour supprimer toutes les affectations
 * afin de permettre des tests avec de nouvelles affectations.
 * 
 * @version 1.0.0-Enterprise
 * @author Chief Software Architect - ZenFleet
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Assignment;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║      🗑️  SUPPRESSION DES AFFECTATIONS - ENTERPRISE GRADE          ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// 1. ANALYSE AVANT SUPPRESSION
echo "📊 ÉTAT ACTUEL DES AFFECTATIONS\n";
echo str_repeat("─", 70) . "\n";

$totalAssignments = Assignment::count();
$activeAssignments = Assignment::where('status', 'active')->count();
$completedAssignments = Assignment::where('status', 'completed')->count();
$vehiclesWithAssignments = Vehicle::has('assignments')->count();

echo "• Total d'affectations: " . $totalAssignments . "\n";
echo "• Affectations actives: " . $activeAssignments . "\n";
echo "• Affectations terminées: " . $completedAssignments . "\n";
echo "• Véhicules avec affectations: " . $vehiclesWithAssignments . "\n\n";

if ($totalAssignments === 0) {
    echo "✅ Aucune affectation à supprimer.\n\n";
    exit(0);
}

// 2. DEMANDE DE CONFIRMATION
echo "⚠️  ATTENTION: Cette action est IRRÉVERSIBLE!\n";
echo "Voulez-vous vraiment supprimer TOUTES les " . $totalAssignments . " affectations? (oui/non): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'oui') {
    echo "\n❌ Suppression annulée.\n\n";
    exit(0);
}

// 3. SUPPRESSION DES AFFECTATIONS
echo "\n🔄 SUPPRESSION EN COURS...\n";
echo str_repeat("─", 70) . "\n";

try {
    DB::beginTransaction();
    
    // Récupération des IDs avant suppression pour logging
    $assignmentIds = Assignment::pluck('id')->toArray();
    
    // Suppression de toutes les affectations
    $deletedCount = Assignment::query()->delete();
    
    // Mise à jour du statut des véhicules si nécessaire
    // (Les véhicules sans affectation devraient être en statut "parking")
    $updatedVehicles = Vehicle::whereIn('status', ['affecte'])
        ->whereDoesntHave('assignments')
        ->update(['status' => 'parking']);
    
    DB::commit();
    
    echo "✅ " . $deletedCount . " affectation(s) supprimée(s) avec succès.\n";
    if ($updatedVehicles > 0) {
        echo "✅ " . $updatedVehicles . " véhicule(s) remis en statut 'parking'.\n";
    }
    
    // 4. VÉRIFICATION APRÈS SUPPRESSION
    echo "\n📊 ÉTAT APRÈS SUPPRESSION\n";
    echo str_repeat("─", 70) . "\n";
    
    $remainingAssignments = Assignment::count();
    $vehiclesWithAssignments = Vehicle::has('assignments')->count();
    
    echo "• Affectations restantes: " . $remainingAssignments . "\n";
    echo "• Véhicules avec affectations: " . $vehiclesWithAssignments . "\n";
    
    if ($remainingAssignments === 0) {
        echo "\n✅ Toutes les affectations ont été supprimées avec succès!\n";
        echo "Vous pouvez maintenant créer de nouvelles affectations pour vos tests.\n";
    } else {
        echo "\n⚠️  Il reste encore " . $remainingAssignments . " affectations.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERREUR lors de la suppression: " . $e->getMessage() . "\n";
    echo "La transaction a été annulée, aucune donnée n'a été modifiée.\n";
    exit(1);
}

// 5. RECOMMANDATIONS POST-SUPPRESSION
echo "\n💡 PROCHAINES ÉTAPES RECOMMANDÉES\n";
echo str_repeat("─", 70) . "\n";
echo "1. Créez de nouvelles affectations via l'interface d'administration\n";
echo "2. Vérifiez que les chauffeurs s'affichent correctement dans le tableau\n";
echo "3. Testez les différents statuts d'affectation (active, completed, etc.)\n";
echo "4. Vérifiez les indicateurs visuels (photos, badges de statut)\n";

echo "\n";
echo "═══════════════════════════════════════════════════════════════════════\n";
echo "✅ OPÉRATION TERMINÉE - Base de données prête pour les tests\n";
echo "═══════════════════════════════════════════════════════════════════════\n";
echo "\n";
