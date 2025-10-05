<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleMileageReadingRequest;
use App\Http\Requests\UpdateVehicleMileageReadingRequest;
use App\Models\VehicleMileageReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * VehicleMileageReadingController - API REST pour les relevés kilométriques
 *
 * Endpoints:
 * - GET    /api/mileage-readings           - Liste des relevés (scoped by permissions)
 * - POST   /api/mileage-readings           - Créer un relevé
 * - GET    /api/mileage-readings/{id}      - Détails d'un relevé
 * - PUT    /api/mileage-readings/{id}      - Modifier un relevé
 * - DELETE /api/mileage-readings/{id}      - Supprimer un relevé
 *
 * Features:
 * - Multi-tenant isolation (organization_id)
 * - Permission-based access (Spatie Laravel Permission)
 * - Policy authorization (VehicleMileageReadingPolicy)
 * - Automatic vehicle.current_mileage update via Observer
 * - Comprehensive error handling
 * - JSON responses with proper status codes
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReadingController extends Controller
{
    /**
     * Display a listing of mileage readings.
     *
     * Scoped by user permissions:
     * - view own: Only user's readings
     * - view team: Team/depot readings
     * - view all: All organization readings
     *
     * Query parameters:
     * - vehicle_id: Filter by vehicle
     * - recording_method: Filter by method (manual/automatic)
     * - from_date: Filter readings from date
     * - to_date: Filter readings to date
     * - per_page: Pagination (default: 15)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', VehicleMileageReading::class);

        $user = auth()->user();

        // Base query with organization isolation
        $query = VehicleMileageReading::query()
            ->where('organization_id', $user->organization_id)
            ->with(['vehicle', 'recordedBy', 'organization']);

        // Apply permission-based scoping
        if ($user->can('view all mileage readings')) {
            // Admin/Fleet Manager: See all organization readings
            // No additional filter needed
        } elseif ($user->can('view team mileage readings')) {
            // Supervisor: See team readings (same depot)
            $query->whereHas('vehicle', function ($q) use ($user) {
                if ($user->depot_id) {
                    $q->where('depot_id', $user->depot_id);
                }
            });
        } else {
            // Driver: See only own readings
            $query->where('recorded_by_id', $user->id);
        }

        // Apply filters
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('recording_method')) {
            $query->where('recording_method', $request->recording_method);
        }

        if ($request->filled('from_date')) {
            $query->where('recorded_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('recorded_at', '<=', $request->to_date);
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $readings = $query->latest('recorded_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Relevés kilométriques récupérés avec succès',
            'data' => $readings->items(),
            'meta' => [
                'current_page' => $readings->currentPage(),
                'last_page' => $readings->lastPage(),
                'per_page' => $readings->perPage(),
                'total' => $readings->total(),
                'from' => $readings->firstItem(),
                'to' => $readings->lastItem(),
            ],
        ], 200);
    }

    /**
     * Store a newly created mileage reading.
     *
     * Business rules:
     * - Validates mileage >= vehicle.current_mileage
     * - Sets recorded_by_id = auth()->id() for manual readings
     * - Observer automatically updates vehicle.current_mileage
     * - Multi-tenant isolation via organization_id
     *
     * @param StoreVehicleMileageReadingRequest $request
     * @return JsonResponse
     */
    public function store(StoreVehicleMileageReadingRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Create mileage reading
            // Note: organization_id, recorded_by_id are set in FormRequest::passedValidation()
            $reading = VehicleMileageReading::create($request->validated());

            // Load relationships for response
            $reading->load(['vehicle', 'recordedBy', 'organization']);

            // Observer automatically updates vehicle.current_mileage if needed

            DB::commit();

            Log::info('Mileage reading created', [
                'reading_id' => $reading->id,
                'vehicle_id' => $reading->vehicle_id,
                'mileage' => $reading->mileage,
                'user_id' => auth()->id(),
                'organization_id' => auth()->user()->organization_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Relevé kilométrique créé avec succès',
                'data' => [
                    'reading' => $reading,
                    'vehicle_updated' => [
                        'id' => $reading->vehicle->id,
                        'current_mileage' => $reading->vehicle->fresh()->current_mileage,
                        'formatted_current_mileage' => $reading->vehicle->fresh()->formatted_current_mileage,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create mileage reading', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du relevé kilométrique',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified mileage reading.
     *
     * @param VehicleMileageReading $mileageReading
     * @return JsonResponse
     */
    public function show(VehicleMileageReading $mileageReading): JsonResponse
    {
        $this->authorize('view', $mileageReading);

        // Load relationships
        $mileageReading->load(['vehicle', 'recordedBy', 'organization']);

        return response()->json([
            'success' => true,
            'message' => 'Relevé kilométrique récupéré avec succès',
            'data' => [
                'reading' => $mileageReading,
                'statistics' => [
                    'difference_from_previous' => $mileageReading->getMileageDifference(),
                    'is_consistent' => $mileageReading->isConsistent(),
                    'formatted_mileage' => $mileageReading->formatted_mileage,
                ],
            ],
        ], 200);
    }

    /**
     * Update the specified mileage reading.
     *
     * Business rules:
     * - Automatic readings cannot be updated (except by admins)
     * - Drivers can only update within 24h (enforced by Policy)
     * - Mileage consistency validation
     * - Observer recalculates vehicle.current_mileage if needed
     *
     * @param UpdateVehicleMileageReadingRequest $request
     * @param VehicleMileageReading $mileageReading
     * @return JsonResponse
     */
    public function update(UpdateVehicleMileageReadingRequest $request, VehicleMileageReading $mileageReading): JsonResponse
    {
        DB::beginTransaction();

        try {
            $oldMileage = $mileageReading->mileage;

            // Update reading
            $mileageReading->update($request->validated());

            // Load fresh data
            $mileageReading->refresh();
            $mileageReading->load(['vehicle', 'recordedBy', 'organization']);

            // Observer automatically handles vehicle.current_mileage update

            DB::commit();

            Log::info('Mileage reading updated', [
                'reading_id' => $mileageReading->id,
                'vehicle_id' => $mileageReading->vehicle_id,
                'old_mileage' => $oldMileage,
                'new_mileage' => $mileageReading->mileage,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Relevé kilométrique mis à jour avec succès',
                'data' => [
                    'reading' => $mileageReading,
                    'vehicle_current_mileage' => $mileageReading->vehicle->fresh()->current_mileage,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update mileage reading', [
                'reading_id' => $mileageReading->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du relevé kilométrique',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified mileage reading.
     *
     * Business rules:
     * - Drivers can only delete own readings (enforced by Policy)
     * - Readings > 7 days old require admin permission
     * - Observer recalculates vehicle.current_mileage from remaining readings
     *
     * @param VehicleMileageReading $mileageReading
     * @return JsonResponse
     */
    public function destroy(VehicleMileageReading $mileageReading): JsonResponse
    {
        $this->authorize('delete', $mileageReading);

        DB::beginTransaction();

        try {
            $vehicleId = $mileageReading->vehicle_id;
            $mileageValue = $mileageReading->mileage;

            // Soft delete
            $mileageReading->delete();

            // Observer automatically recalculates vehicle.current_mileage

            DB::commit();

            Log::info('Mileage reading deleted', [
                'reading_id' => $mileageReading->id,
                'vehicle_id' => $vehicleId,
                'mileage' => $mileageValue,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Relevé kilométrique supprimé avec succès',
                'data' => [
                    'deleted_reading_id' => $mileageReading->id,
                    'vehicle_id' => $vehicleId,
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete mileage reading', [
                'reading_id' => $mileageReading->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du relevé kilométrique',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
