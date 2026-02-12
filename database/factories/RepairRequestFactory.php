<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Organization;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RepairRequest>
 */
class RepairRequestFactory extends Factory
{
    protected $model = RepairRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organization = Organization::factory()->create();
        $driver = Driver::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $organization->id,
        ]);

        return [
            'uuid' => (string) Str::uuid(),
            'organization_id' => $organization->id,
            'driver_id' => $driver->id,
            'requested_by' => $driver->user_id,
            'vehicle_id' => $vehicle->id,
            'title' => $this->faker->randomElement([
                'Flat tire on route',
                'Brake system issue',
                'Oil leak detected',
                'Battery failure',
                'Headlight malfunction',
                'Engine warning light',
            ]),
            'description' => $this->faker->paragraph(3),
            'urgency' => $this->faker->randomElement([
                RepairRequest::URGENCY_LOW,
                RepairRequest::URGENCY_NORMAL,
                RepairRequest::URGENCY_HIGH,
                RepairRequest::URGENCY_CRITICAL,
            ]),
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
            'current_mileage' => $this->faker->numberBetween(10000, 200000),
            'current_location' => $this->faker->city(),
            'estimated_cost' => $this->faker->optional()->randomFloat(2, 500, 50000),
            'photos' => null,
            'attachments' => null,
            'category_id' => null,
        ];
    }

    /**
     * State: Pending supervisor approval.
     */
    public function pendingSupervisor(): static
    {
        return $this->state(fn () => [
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
            'supervisor_id' => null,
            'supervisor_status' => null,
            'supervisor_approved_at' => null,
        ]);
    }

    /**
     * State: Pending fleet manager approval.
     */
    public function pendingFleetManager(): static
    {
        return $this->state(function (array $attributes) {
            $supervisor = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);

            return [
                'status' => RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now(),
                'supervisor_comment' => $this->faker->sentence(),
            ];
        });
    }

    /**
     * State: Approved final.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $supervisor = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);
            $fleetManager = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);

            return [
                'status' => RepairRequest::STATUS_APPROVED_FINAL,
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now()->subDays(2),
                'supervisor_comment' => $this->faker->sentence(),
                'fleet_manager_id' => $fleetManager->id,
                'fleet_manager_status' => 'approved',
                'fleet_manager_approved_at' => now()->subDay(),
                'fleet_manager_comment' => $this->faker->sentence(),
                'final_approved_by' => $fleetManager->id,
                'final_approved_at' => now()->subDay(),
            ];
        });
    }

    /**
     * State: Rejected by supervisor.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            $supervisor = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);

            return [
                'status' => RepairRequest::STATUS_REJECTED_SUPERVISOR,
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'rejected',
                'supervisor_comment' => $this->faker->sentence(),
                'rejection_reason' => $this->faker->paragraph(),
                'rejected_by' => $supervisor->id,
                'rejected_at' => now(),
            ];
        });
    }

    /**
     * State: Rejected by fleet manager.
     */
    public function rejectedFinal(): static
    {
        return $this->state(function (array $attributes) {
            $supervisor = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);
            $fleetManager = User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ]);

            return [
                'status' => RepairRequest::STATUS_REJECTED_FINAL,
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'approved',
                'supervisor_approved_at' => now()->subDays(2),
                'fleet_manager_id' => $fleetManager->id,
                'fleet_manager_status' => 'rejected',
                'fleet_manager_comment' => $this->faker->sentence(),
                'rejection_reason' => $this->faker->paragraph(),
                'rejected_by' => $fleetManager->id,
                'rejected_at' => now(),
            ];
        });
    }

    /**
     * State: Critical urgency.
     */
    public function critical(): static
    {
        return $this->state(fn () => [
            'urgency' => RepairRequest::URGENCY_CRITICAL,
        ]);
    }

    /**
     * State: Low urgency.
     */
    public function lowUrgency(): static
    {
        return $this->state(fn () => [
            'urgency' => RepairRequest::URGENCY_LOW,
        ]);
    }

    /**
     * State: With sample photos metadata.
     */
    public function withPhotos(): static
    {
        return $this->state(fn () => [
            'photos' => [
                [
                    'path' => 'repair-requests/photo1.jpg',
                    'original_name' => 'photo1.jpg',
                    'size' => 123456,
                    'mime_type' => 'image/jpeg',
                    'uploaded_at' => now()->toISOString(),
                ],
                [
                    'path' => 'repair-requests/photo2.jpg',
                    'original_name' => 'photo2.jpg',
                    'size' => 234567,
                    'mime_type' => 'image/jpeg',
                    'uploaded_at' => now()->toISOString(),
                ],
            ],
        ]);
    }

    /**
     * State: For a specific organization.
     */
    public function forOrganization(int $organizationId): static
    {
        return $this->state(function () use ($organizationId) {
            $driver = Driver::factory()->create([
                'organization_id' => $organizationId,
            ]);
            $vehicle = Vehicle::factory()->create([
                'organization_id' => $organizationId,
            ]);

            return [
                'organization_id' => $organizationId,
                'driver_id' => $driver->id,
                'requested_by' => $driver->user_id,
                'vehicle_id' => $vehicle->id,
            ];
        });
    }
}
