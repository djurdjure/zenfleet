<?php

namespace App\Policies;

use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        // Anyone with the permission can view the list page.
        // The controller will scope the query by organization.
        return $user->can('document-categories.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentCategory $documentCategory)
    {
        // User can view if they have the permission AND belong to the same organization.
        return $user->can('document-categories.manage') && $user->organization_id === $documentCategory->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->can('document-categories.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentCategory $documentCategory)
    {
        // User can update if they have the permission AND belong to the same organization.
        return $user->can('document-categories.manage') && $user->organization_id === $documentCategory->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentCategory $documentCategory)
    {
        // User can delete if they have the permission AND belong to the same organization.
        return $user->can('document-categories.manage') && $user->organization_id === $documentCategory->organization_id;
    }
}
