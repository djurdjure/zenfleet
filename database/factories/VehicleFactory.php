<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $initialMileage = $this->faker->numberBetween(5_000, 50_000);

        return [
            'registration_plate' => $this->faker->unique()->regexify('[A-Z]{2}-[0-9]{3}-[A-Z]{2}'),
            'vin' => $this->faker->unique()->bothify(str_repeat('*', 17)),
            'brand' => $this->faker->randomElement(['Renault', 'Peugeot', 'Dacia', 'Volkswagen', 'Toyota']),
            'model' => $this->faker->randomElement(['Clio', '208', 'Sandero', 'Golf', 'Yaris']),
            'color' => $this->faker->safeColorName(),
            'manufacturing_year' => $this->faker->numberBetween(2015, now()->year),
            'initial_mileage' => $initialMileage,
            'current_mileage' => $initialMileage + $this->faker->numberBetween(1_000, 50_000),
            'purchase_price' => $this->faker->randomFloat(2, 1_000_000, 5_000_000),
            'current_value' => $this->faker->randomFloat(2, 800_000, 4_000_000),

            // Utiliser des factories pour toute FK
            'vehicle_type_id' => VehicleType::firstOrCreate(['name' => 'Sedan'])->id,
            'fuel_type_id' => FuelType::firstOrCreate(['name' => 'Diesel'])->id,
            'transmission_type_id' => TransmissionType::firstOrCreate(['name' => 'Manual'])->id,
            'status_id' => VehicleStatus::firstOrCreate(
                ['name' => 'Parking'],
                ['is_active' => true, 'color' => '#6b7280']
            )->id,
        ];
    }
}
