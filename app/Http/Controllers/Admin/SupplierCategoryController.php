<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierCategory\StoreSupplierCategoryRequest;
use App\Services\SupplierCategoryService;
use Illuminate\Http\JsonResponse;

class SupplierCategoryController extends Controller
{
    protected $supplierCategoryService;

    public function __construct(SupplierCategoryService $supplierCategoryService)
    {
        $this->supplierCategoryService = $supplierCategoryService;
    }

    public function store(StoreSupplierCategoryRequest $request): JsonResponse
    {
        $category = $this->supplierCategoryService->createCategory($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Catégorie ajoutée avec succès.',
            'category' => $category
        ]);
    }
}
