<?php

namespace App\Livewire\Admin;

use App\Models\Vehicle;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;
use Livewire\Component;
use Livewire\Attributes\On;
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
    // ✅ FIX: Utiliser l'ID au lieu de l'objet complet pour la réactivité
    public int $vehicleId;
    public Vehicle $vehicle;
    public bool $showConfirmModal = false;
    public ?string $pendingStatus = null;
    public ?VehicleStatusEnum $pendingStatusEnum = null;
    public string $confirmMessage = '';

    // ✅ Listeners pour synchronisation multi-composants
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'vehicleStatusUpdated' => 'refreshVehicleData',
        'vehicleStatusUpdated' => 'refreshVehicleData',
        'vehicleStatusChanged' => 'handleStatusChanged',
        // 'echo:vehicles,VehicleStatusChanged' => 'onVehicleStatusChanged' // Real-time via WebSocket (Disabled: Echo not installed)
    ];

    /**
     * Initialisation du composant avec préchargement des relations
     * ✅ FIX: Stocker l'ID et charger le véhicule dynamiquement
     */
    public function mount($vehicle)
    {
        // Accepter soit un ID soit un objet Vehicle
        if ($vehicle instanceof Vehicle) {
            $this->vehicleId = $vehicle->id;
            $this->vehicle = $vehicle->load(['vehicleStatus', 'depot', 'assignments.driver']);
        } else {
            $this->vehicleId = (int) $vehicle;
            $this->loadVehicle();
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Charge le véhicule depuis la DB avec toutes ses relations
     */
    protected function loadVehicle(): void
    {
        $this->vehicle = Vehicle::with(['vehicleStatus', 'depot', 'assignments.driver'])
            ->findOrFail($this->vehicleId);
    }

    /**
     * Rafraîchit les données du véhicule
     * ✅ FIX: Utilise maintenant loadVehicle() pour éviter les doublons de code
     */
    public function refreshVehicleData($vehicleId = null)
    {
        // Vérifier si c'est bien notre véhicule qui a été modifié
        if ($vehicleId && $vehicleId != $this->vehicleId) {
            return;
        }

        // Rafraîchir le modèle depuis la base de données
        $this->loadVehicle();

        Log::info('Vehicle data refreshed in badge', [
            'vehicle_id' => $this->vehicleId,
            'new_status' => $this->vehicle->vehicleStatus?->name,
            'component' => 'VehicleStatusBadgeUltraPro'
        ]);
    }

    /**
     * Gère l'événement de changement de statut
     * ✅ FIX: Utilise maintenant vehicleId au lieu de vehicle->id
     */
    public function handleStatusChanged($payload)
    {
        // Vérifier si c'est notre véhicule qui a changé
        if (isset($payload['vehicleId']) && $payload['vehicleId'] == $this->vehicleId) {
            $this->refreshVehicleData($payload['vehicleId']);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Gère les changements de statut via WebSocket (temps réel)
     */
    public function onVehicleStatusChanged($event)
    {
        // Vérifier si c'est notre véhicule qui a changé
        if (isset($event['vehicleId']) && $event['vehicleId'] == $this->vehicleId) {
            $this->refreshVehicleData($event['vehicleId']);
        }
    }

    /**
     * Récupère le statut actuel sous forme d'enum
     * ✅ FIX ENTERPRISE: Utilise directement le slug de la table au lieu de le générer
     */
    public function getCurrentStatusEnum(): ?VehicleStatusEnum
    {
        if ($this->vehicle->vehicleStatus) {
            // ✅ CORRECTION: Utiliser le slug de la table qui contient déjà le bon format
            // Avant: \Str::slug($this->vehicle->vehicleStatus->name) générait 'en-panne' (tiret)
            // Maintenant: Utilise directement $this->vehicle->vehicleStatus->slug qui vaut 'en_panne' (underscore)
            $slug = $this->vehicle->vehicleStatus->slug;

            // Tentative directe avec le slug de la table
            $enum = VehicleStatusEnum::tryFrom($slug);

            // ⚠️ FALLBACK: Si le slug de la table ne matche pas exactement, essayer avec les underscores
            // Ceci gère les cas où le slug DB utilise des tirets mais l'enum utilise des underscores
            if (!$enum && str_contains($slug, '-')) {
                $slugWithUnderscore = str_replace('-', '_', $slug);
                $enum = VehicleStatusEnum::tryFrom($slugWithUnderscore);
            }

            // ⚠️ FALLBACK 2: Si toujours pas de match, essayer de générer depuis le name
            if (!$enum) {
                $generatedSlug = str_replace('-', '_', \Str::slug($this->vehicle->vehicleStatus->name));
                $enum = VehicleStatusEnum::tryFrom($generatedSlug);
            }

            // ⚠️ FALLBACK 3: Mapping explicite pour les termes français/anglais courants
            if (!$enum) {
                $map = [
                    'disponible' => VehicleStatusEnum::PARKING,
                    'available' => VehicleStatusEnum::PARKING,
                    'parking' => VehicleStatusEnum::PARKING,
                    'active' => VehicleStatusEnum::AFFECTE,
                    'actif' => VehicleStatusEnum::AFFECTE,
                    'assigned' => VehicleStatusEnum::AFFECTE,
                    'broken' => VehicleStatusEnum::EN_PANNE,
                    'panne' => VehicleStatusEnum::EN_PANNE,
                    'maintenance' => VehicleStatusEnum::EN_MAINTENANCE,
                    'repair' => VehicleStatusEnum::EN_MAINTENANCE,
                    'sold' => VehicleStatusEnum::VENDU,
                    'vendu' => VehicleStatusEnum::VENDU,
                    'retired' => VehicleStatusEnum::REFORME,
                    'reforme' => VehicleStatusEnum::REFORME,
                ];

                $normalizedSlug = str_replace(['-', '_'], '', strtolower($slug));
                foreach ($map as $key => $targetEnum) {
                    if ($key === $slug || $key === $normalizedSlug || str_contains($slug, $key)) {
                        $enum = $targetEnum;
                        break;
                    }
                }
            }

            // 📊 LOGGING: Si aucun enum trouvé, logger pour debugging
            if (!$enum) {
                Log::warning('VehicleStatusEnum not found for vehicle status', [
                    'vehicle_id' => $this->vehicleId,
                    'vehicle_status_id' => $this->vehicle->vehicleStatus->id,
                    'vehicle_status_name' => $this->vehicle->vehicleStatus->name,
                    'vehicle_status_slug' => $slug,
                    'component' => 'VehicleStatusBadgeUltraPro'
                ]);
            }

            return $enum;
        }
        return null;
    }



    /**
     * Prépare le changement de statut avec confirmation
     */
    public function prepareStatusChange(string $newStatus)
    {
        try {
            if (!$this->canUpdateStatus()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Permission refusée',
                    'message' => 'Vous ne pouvez pas modifier le statut de ce véhicule.',
                    'duration' => 5000
                ]);
                return;
            }

            // Récupérer l'enum du nouveau statut
            $this->pendingStatusEnum = VehicleStatusEnum::tryFrom($newStatus);
            if (!$this->pendingStatusEnum) {
                throw new \Exception("Statut invalide: {$newStatus}");
            }

            $this->pendingStatus = $newStatus;
            $currentEnum = $this->getCurrentStatusEnum();

            // Construire le message de confirmation contextuel
            $this->confirmMessage = $this->buildConfirmationMessage($currentEnum, $this->pendingStatusEnum);

            // Ouvrir la modal
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
                        'reason' => "Changement manuel via badge de statut",
                        'change_type' => 'manual', // ✅ FIX: Utiliser 'manual' au lieu de 'manual_badge'
                        'user_id' => auth()->id(),
                        'metadata' => [
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'component' => 'VehicleStatusBadgeUltraPro',
                            'source' => 'badge'
                        ]
                    ]
                );

                // Rafraîchir le véhicule avec toutes ses relations
                $this->vehicle->refresh();
                $this->vehicle->load(['vehicleStatus', 'depot', 'assignments.driver']);
            });

            // Sauvegarder le label du nouveau statut avant de réinitialiser
            $newStatusLabel = $this->pendingStatusEnum->label();
            $newStatusValue = $this->pendingStatusEnum->value;

            // Fermer la modal
            $this->showConfirmModal = false;

            // Notification de succès avec détails
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Statut modifié avec succès',
                'message' => "Le statut du véhicule a été changé vers \"{$newStatusLabel}\".",
                'duration' => 4000
            ]);

            // Rafraîchir immédiatement les données du véhicule
            $this->refreshVehicleData();

            // Émettre l'événement pour que tous les badges de ce véhicule se rafraîchissent
            $this->dispatch('vehicleStatusChanged', [
                'vehicleId' => $this->vehicle->id,
                'newStatus' => $newStatusValue,
                'timestamp' => now()->toIso8601String()
            ]);

            // Réinitialiser les variables temporaires APRÈS avoir envoyé les notifications
            $this->pendingStatus = null;
            $this->pendingStatusEnum = null;

            // Log détaillé pour l'audit
            Log::info('Vehicle status changed via badge', [
                'vehicle_id' => $this->vehicle->id,
                'registration' => $this->vehicle->registration_plate,
                'new_status' => $newStatusValue,
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
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Vérifier plusieurs permissions possibles
        return $user->can('vehicles.status.update') ||
            $user->can('vehicles.update') ||
            $user->can('vehicles.manage');
    }

    /**
     * Récupère les statuts autorisés pour la transition
     */
    public function getAllowedStatuses(): array
    {
        $currentEnum = $this->getCurrentStatusEnum();

        if (!$currentEnum) {
            // Si pas de statut actuel, permettre tous sauf réformé
            return array_filter(VehicleStatusEnum::cases(), function ($status) {
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
