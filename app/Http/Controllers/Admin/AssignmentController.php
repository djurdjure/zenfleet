<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Admin\Assignment\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Services\AssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AssignmentController extends Controller
{
    protected AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

    /**
     * Affiche la liste des affectations.
     */
    public function index(Request $request): View
    {
        $this->authorize('view assignments');
        $filters = $request->only(['search', 'status', 'per_page']);
        $assignments = $this->assignmentService->getFilteredAssignments($filters);

        return view('admin.assignments.index', compact('assignments', 'filters'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create(): View
    {
         $this->authorize('create assignments');
        $data = $this->assignmentService->getDataForCreateForm();

        return view('admin.assignments.create', $data);
    }

    /**
     * Enregistre une nouvelle affectation.
     */
    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $this->assignmentService->createAssignment($request->validated());
        return redirect()->route('admin.assignments.index')->with('success', 'Affectation créée avec succès.');
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
        $this->assignmentService->updateAssignment($assignment, $request->validated());
        return redirect()->route('admin.assignments.index')->with('success', 'Affectation mise à jour avec succès.');
    }

    /**
     * Termine une affectation en cours.
     */
    public function end(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $this->authorize('end assignments');
        $validated = $request->validate([
            'end_mileage' => ['required', 'integer', 'min:'.$assignment->start_mileage],
        ]);

        $success = $this->assignmentService->endAssignment($assignment, $validated['end_mileage']);

        if ($success) {
            $message = 'Affectation terminée avec succès.';
            // Si la requête attend du JSON (AJAX), on retourne une réponse JSON
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            // Sinon, on fait une redirection classique
            return redirect()->route('admin.assignments.index')->with('success', $message);
        }

        // En cas d'échec
        $errorMessage = 'Une erreur est survenue lors de la clôture de l\'affectation.';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $errorMessage], 500);
        }
        return redirect()->back()->with('error', $errorMessage);
    }
}