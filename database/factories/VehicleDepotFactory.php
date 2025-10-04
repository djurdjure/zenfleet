<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\VehicleDepot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleDepot>
 */
class VehicleDepotFactory extends Factory
{
    protected $model = VehicleDepot::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => 'Dépôt ' . $this->faker->city(),
            'code' => strtoupper($this->faker->unique()->lexify('DEP-???')),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement([
                'Alger',
                'Oran',
                'Constantine',
                'Annaba',
                'Blida',
                'Batna',
                'Sétif',
                'Tlemcen',
            ]),
            'wilaya' => $this->faker->numberBetween(1, 58),
            'postal_code' => $this->faker->numberBetween(16000, 48999),
            'phone' => $this->faker->numerify('0### ## ## ##'),
            'email' => $this->faker->optional()->safeEmail(),
            'manager_name' => $this->faker->name(),
            'capacity' => $this->faker->numberBetween(10, 100),
            'current_occupancy' => $this->faker->numberBetween(0, 50),
            'latitude' => $this->faker->latitude(28.0, 37.0), // Algeria lat range
            'longitude' => $this->faker->longitude(-8.7, 11.98), // Algeria lng range
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the depot is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the depot is full.
     */
    public function full(): static
    {
        return $this->state(function (array $attributes) {
            $capacity = $attributes['capacity'] ?? 50;
            return [
                'current_occupancy' => $capacity,
            ];
        });
    }

    /**
     * Indicate that the depot is for a specific organization.
     */
    public function forOrganization(int $organizationId): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organizationId,
        ]);
    }

    /**
     * Indicate that the depot is in Algiers.
     */
    public function inAlgiers(): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => 'Alger',
            'wilaya' => 16,
            'latitude' => 36.7538,
            'longitude' => 3.0588,
        ]);
    }
}
