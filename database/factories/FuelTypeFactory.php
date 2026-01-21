<?php

namespace Database\Factories;

use App\Models\FuelType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelTypeFactory extends Factory
{
    protected $model = FuelType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Diesel', 'Gasoline', 'Electric', 'Hybrid']),
        ];
    }
}
