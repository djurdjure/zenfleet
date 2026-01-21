<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'employee_number' => $this->faker->unique()->numerify('EMP-#####'),
            'personal_phone'  => $this->faker->unique()->phoneNumber(),
            'license_number'  => $this->faker->unique()->numerify('LN-########'),
            'status_id' => DriverStatus::query()->inRandomOrder()->value('id') ?? DriverStatus::factory(),
            'birth_date' => $this->faker->dateTimeBetween('-50 years', '-22 years'),
            'recruitment_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(), // UserFactory lie déjà une organization
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Driver $driver) {
            if ($driver->user) {
                $driver->user->update([
                    'organization_id' => $driver->organization_id,
                    'first_name' => $driver->first_name,
                    'last_name'  => $driver->last_name,
                ]);

                // Attribuer un rôle seulement si spatie/permission est migré en test
                if (method_exists($driver->user, 'assignRole')) {
                    try {
                        $driver->user->assignRole('Chauffeur');
                    } catch (\Throwable $e) {
                        // Ignorer en environnement de test si rôles non seedés
                    }
                }
            }
        });
    }
}
