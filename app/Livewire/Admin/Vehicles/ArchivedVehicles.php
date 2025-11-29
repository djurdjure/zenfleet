<?php

namespace App\Livewire\Admin\Vehicles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * 🗂️ COMPOSANT LIVEWIRE ULTRA-PRO - VÉHICULES ARCHIVÉS
 *
 * Gestion enterprise-grade des véhicules archivés avec:
 * ✨ Réactivité temps réel sans rafraîchissement page
 * ✨ Actions CRUD instantanées (restaurer, supprimer)
 * ✨ Invalidation automatique du cache
 * ✨ Notifications toast enterprise
 * ✨ Audit trail complet
 * ✨ Pagination optimisée
 * ✨ Gestion d'erreurs robuste
 *
 * @version 1.0-Enterprise-Ultra
 * @since 2025-11-27
 * @package App\Livewire\Admin\Vehicles
 */
class ArchivedVehicles extends Component
{
    use WithPagination;

    // ============================================================
    // PROPRIÉTÉS PUBLIQUES
    // ============================================================

    /**
     * Nombre d'éléments par page
     */
    public int $perPage = 20;

    /**
     * ID du véhicule en cours de restauration/suppression
     */
    public ?int $processingVehicleId = null;

    // ============================================================
    // LISTENERS LIVEWIRE
    // ============================================================

    /**
     * Événements écoutés par le composant
     */
    protected $listeners = [
        'refreshVehicles' => '$refresh'
    ];

    // ============================================================
    // MÉTHODES D'ACTION ENTERPRISE
    // ============================================================

    /**
     * 🔄 Restaurer un véhicule archivé
     *
     * @param int $vehicleId ID du véhicule à restaurer
     * @return void
     */
    public function restoreVehicle(int $vehicleId): void
    {
        try {
            // Marquer comme en cours de traitement
            $this->processingVehicleId = $vehicleId;

            // Récupérer le véhicule supprimé
            $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);

            // Log de l'action
            $this->logUserAction('vehicle.restore.attempted', [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
                'user_id' => Auth::id(),
            ]);

            // Restaurer le véhicule
            $vehicle->restore();

            // Invalider le cache
            Cache::tags(['vehicles', 'analytics'])->flush();

            // Log de succès
            $this->logUserAction('vehicle.restore.success', [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
            ]);

            // Notification succès
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Véhicule restauré',
                'message' => "Le véhicule {$vehicle->registration_plate} a été restauré avec succès.",
            ]);

            // Réinitialiser le statut de traitement
            $this->processingVehicleId = null;

            // 🟢 CORRECTION CRITIQUE : Forcer le re-rendu du composant Livewire
            $this->dispatch('$refresh');

            // Émettre un événement global pour rafraîchir d'autres composants
            $this->dispatch('vehicleRestored', vehicleId: $vehicleId);

        } catch (\Exception $e) {
            $this->processingVehicleId = null;

            // Log de l'erreur
            $this->logError('vehicle.restore.error', $e, [
                'vehicle_id' => $vehicleId,
            ]);

            // Notification d'erreur
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Erreur de restauration',
                'message' => 'Une erreur est survenue lors de la restauration du véhicule.',
            ]);
        }
    }

    /**
     * 🗑️ Supprimer définitivement un véhicule
     *
     * @param int $vehicleId ID du véhicule à supprimer définitivement
     * @return void
     */
    public function forceDeleteVehicle(int $vehicleId): void
    {
        try {
            // Marquer comme en cours de traitement
            $this->processingVehicleId = $vehicleId;

            // Récupérer le véhicule supprimé
            $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);

            // Sauvegarder les infos avant suppression
            $registrationPlate = $vehicle->registration_plate;

            // Log de l'action
            $this->logUserAction('vehicle.force_delete.attempted', [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $registrationPlate,
                'user_id' => Auth::id(),
            ]);

            // Suppression définitive - IRRÉVERSIBLE
            $vehicle->forceDelete();

            // Invalider le cache
            Cache::tags(['vehicles', 'analytics'])->flush();

            // Log de succès
            $this->logUserAction('vehicle.force_delete.success', [
                'vehicle_id' => $vehicleId,
                'registration_plate' => $registrationPlate,
            ]);

            // Notification succès
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Véhicule supprimé',
                'message' => "Le véhicule {$registrationPlate} a été supprimé définitivement.",
            ]);

            // Réinitialiser le statut de traitement
            $this->processingVehicleId = null;

            // 🟢 CORRECTION CRITIQUE : Forcer le re-rendu du composant Livewire
            $this->dispatch('$refresh');

            // Émettre un événement global
            $this->dispatch('vehicleForceDeleted', vehicleId: $vehicleId);

        } catch (\Exception $e) {
            $this->processingVehicleId = null;

            // Log de l'erreur
            $this->logError('vehicle.force_delete.error', $e, [
                'vehicle_id' => $vehicleId,
            ]);

            // Notification d'erreur
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Erreur de suppression',
                'message' => 'Une erreur est survenue lors de la suppression définitive du véhicule.',
            ]);
        }
    }

    // ============================================================
    // MÉTHODES UTILITAIRES
    // ============================================================

    /**
     * 📊 Calculer les statistiques des véhicules archivés
     *
     * @return array
     */
    private function getArchiveStats(): array
    {
        return Cache::tags(['vehicles', 'stats'])->remember('archived_vehicles_stats', 300, function () {
            return [
                'total_archived' => Vehicle::onlyTrashed()->count(),
                'archived_this_month' => Vehicle::onlyTrashed()
                    ->whereMonth('deleted_at', now()->month)
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
                'archived_this_year' => Vehicle::onlyTrashed()
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
            ];
        });
    }

    /**
     * 📝 Logger une action utilisateur
     *
     * @param string $action
     * @param array $context
     * @return void
     */
    private function logUserAction(string $action, array $context = []): void
    {
        Log::info($action, array_merge($context, [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? null,
            'timestamp' => now()->toIso8601String(),
        ]));
    }

    /**
     * ⚠️ Logger une erreur
     *
     * @param string $context
     * @param \Exception $exception
     * @param array $additionalData
     * @return void
     */
    private function logError(string $context, \Exception $exception, array $additionalData = []): void
    {
        Log::error($context, [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => Auth::id(),
            'additional_data' => $additionalData,
        ]);
    }

    // ============================================================
    // RENDU COMPOSANT
    // ============================================================

    /**
     * 🎨 Rendu du composant Livewire
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Récupération des véhicules archivés avec relations
        $vehicles = Vehicle::onlyTrashed()
            ->with(['vehicleType', 'fuelType', 'transmissionType', 'vehicleStatus'])
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage);

        // Statistiques
        $stats = $this->getArchiveStats();

        return view('livewire.admin.vehicles.archived-vehicles', [
            'vehicles' => $vehicles,
            'stats' => $stats,
        ]);
    }
}
