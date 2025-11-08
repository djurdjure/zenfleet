<?php

namespace App\Enums;

/**
 * 🚗 VEHICLE STATUS ENUM - Enterprise-Grade Type-Safe Implementation
 *
 * Statuts des véhicules alignés avec les processus opérationnels réels.
 * Utilise des Enums PHP 8.2+ pour la robustesse et la sécurité de type.
 *
 * Architecture:
 * - Backed Enum (string) pour mapping vers DB
 * - Méthodes helper pour logique métier
 * - Integration avec State Machine Pattern
 *
 * @version 2.0-Enterprise
 * @see App\Services\VehicleStatusTransitionService Pour les règles de transition
 */
enum VehicleStatusEnum: string
{
    /**
     * Véhicule fonctionnel, disponible au parking, non affecté.
     * Peut être affecté à un chauffeur disponible.
     */
    case PARKING = 'parking';

    /**
     * Véhicule affecté à un chauffeur.
     * Ne peut être réaffecté qu'après désaffectation.
     */
    case AFFECTE = 'affecte';

    /**
     * Véhicule en panne, nécessite intervention.
     * Doit passer en maintenance pour réparation.
     */
    case EN_PANNE = 'en_panne';

    /**
     * Véhicule chez le réparateur (garage, mécanicien).
     * En cours de réparation ou diagnostic.
     */
    case EN_MAINTENANCE = 'en_maintenance';

    /**
     * Véhicule réformé, hors service définitif.
     * État terminal - aucune transition possible.
     */
    case REFORME = 'reforme';

    // =========================================================================
    // MÉTHODES HELPER - BUSINESS LOGIC
    // =========================================================================

    /**
     * Retourne le label français pour affichage UI
     */
    public function label(): string
    {
        return match($this) {
            self::PARKING => 'Parking',
            self::AFFECTE => 'Affecté',
            self::EN_PANNE => 'En panne',
            self::EN_MAINTENANCE => 'En maintenance',
            self::REFORME => 'Réformé',
        };
    }

    /**
     * Description détaillée du statut
     */
    public function description(): string
    {
        return match($this) {
            self::PARKING => 'Véhicule disponible au parking, prêt pour affectation',
            self::AFFECTE => 'Véhicule affecté à un chauffeur, en service',
            self::EN_PANNE => 'Véhicule en panne, nécessite intervention technique',
            self::EN_MAINTENANCE => 'Véhicule en cours de réparation chez le réparateur',
            self::REFORME => 'Véhicule réformé, hors service définitif',
        };
    }

    /**
     * Couleur Tailwind CSS pour badges
     */
    public function color(): string
    {
        return match($this) {
            self::PARKING => 'blue',
            self::AFFECTE => 'green',
            self::EN_PANNE => 'red',
            self::EN_MAINTENANCE => 'yellow',
            self::REFORME => 'gray',
        };
    }

    /**
     * Couleur hexadécimale pour graphiques/exports
     */
    public function hexColor(): string
    {
        return match($this) {
            self::PARKING => '#3b82f6',  // Bleu
            self::AFFECTE => '#10b981',  // Vert
            self::EN_PANNE => '#ef4444',  // Rouge
            self::EN_MAINTENANCE => '#f59e0b', // Orange
            self::REFORME => '#6b7280',  // Gris
        };
    }

    /**
     * Icône FontAwesome/Heroicons
     */
    public function icon(): string
    {
        return match($this) {
            self::PARKING => 'parking',
            self::AFFECTE => 'user-check',
            self::EN_PANNE => 'exclamation-triangle',
            self::EN_MAINTENANCE => 'wrench',
            self::REFORME => 'archive',
        };
    }

    /**
     * Classes CSS Tailwind complètes pour badge
     */
    public function badgeClasses(): string
    {
        $base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

        $colorClasses = match($this) {
            self::PARKING => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::AFFECTE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::EN_PANNE => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            self::EN_MAINTENANCE => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::REFORME => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };

        return "{$base} {$colorClasses}";
    }

    // =========================================================================
    // BUSINESS RULES - CAPABILITIES
    // =========================================================================

    /**
     * Le véhicule peut-il être affecté à un chauffeur ?
     */
    public function canBeAssigned(): bool
    {
        return $this === self::PARKING;
    }

    /**
     * Le véhicule est-il opérationnel ?
     */
    public function isOperational(): bool
    {
        return in_array($this, [self::PARKING, self::AFFECTE]);
    }

    /**
     * Le véhicule est-il en état de rouler ?
     */
    public function canDrive(): bool
    {
        return in_array($this, [self::PARKING, self::AFFECTE]);
    }

    /**
     * Le véhicule nécessite-t-il une intervention technique ?
     */
    public function requiresMaintenance(): bool
    {
        return in_array($this, [self::EN_PANNE, self::EN_MAINTENANCE]);
    }

    /**
     * Est-ce un état terminal (aucune transition sortante) ?
     */
    public function isTerminal(): bool
    {
        return $this === self::REFORME;
    }

    // =========================================================================
    // STATE TRANSITIONS - ALLOWED NEXT STATES
    // =========================================================================

    /**
     * Retourne les transitions valides depuis cet état
     *
     * @return array<VehicleStatusEnum>
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::PARKING => [self::AFFECTE, self::EN_PANNE],
            self::AFFECTE => [self::PARKING, self::EN_PANNE],
            self::EN_PANNE => [self::EN_MAINTENANCE, self::PARKING], // Parking si panne mineure résolue
            self::EN_MAINTENANCE => [self::PARKING, self::REFORME],
            self::REFORME => [], // État terminal
        };
    }

    /**
     * Vérifie si la transition vers un nouveau statut est autorisée
     */
    public function canTransitionTo(VehicleStatusEnum $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions(), true);
    }

    /**
     * Retourne le message d'erreur si transition invalide
     */
    public function getTransitionErrorMessage(VehicleStatusEnum $newStatus): string
    {
        if ($this === $newStatus) {
            return "Le véhicule est déjà en statut '{$this->label()}'.";
        }

        if ($this->isTerminal()) {
            return "Un véhicule réformé ne peut plus changer de statut.";
        }

        $allowed = array_map(fn($s) => $s->label(), $this->allowedTransitions());
        $allowedStr = implode(', ', $allowed);

        return "Transition impossible de '{$this->label()}' vers '{$newStatus->label()}'. "
             . "Transitions autorisées : {$allowedStr}.";
    }

    // =========================================================================
    // FACTORY & HELPERS
    // =========================================================================

    /**
     * Crée une instance depuis une string (case-insensitive)
     */
    public static function fromString(string $value): ?self
    {
        $value = strtolower($value);

        foreach (self::cases() as $case) {
            if ($case->value === $value || strtolower($case->label()) === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Retourne tous les statuts opérationnels
     */
    public static function operational(): array
    {
        return [self::PARKING, self::AFFECTE];
    }

    /**
     * Retourne tous les statuts nécessitant maintenance
     */
    public static function needingMaintenance(): array
    {
        return [self::EN_PANNE, self::EN_MAINTENANCE];
    }

    /**
     * Options pour select dropdown (label => value)
     */
    public static function selectOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }

    /**
     * Ordre de tri recommandé pour l'affichage
     */
    public function sortOrder(): int
    {
        return match($this) {
            self::PARKING => 1,
            self::AFFECTE => 2,
            self::EN_PANNE => 3,
            self::EN_MAINTENANCE => 4,
            self::REFORME => 5,
        };
    }
}
