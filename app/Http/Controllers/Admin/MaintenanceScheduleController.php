<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Models\MaintenanceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur des planifications de maintenance
 */
class MaintenanceScheduleController extends Controller
{
    public function index(): View
    {
        try {
            $organizationId = auth()->user()->organization_id;

            $schedules = MaintenanceSchedule::with([
                'vehicle:id,registration_plate,brand,model',
                'maintenanceType:id,name,category',
                'maintenanceProvider:id,name'
            ])
            ->where('organization_id', $organizationId)
            ->orderBy('next_due_date', 'asc')
            ->paginate(15);

            return view('admin.maintenance.schedules.index', compact('schedules'));

        } catch (\Exception $e) {
            return view('admin.maintenance.schedules.index', [
                'schedules' => collect([]),
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
                                              ->get(['id', 'name', 'category', 'default_interval_km', 'default_interval_days', 'estimated_duration_minutes']);

            // 🚗 Charger les véhicules actifs
            $vehicles = Vehicle::where('organization_id', $organizationId)
                              ->whereNull('deleted_at')
                              ->orderBy('registration_plate')
                              ->get(['id', 'registration_plate', 'brand', 'model', 'current_mileage']);

            // 🏢 Charger les fournisseurs actifs
            $providers = MaintenanceProvider::where('organization_id', $organizationId)
                                           ->where('is_active', true)
                                           ->orderBy('name')
                                           ->get(['id', 'name', 'phone', 'email']);

            return view('admin.maintenance.schedules.create', compact(
                'maintenanceTypes',
                'vehicles',
                'providers'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement du formulaire de création de planification', [
                'error' => $e->getMessage(),
                'organization_id' => auth()->user()?->organization_id
            ]);

            return view('admin.maintenance.schedules.create', [
                'maintenanceTypes' => collect([]),
                'vehicles' => collect([]),
                'providers' => collect([]),
                'error' => 'Une erreur est survenue lors du chargement des données.'
            ]);
        }
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.maintenance.schedules.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function show($id): View
    {
        try {
            $schedule = MaintenanceSchedule::with([
                'vehicle',
                'maintenanceType',
                'maintenanceProvider'
            ])->findOrFail($id);

            // Vérifier que la planification appartient à l'organisation de l'utilisateur
            if ($schedule->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Accès non autorisé');
            }

            return view('admin.maintenance.schedules.show', compact('schedule'));

        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.schedules.index')
                ->with('error', 'Planification introuvable.');
        }
    }

    public function edit($id): View
    {
        try {
            $organizationId = auth()->user()->organization_id;

            $schedule = MaintenanceSchedule::findOrFail($id);

            // Vérifier que la planification appartient à l'organisation de l'utilisateur
            if ($schedule->organization_id !== $organizationId) {
                abort(403, 'Accès non autorisé');
            }

            // Charger les données pour les selects
            $maintenanceTypes = MaintenanceType::where('organization_id', $organizationId)
                                              ->where('is_active', true)
                                              ->orderBy('category')
                                              ->orderBy('name')
                                              ->get(['id', 'name', 'category', 'default_interval_km', 'default_interval_days']);

            $vehicles = Vehicle::where('organization_id', $organizationId)
                              ->whereNull('deleted_at')
                              ->orderBy('registration_plate')
                              ->get(['id', 'registration_plate', 'brand', 'model', 'current_mileage']);

            $providers = MaintenanceProvider::where('organization_id', $organizationId)
                                           ->where('is_active', true)
                                           ->orderBy('name')
                                           ->get(['id', 'name', 'phone']);

            return view('admin.maintenance.schedules.edit', compact(
                'schedule',
                'maintenanceTypes',
                'vehicles',
                'providers'
            ));

        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.schedules.index')
                ->with('error', 'Planification introuvable.');
        }
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.maintenance.schedules.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.maintenance.schedules.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }
}