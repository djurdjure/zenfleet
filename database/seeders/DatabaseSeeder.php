<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
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

        // --- Création de données de test pour une organisation cliente ---
        if (app()->environment('local', 'development')) {
            $clientOrganization = Organization::factory()->create(['name' => 'Client de Démo Inc.']);
            if ($clientOrganization) {
                $clientAdmin = User::factory()->create([
                    'first_name' => 'Admin',
                    'last_name' => 'Client',
                    'email' => 'client.admin@exemple.com',
                    'organization_id' => $clientOrganization->id,
                ]);
                $clientAdmin->assignRole('Admin');

                \App\Models\Driver::factory()->count(5)->create(['organization_id' => $clientOrganization->id]);
                \App\Models\Vehicle::factory()->count(10)->create(['organization_id' => $clientOrganization->id]);
                $this->command->info('Demo organization with data created.');
            }
        }
    }
}