<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TransmissionType;

class TransmissionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Manuelle', 'Automatique'];
        foreach ($types as $type) {
            TransmissionType::firstOrCreate(['name' => $type]);
        }
    }
}
