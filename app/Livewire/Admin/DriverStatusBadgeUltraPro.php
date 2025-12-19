<?php

namespace App\Livewire\Admin;

use App\Models\Driver;
use App\Enums\DriverStatusEnum;
use App\Services\StatusTransitionService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * 🎯 DRIVER STATUS BADGE ULTRA PRO - Composant Livewire Enterprise-Grade
 *
 * Badge interactif de changement de statut avec confirmation modale.
 * Adapté pour les chauffeurs (basé sur VehicleStatusBadgeUltraPro).
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
 * @version 1.0-Enterprise-Ultra-Pro
 * @since 2025-12-18
 */
class DriverStatusBadgeUltraPro extends Component
{
    // ✅ FIX: Utiliser l'ID au lieu de l'objet complet pour la réactivité
    public int $driverId;
    public Driver $driver;
    public bool $showConfirmModal = false;
    public ?string $pendingStatus = null;
    public ?DriverStatusEnum $pendingStatusEnum = null;
    public string $confirmMessage = '';

    // ✅ Listeners pour synchronisation multi-composants
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'driverStatusUpdated' => 'refreshDriverData',
        'driverStatusChanged' => 'handleStatusChanged',
        'echo:drivers,DriverStatusChanged' => 'onDriverStatusChanged' // Real-time via WebSocket
    ];

    /**
     * Initialisation du composant avec préchargement des relations
     * ✅ FIX: Stocker l'ID et charger le chauffeur dynamiquement
     */
    public function mount($driver)
    {
        // Accepter soit un ID soit un objet Driver
        if ($driver instanceof Driver) {
            $this->driverId = $driver->id;
            $this->driver = $driver->load(['driverStatus', 'activeAssignment.vehicle']);
        } else {
            $this->driverId = (int) $driver;
            $this->loadDriver();
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Charge le chauffeur depuis la DB avec toutes ses relations
     */
    protected function loadDriver(): void
    {
        $this->driver = Driver::with(['driverStatus', 'activeAssignment.vehicle'])
            ->findOrFail($this->driverId);
    }

    /**
     * Rafraîchit les données du chauffeur
     * ✅ FIX: Utilise maintenant loadDriver() pour éviter les doublons de code
     */
    public function refreshDriverData($driverId = null)
    {
        // Vérifier si c'est bien notre chauffeur qui a été modifié
        if ($driverId && $driverId != $this->driverId) {
            return;
        }

        // Rafraîchir le modèle depuis la base de données
        $this->loadDriver();

        Log::info('Driver data refreshed in badge', [
            'driver_id' => $this->driverId,
            'new_status' => $this->driver->driverStatus?->name,
            'component' => 'DriverStatusBadgeUltraPro'
        ]);
    }

    /**
     * Gère l'événement de changement de statut
     * ✅ FIX: Utilise maintenant driverId au lieu de driver->id
     */
    public function handleStatusChanged($payload)
    {
        // Vérifier si c'est notre chauffeur qui a changé
        if (isset($payload['driverId']) && $payload['driverId'] == $this->driverId) {
            $this->refreshDriverData($payload['driverId']);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE: Gère les changements de statut via WebSocket (temps réel)
     */
    public function onDriverStatusChanged($event)
    {
        // Vérifier si c'est notre chauffeur qui a changé
        if (isset($event['driverId']) && $event['driverId'] == $this->driverId) {
            $this->refreshDriverData($event['driverId']);
        }
    }

    /**
     * Récupère le statut actuel sous forme d'enum
     * ✅ FIX ENTERPRISE: Utilise directement le slug de la table au lieu de le générer
     */
    public function getCurrentStatusEnum(): ?DriverStatusEnum
    {
        if ($this->driver->driverStatus) {
            $slug = $this->driver->driverStatus->slug;

            // 1. Match exact/slug logic
            $enum = DriverStatusEnum::tryFrom($slug);

            // 2. Fallback: underscore/hyphen swap
            if (!$enum && str_contains($slug, '-')) {
                $enum = DriverStatusEnum::tryFrom(str_replace('-', '_', $slug));
            } elseif (!$enum && str_contains($slug, '_')) {
                $enum = DriverStatusEnum::tryFrom(str_replace('_', '-', $slug));
            }

            // 3. Fallback: Case insensitive match on value
            if (!$enum) {
                $lowerSlug = strtolower($slug);
                $enum = DriverStatusEnum::tryFrom($lowerSlug);

                if (!$enum) {
                    // Try matching against normalized case values
                    foreach (DriverStatusEnum::cases() as $case) {
                        if (
                            strtolower($case->value) === $lowerSlug ||
                            strtolower(str_replace('-', '_', $case->value)) === $lowerSlug
                        ) {
                            $enum = $case;
                            break;
                        }
                    }
                }
            }

            // 4. Fallback: Match by Name (Legacy/Seeder issues)
            if (!$enum) {
                $name = strtolower($this->driver->driverStatus->name);
                // Map common names to enums
                if (str_contains($name, 'dispo')) $enum = DriverStatusEnum::DISPONIBLE;
                elseif (str_contains($name, 'mission')) $enum = DriverStatusEnum::EN_MISSION;
                elseif (str_contains($name, 'cong')) $enum = DriverStatusEnum::EN_CONGE;
                elseif (str_contains($name, 'repos')) $enum = DriverStatusEnum::EN_CONGE; // Treat repos as conge/unavailable
                elseif (str_contains($name, 'formation')) $enum = DriverStatusEnum::EN_FORMATION;
            }

            // 5. Generate slug from name as last resort
            if (!$enum) {
                $generatedSlug = str_replace('-', '_', Str::slug($this->driver->driverStatus->name));
                $enum = DriverStatusEnum::tryFrom($generatedSlug);
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
            // Récupérer l'enum du nouveau statut
            $this->pendingStatusEnum = DriverStatusEnum::tryFrom($newStatus);
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
    protected function buildConfirmationMessage(?DriverStatusEnum $current, DriverStatusEnum $new): string
    {
        $driverInfo = "{$this->driver->first_name} {$this->driver->last_name} ({$this->driver->license_number})";

        // Messages contextuels selon le nouveau statut
        $contextMessages = [
            'disponible' => "Le chauffeur sera marqué comme disponible pour de nouvelles missions.",
            'en_mission' => "Le chauffeur sera marqué en mission. Assurez-vous qu'il est bien affecté à un véhicule.",
            'en_conge' => "Le chauffeur sera marqué en congé et ne pourra pas recevoir d'affectations.",
            'en_formation' => "Le chauffeur sera marqué en formation et ne pourra pas recevoir d'affectations.",
            'autre' => "Le chauffeur sera marqué comme indisponible pour une autre raison (maladie, sanction, etc.)."
        ];

        $context = $contextMessages[$new->value] ?? "Le statut du chauffeur sera modifié.";
        $currentLabel = $current ? $current->label() : 'Non défini';

        return "Êtes-vous sûr de vouloir changer le statut du chauffeur {$driverInfo} de \"{$currentLabel}\" vers \"{$new->label()}\" ?\n\n{$context}";
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
                $service->changeDriverStatus(
                    $this->driver,
                    $this->pendingStatusEnum,
                    [
                        'reason' => "Changement manuel via badge de statut",
                        'change_type' => 'manual',
                        'user_id' => auth()->id(),
                        'metadata' => [
                            'ip' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'component' => 'DriverStatusBadgeUltraPro',
                            'source' => 'badge'
                        ]
                    ]
                );

                // Rafraîchir le chauffeur avec toutes ses relations
                $this->driver->refresh();
                $this->driver->load(['driverStatus', 'activeAssignment.vehicle']);
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
                'message' => "Le statut du chauffeur a été changé vers \"{$newStatusLabel}\".",
                'duration' => 4000
            ]);

            // Rafraîchir immédiatement les données
            $this->refreshDriverData();

            // Émettre l'événement pour que tous les badges de ce chauffeur se rafraîchissent
            $this->dispatch('driverStatusChanged', [
                'driverId' => $this->driver->id,
                'newStatus' => $newStatusValue,
                'timestamp' => now()->toIso8601String()
            ]);

            // Réinitialiser les variables temporaires
            $this->pendingStatus = null;
            $this->pendingStatusEnum = null;

            // Log détaillé pour l'audit
            Log::info('Driver status changed via badge', [
                'driver_id' => $this->driver->id,
                'driver_name' => $this->driver->full_name,
                'new_status' => $newStatusValue,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Unknown',
                'component' => 'DriverStatusBadgeUltraPro'
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

            Log::error('Error changing driver status via badge', [
                'driver_id' => $this->driver->id,
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
        return auth()->user()->can('update drivers') ||
            auth()->user()->can('manage drivers') ||
            auth()->user()->hasRole(['Admin', 'Super Admin', 'Fleet Manager', 'admin', 'super-admin', 'fleet-manager']);
    }

    /**
     * Récupère les statuts autorisés pour la transition
     */
    public function getAllowedStatuses(): array
    {
        $currentEnum = $this->getCurrentStatusEnum();

        if (!$currentEnum) {
            // Si pas de statut actuel, permettre tous sauf EN_MISSION (qui nécessite flow affectation)
            // Enterprise: Si le statut est buggé/manquant, on permet de le forcer manuellement à n'importe quoi
            return array_filter(DriverStatusEnum::cases(), function ($status) {
                return $status !== DriverStatusEnum::EN_MISSION;
            });
        }

        return $currentEnum->allowedTransitions();
    }

    /**
     * Render du composant avec données optimisées
     */
    public function render()
    {
        return view('livewire.admin.driver-status-badge-ultra-pro', [
            'currentEnum' => $this->getCurrentStatusEnum(),
            'allowedStatuses' => $this->getAllowedStatuses(),
            'canUpdate' => $this->canUpdateStatus(),
            // Driver status doesn't have a "Terminal" state in the same way as vehicles (reformed/sold), 
            // but we can keep the logic if needed later or just set to false.
            'isTerminal' => false,
        ]);
    }
}
