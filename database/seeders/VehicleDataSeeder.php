<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class VehicleDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VehicleTypeSeeder::class,
            FuelTypeSeeder::class,
            TransmissionTypeSeeder::class,
            VehicleStatusSeeder::class,
            // Nous pourrons ajouter un VehicleSeeder ici plus tard pour des véhicules de test
        ]);
    }
}
