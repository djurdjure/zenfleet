<?php
// /app/Http/Controllers/Admin/Handover/VehicleHandoverController.php

namespace App\Http\Controllers\Admin\Handover;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Handover\VehicleHandoverForm;
use App\Models\Handover\VehicleHandoverDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VehicleHandoverController extends Controller
{
    // ... les méthodes create() et store() restent inchangées ...
    public function create(Assignment $assignment): View
    {
        $assignment->load(['vehicle.vehicleType', 'driver']);
        return view('admin.handovers.vehicles.create', compact('assignment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id|unique:vehicle_handover_forms,assignment_id',
            'issue_date' => 'required|date',
            'general_observations' => 'nullable|string|max:2000',
            'checklist' => 'required|array',
            'checklist.*.*' => 'required|in:Bon,Moyen,Mauvais,N/A,Oui,Non',
        ]);
        
        $assignment = Assignment::with('vehicle')->findOrFail($validated['assignment_id']);

        DB::transaction(function () use ($validated, $assignment) {
            $handoverForm = VehicleHandoverForm::create([
                'assignment_id' => $validated['assignment_id'],
                'issue_date' => $validated['issue_date'],
                'current_mileage' => $assignment->vehicle->current_mileage,
                'general_observations' => $validated['general_observations'],
            ]);

            foreach ($validated['checklist'] as $category => $items) {
                foreach ($items as $itemKey => $status) {
                    VehicleHandoverDetail::create([
                        'handover_form_id' => $handoverForm->id,
                        'category' => str_replace('_', ' ', $category),
                        'item' => str_replace('_', ' ', $itemKey),
                        'status' => $status,
                    ]);
                }
            }
        });

        return redirect()->route('admin.assignments.index')->with('success', 'Fiche de remise créée avec succès.');
    }

    public function show(VehicleHandoverForm $handover): View
    {
        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'details']);
        $checklist = $handover->details->groupBy('category');
        return view('admin.handovers.vehicles.show', ['handoverForm' => $handover, 'checklist' => $checklist]);
    }


        public function edit(VehicleHandoverForm $handover): View
    {
        // On charge l'affectation pour pouvoir éditer son motif
        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'details']);

        $detailsMap = $handover->details->mapWithKeys(function ($detail) {
            return [Str::slug($detail->category, '_') . '.' . Str::slug($detail->item, '_') => $detail->status];
        });

        return view('admin.handovers.vehicles.edit', compact('handover', 'detailsMap'));
    }

    public function update(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'general_observations' => 'nullable|string|max:2000',
            'reason' => 'nullable|string|max:500', // Validation du motif
            'checklist' => 'required|array',
            'checklist.*.*' => 'required|in:Bon,Moyen,Mauvais,N/A,Oui,Non',
        ]);

        DB::transaction(function () use ($validated, $request, $handover) {
            // **LA CORRECTION CLÉ**
            // 1. Mettre à jour le motif sur le modèle d'affectation parent
            $handover->assignment()->update([
                'reason' => $request->reason,
            ]);

            // 2. Mettre à jour les champs propres à la fiche de remise
            $handover->update([
                'issue_date' => $validated['issue_date'],
                'general_observations' => $validated['general_observations'],
            ]);

            // 3. Mettre à jour la checklist
            $handover->details()->delete();
            foreach ($validated['checklist'] as $category => $items) {
                foreach ($items as $itemKey => $status) {
                    VehicleHandoverDetail::create([
                        'handover_form_id' => $handover->id,
                        'category' => str_replace('_', ' ', $category),
                        'item' => str_replace('_', ' ', $itemKey),
                        'status' => $status,
                    ]);
                }
            }
        });

        return redirect()->route('admin.handovers.vehicles.show', $handover)->with('success', 'Fiche de remise mise à jour avec succès.');
    }


    /**
     * Gère le téléversement de la fiche signée et archivée.
     */
    public function uploadSigned(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $validated = $request->validate([
            'signed_form' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
        ]);

        // Supprimer l'ancienne fiche signée si elle existe
        if ($handover->signed_form_path) {
            Storage::disk('public')->delete($handover->signed_form_path);
        }

        // Enregistrer le nouveau fichier
        $path = $request->file('signed_form')->store('handovers/signed', 'public');
        $handover->update(['signed_form_path' => $path]);

        return back()->with('success', 'Fiche signée téléversée avec succès.');
    }

}