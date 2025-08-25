<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            ValidationLevelSeeder::class,
            VehicleDataSeeder::class,
            DriverStatusSeeder::class,
            Maintenance\MaintenanceDataSeeder::class,
            DefaultDocumentCategoriesSeeder::class,
        ]);

        $zenfleetOrganization = Organization::firstOrCreate(
            ['name' => 'ZENFLEET Platform'],
            ['uuid' => (string) Str::uuid()]
        );

        // --- Création de l'utilisateur SUPER ADMIN ---
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@zenfleet.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => bcrypt('password'),
                'organization_id' => $zenfleetOrganization->id,
                'email_verified_at' => now(),
            ]
        );

        // CORRECTION : On assigne le bon rôle
        $superAdminUser->syncRoles(['Super Admin']);
        $this->command->info('Super Admin user created and assigned.');
        
        // --- Création de données de test pour les environnements de développement ---
        if (app()->environment('local', 'development')) {
            // Seed data for ZENFLEET organization only if it was just created
            if ($zenfleetOrganization->wasRecentlyCreated) {
                Driver::factory()->count(2)->create(['organization_id' => $zenfleetOrganization->id]);
                Vehicle::factory()->count(3)->create(['organization_id' => $zenfleetOrganization->id]);
                Supplier::factory()->count(2)->create(['organization_id' => $zenfleetOrganization->id]);
                $this->command->info('Test data for ZENFLEET organization created.');
            }

            // Seed data for a demo client organization
            $clientOrganization = Organization::firstOrCreate(
                ['name' => 'Client de Démo Inc.'],
                ['uuid' => (string) Str::uuid()]
            );

            $clientAdmin = User::firstOrCreate(
                ['email' => 'client.admin@exemple.com'],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'Client',
                    'password' => bcrypt('password'),
                    'organization_id' => $clientOrganization->id,
                    'email_verified_at' => now(),
                ]
            );
            $clientAdmin->assignRole('Admin');

            // Seed related data only if the demo organization was just created
            if ($clientOrganization->wasRecentlyCreated) {
                Driver::factory()->count(5)->create(['organization_id' => $clientOrganization->id]);
                Vehicle::factory()->count(10)->create(['organization_id' => $clientOrganization->id]);
                Supplier::factory()->count(5)->create(['organization_id' => $clientOrganization->id]);
                $this->command->info('Demo organization with data created.');
            }
        }
    }
}