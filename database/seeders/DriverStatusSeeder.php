<?php
namespace Database\Seeders;
use App\Models\DriverStatus;
use Illuminate\Database\Seeder;

class DriverStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Disponible', 'En congé', 'Suspendu', 'Inactif', 'En mission', 'Ex-employé'];
        foreach ($statuses as $status) {
            DriverStatus::firstOrCreate(['name' => $status]);
        }
    }
}
