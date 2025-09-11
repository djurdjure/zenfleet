<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceStatus;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class MaintenanceLogController extends Controller
{
    /**
     * 📝 MAINTENANCE LOG CONTROLLER - VERSION ENTERPRISE
     * Gestion des interventions et logs de maintenance
     */

    /**
     * 📊 Afficher la liste des interventions de maintenance
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('view maintenance logs');
        
        $organizationId = Auth::user()->organization_id;
        
        $query = MaintenanceLog::with(['vehicle', 'maintenanceType', 'maintenancePlan', 'maintenanceStatus'])
            ->when($organizationId, function ($query) use ($organizationId) {
                $query->whereHas('vehicle', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                });
            });
        
        // Filtres
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        
        if ($request->filled('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->maintenance_type_id);
        }
        
        if ($request->filled('status_id')) {
            $query->where('maintenance_status_id', $request->status_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('performed_on_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('performed_on_date', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('performed_on_date', 'desc')
                     ->paginate(25)
                     ->withQueryString();
        
        // Données pour les filtres
        $vehicles = Vehicle::when($organizationId, function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->orderBy('registration_plate')
            ->get();
            
        $maintenanceTypes = MaintenanceType::orderBy('name')->get();
        $statuses = MaintenanceStatus::orderBy('name')->get();
        
        return view('admin.maintenance.logs.index', compact(
            'logs', 
            'vehicles', 
            'maintenanceTypes', 
            'statuses'
        ));
    }

    /**
     * 📝 Afficher le formulaire de création d'intervention
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        $this->authorize('log maintenance');
        
        $organizationId = Auth::user()->organization_id;
        
        // Pré-sélection depuis un plan de maintenance si spécifié
        $selectedPlan = null;
        if ($request->filled('plan_id')) {
            $selectedPlan = MaintenancePlan::with(['vehicle', 'maintenanceType'])
                ->when($organizationId, function ($query) use ($organizationId) {
                    $query->whereHas('vehicle', function ($q) use ($organizationId) {
                        $q->where('organization_id', $organizationId);
                    });
                })
                ->findOrFail($request->plan_id);
        }
        
        $vehicles = Vehicle::when($organizationId, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->whereNull('deleted_at')
            ->orderBy('registration_plate')
            ->get();
            
        $maintenanceTypes = MaintenanceType::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
            
        $statuses = MaintenanceStatus::orderBy('name')->get();
        
        return view('admin.maintenance.logs.create', compact(
            'selectedPlan',
            'vehicles',
            'maintenanceTypes', 
            'statuses'
        ));
    }

    /**
     * 💾 Enregistrer une nouvelle intervention de maintenance
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('log maintenance');
        
        $organizationId = Auth::user()->organization_id;
        
        try {
            DB::beginTransaction();
            
            // Validation de base
            $validated = $request->validate([
                'vehicle_id' => [
                    'required',
                    'exists:vehicles,id',
                    Rule::exists('vehicles', 'id')->where(function ($query) use ($organizationId) {
                        if ($organizationId) {
                            $query->where('organization_id', $organizationId);
                        }
                        $query->whereNull('deleted_at');
                    })
                ],
                'maintenance_type_id' => ['required', 'exists:maintenance_types,id'],
                'maintenance_plan_id' => ['nullable', 'exists:maintenance_plans,id'],
                'maintenance_status_id' => ['required', 'exists:maintenance_statuses,id'],
                'performed_on_date' => ['required', 'date', 'before_or_equal:today'],
                'performed_at_mileage' => ['required', 'integer', 'min:0', 'max:9999999'],
                'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'labor_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
                'labor_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'parts_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'details' => ['required', 'string', 'max:2000'],
                'performed_by' => ['required', 'string', 'max:255'],
                'supplier_name' => ['nullable', 'string', 'max:255'],
                'invoice_number' => ['nullable', 'string', 'max:100'],
                'warranty_expires_at' => ['nullable', 'date', 'after:performed_on_date'],
                'next_service_recommendation' => ['nullable', 'string', 'max:1000'],
                'urgency_level' => ['in:low,medium,high,critical'],
                'photos' => ['nullable', 'array', 'max:10'],
                'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
                'documents' => ['nullable', 'array', 'max:5'],
                'documents.*' => ['file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB max
            ], [
                'performed_on_date.before_or_equal' => 'La date d\'intervention ne peut pas être dans le futur.',
                'performed_at_mileage.required' => 'Le kilométrage au moment de l\'intervention est obligatoire.',
                'details.required' => 'Les détails de l\'intervention sont obligatoires.',
                'performed_by.required' => 'L\'intervenant est obligatoire.',
            ]);
            
            // Vérifier le kilométrage par rapport au véhicule
            $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
            if ($validated['performed_at_mileage'] < ($vehicle->current_mileage ?? 0)) {
                return back()
                    ->withInput()
                    ->withErrors(['performed_at_mileage' => 'Le kilométrage d\'intervention ne peut pas être inférieur au kilométrage actuel du véhicule (' . number_format($vehicle->current_mileage, 0, ',', ' ') . ' km).']);
            }
            
            // Traitement des coûts automatiques
            if (!$validated['cost']) {
                $validated['cost'] = ($validated['labor_cost'] ?? 0) + ($validated['parts_cost'] ?? 0);
            }
            
            $validated['created_by'] = Auth::id();
            $validated['organization_id'] = $organizationId;
            
            // Créer l'intervention
            $log = MaintenanceLog::create($validated);
            
            // Traitement des fichiers joints
            if ($request->hasFile('photos')) {
                $this->storePhotos($log, $request->file('photos'));
            }
            
            if ($request->hasFile('documents')) {
                $this->storeDocuments($log, $request->file('documents'));
            }
            
            // Mise à jour du plan de maintenance associé
            if ($validated['maintenance_plan_id']) {
                $this->updateMaintenancePlan($validated['maintenance_plan_id'], $validated);
            }
            
            // Mise à jour du kilométrage du véhicule
            if ($validated['performed_at_mileage'] > ($vehicle->current_mileage ?? 0)) {
                $vehicle->update([
                    'current_mileage' => $validated['performed_at_mileage'],
                    'last_service_date' => $validated['performed_on_date'],
                ]);
            }
            
            DB::commit();
            
            Log::channel('maintenance')->info('Maintenance intervention logged', [
                'log_id' => $log->id,
                'vehicle_id' => $validated['vehicle_id'],
                'maintenance_type_id' => $validated['maintenance_type_id'],
                'cost' => $validated['cost'],
                'performed_by' => $validated['performed_by'],
                'user_id' => Auth::id(),
                'organization_id' => $organizationId
            ]);
            
            $redirectRoute = $request->input('redirect_to', 'admin.maintenance.logs.index');
            
            return redirect()
                ->route($redirectRoute)
                ->with('success', 'Intervention de maintenance enregistrée avec succès.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance log creation failed', [
                'user_id' => Auth::id(),
                'vehicle_id' => $request->vehicle_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement de l\'intervention.']);
        }
    }

    /**
     * 📊 Afficher les détails d'une intervention
     *
     * @param MaintenanceLog $log
     * @return View
     */
    public function show(MaintenanceLog $log): View
    {
        $this->authorize('view maintenance logs');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $log->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }
        
        $log->load([
            'vehicle', 
            'maintenanceType', 
            'maintenancePlan.recurrenceUnit', 
            'maintenanceStatus',
            'photos',
            'documents',
            'createdBy',
            'updatedBy'
        ]);
        
        return view('admin.maintenance.logs.show', compact('log'));
    }

    /**
     * 📝 Afficher le formulaire d'édition d'une intervention
     *
     * @param MaintenanceLog $log
     * @return View
     */
    public function edit(MaintenanceLog $log): View
    {
        $this->authorize('edit maintenance logs');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $log->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }
        
        $log->load(['vehicle', 'maintenanceType', 'maintenancePlan', 'maintenanceStatus']);
        
        $vehicles = Vehicle::when($organizationId, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->whereNull('deleted_at')
            ->orderBy('registration_plate')
            ->get();
            
        $maintenanceTypes = MaintenanceType::orderBy('name')->get();
        $statuses = MaintenanceStatus::orderBy('name')->get();
        
        return view('admin.maintenance.logs.edit', compact(
            'log',
            'vehicles',
            'maintenanceTypes',
            'statuses'
        ));
    }

    /**
     * 💾 Mettre à jour une intervention existante
     *
     * @param Request $request
     * @param MaintenanceLog $log
     * @return RedirectResponse
     */
    public function update(Request $request, MaintenanceLog $log): RedirectResponse
    {
        $this->authorize('edit maintenance logs');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $log->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }
        
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'maintenance_status_id' => ['required', 'exists:maintenance_statuses,id'],
                'cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'labor_hours' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
                'labor_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'parts_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
                'details' => ['required', 'string', 'max:2000'],
                'performed_by' => ['required', 'string', 'max:255'],
                'supplier_name' => ['nullable', 'string', 'max:255'],
                'invoice_number' => ['nullable', 'string', 'max:100'],
                'warranty_expires_at' => ['nullable', 'date', 'after:performed_on_date'],
                'next_service_recommendation' => ['nullable', 'string', 'max:1000'],
                'urgency_level' => ['in:low,medium,high,critical'],
            ]);
            
            // Traitement des coûts automatiques
            if (!$validated['cost']) {
                $validated['cost'] = ($validated['labor_cost'] ?? 0) + ($validated['parts_cost'] ?? 0);
            }
            
            $validated['updated_by'] = Auth::id();
            $validated['updated_at'] = now();
            
            $oldData = $log->toArray();
            $log->update($validated);
            
            DB::commit();
            
            Log::channel('maintenance')->info('Maintenance log updated', [
                'log_id' => $log->id,
                'user_id' => Auth::id(),
                'organization_id' => $organizationId,
                'changes' => array_diff_assoc($validated, $oldData)
            ]);
            
            return redirect()
                ->route('admin.maintenance.logs.show', $log)
                ->with('success', 'Intervention mise à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::channel('errors')->error('Maintenance log update failed', [
                'log_id' => $log->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    /**
     * 📄 Exporter un rapport d'intervention en PDF
     *
     * @param MaintenanceLog $log
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(MaintenanceLog $log)
    {
        $this->authorize('view maintenance logs');
        
        $organizationId = Auth::user()->organization_id;
        if ($organizationId && $log->vehicle->organization_id !== $organizationId) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }
        
        $log->load([
            'vehicle.organization',
            'maintenanceType',
            'maintenancePlan.recurrenceUnit',
            'maintenanceStatus',
            'createdBy'
        ]);
        
        // TODO: Implémenter la génération PDF
        // Pour l'instant, retourner une vue HTML
        return view('admin.maintenance.logs.pdf', compact('log'));
    }

    /**
     * 📸 Stocker les photos d'intervention
     *
     * @param MaintenanceLog $log
     * @param array $photos
     * @return void
     */
    private function storePhotos(MaintenanceLog $log, array $photos): void
    {
        foreach ($photos as $index => $photo) {
            if ($photo->isValid()) {
                $filename = 'maintenance_' . $log->id . '_photo_' . ($index + 1) . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('maintenance/photos', $filename, 'public');
                
                // TODO: Créer une relation MaintenancePhoto ou stocker dans un champ JSON
                // $log->photos()->create(['path' => $path, 'filename' => $filename]);
            }
        }
    }

    /**
     * 📄 Stocker les documents d'intervention
     *
     * @param MaintenanceLog $log
     * @param array $documents
     * @return void
     */
    private function storeDocuments(MaintenanceLog $log, array $documents): void
    {
        foreach ($documents as $index => $document) {
            if ($document->isValid()) {
                $filename = 'maintenance_' . $log->id . '_doc_' . ($index + 1) . '_' . time() . '.' . $document->getClientOriginalExtension();
                $path = $document->storeAs('maintenance/documents', $filename, 'public');
                
                // TODO: Créer une relation MaintenanceDocument ou stocker dans un champ JSON
                // $log->documents()->create(['path' => $path, 'filename' => $filename, 'original_name' => $document->getClientOriginalName()]);
            }
        }
    }

    /**
     * 🔄 Mettre à jour le plan de maintenance après intervention
     *
     * @param int $planId
     * @param array $logData
     * @return void
     */
    private function updateMaintenancePlan(int $planId, array $logData): void
    {
        $plan = MaintenancePlan::with(['recurrenceUnit', 'vehicle'])->findOrFail($planId);
        
        if (!$plan->recurrenceUnit || !$plan->recurrence_value) {
            return;
        }
        
        $recurrenceUnit = $plan->recurrenceUnit;
        $recurrenceValue = $plan->recurrence_value;
        
        // Calculer la prochaine échéance selon l'unité de récurrence
        if ($recurrenceUnit->name === 'Kilomètres') {
            $plan->next_due_mileage = $logData['performed_at_mileage'] + $recurrenceValue;
            $plan->next_due_date = null; // Reset la date si on utilise le kilométrage
        } elseif (in_array($recurrenceUnit->name, ['Jours', 'Days'])) {
            $plan->next_due_date = Carbon::parse($logData['performed_on_date'])->addDays($recurrenceValue);
            $plan->next_due_mileage = null; // Reset le kilométrage si on utilise la date
        } elseif (in_array($recurrenceUnit->name, ['Semaines', 'Weeks'])) {
            $plan->next_due_date = Carbon::parse($logData['performed_on_date'])->addWeeks($recurrenceValue);
            $plan->next_due_mileage = null;
        } elseif (in_array($recurrenceUnit->name, ['Mois', 'Months'])) {
            $plan->next_due_date = Carbon::parse($logData['performed_on_date'])->addMonths($recurrenceValue);
            $plan->next_due_mileage = null;
        } elseif (in_array($recurrenceUnit->name, ['Années', 'Years'])) {
            $plan->next_due_date = Carbon::parse($logData['performed_on_date'])->addYears($recurrenceValue);
            $plan->next_due_mileage = null;
        }
        
        $plan->last_performed_at = $logData['performed_on_date'];
        $plan->last_performed_mileage = $logData['performed_at_mileage'];
        $plan->updated_by = Auth::id();
        
        $plan->save();
        
        Log::channel('maintenance')->info('Maintenance plan updated after intervention', [
            'plan_id' => $plan->id,
            'next_due_date' => $plan->next_due_date?->format('Y-m-d'),
            'next_due_mileage' => $plan->next_due_mileage,
            'user_id' => Auth::id()
        ]);
    }
}
