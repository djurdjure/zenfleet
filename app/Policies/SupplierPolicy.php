<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any suppliers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("view suppliers");
    }

    /**
     * Determine whether the user can view the supplier.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return $user->can("view suppliers") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can create suppliers.
     */
    public function create(User $user): bool
    {
        return $user->can("create suppliers");
    }

    /**
     * Determine whether the user can update the supplier.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return $user->can("edit suppliers") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the supplier.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can("delete suppliers") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the supplier.
     */
    public function restore(User $user, Supplier $supplier): bool
    {
        return $user->can("restore suppliers") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the supplier.
     */
    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return $user->can("force delete suppliers") && $supplier->organization_id === $user->organization_id;
    }
}
