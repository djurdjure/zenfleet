<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * 🛡️ Policy Assignments - Contrôle d'accès multi-tenant
 *
 * Gère les autorisations pour les affectations:
 * - Isolation par organisation
 * - Contrôles basés sur les rôles
 * - Validation business rules
 *
 * @author ZenFleet Architecture Team
 */
class AssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir toutes les affectations
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view assignments');
    }

    /**
     * Détermine si l'utilisateur peut voir une affectation spécifique
     */
    public function view(User $user, Assignment $assignment): bool
    {
        return $user->can('view assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut créer des affectations
     */
    public function create(User $user): bool
    {
        return $user->can('assignments.create') ||
               $user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte']);
    }

    /**
     * Détermine si l'utilisateur peut modifier une affectation
     */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->can('edit assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut terminer une affectation
     */
    public function end(User $user, Assignment $assignment): bool
    {
        return $user->can('end assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer une affectation
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->can('delete assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut restaurer une affectation supprimée
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        return $user->can('restore assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return $user->can('force delete assignments') &&
               $assignment->organization_id === $user->organization_id;
    }

    /**
     * Détermine si l'utilisateur peut exporter les affectations
     */
    public function export(User $user): bool
    {
        return $user->can('assignments.export') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager', 'Fleet Manager']);
    }

    /**
     * Détermine si l'utilisateur peut voir la vue Gantt
     */
    public function viewGantt(User $user): bool
    {
        return $user->can('assignments.view-gantt') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager', 'Fleet Manager']);
    }

    /**
     * Détermine si l'utilisateur peut voir les statistiques
     */
    public function viewStats(User $user): bool
    {
        return $user->can('assignments.view-stats') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager']);
    }

    /**
     * Détermine si l'utilisateur peut créer des affectations en lot
     */
    public function createBatch(User $user): bool
    {
        return $user->can('assignments.create-batch') ||
               $user->hasRole(['Super Admin', 'Admin', 'Fleet Manager']);
    }

    /**
     * Détermine si l'utilisateur peut voir les conflits/chevauchements
     */
    public function viewConflicts(User $user): bool
    {
        return $user->can('assignments.view-conflicts') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager', 'Fleet Manager']);
    }

    /**
     * Validation spécifique - peut affecter ce véhicule
     */
    public function assignVehicle(User $user, int $vehicleId): bool
    {
        // Vérifier que le véhicule appartient à la même organisation
        $vehicle = \App\Models\Vehicle::find($vehicleId);

        if (!$vehicle || $vehicle->organization_id !== $user->organization_id) {
            return false;
        }

        return $user->can('assignments.create') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager', 'Fleet Manager']);
    }

    /**
     * Validation spécifique - peut affecter ce chauffeur
     */
    public function assignDriver(User $user, int $driverId): bool
    {
        // Vérifier que le chauffeur appartient à la même organisation
        $driver = \App\Models\Driver::find($driverId);

        if (!$driver || $driver->organization_id !== $user->organization_id) {
            return false;
        }

        return $user->can('assignments.create') ||
               $user->hasRole(['Super Admin', 'Admin', 'Manager', 'Fleet Manager']);
    }

    /**
     * Méthodes helper privées
     */

    /**
     * Vérifie si l'utilisateur et l'affectation appartiennent à la même organisation
     */
    private function belongsToSameOrganization(User $user, Assignment $assignment): bool
    {
        // Super Admin peut accéder à toutes les organisations
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->organization_id === $assignment->organization_id;
    }

    /**
     * Vérifie si l'utilisateur est participant à l'affectation
     */
    private function isAssignmentParticipant(User $user, Assignment $assignment): bool
    {
        // Si l'utilisateur est le chauffeur de l'affectation
        if ($assignment->driver && $assignment->driver->user_id === $user->id) {
            return true;
        }

        // Si l'utilisateur est associé au véhicule (via une table pivot par exemple)
        // Cette logique peut être étendue selon les besoins business

        return false;
    }
}