<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crée 5 chauffeurs en utilisant la DriverFactory
        Driver::factory()->count(7)->create();

        $this->command->info('7 sample drivers have been created.');
    }
}
