<?php

namespace App\Policies;

use App\Models\DriverSanction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy pour la gestion des sanctions de chauffeurs
 *
 * Contrôle l'accès aux sanctions en fonction des permissions Spatie
 * et de la relation avec l'organization_id (multi-tenant).
 *
 * @author ZenFleet Enterprise Team
 * @version 2.0.0
 */
class DriverSanctionPolicy
{
    use HandlesAuthorization;

    /**
     * Déterminer si l'utilisateur peut voir la liste des sanctions
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('driver-sanctions.view.own')
            || $user->can('driver-sanctions.view.team')
            || $user->can('driver-sanctions.view.all');
    }

    /**
     * Déterminer si l'utilisateur peut voir une sanction spécifique
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function view(User $user, DriverSanction $driverSanction): bool
    {
        // Permission de voir toutes les sanctions
        if ($user->can('driver-sanctions.view.all')) {
            // Pour Admin: vérifier l'organisation
            if ($user->hasRole('Admin')) {
                return $user->organization_id === $driverSanction->organization_id;
            }
            // Super Admin peut tout voir
            return true;
        }

        // Permission de voir les sanctions de l'équipe
        if ($user->can('driver-sanctions.view.team')) {
            return $user->organization_id === $driverSanction->organization_id;
        }

        // Permission de voir ses propres sanctions créées
        if ($user->can('driver-sanctions.view.own')) {
            return $driverSanction->supervisor_id === $user->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer des sanctions
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('driver-sanctions.create');
    }

    /**
     * Déterminer si l'utilisateur peut modifier une sanction
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function update(User $user, DriverSanction $driverSanction): bool
    {
        // Ne peut pas modifier une sanction archivée
        if ($driverSanction->isArchived()) {
            return false;
        }

        // Permission de modifier n'importe quelle sanction
        if ($user->can('driver-sanctions.update.any')) {
            // Pour Admin: vérifier l'organisation
            if ($user->hasRole('Admin')) {
                return $user->organization_id === $driverSanction->organization_id;
            }
            // Super Admin peut tout modifier
            return true;
        }

        // Permission de modifier ses propres sanctions
        if ($user->can('driver-sanctions.update.own') && $driverSanction->supervisor_id === $user->id) {
            // Autoriser la modification dans les 24h suivant la création
            return $driverSanction->created_at->diffInHours(now()) <= 24;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer une sanction
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function delete(User $user, DriverSanction $driverSanction): bool
    {
        if (!$user->can('driver-sanctions.delete')) {
            return false;
        }

        // Pour Admin: vérifier l'organisation
        if ($user->hasRole('Admin')) {
            return $user->organization_id === $driverSanction->organization_id;
        }

        // Super Admin peut tout supprimer
        return true;
    }

    /**
     * Déterminer si l'utilisateur peut archiver une sanction
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function archive(User $user, DriverSanction $driverSanction): bool
    {
        // Ne peut pas archiver ce qui est déjà archivé
        if ($driverSanction->isArchived()) {
            return false;
        }

        if (!$user->can('driver-sanctions.archive')) {
            return false;
        }

        // Pour Admin: vérifier l'organisation
        if ($user->hasRole('Admin')) {
            return $user->organization_id === $driverSanction->organization_id;
        }

        // Super Admin peut tout archiver
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Superviseur peut archiver ses sanctions après 30 jours
        if ($user->hasRole('Superviseur') && $driverSanction->supervisor_id === $user->id) {
            return $driverSanction->getDaysSinceSanction() >= 30;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut désarchiver une sanction
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function unarchive(User $user, DriverSanction $driverSanction): bool
    {
        // Ne peut pas désarchiver ce qui n'est pas archivé
        if (!$driverSanction->isArchived()) {
            return false;
        }

        if (!$user->can('driver-sanctions.unarchive')) {
            return false;
        }

        // Pour Admin: vérifier l'organisation
        if ($user->hasRole('Admin')) {
            return $user->organization_id === $driverSanction->organization_id;
        }

        // Super Admin peut tout désarchiver
        return true;
    }

    /**
     * Déterminer si l'utilisateur peut restaurer une sanction supprimée
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function restore(User $user, DriverSanction $driverSanction): bool
    {
        if (!$user->can('driver-sanctions.restore')) {
            return false;
        }

        // Seuls les Super Admins peuvent restaurer
        return $user->hasRole('Super Admin');
    }

    /**
     * Déterminer si l'utilisateur peut supprimer définitivement une sanction
     *
     * @param User $user
     * @param DriverSanction $driverSanction
     * @return bool
     */
    public function forceDelete(User $user, DriverSanction $driverSanction): bool
    {
        if (!$user->can('driver-sanctions.force-delete')) {
            return false;
        }

        // Seuls les Super Admins peuvent supprimer définitivement
        return $user->hasRole('Super Admin');
    }
}
