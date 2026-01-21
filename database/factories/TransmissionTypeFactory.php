<?php

namespace Database\Factories;

use App\Models\TransmissionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransmissionTypeFactory extends Factory
{
    protected $model = TransmissionType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Manual', 'Automatic']),
        ];
    }
}
