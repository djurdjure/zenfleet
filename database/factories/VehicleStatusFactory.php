<?php

namespace Database\Factories;

use App\Models\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleStatusFactory extends Factory
{
    protected $model = VehicleStatus::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Parking',
            'Assigned',
            'Maintenance',
            'Out of Service',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['car', 'wrench', 'alert-triangle']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 10),
            'can_be_assigned' => $name === 'Assigned',
            'is_operational' => $name !== 'Out of Service',
            'requires_maintenance' => $name === 'Maintenance',
            'organization_id' => null,
        ];
    }
}
