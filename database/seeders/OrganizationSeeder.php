<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\AlgeriaWilaya;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some wilayas for realistic test data
        $wilayas = AlgeriaWilaya::where('is_active', true)->pluck('code')->toArray();

        if (empty($wilayas)) {
            $this->command->error('No wilayas found. Please run the algeria_tables migration first.');
            return;
        }

        // Create test organizations
        $organizations = [
            [
                'name' => 'Transport Algérie Express',
                'legal_name' => 'SARL Transport Algérie Express',
                'organization_type' => 'sme',
                'industry' => 'Transport et Logistique',
                'primary_email' => 'contact@transport-alger.dz',
                'phone_number' => '+213 21 123 456',
                'website' => 'https://www.transport-alger.dz',
                'address' => 'Zone Industrielle Rouiba, Lot 25',
                'wilaya' => '16',
                'city' => 'Alger',
                'commune' => 'Rouiba',
                'zip_code' => '16012',
                'trade_register' => '16/00-1234567B23',
                'nif' => '098765432109876',
                'ai' => '16001234',
                'nis' => '12345678901234',
                'manager_first_name' => 'Ahmed',
                'manager_last_name' => 'Benali',
                'manager_nin' => '123456789012345678',
                'manager_phone_number' => '+213 555 123 456',
                'manager_dob' => '1980-05-15',
                'manager_pob' => 'Alger',
                'manager_address' => 'Cité El Madania, Bt A, Appt 12, Alger',
                'status' => 'active',
                'description' => 'Entreprise spécialisée dans le transport de marchandises et la logistique en Algérie.'
            ],
            [
                'name' => 'Logistics Oran Pro',
                'legal_name' => 'EURL Logistics Oran Pro',
                'organization_type' => 'enterprise',
                'industry' => 'Logistique',
                'primary_email' => 'info@logistics-oran.dz',
                'phone_number' => '+213 41 987 654',
                'website' => 'https://www.logistics-oran.dz',
                'address' => 'Zone Portuaire d\'Oran, Hangar 15',
                'wilaya' => '31',
                'city' => 'Oran',
                'commune' => 'Oran Centre',
                'zip_code' => '31000',
                'trade_register' => '31/00-9876543A19',
                'nif' => '567890123456789',
                'ai' => '31009876',
                'nis' => '98765432109876',
                'manager_first_name' => 'Fatima',
                'manager_last_name' => 'Kaddour',
                'manager_nin' => '987654321098765432',
                'manager_phone_number' => '+213 666 987 654',
                'manager_dob' => '1975-12-03',
                'manager_pob' => 'Oran',
                'manager_address' => 'Hai Es Salem, Villa 45, Oran',
                'status' => 'active',
                'description' => 'Solutions logistiques complètes pour l\'import-export et le transport national.'
            ],
            [
                'name' => 'Fleet Constantine',
                'legal_name' => 'SPA Fleet Management Constantine',
                'organization_type' => 'enterprise',
                'industry' => 'Gestion de Flotte',
                'primary_email' => 'contact@fleet-constantine.dz',
                'phone_number' => '+213 31 456 789',
                'website' => 'https://www.fleet-constantine.dz',
                'address' => 'Boulevard Rahmani Achour, Résidence El Feth',
                'wilaya' => '25',
                'city' => 'Constantine',
                'commune' => 'Constantine Centre',
                'zip_code' => '25000',
                'trade_register' => '25/00-5555444C21',
                'nif' => '234567890123456',
                'ai' => '25005555',
                'nis' => '23456789012345',
                'manager_first_name' => 'Karim',
                'manager_last_name' => 'Messaoud',
                'manager_nin' => '456789012345678901',
                'manager_phone_number' => '+213 777 456 789',
                'manager_dob' => '1985-08-20',
                'manager_pob' => 'Constantine',
                'manager_address' => 'Cité Boussouf, Bloc 12, Appt 8, Constantine',
                'status' => 'active',
                'description' => 'Spécialiste en gestion et maintenance de flottes de véhicules professionnels.'
            ],
            [
                'name' => 'Transport Startup DZ',
                'legal_name' => 'SARL Transport Innovation',
                'organization_type' => 'startup',
                'industry' => 'Tech Transport',
                'primary_email' => 'hello@transport-startup.dz',
                'phone_number' => '+213 23 789 123',
                'website' => 'https://www.transport-startup.dz',
                'address' => 'Cyber Park Sidi Abdellah, Bureau 201',
                'wilaya' => '16',
                'city' => 'Sidi Abdellah',
                'commune' => 'Zeralda',
                'zip_code' => '16075',
                'trade_register' => '16/00-7777888D22',
                'nif' => '345678901234567',
                'ai' => '16007777',
                'nis' => '34567890123456',
                'manager_first_name' => 'Yacine',
                'manager_last_name' => 'Boudjedra',
                'manager_nin' => '789012345678901234',
                'manager_phone_number' => '+213 888 789 123',
                'manager_dob' => '1990-03-12',
                'manager_pob' => 'Tizi Ouzou',
                'manager_address' => 'Nouvelle Ville Ali Mendjeli, Bloc 5, Appt 15',
                'status' => 'pending',
                'description' => 'Startup innovante dans la digitalisation du transport en Algérie.'
            ]
        ];

        foreach ($organizations as $orgData) {
            Organization::create($orgData);
        }

        $this->command->info('Created ' . count($organizations) . ' test organizations successfully!');
    }
}
