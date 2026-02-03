<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->can('documents.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document)
    {
        return $user->can('documents.view') && $user->organization_id === $document->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->can('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document)
    {
        return $user->can('documents.update') && $user->organization_id === $document->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document)
    {
        return $user->can('documents.delete') && $user->organization_id === $document->organization_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document)
    {
        return $user->can('documents.update') && $user->organization_id === $document->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document)
    {
        return $user->can('documents.delete') && $user->organization_id === $document->organization_id;
    }
}
