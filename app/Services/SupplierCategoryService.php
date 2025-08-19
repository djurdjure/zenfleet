<?php

namespace App\Services;

use App\Models\SupplierCategory;
use Illuminate\Support\Facades\Auth; // <-- AJOUT

class SupplierCategoryService
{
    public function createCategory(array $data): SupplierCategory
    {
        // CORRECTION : On fusionne les données validées avec l'ID de l'organisation de l'utilisateur.
        $dataWithOrg = array_merge($data, [
            'organization_id' => Auth::user()->organization_id
        ]);

        return SupplierCategory::create($dataWithOrg);
    }
}