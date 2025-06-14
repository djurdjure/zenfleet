<?php
namespace Database\Seeders\Maintenance;
use App\Models\Maintenance\MaintenanceStatus;
use Illuminate\Database\Seeder;
class MaintenanceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Planifiée', 'En cours', 'Terminée', 'Annulée'];
        foreach ($statuses as $status) {
            MaintenanceStatus::firstOrCreate(['name' => $status]);
        }
    }
}
