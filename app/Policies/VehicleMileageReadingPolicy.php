<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleMileageReading;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * VehicleMileageReadingPolicy - Multi-tenant authorization policy
 *
 * Authorization levels:
 * - Own: User can only access their own readings (created_by)
 * - Team: Supervisor can access team readings (same team/assignment)
 * - All: Admin/Fleet Manager can access all organization readings
 *
 * Business rules:
 * - Drivers can update own readings within 24h
 * - Automatic readings cannot be updated manually
 * - Only admins can delete readings older than 7 days
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReadingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any mileage readings.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('mileage-readings.view.own')
            || $user->can('mileage-readings.view.team')
            || $user->can('mileage-readings.view.all');
    }

    /**
     * Determine if user can view the mileage reading.
     */
    public function view(User $user, VehicleMileageReading $reading): bool
    {
        // Multi-tenant check: Must be same organization
        if ($user->organization_id !== $reading->organization_id) {
            return false;
        }

        // Super Admin: global access
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // View all: Admin, Fleet Manager
        if ($user->can('mileage-readings.view.all')) {
            return true;
        }

        // View team: Supervisor (same team/depot)
        if ($user->can('mileage-readings.view.team')) {
            // Check if user supervises the vehicle's depot or category
            $vehicle = $reading->vehicle;

            // If supervisor has depot_id and it matches vehicle's depot
            if ($user->depot_id && $vehicle->depot_id === $user->depot_id) {
                return true;
            }

            // Additional team logic can be added here
        }

        // View own: Driver (readings they created)
        if ($user->can('mileage-readings.view.own')) {
            return $reading->recorded_by_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if user can mileage-readings.create.
     */
    public function create(User $user): bool
    {
        return $user->can('mileage-readings.create');
    }

    /**
     * Determine if user can update the mileage reading.
     */
    public function update(User $user, VehicleMileageReading $reading): bool
    {
        // Multi-tenant check
        if ($user->organization_id !== $reading->organization_id) {
            return false;
        }

        // Super Admin: always allowed
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Automatic readings cannot be updated manually (only by admins)
        if ($reading->is_automatic && !$user->can('mileage-readings.manage.automatic')) {
            return false;
        }

        // Update any: Admin, Fleet Manager
        if ($user->can('mileage-readings.update.any')) {
            return true;
        }

        // Update own: Driver (within 24h of creation)
        if ($user->can('mileage-readings.update.own') && $reading->recorded_by_id === $user->id) {
            // Check if reading is less than 24 hours old
            $hoursOld = $reading->created_at->diffInHours(now());

            if ($hoursOld <= 24) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if user can delete the mileage reading.
     */
    public function delete(User $user, VehicleMileageReading $reading): bool
    {
        // Multi-tenant check
        if ($user->organization_id !== $reading->organization_id) {
            return false;
        }

        // Super Admin: always allowed
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Force delete permission (admin only)
        if ($user->can('mileage-readings.force-delete')) {
            return true;
        }

        // Regular delete permission
        if (!$user->can('mileage-readings.delete')) {
            return false;
        }

        // Business rule: Cannot delete readings older than 7 days (except admins)
        $daysOld = $reading->created_at->diffInDays(now());

        if ($daysOld > 7 && !$user->can('mileage-readings.update.any')) {
            return false;
        }

        // Drivers can only delete their own readings
        if ($user->hasRole('Chauffeur')) {
            return $reading->recorded_by_id === $user->id;
        }

        return true;
    }

    /**
     * Determine if user can restore the mileage reading.
     */
    public function restore(User $user, VehicleMileageReading $reading): bool
    {
        // Multi-tenant check
        if ($user->organization_id !== $reading->organization_id) {
            return false;
        }

        return $user->can('mileage-readings.restore');
    }

    /**
     * Determine if user can permanently delete the mileage reading.
     */
    public function forceDelete(User $user, VehicleMileageReading $reading): bool
    {
        // Multi-tenant check
        if ($user->organization_id !== $reading->organization_id) {
            return false;
        }

        return $user->can('mileage-readings.force-delete');
    }

    /**
     * Determine if user can mileage-readings.manage.automatic.
     */
    public function manageAutomatic(User $user): bool
    {
        return $user->can('mileage-readings.manage.automatic');
    }

    /**
     * Determine if user can mileage-readings.export.
     */
    public function export(User $user): bool
    {
        return $user->can('mileage-readings.export');
    }

    /**
     * Determine if user can mileage-readings.view.statistics.
     */
    public function viewStatistics(User $user): bool
    {
        return $user->can('mileage-readings.view.statistics');
    }
}
