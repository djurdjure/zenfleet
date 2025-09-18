<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding ZenFleet...');

        // 1. Créer les rôles et permissions d'abord
        $this->call([
            ZenFleetRolesPermissionsSeeder::class,
        ]);

        // 2. Créer l'organisation principale ZenFleet
        $this->createZenFleetOrganization();

        // 3. Créer des organisations de test avec tous les types d'utilisateurs
        if (app()->environment('local', 'development', 'testing')) {
            $this->createTestOrganizations();
        }

        $this->command->info('✅ Seeding ZenFleet terminé avec succès!');
    }

    private function createZenFleetOrganization(): void
    {
        $this->command->info('🏢 Création de l\'organisation ZenFleet...');

        $zenfleet = Organization::firstOrCreate(
            ['email' => 'contact@zenfleet.dz'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'ZenFleet Platform',
                'slug' => 'zenfleet-platform',
                'legal_name' => 'ZenFleet SARL',
                'organization_type' => 'enterprise',
                'industry' => 'Technologie',
                'description' => 'Plateforme de gestion de flotte automobile pour l\'Algérie',

                // Informations légales algériennes
                'nif' => '123456789012345',
                'ai' => '12345678901234',
                'nis' => '123456789012345',
                'trade_register' => '16/23-123456 B 10',
                'legal_form' => 'SARL',
                'registration_date' => '2020-01-15',

                // Contact et adresse
                'phone' => '+213 21 12 34 56',
                'website' => 'https://zenfleet.dz',
                'address' => 'Cité Bouchaoui, Lot 15',
                'city' => 'Alger',
                'postal_code' => '16000',
                'country' => 'DZ',
                'wilaya' => '16',

                // Configuration
                'timezone' => 'Africa/Algiers',
                'currency' => 'DZD',
                'language' => 'fr',
                'subscription_plan' => 'enterprise',
                'status' => 'active',
                'activated_at' => now(),

                // Responsable légal
                'manager_name' => 'Ahmed Benali',
                'manager_nin' => '123456789012345678',
                'manager_function' => 'gerant',
                'manager_email' => 'ahmed.benali@zenfleet.dz',
            ]
        );

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@zenfleet.dz'],
            [
                'name' => 'Super Administrateur',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => bcrypt('password'),
                'phone' => '+213 21 12 34 57',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        $this->command->info('👑 Super Admin créé: superadmin@zenfleet.dz');
    }

    private function createTestOrganizations(): void
    {
        $this->command->info('🏭 Création des organisations de test...');

        // Organisation 1: TransAlger SARL (Enterprise)
        $transAlger = Organization::factory()->enterprise()->active()->create([
            'name' => 'TransAlger',
            'legal_name' => 'TransAlger SARL',
            'email' => 'contact@transalger.dz',
            'industry' => 'Transport',
            'description' => 'Société de transport et de logistique basée à Alger',
            'wilaya' => '16', // Alger
            'city' => 'Alger',
            'manager_name' => 'Karim Abdellah',
            'manager_email' => 'karim.abdellah@transalger.dz',
        ]);

        $this->createUsersForOrganization($transAlger, 'TransAlger');

        // Organisation 2: LogistiqueOran SPA (Professional)
        $logistiqueOran = Organization::factory()->sme()->active()->create([
            'name' => 'LogistiqueOran',
            'legal_name' => 'LogistiqueOran SPA',
            'email' => 'contact@logistiqueoran.dz',
            'industry' => 'Logistique',
            'description' => 'Solutions logistiques pour l\'Ouest algérien',
            'wilaya' => '31', // Oran
            'city' => 'Oran',
            'manager_name' => 'Amina Bensaid',
            'manager_email' => 'amina.bensaid@logistiqueoran.dz',
        ]);

        $this->createUsersForOrganization($logistiqueOran, 'LogistiqueOran', false);

        $this->command->info('📊 Organisations de test créées');
    }

    private function createUsersForOrganization(Organization $org, string $prefix, bool $fullTeam = true): void
    {
        $domain = strtolower($prefix) . '.dz';

        // 1. Admin de l'organisation
        $admin = User::factory()->create([
            'email' => "admin@{$domain}",
            'name' => "Admin {$prefix}",
            'first_name' => 'Admin',
            'last_name' => $prefix,
        ]);
        $admin->assignRole('Admin');

        // 2. Gestionnaire de flotte
        $fleetManager = User::factory()->create([
            'email' => "flotte@{$domain}",
            'name' => "Gestionnaire Flotte {$prefix}",
            'first_name' => 'Gestionnaire',
            'last_name' => 'Flotte',
        ]);
        $fleetManager->assignRole('Gestionnaire Flotte');

        if (!$fullTeam) {
            // Équipe réduite
            $supervisor = User::factory()->create([
                'email' => "superviseur@{$domain}",
                'name' => "Superviseur {$prefix}",
                'first_name' => 'Superviseur',
                'last_name' => $prefix,
            ]);
            $supervisor->assignRole('Superviseur');

            // 2 chauffeurs
            for ($i = 1; $i <= 2; $i++) {
                $driver = User::factory()->create([
                    'email' => "chauffeur{$i}@{$domain}",
                    'name' => "Chauffeur {$i} {$prefix}",
                    'first_name' => "Chauffeur {$i}",
                    'last_name' => $prefix,
                ]);
                $driver->assignRole('Chauffeur');
            }

            $this->command->info("👥 Équipe réduite créée pour {$org->name} (5 utilisateurs)");
            return;
        }

        // Équipe complète pour TransAlger
        // 2 Superviseurs
        for ($i = 1; $i <= 2; $i++) {
            $supervisor = User::factory()->create([
                'email' => "superviseur{$i}@{$domain}",
                'name' => "Superviseur {$i} {$prefix}",
                'first_name' => "Superviseur {$i}",
                'last_name' => $prefix,
            ]);
            $supervisor->assignRole('Superviseur');
        }

        // 5 Chauffeurs
        for ($i = 1; $i <= 5; $i++) {
            $driver = User::factory()->create([
                'email' => "chauffeur{$i}@{$domain}",
                'name' => "Chauffeur {$i} {$prefix}",
                'first_name' => "Chauffeur {$i}",
                'last_name' => $prefix,
            ]);
            $driver->assignRole('Chauffeur');
        }

        // Comptable
        $accountant = User::factory()->create([
            'email' => "comptable@{$domain}",
            'name' => "Comptable {$prefix}",
            'first_name' => 'Comptable',
            'last_name' => $prefix,
        ]);
        $accountant->assignRole('Comptable');

        // Mécanicien
        $mechanic = User::factory()->create([
            'email' => "mecanicien@{$domain}",
            'name' => "Mécanicien {$prefix}",
            'first_name' => 'Mécanicien',
            'last_name' => $prefix,
        ]);
        $mechanic->assignRole('Mécanicien');

        $this->command->info("👥 Équipe complète créée pour {$org->name} (12 utilisateurs)");
    }
}