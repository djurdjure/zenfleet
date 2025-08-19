<?php

namespace App\Repositories\Eloquent;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Supplier::query()->with('category');

        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_name', 'ILIKE', $searchTerm)
                  ->orWhere('email', 'ILIKE', $searchTerm);
            });
        }

        return $query->orderBy('name', 'asc')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        return $supplier->update($data);
    }

    public function archive(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}