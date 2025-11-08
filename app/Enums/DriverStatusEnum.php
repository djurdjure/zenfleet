<?php

namespace App\Enums;

/**
 * 👤 DRIVER STATUS ENUM - Enterprise-Grade Type-Safe Implementation
 *
 * Statuts des chauffeurs alignés avec les processus RH et opérationnels.
 * Utilise des Enums PHP 8.2+ pour la robustesse et la sécurité de type.
 *
 * Architecture:
 * - Backed Enum (string) pour mapping vers DB
 * - Méthodes helper pour logique métier
 * - Integration avec State Machine Pattern
 *
 * @version 2.0-Enterprise
 * @see App\Services\DriverStatusTransitionService Pour les règles de transition
 */
enum DriverStatusEnum: string
{
    /**
     * Chauffeur disponible, peut recevoir une affectation.
     */
    case DISPONIBLE = 'disponible';

    /**
     * Chauffeur actuellement en mission (véhicule affecté).
     */
    case EN_MISSION = 'en_mission';

    /**
     * Chauffeur en congé, ne peut être affecté.
     */
    case EN_CONGE = 'en_conge';

    /**
     * Autre statut : sanctionné, en formation, en maladie, etc.
     * Statut générique pour situations exceptionnelles.
     */
    case AUTRE = 'autre';

    // =========================================================================
    // MÉTHODES HELPER - BUSINESS LOGIC
    // =========================================================================

    /**
     * Retourne le label français pour affichage UI
     */
    public function label(): string
    {
        return match($this) {
            self::DISPONIBLE => 'Disponible',
            self::EN_MISSION => 'En mission',
            self::EN_CONGE => 'En congé',
            self::AUTRE => 'Autre',
        };
    }

    /**
     * Description détaillée du statut
     */
    public function description(): string
    {
        return match($this) {
            self::DISPONIBLE => 'Chauffeur disponible, peut recevoir une affectation de véhicule',
            self::EN_MISSION => 'Chauffeur actuellement en mission avec un véhicule affecté',
            self::EN_CONGE => 'Chauffeur en congé, indisponible pour affectation',
            self::AUTRE => 'Statut spécial : sanctionné, en formation, en maladie, etc.',
        };
    }

    /**
     * Couleur Tailwind CSS pour badges
     */
    public function color(): string
    {
        return match($this) {
            self::DISPONIBLE => 'green',
            self::EN_MISSION => 'blue',
            self::EN_CONGE => 'orange',
            self::AUTRE => 'gray',
        };
    }

    /**
     * Couleur hexadécimale pour graphiques/exports
     */
    public function hexColor(): string
    {
        return match($this) {
            self::DISPONIBLE => '#10b981',  // Vert
            self::EN_MISSION => '#3b82f6',  // Bleu
            self::EN_CONGE => '#f59e0b',    // Orange
            self::AUTRE => '#6b7280',       // Gris
        };
    }

    /**
     * Icône FontAwesome/Heroicons
     */
    public function icon(): string
    {
        return match($this) {
            self::DISPONIBLE => 'user-check',
            self::EN_MISSION => 'road',
            self::EN_CONGE => 'calendar-times',
            self::AUTRE => 'info-circle',
        };
    }

    /**
     * Classes CSS Tailwind complètes pour badge
     */
    public function badgeClasses(): string
    {
        $base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

        $colorClasses = match($this) {
            self::DISPONIBLE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::EN_MISSION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::EN_CONGE => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            self::AUTRE => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };

        return "{$base} {$colorClasses}";
    }

    // =========================================================================
    // BUSINESS RULES - CAPABILITIES
    // =========================================================================

    /**
     * Le chauffeur peut-il se voir affecter un véhicule ?
     */
    public function canBeAssigned(): bool
    {
        return $this === self::DISPONIBLE;
    }

    /**
     * Le chauffeur est-il actuellement en service ?
     */
    public function isOnDuty(): bool
    {
        return $this === self::EN_MISSION;
    }

    /**
     * Le chauffeur est-il indisponible temporairement ?
     */
    public function isTemporarilyUnavailable(): bool
    {
        return in_array($this, [self::EN_CONGE, self::AUTRE]);
    }

    /**
     * Le chauffeur peut-il conduire ?
     */
    public function canDrive(): bool
    {
        return in_array($this, [self::DISPONIBLE, self::EN_MISSION]);
    }

    /**
     * Nécessite une validation RH pour changer ce statut ?
     */
    public function requiresHRValidation(): bool
    {
        return $this === self::AUTRE;
    }

    // =========================================================================
    // STATE TRANSITIONS - ALLOWED NEXT STATES
    // =========================================================================

    /**
     * Retourne les transitions valides depuis cet état
     *
     * @return array<DriverStatusEnum>
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DISPONIBLE => [self::EN_MISSION, self::EN_CONGE, self::AUTRE],
            self::EN_MISSION => [self::DISPONIBLE], // Seulement retour après fin de mission
            self::EN_CONGE => [self::DISPONIBLE, self::AUTRE],
            self::AUTRE => [self::DISPONIBLE, self::EN_CONGE],
        };
    }

    /**
     * Vérifie si la transition vers un nouveau statut est autorisée
     */
    public function canTransitionTo(DriverStatusEnum $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions(), true);
    }

    /**
     * Retourne le message d'erreur si transition invalide
     */
    public function getTransitionErrorMessage(DriverStatusEnum $newStatus): string
    {
        if ($this === $newStatus) {
            return "Le chauffeur est déjà en statut '{$this->label()}'.";
        }

        $allowed = array_map(fn($s) => $s->label(), $this->allowedTransitions());
        $allowedStr = implode(', ', $allowed);

        return "Transition impossible de '{$this->label()}' vers '{$newStatus->label()}'. "
             . "Transitions autorisées : {$allowedStr}.";
    }

    // =========================================================================
    // BUSINESS CONTEXT - REASONS
    // =========================================================================

    /**
     * Raisons possibles pour le statut AUTRE
     */
    public static function otherStatusReasons(): array
    {
        return [
            'sanction' => 'Sanctionné',
            'maladie' => 'En maladie',
            'formation' => 'En formation',
            'accident' => 'Accident de travail',
            'administrative' => 'Raison administrative',
            'other' => 'Autre raison',
        ];
    }

    /**
     * Types de congés
     */
    public static function leaveTypes(): array
    {
        return [
            'annual' => 'Congé annuel',
            'sick' => 'Congé maladie',
            'maternity' => 'Congé maternité',
            'paternity' => 'Congé paternité',
            'unpaid' => 'Congé sans solde',
            'exceptional' => 'Congé exceptionnel',
        ];
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
     * Retourne tous les statuts permettant affectation
     */
    public static function assignable(): array
    {
        return [self::DISPONIBLE];
    }

    /**
     * Retourne tous les statuts actifs (peut conduire)
     */
    public static function active(): array
    {
        return [self::DISPONIBLE, self::EN_MISSION];
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
            self::DISPONIBLE => 1,
            self::EN_MISSION => 2,
            self::EN_CONGE => 3,
            self::AUTRE => 4,
        };
    }
}
