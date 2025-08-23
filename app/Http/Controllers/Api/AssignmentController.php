<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\Assignment\StoreAssignmentRequest;
use App\Services\AssignmentService;

class AssignmentController extends Controller
{
    protected AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment): JsonResponse
    {
        $this->authorize('view assignments');
        $assignment->load(['vehicle', 'driver']);
        return response()->json($assignment);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssignmentRequest $request): JsonResponse
    {
        $this->authorize('create assignments');
        $assignment = $this->assignmentService->createAssignment($request->validated());
        return response()->json($assignment, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        $this->authorize('edit assignments');
        // Validation will be more detailed later
        $validated = $request->validate([
            'driver_id' => 'sometimes|required|exists:drivers,id',
            'start_datetime' => 'sometimes|required|date',
            'end_datetime' => 'nullable|date|after_or_equal:start_datetime',
            // Add other fields as needed
        ]);

        $this->assignmentService->updateAssignment($assignment, $validated);

        return response()->json($assignment->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment): JsonResponse
    {
        $this->authorize('delete assignments');
        $assignment->delete();
        return response()->json(null, 204);
    }

    /**
     * Move or resize an assignment from the GANTT chart.
     */
    public function move(Request $request, Assignment $assignment): JsonResponse
    {
        $this->authorize('edit assignments');

        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'vehicle_id' => 'sometimes|required|exists:vehicles,id'
        ]);

        // Basic conflict check (will be enhanced later)
        $conflicting = Assignment::where('vehicle_id', $validated['vehicle_id'] ?? $assignment->vehicle_id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('start_datetime', '<', $validated['end_datetime'])
                      ->where('end_datetime', '>', $validated['start_datetime']);
                });
            })->exists();

        if ($conflicting) {
            return response()->json(['message' => 'Conflit d\'affectation détecté.'], 409);
        }

        $assignment->update($validated);

        return response()->json($assignment);
    }
}