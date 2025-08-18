<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierCategory; // <-- AJOUT
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    protected SupplierRepositoryInterface $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    /**
     * Récupère les fournisseurs pour la page d'index via le repository.
     */
    public function getFilteredSuppliers(array $filters): LengthAwarePaginator
    {
        return $this->supplierRepository->getFiltered($filters);
    }

    /**
     * Récupère les données nécessaires pour les formulaires de création/modification.
     */
    /**
     * Récupère les données nécessaires pour les formulaires de création/modification.
     */
    public function getDataForCreateForm(): array
    {
        // CORRECTION : On retourne un simple tableau avec les données.
        return [
            'categories' => SupplierCategory::orderBy('name')->get(),
        ];
    }

    /**
     * Gère la création d'un fournisseur.
     */
    public function createSupplier(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    /**
     * Gère la mise à jour d'un fournisseur.
     */
    public function updateSupplier(Supplier $supplier, array $data): bool
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    /**
     * Gère l'archivage d'un fournisseur.
     */
    public function archiveSupplier(Supplier $supplier): bool
    {
        return $this->supplierRepository->archive($supplier);
    }
}
