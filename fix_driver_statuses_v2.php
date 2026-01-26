#!/usr/bin/env php
<?php

/**
 * 🔧 SCRIPT DE CORRECTION - Statuts Chauffeurs v2.0 Enterprise
 *
 * Approche directe sans dépendance au Seeder Command
 * - Crée/Met à jour les statuts directement
 * - Validation complète avec rapport détaillé
 * - Gestion d'erreurs robuste
 * - Compatible Docker et CLI standard
 *
 * @version 2.0-Enterprise
 * @author ZenFleet DevOps Team
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// ============================================================
// CONFIGURATION
// ============================================================

use App\Models\DriverStatus;
use Illuminate\Database\Eloquent\Model;

Model::unguard(); // Autoriser l'assignation de masse

// ============================================================
// AFFICHAGE HEADER
// ============================================================

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  🔧 CORRECTION STATUTS CHAUFFEURS - ENTERPRISE v2.0        ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ============================================================
// DÉFINITION DES STATUTS
// ============================================================

$globalStatuses = [
    [
        'name' => 'Actif',
        'slug' => 'actif',
        'description' => 'Chauffeur actif et disponible pour les affectations',
        'color' => '#10B981', // Green
        'icon' => 'fa-check-circle',
        'is_active' => true,
        'can_drive' => true,
        'can_assign' => true,
        'requires_validation' => false,
        'sort_order' => 1,
        'organization_id' => null,
    ],
    [
        'name' => 'En Mission',
        'slug' => 'en-mission',
        'description' => 'Chauffeur actuellement affecté à un véhicule',
        'color' => '#3B82F6', // Blue
        'icon' => 'fa-car',
        'is_active' => true,
        'can_drive' => true,
        'can_assign' => false,
        'requires_validation' => false,
        'sort_order' => 2,
        'organization_id' => null,
    ],
    [
        'name' => 'En Congé',
        'slug' => 'en-conge',
        'description' => 'Chauffeur temporairement indisponible (congés annuels, maladie)',
        'color' => '#F59E0B', // Amber
        'icon' => 'fa-calendar-times',
        'is_active' => true,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => false,
        'sort_order' => 3,
        'organization_id' => null,
    ],
    [
        'name' => 'Suspendu',
        'slug' => 'suspendu',
        'description' => 'Chauffeur suspendu temporairement (sanctions, enquêtes)',
        'color' => '#EF4444', // Red
        'icon' => 'fa-ban',
        'is_active' => true,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => true,
        'sort_order' => 4,
        'organization_id' => null,
    ],
    [
        'name' => 'Formation',
        'slug' => 'formation',
        'description' => 'Chauffeur en période de formation ou d\'intégration',
        'color' => '#8B5CF6', // Purple
        'icon' => 'fa-graduation-cap',
        'is_active' => true,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => true,
        'sort_order' => 5,
        'organization_id' => null,
    ],
    [
        'name' => 'Retraité',
        'slug' => 'retraite',
        'description' => 'Chauffeur à la retraite (archivé)',
        'color' => '#6B7280', // Gray
        'icon' => 'fa-user-clock',
        'is_active' => false,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => false,
        'sort_order' => 6,
        'organization_id' => null,
    ],
    [
        'name' => 'Démission',
        'slug' => 'demission',
        'description' => 'Chauffeur ayant démissionné (archivé)',
        'color' => '#6B7280', // Gray
        'icon' => 'fa-user-minus',
        'is_active' => false,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => false,
        'sort_order' => 7,
        'organization_id' => null,
    ],
    [
        'name' => 'Licencié',
        'slug' => 'licencie',
        'description' => 'Chauffeur licencié (archivé)',
        'color' => '#991B1B', // Dark Red
        'icon' => 'fa-user-times',
        'is_active' => false,
        'can_drive' => false,
        'can_assign' => false,
        'requires_validation' => false,
        'sort_order' => 8,
        'organization_id' => null,
    ],
];

// ============================================================
// EXÉCUTION
// ============================================================

try {
    echo "📥 Création/Mise à jour des statuts chauffeurs...\n\n";

    $created = 0;
    $updated = 0;
    $errors = 0;

    foreach ($globalStatuses as $index => $statusData) {
        try {
            $status = DriverStatus::updateOrCreate(
                ['slug' => $statusData['slug'], 'organization_id' => null],
                $statusData
            );

            if ($status->wasRecentlyCreated) {
                $created++;
                echo sprintf(
                    "   ✅ [%d/%d] Créé: %-20s (couleur: %s, icône: %s)\n",
                    $index + 1,
                    count($globalStatuses),
                    $status->name,
                    $status->color,
                    $status->icon
                );
            } else {
                $updated++;
                echo sprintf(
                    "   🔄 [%d/%d] Mis à jour: %-20s (couleur: %s, icône: %s)\n",
                    $index + 1,
                    count($globalStatuses),
                    $status->name,
                    $status->color,
                    $status->icon
                );
            }
        } catch (\Exception $e) {
            $errors++;
            $currentIndex = $index + 1;
            $totalStatuses = count($globalStatuses);
            echo "   ❌ [{$currentIndex}/{$totalStatuses}] Erreur: {$statusData['name']} - {$e->getMessage()}\n";
        }
    }

    echo "\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo "📊 RÉSUMÉ DE L'OPÉRATION\n";
    echo "─────────────────────────────────────────────────────────────\n";
    echo sprintf("   ✅ Créés:      %d statut(s)\n", $created);
    echo sprintf("   🔄 Mis à jour: %d statut(s)\n", $updated);
    echo sprintf("   ❌ Erreurs:    %d\n", $errors);
    echo sprintf("   📦 Total:      %d statut(s)\n", $created + $updated);
    echo "\n";

    // ============================================================
    // VÉRIFICATION ET RAPPORT DÉTAILLÉ
    // ============================================================

    echo "─────────────────────────────────────────────────────────────\n";
    echo "🔍 VÉRIFICATION DES STATUTS EN BASE DE DONNÉES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    $allStatuses = DriverStatus::orderBy('sort_order')->get();

    echo "   📈 Total en base: " . $allStatuses->count() . " statut(s)\n\n";

    foreach ($allStatuses as $status) {
        $activeIcon = $status->is_active ? '✓' : '✗';
        $driveIcon = $status->can_drive ? '🚗' : '🚫';
        $assignIcon = $status->can_assign ? '✓' : '✗';

        echo sprintf(
            "   [%d] %-20s │ Actif: %s │ Conduite: %s │ Mission: %s\n",
            $status->sort_order,
            $status->name,
            $activeIcon,
            $driveIcon,
            $assignIcon
        );

        echo sprintf(
            "       └─ %s │ %s │ %s\n",
            $status->color,
            $status->icon,
            $status->description
        );

        echo "\n";
    }

    // ============================================================
    // STATISTIQUES AVANCÉES
    // ============================================================

    $activeCount = $allStatuses->where('is_active', true)->count();
    $canDriveCount = $allStatuses->where('can_drive', true)->count();
    $canAssignCount = $allStatuses->where('can_assign', true)->count();

    echo "─────────────────────────────────────────────────────────────\n";
    echo "📊 STATISTIQUES DÉTAILLÉES\n";
    echo "─────────────────────────────────────────────────────────────\n\n";

    echo sprintf("   🟢 Statuts actifs:           %d / %d (%.1f%%)\n", $activeCount, $allStatuses->count(), ($activeCount / $allStatuses->count()) * 100);
    echo sprintf("   🚗 Autorisés à conduire:     %d / %d (%.1f%%)\n", $canDriveCount, $allStatuses->count(), ($canDriveCount / $allStatuses->count()) * 100);
    echo sprintf("   ✅ Assignables aux missions: %d / %d (%.1f%%)\n", $canAssignCount, $allStatuses->count(), ($canAssignCount / $allStatuses->count()) * 100);

    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ CORRECTION TERMINÉE AVEC SUCCÈS!                        ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "💡 Les statuts sont maintenant disponibles dans:\n";
    echo "   → Formulaire d'ajout de chauffeurs\n";
    echo "   → Modification des chauffeurs existants\n";
    echo "   → Rapports et tableaux de bord\n";
    echo "\n";
    echo "🔄 N'oubliez pas de vider le cache:\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan view:clear\n";
    echo "\n";

    exit(0);

} catch (\Exception $e) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERREUR CRITIQUE                                         ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "❌ Message: {$e->getMessage()}\n";
    echo "📁 Fichier:  {$e->getFile()}:{$e->getLine()}\n";
    echo "\n";
    echo "🔍 Stack trace:\n";
    echo $e->getTraceAsString();
    echo "\n\n";

    exit(1);
}
