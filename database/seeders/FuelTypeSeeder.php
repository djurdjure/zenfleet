<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\FuelType;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Essence', 'Diesel', 'GPL', 'Électrique', 'Hybride'];
        foreach ($types as $type) {
            FuelType::firstOrCreate(['name' => $type]);
        }
    }
}
