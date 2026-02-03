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
        $this->authorize('suppliers.view');
        
        // Filtres avancés
        $filters = $request->only([
            'search', 
            'supplier_type', 
            'category_id', 
            'wilaya', 
            'is_active', 
            'is_preferred', 
            'is_certified', 
            'min_rating',
            'sort_by',
            'sort_direction',
            'per_page'
        ]);
        
        // Récupérer fournisseurs avec filtres avancés
        $suppliers = $this->supplierService->getFilteredSuppliersAdvanced(
            $filters,
            $request->input('per_page', 15)
        );
        
        // Analytics enrichies
        $analytics = $this->supplierService->getAnalytics($filters);
        
        // Données pour les filtres
        $categories = \App\Models\SupplierCategory::orderBy('name')->get();
        $types = Supplier::TYPES;
        $wilayas = Supplier::WILAYAS;
        
        return view('admin.suppliers.index', compact(
            'suppliers', 
            'analytics',
            'filters',
            'categories',
            'types',
            'wilayas'
        ));
    }

    public function create(): View
    {
        $this->authorize('suppliers.create');

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
        $this->authorize('suppliers.update');
        $data = $this->supplierService->getDataForCreateForm();
        $data['supplier'] = $supplier;
        return view('admin.suppliers.edit', $data);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->supplierService->updateSupplier($supplier, $request->validated());
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('suppliers.view');
        
        // Récupérer les données enrichies du fournisseur
        $supplier->load(['category']);
        
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('suppliers.delete');
        $this->supplierService->archiveSupplier($supplier);
        return redirect()->route('admin.suppliers.index')->with('success', 'Fournisseur archivé avec succès.');
    }

    /**
     * Export des fournisseurs (CSV/Excel)
     */
    public function export(Request $request)
    {
        $this->authorize('suppliers.view');
        
        $filters = $request->only([
            'search', 'supplier_type', 'category_id', 'wilaya', 
            'is_active', 'is_preferred', 'is_certified', 'min_rating'
        ]);
        
        $suppliers = $this->supplierService->getFilteredSuppliersAdvanced($filters, 999999);
        
        $filename = 'fournisseurs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($suppliers) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Raison Sociale',
                'Type',
                'Contact Prénom',
                'Contact Nom',
                'Téléphone',
                'Email',
                'Wilaya',
                'Ville',
                'Adresse',
                'Rating',
                'Score Qualité',
                'Score Fiabilité',
                'Actif',
                'Préféré',
                'Certifié',
            ], ';');
            
            // Données
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->company_name,
                    \App\Models\Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type,
                    $supplier->contact_first_name,
                    $supplier->contact_last_name,
                    $supplier->contact_phone,
                    $supplier->contact_email,
                    \App\Models\Supplier::WILAYAS[$supplier->wilaya] ?? $supplier->wilaya,
                    $supplier->city,
                    $supplier->address,
                    $supplier->rating,
                    $supplier->quality_score,
                    $supplier->reliability_score,
                    $supplier->is_active ? 'Oui' : 'Non',
                    $supplier->is_preferred ? 'Oui' : 'Non',
                    $supplier->is_certified ? 'Oui' : 'Non',
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
