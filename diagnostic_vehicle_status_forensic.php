<?php

/**
 * 🔍 DIAGNOSTIC FORENSIC ENTERPRISE-GRADE: Incohérence Affichage Statut Véhicules
 *
 * Analyse complète multi-niveaux:
 * 1. Vérification des données brutes en base PostgreSQL
 * 2. Analyse du schéma et des relations
 * 3. Traçage du processus d'extraction
 * 4. Validation de l'affichage
 *
 * @version 1.0-Enterprise-Forensic
 * @since 2025-11-12
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\VehicleStatus;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 DIAGNOSTIC FORENSIC: Incohérence Statut Véhicules - Analyse Complète   ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

try {
    // ============================================================================
    // ÉTAPE 1: VÉRIFICATION DU VÉHICULE PROBLÉMATIQUE (587449-16)
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 1: ANALYSE DU VÉHICULE 587449-16\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    // Requête SQL directe pour voir les données brutes
    $vehicleRaw = DB::table('vehicles')
        ->where('registration_plate', '587449-16')
        ->first();

    if (!$vehicleRaw) {
        echo "❌ ERREUR CRITIQUE: Véhicule 587449-16 introuvable en base de données!\n";
        exit(1);
    }

    echo "✅ VÉHICULE TROUVÉ EN BASE DE DONNÉES\n";
    echo "   ID: {$vehicleRaw->id}\n";
    echo "   Immatriculation: {$vehicleRaw->registration_plate}\n";
    echo "   Marque/Modèle: {$vehicleRaw->brand} {$vehicleRaw->model}\n";
    echo "   vehicle_status_id (FK): " . ($vehicleRaw->vehicle_status_id ?? 'NULL') . "\n";
    echo "   status (colonne directe): " . ($vehicleRaw->status ?? 'N/A') . "\n";
    echo "   created_at: {$vehicleRaw->created_at}\n";
    echo "   updated_at: {$vehicleRaw->updated_at}\n\n";

    // Vérifier si la colonne 'status' existe
    $vehicleColumns = DB::select("
        SELECT column_name, data_type, is_nullable, column_default
        FROM information_schema.columns
        WHERE table_name = 'vehicles'
        ORDER BY ordinal_position
    ");

    echo "📊 SCHÉMA COMPLET DE LA TABLE 'vehicles':\n";
    echo str_repeat("─", 80) . "\n";
    printf("%-30s %-20s %-10s %-20s\n", "COLONNE", "TYPE", "NULLABLE", "DEFAULT");
    echo str_repeat("─", 80) . "\n";

    $hasStatusColumn = false;
    $hasVehicleStatusIdColumn = false;

    foreach ($vehicleColumns as $col) {
        printf("%-30s %-20s %-10s %-20s\n",
            $col->column_name,
            $col->data_type,
            $col->is_nullable,
            $col->column_default ?? 'NULL'
        );

        if ($col->column_name === 'status') {
            $hasStatusColumn = true;
        }
        if ($col->column_name === 'vehicle_status_id') {
            $hasVehicleStatusIdColumn = true;
        }
    }
    echo str_repeat("─", 80) . "\n\n";

    echo "🔍 COLONNES DE STATUT DÉTECTÉES:\n";
    echo "   - Colonne 'status': " . ($hasStatusColumn ? "✅ Existe" : "❌ N'existe pas") . "\n";
    echo "   - Colonne 'vehicle_status_id': " . ($hasVehicleStatusIdColumn ? "✅ Existe (FK vers vehicle_statuses)" : "❌ N'existe pas") . "\n\n";

    // ============================================================================
    // ÉTAPE 2: VÉRIFICATION DE LA TABLE vehicle_statuses
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 2: ANALYSE DE LA TABLE 'vehicle_statuses'\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    // Vérifier si la table existe
    $tableExists = DB::select("
        SELECT EXISTS (
            SELECT FROM information_schema.tables
            WHERE table_name = 'vehicle_statuses'
        ) as exists
    ");

    if (!$tableExists[0]->exists) {
        echo "❌ ERREUR CRITIQUE: Table 'vehicle_statuses' introuvable!\n";
        exit(1);
    }

    // Récupérer tous les statuts disponibles
    $allStatuses = DB::table('vehicle_statuses')->get();

    echo "✅ TABLE 'vehicle_statuses' TROUVÉE\n";
    echo "   Nombre total de statuts: " . count($allStatuses) . "\n\n";

    echo "📊 LISTE COMPLÈTE DES STATUTS DISPONIBLES:\n";
    echo str_repeat("─", 100) . "\n";
    printf("%-5s %-30s %-30s %-15s %-20s\n", "ID", "NAME", "SLUG", "COLOR", "CREATED_AT");
    echo str_repeat("─", 100) . "\n";

    foreach ($allStatuses as $status) {
        printf("%-5s %-30s %-30s %-15s %-20s\n",
            $status->id,
            $status->name ?? 'N/A',
            $status->slug ?? 'N/A',
            $status->color ?? 'N/A',
            $status->created_at ?? 'N/A'
        );
    }
    echo str_repeat("─", 100) . "\n\n";

    // ============================================================================
    // ÉTAPE 3: VÉRIFICATION DU STATUT DU VÉHICULE 587449-16
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 3: RELATION VEHICLE ↔ VEHICLE_STATUS POUR 587449-16\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    if ($vehicleRaw->vehicle_status_id) {
        $statusRaw = DB::table('vehicle_statuses')
            ->where('id', $vehicleRaw->vehicle_status_id)
            ->first();

        if ($statusRaw) {
            echo "✅ STATUT TROUVÉ VIA RELATION (vehicle_status_id = {$vehicleRaw->vehicle_status_id})\n";
            echo "   ID: {$statusRaw->id}\n";
            echo "   NAME: {$statusRaw->name}\n";
            echo "   SLUG: {$statusRaw->slug}\n";
            echo "   COLOR: {$statusRaw->color}\n";
            echo "   DESCRIPTION: " . ($statusRaw->description ?? 'N/A') . "\n\n";

            echo "🎯 VERDICT ÉTAPE 3:\n";
            echo "   Le véhicule 587449-16 a bien un statut en base de données: '{$statusRaw->name}'\n";
            echo "   ⚠️ MAIS affiche 'Non défini' dans l'interface!\n\n";
        } else {
            echo "❌ INCOHÉRENCE DÉTECTÉE:\n";
            echo "   vehicle_status_id = {$vehicleRaw->vehicle_status_id}\n";
            echo "   MAIS aucun statut correspondant dans 'vehicle_statuses'!\n";
            echo "   → Clé étrangère orpheline (référence un ID qui n'existe plus)\n\n";
        }
    } else {
        echo "⚠️ PROBLÈME DÉTECTÉ:\n";
        echo "   vehicle_status_id est NULL pour le véhicule 587449-16\n";
        echo "   → Le véhicule n'a aucun statut assigné en base de données\n\n";
    }

    // ============================================================================
    // ÉTAPE 4: ANALYSE VIA ELOQUENT (MODÈLE LARAVEL)
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 4: CHARGEMENT VIA ELOQUENT (Modèle Vehicle)\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $vehicle = Vehicle::where('registration_plate', '587449-16')->first();

    if (!$vehicle) {
        echo "❌ Véhicule introuvable via Eloquent!\n";
        exit(1);
    }

    echo "✅ VÉHICULE CHARGÉ VIA ELOQUENT\n";
    echo "   ID: {$vehicle->id}\n";
    echo "   Immatriculation: {$vehicle->registration_plate}\n";
    echo "   vehicle_status_id: " . ($vehicle->vehicle_status_id ?? 'NULL') . "\n";
    echo "   Relation 'vehicleStatus' chargée: " . ($vehicle->relationLoaded('vehicleStatus') ? 'OUI' : 'NON') . "\n\n";

    // Charger explicitement la relation
    $vehicle->load('vehicleStatus');

    echo "🔄 APRÈS load('vehicleStatus'):\n";
    echo "   Relation 'vehicleStatus' chargée: " . ($vehicle->relationLoaded('vehicleStatus') ? 'OUI' : 'NON') . "\n";

    if ($vehicle->vehicleStatus) {
        echo "   vehicleStatus->id: {$vehicle->vehicleStatus->id}\n";
        echo "   vehicleStatus->name: {$vehicle->vehicleStatus->name}\n";
        echo "   vehicleStatus->slug: {$vehicle->vehicleStatus->slug}\n\n";
    } else {
        echo "   vehicleStatus: NULL\n";
        echo "   ⚠️ La relation ne retourne aucun statut!\n\n";
    }

    // ============================================================================
    // ÉTAPE 5: ANALYSE DE TOUS LES VÉHICULES AVEC STATUT "Non défini"
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 5: ANALYSE GLOBALE - TOUS LES VÉHICULES SANS STATUT\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    // Véhicules avec vehicle_status_id NULL
    $vehiclesWithoutStatus = DB::table('vehicles')
        ->whereNull('vehicle_status_id')
        ->get();

    echo "📊 VÉHICULES AVEC vehicle_status_id = NULL:\n";
    echo "   Nombre: " . count($vehiclesWithoutStatus) . "\n\n";

    if (count($vehiclesWithoutStatus) > 0) {
        echo "   Liste (max 20):\n";
        echo str_repeat("─", 80) . "\n";
        printf("%-10s %-20s %-30s %-20s\n", "ID", "IMMATRICULATION", "MARQUE/MODÈLE", "UPDATED_AT");
        echo str_repeat("─", 80) . "\n";

        foreach (array_slice($vehiclesWithoutStatus->toArray(), 0, 20) as $v) {
            printf("%-10s %-20s %-30s %-20s\n",
                $v->id,
                $v->registration_plate,
                "{$v->brand} {$v->model}",
                $v->updated_at
            );
        }
        echo str_repeat("─", 80) . "\n\n";
    }

    // Véhicules avec vehicle_status_id pointant vers un ID inexistant (orphelins)
    $orphanedVehicles = DB::table('vehicles as v')
        ->leftJoin('vehicle_statuses as vs', 'v.vehicle_status_id', '=', 'vs.id')
        ->whereNotNull('v.vehicle_status_id')
        ->whereNull('vs.id')
        ->select('v.*')
        ->get();

    echo "📊 VÉHICULES AVEC FK ORPHELINE (vehicle_status_id pointe vers un ID inexistant):\n";
    echo "   Nombre: " . count($orphanedVehicles) . "\n\n";

    if (count($orphanedVehicles) > 0) {
        echo "   ⚠️ PROBLÈME CRITIQUE D'INTÉGRITÉ RÉFÉRENTIELLE!\n";
        echo "   Liste:\n";
        echo str_repeat("─", 100) . "\n";
        printf("%-10s %-20s %-30s %-20s %-10s\n", "ID", "IMMATRICULATION", "MARQUE/MODÈLE", "UPDATED_AT", "FK_ID");
        echo str_repeat("─", 100) . "\n";

        foreach ($orphanedVehicles as $v) {
            printf("%-10s %-20s %-30s %-20s %-10s\n",
                $v->id,
                $v->registration_plate,
                "{$v->brand} {$v->model}",
                $v->updated_at,
                $v->vehicle_status_id
            );
        }
        echo str_repeat("─", 100) . "\n\n";
    }

    // ============================================================================
    // ÉTAPE 6: VÉRIFICATION DU MODÈLE VEHICLE
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "📋 ÉTAPE 6: ANALYSE DU MODÈLE Vehicle.php\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $vehicleModel = new Vehicle();
    $reflection = new \ReflectionClass($vehicleModel);

    echo "🔍 MÉTHODES DE RELATION DANS Vehicle.php:\n";
    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

    $relationMethods = [];
    foreach ($methods as $method) {
        if (str_contains($method->getName(), 'vehicleStatus') ||
            str_contains($method->getName(), 'status')) {
            $relationMethods[] = $method->getName();
        }
    }

    if (count($relationMethods) > 0) {
        echo "   Méthodes trouvées:\n";
        foreach ($relationMethods as $method) {
            echo "   - {$method}()\n";
        }
    } else {
        echo "   ⚠️ Aucune méthode de relation pour 'status' ou 'vehicleStatus' trouvée!\n";
    }
    echo "\n";

    // ============================================================================
    // RÉSUMÉ FINAL ET DIAGNOSTIC
    // ============================================================================
    echo "═══════════════════════════════════════════════════════════════════════════════\n";
    echo "🎯 DIAGNOSTIC FINAL - CAUSE RACINE\n";
    echo "═══════════════════════════════════════════════════════════════════════════════\n\n";

    $issues = [];

    if (count($vehiclesWithoutStatus) > 0) {
        $issues[] = "📌 PROBLÈME #1: " . count($vehiclesWithoutStatus) . " véhicules ont vehicle_status_id = NULL";
    }

    if (count($orphanedVehicles) > 0) {
        $issues[] = "📌 PROBLÈME #2: " . count($orphanedVehicles) . " véhicules ont une FK orpheline (pointent vers un statut supprimé)";
    }

    if ($vehicleRaw->vehicle_status_id && !isset($statusRaw)) {
        $issues[] = "📌 PROBLÈME #3: Le véhicule 587449-16 a une FK orpheline";
    }

    if (count($issues) === 0) {
        echo "✅ AUCUN PROBLÈME D'INTÉGRITÉ DÉTECTÉ\n";
        echo "   → Le problème est probablement dans le composant d'affichage (VehicleStatusBadge)\n\n";
    } else {
        echo "❌ PROBLÈMES DÉTECTÉS:\n\n";
        foreach ($issues as $i => $issue) {
            echo ($i + 1) . ". {$issue}\n";
        }
        echo "\n";
    }

    echo "💡 RECOMMANDATIONS:\n";
    echo "   1. Vérifier la méthode vehicleStatus() dans app/Models/Vehicle.php\n";
    echo "   2. Vérifier le composant VehicleStatusBadgeUltraPro.php\n";
    echo "   3. Vérifier la vue de la liste des véhicules (index.blade.php)\n";
    echo "   4. Corriger les FK orphelines\n";
    echo "   5. Assigner un statut par défaut aux véhicules sans statut\n\n";

    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    ✅ DIAGNOSTIC FORENSIC TERMINÉ                           ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR DURANT LE DIAGNOSTIC:\n";
    echo "Message: {$e->getMessage()}\n";
    echo "Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
