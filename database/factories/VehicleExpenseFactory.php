<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleExpenseFactory extends Factory
{
    protected $model = VehicleExpense::class;

    public function configure(): static
    {
        return $this->afterMaking(function (VehicleExpense $expense) {
            if ($expense->tva_amount === null) {
                $expense->tva_amount = round(($expense->amount_ht * $expense->tva_rate) / 100, 2);
            }

            if ($expense->total_ttc === null) {
                $expense->total_ttc = round($expense->amount_ht + $expense->tva_amount, 2);
            }
        });
    }

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'vehicle_id' => Vehicle::factory(),
            'supplier_id' => null,
            'driver_id' => null,
            'expense_category' => $this->faker->randomElement([
                VehicleExpense::CATEGORY_MAINTENANCE_PREVENTIVE,
                VehicleExpense::CATEGORY_REPARATION,
                VehicleExpense::CATEGORY_CARBURANT,
                VehicleExpense::CATEGORY_ASSURANCE,
            ]),
            'expense_type' => $this->faker->randomElement(['Maintenance', 'Fuel', 'Insurance']),
            'amount_ht' => $this->faker->randomFloat(2, 1_000, 100_000),
            'tva_rate' => 19.0,
            'recorded_by' => User::factory(),
            'expense_date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'payment_status' => VehicleExpense::PAYMENT_PENDING,
            'approval_status' => 'pending',
        ];
    }
}
