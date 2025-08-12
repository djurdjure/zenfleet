<?php
namespace App\Services;

use App\Models\Vehicle;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class VehicleService
{
    protected VehicleRepositoryInterface $vehicleRepository;

    public function __construct(VehicleRepositoryInterface $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    public function getFilteredVehicles(array $filters): LengthAwarePaginator
    {
        return $this->vehicleRepository->getFiltered($filters);
    }

    public function createVehicle(array $data): Vehicle
    {
        if (isset($data['photo'])) {
            $data['photo_path'] = $data['photo']->store('vehicles/photos', 'public');
            unset($data['photo']);
        }
        $data['current_mileage'] = $data['initial_mileage'];

        return $this->vehicleRepository->create($data);
    }

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

    public function archiveVehicle(Vehicle $vehicle): bool
    {
        return $this->vehicleRepository->delete($vehicle);
    }

    public function restoreVehicle(int $vehicleId): bool
    {
        $vehicle = $this->vehicleRepository->findTrashed($vehicleId);
        if ($vehicle) {
            return $this->vehicleRepository->restore($vehicle);
        }
        return false;
    }

    public function forceDeleteVehicle(int $vehicleId): bool
    {
        $vehicle = $this->vehicleRepository->findTrashed($vehicleId);
        if ($vehicle) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            return $this->vehicleRepository->forceDelete($vehicle);
        }
        return false;
    }
}