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
     * Couleur Tailwind CSS pour badges - Design Ultra-Pro
     */
    public function color(): string
    {
        return match($this) {
            self::PARKING => 'sky',        // Bleu ciel pour disponibilité
            self::AFFECTE => 'emerald',    // Vert émeraude pour actif
            self::EN_PANNE => 'rose',       // Rouge rosé pour urgence
            self::EN_MAINTENANCE => 'amber', // Ambre pour maintenance
            self::REFORME => 'slate',      // Gris ardoise pour archivé
        };
    }

    /**
     * Couleur hexadécimale pour graphiques/exports - Palette Enterprise
     */
    public function hexColor(): string
    {
        return match($this) {
            self::PARKING => '#0ea5e9',     // Sky-500 - Disponible
            self::AFFECTE => '#10b981',     // Emerald-500 - Actif
            self::EN_PANNE => '#f43f5e',    // Rose-500 - Panne
            self::EN_MAINTENANCE => '#f59e0b', // Amber-500 - Maintenance
            self::REFORME => '#64748b',     // Slate-500 - Réformé
        };
    }

    /**
     * Icône moderne pour Iconify/Lucide - Design System Ultra-Pro
     */
    public function icon(): string
    {
        return match($this) {
            self::PARKING => 'check-circle',        // Disponible
            self::AFFECTE => 'user-check',          // Assigné à un chauffeur
            self::EN_PANNE => 'alert-triangle',     // Alerte panne
            self::EN_MAINTENANCE => 'wrench',       // En réparation
            self::REFORME => 'archive',             // Archivé/Réformé
        };
    }

    /**
     * Classes CSS Tailwind complètes pour badge - Style Enterprise Grade
     */
    public function badgeClasses(): string
    {
        $colorClasses = match($this) {
            // Parking: Bleu clair professionnel - Disponible
            self::PARKING => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
            
            // Affecté: Vert émeraude clair - Actif/opérationnel  
            self::AFFECTE => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
            
            // En panne: Rouge rose clair - Attention requise
            self::EN_PANNE => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
            
            // En maintenance: Ambre clair - Travaux en cours
            self::EN_MAINTENANCE => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
            
            // Réformé: Gris neutre - Archivé/inactif
            self::REFORME => 'bg-gray-100 text-gray-600 ring-1 ring-gray-200',
        };

        return $colorClasses;
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
