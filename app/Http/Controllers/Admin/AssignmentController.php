<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // <--- L'INSTRUCTION CAPITALE QUI MANQUAIT
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssignmentController extends Controller
{


    ////////////////____________ lE RESTE DES FONCTIONS
    /**
     * Affiche la liste des affectations avec filtres, recherche et pagination.
     */
    /**
     * Affiche la liste des affectations avec une recherche insensible à la casse.
     */
    public function index(Request $request): View
    {
        $this->authorize('view assignments');

        $perPage = $request->query('per_page', 15);
        $query = Assignment::with(['vehicle', 'driver']);

        // Moteur de Recherche (CORRIGÉ pour être insensible à la casse)
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('vehicle', function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(registration_plate) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(brand) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(model) LIKE ?', ["%{$searchTerm}%"]);
                })
                ->orWhereHas('driver', function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ["%{$searchTerm}%"]);
                });
            });
        }

        $assignments = $query->orderBy('start_datetime', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.assignments.index', [
            'assignments' => $assignments,
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }
    
     /**
     * Affiche le formulaire de création en ne listant que les ressources disponibles.
     */
    public function create(): View
    {
        $this->authorize('create assignments');
        
        // CORRIGÉ : On ne cherche que les véhicules au "Parking"
        $availableVehicles = Vehicle::whereHas('vehicleStatus', function ($q) {
            $q->where('name', 'Parking');
        })->whereDoesntHave('assignments', function ($q) {
            $q->whereNull('end_datetime');
        })->get();

        // CORRIGÉ : On ne cherche que les chauffeurs "Disponibles"
        $availableDrivers = Driver::whereHas('driverStatus', function ($q) {
            $q->where('name', 'Disponible');
        })->whereDoesntHave('assignments', function ($q) {
            $q->whereNull('end_datetime');
        })->get();

        return view('admin.assignments.create', compact('availableVehicles', 'availableDrivers'));
    }

    /**
     * Enregistre une nouvelle affectation et met à jour les statuts.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create assignments');

        $validated = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'start_datetime' => ['required', 'date'],
            'start_mileage' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->isCurrentlyAssigned()) {
            return back()->withInput()->withErrors(['vehicle_id' => 'Ce véhicule est déjà en cours d\'affectation.']);
        }

        $driver = Driver::findOrFail($validated['driver_id']);
        if ($driver->isCurrentlyAssigned()) {
            return back()->withInput()->withErrors(['driver_id' => 'Ce chauffeur est déjà en cours d\'affectation.']);
        }

        $assignmentData = $validated;
        $assignmentData['created_by_user_id'] = Auth::id();
        Assignment::create($assignmentData);

        // MISE À JOUR : Changement de statut vers "En mission"
        $vehicle->update([
            'current_mileage' => $validated['start_mileage'],
            'status_id' => VehicleStatus::where('name', 'En mission')->firstOrFail()->id,
        ]);
        $driver->update(['status_id' => DriverStatus::where('name', 'En mission')->firstOrFail()->id]);
        
        return redirect()->route('admin.assignments.index')->with('success', 'Nouvelle affectation créée avec succès.');
    }

    /**
     * Termine une affectation et met à jour les statuts.
     */
    public function end(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $this->authorize('end assignments');

        $validator = Validator::make($request->all(), [
            'end_datetime' => ['required', 'date', 'after_or_equal:' . $assignment->start_datetime],
            'end_mileage' => ['required', 'integer', 'gte:' . $assignment->start_mileage],
        ]);

        if ($validator->fails()) {
            return $request->expectsJson()
                ? response()->json(['errors' => $validator->errors()], 422)
                : back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $assignment->update($validated);

        // MISE À JOUR : Libération avec les nouveaux statuts
        $assignment->vehicle->update([
            'status_id' => VehicleStatus::where('name', 'Parking')->firstOrFail()->id,
            'current_mileage' => $validated['end_mileage'],
        ]);
        $assignment->driver->update(['status_id' => DriverStatus::where('name', 'Disponible')->firstOrFail()->id]);
        
        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Affectation terminée avec succès.'])
            : redirect()->route('admin.assignments.index')->with('success', 'Affectation terminée avec succès.');
    }


  /**
     * Affiche le formulaire pour modifier les détails d'une affectation.
     */
    public function edit(Assignment $assignment): View
    {
        $this->authorize('edit assignments');

        // On charge les relations nécessaires pour la vue
        $assignment->load(['vehicle', 'driver']);

        return view('admin.assignments.edit', compact('assignment'));
    }

    /**
     * Met à jour les détails d'une affectation.
     */
    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        $this->authorize('edit assignments');

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')->with('success', 'Affectation mise à jour avec succès.');
    }





}
