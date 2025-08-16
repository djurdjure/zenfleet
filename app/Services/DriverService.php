<?php
namespace App\Services;

use App\Models\Driver;
use App\Repositories\Interfaces\DriverRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class DriverService
{
    protected DriverRepositoryInterface $driverRepository;
    public function __construct(DriverRepositoryInterface $driverRepository) { $this->driverRepository = $driverRepository; }
    public function getFilteredDrivers(array $filters): LengthAwarePaginator { return $this->driverRepository->getFiltered($filters); }

    public function createDriver(array $data): Driver
    {
        if (isset($data['photo'])) {
            $data['photo_path'] = $data['photo']->store('drivers/photos', 'public');
            unset($data['photo']);
        }
        return $this->driverRepository->create($data);
    }

    public function updateDriver(Driver $driver, array $data): bool
    {
        if (isset($data['photo'])) {
            if ($driver->photo_path) {
                Storage::disk('public')->delete($driver->photo_path);
            }
            $data['photo_path'] = $data['photo']->store('drivers/photos', 'public');
            unset($data['photo']);
        }
        return $this->driverRepository->update($driver, $data);
    }

    public function archiveDriver(Driver $driver): bool { return $this->driverRepository->delete($driver); }

    public function restoreDriver(int $driverId): bool
    {
        $driver = $this->driverRepository->findTrashed($driverId);
        return $driver ? $this->driverRepository->restore($driver) : false;
    }

   public function forceDeleteDriver(int $driverId): bool
    {
        $driver = $this->driverRepository->findTrashed($driverId);

        if ($driver) {
            // RÈGLE MÉTIER : On vérifie si le chauffeur a des affectations
            if ($driver->assignments()->exists()) {
                // Si oui, on refuse la suppression
                return false;
            }

            // Si non, on procède à la suppression
            if ($driver->photo_path) {
                Storage::disk('public')->delete($driver->photo_path);
            }
            return $this->driverRepository->forceDelete($driver);
        }
        return false;
    }
}
