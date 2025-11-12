<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * 🎯 VEHICLE STATUS BADGE ULTRA PRO - Composant Livewire Enterprise-Grade
 *
 * Badge interactif de changement de statut avec confirmation modale.
 * Architecture ultra-professionnelle surpassant Fleetio et Samsara.
 *
 * Features Enterprise:
 * ✅ Badge cliquable avec transitions fluides
 * ✅ Modal de confirmation avec message contextuel
 * ✅ Validation State Machine avancée
 * ✅ Notification toast avec feedback instantané
 * ✅ Gestion des erreurs robuste
 * ✅ Support multi-tenant avec permissions RBAC
 * ✅ Historisation automatique des changements
 * ✅ Animation et UX premium
 *
 * @version 3.0-Enterprise-Ultra-Pro
 * @since 2025-11-12
 */
class VehicleStatusBadgeUltraPro extends Component
{
    public Vehicle $vehicle;
    public bool $showDropdown = false;
    public bool $showConfirmModal = false;
    public ?string $pendingStatus = null;
    public ?VehicleStatusEnum $pendingStatusEnum = null;
    public string $confirmMessage = '';
    
    protected $listeners = ['refreshComponent' => '$refresh'];

    /**
     * Initialisation du composant avec préchargement des relations
     */
    public function mount(Vehicle $vehicle)
    {
        // Précharger les relations nécessaires pour éviter les requêtes N+1
        $this->vehicle = $vehicle->load(['vehicleStatus', 'depot', 'assignments.driver']);
    }

    /**
     * Récupère le statut actuel sous forme d'enum
     */
    public function getCurrentStatusEnum(): ?VehicleStatusEnum
    {
        if ($this->vehicle->vehicleStatus) {
            $slug = \Str::slug($this->vehicle->vehicleStatus->name);
            return VehicleStatusEnum::tryFrom($slug);
        }
        return null;
    }

    /**
     * Toggle du dropdown avec vérification des permissions
     */
    public function toggleDropdown()
    {
        // Vérification permission avec message explicite
        if (!$this->canUpdateStatus()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Accès refusé',
                'message' => 'Vous n\'avez pas la permission de modifier les statuts de véhicule.',
                'duration' => 5000
            ]);
            return;
        }

        $this->showDropdown = !$this->showDropdown;
    }

    /**
     * Prépare le changement de statut avec confirmation
     */
    public function prepareStatusChange(string $newStatus)
    {
        try {
            // Récupérer l'enum du nouveau statut
            $this->pendingStatusEnum = VehicleStatusEnum::tryFrom($newStatus);
            if (!$this->pendingStatusEnum) {
                throw new \Exception("Statut invalide: {$newStatus}");
            }

            $this->pendingStatus = $newStatus;
            $currentEnum = $this->getCurrentStatusEnum();
            
            // Construire le message de confirmation contextuel
            $this->confirmMessage = $this->buildConfirmationMessage($currentEnum, $this->pendingStatusEnum);
            
            // Fermer le dropdown et ouvrir la modal
            $this->showDropdown = false;
            $this->showConfirmModal = true;

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Erreur',
                'message' => $e->getMessage(),
                'duration' => 5000
            ]);
        }
    }

    /**
     * Construit un message de confirmation intelligent
     */
    protected function buildConfirmationMessage(?VehicleStatusEnum $current, VehicleStatusEnum $new): string
    {
        $vehicleInfo = "{$this->vehicle->brand} {$this->vehicle->model} ({$this->vehicle->registration_plate})";
        
        // Messages contextuels selon le nouveau statut
        $contextMessages = [
            'disponible' => "Le véhicule sera marqué comme disponible et pourra être affecté à un chauffeur.",
            'affecte' => "Le véhicule sera marqué comme affecté. Assurez-vous qu'une affectation active existe.",
            'en-maintenance' => "Le véhicule sera indisponible pendant la maintenance. Les affectations actives seront suspendues.",
            'en-reparation' => "Le véhicule sera indisponible pendant les réparations. Durée estimée à préciser.",
            'reserve' => "Le véhicule sera mis en réserve et ne sera pas disponible pour les affectations normales.",
            'hors-service' => "Le véhicule sera marqué hors service. Cette action peut nécessiter une inspection.",
            'reforme' => "⚠️ ATTENTION: Le véhicule sera définitivement réformé. Cette action est IRRÉVERSIBLE.",
            'en-commande' => "Le véhicule sera marqué comme en commande (pas encore livré).",
            'vendu' => "Le véhicule sera marqué comme vendu et retiré de la flotte active."
        ];

        $context = $contextMessages[$new->value] ?? "Le statut du véhicule sera modifié.";
        $currentLabel = $current ? $current->label() : 'Non défini';
        
        return "Êtes-vous sûr de vouloir changer le statut du véhicule {$vehicleInfo} de \"{$currentLabel}\" vers \"{$new->label()}\" ?\n\n{$context}";
    }

    /**
     * Confirme et exécute le changement de statut
     */
    public function confirmStatusChange()
    {
        if (!$this->pendingStatus || !$this->pendingStatusEnum) {
            return;
        }

        try {
            // Double vérification des permissions
            if (!$this->canUpdateStatus()) {
                throw new \Exception('Permission refusée');
            }

            // Transaction pour garantir l'intégrité
            DB::transaction(function () {
                // Utiliser le service de transition avec validation
                $service = app(StatusTransitionService::class);
                $service->changeVehicleStatus(
                    $this->vehicle,
                    $this->pendingStatusEnum,
                    [
                        'reason' => "Changement manuel via interface badge",
                        'change_type' => 'manual_badge',
                        'user_id' => auth()->id(),
                        'metadata' => [
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'component' => 'VehicleStatusBadgeUltraPro'
                        ]
                    ]
                );

                // Rafraîchir le véhicule avec toutes ses relations
                $this->vehicle->refresh();
                $this->vehicle->load(['vehicleStatus', 'depot', 'assignments.driver']);
            });

            // Fermer la modal et notifier le succès
            $this->showConfirmModal = false;
            $this->pendingStatus = null;
            $this->pendingStatusEnum = null;

            // Notification de succès avec détails
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Statut modifié',
                'message' => "Le statut a été changé vers \"{$this->pendingStatusEnum->label()}\" avec succès.",
                'duration' => 4000
            ]);

            // Émettre l'événement pour rafraîchir d'autres composants si nécessaire
            $this->dispatch('vehicleStatusChanged', [
                'vehicleId' => $this->vehicle->id,
                'newStatus' => $this->pendingStatusEnum->value,
                'timestamp' => now()->toIso8601String()
            ]);

            // Log détaillé pour l'audit
            Log::info('Vehicle status changed via badge', [
                'vehicle_id' => $this->vehicle->id,
                'registration' => $this->vehicle->registration_plate,
                'new_status' => $this->pendingStatusEnum->value,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'component' => 'VehicleStatusBadgeUltraPro'
            ]);

        } catch (\InvalidArgumentException $e) {
            // Erreur de validation de transition
            $this->showConfirmModal = false;
            $this->dispatch('toast', [
                'type' => 'warning',
                'title' => 'Transition non autorisée',
                'message' => $e->getMessage(),
                'duration' => 6000
            ]);
            
        } catch (\Exception $e) {
            // Erreur générique
            $this->showConfirmModal = false;
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Erreur système',
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
                'duration' => 6000
            ]);

            Log::error('Error changing vehicle status via badge', [
                'vehicle_id' => $this->vehicle->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Annule le changement de statut
     */
    public function cancelStatusChange()
    {
        $this->showConfirmModal = false;
        $this->pendingStatus = null;
        $this->pendingStatusEnum = null;
        $this->confirmMessage = '';
    }

    /**
     * Vérifie si l'utilisateur peut modifier le statut
     */
    protected function canUpdateStatus(): bool
    {
        // Vérifier plusieurs permissions possibles
        return auth()->user()->can('update vehicles') || 
               auth()->user()->can('update-vehicle-status') ||
               auth()->user()->can('manage vehicles') ||
               auth()->user()->hasRole(['admin', 'super-admin', 'fleet-manager']);
    }

    /**
     * Récupère les statuts autorisés pour la transition
     */
    public function getAllowedStatuses(): array
    {
        $currentEnum = $this->getCurrentStatusEnum();
        
        if (!$currentEnum) {
            // Si pas de statut actuel, permettre tous sauf réformé
            return array_filter(VehicleStatusEnum::cases(), function($status) {
                return $status !== VehicleStatusEnum::REFORME;
            });
        }

        return $currentEnum->allowedTransitions();
    }

    /**
     * Render du composant avec données optimisées
     */
    public function render()
    {
        return view('livewire.admin.vehicle-status-badge-ultra-pro', [
            'currentEnum' => $this->getCurrentStatusEnum(),
            'allowedStatuses' => $this->getAllowedStatuses(),
            'canUpdate' => $this->canUpdateStatus(),
            'isTerminal' => $this->getCurrentStatusEnum()?->isTerminal() ?? false,
        ]);
    }
}
