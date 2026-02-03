<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, Role $role): bool
    {
        // Seul Super Admin peut modifier le rôle Super Admin
        if ($role->name === 'Super Admin') {
            return $user->hasRole('Super Admin');
        }
        
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    public function delete(User $user, Role $role): bool
    {
        // Interdire la suppression du Super Admin
        if ($role->name === 'Super Admin') {
            return false;
        }
        
        return $user->hasRole('Super Admin');
    }

    public function assign(User $user, Role $role, User $targetUser): bool
    {
        // Seul Super Admin peut assigner Super Admin
        if ($role->name === 'Super Admin') {
            return $user->hasRole('Super Admin');
        }

        // Empêcher l'auto-promotion
        if ($user->id === $targetUser->id && $role->name === 'Super Admin') {
            return false;
        }

        return $user->hasRole(['Super Admin', 'Admin']);
    }

    /**
     * 🎭 GÉRER LA MATRICE DES PERMISSIONS
     *
     * Permission pour accéder à la console avancée de gestion des permissions
     */
    public function manage(User $user): bool
    {
        return $user->can('roles.manage') || $user->hasRole(['Super Admin', 'Admin']);
    }
}
