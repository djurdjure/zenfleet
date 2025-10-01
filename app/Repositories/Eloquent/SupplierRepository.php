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
        $query = Supplier::query();

        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('company_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_first_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_last_name', 'ILIKE', $searchTerm)
                  ->orWhere('contact_email', 'ILIKE', $searchTerm);
            });
        }

        return $query->orderBy('company_name', 'asc')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): Supplier
    {
        // Ajouter l'organization_id automatiquement si pas présent
        if (!isset($data['organization_id'])) {
            $data['organization_id'] = auth()->user()->organization_id ?? 1;
        }

        // Gérer les checkboxes
        $data['is_active'] = isset($data['is_active']) ? true : false;
        $data['is_preferred'] = isset($data['is_preferred']) ? true : false;
        $data['is_certified'] = isset($data['is_certified']) ? true : false;

        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): bool
    {
        // Gérer les checkboxes
        $data['is_active'] = isset($data['is_active']) ? true : false;
        $data['is_preferred'] = isset($data['is_preferred']) ? true : false;
        $data['is_certified'] = isset($data['is_certified']) ? true : false;

        return $supplier->update($data);
    }

    public function archive(Supplier $supplier): bool
    {
        return $supplier->delete();
    }
}