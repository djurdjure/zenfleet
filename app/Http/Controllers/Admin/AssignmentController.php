<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Admin\Assignment\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Traits\ResourceAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    use ResourceAvailability;
    public function __construct()
    {
        $this->middleware('auth');

        // ✅ Utiliser authorizeResource pour appliquer automatiquement AssignmentPolicy
        $this->authorizeResource(Assignment::class, 'assignment');
    }

    /**
     * Affiche la page d'affectations enterprise-grade
     */
    public function index(Request $request): View
    {
        $this->authorize('view assignments');

        // Construction de la requête avec filtres
        $query = Assignment::with(['vehicle', 'driver', 'creator'])
            ->where('organization_id', auth()->user()->organization_id);

        // Application des filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                })
                ->orWhereHas('driver', function ($driverQuery) use ($search) {
                    $driverQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('personal_phone', 'like', "%{$search}%");
                });
            });
        }

        // Pagination avec filtres
        $perPage = (int) $request->get('per_page', 15);
        $assignments = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);

        // ✅ Récupérer tous les véhicules et chauffeurs pour les filtres
        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
            ->orderBy('registration_plate')
            ->get();

        $drivers = Driver::where('organization_id', auth()->user()->organization_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Calculer les statistiques des affectations
        $allAssignments = Assignment::where('organization_id', auth()->user()->organization_id);
        $activeAssignments = (clone $allAssignments)
            ->where('status', 'active')
            ->count();
        $inProgressAssignments = (clone $allAssignments)
            ->where('status', 'in_progress')
            ->count();
        $scheduledAssignments = (clone $allAssignments)
            ->where('status', 'scheduled')
            ->count();

        return view('admin.assignments.index', compact(
            'assignments',
            'vehicles',
            'drivers',
            'activeAssignments',
            'inProgressAssignments',
            'scheduledAssignments'
        ));
    }


    /**
     * Affiche le formulaire de création.
     */
    public function create(): View
    {
        $this->authorize('create assignments');

        // ✅ NOUVELLE LOGIQUE ENTERPRISE: Utilisation du trait ResourceAvailability
        // Source de vérité unique: is_available + assignment_status
        $availableVehicles = $this->getAvailableVehicles();
        $availableDrivers = $this->getAvailableDrivers();

        // Affectations actives pour les statistiques
        $activeAssignments = Assignment::where('organization_id', auth()->user()->organization_id)
            ->whereNull('end_datetime')
            ->where('start_datetime', '<=', now())
            ->with(['vehicle', 'driver'])
            ->get();

        // Debug pour diagnostique (à retirer en production)
        \Log::info('Ressources disponibles pour affectation', [
            'user_org_id' => auth()->user()->organization_id,
            'vehicles_count' => $availableVehicles->count(),
            'drivers_count' => $availableDrivers->count(),
            'active_assignments_count' => $activeAssignments->count(),
            'vehicles' => $availableVehicles->pluck('registration_plate'),
            'drivers' => $availableDrivers->pluck('first_name', 'last_name')
        ]);

        return view('admin.assignments.create-enterprise', compact('availableVehicles', 'availableDrivers', 'activeAssignments'));
    }

    /**
     * Enregistre une nouvelle affectation.
     */
    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        // Traiter les données de programmation
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $data['start_date'] . ' ' . $data['start_time']);
        $data['start_datetime'] = $startDateTime;

        // Gestion de l'affectation programmée
        if ($data['assignment_type'] === 'scheduled' && isset($data['end_date']) && isset($data['end_time'])) {
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $data['end_date'] . ' ' . $data['end_time']);
            $data['end_datetime'] = $endDateTime;
            $data['status'] = 'scheduled'; // Affectation programmée
        } else {
            $data['end_datetime'] = null;
            $data['status'] = 'active'; // Affectation ouverte
        }

        // Mapper les champs pour compatibilité
        if (isset($data['purpose'])) {
            $data['reason'] = $data['purpose'];
        }

        // Nettoyer les champs temporaires
        unset($data['start_date'], $data['start_time'], $data['end_date'], $data['end_time'], $data['assignment_type'], $data['purpose']);

        // ✅ VÉRIFICATION DES CHEVAUCHEMENTS AVANT CRÉATION
        $newAssignment = new Assignment($data); // Créer une instance sans la persister
        
        if ($newAssignment->isOverlapping()) {
            Log::warning('Tentative de création d\'affectation avec chevauchement', [
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_datetime' => $data['start_datetime'],
                'end_datetime' => $data['end_datetime'],
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Un chevauchement d\'affectation a été détecté pour ce véhicule ou ce chauffeur. '
                    . 'Veuillez vérifier les périodes existantes.'
                );
        }

        try {
            $assignment = Assignment::create($data);

            // Log de l'activité pour traçabilité enterprise
            \Log::info('Nouvelle affectation créée', [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
                'end_datetime' => $data['end_datetime'] ? $data['end_datetime']->format('Y-m-d H:i:s') : 'Ouverte',
                'created_by' => auth()->id(),
                'organization_id' => $data['organization_id']
            ]);

            $message = $data['status'] === 'scheduled'
                ? 'Affectation programmée créée avec succès.'
                : 'Affectation ouverte créée avec succès.';

            return redirect()->route('admin.assignments.index')
                ->with('success', $message)
                ->with('assignment_id', $assignment->id);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'affectation', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'affectation : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une affectation.
     */
    public function show(Assignment $assignment): View
    {
        $this->authorize('view assignments');
        $assignment->load(['vehicle', 'driver', 'creator', 'handoverForm']);

        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Assignment $assignment): View
    {
        $this->authorize('edit assignments');
        $assignment->load(['vehicle', 'driver']);

        return view('admin.assignments.edit', compact('assignment'));
    }

    /**
     * Met à jour une affectation.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // ✅ VÉRIFICATION DES CHEVAUCHEMENTS AVANT MISE À JOUR
        $assignment->fill($data); // Mettre à jour l'instance existante
        
        if ($assignment->isOverlapping($assignment->id)) { // Passer l'ID de l'affectation actuelle
            Log::warning('Tentative de modification d\'affectation avec chevauchement', [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $data['vehicle_id'] ?? $assignment->vehicle_id,
                'driver_id' => $data['driver_id'] ?? $assignment->driver_id,
                'start_datetime' => $data['start_datetime'] ?? $assignment->start_datetime,
                'end_datetime' => $data['end_datetime'] ?? $assignment->end_datetime,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Un chevauchement d\'affectation a été détecté pour ce véhicule ou ce chauffeur. '
                    . 'Veuillez vérifier les périodes existantes.'
                );
        }

        try {
            $assignment->save();
            
            Log::info('Affectation mise à jour avec succès', [
                'assignment_id' => $assignment->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Affectation mise à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'affectation', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'affectation : ' . $e->getMessage());
        }
    }

    /**
     * Termine une affectation en cours
     */
    public function end(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $assignment);

        if (!$assignment->canBeEnded()) {
            $message = 'Cette affectation ne peut pas être terminée.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        // Validation des données
        $validated = $request->validate([
            'end_datetime' => ['required', 'date', 'after_or_equal:' . $assignment->start_datetime],
            'end_mileage' => ['nullable', 'integer', 'min:' . ($assignment->start_mileage ?? 0)],
            'notes' => ['nullable', 'string', 'max:1000']
        ], [
            'end_datetime.required' => 'La date de fin est obligatoire.',
            'end_datetime.date' => 'Le format de la date de fin est invalide.',
            'end_datetime.after_or_equal' => 'La date de fin doit être postérieure au début.',
            'end_mileage.integer' => 'Le kilométrage doit être un nombre.',
            'end_mileage.min' => 'Le kilométrage de fin doit être supérieur au kilométrage de début.'
        ]);

        try {
            // Utiliser la méthode enterprise du modèle
            $success = $assignment->end(
                Carbon::parse($validated['end_datetime']),
                $validated['end_mileage'] ?? null,
                $validated['notes'] ?? null
            );

            if ($success) {
                $message = 'Affectation terminée avec succès.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'assignment' => $assignment->fresh()
                    ]);
                }

                return redirect()->route('admin.assignments.index')->with('success', $message);
            }

            throw new \Exception('Échec de la terminaison');

        } catch (\Exception $e) {
            $errorMessage = 'Erreur lors de la terminaison de l\'affectation: ' . $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Vue Gantt des affectations
     */
    public function gantt(): View
    {
        $this->authorize('view assignments');

        return view('admin.assignments.gantt', [
            'title' => 'Planning Gantt des Affectations',
            'breadcrumbs' => [
                'Admin' => route('admin.dashboard'),
                'Affectations' => route('admin.assignments.index'),
                'Planning Gantt' => null
            ]
        ]);
    }

    /**
     * API - Export des affectations
     */
    public function export(Request $request): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('view assignments');

        $format = $request->input('format', 'csv');
        $filters = $request->only(['status', 'vehicle_id', 'driver_id', 'date_from', 'date_to']);

        $query = Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator']);

        // Application des filtres
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['vehicle_id']) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if ($filters['driver_id']) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if ($filters['date_from']) {
            $query->where('start_datetime', '>=', Carbon::parse($filters['date_from']));
        }

        if ($filters['date_to']) {
            $query->where('start_datetime', '<=', Carbon::parse($filters['date_to']));
        }

        $assignments = $query->orderBy('start_datetime', 'desc')->get();

        return match($format) {
            'csv' => $this->exportToCsv($assignments),
            default => response()->json(['error' => 'Format non supporté'], 400)
        };
    }

    /**
     * API - Statistiques des affectations enterprise
     */
    public function stats(Request $request): JsonResponse
    {
        $this->authorize('view assignment statistics');

        $organizationId = auth()->user()->organization_id;

        // Période par défaut : ce mois-ci, mais compatible avec requêtes sans paramètres
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        try {
            // Affectations actives (en cours, pas de date de fin)
            $activeCount = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->where('start_datetime', '<=', now())
                ->count();

            // Affectations programmées (date de début future)
            $scheduledCount = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->where('start_datetime', '>', now())
                ->count();

            // Affectations terminées ce mois
            $completedCount = Assignment::where('organization_id', $organizationId)
                ->whereNotNull('end_datetime')
                ->whereMonth('end_datetime', now()->month)
                ->whereYear('end_datetime', now()->year)
                ->count();

            // Total pour la période
            $totalAssignments = Assignment::where('organization_id', $organizationId)
                ->whereBetween('start_datetime', [$dateFrom, $dateTo])
                ->count();

            // Calcul du taux d'utilisation (véhicules affectés / total véhicules)
            $totalVehicles = Vehicle::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->count();

            $assignedVehicles = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->distinct('vehicle_id')
                ->count('vehicle_id');

            $avgUtilization = $totalVehicles > 0 ? round(($assignedVehicles / $totalVehicles) * 100, 1) : 0;

            // Structure de réponse compatible avec l'interface frontend
            $stats = [
                'total_assignments' => $totalAssignments,
                'active_assignments' => $activeCount,
                'scheduled_assignments' => $scheduledCount,
                'completed_assignments' => $completedCount,
                'average_utilization' => $avgUtilization,

                // Métriques avancées pour usage enterprise
                'total_vehicles' => $totalVehicles,
                'assigned_vehicles' => $assignedVehicles,
                'available_vehicles' => $totalVehicles - $assignedVehicles,
                'utilization_percentage' => $avgUtilization,

                // Meta informations
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                    'current_month' => now()->format('Y-m')
                ],

                // Données détaillées (optionnelles, selon les besoins)
                'vehicles_breakdown' => $request->boolean('detailed') ?
                    Vehicle::where('organization_id', $organizationId)
                        ->withCount(['assignments as active_assignments_count' => function ($query) {
                            $query->whereNull('end_datetime')->where('start_datetime', '<=', now());
                        }])
                        ->get(['id', 'registration_plate', 'brand', 'model'])
                        ->map(function ($vehicle) {
                            return [
                                'vehicle_id' => $vehicle->id,
                                'registration_plate' => $vehicle->registration_plate,
                                'brand_model' => $vehicle->brand . ' ' . $vehicle->model,
                                'is_assigned' => $vehicle->active_assignments_count > 0,
                                'active_assignments' => $vehicle->active_assignments_count
                            ];
                        }) : []
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            // Gestion d'erreur enterprise avec fallback
            \Log::error('Erreur dans AssignmentController::stats()', [
                'error' => $e->getMessage(),
                'organization_id' => $organizationId,
                'user_id' => auth()->id()
            ]);

            // Retourner des valeurs par défaut en cas d'erreur
            return response()->json([
                'total_assignments' => 0,
                'active_assignments' => 0,
                'scheduled_assignments' => 0,
                'completed_assignments' => 0,
                'average_utilization' => 0,
                'total_vehicles' => 0,
                'assigned_vehicles' => 0,
                'error' => 'Erreur lors du calcul des statistiques'
            ], 200); // 200 pour ne pas casser l'interface
        }
    }

    /**
     * API - Véhicules disponibles pour affectation
     */
    public function availableVehicles(Request $request): JsonResponse
    {
        $this->authorize('view assignments');

        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
            ->where('status', 'active')
            ->whereDoesntHave('assignments', function($query) {
                // Véhicules sans affectation en cours
                $query->whereNull('end_datetime')
                      ->where('start_datetime', '<=', now());
            })
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage', 'status')
            ->orderBy('registration_plate')
            ->get();

        return response()->json($vehicles);
    }

    /**
     * API - Chauffeurs disponibles pour affectation
     */
    public function availableDrivers(Request $request): JsonResponse
    {
        $this->authorize('view assignments');

        $drivers = Driver::where('organization_id', auth()->user()->organization_id)
            ->whereHas('driverStatus', function($statusQuery) {
                $statusQuery->where('is_active', true)
                           ->where('can_drive', true)
                           ->where('can_assign', true);
            })
            ->whereDoesntHave('assignments', function($query) {
                // Chauffeurs sans affectation en cours
                $query->whereNull('end_datetime')
                      ->where('start_datetime', '<=', now());
            })
            ->with('driverStatus')
            ->select('id', 'first_name', 'last_name', 'license_number', 'personal_phone', 'status_id')
            ->orderBy('last_name')
            ->get()
            ->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'full_name' => $driver->full_name,
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                    'license_number' => $driver->license_number,
                    'personal_phone' => $driver->personal_phone,
                    'status' => $driver->driverStatus?->name ?? 'Actif',
                    'status_color' => $driver->driverStatus?->color ?? '#10b981'
                ];
            });

        return response()->json($drivers);
    }

    /**
     * Helpers privés pour export
     */
    private function exportToCsv($assignments): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="affectations_' . now()->format('Y-m-d') . '.csv"'
        ];

        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Véhicule',
                'Chauffeur',
                'Date début',
                'Date fin',
                'Durée (heures)',
                'Statut',
                'Motif',
                'Kilométrage début',
                'Kilométrage fin',
                'Créé par',
                'Créé le'
            ]);

            // Données
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->id,
                    $assignment->vehicle_display,
                    $assignment->driver_display,
                    $assignment->start_datetime->format('d/m/Y H:i'),
                    $assignment->end_datetime?->format('d/m/Y H:i') ?? 'En cours',
                    $assignment->duration_hours,
                    $assignment->status_label,
                    $assignment->reason,
                    $assignment->start_mileage,
                    $assignment->end_mileage,
                    $assignment->creator?->name ?? 'Système',
                    $assignment->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calcule le taux d'utilisation d'une ressource
     */
    private function calculateUtilizationRate($resource, $dateFrom, $dateTo): float
    {
        $totalPeriodHours = Carbon::parse($dateFrom)->diffInHours(Carbon::parse($dateTo));

        $usedHours = $resource->assignments()
            ->whereBetween('start_datetime', [$dateFrom, $dateTo])
            ->get()
            ->sum('duration_hours');

        return $totalPeriodHours > 0 ? round(($usedHours / $totalPeriodHours) * 100, 2) : 0;
    }
}