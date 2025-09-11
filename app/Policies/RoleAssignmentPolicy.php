<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoleAssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Contrôler l'attribution des rôles selon la hiérarchie de sécurité
     */
    public function assign(User $user, Role $role, User $targetUser = null): bool
    {
        // Règle 1: Seul un Super Admin peut créer/assigner le rôle Super Admin
        if ($role->name === 'Super Admin') {
            return $user->hasRole('Super Admin');
        }
        
        // Règle 2: Un Admin ne peut pas s'auto-promouvoir
        if ($targetUser && $user->id === $targetUser->id) {
            $currentHighestRole = $this->getHighestRoleLevel($user);
            $targetRoleLevel = $this->getRoleLevel($role);
            
            // Interdire l'auto-promotion à un niveau supérieur
            if ($targetRoleLevel < $currentHighestRole) {
                return false;
            }
        }
        
        // Règle 3: Hiérarchie des permissions d'attribution
        $userLevel = $this->getHighestRoleLevel($user);
        $roleLevel = $this->getRoleLevel($role);
        
        // Un utilisateur peut seulement assigner des rôles de niveau inférieur
        return $userLevel < $roleLevel;
    }

    /**
     * Contrôler la modification des rôles
     */
    public function update(User $user, Role $role): bool
    {
        // Seul Super Admin peut modifier le rôle Super Admin
        if ($role->name === 'Super Admin') {
            return $user->hasRole('Super Admin');
        }
        
        // Admin peut modifier les autres rôles
        return $user->hasRole(['Super Admin', 'Admin']);
    }

    /**
     * Contrôler la suppression des rôles
     */
    public function delete(User $user, Role $role): bool
    {
        // Interdire la suppression du rôle Super Admin
        if ($role->name === 'Super Admin') {
            return false;
        }
        
        // Seul Super Admin peut supprimer des rôles
        return $user->hasRole('Super Admin');
    }

    /**
     * Obtenir le niveau hiérarchique d'un rôle (plus petit = plus haut niveau)
     */
    private function getRoleLevel(Role $role): int
    {
        $hierarchy = [
            'Super Admin' => 1,
            'Admin' => 2,
            'Gestionnaire Flotte' => 3,
            'supervisor' => 4,
            'Chauffeur' => 5
        ];
        
        return $hierarchy[$role->name] ?? 999;
    }

    /**
     * Obtenir le niveau le plus élevé d'un utilisateur
     */
    private function getHighestRoleLevel(User $user): int
    {
        $userRoles = $user->roles;
        $minLevel = 999;
        
        foreach ($userRoles as $role) {
            $level = $this->getRoleLevel($role);
            if ($level < $minLevel) {
                $minLevel = $level;
            }
        }
        
        return $minLevel;
    }
}

