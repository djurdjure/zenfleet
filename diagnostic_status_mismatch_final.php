<?php

/**
 * 🔍 DIAGNOSTIC FINAL: Identification précise du problème de statut
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DIAGNOSTIC FINAL: Problème d'affichage des statuts véhicules           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Statistiques globales
$totalVehicles = DB::table('vehicles')->count();
$vehiclesWithStatusId = DB::table('vehicles')->whereNotNull('status_id')->count();
$vehiclesWithStatusColumn = DB::table('vehicles')->whereNotNull('status')->count();
$vehiclesWithBoth = DB::table('vehicles')->whereNotNull('status_id')->whereNotNull('status')->count();
$vehiclesWithNeither = DB::table('vehicles')->whereNull('status_id')->whereNull('status')->count();
$vehiclesWithOnlyStatusColumn = DB::table('vehicles')->whereNull('status_id')->whereNotNull('status')->count();

echo "📊 STATISTIQUES GLOBALES:\n";
echo str_repeat("─", 80) . "\n";
echo "   Total véhicules:                     {$totalVehicles}\n";
echo "   Avec status_id (FK):                 {$vehiclesWithStatusId}\n";
echo "   Avec status (colonne directe):       {$vehiclesWithStatusColumn}\n";
echo "   Avec les deux:                       {$vehiclesWithBoth}\n";
echo "   Sans aucun statut:                   {$vehiclesWithNeither}\n";
echo "   ⚠️  Avec UNIQUEMENT status (VARCHAR): {$vehiclesWithOnlyStatusColumn}\n";
echo str_repeat("─", 80) . "\n\n";

echo "🎯 CAUSE RACINE IDENTIFIÉE:\n";
echo "   Le modèle Vehicle.php utilise la relation:\n";
echo "   public function vehicleStatus() { return \$this->belongsTo(VehicleStatus::class, 'status_id'); }\n\n";
echo "   MAIS {$vehiclesWithOnlyStatusColumn} véhicules ont:\n";
echo "   - status_id = NULL\n";
echo "   - status = 'valeur' (colonne VARCHAR)\n\n";
echo "   Résultat: \$vehicle->vehicleStatus retourne NULL\n";
echo "   → Le badge affiche 'Non défini'\n\n";

// Lister les véhicules problématiques
echo "📋 VÉHICULES PROBLÉMATIQUES (status_id NULL mais status rempli):\n";
echo str_repeat("─", 120) . "\n";
printf("%-5s %-15s %-25s %-15s %-30s %-15s\n", "ID", "IMMAT", "MARQUE/MODÈLE", "status (VARCHAR)", "CORRESPONDANCE table", "SOLUTION");
echo str_repeat("─", 120) . "\n";

$problematicVehicles = DB::table('vehicles')
    ->whereNull('status_id')
    ->whereNotNull('status')
    ->get();

foreach ($problematicVehicles as $v) {
    // Chercher le statut correspondant dans vehicle_statuses
    $matchingStatus = DB::table('vehicle_statuses')
        ->where('slug', $v->status)
        ->orWhere('slug', str_replace('_', '', $v->status))
        ->orWhere('slug', str_replace('_', '-', $v->status))
        ->first();

    $correspondence = $matchingStatus ? "{$matchingStatus->name} (ID:{$matchingStatus->id})" : "❌ AUCUNE";
    $solution = $matchingStatus ? "SET status_id={$matchingStatus->id}" : "Créer statut";

    printf("%-5s %-15s %-25s %-15s %-30s %-15s\n",
        $v->id,
        $v->registration_plate,
        substr("{$v->brand} {$v->model}", 0, 25),
        $v->status,
        $correspondence,
        $solution
    );
}
echo str_repeat("─", 120) . "\n\n";

echo "💡 SOLUTION ENTERPRISE-GRADE:\n";
echo "   1. Créer une migration pour synchroniser status → status_id\n";
echo "   2. Mapper les valeurs VARCHAR vers les IDs de vehicle_statuses\n";
echo "   3. Ajouter une contrainte FK sur status_id\n";
echo "   4. Déprécier la colonne 'status' (VARCHAR)\n";
echo "   5. Utiliser UNIQUEMENT status_id + relation Eloquent\n\n";

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ DIAGNOSTIC TERMINÉ - SOLUTION CLAIRE                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";
