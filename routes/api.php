<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MaintenanceApiController;
use App\Http\Controllers\Api\VehicleMileageReadingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'can:view assignments'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::patch('assignments/{assignment}/move', [\App\Http\Controllers\Api\AssignmentController::class, 'move'])->name('assignments.move');
    Route::apiResource('assignments', \App\Http\Controllers\Api\AssignmentController::class)->only(['show', 'update', 'store', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| API V1 Routes - Module Maintenance Enterprise-Grade
|--------------------------------------------------------------------------
|
| Routes API complètes pour le module maintenance avec authentification
| Sanctum, pagination, filtrage et fonctionnalités avancées.
|
*/

Route::prefix('v1')->name('api.v1.')->middleware(['auth:sanctum'])->group(function () {

    // 📊 Module Relevés Kilométriques API
    Route::prefix('mileage-readings')->name('mileage-readings.')->group(function () {

        // RESTful Resource Routes
        Route::get('/', [VehicleMileageReadingController::class, 'index'])
            ->name('index')
            ->middleware('can:view own mileage readings');

        Route::post('/', [VehicleMileageReadingController::class, 'store'])
            ->name('store')
            ->middleware('can:create mileage readings');

        Route::get('/{mileageReading}', [VehicleMileageReadingController::class, 'show'])
            ->name('show');

        Route::put('/{mileageReading}', [VehicleMileageReadingController::class, 'update'])
            ->name('update');

        Route::patch('/{mileageReading}', [VehicleMileageReadingController::class, 'update'])
            ->name('patch');

        Route::delete('/{mileageReading}', [VehicleMileageReadingController::class, 'destroy'])
            ->name('destroy');

        // Additional Routes - Statistics & Export
        Route::get('/statistics/summary', [VehicleMileageReadingController::class, 'statistics'])
            ->name('statistics')
            ->middleware('can:view mileage statistics');

        Route::get('/export/{format}', [VehicleMileageReadingController::class, 'export'])
            ->name('export')
            ->middleware('can:export mileage readings')
            ->where('format', 'csv|xlsx|pdf');

        // Bulk Operations
        Route::post('/bulk-delete', [VehicleMileageReadingController::class, 'bulkDelete'])
            ->name('bulk-delete')
            ->middleware('can:delete mileage readings');
    });

    // 🔧 Module Maintenance API
    Route::prefix('maintenance')->name('maintenance.')->group(function () {

        // 🚨 Alertes de Maintenance
        Route::prefix('alerts')->name('alerts.')->group(function () {
            Route::get('/', [MaintenanceApiController::class, 'alerts'])->name('index');
            Route::get('/{alert}', [MaintenanceApiController::class, 'alertShow'])->name('show');
            Route::post('/{alert}/acknowledge', [MaintenanceApiController::class, 'alertAcknowledge'])->name('acknowledge');
            Route::get('/critical/latest', [MaintenanceApiController::class, 'criticalAlerts'])->name('critical');
        });

        // 🔧 Opérations de Maintenance
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::get('/', [MaintenanceApiController::class, 'operations'])->name('index');
            Route::post('/', [MaintenanceApiController::class, 'operationCreate'])->name('create');
            Route::get('/{operation}', [MaintenanceApiController::class, 'operationShow'])->name('show');
            Route::put('/{operation}', [MaintenanceApiController::class, 'operationUpdate'])->name('update');
            Route::get('/upcoming/list', [MaintenanceApiController::class, 'upcomingOperations'])->name('upcoming');
        });

        // 📅 Planifications de Maintenance
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/', [MaintenanceApiController::class, 'schedules'])->name('index');
            Route::post('/', [MaintenanceApiController::class, 'scheduleCreate'])->name('create');
        });

        // 📊 Dashboard et Statistiques
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/stats', [MaintenanceApiController::class, 'dashboardStats'])->name('stats');
        });

        // 🏥 Health Check et Monitoring
        Route::get('/health', [MaintenanceApiController::class, 'health'])->name('health');
    });

    // 🚗 API Véhicules (pour référence croisée maintenance)
    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::get('/', function (Request $request) {
            $organizationId = auth()->user()->organization_id;

            $vehicles = \App\Models\Vehicle::where('organization_id', $organizationId)
                ->select(['id', 'registration_plate', 'brand', 'model', 'current_mileage'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('registration_plate', 'ILIKE', "%{$search}%")
                          ->orWhere('brand', 'ILIKE', "%{$search}%")
                          ->orWhere('model', 'ILIKE', "%{$search}%");
                    });
                })
                ->orderBy('registration_plate')
                ->paginate(min($request->get('per_page', 20), 100));

            return response()->json([
                'success' => true,
                'data' => $vehicles->items(),
                'meta' => [
                    'current_page' => $vehicles->currentPage(),
                    'last_page' => $vehicles->lastPage(),
                    'per_page' => $vehicles->perPage(),
                    'total' => $vehicles->total(),
                ]
            ]);
        })->name('index');

        Route::get('/{vehicle}', function (Request $request, int $vehicleId) {
            $organizationId = auth()->user()->organization_id;

            $vehicle = \App\Models\Vehicle::where('organization_id', $organizationId)
                ->findOrFail($vehicleId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                    'current_mileage' => $vehicle->current_mileage,
                    'vehicle_type' => $vehicle->vehicle_type,
                    'year' => $vehicle->year,
                    'display_name' => $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->registration_plate . ')'
                ]
            ]);
        })->name('show');
    });

    // 📋 API Types de Maintenance
    Route::prefix('maintenance-types')->name('maintenance_types.')->group(function () {
        Route::get('/', function (Request $request) {
            $organizationId = auth()->user()->organization_id;

            $types = \App\Models\MaintenanceType::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->when($request->filled('category'), function ($query) use ($request) {
                    $query->where('category', $request->category);
                })
                ->when($request->filled('recurring'), function ($query) use ($request) {
                    $query->where('is_recurring', $request->boolean('recurring'));
                })
                ->select([
                    'id', 'name', 'description', 'category', 'is_recurring',
                    'estimated_duration_minutes', 'estimated_cost'
                ])
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $types->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'description' => $type->description,
                        'category' => $type->category,
                        'category_label' => match($type->category) {
                            'preventive' => 'Préventive',
                            'corrective' => 'Corrective',
                            'inspection' => 'Inspection',
                            'revision' => 'Révision',
                            default => $type->category
                        },
                        'is_recurring' => $type->is_recurring,
                        'estimated_duration_minutes' => $type->estimated_duration_minutes,
                        'estimated_cost' => $type->estimated_cost
                    ];
                })
            ]);
        })->name('index');
    });

    // 🏢 API Fournisseurs de Maintenance
    Route::prefix('maintenance-providers')->name('maintenance_providers.')->group(function () {
        Route::get('/', function (Request $request) {
            $organizationId = auth()->user()->organization_id;

            $providers = \App\Models\MaintenanceProvider::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->when($request->filled('specialty'), function ($query) use ($request) {
                    $query->whereJsonContains('specialties', $request->specialty);
                })
                ->select(['id', 'name', 'company_name', 'phone', 'email', 'rating', 'specialties'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $providers
            ]);
        })->name('index');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes Publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Health check public
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'ZenFleet API',
        'version' => '1.0.0'
    ]);
});

// API Documentation endpoint
Route::get('/docs', function () {
    return response()->json([
        'service' => 'ZenFleet API',
        'version' => '1.0.0',
        'documentation' => [
            'base_url' => url('/api'),
            'authentication' => 'Bearer token (Sanctum)',
            'endpoints' => [
                'maintenance' => [
                    'alerts' => 'GET /v1/maintenance/alerts',
                    'operations' => 'GET /v1/maintenance/operations',
                    'schedules' => 'GET /v1/maintenance/schedules',
                    'dashboard' => 'GET /v1/maintenance/dashboard/stats'
                ],
                'vehicles' => 'GET /v1/vehicles',
                'maintenance_types' => 'GET /v1/maintenance-types',
                'providers' => 'GET /v1/maintenance-providers'
            ],
            'common_parameters' => [
                'per_page' => 'Nombre d\'éléments par page (max 100)',
                'page' => 'Numéro de page',
                'sort_by' => 'Champ de tri',
                'sort_order' => 'Ordre de tri (asc/desc)'
            ]
        ]
    ]);
});

/*
|--------------------------------------------------------------------------
| Webhooks et Intégrations Externes
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->name('webhooks.')->group(function () {

    // Webhook pour notifications externes d'alertes critiques
    Route::post('/maintenance/critical-alert', function (Request $request) {
        // Validation du token webhook
        $expectedToken = config('app.webhook_token');
        if (!$expectedToken || $request->header('X-Webhook-Token') !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Traitement du webhook
        \Log::info('Critical maintenance alert webhook received', $request->all());

        return response()->json(['status' => 'received']);
    })->name('critical_alert');

    // Webhook pour mise à jour automatique du kilométrage
    Route::post('/vehicle/mileage-update', function (Request $request) {
        $expectedToken = config('app.webhook_token');
        if (!$expectedToken || $request->header('X-Webhook-Token') !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|integer',
            'current_mileage' => 'required|integer|min:0',
            'timestamp' => 'required|date'
        ]);

        // Mise à jour du kilométrage si plus récent
        $vehicle = \App\Models\Vehicle::find($validated['vehicle_id']);
        if ($vehicle && $validated['current_mileage'] > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $validated['current_mileage']]);

            // Déclencher la vérification des planifications
            \App\Jobs\Maintenance\CheckMaintenanceSchedulesJob::dispatch(
                $vehicle->organization_id,
                false
            );
        }

        return response()->json(['status' => 'updated']);
    })->name('mileage_update');
});