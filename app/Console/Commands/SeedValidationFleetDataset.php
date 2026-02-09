<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\ExpenseBudget;
use App\Models\FuelType;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Organization;
use App\Models\TransmissionType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Models\VehicleMileageReading;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use App\Services\OrganizationRoleProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeedValidationFleetDataset extends Command
{
    protected $signature = 'zenfleet:seed-validation-dataset
        {--organization=ZenFleet Validation Lab}
        {--code=validation}
        {--vehicles=10}
        {--drivers=3}';

    protected $description = 'Seed a complete multi-tenant validation dataset (users, vehicles, drivers, assignments, expenses, maintenance, mileage, budgets).';

    private array $credentials = [];

    public function handle(OrganizationRoleProvisioner $roleProvisioner): int
    {
        $organization = null;

        DB::beginTransaction();

        try {
            $organization = $this->ensureOrganization();
            $roleReport = $roleProvisioner->ensureRolesForOrganization($organization);
            $refs = $this->ensureReferenceData($organization);
            $users = $this->ensureUsers($organization);
            $drivers = $this->ensureDrivers($organization, $users['driver_users'], $refs['driver_status_active']);
            $vehicles = $this->ensureVehicles($organization, $refs, (int) $this->option('vehicles'));

            $this->seedAssignments($organization, $vehicles, $drivers, $users['admin']);
            $this->seedMileageReadings($organization, $vehicles, $users['fleet_manager']);
            $this->seedVehicleExpenses($organization, $vehicles, $users['fleet_manager'], $users['admin']);
            $this->seedMaintenance($organization, $vehicles, $users['admin'], $refs['maintenance_types']);
            $this->seedBudgets($organization);
            $this->seedStatusHistory($organization, $vehicles, $drivers, $users['admin']);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            DB::commit();

            $this->renderSummary($organization, $roleReport);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error('Dataset seeding failed: ' . $e->getMessage());
            $this->line($e->getTraceAsString());

            if ($organization) {
                $this->warn("Organization impacted: #{$organization->id} {$organization->name}");
            }

            return self::FAILURE;
        }
    }

    private function ensureOrganization(): Organization
    {
        $name = (string) $this->option('organization');
        $code = Str::slug((string) $this->option('code'));

        $organization = Organization::firstOrCreate(
            ['name' => $name],
            [
                'uuid' => (string) Str::uuid(),
                'legal_name' => "{$name} SARL",
                'organization_type' => 'enterprise',
                'industry' => 'Transport & Logistique',
                'description' => 'Organisation de validation technique ZenFleet (dataset QA).',
                'status' => 'active',
                'trade_register' => '16/26-' . now()->format('y') . '0001 B 01',
                'nif' => '000016123456789',
                'ai' => '16123456789012',
                'nis' => '000016123456789',
                'address' => 'Zone Industrielle Oued Smar',
                'city' => 'Alger',
                'commune' => 'Oued Smar',
                'zip_code' => '16000',
                'wilaya' => '16',
                'phone_number' => '+213 21 90 00 00',
                'email' => "contact+{$code}@zenfleet.local",
                'manager_first_name' => 'Validation',
                'manager_last_name' => 'ZenFleet',
                'manager_nin' => '100000000000000001',
                'manager_address' => 'Alger',
                'manager_dob' => now()->subYears(40)->toDateString(),
                'manager_pob' => 'Alger',
                'manager_phone_number' => '+213 555 000 001',
            ]
        );

        if (blank($organization->uuid)) {
            $organization->uuid = (string) Str::uuid();
            $organization->save();
        }

        return $organization;
    }

    private function ensureReferenceData(Organization $organization): array
    {
        $vehicleType = VehicleType::firstOrCreate(['name' => 'SUV']);
        $fuelType = FuelType::firstOrCreate(['name' => 'Diesel']);
        $transmission = TransmissionType::firstOrCreate(['name' => 'Automatique']);

        $vehicleStatuses = [
            'parking' => VehicleStatus::updateOrCreate(
                ['name' => 'Parking'],
                [
                    'slug' => 'parking',
                    'organization_id' => null,
                    'description' => 'Véhicule stationné et disponible',
                    'color' => '#3b82f6',
                    'icon' => 'lucide:parking-circle',
                    'is_active' => true,
                    'sort_order' => 10,
                    'can_be_assigned' => true,
                    'is_operational' => true,
                    'requires_maintenance' => false,
                ]
            ),
            'assigned' => VehicleStatus::updateOrCreate(
                ['name' => 'Affecté'],
                [
                    'slug' => 'assigned',
                    'organization_id' => null,
                    'description' => 'Véhicule affecté à un chauffeur',
                    'color' => '#10b981',
                    'icon' => 'lucide:users',
                    'is_active' => true,
                    'sort_order' => 20,
                    'can_be_assigned' => false,
                    'is_operational' => true,
                    'requires_maintenance' => false,
                ]
            ),
            'maintenance' => VehicleStatus::updateOrCreate(
                ['name' => 'En maintenance'],
                [
                    'slug' => 'maintenance',
                    'organization_id' => null,
                    'description' => 'Véhicule en maintenance planifiée/corrective',
                    'color' => '#f59e0b',
                    'icon' => 'lucide:wrench',
                    'is_active' => true,
                    'sort_order' => 30,
                    'can_be_assigned' => false,
                    'is_operational' => false,
                    'requires_maintenance' => true,
                ]
            ),
            'breakdown' => VehicleStatus::updateOrCreate(
                ['name' => 'En panne'],
                [
                    'slug' => 'breakdown',
                    'organization_id' => null,
                    'description' => 'Véhicule indisponible suite à une panne',
                    'color' => '#ef4444',
                    'icon' => 'lucide:triangle-alert',
                    'is_active' => true,
                    'sort_order' => 40,
                    'can_be_assigned' => false,
                    'is_operational' => false,
                    'requires_maintenance' => true,
                ]
            ),
        ];

        $driverStatusActive = DriverStatus::firstOrCreate(
            ['slug' => 'active', 'organization_id' => null],
            [
                'name' => 'Actif',
                'description' => 'Chauffeur actif et disponible',
                'color' => '#10b981',
                'icon' => 'lucide:user-check',
                'is_active' => true,
                'sort_order' => 10,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
            ]
        );

        DriverStatus::firstOrCreate(
            ['slug' => 'training', 'organization_id' => null],
            [
                'name' => 'En formation',
                'description' => 'Chauffeur en formation',
                'color' => '#6366f1',
                'icon' => 'lucide:graduation-cap',
                'is_active' => true,
                'sort_order' => 20,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
            ]
        );

        DriverStatus::firstOrCreate(
            ['slug' => 'on-leave', 'organization_id' => null],
            [
                'name' => 'En congé',
                'description' => 'Chauffeur temporairement indisponible',
                'color' => '#9ca3af',
                'icon' => 'lucide:calendar-off',
                'is_active' => true,
                'sort_order' => 30,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
            ]
        );

        $maintenanceTypes = [
            'preventive' => MaintenanceType::firstOrCreate(
                ['organization_id' => $organization->id, 'name' => 'Vidange préventive'],
                [
                    'category' => MaintenanceType::CATEGORY_PREVENTIVE,
                    'description' => 'Entretien périodique huile et filtres',
                    'is_recurring' => true,
                    'default_interval_km' => 10000,
                    'default_interval_days' => 180,
                    'estimated_duration_minutes' => 120,
                    'estimated_cost' => 15000,
                    'is_active' => true,
                ]
            ),
            'corrective' => MaintenanceType::firstOrCreate(
                ['organization_id' => $organization->id, 'name' => 'Réparation corrective'],
                [
                    'category' => MaintenanceType::CATEGORY_CORRECTIVE,
                    'description' => 'Intervention corrective après panne',
                    'is_recurring' => false,
                    'default_interval_km' => null,
                    'default_interval_days' => null,
                    'estimated_duration_minutes' => 240,
                    'estimated_cost' => 50000,
                    'is_active' => true,
                ]
            ),
        ];

        return [
            'vehicle_type_id' => $vehicleType->id,
            'fuel_type_id' => $fuelType->id,
            'transmission_type_id' => $transmission->id,
            'vehicle_statuses' => $vehicleStatuses,
            'driver_status_active' => $driverStatusActive,
            'maintenance_types' => $maintenanceTypes,
        ];
    }

    private function ensureUsers(Organization $organization): array
    {
        $code = Str::slug((string) $this->option('code'));
        $domain = "{$code}.zenfleet.local";

        $admin = $this->upsertUserWithRole(
            $organization,
            'Admin',
            'Admin',
            "admin.{$code}@{$domain}",
            'Admin@123!'
        );

        $fleetManager = $this->upsertUserWithRole(
            $organization,
            'Gestionnaire Flotte',
            'Gestionnaire',
            "flotte.{$code}@{$domain}",
            'Flotte@123!'
        );

        $supervisor = $this->upsertUserWithRole(
            $organization,
            'Superviseur',
            'Superviseur',
            "superviseur.{$code}@{$domain}",
            'Superviseur@123!'
        );

        $driverUsers = [];
        $driverCount = max(1, (int) $this->option('drivers'));

        for ($i = 1; $i <= $driverCount; $i++) {
            $driverUsers[] = $this->upsertUserWithRole(
                $organization,
                'Chauffeur',
                "Chauffeur {$i}",
                "chauffeur{$i}.{$code}@{$domain}",
                "Chauffeur{$i}@123!"
            );
        }

        return [
            'admin' => $admin,
            'fleet_manager' => $fleetManager,
            'supervisor' => $supervisor,
            'driver_users' => collect($driverUsers),
        ];
    }

    private function upsertUserWithRole(
        Organization $organization,
        string $roleName,
        string $label,
        string $email,
        string $password
    ): User {
        $firstName = Str::of($label)->before(' ')->toString();
        $lastName = Str::of($label)->after(' ')->toString();
        $fullName = trim($firstName . ' ' . ($lastName ?: 'ZenFleet'));
        $phoneSeed = str_pad((string) (abs(crc32($email)) % 1000000), 6, '0', STR_PAD_LEFT);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName ?: 'ZenFleet',
                'phone' => '+213 555 ' . $phoneSeed,
                'password' => Hash::make($password),
                'organization_id' => $organization->id,
                'use_custom_permissions' => false,
            ]
        );

        $user->forceFill([
            'organization_id' => $organization->id,
            'status' => 'active',
            'is_active' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->attachRoleToUserInOrganization($user, $roleName, $organization->id);
        $this->credentials[$roleName . " ({$label})"] = ['email' => $email, 'password' => $password];

        return $user;
    }

    private function attachRoleToUserInOrganization(User $user, string $roleName, int $organizationId): void
    {
        $role = Role::query()
            ->where('name', $roleName)
            ->where('organization_id', $organizationId)
            ->first();

        if (!$role) {
            throw new \RuntimeException("Role '{$roleName}' not provisioned for organization #{$organizationId}.");
        }

        DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->where('organization_id', $organizationId)
            ->delete();

        DB::table('model_has_roles')->updateOrInsert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'organization_id' => $organizationId,
        ], []);
    }

    private function ensureDrivers(Organization $organization, Collection $driverUsers, DriverStatus $activeStatus): Collection
    {
        $drivers = collect();

        foreach ($driverUsers->values() as $index => $user) {
            $sequence = $index + 1;
            $license = sprintf('VAL-%02d-DRV-%03d', $organization->id, $sequence);

            $driver = Driver::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'organization_id' => $organization->id,
                    'first_name' => $user->first_name ?: "Driver{$sequence}",
                    'last_name' => $user->last_name ?: 'ZenFleet',
                    'personal_email' => $user->email,
                    'personal_phone' => $user->phone ?: '+213 555 000 000',
                    'license_number' => $license,
                    'license_issue_date' => now()->subYears(3)->toDateString(),
                    'license_expiry_date' => now()->addYears(10)->subDay()->toDateString(),
                    'license_authority' => 'Daira Alger Centre',
                    'license_verified' => true,
                    'employee_number' => sprintf('DRV-%03d', $sequence),
                    'birth_date' => now()->subYears(30 + $sequence)->toDateString(),
                    'recruitment_date' => now()->subMonths(6 + $sequence)->toDateString(),
                    'status_id' => $activeStatus->id,
                    'notes' => 'Profil chauffeur de validation QA.',
                    'is_available' => true,
                    'assignment_status' => 'available',
                ]
            );

            $drivers->push($driver);
        }

        return $drivers;
    }

    private function ensureVehicles(Organization $organization, array $refs, int $vehicleCount): Collection
    {
        $vehicles = collect();
        $statuses = $refs['vehicle_statuses'];

        for ($i = 1; $i <= max(1, $vehicleCount); $i++) {
            $plate = sprintf('VAL-%02d-%03d', $organization->id, $i);

            $status = match (true) {
                $i <= 3 => $statuses['assigned'],
                $i <= 5 => $statuses['maintenance'],
                $i === 6 => $statuses['breakdown'],
                default => $statuses['parking'],
            };

            $initialMileage = 12000 + ($i * 2100);
            $currentMileage = $initialMileage + random_int(5000, 40000);

            $vehicle = Vehicle::withoutGlobalScopes()->updateOrCreate(
                ['registration_plate' => $plate],
                [
                    'organization_id' => $organization->id,
                    'vehicle_name' => "Validation Vehicle {$i}",
                    'vin' => sprintf('VAL%02d%012d', $organization->id, $i),
                    'brand' => ['Renault', 'Peugeot', 'Toyota', 'Hyundai', 'Iveco'][($i - 1) % 5],
                    'model' => ['Kangoo', '208', 'Hilux', 'H1', 'Daily'][($i - 1) % 5],
                    'color' => ['Blanc', 'Gris', 'Noir', 'Bleu', 'Argent'][($i - 1) % 5],
                    'vehicle_type_id' => $refs['vehicle_type_id'],
                    'fuel_type_id' => $refs['fuel_type_id'],
                    'transmission_type_id' => $refs['transmission_type_id'],
                    'status_id' => $status->id,
                    'manufacturing_year' => now()->year - (($i % 5) + 1),
                    'acquisition_date' => now()->subYears(($i % 4) + 1)->toDateString(),
                    'purchase_price' => 2800000 + ($i * 95000),
                    'current_value' => 1800000 + ($i * 65000),
                    'initial_mileage' => $initialMileage,
                    'current_mileage' => $currentMileage,
                    'engine_displacement_cc' => 1600 + ($i * 100),
                    'power_hp' => 90 + ($i * 5),
                    'seats' => $i % 4 === 0 ? 2 : 5,
                    'notes' => 'Véhicule seedé pour la validation inter-modules.',
                    'is_available' => $status->slug === 'parking',
                    'assignment_status' => $status->slug === 'assigned' ? 'assigned' : 'available',
                ]
            );

            $vehicles->push($vehicle);
        }

        return $vehicles;
    }

    private function seedAssignments(
        Organization $organization,
        Collection $vehicles,
        Collection $drivers,
        User $admin
    ): void {
        $startBase = now()->startOfDay()->subDays(7);

        foreach ($drivers->values() as $idx => $driver) {
            $vehicle = $vehicles->get($idx);

            if (!$vehicle) {
                continue;
            }

            Assignment::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'start_datetime' => $startBase->copy()->addHours($idx)->toDateTimeString(),
                ],
                [
                    'end_datetime' => null,
                    'start_mileage' => $vehicle->initial_mileage + 1000,
                    'end_mileage' => null,
                    'reason' => 'Affectation opérationnelle continue',
                    'notes' => 'Dataset de validation',
                    'status' => Assignment::STATUS_ACTIVE,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );

            $vehicle->forceFill([
                'current_driver_id' => $driver->id,
                'is_available' => false,
                'assignment_status' => 'assigned',
                'status_id' => VehicleStatus::where('slug', 'assigned')->value('id') ?? $vehicle->status_id,
            ])->save();

            $driver->forceFill([
                'is_available' => false,
                'current_vehicle_id' => $vehicle->id,
                'assignment_status' => 'assigned',
                'last_assignment_end' => null,
            ])->save();
        }

        if ($drivers->isNotEmpty() && $vehicles->count() > $drivers->count()) {
            $completedVehicle = $vehicles->get($drivers->count());
            $driver = $drivers->first();

            Assignment::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'vehicle_id' => $completedVehicle->id,
                    'driver_id' => $driver->id,
                    'start_datetime' => now()->subDays(35)->startOfDay()->toDateTimeString(),
                ],
                [
                    'end_datetime' => now()->subDays(30)->endOfDay()->toDateTimeString(),
                    'start_mileage' => $completedVehicle->initial_mileage + 4000,
                    'end_mileage' => $completedVehicle->initial_mileage + 4800,
                    'reason' => 'Mission ponctuelle terminée',
                    'notes' => 'Historique de validation',
                    'status' => Assignment::STATUS_COMPLETED,
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                    'ended_by_user_id' => $admin->id,
                    'ended_at' => now()->subDays(30)->endOfDay()->toDateTimeString(),
                ]
            );
        }
    }

    private function seedMileageReadings(Organization $organization, Collection $vehicles, User $fleetManager): void
    {
        foreach ($vehicles->values() as $idx => $vehicle) {
            $base = $vehicle->initial_mileage;
            $dates = [
                now()->subDays(30)->startOfDay(),
                now()->subDays(15)->startOfDay(),
                now()->subDays(1)->startOfDay(),
            ];
            $increments = [2000 + ($idx * 120), 4500 + ($idx * 150), 7000 + ($idx * 180)];

            foreach ($dates as $step => $date) {
                DB::table('vehicle_mileage_readings')->updateOrInsert(
                    [
                        'organization_id' => $organization->id,
                        'vehicle_id' => $vehicle->id,
                        'recorded_at' => $date->toDateTimeString(),
                    ],
                    [
                        'mileage' => $base + $increments[$step],
                        'recorded_by_id' => $fleetManager->id,
                        'recording_method' => VehicleMileageReading::METHOD_MANUAL,
                        'notes' => 'Lecture de validation dashboard.',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            $vehicle->update([
                'current_mileage' => $base + $increments[2],
            ]);
        }
    }

    private function seedVehicleExpenses(
        Organization $organization,
        Collection $vehicles,
        User $fleetManager,
        User $admin
    ): void {
        foreach ($vehicles->values() as $idx => $vehicle) {
            $expenseDateA = now()->subDays(25 - min($idx, 20))->toDateString();
            $expenseDateB = now()->subDays(8 - ($idx % 5))->toDateString();

            $this->upsertExpense(
                $organization->id,
                $vehicle->id,
                VehicleExpense::CATEGORY_CARBURANT,
                'Fuel',
                7800 + ($idx * 350),
                $expenseDateA,
                'Approvisionnement carburant'
            , $fleetManager->id, $admin->id);

            $this->upsertExpense(
                $organization->id,
                $vehicle->id,
                $idx % 2 === 0
                    ? VehicleExpense::CATEGORY_MAINTENANCE_PREVENTIVE
                    : VehicleExpense::CATEGORY_REPARATION,
                'Maintenance',
                22000 + ($idx * 1200),
                $expenseDateB,
                'Intervention maintenance atelier'
            , $fleetManager->id, $admin->id);
        }
    }

    private function upsertExpense(
        int $organizationId,
        int $vehicleId,
        string $category,
        string $type,
        float $amountHt,
        string $expenseDate,
        string $description,
        int $recordedBy,
        int $approvedBy
    ): void {
        $tvaRate = 19.0;
        VehicleExpense::updateOrCreate(
            [
                'organization_id' => $organizationId,
                'vehicle_id' => $vehicleId,
                'expense_category' => $category,
                'expense_date' => $expenseDate,
                'description' => $description,
            ],
            [
                'expense_type' => $type,
                'amount_ht' => $amountHt,
                'tva_rate' => $tvaRate,
                'recorded_by' => $recordedBy,
                'requester_id' => $recordedBy,
                'approved' => true,
                'approved_by' => $approvedBy,
                'approved_at' => now(),
                'approval_status' => VehicleExpense::APPROVAL_APPROVED,
                'payment_status' => VehicleExpense::PAYMENT_PAID,
                'payment_method' => VehicleExpense::PAYMENT_VIREMENT,
                'payment_date' => $expenseDate,
                'needs_approval' => false,
                'is_urgent' => false,
                'cost_center' => 'fleet-ops',
                'internal_notes' => 'Donnée de validation analytique',
            ]
        );
    }

    private function seedMaintenance(
        Organization $organization,
        Collection $vehicles,
        User $admin,
        array $maintenanceTypes
    ): void {
        $vehicleList = $vehicles->values();

        if ($vehicleList->isEmpty()) {
            return;
        }

        $operations = [
            [
                'vehicle' => $vehicleList->get(0),
                'type' => $maintenanceTypes['preventive'],
                'status' => MaintenanceOperation::STATUS_COMPLETED,
                'scheduled_date' => now()->subDays(20)->toDateString(),
                'completed_date' => now()->subDays(19)->toDateString(),
                'total_cost' => 18500,
            ],
            [
                'vehicle' => $vehicleList->get(1),
                'type' => $maintenanceTypes['corrective'],
                'status' => MaintenanceOperation::STATUS_IN_PROGRESS,
                'scheduled_date' => now()->subDays(2)->toDateString(),
                'completed_date' => null,
                'total_cost' => 42000,
            ],
            [
                'vehicle' => $vehicleList->get(2),
                'type' => $maintenanceTypes['preventive'],
                'status' => MaintenanceOperation::STATUS_PLANNED,
                'scheduled_date' => now()->addDays(5)->toDateString(),
                'completed_date' => null,
                'total_cost' => 16000,
            ],
        ];

        foreach ($operations as $op) {
            if (!$op['vehicle']) {
                continue;
            }

            MaintenanceOperation::updateOrCreate(
                [
                    'organization_id' => $organization->id,
                    'vehicle_id' => $op['vehicle']->id,
                    'maintenance_type_id' => $op['type']->id,
                    'scheduled_date' => $op['scheduled_date'],
                ],
                [
                    'status' => $op['status'],
                    'completed_date' => $op['completed_date'],
                    'mileage_at_maintenance' => $op['vehicle']->current_mileage,
                    'duration_minutes' => $op['status'] === MaintenanceOperation::STATUS_COMPLETED ? 150 : null,
                    'total_cost' => $op['total_cost'],
                    'description' => 'Opération de maintenance seedée pour validation',
                    'notes' => 'Dataset QA multi-modules',
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );
        }
    }

    private function seedBudgets(Organization $organization): void
    {
        $year = (int) now()->format('Y');
        $month = (int) now()->format('m');

        $categories = [
            VehicleExpense::CATEGORY_CARBURANT,
            VehicleExpense::CATEGORY_MAINTENANCE_PREVENTIVE,
            VehicleExpense::CATEGORY_REPARATION,
        ];

        foreach ($categories as $category) {
            $spent = (float) VehicleExpense::query()
                ->where('organization_id', $organization->id)
                ->where('expense_category', $category)
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('total_ttc');

            $budgeted = max(50000.0, round($spent * 1.25, 2));

            DB::table('expense_budgets')->updateOrInsert(
                [
                    'organization_id' => $organization->id,
                    'vehicle_id' => null,
                    'expense_category' => $category,
                    'budget_period' => ExpenseBudget::PERIOD_MONTHLY,
                    'budget_year' => $year,
                    'budget_month' => $month,
                ],
                [
                    'budget_quarter' => null,
                    'budgeted_amount' => $budgeted,
                    'spent_amount' => round($spent, 2),
                    'warning_threshold' => 80,
                    'critical_threshold' => 95,
                    'scope_type' => 'category',
                    'scope_description' => "Budget {$category}",
                    'description' => "Budget mensuel de validation pour {$category}",
                    'approval_workflow' => json_encode([]),
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function seedStatusHistory(
        Organization $organization,
        Collection $vehicles,
        Collection $drivers,
        User $admin
    ): void {
        foreach ($vehicles->take(4)->values() as $idx => $vehicle) {
            DB::table('status_history')->updateOrInsert(
                [
                    'statusable_type' => Vehicle::class,
                    'statusable_id' => $vehicle->id,
                    'changed_at' => now()->subDays(10 - $idx)->toDateTimeString(),
                    'to_status' => ['parking', 'assigned', 'maintenance', 'breakdown'][$idx],
                ],
                [
                    'from_status' => $idx === 0 ? null : ['parking', 'assigned', 'maintenance', 'breakdown'][$idx - 1],
                    'reason' => 'Historique seedé pour analytics statut',
                    'metadata' => json_encode(['source' => 'seed-validation-dataset']),
                    'changed_by_user_id' => $admin->id,
                    'change_type' => 'manual',
                    'organization_id' => $organization->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        foreach ($drivers->values() as $idx => $driver) {
            DB::table('status_history')->updateOrInsert(
                [
                    'statusable_type' => Driver::class,
                    'statusable_id' => $driver->id,
                    'changed_at' => now()->subDays(6 - $idx)->toDateTimeString(),
                    'to_status' => 'active',
                ],
                [
                    'from_status' => null,
                    'reason' => 'Activation chauffeur dataset QA',
                    'metadata' => json_encode(['source' => 'seed-validation-dataset']),
                    'changed_by_user_id' => $admin->id,
                    'change_type' => 'manual',
                    'organization_id' => $organization->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function renderSummary(Organization $organization, array $roleReport): void
    {
        $this->newLine();
        $this->info('Validation dataset created successfully.');
        $this->line("Organization: #{$organization->id} {$organization->name}");
        $this->line("Role provisioning: created={$roleReport['created']} synced={$roleReport['synced']}");

        $usersCount = DB::table('users')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();
        $vehiclesCount = DB::table('vehicles')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();
        $driversCount = DB::table('drivers')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();
        $activeAssignments = DB::table('assignments')
            ->where('organization_id', $organization->id)
            ->whereNull('deleted_at')
            ->whereNull('end_datetime')
            ->count();
        $expensesCount = DB::table('vehicle_expenses')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();
        $maintenanceCount = DB::table('maintenance_operations')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();
        $mileageCount = DB::table('vehicle_mileage_readings')->where('organization_id', $organization->id)->count();
        $budgetsCount = DB::table('expense_budgets')->where('organization_id', $organization->id)->whereNull('deleted_at')->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Users', (string) $usersCount],
                ['Vehicles', (string) $vehiclesCount],
                ['Drivers', (string) $driversCount],
                ['Active assignments', (string) $activeAssignments],
                ['Vehicle expenses', (string) $expensesCount],
                ['Maintenance operations', (string) $maintenanceCount],
                ['Mileage readings', (string) $mileageCount],
                ['Expense budgets', (string) $budgetsCount],
            ]
        );

        $this->newLine();
        $this->info('Test accounts (for this organization):');
        foreach ($this->credentials as $role => $data) {
            $this->line("- {$role}: {$data['email']} / {$data['password']}");
        }
    }
}
