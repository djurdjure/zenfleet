<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleDepot;
use App\Models\Vehicle;
use App\Services\DepotAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * VehicleDepotController - Gestion Enterprise-Grade des Dépôts
 *
 * Fonctionnalités:
 * - Affichage fiche dépôt (format document professionnel)
 * - Export PDF imprimable
 * - Statistiques détaillées
 * - Suppression soft delete
 * - Liste véhicules assignés
 *
 * @package App\Http\Controllers\Admin
 */
class VehicleDepotController extends Controller
{
    protected DepotAssignmentService $depotService;

    public function __construct(DepotAssignmentService $depotService)
    {
        $this->depotService = $depotService;
    }

    /**
     * Afficher la fiche détaillée d'un dépôt
     *
     * Format document professionnel imprimable
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $depot = VehicleDepot::where('id', $id)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        // Statistiques du dépôt
        $stats = $this->depotService->getDepotStats($depot);

        // Véhicules assignés avec détails
        $vehicles = Vehicle::where('depot_id', $depot->id)
            ->where('organization_id', Auth::user()->organization_id)
            ->with(['vehicleType', 'fuelType', 'vehicleStatus'])
            ->orderBy('registration_plate')
            ->get();

        // Grouper véhicules par statut
        $vehiclesByStatus = $vehicles->groupBy(function ($vehicle) {
            return $vehicle->vehicleStatus?->name ?? 'Non défini';
        });

        // Historique récent (10 dernières affectations)
        $recentHistory = DB::table('depot_assignment_history')
            ->join('vehicles', 'depot_assignment_history.vehicle_id', '=', 'vehicles.id')
            ->join('users', 'depot_assignment_history.assigned_by', '=', 'users.id')
            ->where('depot_assignment_history.depot_id', $depot->id)
            ->where('depot_assignment_history.organization_id', Auth::user()->organization_id)
            ->select(
                'depot_assignment_history.*',
                'vehicles.registration_plate',
                'users.name as assigned_by_name'
            )
            ->orderBy('depot_assignment_history.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.depots.show', compact(
            'depot',
            'stats',
            'vehicles',
            'vehiclesByStatus',
            'recentHistory'
        ));
    }

    /**
     * Exporter la fiche dépôt en PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id)
    {
        $depot = VehicleDepot::where('id', $id)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        // Mêmes données que show()
        $stats = $this->depotService->getDepotStats($depot);

        $vehicles = Vehicle::where('depot_id', $depot->id)
            ->where('organization_id', Auth::user()->organization_id)
            ->with(['vehicleType', 'fuelType', 'vehicleStatus'])
            ->orderBy('registration_plate')
            ->get();

        $vehiclesByStatus = $vehicles->groupBy(function ($vehicle) {
            return $vehicle->vehicleStatus?->name ?? 'Non défini';
        });

        $recentHistory = DB::table('depot_assignment_history')
            ->join('vehicles', 'depot_assignment_history.vehicle_id', '=', 'vehicles.id')
            ->join('users', 'depot_assignment_history.assigned_by', '=', 'users.id')
            ->where('depot_assignment_history.depot_id', $depot->id)
            ->where('depot_assignment_history.organization_id', Auth::user()->organization_id)
            ->select(
                'depot_assignment_history.*',
                'vehicles.registration_plate',
                'users.name as assigned_by_name'
            )
            ->orderBy('depot_assignment_history.created_at', 'desc')
            ->limit(10)
            ->get();

        // Configuration PDF
        $pdf = Pdf::loadView('admin.depots.pdf', compact(
            'depot',
            'stats',
            'vehicles',
            'vehiclesByStatus',
            'recentHistory'
        ));

        $pdf->setPaper('A4', 'portrait');

        $filename = 'depot_' . ($depot->code ?? $depot->id) . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Supprimer un dépôt (soft delete)
     *
     * Validations:
     * - Dépôt doit être vide (aucun véhicule assigné)
     * - Ou confirmation forcée si véhicules présents
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $depot = VehicleDepot::where('id', $id)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        // Vérifier si dépôt contient des véhicules
        $vehicleCount = Vehicle::where('depot_id', $depot->id)
            ->where('organization_id', Auth::user()->organization_id)
            ->count();

        if ($vehicleCount > 0 && !$request->boolean('force')) {
            return redirect()
                ->route('admin.depots.show', $depot->id)
                ->with('error', "Le dépôt contient {$vehicleCount} véhicule(s). Réaffectez-les avant de supprimer le dépôt.");
        }

        DB::beginTransaction();

        try {
            // Si force=true, désassigner tous les véhicules
            if ($vehicleCount > 0 && $request->boolean('force')) {
                Vehicle::where('depot_id', $depot->id)
                    ->update(['depot_id' => null]);

                Log::warning('Depot deleted with force - vehicles unassigned', [
                    'depot_id' => $depot->id,
                    'depot_name' => $depot->name,
                    'vehicle_count' => $vehicleCount,
                    'user_id' => Auth::id()
                ]);
            }

            // Soft delete
            $depot->delete();

            DB::commit();

            Log::info('Depot deleted successfully', [
                'depot_id' => $depot->id,
                'depot_name' => $depot->name,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('admin.depots.index')
                ->with('success', "Dépôt \"{$depot->name}\" supprimé avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete depot', [
                'depot_id' => $depot->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.depots.show', $depot->id)
                ->with('error', 'Erreur lors de la suppression du dépôt: ' . $e->getMessage());
        }
    }

    /**
     * Restaurer un dépôt soft-deleted
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $depot = VehicleDepot::withTrashed()
            ->where('id', $id)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        if (!$depot->trashed()) {
            return redirect()
                ->route('admin.depots.index')
                ->with('warning', 'Le dépôt n\'est pas supprimé.');
        }

        $depot->restore();

        Log::info('Depot restored', [
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
            'user_id' => Auth::id()
        ]);

        return redirect()
            ->route('admin.depots.show', $depot->id)
            ->with('success', "Dépôt \"{$depot->name}\" restauré avec succès.");
    }
}
