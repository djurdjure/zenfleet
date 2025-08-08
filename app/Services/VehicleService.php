<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class VehicleService
{
    protected VehicleRepositoryInterface $vehicleRepository;

    public function __construct(VehicleRepositoryInterface $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Récupère les véhicules pour la page d'index avec filtres et pagination.
     */
    public function getFilteredVehicles(array $filters): LengthAwarePaginator
    {
        return $this->vehicleRepository->getFiltered($filters);
    }

    /**
     * Gère la création d'un véhicule, y compris la photo.
     */
    public function createVehicle(array $data): Vehicle
    {
        if (isset($data['photo'])) {
            $data['photo_path'] = $data['photo']->store('vehicles/photos', 'public');
            unset($data['photo']);
        }
        $data['current_mileage'] = $data['initial_mileage'];

        return $this->vehicleRepository->create($data);
    }

    /**
     * Gère la mise à jour d'un véhicule, y compris la photo.
     */
    public function updateVehicle(Vehicle $vehicle, array $data): bool
    {
        if (isset($data['photo'])) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $data['photo_path'] = $data['photo']->store('vehicles/photos', 'public');
            unset($data['photo']);
        }
        return $this->vehicleRepository->update($vehicle, $data);
    }

    /**
     * Archive un véhicule.
     */
    public function archiveVehicle(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    /**
     * Restaure un véhicule archivé.
     */
    public function restoreVehicle(int $vehicleId): bool
    {
        $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);
        return $vehicle->restore();
    }

    /**
     * Supprime définitivement un véhicule et sa photo.
     */
    public function forceDeleteVehicle(int $vehicleId): bool
    {
        $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }
        return $vehicle->forceDelete();
    }
}