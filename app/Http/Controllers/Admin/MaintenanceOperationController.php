<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Models\MaintenanceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

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
        try {
            $organizationId = auth()->user()->organization_id;

            // 📋 Charger les types de maintenance actifs
            $maintenanceTypes = MaintenanceType::where('organization_id', $organizationId)
                                              ->where('is_active', true)
                                              ->orderBy('category')
                                              ->orderBy('name')
                                              ->get(['id', 'name', 'category', 'estimated_duration_minutes', 'estimated_cost']);

            // 🚗 Charger les véhicules actifs
            $vehicles = Vehicle::where('organization_id', $organizationId)
                              ->whereNull('deleted_at')
                              ->orderBy('registration_plate')
                              ->get(['id', 'registration_plate', 'brand', 'model']);

            // 🏢 Charger les fournisseurs actifs
            $providers = MaintenanceProvider::where('organization_id', $organizationId)
                                           ->where('is_active', true)
                                           ->orderBy('name')
                                           ->get(['id', 'name', 'phone', 'email']);

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
        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function show($id): View
    {
        return view('admin.maintenance.operations.show');
    }

    public function edit($id): View
    {
        return view('admin.maintenance.operations.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.maintenance.operations.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }
}