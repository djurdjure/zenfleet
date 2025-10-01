<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MaintenanceType;
use App\Models\MaintenanceProvider;
use App\Models\MaintenanceSchedule;
use App\Models\Organization;
use App\Models\Vehicle;

/**
 * Seeder pour le module de maintenance enterprise-grade
 * Crée des données de démonstration réalistes pour toutes les organisations
 */
class MaintenanceModuleSeeder extends Seeder
{
    /**
     * Exécution du seeder
     */
    public function run(): void
    {
        $this->command->info('🔧 Création des données de démonstration pour le module maintenance...');

        // Obtenir toutes les organisations actives
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            $this->command->info("Traitement de l'organisation : {$organization->name}");

            // Créer les types de maintenance
            $maintenanceTypes = $this->createMaintenanceTypes($organization);

            // Créer les fournisseurs
            $providers = $this->createMaintenanceProviders($organization);

            // Créer les planifications pour les véhicules existants
            $this->createMaintenanceSchedules($organization, $maintenanceTypes);
        }

        $this->command->info('✅ Module maintenance initialisé avec succès !');
    }

    /**
     * Créer les types de maintenance standards
     */
    private function createMaintenanceTypes(Organization $organization): array
    {
        $types = [
            // Maintenance préventive
            [
                'name' => 'Vidange moteur',
                'description' => 'Changement de l\'huile moteur et du filtre à huile',
                'category' => 'preventive',
                'is_recurring' => true,
                'default_interval_km' => 10000,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 60,
                'estimated_cost' => 8500.00,
            ],
            [
                'name' => 'Révision générale',
                'description' => 'Contrôle complet du véhicule selon le plan d\'entretien constructeur',
                'category' => 'revision',
                'is_recurring' => true,
                'default_interval_km' => 20000,
                'default_interval_days' => 730,
                'estimated_duration_minutes' => 180,
                'estimated_cost' => 25000.00,
            ],
            [
                'name' => 'Changement des freins',
                'description' => 'Remplacement des plaquettes et/ou disques de frein',
                'category' => 'preventive',
                'is_recurring' => true,
                'default_interval_km' => 40000,
                'default_interval_days' => null,
                'estimated_duration_minutes' => 120,
                'estimated_cost' => 15000.00,
            ],
            [
                'name' => 'Contrôle technique',
                'description' => 'Visite technique obligatoire annuelle',
                'category' => 'inspection',
                'is_recurring' => true,
                'default_interval_km' => null,
                'default_interval_days' => 365,
                'estimated_duration_minutes' => 45,
                'estimated_cost' => 3500.00,
            ],
            [
                'name' => 'Changement de pneumatiques',
                'description' => 'Remplacement des pneus usagés',
                'category' => 'preventive',
                'is_recurring' => false,
                'default_interval_km' => 60000,
                'default_interval_days' => null,
                'estimated_duration_minutes' => 90,
                'estimated_cost' => 35000.00,
            ],
            [
                'name' => 'Réparation système électrique',
                'description' => 'Diagnostic et réparation des problèmes électriques',
                'category' => 'corrective',
                'is_recurring' => false,
                'default_interval_km' => null,
                'default_interval_days' => null,
                'estimated_duration_minutes' => 240,
                'estimated_cost' => 12000.00,
            ],
            [
                'name' => 'Entretien climatisation',
                'description' => 'Nettoyage et recharge du système de climatisation',
                'category' => 'preventive',
                'is_recurring' => true,
                'default_interval_km' => null,
                'default_interval_days' => 730,
                'estimated_duration_minutes' => 75,
                'estimated_cost' => 6500.00,
            ],
            [
                'name' => 'Inspection sécurité',
                'description' => 'Contrôle de sécurité approfondi du véhicule',
                'category' => 'inspection',
                'is_recurring' => true,
                'default_interval_km' => 30000,
                'default_interval_days' => 180,
                'estimated_duration_minutes' => 90,
                'estimated_cost' => 4500.00,
            ],
        ];

        $createdTypes = [];

        foreach ($types as $typeData) {
            $typeData['organization_id'] = $organization->id;
            $typeData['is_active'] = true;

            $type = MaintenanceType::create($typeData);
            $createdTypes[] = $type;
        }

        $this->command->info("  ✓ {$organization->name}: " . count($createdTypes) . " types de maintenance créés");

        return $createdTypes;
    }

    /**
     * Créer les fournisseurs de maintenance
     */
    private function createMaintenanceProviders(Organization $organization): array
    {
        $providers = [
            [
                'name' => 'Garage Central',
                'company_name' => 'Garage Central SARL',
                'email' => 'contact@garage-central.dz',
                'phone' => '021-123-456',
                'address' => '15 Rue des Frères Bouadou',
                'city' => 'Alger',
                'postal_code' => '16000',
                'specialties' => ['engine', 'transmission', 'brake', 'general'],
                'rating' => 4.2,
            ],
            [
                'name' => 'Auto Service Plus',
                'company_name' => 'Auto Service Plus EURL',
                'email' => 'info@autoserviceplus.dz',
                'phone' => '021-789-012',
                'address' => '42 Boulevard Mohamed V',
                'city' => 'Oran',
                'postal_code' => '31000',
                'specialties' => ['electrical', 'ac', 'lighting', 'fuel_system'],
                'rating' => 4.7,
            ],
            [
                'name' => 'Pneumatiques Express',
                'company_name' => 'Pneus Express SPA',
                'email' => 'vente@pneusexpress.dz',
                'phone' => '025-345-678',
                'address' => '28 Zone Industrielle',
                'city' => 'Constantine',
                'postal_code' => '25000',
                'specialties' => ['tire', 'suspension'],
                'rating' => 4.0,
            ],
            [
                'name' => 'Carrosserie Moderne',
                'company_name' => 'Carrosserie Moderne & Fils',
                'email' => 'atelier@carrosserie-moderne.dz',
                'phone' => '027-567-890',
                'address' => '67 Rue de l\'Indépendance',
                'city' => 'Sétif',
                'postal_code' => '19000',
                'specialties' => ['bodywork', 'general'],
                'rating' => 3.8,
            ],
            [
                'name' => 'Mécanique Pro',
                'company_name' => 'Atelier Mécanique Professionnel',
                'email' => 'mecanique@pro-auto.dz',
                'phone' => '029-234-567',
                'address' => '134 Route Nationale 1',
                'city' => 'Blida',
                'postal_code' => '09000',
                'specialties' => ['engine', 'cooling', 'exhaust', 'general'],
                'rating' => 4.5,
            ],
        ];

        $createdProviders = [];

        foreach ($providers as $providerData) {
            $providerData['organization_id'] = $organization->id;
            $providerData['is_active'] = true;

            $provider = MaintenanceProvider::create($providerData);
            $createdProviders[] = $provider;
        }

        $this->command->info("  ✓ {$organization->name}: " . count($createdProviders) . " fournisseurs créés");

        return $createdProviders;
    }

    /**
     * Créer les planifications de maintenance pour les véhicules
     */
    private function createMaintenanceSchedules(Organization $organization, array $maintenanceTypes): void
    {
        $vehicles = Vehicle::where('organization_id', $organization->id)->get();

        if ($vehicles->isEmpty()) {
            $this->command->warn("  ⚠ {$organization->name}: Aucun véhicule trouvé, planifications ignorées");
            return;
        }

        $schedulesCreated = 0;
        $today = Carbon::today();

        foreach ($vehicles as $vehicle) {
            foreach ($maintenanceTypes as $type) {
                // Créer uniquement les planifications pour les types récurrents
                if (!$type->is_recurring) {
                    continue;
                }

                // Calculer la prochaine échéance basée sur l'âge du véhicule et les intervalles
                $nextDueDate = null;
                $nextDueMileage = null;

                if ($type->default_interval_days) {
                    // Simuler des échéances variées : certaines passées, d'autres futures
                    $daysVariation = rand(-30, 180); // Entre 30 jours en retard et 180 jours dans le futur
                    $nextDueDate = $today->copy()->addDays($daysVariation);
                }

                if ($type->default_interval_km && $vehicle->current_mileage) {
                    // Calculer basé sur le kilométrage actuel
                    $kmVariation = rand(-5000, 15000); // Variation pour créer des situations diverses
                    $nextDueMileage = $vehicle->current_mileage + $kmVariation;

                    // S'assurer que le kilométrage n'est pas négatif
                    if ($nextDueMileage < $vehicle->current_mileage) {
                        $nextDueMileage = $vehicle->current_mileage + $type->default_interval_km;
                    }
                }

                // Créer la planification
                MaintenanceSchedule::create([
                    'organization_id' => $organization->id,
                    'vehicle_id' => $vehicle->id,
                    'maintenance_type_id' => $type->id,
                    'next_due_date' => $nextDueDate,
                    'next_due_mileage' => $nextDueMileage,
                    'interval_km' => $type->default_interval_km,
                    'interval_days' => $type->default_interval_days,
                    'alert_km_before' => 1000,
                    'alert_days_before' => 7,
                    'is_active' => true,
                ]);

                $schedulesCreated++;
            }
        }

        $this->command->info("  ✓ {$organization->name}: {$schedulesCreated} planifications créées pour " . $vehicles->count() . " véhicule(s)");
    }
}