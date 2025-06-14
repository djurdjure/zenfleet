<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MaintenancePlan;
use App\Models\Maintenance\MaintenanceType;
use App\Models\Maintenance\RecurrenceUnit;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;



class MaintenancePlanController extends Controller
{
    /**
     * Affiche la liste des plans de maintenance.
     */

 /**
     * Affiche la liste des plans de maintenance avec filtres.
     */
public function index(Request $request): View
    {
        $this->authorize('manage maintenance plans');

        $query = MaintenancePlan::with(['vehicle', 'maintenanceType', 'recurrenceUnit']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->maintenance_type_id);
        }

        $query->orderBy('next_due_date', 'asc');
        $plans = $query->paginate(15)->withQueryString();

        // On prépare un tableau simple et propre pour la vue
        $plansForJs = $plans->getCollection()->mapWithKeys(function ($plan) {
            return [$plan->id => [
                'id' => $plan->id,
                'vehicle_id' => $plan->vehicle_id,
                'maintenance_type_id' => $plan->maintenance_type_id,
                'recurrence_value' => $plan->recurrence_value,
                'notes' => $plan->notes,
                'next_due_date' => $plan->next_due_date ? $plan->next_due_date->format('Y-m-d') : null,
                'next_due_mileage' => $plan->next_due_mileage,
                'vehicle' => $plan->vehicle ? $plan->vehicle->only('brand', 'model', 'registration_plate', 'current_mileage') : null,
                'maintenance_type' => $plan->maintenanceType ? $plan->maintenanceType->only('name') : null,
                'recurrence_unit' => $plan->recurrenceUnit ? $plan->recurrenceUnit->only('name') : null,
            ]];
        });

        $vehicles = Vehicle::orderBy('brand')->get();
        $maintenanceTypes = MaintenanceType::orderBy('name')->get();
	$recurrenceUnits = RecurrenceUnit::all();


        return view('admin.maintenance.plans.index', [
            'plans' => $plans,
            'plansForJs' => $plansForJs,
            'vehicles' => $vehicles,
            'maintenanceTypes' => $maintenanceTypes,
	    'recurrenceUnits' => $recurrenceUnits,
	    'filters' => $request->only(['vehicle_id', 'maintenance_type_id']),
        ]);
    }


         /**
     * Affiche le formulaire de création de plan de maintenance.
     */
    public function create(): View
    {
        $this->authorize('manage maintenance plans');

        // On passe simplement les données brutes nécessaires aux listes déroulantes.
        $vehicles = Vehicle::whereDoesntHave('maintenancePlans')->orderBy('brand')->get();
        $maintenanceTypes = MaintenanceType::orderBy('name')->get();
        $recurrenceUnits = RecurrenceUnit::all();

        return view('admin.maintenance.plans.create', compact('vehicles', 'maintenanceTypes', 'recurrenceUnits'));
    }

    /**
     * Affiche le formulaire de modification (cette méthode n'est plus utilisée,
     * car l'édition se fait via une modale directement dans la page index).
     * On la garde par convention pour un contrôleur de ressource.
     */
    public function edit(MaintenancePlan $plan): RedirectResponse
    {
        return redirect()->route('admin.maintenance.plans.index');
    }

    /**
     * Met à jour un plan de maintenance existant.
     */
    public function update(Request $request, MaintenancePlan $plan): RedirectResponse
    {
        $this->authorize('manage maintenance plans');

        $validated = $request->validate([
            'recurrence_value' => ['required', 'integer', 'min:1'],
            'recurrence_unit_id' => ['required', 'exists:recurrence_units,id'],
            'next_due_date' => ['nullable', 'date'],
            'next_due_mileage' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $plan->update($validated);

        return back()->with('success', 'Plan de maintenance mis à jour avec succès.');
    }


    /**
     * Enregistre un nouveau plan de maintenance.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage maintenance plans');

        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'maintenance_type_id' => ['required', 'exists:maintenance_types,id'],
            'recurrence_value' => ['required', 'integer', 'min:1'],
            'recurrence_unit_id' => ['required', 'exists:recurrence_units,id'],
            'next_due_date' => ['nullable', 'date'],
            'next_due_mileage' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $planData = $validated;

        // Calcul automatique de la première échéance si non fournie
        if (empty($planData['next_due_date']) && empty($planData['next_due_mileage'])) {
            $vehicle = Vehicle::find($planData['vehicle_id']);
            $recurrenceUnit = RecurrenceUnit::find($planData['recurrence_unit_id']);
            $recurrenceValue = $planData['recurrence_value'];

            if ($recurrenceUnit->name === 'Kilomètres') {
                $planData['next_due_mileage'] = $vehicle->current_mileage + $recurrenceValue;
            } elseif ($recurrenceUnit->name === 'Jours') {
                $planData['next_due_date'] = Carbon::now()->addDays($recurrenceValue);
            } elseif ($recurrenceUnit->name === 'Mois') {
                $planData['next_due_date'] = Carbon::now()->addMonths($recurrenceValue);
            }
        }

        MaintenancePlan::create($planData);

        return redirect()->route('admin.maintenance.plans.index')->with('success', 'Nouveau plan de maintenance ajouté avec succès.');
    }


}
