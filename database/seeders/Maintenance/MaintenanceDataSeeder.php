<?php
namespace Database\Seeders\Maintenance;
use Illuminate\Database\Seeder;
class MaintenanceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MaintenanceTypeSeeder::class,
            RecurrenceUnitSeeder::class,
            MaintenanceStatusSeeder::class,
        ]);
    }
}
