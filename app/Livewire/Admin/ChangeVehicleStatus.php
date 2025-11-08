<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

/**
 * 🚗 CHANGE VEHICLE STATUS - Livewire Component Enterprise-Grade
 *
 * Composant Livewire pour changer le statut d'un véhicule avec validation,
 * historisation et feedback utilisateur en temps réel.
 *
 * Fonctionnalités:
 * - Affichage des statuts disponibles selon les transitions autorisées
 * - Validation en temps réel
 * - Champs conditionnels (raison, métadonnées)
 * - Feedback utilisateur (succès/erreur)
 * - Historique des changements
 *
 * @version 2.0-Enterprise
 */
class ChangeVehicleStatus extends Component
{
    // =========================================================================
    // PROPERTIES
    // =========================================================================

    public Vehicle $vehicle;

    public ?string $selectedStatus = null;
    public string $reason = '';
    public array $metadata = [];

    public bool $showReasonField = false;
    public bool $showMetadataFields = false;

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    protected StatusTransitionService $statusService;

    // =========================================================================
    // VALIDATION RULES
    // =========================================================================

    protected function rules(): array
    {
        return [
            'selectedStatus' => 'required|string',
            'reason' => $this->showReasonField ? 'required|string|max:1000' : 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ];
    }

    protected $messages = [
        'selectedStatus.required' => 'Veuillez sélectionner un statut.',
        'reason.required' => 'La raison est obligatoire pour ce statut.',
        'reason.max' => 'La raison ne peut pas dépasser 1000 caractères.',
    ];

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    public function boot(StatusTransitionService $statusService): void
    {
        $this->statusService = $statusService;
    }

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle->load('vehicleStatus');
    }

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    /**
     * Retourne les statuts disponibles pour la transition
     */
    public function getAvailableStatusesProperty(): array
    {
        $currentStatus = $this->getCurrentVehicleStatus();

        if (!$currentStatus) {
            // Si pas de statut actuel, autoriser tous sauf REFORMÉ
            return collect(VehicleStatusEnum::cases())
                ->filter(fn($status) => $status !== VehicleStatusEnum::REFORME)
                ->mapWithKeys(fn($status) => [$status->value => $status->label()])
                ->toArray();
        }

        // Sinon, retourner les transitions autorisées
        return collect($currentStatus->allowedTransitions())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }

    /**
     * Récupère le statut actuel du véhicule
     */
    protected function getCurrentVehicleStatus(): ?VehicleStatusEnum
    {
        if ($this->vehicle->vehicleStatus) {
            $slug = \Str::slug($this->vehicle->vehicleStatus->name);
            return VehicleStatusEnum::tryFrom($slug);
        }
        return null;
    }

    // =========================================================================
    // METHODS - USER INTERACTIONS
    // =========================================================================

    /**
     * Appelé quand l'utilisateur change le statut sélectionné
     */
    public function updatedSelectedStatus($value): void
    {
        $status = VehicleStatusEnum::tryFrom($value);

        if (!$status) {
            return;
        }

        // Afficher le champ raison pour certains statuts
        $this->showReasonField = in_array($status, [
            VehicleStatusEnum::EN_PANNE,
            VehicleStatusEnum::EN_MAINTENANCE,
            VehicleStatusEnum::REFORME,
        ]);

        // Afficher les champs de métadonnées pour REFORMÉ
        $this->showMetadataFields = $status === VehicleStatusEnum::REFORME;

        // Reset messages
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    /**
     * Soumet le changement de statut
     */
    public function changeStatus(): void
    {
        $this->validate();

        try {
            $newStatus = VehicleStatusEnum::from($this->selectedStatus);

            $this->statusService->changeVehicleStatus(
                $this->vehicle,
                $newStatus,
                [
                    'reason' => $this->reason ?: null,
                    'metadata' => $this->metadata ?: null,
                    'change_type' => 'manual',
                ]
            );

            $this->successMessage = "Le statut du véhicule a été changé avec succès vers '{$newStatus->label()}'.";
            $this->errorMessage = null;

            // Rafraîchir le véhicule
            $this->vehicle->refresh()->load('vehicleStatus');

            // Reset le formulaire
            $this->reset(['selectedStatus', 'reason', 'metadata', 'showReasonField', 'showMetadataFields']);

            // Émettre un événement pour rafraîchir d'autres composants
            $this->dispatch('vehicle-status-changed', vehicleId: $this->vehicle->id);

        } catch (\InvalidArgumentException $e) {
            $this->errorMessage = $e->getMessage();
            $this->successMessage = null;
        } catch (\Exception $e) {
            Log::error('Error changing vehicle status', [
                'vehicle_id' => $this->vehicle->id,
                'new_status' => $this->selectedStatus,
                'error' => $e->getMessage(),
            ]);

            $this->errorMessage = "Une erreur est survenue lors du changement de statut. Veuillez réessayer.";
            $this->successMessage = null;
        }
    }

    /**
     * Annule et ferme le formulaire
     */
    public function cancel(): void
    {
        $this->reset(['selectedStatus', 'reason', 'metadata', 'showReasonField', 'showMetadataFields', 'successMessage', 'errorMessage']);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.admin.change-vehicle-status', [
            'availableStatuses' => $this->availableStatuses,
            'currentStatus' => $this->getCurrentVehicleStatus(),
        ]);
    }
}
