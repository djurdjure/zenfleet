<?php
namespace Database\Seeders\Maintenance;
use App\Models\Maintenance\RecurrenceUnit;
use Illuminate\Database\Seeder;
class RecurrenceUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['Jours', 'Mois', 'Kilomètres'];
        foreach ($units as $unit) {
            RecurrenceUnit::firstOrCreate(['name' => $unit]);
        }
    }
}
