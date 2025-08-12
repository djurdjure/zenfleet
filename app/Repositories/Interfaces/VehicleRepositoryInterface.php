<?php
namespace App\Repositories\Interfaces;

use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator;
    public function find(int $id): ?Vehicle;
    public function findTrashed(int $id): ?Vehicle;
    public function create(array $data): Vehicle;
    public function update(Vehicle $vehicle, array $data): bool;
    public function delete(Vehicle $vehicle): bool;
    public function restore(Vehicle $vehicle): bool;
    public function forceDelete(Vehicle $vehicle): bool;
}