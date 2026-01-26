<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuelType;
use App\Models\VehicleType;
use App\Models\TransmissionType;
use Illuminate\Support\Facades\DB;

class FixImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Fix Fuel Types (Carburants)
        $fuelTypes = [
            'Essence',
            'Diesel',
            'GPL',
            'Électrique',
            'Hybride',
            'Hybride Rechargeable',
            'Ethanol',
            'Hydrogène',
            'GNV', // Gaz Naturel Véhicule
            'Bioéthanol'
        ];

        foreach ($fuelTypes as $type) {
            FuelType::firstOrCreate(
                ['name' => $type]
            );
        }
        $this->command->info('✅ Fuel Types populated: ' . implode(', ', $fuelTypes));

        // 2. Fix Vehicle Types (Types de véhicules)
        // Ensure 'Berline' exists as it is the canonical type in import logic
        $vehicleTypes = [
            'Berline',
            'SUV',
            'Citadine',
            'Break',
            'Coupé',
            'Cabriolet',
            'Monospace',
            'Crossover',
            'Pick-up',
            'Utilitaire léger',
            'Camionnette',
            'Camion',
            'Bus',
            'Autocar',
            'Minibus',
            'Moto',
            'Scooter',
            'Quad',
            'Remorque',
            'Semi-remorque',
            // Ajout pour compatibilité si 'Voiture' existe déjà, 
            // mais 'Berline' est préféré par le système
            'Voiture'
        ];

        foreach ($vehicleTypes as $type) {
            VehicleType::firstOrCreate(
                ['name' => $type]
            );
        }
        $this->command->info('✅ Vehicle Types populated including: Berline');

        // 3. Fix Transmission Types (just in case they are missing too)
        $transmissionTypes = [
            'Manuelle',
            'Automatique',
            'Semi-automatique',
            'CVT'
        ];

        foreach ($transmissionTypes as $type) {
            TransmissionType::firstOrCreate(
                ['name' => $type]
            );
        }
        $this->command->info('✅ Transmission Types verified');
    }
}
