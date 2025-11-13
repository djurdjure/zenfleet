<?php

namespace App\Traits;

use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * 🎯 TRAIT ENTERPRISE-GRADE: RESOURCE AVAILABILITY
 *
 * Fournit des méthodes unifiées et optimisées pour récupérer les ressources
 * disponibles (véhicules et chauffeurs) avec une source de vérité unique:
 * le champ `is_available`.
 *
 * Ce trait remplace les anciennes requêtes basées sur les relations
 * de statuts (vehicleStatus, driverStatus) par des requêtes directes
 * sur les champs dynamiques, garantissant ainsi:
 *
 * ✅ Cohérence à 100%
 * ✅ Performance optimale (pas de N+1 queries)
 * ✅ Source de vérité unique
 * ✅ Maintenabilité DRY
 *
 * Utilisation dans les contrôleurs:
 * ```php
 * use App\Traits\ResourceAvailability;
 *
 * class AssignmentController extends Controller {
 *     use ResourceAvailability;
 *
 *     public function create() {
 *         $vehicles = $this->getAvailableVehicles();
 *         $drivers = $this->getAvailableDrivers();
 *     }
 * }
 * ```
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Team
 */
trait ResourceAvailability
{
    /**
     * Récupère tous les véhicules disponibles pour une organisation
     *
     * Critères de disponibilité:
     * - is_available = true
     * - assignment_status = 'available'
     * - current_driver_id IS NULL
     * - is_archived = false
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @param bool $withRelations Charger les relations (vehicleType, vehicleStatus)
     * @return Collection<Vehicle>
     */
    protected function getAvailableVehicles(?int $organizationId = null, bool $withRelations = true): Collection
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Vehicle::where('organization_id', $orgId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->where('is_archived', false)
            ->when($withRelations, function ($query) {
                return $query->with(['vehicleType', 'vehicleStatus']);
            })
            ->orderBy('registration_plate')
            ->get();
    }

    /**
     * Récupère tous les chauffeurs disponibles pour une organisation
     *
     * Critères de disponibilité:
     * - is_available = true
     * - assignment_status = 'available'
     * - current_vehicle_id IS NULL
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @param bool $withRelations Charger la relation driverStatus
     * @return Collection<Driver>
     */
    protected function getAvailableDrivers(?int $organizationId = null, bool $withRelations = true): Collection
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Driver::where('organization_id', $orgId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->when($withRelations, function ($query) {
                return $query->with('driverStatus');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Compte le nombre de véhicules disponibles pour une organisation
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @return int
     */
    protected function countAvailableVehicles(?int $organizationId = null): int
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Vehicle::where('organization_id', $orgId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->where('is_archived', false)
            ->count();
    }

    /**
     * Compte le nombre de chauffeurs disponibles pour une organisation
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @return int
     */
    protected function countAvailableDrivers(?int $organizationId = null): int
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Driver::where('organization_id', $orgId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->count();
    }

    /**
     * Récupère tous les véhicules affectés (en mission) pour une organisation
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @param bool $withRelations Charger les relations
     * @return Collection<Vehicle>
     */
    protected function getAssignedVehicles(?int $organizationId = null, bool $withRelations = true): Collection
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Vehicle::where('organization_id', $orgId)
            ->where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->whereNotNull('current_driver_id')
            ->where('is_archived', false)
            ->when($withRelations, function ($query) {
                return $query->with(['vehicleType', 'vehicleStatus', 'currentDriver']);
            })
            ->orderBy('registration_plate')
            ->get();
    }

    /**
     * Récupère tous les chauffeurs en mission pour une organisation
     *
     * @param int|null $organizationId ID de l'organisation (null = organisation courante)
     * @param bool $withRelations Charger les relations
     * @return Collection<Driver>
     */
    protected function getAssignedDrivers(?int $organizationId = null, bool $withRelations = true): Collection
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return Driver::where('organization_id', $orgId)
            ->where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->whereNotNull('current_vehicle_id')
            ->when($withRelations, function ($query) {
                return $query->with(['driverStatus', 'currentVehicle']);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Scope réutilisable pour filtrer les véhicules disponibles
     *
     * @param Builder $query
     * @return Builder
     */
    protected function scopeAvailableVehicles(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->where('is_archived', false);
    }

    /**
     * Scope réutilisable pour filtrer les chauffeurs disponibles
     *
     * @param Builder $query
     * @return Builder
     */
    protected function scopeAvailableDrivers(Builder $query): Builder
    {
        return $query->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id');
    }

    /**
     * Vérifie si un véhicule est disponible
     *
     * @param int $vehicleId
     * @return bool
     */
    protected function isVehicleAvailable(int $vehicleId): bool
    {
        return Vehicle::where('id', $vehicleId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->where('is_archived', false)
            ->exists();
    }

    /**
     * Vérifie si un chauffeur est disponible
     *
     * @param int $driverId
     * @return bool
     */
    protected function isDriverAvailable(int $driverId): bool
    {
        return Driver::where('id', $driverId)
            ->where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->exists();
    }

    /**
     * Récupère les statistiques de disponibilité pour le dashboard
     *
     * @param int|null $organizationId
     * @return array
     */
    protected function getAvailabilityStats(?int $organizationId = null): array
    {
        $orgId = $organizationId ?? auth()->user()?->organization_id;

        return [
            'vehicles' => [
                'total' => Vehicle::where('organization_id', $orgId)
                    ->where('is_archived', false)
                    ->count(),
                'available' => $this->countAvailableVehicles($orgId),
                'assigned' => Vehicle::where('organization_id', $orgId)
                    ->where('is_available', false)
                    ->where('assignment_status', 'assigned')
                    ->where('is_archived', false)
                    ->count(),
            ],
            'drivers' => [
                'total' => Driver::where('organization_id', $orgId)->count(),
                'available' => $this->countAvailableDrivers($orgId),
                'assigned' => Driver::where('organization_id', $orgId)
                    ->where('is_available', false)
                    ->where('assignment_status', 'assigned')
                    ->count(),
            ]
        ];
    }

    /**
     * Récupère les options de select pour les véhicules disponibles
     * Format: ['id' => 'label']
     *
     * @param int|null $organizationId
     * @return array
     */
    protected function getAvailableVehiclesOptions(?int $organizationId = null): array
    {
        return $this->getAvailableVehicles($organizationId, false)
            ->mapWithKeys(function ($vehicle) {
                return [$vehicle->id => $vehicle->registration_plate . ' - ' . $vehicle->brand . ' ' . $vehicle->model];
            })
            ->toArray();
    }

    /**
     * Récupère les options de select pour les chauffeurs disponibles
     * Format: ['id' => 'label']
     *
     * @param int|null $organizationId
     * @return array
     */
    protected function getAvailableDriversOptions(?int $organizationId = null): array
    {
        return $this->getAvailableDrivers($organizationId, false)
            ->mapWithKeys(function ($driver) {
                return [$driver->id => $driver->full_name . ' (' . $driver->license_number . ')'];
            })
            ->toArray();
    }
}
