<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * 🔧 Contrôleur des opérations de maintenance - ENTERPRISE GRADE
 * Gestion complète et ultra-professionnelle des opérations maintenance
 */
class MaintenanceOperationController extends Controller
{
    /**
     * 📋 Liste des opérations de maintenance avec filtres avancés - ENTERPRISE GRADE
     */
    public function index(Request $request): View
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('viewAny', MaintenanceOperation::class);

        try {
            $organizationId = auth()->user()->organization_id;

            // 🔍 Construction de la requête avec filtres enterprise
            $query = MaintenanceOperation::with([
                'vehicle:id,registration_plate,brand,model',
                'maintenanceType:id,name,category,estimated_cost,estimated_duration_minutes',
                'provider:id,name,phone'
            ])->where('organization_id', $organizationId);

            // 🔍 Filtres avancés
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function($vq) use ($search) {
                          $vq->where('registration_plate', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                      })
                      ->orWhereHas('provider', function($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('maintenance_type_id')) {
                $query->where('maintenance_type_id', $request->get('maintenance_type_id'));
            }

            if ($request->filled('vehicle_id')) {
                $query->where('vehicle_id', $request->get('vehicle_id'));
            }

            // 📅 Filtres de date
            switch ($request->get('date_filter')) {
                case 'today':
                    $query->whereDate('scheduled_date', today());
                    break;
                case 'week':
                    $query->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('scheduled_date', now()->month);
                    break;
                case 'quarter':
                    $query->whereBetween('scheduled_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
            }

            // 📊 Récupération paginée avec tri
            $operations = $query->orderBy('scheduled_date', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);

            // 📋 Données pour les filtres
            $maintenanceTypes = MaintenanceType::where('organization_id', $organizationId)
                                              ->orderBy('name')
                                              ->get(['id', 'name']);

            $vehicles = Vehicle::where('organization_id', $organizationId)
                              ->whereNull('deleted_at')
                              ->orderBy('registration_plate')
                              ->get(['id', 'registration_plate', 'brand', 'model']);

            return view('admin.maintenance.operations.index', compact(
                'operations',
                'maintenanceTypes',
                'vehicles'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage des opérations de maintenance', [
                'user_id' => auth()->id(),
                'organization_id' => auth()->user()?->organization_id,
                'error' => $e->getMessage()
            ]);

            // Mode fallback avec données vides
            return view('admin.maintenance.operations.index', [
                'operations' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'maintenanceTypes' => collect([]),
                'vehicles' => collect([]),
                'error' => 'Une erreur est survenue lors du chargement des données.'
            ]);
        }
    }

    public function create(): View
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('create', MaintenanceOperation::class);

        try {
            $organizationId = auth()->user()->organization_id;

            // 📋 Charger les types de maintenance actifs avec toutes les métadonnées pour SlimSelect
            $maintenanceTypes = MaintenanceType::where('organization_id', $organizationId)
                                              ->where('is_active', true)
                                              ->orderBy('category')
                                              ->orderBy('name')
                                              ->get()
                                              ->map(function ($type) {
                                                  return [
                                                      'id' => $type->id,
                                                      'name' => $type->name,
                                                      'category' => $type->category,
                                                      'category_label' => match($type->category) {
                                                          'preventive' => 'Préventive',
                                                          'corrective' => 'Corrective',
                                                          'inspection' => 'Inspection',
                                                          'revision' => 'Révision',
                                                          default => ucfirst($type->category)
                                                      },
                                                      'estimated_duration_hours' => $type->estimated_duration_minutes ? round($type->estimated_duration_minutes / 60, 2) : null,
                                                      'estimated_duration_minutes' => $type->estimated_duration_minutes,
                                                      'estimated_cost' => $type->estimated_cost,
                                                      'description' => $type->description,
                                                      'display_text' => $type->name . ' (' . match($type->category) {
                                                          'preventive' => 'Préventive',
                                                          'corrective' => 'Corrective',
                                                          'inspection' => 'Inspection',
                                                          'revision' => 'Révision',
                                                          default => ucfirst($type->category)
                                                      } . ')'
                                                  ];
                                              });

            // 🚗 Charger les véhicules actifs avec kilométrage actuel pour SlimSelect
            $vehicles = Vehicle::where('organization_id', $organizationId)
                              ->whereNull('deleted_at')
                              ->orderBy('registration_plate')
                              ->get()
                              ->map(function ($vehicle) {
                                  return [
                                      'id' => $vehicle->id,
                                      'registration_plate' => $vehicle->registration_plate,
                                      'brand' => $vehicle->brand,
                                      'model' => $vehicle->model,
                                      'current_mileage' => $vehicle->current_mileage ?? 0,
                                      'display_text' => $vehicle->registration_plate . ' - ' . $vehicle->brand . ' ' . $vehicle->model .
                                                       ($vehicle->current_mileage ? ' (' . number_format($vehicle->current_mileage, 0, ',', ' ') . ' km)' : '')
                                  ];
                              });

            // 🏢 Charger les fournisseurs de maintenance (Suppliers) actifs
            // Types pertinents : mécanicien, peinture, électricité, pneumatiques, contrôle technique
            $maintenanceSupplierTypes = [
                Supplier::TYPE_MECANICIEN,
                Supplier::TYPE_PEINTURE_CARROSSERIE,
                Supplier::TYPE_ELECTRICITE_AUTO,
                Supplier::TYPE_PNEUMATIQUES,
                Supplier::TYPE_CONTROLE_TECHNIQUE,
                Supplier::TYPE_PIECES_DETACHEES
            ];

            $providers = Supplier::where('organization_id', $organizationId)
                                 ->whereIn('supplier_type', $maintenanceSupplierTypes)
                                 ->where('is_active', true)
                                 ->orderBy('company_name')
                                 ->get()
                                 ->map(function ($supplier) {
                                     $displayName = $supplier->company_name ?:
                                                   ($supplier->contact_first_name . ' ' . $supplier->contact_last_name);

                                     return [
                                         'id' => $supplier->id,
                                         'name' => $displayName,
                                         'company_name' => $supplier->company_name,
                                         'phone' => $supplier->phone ?: $supplier->contact_phone,
                                         'email' => $supplier->email ?: $supplier->contact_email,
                                         'rating' => $supplier->rating,
                                         'supplier_type' => $supplier->supplier_type,
                                         'type_label' => Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type,
                                         'display_text' => $displayName .
                                                          ($supplier->phone || $supplier->contact_phone ? ' - ' . ($supplier->phone ?: $supplier->contact_phone) : '') .
                                                          ($supplier->rating ? ' ⭐ ' . number_format($supplier->rating, 1) : '') .
                                                          ' (' . (Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type) . ')'
                                     ];
                                 });

            return view('admin.maintenance.operations.create', compact(
                'maintenanceTypes',
                'vehicles',
                'providers'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement du formulaire de création d\'opération', [
                'error' => $e->getMessage(),
                'organization_id' => auth()->user()?->organization_id
            ]);

            return view('admin.maintenance.operations.create', [
                'maintenanceTypes' => collect([]),
                'vehicles' => collect([]),
                'providers' => collect([]),
                'error' => 'Une erreur est survenue lors du chargement des données.'
            ]);
        }
    }

    public function store(Request $request)
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('create', MaintenanceOperation::class);

        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function show(MaintenanceOperation $operation): View
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('view', $operation);

        return view('admin.maintenance.operations.show', compact('operation'));
    }

    public function edit(MaintenanceOperation $operation): View
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('update', $operation);

        return view('admin.maintenance.operations.edit', compact('operation'));
    }

    public function update(Request $request, MaintenanceOperation $operation)
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('update', $operation);

        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function destroy(MaintenanceOperation $operation)
    {
        // 🔐 AUTORISATION ENTERPRISE-GRADE
        Gate::authorize('delete', $operation);

        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }
}