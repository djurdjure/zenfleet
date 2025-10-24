<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Services\Maintenance\MaintenanceScheduleService;
use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * 🔄 CONTROLLER PLANIFICATIONS MAINTENANCE
 * 
 * Gestion des planifications de maintenance préventive
 * Controller slim pattern - Délègue logique au service
 * 
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 */
class MaintenanceScheduleController extends Controller
{
    protected MaintenanceScheduleService $scheduleService;

    public function __construct(MaintenanceScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
        $this->middleware('auth');
    }

    /**
     * Liste des planifications
     */
    public function index(Request $request)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('viewAny', MaintenanceSchedule::class);

        // Récupérer les planifications avec filtres
        $schedules = MaintenanceSchedule::with(['vehicle', 'maintenanceType'])
            ->when($request->input('vehicle_id'), function ($query, $vehicleId) {
                $query->where('vehicle_id', $vehicleId);
            })
            ->when($request->input('maintenance_type_id'), function ($query, $typeId) {
                $query->where('maintenance_type_id', $typeId);
            })
            ->when($request->input('is_active'), function ($query, $isActive) {
                $query->where('is_active', $isActive === '1');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        // Données pour les filtres
        $vehicles = Vehicle::select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        $maintenanceTypes = MaintenanceType::select('id', 'name', 'category')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('admin.maintenance.schedules.index', compact(
            'schedules',
            'vehicles',
            'maintenanceTypes'
        ));
    }

    /**
     * Formulaire création
     */
    public function create()
    {
        // TODO: Implémenter authorization
        // Gate::authorize('create', MaintenanceSchedule::class);

        $vehicles = Vehicle::select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        $maintenanceTypes = MaintenanceType::select('id', 'name', 'category', 'estimated_cost')
            ->where('is_recurring', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('admin.maintenance.schedules.create', compact(
            'vehicles',
            'maintenanceTypes'
        ));
    }

    /**
     * Enregistrer nouvelle planification
     */
    public function store(Request $request)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('create', MaintenanceSchedule::class);

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'interval_type' => 'required|in:mileage,time,both',
            'interval_value_km' => 'nullable|integer|min:1',
            'interval_value_days' => 'nullable|integer|min:1',
            'last_maintenance_date' => 'nullable|date',
            'last_maintenance_mileage' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['organization_id'] = auth()->user()->organization_id;
            $validated['is_active'] = $request->has('is_active');
            
            $schedule = MaintenanceSchedule::create($validated);

            return redirect()
                ->route('admin.maintenance.schedules.index')
                ->with('success', 'Planification créée avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher détails planification
     */
    public function show(MaintenanceSchedule $schedule)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('view', $schedule);

        $schedule->load([
            'vehicle',
            'maintenanceType',
            'operations' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        return view('admin.maintenance.schedules.show', compact('schedule'));
    }

    /**
     * Formulaire édition
     */
    public function edit(MaintenanceSchedule $schedule)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('update', $schedule);

        $schedule->load(['vehicle', 'maintenanceType']);

        $vehicles = Vehicle::select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        $maintenanceTypes = MaintenanceType::select('id', 'name', 'category')
            ->where('is_recurring', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('admin.maintenance.schedules.edit', compact(
            'schedule',
            'vehicles',
            'maintenanceTypes'
        ));
    }

    /**
     * Mettre à jour planification
     */
    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('update', $schedule);

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'interval_type' => 'required|in:mileage,time,both',
            'interval_value_km' => 'nullable|integer|min:1',
            'interval_value_days' => 'nullable|integer|min:1',
            'last_maintenance_date' => 'nullable|date',
            'last_maintenance_mileage' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['is_active'] = $request->has('is_active');
            $schedule->update($validated);

            return redirect()
                ->route('admin.maintenance.schedules.show', $schedule)
                ->with('success', 'Planification mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer planification
     */
    public function destroy(MaintenanceSchedule $schedule)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('delete', $schedule);

        try {
            $schedule->delete();

            return redirect()
                ->route('admin.maintenance.schedules.index')
                ->with('success', 'Planification supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Activer/Désactiver planification
     */
    public function toggleActive(MaintenanceSchedule $schedule)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('update', $schedule);

        try {
            $schedule->update([
                'is_active' => !$schedule->is_active
            ]);

            $status = $schedule->is_active ? 'activée' : 'désactivée';

            return back()->with('success', "Planification {$status} avec succès.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Créer opérations à partir de planifications dues
     */
    public function createOperations(Request $request)
    {
        // TODO: Implémenter authorization
        // Gate::authorize('create', MaintenanceOperation::class);

        try {
            $scheduleIds = $request->input('schedule_ids', []);
            
            if (empty($scheduleIds)) {
                return back()->with('warning', 'Aucune planification sélectionnée.');
            }

            $count = $this->scheduleService->createOperationsFromSchedules($scheduleIds);

            return back()->with('success', "{$count} opération(s) créée(s) avec succès.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
