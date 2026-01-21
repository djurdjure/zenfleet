<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('-1 month', '+1 month'));
        $end = (clone $start)->addHours($this->faker->numberBetween(4, 12));
        $startMileage = $this->faker->numberBetween(1_000, 50_000);
        $endMileage = $startMileage + $this->faker->numberBetween(0, 500);

        return [
            'organization_id' => Organization::factory(),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'start_datetime' => $start,
            'end_datetime' => $end,
            'start_mileage' => $startMileage,
            'end_mileage' => $endMileage,
            'reason' => $this->faker->sentence(),
            'notes' => $this->faker->sentence(),
            'status' => Assignment::STATUS_SCHEDULED,
        ];
    }

    public function ongoing(): static
    {
        return $this->state([
            'end_datetime' => null,
            'status' => Assignment::STATUS_ACTIVE,
        ]);
    }
}
