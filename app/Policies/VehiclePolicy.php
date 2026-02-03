<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any vehicles.
     *
     * NOTE: Avec le multi-tenancy activé, les permissions sont automatiquement scopées
     * par organization_id via OrganizationTeamResolver
     */
    public function viewAny(User $user): bool
    {
        return $user->can("vehicles.view");
    }

    /**
     * Determine whether the user can view the vehicle.
     *
     * SÉCURITÉ: Double vérification (defense in depth)
     * 1. Permission scopée par organization via TeamResolver
     * 2. Vérification explicite organization_id (par sécurité)
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        // Super Admin bypass (déjà géré dans AuthServiceProvider Gate::before)
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("vehicles.view") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can vehicles.create.
     */
    public function create(User $user): bool
    {
        return $user->can("vehicles.create");
    }

    /**
     * Determine whether the user can update the vehicle.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("vehicles.update") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can delete the vehicle.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("vehicles.delete") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can restore the vehicle.
     */
    public function restore(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("vehicles.restore") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can permanently delete the vehicle.
     */
    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->can("vehicles.force-delete") && $vehicle->organization_id === $user->organization_id;
    }

    /**
     * Determine whether the user can view the vehicle's mileage history.
     *
     * Permissions hiérarchiques:
     * - Chauffeur: peut voir l'historique de son véhicule assigné
     * - Superviseur/Chef de Parc: peut voir l'historique des véhicules de son dépôt
     * - Admin/Gestionnaire: peut voir l'historique de tous les véhicules de l'organisation
     * - Super Admin: accès global
     *
     * @param User $user L'utilisateur tentant d'accéder à l'historique
     * @param Vehicle $vehicle Le véhicule dont on veut voir l'historique
     * @return bool True si l'accès est autorisé
     */
    public function mileageHistory(User $user, Vehicle $vehicle): bool
    {
        // Super Admin bypass
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Vérifier que le véhicule appartient à la même organisation
        if ($vehicle->organization_id !== $user->organization_id) {
            return false;
        }

        // Vérifier les permissions kilométrage de manière hiérarchique
        // view all > view team > view own
        if ($user->can('mileage-readings.view.all')) {
            return true;
        }

        // Superviseur/Chef de Parc: véhicules de son dépôt uniquement
        if ($user->can('mileage-readings.view.team')) {
            if ($user->depot_id && $vehicle->depot_id === $user->depot_id) {
                return true;
            }
        }

        // Chauffeur: uniquement son véhicule assigné
        if ($user->can('mileage-readings.view.own')) {
            if ($user->driver_id) {
                // Vérifier si le véhicule est assigné à ce chauffeur
                return $vehicle->currentAssignments()
                    ->where('driver_id', $user->driver_id)
                    ->exists();
            }
        }

        return false;
    }
}
