<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Supplier\StoreSupplierRequest;
use App\Http\Requests\Admin\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->middleware('auth');

        // ✅ Utiliser authorizeResource pour appliquer automatiquement SupplierPolicy
        $this->authorizeResource(Supplier::class, 'supplier');

        $this->supplierService = $supplierService;
    }

    public function index(Request $request): View
    {
        $this->authorize('view suppliers');
        $filters = $request->only(['search', 'per_page']);
        $suppliers = $this->supplierService->getFilteredSuppliers($filters);
        return view('admin.suppliers.index', compact('suppliers', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create suppliers');

        // La méthode du service retourne déjà un tableau prêt pour la vue.
        $data = $this->supplierService->getDataForCreateForm();

        return view('admin.suppliers.create', $data);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $this->supplierService->createSupplier($request->validated());
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorize('edit suppliers');
        $data = $this->supplierService->getDataForCreateForm();
        $data['supplier'] = $supplier;
        return view('admin.suppliers.edit', $data);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->supplierService->updateSupplier($supplier, $request->validated());
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete suppliers');
        $this->supplierService->archiveSupplier($supplier);
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur archivé avec succès.');
    }
}