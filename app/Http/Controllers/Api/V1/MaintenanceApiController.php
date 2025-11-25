<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MaintenanceAlert;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Http\Resources\MaintenanceAlertResource;
use App\Http\Resources\MaintenanceOperationResource;
use App\Http\Resources\MaintenanceScheduleResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur API REST pour le module Maintenance Enterprise-Grade
 * API complète avec authentification Sanctum et pagination
 */
class MaintenanceApiController extends Controller
{
    /**
     * Liste des alertes de maintenance
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function alerts(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $query = MaintenanceAlert::with(['vehicle:id,registration_plate,brand,model', 'schedule.maintenanceType'])
            ->where('organization_id', $organizationId);

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'unacknowledged') {
                $query->unacknowledged();
            } elseif ($request->status === 'acknowledged') {
                $query->acknowledged();
            }
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $alerts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => MaintenanceAlertResource::collection($alerts),
            'meta' => [
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
                'per_page' => $alerts->perPage(),
                'total' => $alerts->total(),
            ],
            'links' => [
                'first' => $alerts->url(1),
                'last' => $alerts->url($alerts->lastPage()),
                'prev' => $alerts->previousPageUrl(),
                'next' => $alerts->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Détails d'une alerte spécifique
     */
    public function alertShow(Request $request, int $alertId): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $alert = MaintenanceAlert::with(['vehicle', 'schedule.maintenanceType'])
            ->where('organization_id', $organizationId)
            ->findOrFail($alertId);

        return response()->json([
            'success' => true,
            'data' => new MaintenanceAlertResource($alert)
        ]);
    }

    /**
     * Acquitter une alerte
     */
    public function alertAcknowledge(Request $request, int $alertId): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $alert = MaintenanceAlert::where('organization_id', $organizationId)
            ->findOrFail($alertId);

        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => auth()->id(),
            'acknowledgment_notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerte acquittée avec succès',
            'data' => new MaintenanceAlertResource($alert->fresh())
        ]);
    }

    /**
     * Liste des opérations de maintenance
     */
    public function operations(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $query = MaintenanceOperation::with(['vehicle:id,registration_plate,brand,model', 'maintenanceType', 'provider'])
            ->where('organization_id', $organizationId);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->maintenance_type_id);
        }

        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $operations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => MaintenanceOperationResource::collection($operations),
            'meta' => [
                'current_page' => $operations->currentPage(),
                'last_page' => $operations->lastPage(),
                'per_page' => $operations->perPage(),
                'total' => $operations->total(),
            ],
            'links' => [
                'first' => $operations->url(1),
                'last' => $operations->url($operations->lastPage()),
                'prev' => $operations->previousPageUrl(),
                'next' => $operations->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Créer une nouvelle opération de maintenance
     */
    public function operationCreate(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'provider_id' => 'nullable|exists:maintenance_providers,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'estimated_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'description' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que le véhicule appartient à l'organisation
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $operation = MaintenanceOperation::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $request->vehicle_id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'provider_id' => $request->provider_id,
            'scheduled_date' => $request->scheduled_date,
            'estimated_cost' => $request->estimated_cost,
            'priority' => $request->priority,
            'status' => 'planned',
            'description' => $request->description,
            'notes' => $request->notes,
            'mileage_at_service' => $vehicle->current_mileage,
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Opération créée avec succès',
            'data' => new MaintenanceOperationResource($operation->load(['vehicle', 'maintenanceType', 'provider']))
        ], 201);
    }

    /**
     * Détails d'une opération
     */
    public function operationShow(Request $request, int $operationId): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $operation = MaintenanceOperation::with(['vehicle', 'maintenanceType', 'provider'])
            ->where('organization_id', $organizationId)
            ->findOrFail($operationId);

        return response()->json([
            'success' => true,
            'data' => new MaintenanceOperationResource($operation)
        ]);
    }

    /**
     * Mettre à jour une opération
     */
    public function operationUpdate(Request $request, int $operationId): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $operation = MaintenanceOperation::where('organization_id', $organizationId)
            ->findOrFail($operationId);

        $validator = Validator::make($request->all(), [
            'scheduled_date' => 'sometimes|date',
            'estimated_cost' => 'sometimes|numeric|min:0',
            'total_cost' => 'sometimes|numeric|min:0',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'status' => 'sometimes|in:planned,in_progress,completed,cancelled',
            'description' => 'sometimes|string|max:1000',
            'notes' => 'sometimes|string|max:2000',
            'duration_minutes' => 'sometimes|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'scheduled_date', 'estimated_cost', 'total_cost', 'priority',
            'status', 'description', 'notes', 'duration_minutes'
        ]);

        // Gestion automatique des dates selon le statut
        if (isset($updateData['status'])) {
            switch ($updateData['status']) {
                case 'in_progress':
                    if (!$operation->started_date) {
                        $updateData['started_date'] = now();
                    }
                    break;
                case 'completed':
                    if (!$operation->completed_date) {
                        $updateData['completed_date'] = now();
                    }
                    break;
                case 'cancelled':
                    $updateData['cancelled_date'] = now();
                    $updateData['cancelled_by'] = auth()->id();
                    break;
            }
        }

        $operation->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Opération mise à jour avec succès',
            'data' => new MaintenanceOperationResource($operation->fresh(['vehicle', 'maintenanceType', 'provider']))
        ]);
    }

    /**
     * Liste des planifications de maintenance
     */
    public function schedules(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $query = MaintenanceSchedule::with(['vehicle:id,registration_plate,brand,model', 'maintenanceType'])
            ->where('organization_id', $organizationId);

        // Filtres
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('maintenance_type_id')) {
            $query->where('maintenance_type_id', $request->maintenance_type_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'overdue') {
                $query->overdue();
            }
        }

        if ($request->filled('due_within_days')) {
            $days = (int) $request->due_within_days;
            $query->where('next_due_date', '<=', Carbon::today()->addDays($days));
        }

        // Tri
        $sortBy = $request->get('sort_by', 'next_due_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $schedules = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => MaintenanceScheduleResource::collection($schedules),
            'meta' => [
                'current_page' => $schedules->currentPage(),
                'last_page' => $schedules->lastPage(),
                'per_page' => $schedules->perPage(),
                'total' => $schedules->total(),
            ],
            'links' => [
                'first' => $schedules->url(1),
                'last' => $schedules->url($schedules->lastPage()),
                'prev' => $schedules->previousPageUrl(),
                'next' => $schedules->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Créer une planification de maintenance
     */
    public function scheduleCreate(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'maintenance_type_id' => 'required|exists:maintenance_types,id',
            'next_due_date' => 'nullable|date|after_or_equal:today',
            'next_due_mileage' => 'nullable|integer|min:0',
            'interval_km' => 'nullable|integer|min:1',
            'interval_days' => 'nullable|integer|min:1',
            'alert_km_before' => 'nullable|integer|min:0',
            'alert_days_before' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier que le véhicule appartient à l'organisation
        Vehicle::where('id', $request->vehicle_id)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $schedule = MaintenanceSchedule::create([
            'organization_id' => $organizationId,
            'vehicle_id' => $request->vehicle_id,
            'maintenance_type_id' => $request->maintenance_type_id,
            'next_due_date' => $request->next_due_date,
            'next_due_mileage' => $request->next_due_mileage,
            'interval_km' => $request->interval_km,
            'interval_days' => $request->interval_days,
            'alert_km_before' => $request->alert_km_before ?? 1000,
            'alert_days_before' => $request->alert_days_before ?? 7,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Planification créée avec succès',
            'data' => new MaintenanceScheduleResource($schedule->load(['vehicle', 'maintenanceType']))
        ], 201);
    }

    /**
     * Statistiques dashboard mobile
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $stats = [
            'alerts' => [
                'total' => MaintenanceAlert::where('organization_id', $organizationId)->count(),
                'unacknowledged' => MaintenanceAlert::where('organization_id', $organizationId)->unacknowledged()->count(),
                'critical' => MaintenanceAlert::where('organization_id', $organizationId)
                    ->unacknowledged()
                    ->where('priority', 'critical')
                    ->count(),
            ],
            'operations' => [
                'today' => MaintenanceOperation::where('organization_id', $organizationId)
                    ->whereDate('scheduled_date', today())
                    ->count(),
                'in_progress' => MaintenanceOperation::where('organization_id', $organizationId)
                    ->where('status', 'in_progress')
                    ->count(),
                'this_week' => MaintenanceOperation::where('organization_id', $organizationId)
                    ->whereBetween('scheduled_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])
                    ->count(),
            ],
            'schedules' => [
                'overdue' => MaintenanceSchedule::where('organization_id', $organizationId)->overdue()->count(),
                'due_this_week' => MaintenanceSchedule::where('organization_id', $organizationId)
                    ->active()
                    ->whereBetween('next_due_date', [
                        Carbon::today(),
                        Carbon::today()->addDays(7)
                    ])
                    ->count(),
            ],
            'fleet' => [
                'total_vehicles' => Vehicle::where('organization_id', $organizationId)->count(),
                'maintenance_due' => MaintenanceSchedule::where('organization_id', $organizationId)
                    ->overdue()
                    ->distinct('vehicle_id')
                    ->count('vehicle_id'),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Prochaines opérations (mobile)
     */
    public function upcomingOperations(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $days = $request->get('days', 7);

        $operations = MaintenanceOperation::with(['vehicle:id,registration_plate', 'maintenanceType:id,name'])
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['planned', 'in_progress'])
            ->whereBetween('scheduled_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get()
            ->map(function ($operation) {
                return [
                    'id' => $operation->id,
                    'vehicle' => $operation->vehicle->registration_plate,
                    'maintenance_type' => $operation->maintenanceType->name,
                    'scheduled_date' => $operation->scheduled_date->format('d/m/Y'),
                    'priority' => $operation->priority,
                    'status' => $operation->status
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $operations
        ]);
    }

    /**
     * Alertes critiques récentes (mobile)
     */
    public function criticalAlerts(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $alerts = MaintenanceAlert::with(['vehicle:id,registration_plate'])
            ->where('organization_id', $organizationId)
            ->unacknowledged()
            ->where('priority', 'critical')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($alert) {
                return [
                    'id' => $alert->id,
                    'vehicle' => $alert->vehicle->registration_plate,
                    'message' => $alert->message,
                    'created_at' => $alert->created_at->format('d/m/Y H:i'),
                    'priority' => $alert->priority
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Health check de l'API
     */
    public function health(): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        // Tests de connectivité et de performance
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'organization_access' => $this->checkOrganizationAccess($organizationId),
            'maintenance_module' => $this->checkMaintenanceModule($organizationId)
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'ok');

        return response()->json([
            'success' => $allHealthy,
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'checks' => $checks
        ], $allHealthy ? 200 : 503);
    }

    // Méthodes privées pour les checks de santé

    private function checkDatabase(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    private function checkCache(): array
    {
        try {
            \Cache::put('health_check', time(), 10);
            return ['status' => 'ok', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache is not working'];
        }
    }

    private function checkOrganizationAccess(int $organizationId): array
    {
        try {
            $vehicleCount = Vehicle::where('organization_id', $organizationId)->count();
            return ['status' => 'ok', 'message' => "Organization access OK ({$vehicleCount} vehicles)"];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Organization access failed'];
        }
    }

    private function checkMaintenanceModule(int $organizationId): array
    {
        try {
            $alertCount = MaintenanceAlert::where('organization_id', $organizationId)->count();
            $operationCount = MaintenanceOperation::where('organization_id', $organizationId)->count();
            $scheduleCount = MaintenanceSchedule::where('organization_id', $organizationId)->count();

            return [
                'status' => 'ok',
                'message' => "Maintenance module OK",
                'stats' => [
                    'alerts' => $alertCount,
                    'operations' => $operationCount,
                    'schedules' => $scheduleCount
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Maintenance module check failed'];
        }
    }
}