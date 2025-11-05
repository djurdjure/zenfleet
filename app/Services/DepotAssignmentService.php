<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleDepot;
use App\Models\DepotAssignmentHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;

/**
 * DepotAssignmentService
 *
 * Enterprise-grade service for managing vehicle-to-depot assignments
 * with complete audit trail, capacity management, and business logic validation.
 *
 * Features:
 * - Atomic transactions for data consistency
 * - Capacity validation before assignment
 * - Complete audit trail in depot_assignment_history
 * - Automatic counter management (vehicle_depots.current_count)
 * - Multi-tenant organization isolation
 * - Comprehensive error handling and logging
 *
 * @package App\Services
 * @version 1.0
 * @since 2025-11-04
 */
class DepotAssignmentService
{
    /**
     * Assign a vehicle to a depot
     *
     * Business rules:
     * 1. Depot must have available capacity
     * 2. Vehicle must not already be assigned to this depot
     * 3. Vehicle and depot must belong to same organization
     * 4. Previous depot count is decremented
     * 5. New depot count is incremented
     * 6. History record is created
     *
     * @param Vehicle $vehicle The vehicle to assign
     * @param VehicleDepot $depot The target depot
     * @param User $user The user performing the action
     * @param string|null $notes Optional notes/reason for assignment
     * @return DepotAssignmentHistory The created history record
     * @throws InvalidArgumentException If validation fails
     * @throws RuntimeException If operation fails
     */
    public function assignVehicleToDepot(
        Vehicle $vehicle,
        VehicleDepot $depot,
        User $user,
        ?string $notes = null
    ): DepotAssignmentHistory {
        // Validate organization match
        if ($vehicle->organization_id !== $depot->organization_id) {
            throw new InvalidArgumentException(
                "Vehicle and depot must belong to the same organization. " .
                "Vehicle org: {$vehicle->organization_id}, Depot org: {$depot->organization_id}"
            );
        }

        if ($vehicle->organization_id !== $user->organization_id) {
            throw new InvalidArgumentException(
                "User must belong to the same organization as vehicle. " .
                "User org: {$user->organization_id}, Vehicle org: {$vehicle->organization_id}"
            );
        }

        // Check if already assigned to this depot
        if ($vehicle->depot_id === $depot->id) {
            throw new InvalidArgumentException(
                "Vehicle {$vehicle->registration_plate} is already assigned to depot {$depot->name}"
            );
        }

        // Check depot capacity
        if (!$depot->hasAvailableSpace()) {
            throw new RuntimeException(
                "Depot {$depot->name} is at full capacity ({$depot->current_count}/{$depot->capacity}). " .
                "Cannot assign vehicle {$vehicle->registration_plate}."
            );
        }

        DB::beginTransaction();

        try {
            $previousDepotId = $vehicle->depot_id;

            // Decrement previous depot count if exists
            if ($previousDepotId) {
                $previousDepot = VehicleDepot::find($previousDepotId);
                if ($previousDepot) {
                    $previousDepot->decrementCount();
                    Log::info('Depot count decremented', [
                        'depot_id' => $previousDepot->id,
                        'depot_name' => $previousDepot->name,
                        'new_count' => $previousDepot->current_count,
                        'vehicle_id' => $vehicle->id
                    ]);
                }
            }

            // Update vehicle depot assignment
            $vehicle->depot_id = $depot->id;
            $vehicle->save();

            // Increment new depot count
            $depot->incrementCount();
            Log::info('Depot count incremented', [
                'depot_id' => $depot->id,
                'depot_name' => $depot->name,
                'new_count' => $depot->current_count,
                'vehicle_id' => $vehicle->id
            ]);

            // Create history record
            $historyRecord = DepotAssignmentHistory::create([
                'vehicle_id' => $vehicle->id,
                'depot_id' => $depot->id,
                'organization_id' => $vehicle->organization_id,
                'previous_depot_id' => $previousDepotId,
                'action' => $previousDepotId
                    ? DepotAssignmentHistory::ACTION_TRANSFERRED
                    : DepotAssignmentHistory::ACTION_ASSIGNED,
                'assigned_by' => $user->id,
                'notes' => $notes,
                'assigned_at' => now(),
            ]);

            DB::commit();

            Log::info('Vehicle assigned to depot successfully', [
                'vehicle_id' => $vehicle->id,
                'vehicle_plate' => $vehicle->registration_plate,
                'depot_id' => $depot->id,
                'depot_name' => $depot->name,
                'previous_depot_id' => $previousDepotId,
                'action' => $historyRecord->action,
                'user_id' => $user->id,
                'history_id' => $historyRecord->id
            ]);

            return $historyRecord;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to assign vehicle to depot', [
                'vehicle_id' => $vehicle->id,
                'depot_id' => $depot->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new RuntimeException(
                "Failed to assign vehicle to depot: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Unassign a vehicle from its current depot
     *
     * Business rules:
     * 1. Vehicle must be currently assigned to a depot
     * 2. Previous depot count is decremented
     * 3. Vehicle depot_id is set to null
     * 4. History record is created with unassigned action
     *
     * @param Vehicle $vehicle The vehicle to unassign
     * @param User $user The user performing the action
     * @param string|null $notes Optional notes/reason for unassignment
     * @return DepotAssignmentHistory The created history record
     * @throws InvalidArgumentException If validation fails
     * @throws RuntimeException If operation fails
     */
    public function unassignVehicleFromDepot(
        Vehicle $vehicle,
        User $user,
        ?string $notes = null
    ): DepotAssignmentHistory {
        // Validate user organization
        if ($vehicle->organization_id !== $user->organization_id) {
            throw new InvalidArgumentException(
                "User must belong to the same organization as vehicle"
            );
        }

        // Check if vehicle is assigned
        if (!$vehicle->depot_id) {
            throw new InvalidArgumentException(
                "Vehicle {$vehicle->registration_plate} is not currently assigned to any depot"
            );
        }

        DB::beginTransaction();

        try {
            $previousDepotId = $vehicle->depot_id;
            $previousDepot = VehicleDepot::find($previousDepotId);

            // Decrement previous depot count
            if ($previousDepot) {
                $previousDepot->decrementCount();
                Log::info('Depot count decremented on unassignment', [
                    'depot_id' => $previousDepot->id,
                    'depot_name' => $previousDepot->name,
                    'new_count' => $previousDepot->current_count,
                    'vehicle_id' => $vehicle->id
                ]);
            }

            // Remove vehicle depot assignment
            $vehicle->depot_id = null;
            $vehicle->save();

            // Create history record
            $historyRecord = DepotAssignmentHistory::create([
                'vehicle_id' => $vehicle->id,
                'depot_id' => null,
                'organization_id' => $vehicle->organization_id,
                'previous_depot_id' => $previousDepotId,
                'action' => DepotAssignmentHistory::ACTION_UNASSIGNED,
                'assigned_by' => $user->id,
                'notes' => $notes,
                'assigned_at' => now(),
            ]);

            DB::commit();

            Log::info('Vehicle unassigned from depot successfully', [
                'vehicle_id' => $vehicle->id,
                'vehicle_plate' => $vehicle->registration_plate,
                'previous_depot_id' => $previousDepotId,
                'user_id' => $user->id,
                'history_id' => $historyRecord->id
            ]);

            return $historyRecord;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to unassign vehicle from depot', [
                'vehicle_id' => $vehicle->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new RuntimeException(
                "Failed to unassign vehicle from depot: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Transfer a vehicle from current depot to target depot
     *
     * This is a convenience method that internally calls assignVehicleToDepot
     * with the transferred action. The logic is identical but provides clearer
     * semantics for direct transfer operations.
     *
     * @param Vehicle $vehicle The vehicle to transfer
     * @param VehicleDepot $targetDepot The destination depot
     * @param User $user The user performing the action
     * @param string|null $notes Optional notes/reason for transfer
     * @return DepotAssignmentHistory The created history record
     * @throws InvalidArgumentException If validation fails
     * @throws RuntimeException If operation fails
     */
    public function transferVehicle(
        Vehicle $vehicle,
        VehicleDepot $targetDepot,
        User $user,
        ?string $notes = null
    ): DepotAssignmentHistory {
        // Validate vehicle is currently assigned
        if (!$vehicle->depot_id) {
            throw new InvalidArgumentException(
                "Vehicle {$vehicle->registration_plate} is not currently assigned to any depot. " .
                "Use assignVehicleToDepot() instead for initial assignment."
            );
        }

        // Validate not transferring to same depot
        if ($vehicle->depot_id === $targetDepot->id) {
            throw new InvalidArgumentException(
                "Vehicle {$vehicle->registration_plate} is already at depot {$targetDepot->name}"
            );
        }

        Log::info('Initiating vehicle transfer', [
            'vehicle_id' => $vehicle->id,
            'vehicle_plate' => $vehicle->registration_plate,
            'from_depot_id' => $vehicle->depot_id,
            'to_depot_id' => $targetDepot->id,
            'user_id' => $user->id
        ]);

        // Use assignVehicleToDepot which handles all the logic
        // It will automatically detect this is a transfer (previous_depot_id exists)
        return $this->assignVehicleToDepot($vehicle, $targetDepot, $user, $notes);
    }

    /**
     * Bulk assign multiple vehicles to a depot
     *
     * This method handles batch assignment of multiple vehicles to a single depot
     * with comprehensive validation, capacity checking, and atomic transactions.
     *
     * Business rules:
     * 1. All vehicles must exist and belong to same organization
     * 2. Depot must have sufficient capacity for ALL vehicles
     * 3. Pre-validates ALL vehicles before starting assignment
     * 4. Uses atomic transaction - all succeed or all fail
     * 5. Skips vehicles already assigned to target depot
     * 6. Creates individual history records for each assignment
     * 7. Returns detailed result with success/failure breakdown
     *
     * @param array $vehicleIds Array of vehicle IDs to assign
     * @param VehicleDepot $depot The target depot
     * @param User $user The user performing the action
     * @param string|null $notes Optional notes/reason for bulk assignment
     * @return array Result with success/failure counts and details
     * @throws InvalidArgumentException If validation fails
     * @throws RuntimeException If operation fails
     */
    public function bulkAssignVehiclesToDepot(
        array $vehicleIds,
        VehicleDepot $depot,
        User $user,
        ?string $notes = null
    ): array {
        // Validate input
        if (empty($vehicleIds)) {
            throw new InvalidArgumentException('No vehicles provided for bulk assignment');
        }

        // Validate user organization
        if ($depot->organization_id !== $user->organization_id) {
            throw new InvalidArgumentException(
                "User must belong to the same organization as depot. " .
                "User org: {$user->organization_id}, Depot org: {$depot->organization_id}"
            );
        }

        // Fetch all vehicles with organization filter
        $vehicles = Vehicle::whereIn('id', $vehicleIds)
            ->where('organization_id', $user->organization_id)
            ->get();

        // Validate all vehicles were found
        if ($vehicles->count() !== count($vehicleIds)) {
            $foundIds = $vehicles->pluck('id')->toArray();
            $missingIds = array_diff($vehicleIds, $foundIds);
            throw new InvalidArgumentException(
                "Some vehicles not found or don't belong to your organization: " .
                implode(', ', $missingIds)
            );
        }

        // PRE-VALIDATION: Check all vehicles before starting transaction
        $validationErrors = [];
        $vehiclesToAssign = collect();

        foreach ($vehicles as $vehicle) {
            // Skip vehicles already assigned to this depot
            if ($vehicle->depot_id === $depot->id) {
                Log::info('Vehicle already assigned to target depot, skipping', [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_plate' => $vehicle->registration_plate,
                    'depot_id' => $depot->id
                ]);
                continue;
            }

            // Validate organization match
            if ($vehicle->organization_id !== $depot->organization_id) {
                $validationErrors[] = [
                    'vehicle_id' => $vehicle->id,
                    'vehicle_plate' => $vehicle->registration_plate,
                    'error' => 'Vehicle and depot belong to different organizations'
                ];
                continue;
            }

            $vehiclesToAssign->push($vehicle);
        }

        // Check if depot has capacity for all vehicles to assign
        $requiredCapacity = $vehiclesToAssign->count();
        $availableCapacity = $depot->availableCapacity;

        if ($requiredCapacity > $availableCapacity) {
            throw new RuntimeException(
                "Depot {$depot->name} has insufficient capacity. " .
                "Required: {$requiredCapacity}, Available: {$availableCapacity} " .
                "(Current: {$depot->current_count}/{$depot->capacity})"
            );
        }

        // If no vehicles to assign after filtering
        if ($vehiclesToAssign->isEmpty()) {
            return [
                'success' => true,
                'total_requested' => count($vehicleIds),
                'assigned' => 0,
                'skipped' => count($vehicleIds) - count($validationErrors),
                'failed' => count($validationErrors),
                'errors' => $validationErrors,
                'message' => 'No vehicles required assignment (all already assigned or invalid)'
            ];
        }

        // START ATOMIC TRANSACTION
        DB::beginTransaction();

        try {
            $successCount = 0;
            $failedCount = 0;
            $historyRecords = [];

            foreach ($vehiclesToAssign as $vehicle) {
                try {
                    $previousDepotId = $vehicle->depot_id;

                    // Decrement previous depot count if exists
                    if ($previousDepotId) {
                        $previousDepot = VehicleDepot::find($previousDepotId);
                        if ($previousDepot) {
                            $previousDepot->decrementCount();
                        }
                    }

                    // Update vehicle depot assignment
                    $vehicle->depot_id = $depot->id;
                    $vehicle->save();

                    // Increment new depot count
                    $depot->incrementCount();

                    // Create history record
                    $historyRecord = DepotAssignmentHistory::create([
                        'vehicle_id' => $vehicle->id,
                        'depot_id' => $depot->id,
                        'organization_id' => $vehicle->organization_id,
                        'previous_depot_id' => $previousDepotId,
                        'action' => $previousDepotId
                            ? DepotAssignmentHistory::ACTION_TRANSFERRED
                            : DepotAssignmentHistory::ACTION_ASSIGNED,
                        'assigned_by' => $user->id,
                        'notes' => $notes ? "BULK: {$notes}" : 'BULK: Affectation par lot',
                        'assigned_at' => now(),
                    ]);

                    $historyRecords[] = $historyRecord;
                    $successCount++;

                    Log::info('Vehicle bulk assigned successfully', [
                        'vehicle_id' => $vehicle->id,
                        'vehicle_plate' => $vehicle->registration_plate,
                        'depot_id' => $depot->id,
                        'previous_depot_id' => $previousDepotId
                    ]);

                } catch (\Exception $e) {
                    $failedCount++;
                    $validationErrors[] = [
                        'vehicle_id' => $vehicle->id,
                        'vehicle_plate' => $vehicle->registration_plate,
                        'error' => $e->getMessage()
                    ];

                    Log::error('Failed to assign vehicle in bulk operation', [
                        'vehicle_id' => $vehicle->id,
                        'vehicle_plate' => $vehicle->registration_plate,
                        'error' => $e->getMessage()
                    ]);

                    // Rollback and rethrow to fail entire batch
                    throw $e;
                }
            }

            DB::commit();

            Log::info('Bulk vehicle assignment completed successfully', [
                'depot_id' => $depot->id,
                'depot_name' => $depot->name,
                'total_requested' => count($vehicleIds),
                'assigned' => $successCount,
                'skipped' => count($vehicleIds) - $vehiclesToAssign->count() - count($validationErrors),
                'failed' => $failedCount,
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'total_requested' => count($vehicleIds),
                'assigned' => $successCount,
                'skipped' => count($vehicleIds) - $vehiclesToAssign->count() - count($validationErrors),
                'failed' => $failedCount,
                'errors' => $validationErrors,
                'history_records' => $historyRecords,
                'message' => "{$successCount} véhicule(s) affecté(s) avec succès au dépôt {$depot->name}"
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Bulk vehicle assignment failed - transaction rolled back', [
                'depot_id' => $depot->id,
                'vehicle_count' => $vehiclesToAssign->count(),
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new RuntimeException(
                "Échec de l'affectation par lot: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get assignment history for a vehicle
     *
     * @param Vehicle $vehicle
     * @param int $limit Number of records to return (default: 50)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehicleHistory(Vehicle $vehicle, int $limit = 50)
    {
        return DepotAssignmentHistory::forVehicle($vehicle->id)
            ->with(['depot', 'previousDepot', 'assignedBy'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all vehicles currently assigned to a depot
     *
     * @param VehicleDepot $depot
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDepotVehicles(VehicleDepot $depot)
    {
        return Vehicle::where('depot_id', $depot->id)
            ->where('organization_id', $depot->organization_id)
            ->get();
    }

    /**
     * Get depot statistics
     *
     * @param VehicleDepot $depot
     * @return array
     */
    public function getDepotStats(VehicleDepot $depot): array
    {
        $vehicles = $this->getDepotVehicles($depot);

        return [
            'total_vehicles' => $vehicles->count(),
            'capacity' => $depot->capacity,
            'available_space' => $depot->availableCapacity,
            'occupancy_percentage' => $depot->occupancyPercentage,
            'is_full' => $depot->isFull(),
            'has_space' => $depot->hasAvailableSpace(),
            'vehicles_by_status' => [
                'active' => $vehicles->where('status', 'active')->count(),
                'maintenance' => $vehicles->where('status', 'maintenance')->count(),
                'inactive' => $vehicles->where('status', 'inactive')->count(),
            ],
            'recent_assignments' => DepotAssignmentHistory::forDepot($depot->id)
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Validate if assignment is possible
     *
     * Non-throwing validation method for UI/API use
     *
     * @param Vehicle $vehicle
     * @param VehicleDepot $depot
     * @return array ['valid' => bool, 'message' => string|null]
     */
    public function validateAssignment(Vehicle $vehicle, VehicleDepot $depot): array
    {
        // Check organization match
        if ($vehicle->organization_id !== $depot->organization_id) {
            return [
                'valid' => false,
                'message' => 'Le véhicule et le dépôt doivent appartenir à la même organisation'
            ];
        }

        // Check if already assigned
        if ($vehicle->depot_id === $depot->id) {
            return [
                'valid' => false,
                'message' => 'Le véhicule est déjà affecté à ce dépôt'
            ];
        }

        // Check capacity
        if (!$depot->hasAvailableSpace()) {
            return [
                'valid' => false,
                'message' => "Le dépôt est complet ({$depot->current_count}/{$depot->capacity})"
            ];
        }

        return [
            'valid' => true,
            'message' => null
        ];
    }
}
