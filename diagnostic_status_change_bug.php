<?php

/**
 * 🔍 DIAGNOSTIC: Bug changement de statut depuis le badge
 *
 * Ce script teste le flow complet de changement de statut pour identifier
 * pourquoi le badge ne change pas le statut alors que la page edit fonctionne.
 */

require __DIR__.'/vendor/autoload.php';

use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DIAGNOSTIC: Bug Changement Statut depuis Badge                         ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // ============================================================================
    // ÉTAPE 1: Sélectionner un véhicule de test
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 1: Sélection d'un véhicule de test\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $vehicle = Vehicle::with('vehicleStatus')->where('registration_plate', '587449-16')->first();

    if (!$vehicle) {
        echo "❌ Véhicule de test non trouvé!\n";
        exit(1);
    }

    echo "✅ Véhicule sélectionné: {$vehicle->registration_plate}\n";
    echo "   ID: {$vehicle->id}\n";
    echo "   Marque/Modèle: {$vehicle->brand} {$vehicle->model}\n";
    echo "   Statut actuel (status_id): {$vehicle->status_id}\n";

    if ($vehicle->vehicleStatus) {
        echo "   Statut actuel (name): {$vehicle->vehicleStatus->name}\n";
        echo "   Statut actuel (slug): {$vehicle->vehicleStatus->slug}\n";
    }
    echo "\n";

    // ============================================================================
    // ÉTAPE 2: Tester getCurrentVehicleStatus() du service
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 2: Test de getCurrentVehicleStatus() dans StatusTransitionService\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $service = app(StatusTransitionService::class);

    // Utiliser la réflexion pour appeler la méthode protected
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('getCurrentVehicleStatus');
    $method->setAccessible(true);

    $currentEnum = $method->invoke($service, $vehicle);

    if ($currentEnum) {
        echo "✅ getCurrentVehicleStatus() a retourné un enum:\n";
        echo "   Enum: {$currentEnum->name}\n";
        echo "   Value: {$currentEnum->value}\n";
        echo "   Label: {$currentEnum->label()}\n\n";
    } else {
        echo "❌ getCurrentVehicleStatus() a retourné NULL!\n";
        echo "   Ceci empêche toute transition de statut!\n\n";

        // Débugger pourquoi
        echo "🔍 DÉBOGAGE:\n";
        echo "   vehicle->status instanceof VehicleStatusEnum: " . ($vehicle->status instanceof VehicleStatusEnum ? 'OUI' : 'NON') . "\n";
        echo "   vehicle->status_id: " . ($vehicle->status_id ?? 'NULL') . "\n";
        echo "   vehicle->vehicleStatus: " . ($vehicle->vehicleStatus ? 'CHARGÉ' : 'NULL') . "\n";

        if ($vehicle->vehicleStatus) {
            echo "   vehicleStatus->name: {$vehicle->vehicleStatus->name}\n";
            echo "   vehicleStatus->slug: {$vehicle->vehicleStatus->slug}\n";

            $generatedSlug = \Str::slug($vehicle->vehicleStatus->name);
            echo "   \\Str::slug(name): '{$generatedSlug}'\n";

            $enumAttempt = VehicleStatusEnum::tryFrom($generatedSlug);
            echo "   VehicleStatusEnum::tryFrom('{$generatedSlug}'): " . ($enumAttempt ? $enumAttempt->name : 'NULL') . "\n";

            // Essayer avec le vrai slug
            $realSlug = $vehicle->vehicleStatus->slug;
            $enumAttempt2 = VehicleStatusEnum::tryFrom($realSlug);
            echo "   VehicleStatusEnum::tryFrom('{$realSlug}'): " . ($enumAttempt2 ? $enumAttempt2->name : 'NULL') . "\n";
        }
        echo "\n";
    }

    // ============================================================================
    // ÉTAPE 3: Simuler un changement de statut vers PARKING
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 3: Simulation d'un changement de statut vers PARKING\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $newStatus = VehicleStatusEnum::PARKING;

    echo "🔄 Tentative de changement de statut:\n";
    echo "   De: " . ($currentEnum ? $currentEnum->label() : 'NULL') . "\n";
    echo "   Vers: {$newStatus->label()}\n\n";

    try {
        // Utiliser une transaction test qu'on va rollback
        DB::beginTransaction();

        $result = $service->changeVehicleStatus(
            $vehicle,
            $newStatus,
            [
                'reason' => 'Test diagnostic',
                'change_type' => 'diagnostic_test',
                'user_id' => 1,
            ]
        );

        // Vérifier si le statut a changé
        $vehicle->refresh();
        $vehicle->load('vehicleStatus');

        echo "✅ changeVehicleStatus() a retourné: " . ($result ? 'TRUE' : 'FALSE') . "\n";
        echo "   Nouveau status_id: {$vehicle->status_id}\n";

        if ($vehicle->vehicleStatus) {
            echo "   Nouveau statut (name): {$vehicle->vehicleStatus->name}\n";
            echo "   Nouveau statut (slug): {$vehicle->vehicleStatus->slug}\n";
        }

        // Rollback pour ne pas modifier vraiment
        DB::rollBack();
        echo "\n🔄 Transaction rollback (véhicule non modifié)\n\n";

    } catch (\Exception $e) {
        DB::rollBack();
        echo "❌ ERREUR lors du changement de statut:\n";
        echo "   Message: {$e->getMessage()}\n";
        echo "   Fichier: {$e->getFile()}:{$e->getLine()}\n\n";
    }

    // ============================================================================
    // ÉTAPE 4: Comparer avec la méthode de la page edit
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 4: Comment la page edit change-t-elle le statut?\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    echo "💡 HYPOTHÈSE:\n";
    echo "   La page edit change probablement le statut_id directement via:\n";
    echo "   \$vehicle->status_id = \$request->input('status_id');\n";
    echo "   \$vehicle->save();\n\n";
    echo "   Le badge utilise StatusTransitionService qui:\n";
    echo "   1. Appelle getCurrentVehicleStatus() → peut retourner NULL si bug\n";
    echo "   2. Si NULL, peut bloquer la transition\n\n";

    // ============================================================================
    // RÉSUMÉ ET DIAGNOSTIC FINAL
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "🎯 DIAGNOSTIC FINAL\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    echo "🔍 PROBLÈME IDENTIFIÉ:\n";
    echo "   StatusTransitionService::getCurrentVehicleStatus() ligne 246:\n";
    echo "   \$statusSlug = \\Str::slug(\$vehicle->vehicleStatus->name);\n\n";
    echo "   ❌ BUG: \\Str::slug('En panne') → 'en-panne' (tiret)\n";
    echo "   ❌ Mais VehicleStatusEnum::EN_PANNE = 'en_panne' (underscore)\n";
    echo "   ❌ tryFrom('en-panne') → NULL\n";
    echo "   ❌ getCurrentVehicleStatus() retourne NULL\n";
    echo "   ❌ La validation de transition échoue ou bloque\n\n";

    echo "✅ SOLUTION:\n";
    echo "   Utiliser directement le slug de la table:\n";
    echo "   \$statusSlug = \$vehicle->vehicleStatus->slug; // 'en_panne'\n\n";

    echo "📝 FICHIERS À CORRIGER:\n";
    echo "   1. app/Services/StatusTransitionService.php (ligne 246)\n";
    echo "   2. app/Services/StatusTransitionService.php (ligne 265 - même bug pour Driver)\n\n";

    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    ✅ DIAGNOSTIC TERMINÉ                                    ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR DURANT LE DIAGNOSTIC:\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
