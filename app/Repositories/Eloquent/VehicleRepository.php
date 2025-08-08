<?php

namespace App\Repositories\Eloquent;

use App\Models\Vehicle;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleRepository implements VehicleRepositoryInterface
{
    /**
     * Récupère une liste paginée et filtrée de véhicules.
     */
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Vehicle::query()->with(['vehicleType', 'vehicleStatus']);

        if (!empty($filters['view_deleted'])) {
            $query->onlyTrashed();
        }

        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(registration_plate) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(brand) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Crée un nouveau véhicule.
     */
    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    /**
     * Met à jour un véhicule existant.
     */
    public function update(Vehicle $vehicle, array $data): bool
    {
        return $vehicle->update($data);
    }
}
