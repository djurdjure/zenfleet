<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceType;
use App\Models\RecurrenceUnit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MaintenancePlanController extends Controller
{
    /**
     * 📋 MAINTENANCE PLANS CONTROLLER - VERSION ENTERPRISE
     * Gestion complète des plans de maintenance préventive
     */

    /**
     * 📊 Afficher la liste des plans de maintenance avec filtres avancés
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('manage maintenance plans');
        
        try {
            $organizationId = Auth::user()->organization_id;
            
            // Query de base avec relations optimisées
            $query = MaintenancePlan::with(['vehicle', 'maintenanceType', 'recurrenceUnit'])
                ->when($organizationId, function ($query) use ($organizationId) {
                    $query->whereHas('vehicle', function ($q) use ($organizationId) {
                        $q->where('organization_id', $organizationId);
                    });
                });

            // Filtres avancés
            if ($request->filled('vehicle_id')) {
                $query->where('vehicle_id', $request->vehicle_id);
            }

            if ($request->filled('maintenance_type_id')) {
                $query->where('maintenance_type_id', $request->maintenance_type_id);
            }
            
            if ($request->filled('status')) {
                $status = $request->status;
                $query->where(function ($q) use ($status) {
                    switch ($status) {
                        case 'overdue':
                            $q->where('next_due_date', '<', now())
                              ->orWhereRaw('next_due_mileage < (
                                  SELECT current_mileage FROM vehicles 
                                  WHERE vehicles.id = maintenance_plans.vehicle_id
                              )');
                            break;
                        case 'upcoming':
                            $q->whereBetween('next_due_date', [now(), now()->addDays(30)]);
                            break;
                        case 'scheduled':
                            $q->where('next_due_date', '>', now()->addDays(30));
                            break;
                    }
                });
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('registration_plate', 'ILIKE', "%{$search}%")
                          ->orWhere('brand', 'ILIKE', "%{$search}%")
                          ->orWhere('model', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('maintenanceType', function ($mq) use ($search) {
                        $mq->where('name', 'ILIKE', "%{$search}%");
                    })
                    ->orWhere('notes', 'ILIKE', "%{$search}%");
                });
            }

            // Tri intelligent par urgence puis par date
            $query->orderByRaw('
                CASE 
                    WHEN next_due_date IS NOT NULL AND next_due_date < NOW() THEN 1
                    WHEN next_due_mileage IS NOT NULL AND EXISTS (
                        SELECT 1 FROM vehicles v 
                        WHERE v.id = maintenance_plans.vehicle_id 
                        AND maintenance_plans.next_due_mileage <= v.current_mileage
                    ) THEN 2
                    ELSE 3 
                END
            ')
            ->orderBy('next_due_date', 'asc')
            ->orderBy('next_due_mileage', 'asc');

            $plans = $query->paginate(20)->withQueryString();

            // Préparer les données pour JavaScript (modales, etc.)
            $plansForJs = $plans->getCollection()->mapWithKeys(function ($plan) {
                return [$plan->id => [
                    'id' => $plan->id,
                    'vehicle_id' => $plan->vehicle_id,
                    'maintenance_type_id' => $plan->maintenance_type_id,
                    'recurrence_value' => $plan->recurrence_value,
                    'recurrence_unit_id' => $plan->recurrence_unit_id,
                    'notes' => $plan->notes,
                    'next_due_date' => $plan->next_due_date?->format('Y-m-d'),
                    'next_due_mileage' => $plan->next_due_mileage,
                    'vehicle' => $plan->vehicle?->only(['id', 'brand', 'model', 'registration_plate', 'current_mileage']),
                    'maintenance_type' => $plan->maintenanceType?->only(['id', 'name', 'description']),
                    'recurrence_unit' => $plan->recurrenceUnit?->only(['id', 'name']),
                    'status' => $this->calculatePlanStatus($plan),
                    'urgency_level' => $this->calculateUrgencyLevel($plan),
                ]];
            });

            // Données pour les filtres
            $vehicles = Vehicle::when($organizationId, function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->whereNull('deleted_at')
                ->orderBy('brand')
                ->orderBy('model')
                ->get();
                
            $maintenanceTypes = MaintenanceType::orderBy('name')->get();
            $recurrenceUnits = RecurrenceUnit::orderBy('order_index')->get();
            
            // Statistiques pour le header
            $stats = [
                'total' => $plans->total(),
                'overdue' => MaintenancePlan::when($organizationId, function ($q) use ($organizationId) {
                        $q->whereHas('vehicle', function ($vq) use ($organizationId) {
                            $vq->where('organization_id', $organizationId);
                        });
                    })
                    ->where(function ($q) {
                        $q->where('next_due_date', '<', now())
                          ->orWhereRaw('next_due_mileage < (
                              SELECT current_mileage FROM vehicles 
                              WHERE vehicles.id = maintenance_plans.vehicle_id
                          )');
                    })
                    ->count(),
                'upcoming' => MaintenancePlan::when($organizationId, function ($q) use ($organizationId) {
                        $q->whereHas('vehicle', function ($vq) use ($organizationId) {
                            $vq->where('organization_id', $organizationId);
                        });
                    })
                    ->whereBetween('next_due_date', [now(), now()->addDays(30)])
                    ->count(),
            ];

            Log::channel('maintenance')->info('Maintenance plans viewed', [
                'user_id' => Auth::id(),
                'organization_id' => $organizationId,
                'filters' => $request->only(['vehicle_id', 'maintenance_type_id', 'status', 'search']),
                'results_count' => $plans->total()
            ]);

            return view('admin.maintenance.plans.index', [
                'plans' => $plans,
                'plansForJs' => $plansForJs,
                'vehicles' => $vehicles,
                'maintenanceTypes' => $maintenanceTypes,
                'recurrenceUnits' => $recurrenceUnits,
                'filters' => $request->only(['vehicle_id', 'maintenance_type_id', 'status', 'search']),
                'stats' => $stats,
            ]);
            
        } catch (\Exception $e) {
            Log::channel('errors')->error('Maintenance plans index error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.maintenance.plans.index')->withErrors([
                'error' => 'Erreur lors du chargement des plans de maintenance.'
            ]);
        }
    }

    /**
     * 📝 Afficher le formulaire de création de plan de maintenance
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('manage maintenance plans');
        
        $organizationId = Auth::user()->organization_id;
        
        // Véhicules disponibles (sans plan de maintenance actif pour le même type)
        $vehicles = Vehicle::when($organizationId, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->whereNull('deleted_at')
            ->orderBy('brand')
            ->orderBy('model')
            ->get();
            
        $maintenanceTypes = MaintenanceType::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        $recurrenceUnits = RecurrenceUnit::orderBy('order_index')->get();

        return view('admin.maintenance.plans.create', compact(
            'vehicles', 
            'maintenanceTypes', 
            'recurrenceUnits'
        ));
    }

    /**
     * 💾 Enregistrer un nouveau plan de maintenance
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage maintenance plans');
        
        $organizationId = Auth::user()->organization_id;
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'vehicle_id' => [
                    'required',
                    'exists:vehicles,id',
                    // Vérifier que le véhicule appartient à l'organisation
                    Rule::exists('vehicles', 'id')->where(function ($query) use ($organizationId) {
                        if ($organizationId) {
                            $query->where('organization_id', $organizationId);
                        }
                        $query->whereNull('deleted_at');
                    })
                ],
                'maintenance_type_id' => ['required', 'exists:maintenance_types,id'],
                'recurrence_value' => ['required', 'integer', 'min:1', 'max:9999'],
                'recurrence_unit_id' => ['required', 'exists:recurrence_units,id'],
                'next_due_date' => ['nullable', 'date', 'after_or_equal:today'],
                'next_due_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
                'notes' => ['nullable', 'string', 'max:2000'],
                'is_critical' => ['boolean'],
                'estimated_duration_hours' => ['nullable', 'numeric', 'min:0.25', 'max:999'],
                'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            ], [
                'vehicle_id.exists' => 'Le véhicule sélectionné n\'est pas accessible.',
                'recurrence_value.max' => 'La valeur de récurrence ne peut pas dépasser 9999.',
                'next_due_date.after_or_equal' => 'La date d\'échéance ne peut pas être dans le passé.',
            ]);
            
            // Vérifier qu'il n'y a pas déjà un plan identique
            $existingPlan = MaintenancePlan::where([
                'vehicle_id' => $validated['vehicle_id'],
                'maintenance_type_id' => $validated['maintenance_type_id'],
            ])->first();
            
            if ($existingPlan) {
                return back()
                    ->withInput()
                    ->withErrors(['maintenance_type_id' => 'Un plan de maintenance existe déjà pour ce véhicule et ce type de maintenance.']);
            }

            // Calcul automatique de la première échéance si non fournie
            if (empty($validated['next_due_date']) && empty($validated['next_due_mileage'])) {
                $vehicle = Vehicle::find($validated['vehicle_id']);
                $recurrenceUnit = RecurrenceUnit::find($validated['recurrence_unit_id']);
                $recurrenceValue = $validated['recurrence_value'];

                if ($recurrenceUnit->name === 'Kilomètres') {
                    $validated['next_due_mileage'] = ($vehicle->current_mileage ?? 0) + $recurrenceValue;
                } elseif (in_array($recurrenceUnit->name, ['Jours', 'Days'])) {
                    $validated['next_due_date'] = Carbon::now()->addDays($recurrenceValue);
                } elseif (in_array($recurrenceUnit->name, ['Semaines', 'Weeks'])) {
                    $validated['next_due_date'] = Carbon::now()->addWeeks($recurrenceValue);
                } elseif (in_array($recurrenceUnit->name, ['Mois', 'Months'])) {
                    $validated['next_due_date'] = Carbon::now()->addMonths($recurrenceValue);
                } elseif (in_array($recurrenceUnit->name, ['Années', 'Years'])) {
                    $validated['next_due_date'] = Carbon::now()->addYears($recurrenceValue);
                }
            }
            
            $validated['created_by'] = Auth::id();
            $validated['is_active'] = true;

            $plan = MaintenancePlan::create($validated);
            
            DB::commit();
            
            Log::channel('maintenance')->info('Maintenance plan created', [
                'plan_id' => $plan->id,
                'vehicle_id' => $validated['vehicle_id'],
                'maintenance_type_id' => $validated['maintenance_type_id'],
                'user_id' => Auth::id(),
                'organization_id' => $organizationId
            ]);

            return redirect()
                ->route('admin.maintenance.plans.index')
                ->with('success', 'Plan de maintenance créé avec succès.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance plan creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création du plan de maintenance.']);
        }
    }

    /**
     * 📝 Mettre à jour un plan de maintenance existant
     *
     * @param Request $request
     * @param MaintenancePlan $plan
     * @return RedirectResponse
     */
    public function update(Request $request, MaintenancePlan $plan): RedirectResponse
    {
        $this->authorize('manage maintenance plans');
        
        // Vérifier l'accès à l'organisation
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $plan->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à ce plan de maintenance.');
        }
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'recurrence_value' => ['required', 'integer', 'min:1', 'max:9999'],
                'recurrence_unit_id' => ['required', 'exists:recurrence_units,id'],
                'next_due_date' => ['nullable', 'date'],
                'next_due_mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
                'notes' => ['nullable', 'string', 'max:2000'],
                'is_critical' => ['boolean'],
                'estimated_duration_hours' => ['nullable', 'numeric', 'min:0.25', 'max:999'],
                'estimated_cost' => ['nullable', 'numeric', 'min:0', 'max:999999'],
                'is_active' => ['boolean'],
            ]);
            
            $validated['updated_by'] = Auth::id();
            $validated['updated_at'] = now();

            $oldData = $plan->toArray();
            $plan->update($validated);
            
            DB::commit();
            
            Log::channel('maintenance')->info('Maintenance plan updated', [
                'plan_id' => $plan->id,
                'user_id' => Auth::id(),
                'organization_id' => $organizationId,
                'changes' => array_diff_assoc($validated, $oldData)
            ]);

            return back()->with('success', 'Plan de maintenance mis à jour avec succès.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance plan update failed', [
                'plan_id' => $plan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    /**
     * 🗑️ Supprimer un plan de maintenance
     *
     * @param MaintenancePlan $plan
     * @return RedirectResponse
     */
    public function destroy(MaintenancePlan $plan): RedirectResponse
    {
        $this->authorize('manage maintenance plans');
        
        // Vérifier l'accès à l'organisation
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $plan->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à ce plan de maintenance.');
        }
        
        try {
            DB::beginTransaction();
            
            // Vérifier s'il y a des logs associés
            if ($plan->maintenanceLogs()->count() > 0) {
                return back()->withErrors([
                    'error' => 'Impossible de supprimer ce plan car il y a des interventions associées. Désactivez-le plutôt.'
                ]);
            }
            
            $planInfo = [
                'id' => $plan->id,
                'vehicle' => $plan->vehicle->registration_plate,
                'maintenance_type' => $plan->maintenanceType->name,
            ];
            
            $plan->delete();
            
            DB::commit();
            
            Log::channel('maintenance')->warning('Maintenance plan deleted', [
                'plan_info' => $planInfo,
                'user_id' => Auth::id(),
                'organization_id' => $organizationId
            ]);
            
            return redirect()
                ->route('admin.maintenance.plans.index')
                ->with('success', 'Plan de maintenance supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance plan deletion failed', [
                'plan_id' => $plan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression.']);
        }
    }

    /**
     * 🔄 Dupliquer un plan de maintenance pour un autre véhicule
     *
     * @param Request $request
     * @param MaintenancePlan $plan
     * @return RedirectResponse
     */
    public function duplicate(Request $request, MaintenancePlan $plan): RedirectResponse
    {
        $this->authorize('manage maintenance plans');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $plan->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à ce plan de maintenance.');
        }
        
        try {
            $validated = $request->validate([
                'target_vehicle_ids' => ['required', 'array', 'min:1'],
                'target_vehicle_ids.*' => [
                    'exists:vehicles,id',
                    Rule::exists('vehicles', 'id')->where(function ($query) use ($organizationId) {
                        if ($organizationId) {
                            $query->where('organization_id', $organizationId);
                        }
                        $query->whereNull('deleted_at');
                    })
                ],
            ]);
            
            DB::beginTransaction();
            
            $duplicatedCount = 0;
            $skippedCount = 0;
            
            foreach ($validated['target_vehicle_ids'] as $vehicleId) {
                // Vérifier qu'un plan n'existe pas déjà
                $exists = MaintenancePlan::where([
                    'vehicle_id' => $vehicleId,
                    'maintenance_type_id' => $plan->maintenance_type_id,
                ])->exists();
                
                if ($exists) {
                    $skippedCount++;
                    continue;
                }
                
                // Dupliquer le plan
                $newPlan = $plan->replicate();
                $newPlan->vehicle_id = $vehicleId;
                $newPlan->created_by = Auth::id();
                $newPlan->updated_by = null;
                $newPlan->created_at = now();
                $newPlan->updated_at = now();
                
                // Recalculer les échéances pour le nouveau véhicule
                $targetVehicle = Vehicle::find($vehicleId);
                if ($plan->next_due_mileage && $targetVehicle) {
                    $newPlan->next_due_mileage = ($targetVehicle->current_mileage ?? 0) + $plan->recurrence_value;
                }
                
                $newPlan->save();
                $duplicatedCount++;
            }
            
            DB::commit();
            
            Log::channel('maintenance')->info('Maintenance plan duplicated', [
                'source_plan_id' => $plan->id,
                'duplicated_count' => $duplicatedCount,
                'skipped_count' => $skippedCount,
                'user_id' => Auth::id(),
                'organization_id' => $organizationId
            ]);
            
            $message = "Plan dupliqué avec succès pour {$duplicatedCount} véhicule(s).";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} véhicule(s) ignoré(s) (plan existant).";
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance plan duplication failed', [
                'plan_id' => $plan->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la duplication.']);
        }
    }

    /**
     * 📊 API pour obtenir les données d'un plan
     *
     * @param MaintenancePlan $plan
     * @return JsonResponse
     */
    public function show(MaintenancePlan $plan): JsonResponse
    {
        $this->authorize('manage maintenance plans');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $plan->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à ce plan de maintenance.');
        }
        
        $plan->load(['vehicle', 'maintenanceType', 'recurrenceUnit', 'maintenanceLogs' => function ($query) {
            $query->orderBy('performed_on_date', 'desc')->limit(5);
        }]);
        
        return response()->json([
            'success' => true,
            'plan' => $plan,
            'status' => $this->calculatePlanStatus($plan),
            'urgency_level' => $this->calculateUrgencyLevel($plan),
        ]);
    }

    /**
     * 📝 Rediriger l'édition vers l'index (formulaire modal)
     *
     * @param MaintenancePlan $plan
     * @return RedirectResponse
     */
    public function edit(MaintenancePlan $plan): RedirectResponse
    {
        return redirect()->route('admin.maintenance.plans.index', ['edit' => $plan->id]);
    }

    /**
     * 🔍 Calculer le statut d'un plan de maintenance
     *
     * @param MaintenancePlan $plan
     * @return string
     */
    private function calculatePlanStatus(MaintenancePlan $plan): string
    {
        if (!$plan->is_active) {
            return 'inactive';
        }
        
        // Vérifier si en retard
        if ($plan->next_due_date && $plan->next_due_date->isPast()) {
            return 'overdue';
        }
        
        if ($plan->next_due_mileage && $plan->vehicle && 
            $plan->vehicle->current_mileage >= $plan->next_due_mileage) {
            return 'overdue';
        }
        
        // Vérifier si urgent (dans les 7 prochains jours ou 500 km)
        if ($plan->next_due_date && $plan->next_due_date->diffInDays(now()) <= 7) {
            return 'urgent';
        }
        
        if ($plan->next_due_mileage && $plan->vehicle && 
            ($plan->next_due_mileage - $plan->vehicle->current_mileage) <= 500) {
            return 'urgent';
        }
        
        // Vérifier si à venir (dans le mois)
        if ($plan->next_due_date && $plan->next_due_date->diffInDays(now()) <= 30) {
            return 'upcoming';
        }
        
        return 'scheduled';
    }

    /**
     * ⚠️ Calculer le niveau d'urgence d'un plan
     *
     * @param MaintenancePlan $plan
     * @return string
     */
    private function calculateUrgencyLevel(MaintenancePlan $plan): string
    {
        $status = $this->calculatePlanStatus($plan);
        
        switch ($status) {
            case 'overdue':
                return 'critical';
            case 'urgent':
                return 'high';
            case 'upcoming':
                return 'medium';
            default:
                return 'low';
        }
    }
}
