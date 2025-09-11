<?php

namespace App\Http\Controllers\Admin\Handover;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\VehicleHandoverDetail;
use App\Models\VehicleHandoverForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\PdfGenerationService;

class VehicleHandoverController extends Controller
{
    /**
     * Affiche la vue de création d’une fiche de remise pour une affectation donnée.
     *
     * @param Assignment $assignment
     * @return View
     */
    public function create(Assignment $assignment): View
    {
        $this->authorize('create handovers');

        $assignment->load(['vehicle.vehicleType', 'driver']);

        return view('admin.handovers.vehicles.create', compact('assignment'));
    }

    /**
     * Enregistre une nouvelle fiche de remise et ses détails.
     *
     * @param Request $request
     * @return RedirectResponse
     */
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

        DB::beginTransaction();
        try {
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

            DB::commit();

            Log::channel('audit')->info('Fiche de remise créée', [
                'handover_id' => $handover->id,
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
            ]);

            return redirect()
                ->route('admin.handovers.vehicles.show', $handover)
                ->with('flash', [
                    'type' => 'success',
                    'message' => 'Fiche de remise créée avec succès.',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('errors')->error('Erreur lors de la création de la fiche de remise', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Une erreur est survenue lors de la création de la fiche. Veuillez réessayer.',
            ]);
        }
    }

    /**
     * Affiche la fiche de remise avec ses détails.
     *
     * @param VehicleHandoverForm $handover
     * @return View
     */
    public function show(VehicleHandoverForm $handover): View
    {
        $this->authorize('view handovers');

        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'assignment.organization', 'details']);
        $checklist = $handover->details->groupBy('category');

        return view('admin.handovers.vehicles.show', [
            'handoverForm' => $handover,
            'checklist' => $checklist,
        ]);
    }

    /**
     * Affiche le formulaire d’édition d’une fiche de remise.
     *
     * @param VehicleHandoverForm $handover
     * @return View
     */
    public function edit(VehicleHandoverForm $handover): View
    {
        $this->authorize('edit handovers');

        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'details']);

        $detailsMap = $handover->details->mapWithKeys(function ($detail) {
            return [
                Str::slug($detail->category, '_') . '.' . Str::slug($detail->item, '_') => $detail->status,
            ];
        });

        return view('admin.handovers.vehicles.edit', compact('handover', 'detailsMap'));
    }

    /**
     * Met à jour une fiche de remise et ses détails.
     *
     * @param Request $request
     * @param VehicleHandoverForm $handover
     * @return RedirectResponse
     */
    public function update(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('edit handovers');

        $validated = $request->validate([
            'issue_date' => 'required|date',
            'general_observations' => 'nullable|string|max:2000',
            'checklist' => 'required|array',
            'checklist.*.*' => 'required|in:Bon,Moyen,Mauvais,N/A,Oui,Non',
        ]);

        DB::beginTransaction();
        try {
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
            
            DB::commit();

            Log::channel('audit')->info('Fiche de remise mise à jour', [
                'handover_id' => $handover->id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
            ]);

            return redirect()
                ->route('admin.handovers.vehicles.show', $handover)
                ->with('flash', [
                    'type' => 'success',
                    'message' => 'Fiche de remise mise à jour avec succès.',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('errors')->error('Erreur mise à jour fiche de remise', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            return back()->withInput()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour. Veuillez réessayer.',
            ]);
        }
    }

    /**
     * Téléverse une fiche signée et met à jour le chemin
     *
     * @param Request $request
     * @param VehicleHandoverForm $handover
     * @return RedirectResponse
     */
    public function uploadSigned(Request $request, VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('upload signed handovers');

        $request->validate([
            'signed_form' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            if ($handover->signed_form_path) {
                Storage::disk('public')->delete($handover->signed_form_path);
            }

            $path = $request->file('signed_form')->store('handovers/signed', 'public');

            $handover->update(['signed_form_path' => $path]);

            Log::channel('audit')->info('Fiche signée téléversée', [
                'handover_id' => $handover->id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
            ]);

            return back()->with('flash', [
                'type' => 'success',
                'message' => 'Fiche signée téléversée avec succès.',
            ]);
        } catch (\Exception $e) {
            Log::channel('errors')->error('Erreur téléversement fiche signée', [
                'error' => $e->getMessage(),
                'handover_id' => $handover->id,
                'user_id' => Auth::id(),
            ]);
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Erreur lors du téléversement du fichier signé. Veuillez réessayer.',
            ]);
        }
    }

    /**
     * Supprime une fiche de remise (soft delete)
     *
     * @param VehicleHandoverForm $handover
     * @return RedirectResponse
     */
    public function destroy(VehicleHandoverForm $handover): RedirectResponse
    {
        $this->authorize('delete handovers');

        $assignmentId = $handover->assignment_id;

        try {
            $handover->delete();

            Log::channel('security')->warning('Fiche de remise archivée', [
                'handover_id' => $handover->id,
                'assignment_id' => $assignmentId,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
            ]);

            return redirect()
                ->route('admin.assignments.index')
                ->with('flash', [
                    'type' => 'warning',
                    'message' => 'Fiche de remise archivée',
                    'description' => "La fiche de remise pour l'affectation N°{$assignmentId} a été archivée.",
                ]);
        } catch (\Exception $e) {
            Log::channel('errors')->error('Erreur suppression fiche de remise', [
                'error' => $e->getMessage(),
                'handover_id' => $handover->id,
                'user_id' => Auth::id(),
            ]);
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la suppression.',
            ]);
        }
    }

    /**
     * Génère et télécharge la fiche de remise au format PDF.
     *
     * @param VehicleHandoverForm $handover
     * @param PdfGenerationService $pdfService
     * @return Response|RedirectResponse
     */
    public function downloadPdf(VehicleHandoverForm $handover, PdfGenerationService $pdfService)
    {
        $this->authorize('view handovers');

        $handover->load(['assignment.vehicle.vehicleType', 'assignment.driver', 'assignment.organization', 'details']);
        $checklist = $handover->details->groupBy('category');

        // Sélection de l'image schématique selon le type de véhicule
        $sketchName = $handover->assignment->vehicle->vehicleType->name === 'Moto' 
            ? 'scooter_sketch.png' 
            : 'car_sketch.png';
        $sketchPath = public_path('images/' . $sketchName);
        $vehicleSketchBase64 = '';

        if (File::exists($sketchPath)) {
            $fileContent = File::get($sketchPath);
            $vehicleSketchBase64 = 'data:image/png;base64,' . base64_encode($fileContent);
        }

        $html = view('admin.handovers.vehicles.pdf', [
            'handoverForm' => $handover,
            'checklist' => $checklist,
            'vehicle_sketch_base64' => $vehicleSketchBase64,
        ])->render();

        try {
            $pdfContent = $pdfService->generateFromHtml($html);

            $fileName = 'fiche-remise-' . $handover->assignment->id . '-' . $handover->issue_date->format('Y-m-d') . '.pdf';

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            Log::error("Erreur de génération PDF pour la fiche {$handover->id}: " . $e->getMessage(), ['exception' => $e]);

            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Erreur lors de la génération du PDF.',
                'description' => 'Le service PDF a échoué. Veuillez réessayer ou contacter le support si le problème persiste.',
            ]);
        }
    }
}
