<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Maintenance\MaintenancePlan;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Données pour le Graphique d'État de la Flotte ---
        $vehicleStats = Vehicle::query()
            ->join('vehicle_statuses', 'vehicles.status_id', '=', 'vehicle_statuses.id')
            ->select('vehicle_statuses.name', DB::raw('count(vehicles.id) as count'))
            ->groupBy('vehicle_statuses.name')
            ->pluck('count', 'name');

        // --- Données pour les Jauges d'Urgence de Maintenance ---
        $urgentPlans = MaintenancePlan::with('vehicle', 'maintenanceType')
            ->where(function ($query) {
                // Échéance par date dans les 45 prochains jours ou dépassée
                $query->where('next_due_date', '<=', Carbon::now()->addDays(45));
            })
            ->orWhere(function ($query) {
                // Échéance par kilométrage proche (à 2000 km ou moins) ou dépassée
                $query->whereNotNull('next_due_mileage')
                      ->whereHas('vehicle', function($vehicleQuery) {
                          $vehicleQuery->whereRaw('maintenance_plans.next_due_mileage - vehicles.current_mileage <= 2000');
                      });
            })
            ->whereHas('vehicle', fn($q) => $q->whereNull('deleted_at')) // Uniquement pour les véhicules actifs
            ->orderBy('next_due_date', 'asc')
            ->limit(6) // On limite à 6 pour un affichage clair
            ->get()
            ->map(function ($plan) {
                // Calcul du pourcentage d'urgence pour la jauge
                $urgencyPercent = 0;
                if ($plan->next_due_mileage && $plan->vehicle && $plan->vehicle->current_mileage > 0) {
                    $urgencyPercent = round(($plan->vehicle->current_mileage / $plan->next_due_mileage) * 100);
                }
                // La logique de date pourrait être plus complexe, on se base sur le kilométrage pour la jauge

                return [
                    'id' => $plan->id,
                    'vehicle_name' => $plan->vehicle->brand . ' ' . $plan->vehicle->model,
                    'plate' => $plan->vehicle->registration_plate,
                    'maintenance_type' => $plan->maintenanceType->name,
                    'urgency_percent' => min($urgencyPercent, 100), // Plafonner à 100%
                    'next_due' => $plan->next_due_mileage ? number_format($plan->next_due_mileage, 0, ',', ' ') . ' km' : $plan->next_due_date->format('d/m/Y'),
                ];
            });

        return view('admin.maintenance.dashboard', [
            'vehicleStats' => $vehicleStats,
            'urgentPlans' => $urgentPlans,
        ]);
    }
}
