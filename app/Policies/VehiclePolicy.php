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
     */
    public function viewAny(User $user): bool
    {
        return $user->can("view vehicles");
    }

    /**
     * Determine whether the user can view the vehicle.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
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
        return $user->can("edit vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the vehicle.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->can("delete vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the vehicle.
     */
    public function restore(User $user, Vehicle $vehicle): bool
    {
        return $user->can("restore vehicles") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the vehicle.
     */
    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return $user->can("force delete vehicles") && $vehicle->organization_id === $user->organization_id;
    }
}

