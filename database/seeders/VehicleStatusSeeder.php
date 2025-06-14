<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\VehicleStatus;

class VehicleStatusSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Parking', 'En maintenance', 'Hors service', 'En mission', 'En attente'];
        foreach ($types as $type) {
            VehicleStatus::firstOrCreate(['name' => $type]);
        }
    }
}
