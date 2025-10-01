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
        return $user->can("view drivers");
    }

    /**
     * Determine whether the user can view the driver.
     */
    public function view(User $user, Driver $driver): bool
    {
        return $user->can("view drivers") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can create drivers.
     */
    public function create(User $user): bool
    {
        return $user->can("create drivers");
    }

    /**
     * Determine whether the user can update the driver.
     */
    public function update(User $user, Driver $driver): bool
    {
        return $user->can("edit drivers") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the driver.
     */
    public function delete(User $user, Driver $driver): bool
    {
        return $user->can("delete drivers") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the driver.
     */
    public function restore(User $user, Driver $driver): bool
    {
        return $user->can("restore drivers") && $driver->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the driver.
     */
    public function forceDelete(User $user, Driver $driver): bool
    {
        return $user->can("force delete drivers") && $driver->organization_id === $user->organization_id;
    }
}
