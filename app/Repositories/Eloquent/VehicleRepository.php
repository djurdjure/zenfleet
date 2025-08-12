<?php
namespace App\Repositories\Eloquent;

use App\Models\Vehicle;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Vehicle::query()->with(['vehicleType', 'vehicleStatus']);
        $user = Auth::user();

        // Appliquer le filtre par organisation pour tous sauf Super Admin
        if ($user && !$user->hasRole('Super Admin')) {
            $query->where('organization_id', $user->organization_id);
        }

        // Si l'utilisateur n'est ni Super Admin, ni Admin, restreindre aux véhicules assignés
        if ($user && !$user->hasAnyRole(['Super Admin', 'Admin'])) {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

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

    public function find(int $id): ?Vehicle
    {
        return Vehicle::find($id);
    }

    public function findTrashed(int $id): ?Vehicle
    {
        return Vehicle::onlyTrashed()->find($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(Vehicle $vehicle, array $data): bool
    {
        return $vehicle->update($data);
    }

    public function delete(Vehicle $vehicle): bool
    {
        return $vehicle->delete();
    }

    public function restore(Vehicle $vehicle): bool
    {
        return $vehicle->restore();
    }

    public function forceDelete(Vehicle $vehicle): bool
    {
        return $vehicle->forceDelete();
    }
}