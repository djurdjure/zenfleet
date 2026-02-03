<?php

namespace App\Policies;

use App\Models\MaintenanceOperation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenanceOperationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any maintenance operations.
     *
     * NOTE: Avec le multi-tenancy activé, les permissions sont automatiquement scopées
     * par organization_id via OrganizationTeamResolver
     */
    public function viewAny(User $user): bool
    {
        return $user->can("maintenance.view");
    }

    /**
     * Determine whether the user can view the maintenance operation.
     *
     * SÉCURITÉ: Double vérification (defense in depth)
     * 1. Permission scopée par organization via TeamResolver
     * 2. Vérification explicite organization_id (par sécurité)
     */
    public function view(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        // Super Admin bypass (déjà géré dans AuthServiceProvider Gate::before)
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.view") 
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can create maintenance operations.
     */
    public function create(User $user): bool
    {
        return $user->can("maintenance.operations.create");
    }

    /**
     * Determine whether the user can update the maintenance operation.
     */
    public function update(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.update")
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the maintenance operation.
     */
    public function delete(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.delete")
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the maintenance operation.
     */
    public function restore(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.delete")
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the maintenance operation.
     */
    public function forceDelete(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.delete")
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can start a maintenance operation.
     */
    public function start(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.update")
            && $maintenanceOperation->status === 'planned'
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can complete a maintenance operation.
     */
    public function complete(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.update")
            && $maintenanceOperation->status === 'in_progress'
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can cancel a maintenance operation.
     */
    public function cancel(User $user, MaintenanceOperation $maintenanceOperation): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("maintenance.operations.update")
            && in_array($maintenanceOperation->status, ['planned', 'in_progress'])
            && $maintenanceOperation->vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can export maintenance data.
     */
    public function export(User $user): bool
    {
        return $user->can("maintenance.view");
    }
}
