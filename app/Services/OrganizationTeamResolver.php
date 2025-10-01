<?php

namespace App\Services;

use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 🏢 ORGANIZATION TEAM RESOLVER - ENTERPRISE-GRADE
 *
 * Résout automatiquement l'organization_id de l'utilisateur connecté
 * pour le scoping automatique des permissions par organisation
 *
 * IMPORTANT: Évite les boucles infinies en vérifiant directement dans la BDD
 * au lieu d'utiliser $user->hasRole() qui déclencherait le resolver
 *
 * @package App\Services
 * @author ZenFleet Enterprise System
 */
class OrganizationTeamResolver implements PermissionsTeamResolver
{
    /**
     * @var int|string|null
     */
    protected $teamId;

    /**
     * Retourne l'ID de l'organisation de l'utilisateur connecté
     *
     * @return int|string|null L'ID de l'organisation ou null si non connecté
     */
    public function getPermissionsTeamId(): int|string|null
    {
        // Si un teamId a été défini manuellement, le retourner
        if ($this->teamId !== null) {
            return $this->teamId;
        }

        // Vérifier si un utilisateur est connecté
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        // CORRECTIF ANTI-BOUCLE INFINIE:
        // Vérifier directement en BDD si l'utilisateur est Super Admin
        // sans passer par hasRole() qui déclencherait le resolver
        $isSuperAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->where('roles.name', 'Super Admin')
            ->whereNull('roles.organization_id') // Super Admin est global
            ->exists();

        // Super Admin n'a pas de scoping (retourne null)
        // Cela lui permet d'accéder à toutes les organisations
        if ($isSuperAdmin) {
            return null;
        }

        // Pour tous les autres rôles, retourner l'organization_id
        return $user->organization_id;
    }

    /**
     * Définir manuellement un team ID (pour les tests ou cas spéciaux)
     *
     * @param int|string|\Illuminate\Database\Eloquent\Model|null $id
     * @return void
     */
    public function setPermissionsTeamId($id): void
    {
        if ($id instanceof \Illuminate\Database\Eloquent\Model) {
            $this->teamId = $id->getKey();
        } else {
            $this->teamId = $id;
        }
    }
}
