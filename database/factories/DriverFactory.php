<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Driver::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activeStatus = DriverStatus::whereIn('name', ['Actif', 'Disponible'])->inRandomOrder()->first();
        
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'employee_number' => fake()->unique()->numerify('EMP-#####'),
            'personal_phone' => fake()->unique()->phoneNumber(),
            'license_number' => fake()->unique()->numerify('LN-########'),
            'status_id' => $activeStatus ? $activeStatus->id : DriverStatus::factory(),
            'birth_date' => fake()->dateTimeBetween('-50 years', '-22 years'),
            'recruitment_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'user_id' => User::factory(), // This links to a User factory call
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Driver $driver) {
            // Ensure the associated user has the same organization and name details
            // and is assigned the 'Chauffeur' role.
            if ($driver->user) {
                $driver->user->update([
                    'organization_id' => $driver->organization_id,
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                ]);
                $driver->user->assignRole('Chauffeur');
            }
        });
    }
}
