<?php

namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator;
    public function create(array $data): Supplier;
    public function update(Supplier $supplier, array $data): bool;
}
