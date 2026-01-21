<?php

namespace Database\Factories;

use App\Models\ExpenseBudget;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseBudgetFactory extends Factory
{
    protected $model = ExpenseBudget::class;

    public function definition(): array
    {
        $year = (int) date('Y');
        $month = $this->faker->numberBetween(1, 12);

        return [
            'organization_id' => Organization::factory(),
            'vehicle_id' => null,
            'expense_category' => $this->faker->randomElement([
                'maintenance_preventive',
                'reparation',
                'carburant',
                'assurance',
            ]),
            'budget_period' => ExpenseBudget::PERIOD_MONTHLY,
            'budget_year' => $year,
            'budget_month' => $month,
            'budget_quarter' => null,
            'budgeted_amount' => $this->faker->randomFloat(2, 50_000, 500_000),
            'spent_amount' => $this->faker->randomFloat(2, 0, 50_000),
            'warning_threshold' => 80.0,
            'critical_threshold' => 95.0,
            'description' => $this->faker->sentence(),
            'approval_workflow' => [],
            'is_active' => true,
        ];
    }
}
