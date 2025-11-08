<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 VEHICLE STATUS BADGE - Composant Livewire Inline
 *
 * Badge interactif de changement de statut directement dans la table.
 * Click sur le badge → Dropdown des statuts autorisés → Changement instantané
 *
 * Features:
 * - Affichage badge actuel avec couleurs
 * - Dropdown statuts autorisés (validation State Machine)
 * - Changement instantané sans rechargement page
 * - Toast notification de succès/erreur
 * - Support permissions
 *
 * @version 1.0-Enterprise
 */
class VehicleStatusBadge extends Component
{
    public Vehicle $vehicle;
    public bool $showDropdown = false;
    public string $currentStatus;

    /**
     * Initialisation du composant
     */
    public function mount(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
        $this->currentStatus = $this->getCurrentStatusSlug();
    }

    /**
     * Récupère le slug du statut actuel
     */
    protected function getCurrentStatusSlug(): string
    {
        $statusRelation = $this->vehicle->vehicleStatus;
        if ($statusRelation) {
            return \Str::slug($statusRelation->name);
        }
        return 'inconnu';
    }

    /**
     * Toggle dropdown
     */
    public function toggleDropdown()
    {
        if (!auth()->user()->can('update-vehicle-status')) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Vous n\'avez pas la permission de changer les statuts.'
            ]);
            return;
        }

        $this->showDropdown = !$this->showDropdown;
    }

    /**
     * Change le statut du véhicule
     */
    public function changeStatus(string $newStatusSlug)
    {
        try {
            // Vérification permission
            if (!auth()->user()->can('update-vehicle-status')) {
                throw new \Exception('Permission refusée');
            }

            // Convertir slug en Enum
            $newStatus = VehicleStatusEnum::tryFrom($newStatusSlug);
            if (!$newStatus) {
                throw new \Exception('Statut invalide');
            }

            // Appeler le service de transition
            $service = app(StatusTransitionService::class);
            $service->changeVehicleStatus(
                $this->vehicle,
                $newStatus,
                [
                    'reason' => 'Changement manuel depuis l\'interface',
                    'change_type' => 'manual',
                ]
            );

            // Rafraîchir le véhicule
            $this->vehicle->refresh();
            $this->currentStatus = $newStatusSlug;
            $this->showDropdown = false;

            // Notifier succès
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Statut changé vers '{$newStatus->label()}' avec succès."
            ]);

            // Émettre événement pour rafraîchir la page parente si besoin
            $this->dispatch('vehicleStatusChanged', [
                'vehicleId' => $this->vehicle->id,
                'newStatus' => $newStatusSlug
            ]);

            Log::info('Vehicle status changed via Livewire', [
                'vehicle_id' => $this->vehicle->id,
                'new_status' => $newStatusSlug,
                'user_id' => auth()->id(),
            ]);

        } catch (\InvalidArgumentException $e) {
            // Erreur de validation de transition
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // Autre erreur
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur: ' . $e->getMessage()
            ]);

            Log::error('Error changing vehicle status via Livewire', [
                'vehicle_id' => $this->vehicle->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Retourne les statuts autorisés pour la transition
     */
    public function getAllowedStatuses(): array
    {
        try {
            $currentEnum = VehicleStatusEnum::tryFrom($this->currentStatus);
            if (!$currentEnum) {
                return [];
            }

            return $currentEnum->allowedTransitions();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Render du composant
     */
    public function render()
    {
        $currentEnum = VehicleStatusEnum::tryFrom($this->currentStatus);
        $allowedStatuses = $this->getAllowedStatuses();

        return view('livewire.admin.vehicle-status-badge', [
            'currentEnum' => $currentEnum,
            'allowedStatuses' => $allowedStatuses,
            'canUpdate' => auth()->user()->can('update-vehicle-status'),
        ]);
    }
}
