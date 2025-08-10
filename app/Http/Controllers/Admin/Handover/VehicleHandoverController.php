<?php

namespace App\Http\Controllers\Admin\Handover;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Handover\VehicleHandoverForm;
use App\Models\Handover\VehicleHandoverDetail;
use App\Services\PdfGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VehicleHandoverController extends Controller
{
    // ... (les méthodes create, store, show, edit, update, uploadSigned, destroy restent inchangées)
    public function create(Assignment $assignment): View
    {
        $this->authorize('create handovers');
        $assignment->load(['vehicle.vehicleType', 'driver']);
        return view('admin.handovers.vehicles.create', compact('assignment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create handovers');
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id|unique:vehicle_handover_forms,assignment_id',
            'issue_date' => 'required|date',
            'general_observations' => 'nullable|string|max:2000',
            'checklist' => 'required|array',
            'checklist.*.*' => 'required|in:Bon,Moyen,Mauvais,N/A,Oui,Non',
        ]);

        $assignment = Assignment::with('vehicle')->findOrFail($validated['assignment_id']);

        $handoverForm = DB::transaction(function () use ($validated, $assignment) {
            $handover = VehicleHandoverForm::create([
                'assignment_id' => $validated['assignment_id'],
                'issue_date' => $validated['issue_date'],
                'current_mileage' => $assignment->vehicle->current_mileage,
                'general_observations' => $validated['general_observations'],
            ]);

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
            return $handover;
        });

        return redirect()->route('admin.handovers.vehicles.show', $handoverForm)->with('flash', [
            'type' => 'success',
            'message' => 'Fiche de remise créée avec succès.'
        ]);
    }

    public function show(VehicleHandoverForm $handover): View
    {
        $this->authorize('view handovers');
        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'assignment.organization', 'details']);
        $checklist = $handover->details->groupBy('category');
        return view('admin.handovers.vehicles.show', ['handoverForm' => $handover, 'checklist' => $checklist]);
    }

    public function edit(VehicleHandoverForm $handover): View
    {
        $this->authorize('edit handovers');
        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'details']);
        $detailsMap = $handover->details->mapWithKeys(function ($detail) {
            return [Str::slug($detail->category, '_') . '.' . Str::slug($detail->item, '_') => $detail->status];
        });
        return view('admin.handovers.vehicles.edit', compact('handover', 'detailsMap'));
    }

    public function update(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('edit handovers');
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'general_observations' => 'nullable|string|max:2000',
            'checklist' => 'required|array',
            'checklist.*.*' => 'required|in:Bon,Moyen,Mauvais,N/A,Oui,Non',
        ]);

        DB::transaction(function () use ($validated, $handover) {
            $handover->update([
                'issue_date' => $validated['issue_date'],
                'general_observations' => $validated['general_observations'],
            ]);
            
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

        return redirect()->route('admin.handovers.vehicles.show', $handover)->with('flash', [
            'type' => 'success',
            'message' => 'Fiche de remise mise à jour avec succès.'
        ]);
    }
    
    public function uploadSigned(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('upload signed handovers');
        $request->validate([
            'signed_form' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($handover->signed_form_path) {
            Storage::disk('public')->delete($handover->signed_form_path);
        }

        $path = $request->file('signed_form')->store('handovers/signed', 'public');
        $handover->update(['signed_form_path' => $path]);

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Fiche signée téléversée avec succès.'
        ]);
    }
    
    public function destroy(VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('delete handovers');
        
        $assignmentId = $handover->assignment_id;
        $handover->delete();

        return redirect()->route('admin.assignments.index')->with('flash', [
            'type' => 'warning',
            'message' => 'Fiche de remise archivée',
            'description' => "La fiche de remise pour l'affectation N°{$assignmentId} a été archivée."
        ]);
    }

    /**
     * Génère et télécharge la fiche de remise au format PDF via le microservice.
     * VERSION DE DÉBOGAGE
     */
    public function downloadPdf(VehicleHandoverForm $handover, PdfGenerationService $pdfService): Response|RedirectResponse
    {
        $this->authorize('view handovers');
        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'assignment.organization', 'details']);
        $checklist = $handover->details->groupBy('category');

        $html = view('admin.handovers.vehicles.show', [
            'handoverForm' => $handover,
            'checklist' => $checklist,
        ])->render();

        try {
            $pdfContent = $pdfService->generateFromHtml($html);
            $fileName = 'fiche-remise-' . $handover->assignment->id . '-' . $handover->issue_date->format('Y-m-d') . '.pdf';

            return new Response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur de génération PDF pour la fiche {$handover->id}: " . $e->getMessage());
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Erreur lors de la génération du PDF.',
                'description' => 'Le service de PDF a rencontré un problème. Veuillez réessayer plus tard ou contacter le support technique.'
            ]);
        }
    }
}