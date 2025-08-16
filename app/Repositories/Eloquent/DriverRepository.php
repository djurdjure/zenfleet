<?php
namespace App\Repositories\Eloquent;

use App\Models\Driver;
use App\Repositories\Interfaces\DriverRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class DriverRepository implements DriverRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Driver::query()->with(['driverStatus', 'user']);

        if (!empty($filters['view_deleted'])) {
            $query->onlyTrashed();
        }
        if (!empty($filters['status_id'])) {
            $query->where('status_id', $filters['status_id']);
        }
        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(employee_number) LIKE ?', ["%{$searchTerm}%"]);
            });
        }
        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Driver { return Driver::find($id); }
    public function findTrashed(int $id): ?Driver { return Driver::onlyTrashed()->find($id); }
    public function create(array $data): Driver { return Driver::create($data); }
    public function update(Driver $driver, array $data): bool { return $driver->update($data); }
    public function delete(Driver $driver): bool { return $driver->delete(); }
    public function restore(Driver $driver): bool { return $driver->restore(); }
    public function forceDelete(Driver $driver): bool { return $driver->forceDelete(); }
}
