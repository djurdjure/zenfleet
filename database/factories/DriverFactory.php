<?php

namespace Database\Factories;

use App\Models\DriverStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // On récupère un statut "Actif" ou "Disponible" au hasard pour le chauffeur
        $activeStatus = DriverStatus::whereIn('name', ['Actif', 'Disponible'])->inRandomOrder()->first();

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'employee_number' => fake()->unique()->numerify('EMP-#####'),
            'personal_phone' => fake()->unique()->phoneNumber(),
            'license_number' => fake()->unique()->numerify('LN-########'),
            'status_id' => $activeStatus->id,
            'birth_date' => fake()->dateTimeBetween('-50 years', '-22 years'),
            'recruitment_date' => fake()->dateTimeBetween('-5 years', 'now'),
            // ... vous pouvez ajouter d'autres champs si nécessaire
        ];
    }
}
