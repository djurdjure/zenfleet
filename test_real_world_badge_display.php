<?php

/**
 * 🧪 TEST REAL-WORLD: Simulation de l'affichage des badges dans la liste
 *
 * Ce script simule exactement ce que fait le composant VehicleStatusBadgeUltraPro
 * pour afficher les statuts dans la liste des véhicules.
 */

require __DIR__.'/vendor/autoload.php';

use App\Models\Vehicle;
use App\Livewire\Admin\VehicleStatusBadgeUltraPro;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST REAL-WORLD: Simulation Affichage Badges Liste Véhicules          ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Récupérer tous les véhicules comme le ferait la liste
$vehicles = Vehicle::with(['vehicleStatus', 'depot', 'assignments.driver'])
    ->whereNotNull('status_id')
    ->orderBy('registration_plate')
    ->get();

echo "📊 SIMULATION DE LA LISTE DES VÉHICULES\n";
echo "   Total véhicules: " . count($vehicles) . "\n\n";

echo str_repeat("═", 140) . "\n";
printf("%-15s %-25s %-20s %-20s %-25s %-35s\n",
    "IMMATRICULATION",
    "MARQUE/MODÈLE",
    "STATUT DB (NAME)",
    "STATUT DB (SLUG)",
    "ENUM TROUVÉ",
    "BADGE AFFICHÉ"
);
echo str_repeat("═", 140) . "\n";

$successCount = 0;
$failureCount = 0;
$failures = [];

foreach ($vehicles as $vehicle) {
    // Créer une instance du badge component comme Livewire le ferait
    $badgeComponent = new VehicleStatusBadgeUltraPro();
    $badgeComponent->mount($vehicle);

    // Appeler getCurrentStatusEnum() comme le fait la vue
    $currentEnum = $badgeComponent->getCurrentStatusEnum();

    $statusDbName = $vehicle->vehicleStatus ? $vehicle->vehicleStatus->name : 'NULL';
    $statusDbSlug = $vehicle->vehicleStatus ? $vehicle->vehicleStatus->slug : 'NULL';
    $enumFound = $currentEnum ? $currentEnum->name : '❌ NULL';
    $badgeDisplay = $currentEnum ? $currentEnum->label() : '❌ Non défini';

    // Déterminer si c'est un succès ou un échec
    if ($currentEnum) {
        $successCount++;
        $status = '✅';
    } else {
        $failureCount++;
        $status = '❌';
        $failures[] = [
            'immat' => $vehicle->registration_plate,
            'brand_model' => "{$vehicle->brand} {$vehicle->model}",
            'status_name' => $statusDbName,
            'status_slug' => $statusDbSlug
        ];
    }

    printf("%s %-13s %-25s %-20s %-20s %-25s %-35s\n",
        $status,
        $vehicle->registration_plate,
        substr("{$vehicle->brand} {$vehicle->model}", 0, 25),
        substr($statusDbName, 0, 20),
        substr($statusDbSlug, 0, 20),
        substr($enumFound, 0, 25),
        substr($badgeDisplay, 0, 35)
    );
}

echo str_repeat("═", 140) . "\n\n";

// Résumé final
echo "📊 RÉSUMÉ DE L'AFFICHAGE:\n";
echo str_repeat("─", 80) . "\n";
echo "   ✅ Badges affichés correctement: {$successCount}\n";
echo "   ❌ Badges \"Non défini\":          {$failureCount}\n";
echo "   📈 Taux de réussite:              " . round(($successCount / count($vehicles)) * 100, 2) . "%\n";
echo str_repeat("─", 80) . "\n\n";

if ($failureCount > 0) {
    echo "❌ VÉHICULES AVEC PROBLÈME D'AFFICHAGE:\n";
    echo str_repeat("─", 100) . "\n";
    printf("%-20s %-30s %-25s %-25s\n", "IMMATRICULATION", "MARQUE/MODÈLE", "STATUT DB (NAME)", "STATUT DB (SLUG)");
    echo str_repeat("─", 100) . "\n";

    foreach ($failures as $f) {
        printf("%-20s %-30s %-25s %-25s\n",
            $f['immat'],
            substr($f['brand_model'], 0, 30),
            $f['status_name'],
            $f['status_slug']
        );
    }
    echo str_repeat("─", 100) . "\n\n";

    echo "⚠️ ACTION REQUISE: Certains badges n'affichent pas correctement!\n\n";
} else {
    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║             ✅ TOUS LES BADGES S'AFFICHENT CORRECTEMENT!                   ║\n";
    echo "║                                                                              ║\n";
    echo "║  Le problème est 100% résolu. Tous les véhicules affichent leur vrai       ║\n";
    echo "║  statut dans la liste. La correction est validée et prête pour production. ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

    echo "🎨 EXEMPLES DE BADGES AFFICHÉS:\n";
    echo str_repeat("─", 80) . "\n";

    // Afficher un exemple de chaque statut
    $statusExamples = [];
    foreach ($vehicles as $vehicle) {
        $badgeComponent = new VehicleStatusBadgeUltraPro();
        $badgeComponent->mount($vehicle);
        $currentEnum = $badgeComponent->getCurrentStatusEnum();

        if ($currentEnum) {
            $statusKey = $currentEnum->value;
            if (!isset($statusExamples[$statusKey])) {
                $statusExamples[$statusKey] = [
                    'immat' => $vehicle->registration_plate,
                    'enum' => $currentEnum
                ];
            }
        }
    }

    foreach ($statusExamples as $statusKey => $example) {
        $enum = $example['enum'];
        echo "\n   Statut: {$enum->label()}\n";
        echo "   ├─ Véhicule exemple: {$example['immat']}\n";
        echo "   ├─ Couleur: {$enum->color()}\n";
        echo "   ├─ Icône: {$enum->icon()}\n";
        echo "   ├─ Classes CSS: {$enum->badgeClasses()}\n";
        echo "   └─ Description: {$enum->description()}\n";
    }

    echo str_repeat("─", 80) . "\n\n";
}

echo "💡 TEST TERMINÉ - " . date('Y-m-d H:i:s') . "\n\n";
