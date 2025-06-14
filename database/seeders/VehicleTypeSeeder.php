<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Berline', 'SUV', 'Utilitaire léger', 'Camion', 'Bus', 'Moto'];
        foreach ($types as $type) {
            VehicleType::firstOrCreate(['name' => $type]);
        }
    }
}
