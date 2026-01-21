<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'supplier_type' => $this->faker->randomElement([
                'mecanicien', 'assureur', 'station_service', 'pieces_detachees',
                'peinture_carrosserie', 'pneumatiques', 'electricite_auto',
                'controle_technique', 'transport_vehicules', 'autre'
            ]),
            'company_name' => $this->faker->company(),
            'contact_first_name' => $this->faker->firstName(),
            'contact_last_name' => $this->faker->lastName(),
            'contact_phone' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
            'contact_email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'wilaya' => $this->faker->randomElement(['16', '31', '19', '25']),
            'commune' => $this->faker->optional(0.6)->city(),
            'postal_code' => $this->faker->numerify('#####'),
            'phone' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
            'email' => $this->faker->unique()->companyEmail(),
            'organization_id' => Organization::factory(),
        ];
    }
}
