<?php
namespace App\Repositories\Interfaces;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;

interface DriverRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator;
    public function create(array $data): Driver;
    public function update(Driver $driver, array $data): bool;
    // ... (d'autres méthodes si nécessaire)
}
