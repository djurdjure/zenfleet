<?php
namespace App\Repositories\Interfaces;
use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator;
    public function create(array $data): Vehicle;
    public function update(Vehicle $vehicle, array $data): bool;
    // Ajoutez d'autres méthodes si nécessaire
}
