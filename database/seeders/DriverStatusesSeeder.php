<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverStatus;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🚛 DRIVER STATUSES SEEDER - Version Enterprise Ultra-Professional
 * 
 * Seeder pour initialiser et maintenir les statuts de chauffeurs avec:
 * - Multi-tenant avec statuts globaux et par organisation
 * - Configuration avancée des permissions
 * - Interface utilisateur riche
 * - Traçabilité complète
 * 
 * @version 3.0-Enterprise
 * @author ZenFleet Professional Team
 */
class DriverStatusesSeeder extends Seeder
{
    /**
     * Statuts de base pour toute organisation de gestion de flotte
     * Conformes aux standards internationaux de transport
     */
    private array $defaultStatuses = [
        [
            'name' => 'Disponible',
            'slug' => 'disponible',
            'description' => 'Chauffeur disponible pour les missions',
            'color' => '#10B981', // Vert emerald-500
            'icon' => 'fa-check-circle',
            'is_active' => true,
            'sort_order' => 1,
            'can_drive' => true,
            'can_assign' => true,
            'requires_validation' => false,
        ],
        [
            'name' => 'En mission',
            'slug' => 'en-mission',
            'description' => 'Chauffeur actuellement en mission',
            'color' => '#3B82F6', // Bleu blue-500
            'icon' => 'fa-truck',
            'is_active' => true,
            'sort_order' => 2,
            'can_drive' => true,
            'can_assign' => false,
            'requires_validation' => false,
        ],
        [
            'name' => 'En pause',
            'slug' => 'en-pause',
            'description' => 'Chauffeur en pause temporaire',
            'color' => '#F59E0B', // Orange amber-500
            'icon' => 'fa-pause-circle',
            'is_active' => true,
            'sort_order' => 3,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => false,
        ],
        [
            'name' => 'En congé',
            'slug' => 'en-conge',
            'description' => 'Chauffeur en congé annuel ou spécial',
            'color' => '#8B5CF6', // Violet violet-500
            'icon' => 'fa-plane',
            'is_active' => true,
            'sort_order' => 4,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => false,
        ],
        [
            'name' => 'Maladie',
            'slug' => 'maladie',
            'description' => 'Chauffeur en arrêt maladie',
            'color' => '#EF4444', // Rouge red-500
            'icon' => 'fa-medkit',
            'is_active' => true,
            'sort_order' => 5,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => true,
        ],
        [
            'name' => 'En formation',
            'slug' => 'en-formation',
            'description' => 'Chauffeur en formation ou perfectionnement',
            'color' => '#06B6D4', // Cyan cyan-500
            'icon' => 'fa-graduation-cap',
            'is_active' => true,
            'sort_order' => 6,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => false,
        ],
        [
            'name' => 'Suspendu',
            'slug' => 'suspendu',
            'description' => 'Chauffeur temporairement suspendu',
            'color' => '#DC2626', // Rouge red-600
            'icon' => 'fa-ban',
            'is_active' => true,
            'sort_order' => 7,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => true,
        ],
        [
            'name' => 'Sanctionné',
            'slug' => 'sanctionne',
            'description' => 'Chauffeur sous sanction disciplinaire',
            'color' => '#991B1B', // Rouge red-800
            'icon' => 'fa-exclamation-triangle',
            'is_active' => true,
            'sort_order' => 8,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => true,
        ],
        [
            'name' => 'Inactif',
            'slug' => 'inactif',
            'description' => 'Chauffeur inactif dans le système',
            'color' => '#6B7280', // Gris gray-500
            'icon' => 'fa-user-slash',
            'is_active' => false,
            'sort_order' => 9,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => false,
        ],
        [
            'name' => 'Retraité',
            'slug' => 'retraite',
            'description' => 'Chauffeur parti à la retraite',
            'color' => '#A78BFA', // Violet purple-400
            'icon' => 'fa-umbrella-beach',
            'is_active' => false,
            'sort_order' => 10,
            'can_drive' => false,
            'can_assign' => false,
            'requires_validation' => false,
        ],
    ];

    /**
     * Run the database seeds with enterprise-grade implementation
     */
    public function run(): void
    {
        Log::info('🚀 Starting DriverStatusesSeeder - Enterprise Version', [
            'timestamp' => now()->toISOString(),
            'environment' => app()->environment(),
        ]);

        DB::beginTransaction();

        try {
            // Étape 1: Créer les statuts globaux (pour toutes les organisations)
            $this->createGlobalStatuses();

            // Étape 2: Créer les statuts pour chaque organisation existante
            $this->createOrganizationStatuses();

            DB::commit();

            Log::info('✅ DriverStatusesSeeder completed successfully', [
                'global_statuses_count' => DriverStatus::whereNull('organization_id')->count(),
                'organization_statuses_count' => DriverStatus::whereNotNull('organization_id')->count(),
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ DriverStatusesSeeder failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString(),
            ]);

            throw $e;
        }
    }

    /**
     * Créer les statuts globaux (disponibles pour toutes les organisations)
     */
    private function createGlobalStatuses(): void
    {
        Log::info('Creating global driver statuses...');

        foreach ($this->defaultStatuses as $statusData) {
            $this->createOrUpdateStatus($statusData, null);
        }
    }

    /**
     * Créer les statuts spécifiques pour chaque organisation
     */
    private function createOrganizationStatuses(): void
    {
        $organizations = Organization::all();

        Log::info('Creating organization-specific driver statuses', [
            'organizations_count' => $organizations->count(),
        ]);

        foreach ($organizations as $organization) {
            Log::info("Processing organization: {$organization->name} (ID: {$organization->id})");

            foreach ($this->defaultStatuses as $statusData) {
                $this->createOrUpdateStatus($statusData, $organization->id);
            }

            // Ajouter des statuts personnalisés pour certaines organisations
            if ($this->shouldAddCustomStatuses($organization)) {
                $this->addCustomStatusesForOrganization($organization);
            }
        }
    }

    /**
     * Créer ou mettre à jour un statut avec gestion intelligente
     */
    private function createOrUpdateStatus(array $statusData, ?int $organizationId): void
    {
        try {
            $conditions = [
                'slug' => $statusData['slug'],
                'organization_id' => $organizationId,
            ];

            $status = DriverStatus::updateOrCreate(
                $conditions,
                array_merge($statusData, ['organization_id' => $organizationId])
            );

            $action = $status->wasRecentlyCreated ? 'Created' : 'Updated';

            Log::debug("{$action} driver status", [
                'status_id' => $status->id,
                'name' => $status->name,
                'organization_id' => $organizationId,
                'action' => strtolower($action),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create/update driver status', [
                'status_name' => $statusData['name'],
                'organization_id' => $organizationId,
                'error' => $e->getMessage(),
            ]);

            // Ne pas faire échouer tout le seeder pour un statut
            // mais logger l'erreur pour investigation
        }
    }

    /**
     * Déterminer si des statuts personnalisés doivent être ajoutés
     */
    private function shouldAddCustomStatuses(Organization $organization): bool
    {
        // Logique métier pour déterminer si une organisation a besoin de statuts personnalisés
        // Par exemple, basé sur le type d'organisation, le pays, etc.
        return $organization->type === 'enterprise' || 
               $organization->country === 'DZ' ||
               $organization->has_custom_workflow === true;
    }

    /**
     * Ajouter des statuts personnalisés pour une organisation spécifique
     */
    private function addCustomStatusesForOrganization(Organization $organization): void
    {
        $customStatuses = [
            [
                'name' => 'Mission spéciale',
                'slug' => 'mission-speciale',
                'description' => 'Chauffeur affecté à une mission confidentielle ou prioritaire',
                'color' => '#EC4899', // Pink-500
                'icon' => 'fa-star',
                'is_active' => true,
                'sort_order' => 11,
                'can_drive' => true,
                'can_assign' => false,
                'requires_validation' => true,
            ],
            [
                'name' => 'Réserve',
                'slug' => 'reserve',
                'description' => 'Chauffeur en réserve pour urgences',
                'color' => '#14B8A6', // Teal-500
                'icon' => 'fa-clock',
                'is_active' => true,
                'sort_order' => 12,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
            ],
        ];

        foreach ($customStatuses as $statusData) {
            $this->createOrUpdateStatus($statusData, $organization->id);
        }

        Log::info("Added custom statuses for organization: {$organization->name}");
    }

    /**
     * Nettoyer les statuts orphelins ou invalides
     */
    public function cleanupInvalidStatuses(): void
    {
        Log::info('Cleaning up invalid or orphaned driver statuses...');

        // Supprimer les statuts sans nom
        $deletedCount = DriverStatus::whereNull('name')->orWhere('name', '')->delete();

        if ($deletedCount > 0) {
            Log::warning("Deleted {$deletedCount} driver statuses with empty names");
        }

        // Supprimer les statuts d'organisations inexistantes
        $orphanedStatuses = DriverStatus::whereNotNull('organization_id')
            ->whereNotIn('organization_id', Organization::pluck('id'))
            ->delete();

        if ($orphanedStatuses > 0) {
            Log::warning("Deleted {$orphanedStatuses} orphaned driver statuses");
        }
    }
}
