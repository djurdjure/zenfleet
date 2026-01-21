<?php

namespace Database\Factories;

use App\Models\DriverStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DriverStatusFactory extends Factory
{
    protected $model = DriverStatus::class;

    public function configure(): static
    {
        return $this->afterMaking(function (DriverStatus $status) {
            if ($status->name) {
                $baseSlug = Str::slug($status->name);
                $slug = $baseSlug;

                if (DriverStatus::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . Str::random(6);
                }

                $status->slug = $slug;
            }
        });
    }

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Available',
            'Assigned',
            'Off Duty',
            'On Leave',
            'Training',
            'Unavailable',
            'On Break',
            'Standby',
            'In Transit',
            'At Depot',
            'Maintenance',
            'Suspended',
        ]);

        return [
            'name' => $name,
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['check', 'user', 'car']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 10),
            'can_drive' => true,
            'can_assign' => true,
            'requires_validation' => false,
            'organization_id' => null,
        ];
    }
}
