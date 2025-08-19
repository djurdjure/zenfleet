<?php

namespace App\Services;

use App\Models\SupplierCategory;

class SupplierCategoryService
{
    public function createCategory(array $data): SupplierCategory
    {
        return SupplierCategory::create($data);
    }
}
