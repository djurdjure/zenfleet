<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VehiclesExport;
use App\Exports\VehiclesCsvExport;
use App\Services\VehiclePdfExportService;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 📦 Extension Methods for VehicleController
 * 
 * Ces méthodes doivent être ajoutées au VehicleController existant
 * 
 * @package App\Http\Controllers\Admin
 * @version 1.0
 * @since 2025-11-03
 */
trait VehicleControllerExtensions
{
    // START: Tâche 1 - Méthodes d'Exportation
    
    /**
     * 📊 Export des véhicules en CSV
     */
    public function exportCsv(Request $request)
    {
        $this->logUserAction('vehicle.export.csv', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('export vehicles')) {
                abort(403, 'Non autorisé à exporter les véhicules');
            }

            $filters = $request->all();
            $csvExport = new VehiclesCsvExport($filters);
            
            return $csvExport->download();
            
        } catch (\Exception $e) {
            $this->logError('vehicle.export.csv.error', $e, ['request' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export CSV: ' . $e->getMessage());
        }
    }

    /**
     * 📊 Export des véhicules en Excel
     */
    public function exportExcel(Request $request)
    {
        $this->logUserAction('vehicle.export.excel', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('export vehicles')) {
                abort(403, 'Non autorisé à exporter les véhicules');
            }

            $filters = $request->all();
            $fileName = 'vehicles_export_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(new VehiclesExport($filters), $fileName);
            
        } catch (\Exception $e) {
            $this->logError('vehicle.export.excel.error', $e, ['request' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export Excel: ' . $e->getMessage());
        }
    }

    /**
     * 📑 Export des véhicules en PDF (Liste)
     */
    public function exportPdf(Request $request)
    {
        $this->logUserAction('vehicle.export.pdf', $request);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('export vehicles')) {
                abort(403, 'Non autorisé à exporter les véhicules');
            }

            $filters = $request->all();
            $pdfService = new VehiclePdfExportService($filters);
            
            return $pdfService->exportList();
            
        } catch (\Exception $e) {
            $this->logError('vehicle.export.pdf.error', $e, ['request' => $request->all()]);
            return back()->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }

    /**
     * 📑 Export d'un véhicule unique en PDF
     */
    public function exportSinglePdf(Vehicle $vehicle)
    {
        $this->logUserAction('vehicle.export.single.pdf', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('view vehicles')) {
                abort(403, 'Non autorisé à voir ce véhicule');
            }

            // Vérifier l'appartenance à l'organisation
            if ($vehicle->organization_id !== Auth::user()->organization_id) {
                abort(403, 'Véhicule non trouvé');
            }

            $pdfService = new VehiclePdfExportService();
            
            return $pdfService->exportSingle($vehicle->id);
            
        } catch (\Exception $e) {
            $this->logError('vehicle.export.single.pdf.error', $e, ['vehicle_id' => $vehicle->id]);
            return back()->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }

    // END: Tâche 1 - Méthodes d'Exportation
    
    // START: Tâche 2 - Méthode de Duplication

    /**
     * 🔄 Dupliquer un véhicule
     */
    public function duplicate(Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.duplicate', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('create vehicles')) {
                abort(403, 'Non autorisé à créer des véhicules');
            }

            // Vérifier l'appartenance à l'organisation
            if ($vehicle->organization_id !== Auth::user()->organization_id) {
                abort(403, 'Véhicule non trouvé');
            }

            DB::beginTransaction();

            // Créer une copie du véhicule
            $newVehicle = $vehicle->replicate([
                'id',
                'registration_plate', // Sera modifié
                'vin', // Sera modifié
                'created_at',
                'updated_at',
                'is_archived',
                'current_mileage' // Reset à 0 pour le nouveau
            ]);

            // Générer une nouvelle immatriculation unique
            $baseRegistration = $vehicle->registration_plate;
            $counter = 1;
            do {
                $newRegistration = $baseRegistration . '-COPY' . $counter;
                $exists = Vehicle::where('registration_plate', $newRegistration)
                    ->where('organization_id', Auth::user()->organization_id)
                    ->exists();
                $counter++;
            } while ($exists);

            $newVehicle->registration_plate = $newRegistration;
            
            // Générer un nouveau VIN si présent
            if ($vehicle->vin) {
                $newVehicle->vin = $vehicle->vin . '-COPY';
            }
            
            // Reset certaines valeurs
            $newVehicle->current_mileage = 0;
            $newVehicle->is_archived = false;
            
            // Ajouter une note sur la duplication
            $newVehicle->notes = 'Dupliqué depuis ' . $vehicle->registration_plate . ' le ' . now()->format('d/m/Y à H:i') . 
                                 ($vehicle->notes ? "\n\nNotes originales:\n" . $vehicle->notes : '');
            
            // Sauvegarder le nouveau véhicule
            $newVehicle->save();

            // Dupliquer les documents associés si nécessaire
            if ($vehicle->documents && $vehicle->documents->count() > 0) {
                foreach ($vehicle->documents as $document) {
                    $newDocument = $document->replicate(['id', 'vehicle_id', 'created_at', 'updated_at']);
                    $newDocument->vehicle_id = $newVehicle->id;
                    $newDocument->save();
                }
            }

            DB::commit();

            // Log de l'action
            Log::info('Vehicle duplicated', [
                'original_id' => $vehicle->id,
                'new_id' => $newVehicle->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.vehicles.edit', $newVehicle)
                ->with('success', 'Véhicule dupliqué avec succès. Veuillez mettre à jour les informations nécessaires.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError('vehicle.duplicate.error', $e, ['vehicle_id' => $vehicle->id]);
            return back()->with('error', 'Erreur lors de la duplication: ' . $e->getMessage());
        }
    }

    // END: Tâche 2 - Méthode de Duplication

    /**
     * 🕐 Historique du véhicule (Timeline)
     */
    public function history(Vehicle $vehicle)
    {
        $this->logUserAction('vehicle.history', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Vérifier les permissions
            if (!Auth::user()->can('view vehicles')) {
                abort(403, 'Non autorisé à voir l\'historique');
            }

            // Vérifier l'appartenance à l'organisation
            if ($vehicle->organization_id !== Auth::user()->organization_id) {
                abort(403, 'Véhicule non trouvé');
            }

            // Charger l'historique complet
            $vehicle->load([
                'assignments' => function($q) {
                    $q->with('driver.user')
                      ->orderBy('assigned_at', 'desc');
                },
                'maintenances' => function($q) {
                    $q->orderBy('scheduled_date', 'desc');
                },
                'mileageReadings' => function($q) {
                    $q->orderBy('recorded_at', 'desc')
                      ->limit(50);
                },
                'documents' => function($q) {
                    $q->orderBy('created_at', 'desc');
                },
                'expenses' => function($q) {
                    $q->orderBy('expense_date', 'desc')
                      ->limit(50);
                }
            ]);

            // Créer une timeline consolidée
            $timeline = collect();

            // Ajouter les affectations
            foreach ($vehicle->assignments as $assignment) {
                $timeline->push([
                    'type' => 'assignment',
                    'date' => $assignment->assigned_at,
                    'icon' => 'user-check',
                    'color' => 'blue',
                    'title' => 'Affectation',
                    'description' => 'Affecté à ' . ($assignment->driver->user->name ?? 'N/A'),
                    'data' => $assignment
                ]);
            }

            // Ajouter les maintenances
            foreach ($vehicle->maintenances ?? collect() as $maintenance) {
                $timeline->push([
                    'type' => 'maintenance',
                    'date' => $maintenance->scheduled_date,
                    'icon' => 'wrench',
                    'color' => 'orange',
                    'title' => 'Maintenance',
                    'description' => $maintenance->description,
                    'data' => $maintenance
                ]);
            }

            // Ajouter les relevés kilométriques significatifs
            foreach ($vehicle->mileageReadings ?? collect() as $reading) {
                $timeline->push([
                    'type' => 'mileage',
                    'date' => $reading->recorded_at,
                    'icon' => 'gauge',
                    'color' => 'green',
                    'title' => 'Relevé kilométrique',
                    'description' => number_format($reading->mileage) . ' km',
                    'data' => $reading
                ]);
            }

            // Trier par date décroissante
            $timeline = $timeline->sortByDesc('date')->values();

            return view('admin.vehicles.history', compact('vehicle', 'timeline'));

        } catch (\Exception $e) {
            $this->logError('vehicle.history.error', $e, ['vehicle_id' => $vehicle->id]);
            return back()->with('error', 'Erreur lors du chargement de l\'historique');
        }
    }
}
