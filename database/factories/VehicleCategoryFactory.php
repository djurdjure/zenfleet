<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleCategory>
 */
class VehicleCategoryFactory extends Factory
{
    protected $model = VehicleCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => $this->faker->unique()->randomElement([
                'Utilitaire',
                'Berline',
                'SUV',
                'Camionnette',
                'Poids Lourd',
                'Véhicule Léger',
            ]),
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'color_code' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement([
                'truck',
                'car',
                'van',
                'bus',
            ]),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category is for a specific organization.
     */
    public function forOrganization(int $organizationId): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organizationId,
        ]);
    }
}
