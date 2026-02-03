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
        return $user->can("suppliers.view");
    }

    /**
     * Determine whether the user can view the supplier.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return $user->can("suppliers.view") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can suppliers.create.
     */
    public function create(User $user): bool
    {
        return $user->can("suppliers.create");
    }

    /**
     * Determine whether the user can update the supplier.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return $user->can("suppliers.update") && $supplier->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the supplier.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can("suppliers.delete") && $supplier->organization_id === $user->organization_id;
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
        return $user->can("force suppliers.delete") && $supplier->organization_id === $user->organization_id;
    }
}
