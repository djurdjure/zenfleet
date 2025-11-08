<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ============================================================================
 * 🚀 MIGRATION: Mise à jour Statuts Chauffeurs - Architecture Enterprise
 * ============================================================================
 *
 * OBJECTIF: Synchroniser les statuts chauffeurs avec DriverStatusEnum
 *
 * Statuts conformes à l'Enum:
 * - DISPONIBLE (disponible) - Chauffeur prêt pour affectation
 * - EN_MISSION (en_mission) - Chauffeur actuellement en mission
 * - EN_CONGE (en_conge) - Chauffeur en congé
 * - AUTRE (autre) - Statut libre personnalisé
 *
 * MÉTHODE: Upsert pour éviter duplicatas
 *
 * @version 1.0-Enterprise
 * @date 2025-11-08
 */
return new class extends Migration
{
    /**
     * Exécuter la migration
     */
    public function up(): void
    {
        $now = Carbon::now();

        // Liste des statuts conformes à DriverStatusEnum
        $newStatuses = [
            [
                'name' => 'Disponible',
                'slug' => 'disponible',
                'description' => 'Chauffeur disponible pour nouvelle affectation',
                'color' => '#10b981', // Vert
                'text_color' => '#065f46',
                'icon' => 'user-check',
                'is_active' => true,
                'is_default' => true,
                'allows_assignments' => true,
                'is_available_for_work' => true,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
                'sort_order' => 1,
                'priority_level' => 1,
            ],
            [
                'name' => 'En mission',
                'slug' => 'en_mission',
                'description' => 'Chauffeur actuellement affecté à un véhicule',
                'color' => '#3b82f6', // Bleu
                'text_color' => '#1e40af',
                'icon' => 'car',
                'is_active' => true,
                'is_default' => false,
                'allows_assignments' => false,
                'is_available_for_work' => false,
                'can_drive' => true,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 2,
                'priority_level' => 2,
            ],
            [
                'name' => 'En congé',
                'slug' => 'en_conge',
                'description' => 'Chauffeur en congé (payé ou non payé)',
                'color' => '#f59e0b', // Orange
                'text_color' => '#92400e',
                'icon' => 'umbrella-beach',
                'is_active' => true,
                'is_default' => false,
                'allows_assignments' => false,
                'is_available_for_work' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 3,
                'priority_level' => 3,
            ],
            [
                'name' => 'Autre',
                'slug' => 'autre',
                'description' => 'Statut personnalisé ou temporaire',
                'color' => '#6b7280', // Gris
                'text_color' => '#374151',
                'icon' => 'question-circle',
                'is_active' => true,
                'is_default' => false,
                'allows_assignments' => false,
                'is_available_for_work' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 10,
                'priority_level' => 5,
            ],
        ];

        foreach ($newStatuses as $statusData) {
            // Vérifier si le statut existe déjà (par slug pour éviter duplicatas)
            $existingStatus = DB::table('driver_statuses')
                ->where('slug', $statusData['slug'])
                ->first();

            if (!$existingStatus) {
                // Créer nouveau statut
                DB::table('driver_statuses')->insert(array_merge($statusData, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));

                echo "✅ Statut chauffeur créé: {$statusData['name']} ({$statusData['slug']})\n";
            } else {
                // Mettre à jour le statut existant
                DB::table('driver_statuses')
                    ->where('id', $existingStatus->id)
                    ->update([
                        'name' => $statusData['name'],
                        'description' => $statusData['description'],
                        'color' => $statusData['color'],
                        'text_color' => $statusData['text_color'],
                        'icon' => $statusData['icon'],
                        'is_active' => $statusData['is_active'],
                        'is_default' => $statusData['is_default'],
                        'allows_assignments' => $statusData['allows_assignments'],
                        'is_available_for_work' => $statusData['is_available_for_work'],
                        'can_drive' => $statusData['can_drive'],
                        'can_assign' => $statusData['can_assign'],
                        'requires_validation' => $statusData['requires_validation'],
                        'sort_order' => $statusData['sort_order'],
                        'priority_level' => $statusData['priority_level'],
                        'updated_at' => $now,
                    ]);

                echo "🔄 Statut chauffeur mis à jour: {$statusData['name']} ({$statusData['slug']})\n";
            }
        }

        // Désactiver les anciens statuts qui ne sont plus utilisés (optionnel, prudent)
        $keepSlugs = collect($newStatuses)->pluck('slug')->toArray();

        // On ne supprime rien, juste marquage des anciens comme "legacy"
        DB::table('driver_statuses')
            ->whereNotIn('slug', $keepSlugs)
            ->update([
                'description' => DB::raw("CONCAT('[LEGACY] ', COALESCE(description, ''))"),
                'updated_at' => $now,
            ]);

        echo "✅ Migration des statuts chauffeurs terminée avec succès!\n";
    }

    /**
     * Annuler la migration
     */
    public function down(): void
    {
        // Ne pas supprimer les statuts pour préserver l'intégrité des données
        // Les statuts legacy restent en base avec le préfixe [LEGACY]

        $slugs = ['disponible', 'en_mission', 'en_conge', 'autre'];

        DB::table('driver_statuses')
            ->whereIn('slug', $slugs)
            ->update([
                'description' => DB::raw("REPLACE(description, '[LEGACY] ', '')"),
                'updated_at' => Carbon::now(),
            ]);

        echo "⚠️ Rollback migration statuts chauffeurs - Statuts marqués comme legacy\n";
    }
};
