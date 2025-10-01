<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any vehicles.
     *
     * NOTE: Avec le multi-tenancy activé, les permissions sont automatiquement scopées
     * par organization_id via OrganizationTeamResolver
     */
    public function viewAny(User $user): bool
    {
        return $user->can("view vehicles");
    }

    /**
     * Determine whether the user can view the vehicle.
     *
     * SÉCURITÉ: Double vérification (defense in depth)
     * 1. Permission scopée par organization via TeamResolver
     * 2. Vérification explicite organization_id (par sécurité)
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        // Super Admin bypass (déjà géré dans AuthServiceProvider Gate::before)
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("view vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can create vehicles.
     */
    public function create(User $user): bool
    {
        return $user->can("create vehicles");
    }

    /**
     * Determine whether the user can update the vehicle.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("edit vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the vehicle.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("delete vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the vehicle.
     */
    public function restore(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("restore vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the vehicle.
     */
    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("force delete vehicles") && $vehicle->organization_id === $user->organization_id;
    }
}

