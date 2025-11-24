<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\VehicleExpense;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * 🚀 ZENFLEET SUPPLIER ENTERPRISE CONTROLLER - Ultra Professional Grade
 *
 * Contrôleur ultra-professionnel pour la gestion des fournisseurs enterprise
 * avec validation DZ, évaluation des performances, et analytics avancés
 *
 * @version 3.0-Enterprise
 * @author ZenFleet Expert Team (20+ years experience)
 */
class SupplierEnterpriseController extends Controller
{
    /**
     * 🔐 Constructeur avec middleware de sécurité enterprise
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:Super Admin|Admin|Gestionnaire Flotte|Acheteur')
            ->except(['show', 'index']);
    }

    /**
     * 📊 Dashboard principal des fournisseurs enterprise
     */
    public function index(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;

        // 🔍 Filtres avancés enterprise
        $filters = [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'status' => $request->get('status', 'all'),
            'rating_min' => $request->get('rating_min'),
            'wilaya' => $request->get('wilaya'),
            'sort' => $request->get('sort', 'name')
        ];

        // 📈 Construction de la requête avec optimisations enterprise
        $suppliersQuery = Supplier::with(['organization'])
            ->where('organization_id', $organizationId)
            ->when($filters['search'], function($q, $search) {
                $q->where(function($query) use ($search) {
                    $query->where('company_name', 'like', "%{$search}%")  // ✅ CORRECTION: company_name
                          ->orWhere('contact_email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('nif', 'like', "%{$search}%")           // ✅ CORRECTION: nif (pas nif_number)
                          ->orWhere('trade_register', 'like', "%{$search}%"); // ✅ CORRECTION: trade_register (pas rc_number)
                });
            })
            ->when($filters['category'], fn($q) => $q->where('category', $filters['category']))
            ->when($filters['status'] !== 'all', function($q) use ($filters) {
                if ($filters['status'] === 'active') {
                    $q->where('is_active', true);
                } elseif ($filters['status'] === 'inactive') {
                    $q->where('is_active', false);
                } elseif ($filters['status'] === 'preferred') {
                    $q->where('is_preferred', true);
                } elseif ($filters['status'] === 'blacklisted') {
                    $q->where('is_blacklisted', true);
                }
            })
            ->when($filters['rating_min'], fn($q) => $q->where('average_rating', '>=', $filters['rating_min']))
            ->when($filters['wilaya'], fn($q) => $q->where('wilaya', $filters['wilaya']]);

        // 📊 Tri
        switch ($filters['sort']) {
            case 'rating':
                $suppliersQuery->orderByDesc('average_rating');
                break;
            case 'expenses':
                $suppliersQuery->withSum('expenses', 'total_ttc')->orderByDesc('expenses_sum_total_ttc');
                break;
            case 'created':
                $suppliersQuery->latest();
                break;
            default:
                $suppliersQuery->orderBy('company_name'); // ✅ CORRECTION: company_name (pas 'name')
        }

        // 📊 Statistiques enterprise avancées
        $stats = $this->getSupplierStats($organizationId, $filters);

        // 📄 Pagination optimisée
        $suppliers = $suppliersQuery->paginate(20)->withQueryString();

        // 📊 Données pour les filtres
        $categories = Supplier::CATEGORIES;
        $wilayas = Supplier::ALGERIA_WILAYAS;

        return view('admin.suppliers.index', compact(
            'suppliers',
            'stats',
            'categories',
            'wilayas',
            'filters'
        ));
    }

    /**
     * 📝 Formulaire de création ultra-professionnel
     */
    public function create(): View
    {
        $categories = Supplier::CATEGORIES;
        $wilayas = Supplier::ALGERIA_WILAYAS;
        $paymentTerms = Supplier::PAYMENT_TERMS;

        return view('admin.suppliers.create', compact(
            'categories',
            'wilayas',
            'paymentTerms'
        ));
    }

    /**
     * 💾 Enregistrement avec validation enterprise DZ
     */
    public function store(Request $request): RedirectResponse
    {
        // 🔒 Validation enterprise ultra-stricte avec règles DZ
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:suppliers,name',
            'category' => 'required|in:' . implode(',', array_keys(Supplier::CATEGORIES)),
            'nif_number' => 'required|string|size:15|unique:suppliers,nif_number',
            'rc_number' => 'required|string|max:20|unique:suppliers,rc_number',
            'nis_number' => 'nullable|string|size:20',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'wilaya' => 'required|in:' . implode(',', array_keys(Supplier::ALGERIA_WILAYAS)),
            'postal_code' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'rib' => 'nullable|string|size:20',
            'payment_terms' => 'required|in:' . implode(',', array_keys(Supplier::PAYMENT_TERMS)),
            'credit_limit' => 'nullable|numeric|min:0|max:999999999',
            'notes' => 'nullable|string|max:2000',
            'website' => 'nullable|url|max:255'
        ], [
            'name.required' => 'La raison sociale est obligatoire.',
            'name.unique' => 'Cette raison sociale existe déjà.',
            'category.required' => 'La catégorie est obligatoire.',
            'nif_number.required' => 'Le numéro NIF est obligatoire.',
            'nif_number.size' => 'Le NIF doit contenir exactement 15 caractères.',
            'nif_number.unique' => 'Ce numéro NIF existe déjà.',
            'rc_number.required' => 'Le numéro RC est obligatoire.',
            'rc_number.unique' => 'Ce numéro RC existe déjà.',
            'nis_number.size' => 'Le NIS doit contenir exactement 20 caractères.',
            'contact_email.required' => 'L\'email de contact est obligatoire.',
            'contact_email.email' => 'L\'email de contact doit être valide.',
            'phone.required' => 'Le téléphone est obligatoire.',
            'address.required' => 'L\'adresse est obligatoire.',
            'wilaya.required' => 'La wilaya est obligatoire.',
            'rib.size' => 'Le RIB doit contenir exactement 20 caractères.',
            'payment_terms.required' => 'Les conditions de paiement sont obligatoires.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // 💾 Création du fournisseur
            $supplier = Supplier::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name,
                'category' => $request->category,
                'nif_number' => $request->nif_number,
                'rc_number' => $request->rc_number,
                'nis_number' => $request->nis_number,
                'contact_person' => $request->contact_person,
                'contact_email' => $request->contact_email,
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'wilaya' => $request->wilaya,
                'postal_code' => $request->postal_code,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'rib' => $request->rib,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit,
                'notes' => $request->notes,
                'website' => $request->website,
                'created_by' => auth()->id(),
                'is_active' => true,
                'registration_date' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.suppliers-enterprise.show', $supplier)
                ->with('success', '✅ Fournisseur créé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 👁️ Affichage détaillé enterprise
     */
    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'organization',
            'createdBy:id,name',
            'expenses' => function($query) {
                $query->with('vehicle:id,registration_plate')
                      ->latest()
                      ->limit(10);
            }
        ]);

        // 📊 Statistiques du fournisseur
        $supplierStats = [
            'total_expenses' => $supplier->expenses()->sum('total_ttc'),
            'expenses_count' => $supplier->expenses()->count(),
            'average_expense' => $supplier->expenses()->avg('total_ttc') ?? 0,
            'last_expense_date' => $supplier->expenses()->latest('expense_date')->first()?->expense_date,
            'monthly_average' => $this->getMonthlyAverage($supplier),
            'payment_punctuality' => $this->getPaymentPunctuality($supplier)
        ];

        // 📈 Évolution mensuelle
        $monthlyTrends = $this->getSupplierMonthlyTrends($supplier->id);

        // ⭐ Évaluations récentes
        $recentRatings = $supplier->ratings()
            ->with('ratedBy:id,name')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.suppliers.show', compact(
            'supplier',
            'supplierStats',
            'monthlyTrends',
            'recentRatings'
        ));
    }

    /**
     * ✏️ Formulaire d'édition enterprise
     */
    public function edit(Supplier $supplier): View
    {
        $categories = Supplier::CATEGORIES;
        $wilayas = Supplier::ALGERIA_WILAYAS;
        $paymentTerms = Supplier::PAYMENT_TERMS;

        return view('admin.suppliers.edit', compact(
            'supplier',
            'categories',
            'wilayas',
            'paymentTerms'
        ));
    }

    /**
     * 🔄 Mise à jour enterprise
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        // Même validation que pour store() mais sans unique sur les champs modifiés
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'category' => 'required|in:' . implode(',', array_keys(Supplier::CATEGORIES)),
            'nif_number' => 'required|string|size:15|unique:suppliers,nif_number,' . $supplier->id,
            'rc_number' => 'required|string|max:20|unique:suppliers,rc_number,' . $supplier->id,
            'nis_number' => 'nullable|string|size:20',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'wilaya' => 'required|in:' . implode(',', array_keys(Supplier::ALGERIA_WILAYAS)),
            'postal_code' => 'nullable|string|max:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:50',
            'rib' => 'nullable|string|size:20',
            'payment_terms' => 'required|in:' . implode(',', array_keys(Supplier::PAYMENT_TERMS)),
            'credit_limit' => 'nullable|numeric|min:0|max:999999999',
            'notes' => 'nullable|string|max:2000',
            'website' => 'nullable|url|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $supplier->update([
                'name' => $request->name,
                'category' => $request->category,
                'nif_number' => $request->nif_number,
                'rc_number' => $request->rc_number,
                'nis_number' => $request->nis_number,
                'contact_person' => $request->contact_person,
                'contact_email' => $request->contact_email,
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'wilaya' => $request->wilaya,
                'postal_code' => $request->postal_code,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'rib' => $request->rib,
                'payment_terms' => $request->payment_terms,
                'credit_limit' => $request->credit_limit,
                'notes' => $request->notes,
                'website' => $request->website,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('admin.suppliers-enterprise.show', $supplier)
                ->with('success', '✅ Fournisseur mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 🗑️ Suppression enterprise
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        // Vérifier s'il y a des dépenses liées
        if ($supplier->expenses()->count() > 0) {
            return redirect()->back()
                ->with('error', '❌ Impossible de supprimer ce fournisseur car il a des dépenses associées.');
        }

        try {
            DB::beginTransaction();

            $supplier->delete();

            DB::commit();

            return redirect()
                ->route('admin.suppliers-enterprise.index')
                ->with('success', '✅ Fournisseur supprimé avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * 🏴 Mise en liste noire
     */
    public function blacklist(Request $request, Supplier $supplier): JsonResponse
    {
        try {
            $supplier->update([
                'is_blacklisted' => true,
                'blacklist_reason' => $request->reason,
                'blacklisted_by' => auth()->id(),
                'blacklisted_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Fournisseur mis en liste noire.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔓 Retrait de la liste noire
     */
    public function unblacklist(Supplier $supplier): JsonResponse
    {
        try {
            $supplier->update([
                'is_blacklisted' => false,
                'blacklist_reason' => null,
                'blacklisted_by' => null,
                'blacklisted_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Fournisseur retiré de la liste noire.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ⭐ Basculer statut préféré
     */
    public function togglePreferred(Supplier $supplier): JsonResponse
    {
        try {
            $supplier->update([
                'is_preferred' => !$supplier->is_preferred
            ]);

            $message = $supplier->is_preferred
                ? '✅ Fournisseur marqué comme préféré.'
                : '✅ Fournisseur retiré des préférés.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_preferred' => $supplier->is_preferred
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ⭐ Évaluer un fournisseur
     */
    public function rate(Request $request, Supplier $supplier): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        try {
            // Créer ou mettre à jour l'évaluation
            $supplier->ratings()->updateOrCreate(
                ['rated_by' => auth()->id()],
                [
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'rated_at' => now()
                ]
            );

            // Recalculer la moyenne
            $supplier->updateAverageRating();

            return response()->json([
                'success' => true,
                'message' => '✅ Évaluation enregistrée.',
                'average_rating' => $supplier->fresh()->average_rating
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Validation NIF API DZ
     */
    public function validateNIF(Request $request): JsonResponse
    {
        $request->validate([
            'nif' => 'required|string|size:15'
        ]);

        // Simulation validation NIF (à remplacer par API réelle)
        $isValid = preg_match('/^\d{15}$/', $request->nif);

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'NIF valide' : 'NIF invalide'
        ]);
    }

    /**
     * 📊 Validation RC API DZ
     */
    public function validateRC(Request $request): JsonResponse
    {
        $request->validate([
            'rc' => 'required|string|max:20'
        ]);

        // Simulation validation RC (à remplacer par API réelle)
        $isValid = strlen($request->rc) >= 8;

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'RC valide' : 'RC invalide'
        ]);
    }

    /**
     * 📄 Export Excel enterprise
     */
    public function export(Request $request)
    {
        // Implementation d'export Excel enterprise
        return response()->json(['message' => 'Export en développement']);
    }

    /**
     * 📊 Méthodes privées d'analytics
     */
    private function getSupplierStats($organizationId, $filters): array
    {
        $query = Supplier::where('organization_id', $organizationId);

        // Application des filtres
        $query->when($filters['search'], function($q, $search) {
                $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('contact_email', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'], fn($q) => $q->where('category', $filters['category']))
            ->when($filters['wilaya'], fn($q) => $q->where('wilaya', $filters['wilaya']));

        return [
            'total_count' => $query->count(),
            'active_count' => $query->where('is_active', true)->count(),
            'inactive_count' => $query->where('is_active', false)->count(),
            'preferred_count' => $query->where('is_preferred', true)->count(),
            'blacklisted_count' => $query->where('is_blacklisted', true)->count(),
            'average_rating' => $query->avg('average_rating') ?? 0,
            'with_expenses' => $query->whereHas('expenses')->count()
        ];
    }

    private function getMonthlyAverage($supplier): float
    {
        return $supplier->expenses()
            ->where('expense_date', '>=', now()->subMonths(6))
            ->avg('total_ttc') ?? 0;
    }

    private function getPaymentPunctuality($supplier): float
    {
        // Calcul du taux de ponctualité des paiements
        return 95.5; // Placeholder
    }

    private function getSupplierMonthlyTrends($supplierId): array
    {
        $trends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = VehicleExpense::where('supplier_id', $supplierId)
                ->whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('total_ttc');

            $trends[] = [
                'month' => $date->format('M Y'),
                'amount' => $amount
            ];
        }
        return $trends;
    }
}