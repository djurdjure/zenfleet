<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any drivers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("drivers.view");
    }

    /**
     * Determine whether the user can view the driver.
     */
    public function view(User $user, Driver $driver): bool
    {
        return $user->can("drivers.view") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can drivers.create.
     */
    public function create(User $user): bool
    {
        return $user->can("drivers.create");
    }

    /**
     * Determine whether the user can update the driver.
     */
    public function update(User $user, Driver $driver): bool
    {
        return $user->can("drivers.update") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the driver.
     */
    public function delete(User $user, Driver $driver): bool
    {
        return $user->can("drivers.delete") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the driver.
     */
    public function restore(User $user, Driver $driver): bool
    {
        return $user->can("drivers.restore") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the driver.
     */
    public function forceDelete(User $user, Driver $driver): bool
    {
        return $user->can("force drivers.delete") && $driver->organization_id === $user->organization_id;
    }
}
