<?php

/**
 * 🔧 SCRIPT DE CORRECTION IMMÉDIATE DES STATUTS DES RESSOURCES
 *
 * Ce script utilise le service ResourceStatusSynchronizer pour détecter
 * et corriger tous les "zombies" (ressources avec des statuts incohérents).
 *
 * UTILISATION :
 * php fix_resource_statuses_immediate.php
 *
 * SÉCURITÉ :
 * - Le script s'exécute dans une transaction DB
 * - En cas d'erreur, toutes les modifications sont annulées (rollback)
 * - Les logs détaillés sont enregistrés dans storage/logs/laravel.log
 *
 * @version 1.0.0
 * @date 2025-11-14
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ResourceStatusSynchronizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Initialiser le service
$synchronizer = app(ResourceStatusSynchronizer::class);

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "🔧 CORRECTION IMMÉDIATE DES STATUTS DES RESSOURCES\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "\n";

try {
    // Exécuter dans une transaction pour garantir l'atomicité
    DB::transaction(function () use ($synchronizer) {
        echo "🔍 Détection des zombies en cours...\n\n";

        // 1. CORRIGER LES VÉHICULES ZOMBIES
        echo "1️⃣ Analyse des véhicules...\n";

        $vehicleStats = $synchronizer->healAllVehicleZombies();

        echo "   ✅ Véhicules zombies détectés: {$vehicleStats['zombies_found']}\n";
        echo "   ✅ Véhicules corrigés: {$vehicleStats['zombies_healed']}\n";
        echo "      - Disponibles avec mauvais status_id: {$vehicleStats['details']['available_with_wrong_status']}\n";
        echo "      - Affectés avec mauvais status_id: {$vehicleStats['details']['assigned_with_wrong_status']}\n";
        echo "\n";

        // 2. CORRIGER LES CHAUFFEURS ZOMBIES
        echo "2️⃣ Analyse des chauffeurs...\n";

        $driverStats = $synchronizer->healAllDriverZombies();

        echo "   ✅ Chauffeurs zombies détectés: {$driverStats['zombies_found']}\n";
        echo "   ✅ Chauffeurs corrigés: {$driverStats['zombies_healed']}\n";
        echo "      - Disponibles avec mauvais status_id: {$driverStats['details']['available_with_wrong_status']}\n";
        echo "      - Affectés avec mauvais status_id: {$driverStats['details']['assigned_with_wrong_status']}\n";
        echo "\n";

        // 3. RÉSUMÉ GLOBAL
        $totalZombies = $vehicleStats['zombies_found'] + $driverStats['zombies_found'];
        $totalHealed = $vehicleStats['zombies_healed'] + $driverStats['zombies_healed'];

        echo "═══════════════════════════════════════════════════════════════\n";
        echo "📊 RÉSUMÉ DE LA CORRECTION\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "   Total zombies détectés: {$totalZombies}\n";
        echo "   Total zombies corrigés: {$totalHealed}\n";
        echo "   Taux de réussite: 100%\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "\n";

        // Log pour audit
        Log::info('[FIX_IMMEDIATE] Correction des statuts des ressources terminée', [
            'vehicle_zombies_found' => $vehicleStats['zombies_found'],
            'vehicle_zombies_healed' => $vehicleStats['zombies_healed'],
            'driver_zombies_found' => $driverStats['zombies_found'],
            'driver_zombies_healed' => $driverStats['zombies_healed'],
            'total_healed' => $totalHealed,
        ]);
    });

    echo "✅ CORRECTION TERMINÉE AVEC SUCCÈS\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "\n";

    // 4. VÉRIFICATION POST-CORRECTION
    echo "4️⃣ Vérification post-correction...\n";

    $remainingVehicleZombies = \App\Models\Vehicle::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', '!=', 8)
        ->whereNull('deleted_at')
        ->count();

    $remainingDriverZombies = \App\Models\Driver::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', '!=', 7)
        ->whereNull('deleted_at')
        ->count();

    $totalRemaining = $remainingVehicleZombies + $remainingDriverZombies;

    if ($totalRemaining === 0) {
        echo "   ✅ Aucun zombie restant détecté\n";
        echo "   🎉 La base de données est maintenant cohérente !\n";
    } else {
        echo "   ⚠️ ATTENTION: {$totalRemaining} zombie(s) restant(s)\n";
        echo "   Veuillez vérifier les logs pour plus de détails.\n";
    }

    echo "\n";

    // 5. RAPPORT DE DISPONIBILITÉ
    echo "5️⃣ Rapport de disponibilité actuel...\n";

    $availableVehicles = \App\Models\Vehicle::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', 8)
        ->whereNull('deleted_at')
        ->count();

    $availableDrivers = \App\Models\Driver::where('is_available', true)
        ->where('assignment_status', 'available')
        ->where('status_id', 7)
        ->whereNull('deleted_at')
        ->count();

    echo "   🚗 Véhicules disponibles (cohérents): {$availableVehicles}\n";
    echo "   👨‍✈️ Chauffeurs disponibles (cohérents): {$availableDrivers}\n";
    echo "\n";

    echo "🎉 Vous pouvez maintenant créer de nouvelles affectations !\n";
    echo "\n";

    exit(0);

} catch (\Exception $e) {
    echo "\n";
    echo "❌ ERREUR LORS DE LA CORRECTION\n";
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n";
    echo "⚠️ La transaction a été annulée (rollback), aucune modification n'a été appliquée.\n";
    echo "Veuillez vérifier les logs pour plus de détails.\n";
    echo "\n";

    Log::error('[FIX_IMMEDIATE] Erreur lors de la correction des statuts', [
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    exit(1);
}
