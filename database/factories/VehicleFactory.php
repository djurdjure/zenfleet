<?php

namespace Database\Factories;

use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $initialMileage = $this->faker->numberBetween(5000, 50000);

        return [
            'registration_plate' => $this->faker->unique()->regexify('[A-Z]{2}-[0-9]{3}-[A-Z]{2}'),
            'vin' => $this->faker->unique()->bothify('*****************'),
            'brand' => $this->faker->randomElement(['Renault', 'Peugeot', 'Dacia', 'Volkswagen', 'Toyota']),
            'model' => $this->faker->randomElement(['Clio', '208', 'Sandero', 'Golf', 'Yaris']),
            'color' => $this->faker->safeColorName(),
            'manufacturing_year' => $this->faker->numberBetween(2015, now()->year),
            'initial_mileage' => $initialMileage,
            'current_mileage' => $initialMileage + $this->faker->numberBetween(1000, 50000),
            'purchase_price' => $this->faker->randomFloat(2, 1000000, 5000000),
            'current_value' => $this->faker->randomFloat(2, 800000, 4000000),

            // On récupère des IDs valides depuis les tables de référence
            'vehicle_type_id' => VehicleType::inRandomOrder()->first()->id,
            'fuel_type_id' => FuelType::inRandomOrder()->first()->id,
            'transmission_type_id' => TransmissionType::inRandomOrder()->first()->id,
            'status_id' => VehicleStatus::where('name', 'Parking')->first()->id,
        ];
    }
}