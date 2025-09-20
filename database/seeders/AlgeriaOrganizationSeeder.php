<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlgeriaOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates realistic Algeria-centric organizations for testing
     */
    public function run(): void
    {
        // Create 3 baseline organizations for consistent testing
        $this->createBaselineOrganizations();

        // Create additional random organizations
        $this->createRandomOrganizations();

        $this->command->info('✅ Algeria organizations seeded successfully');
    }

    /**
     * Create baseline organizations with predictable data for testing
     */
    private function createBaselineOrganizations(): void
    {
        $baselineOrgs = [
            [
                'uuid' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                'name' => 'Trans-Alger Logistics',
                'legal_name' => 'Trans-Alger Logistics SARL',
                'organization_type' => 'enterprise',
                'industry' => 'Transport',
                'description' => 'Entreprise leader dans le transport et la logistique en Algérie, desservant toutes les wilayas du pays.',
                'primary_email' => 'contact@transalger.dz',
                'phone_number' => '+213 21 12 34 56',
                'website' => 'https://transalger.dz',
                'status' => 'active',

                // Informations légales
                'trade_register' => '16/20-123456 A 15',
                'nif' => '098765432109876',
                'ai' => '01234567890123',
                'nis' => '123456789012345',

                // Adresse - Alger
                'address' => '15 Rue Didouche Mourad',
                'city' => 'Alger-Centre',
                'commune' => null,
                'zip_code' => '16000',
                'wilaya' => '16',

                // Représentant légal
                'manager_first_name' => 'Ahmed',
                'manager_last_name' => 'Benali',
                'manager_nin' => '162012345678901234',
                'manager_address' => '45 Rue des Martyrs, Alger',
                'manager_dob' => '1975-03-15',
                'manager_pob' => 'Alger',
                'manager_phone_number' => '+213 21 98 76 54',
                'created_at' => '2023-01-15 08:30:00',
                'updated_at' => '2024-12-01 10:15:00',
            ],

            [
                'uuid' => 'b2c3d4e5-f6a7-8901-bcde-f23456789012',
                'name' => 'Setif Fleet Services',
                'legal_name' => 'Setif Fleet Services EURL',
                'organization_type' => 'sme',
                'industry' => 'Services',
                'description' => 'Gestion de flotte automobile pour PME dans la région des Hauts Plateaux.',
                'primary_email' => 'info@setiffleet.dz',
                'phone_number' => '+213 36 45 67 89',
                'website' => null,
                'status' => 'active',

                // Informations légales
                'trade_register' => '19/22-987654 B 28',
                'nif' => '198765432109876',
                'ai' => '98765432109876',
                'nis' => '987654321098765',

                // Adresse - Sétif
                'address' => '25 Boulevard du 1er Novembre',
                'city' => 'Sétif',
                'commune' => 'Sétif Centre',
                'zip_code' => '19000',
                'wilaya' => '19',

                // Représentant légal
                'manager_first_name' => 'Fatima',
                'manager_last_name' => 'Meziani',
                'manager_nin' => '190585432167890123',
                'manager_address' => '12 Cité des Oliviers, Sétif',
                'manager_dob' => '1985-07-22',
                'manager_pob' => 'Sétif',
                'manager_phone_number' => '+213 36 78 90 12',
                'created_at' => '2023-05-20 14:20:00',
                'updated_at' => '2024-11-15 16:45:00',
            ],

            [
                'uuid' => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
                'name' => 'Oran Maritime Transport',
                'legal_name' => 'Oran Maritime Transport SPA',
                'organization_type' => 'enterprise',
                'industry' => 'Transport',
                'description' => 'Transport maritime et terrestre, import-export via le port d\'Oran.',
                'primary_email' => 'direction@omtransport.dz',
                'phone_number' => '+213 41 23 45 67',
                'website' => 'https://omtransport.dz',
                'status' => 'active',

                // Informations légales
                'trade_register' => '31/19-456789 A 42',
                'nif' => '318765432109876',
                'ai' => '31876543210987',
                'nis' => '318765432109876',

                // Adresse - Oran
                'address' => '8 Avenue de l\'ALN',
                'city' => 'Oran',
                'commune' => 'Oran Centre',
                'zip_code' => '31000',
                'wilaya' => '31',

                // Représentant légal
                'manager_first_name' => 'Karim',
                'manager_last_name' => 'Benaissa',
                'manager_nin' => '311278456789012345',
                'manager_address' => '102 Hai El Menzah, Oran',
                'manager_dob' => '1978-12-10',
                'manager_pob' => 'Oran',
                'manager_phone_number' => '+213 41 87 65 43',
                'created_at' => '2022-09-10 09:00:00',
                'updated_at' => '2024-10-30 11:30:00',
            ]
        ];

        foreach ($baselineOrgs as $orgData) {
            Organization::create($orgData);
        }

        $this->command->info('✅ Created 3 baseline organizations');
    }

    /**
     * Create additional random organizations for testing pagination and filters
     */
    private function createRandomOrganizations(): void
    {
        // Create 7 additional organizations using the factory
        Organization::factory()
            ->count(3)
            ->active()
            ->enterprise()
            ->create();

        Organization::factory()
            ->count(4)
            ->active()
            ->sme()
            ->create();

        // Create 2 inactive organizations for testing status filters
        Organization::factory()
            ->count(2)
            ->inactive()
            ->create();

        $this->command->info('✅ Created 9 additional random organizations');
    }
}