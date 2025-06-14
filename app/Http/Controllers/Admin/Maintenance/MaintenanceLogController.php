<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MaintenanceLog;
use App\Models\Maintenance\MaintenancePlan;
use App\Models\Maintenance\MaintenanceStatus;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    /**
     * Enregistre une nouvelle intervention de maintenance et met à jour le plan associé.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('log maintenance');

        // On récupère le plan pour la validation du kilométrage
        $plan = MaintenancePlan::with('vehicle')->find($request->input('maintenance_plan_id'));

        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'maintenance_type_id' => ['required', 'exists:maintenance_types,id'],
            'maintenance_plan_id' => ['nullable', 'exists:maintenance_plans,id'],
            'performed_on_date' => ['required', 'date'],
            'performed_at_mileage' => ['required', 'integer', 'gte:' . ($plan ? $plan->vehicle->current_mileage : 0)],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'details' => ['nullable', 'string', 'max:2000'],
            'performed_by' => ['nullable', 'string', 'max:255'],
        ]);

        $statusTerminee = MaintenanceStatus::where('name', 'Terminée')->firstOrFail();
        $validated['maintenance_status_id'] = $statusTerminee->id;

        MaintenanceLog::create($validated);

        if ($plan) {
            $recurrenceUnit = $plan->recurrenceUnit;
            $recurrenceValue = $plan->recurrence_value;

            if ($recurrenceUnit->name === 'Kilomètres') {
                $plan->next_due_mileage = $validated['performed_at_mileage'] + $recurrenceValue;
            } elseif ($recurrenceUnit->name === 'Jours') {
                $plan->next_due_date = Carbon::parse($validated['performed_on_date'])->addDays($recurrenceValue);
            } elseif ($recurrenceUnit->name === 'Mois') {
                $plan->next_due_date = Carbon::parse($validated['performed_on_date'])->addMonths($recurrenceValue);
            }
            $plan->save();
        }

        // Mettre à jour le kilométrage principal du véhicule
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($vehicle && $validated['performed_at_mileage'] > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $validated['performed_at_mileage']]);
        }

        return back()->with('success', 'Intervention de maintenance enregistrée avec succès.');
    }
}
