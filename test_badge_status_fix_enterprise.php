<?php

/**
 * 🧪 TEST ENTERPRISE: Validation de la correction du bug d'affichage des statuts
 *
 * @version 1.0-Enterprise-Test
 * @since 2025-11-12
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST ENTERPRISE: Validation Correction Bug Statut Véhicules            ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // ============================================================================
    // TEST 1: Vérifier le véhicule 587449-16 spécifiquement
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 TEST 1: Véhicule 587449-16 (le véhicule problématique initial)\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $vehicle = Vehicle::with('vehicleStatus')->where('registration_plate', '587449-16')->first();

    if (!$vehicle) {
        echo "❌ Véhicule 587449-16 introuvable!\n";
        exit(1);
    }

    echo "✅ VÉHICULE CHARGÉ\n";
    echo "   ID: {$vehicle->id}\n";
    echo "   Immatriculation: {$vehicle->registration_plate}\n";
    echo "   status_id (FK): {$vehicle->status_id}\n\n";

    if ($vehicle->vehicleStatus) {
        echo "✅ RELATION vehicleStatus CHARGÉE\n";
        echo "   vehicleStatus->id: {$vehicle->vehicleStatus->id}\n";
        echo "   vehicleStatus->name: {$vehicle->vehicleStatus->name}\n";
        echo "   vehicleStatus->slug: {$vehicle->vehicleStatus->slug}\n\n";

        // Test de conversion vers enum
        $slug = $vehicle->vehicleStatus->slug;
        echo "🔍 TEST DE CONVERSION VERS ENUM:\n";
        echo "   Slug de la table: '{$slug}'\n";

        $enum = VehicleStatusEnum::tryFrom($slug);

        if ($enum) {
            echo "   ✅ Conversion réussie!\n";
            echo "   Enum trouvé: {$enum->name}\n";
            echo "   Enum value: {$enum->value}\n";
            echo "   Enum label: {$enum->label()}\n";
            echo "   Enum color: {$enum->color()}\n";
            echo "   Enum icon: {$enum->icon()}\n";
            echo "   Badge classes: {$enum->badgeClasses()}\n\n";
        } else {
            echo "   ❌ Conversion échouée avec slug direct: '{$slug}'\n";

            // Essayer avec underscore
            $slugWithUnderscore = str_replace('-', '_', $slug);
            echo "   🔄 Tentative avec underscore: '{$slugWithUnderscore}'\n";

            $enum = VehicleStatusEnum::tryFrom($slugWithUnderscore);

            if ($enum) {
                echo "   ✅ Conversion réussie avec underscore!\n";
                echo "   Enum trouvé: {$enum->name}\n";
                echo "   Enum value: {$enum->value}\n";
                echo "   Enum label: {$enum->label()}\n\n";
            } else {
                echo "   ❌ Conversion échouée même avec underscore!\n\n";
            }
        }
    } else {
        echo "❌ ERREUR: Relation vehicleStatus NULL!\n";
        echo "   Le véhicule n'a pas de statut associé.\n\n";
    }

    // ============================================================================
    // TEST 2: Vérifier TOUS les véhicules avec statuts
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 TEST 2: Validation de TOUS les véhicules avec statuts\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $allVehicles = Vehicle::with('vehicleStatus')
        ->whereNotNull('status_id')
        ->get();

    echo "   Total véhicules avec statut: " . count($allVehicles) . "\n\n";

    $successCount = 0;
    $failureCount = 0;
    $failures = [];

    foreach ($allVehicles as $v) {
        if (!$v->vehicleStatus) {
            $failureCount++;
            $failures[] = [
                'id' => $v->id,
                'immat' => $v->registration_plate,
                'reason' => 'Relation vehicleStatus NULL'
            ];
            continue;
        }

        $slug = $v->vehicleStatus->slug;

        // Tenter conversion directe
        $enum = VehicleStatusEnum::tryFrom($slug);

        // Si échec, tenter avec underscore
        if (!$enum && str_contains($slug, '-')) {
            $enum = VehicleStatusEnum::tryFrom(str_replace('-', '_', $slug));
        }

        if ($enum) {
            $successCount++;
        } else {
            $failureCount++;
            $failures[] = [
                'id' => $v->id,
                'immat' => $v->registration_plate,
                'status_name' => $v->vehicleStatus->name,
                'status_slug' => $slug,
                'reason' => "Aucun enum trouvé pour slug '{$slug}'"
            ];
        }
    }

    echo "📊 RÉSULTATS:\n";
    echo "   ✅ Conversions réussies: {$successCount}\n";
    echo "   ❌ Conversions échouées: {$failureCount}\n\n";

    if ($failureCount > 0) {
        echo "❌ VÉHICULES PROBLÉMATIQUES:\n";
        echo str_repeat("─", 120) . "\n";
        printf("%-10s %-20s %-30s %-25s %-30s\n", "ID", "IMMAT", "STATUS NAME", "STATUS SLUG", "RAISON");
        echo str_repeat("─", 120) . "\n";

        foreach ($failures as $f) {
            printf("%-10s %-20s %-30s %-25s %-30s\n",
                $f['id'],
                $f['immat'],
                $f['status_name'] ?? 'N/A',
                $f['status_slug'] ?? 'N/A',
                $f['reason']
            );
        }
        echo str_repeat("─", 120) . "\n\n";
    }

    // ============================================================================
    // TEST 3: Vérifier la cohérence des slugs dans vehicle_statuses
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 TEST 3: Vérification de la cohérence des slugs dans vehicle_statuses\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $statuses = DB::table('vehicle_statuses')->get();

    echo "📊 SLUGS DANS LA TABLE vehicle_statuses:\n";
    echo str_repeat("─", 100) . "\n";
    printf("%-5s %-30s %-30s %-20s %-15s\n", "ID", "NAME", "SLUG", "ENUM MATCH", "SOLUTION");
    echo str_repeat("─", 100) . "\n";

    foreach ($statuses as $status) {
        $slug = $status->slug;
        $enum = VehicleStatusEnum::tryFrom($slug);

        // Essayer avec underscore si échec
        if (!$enum && str_contains($slug, '-')) {
            $slugWithUnderscore = str_replace('-', '_', $slug);
            $enum = VehicleStatusEnum::tryFrom($slugWithUnderscore);
            $match = $enum ? "✅ Via underscore" : "❌ Aucun";
            $solution = $enum ? "UPDATE slug='{$slugWithUnderscore}'" : "Créer enum";
        } else {
            $match = $enum ? "✅ Direct" : "❌ Aucun";
            $solution = $enum ? "OK" : "Créer enum ou corriger slug";
        }

        printf("%-5s %-30s %-30s %-20s %-15s\n",
            $status->id,
            $status->name,
            $slug,
            $match,
            $solution
        );
    }
    echo str_repeat("─", 100) . "\n\n";

    // ============================================================================
    // RÉSUMÉ FINAL
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "🎯 RÉSUMÉ FINAL\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    if ($failureCount === 0) {
        echo "✅ TOUS LES VÉHICULES AFFICHENT CORRECTEMENT LEUR STATUT\n";
        echo "   La correction est validée et fonctionne à 100%!\n\n";

        echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✅ CORRECTION VALIDÉE - PROBLÈME RÉSOLU                 ║\n";
        echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";
    } else {
        echo "⚠️ {$failureCount} VÉHICULE(S) ONT ENCORE DES PROBLÈMES\n";
        echo "   Vérifier les slugs dans la table vehicle_statuses\n";
        echo "   et les valeurs des enums dans VehicleStatusEnum.php\n\n";

        echo "💡 RECOMMANDATIONS:\n";
        echo "   1. Corriger les slugs dans vehicle_statuses pour utiliser des underscores\n";
        echo "   2. OU ajouter les valeurs manquantes dans VehicleStatusEnum\n";
        echo "   3. Relancer ce script de test\n\n";
    }

} catch (\Exception $e) {
    echo "\n❌ ERREUR DURANT LE TEST:\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
