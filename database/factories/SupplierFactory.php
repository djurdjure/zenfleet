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
            'company' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'supplier_category_id' => null, // à surcharger si nécessaire
            'organization_id' => Organization::factory(),
        ];
    }
}
