<?php

namespace App\Enums;

/**
 * 🚙 VEHICLE TYPE ENUM - Enterprise-Grade Type-Safe Implementation
 *
 * Types de véhicules pour catégorisation de la flotte.
 * Utilise des Enums PHP 8.2+ pour la robustesse et la sécurité de type.
 *
 * Types couverts :
 * - Voitures de tourisme et utilitaires légers
 * - Camions et poids lourds
 * - Motos et deux-roues
 * - Engins spéciaux (construction, agriculture)
 * - Véhicules spécialisés
 *
 * @version 2.0-Enterprise
 */
enum VehicleTypeEnum: string
{
    /**
     * Voiture de tourisme ou utilitaire léger
     */
    case VOITURE = 'voiture';

    /**
     * Camion ou poids lourd
     */
    case CAMION = 'camion';

    /**
     * Moto, scooter ou deux-roues motorisé
     */
    case MOTO = 'moto';

    /**
     * Engin spécial : construction, BTP, agriculture
     * (ex: pelleteuse, bulldozer, tracteur)
     */
    case ENGIN = 'engin';

    /**
     * Fourgonnette ou van utilitaire
     */
    case FOURGONNETTE = 'fourgonnette';

    /**
     * Bus ou minibus pour transport de personnes
     */
    case BUS = 'bus';

    /**
     * Véhicule léger utilitaire (VUL)
     */
    case VUL = 'vul';

    /**
     * Semi-remorque ou camion avec remorque
     */
    case SEMI_REMORQUE = 'semi_remorque';

    /**
     * Autre type de véhicule non catégorisé
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
            self::VOITURE => 'Voiture',
            self::CAMION => 'Camion',
            self::MOTO => 'Moto',
            self::ENGIN => 'Engin',
            self::FOURGONNETTE => 'Fourgonnette',
            self::BUS => 'Bus',
            self::VUL => 'VUL',
            self::SEMI_REMORQUE => 'Semi-remorque',
            self::AUTRE => 'Autre',
        };
    }

    /**
     * Description détaillée du type
     */
    public function description(): string
    {
        return match($this) {
            self::VOITURE => 'Voiture de tourisme ou véhicule utilitaire léger',
            self::CAMION => 'Camion ou poids lourd pour transport de marchandises',
            self::MOTO => 'Moto, scooter ou deux-roues motorisé',
            self::ENGIN => 'Engin spécialisé : construction, BTP, agriculture',
            self::FOURGONNETTE => 'Fourgonnette ou van utilitaire',
            self::BUS => 'Bus ou minibus pour transport collectif de personnes',
            self::VUL => 'Véhicule utilitaire léger (VUL)',
            self::SEMI_REMORQUE => 'Semi-remorque ou camion avec remorque',
            self::AUTRE => 'Autre type de véhicule non catégorisé',
        };
    }

    /**
     * Couleur Tailwind CSS pour badges
     */
    public function color(): string
    {
        return match($this) {
            self::VOITURE => 'blue',
            self::CAMION => 'purple',
            self::MOTO => 'green',
            self::ENGIN => 'yellow',
            self::FOURGONNETTE => 'indigo',
            self::BUS => 'pink',
            self::VUL => 'cyan',
            self::SEMI_REMORQUE => 'orange',
            self::AUTRE => 'gray',
        };
    }

    /**
     * Couleur hexadécimale pour graphiques/exports
     */
    public function hexColor(): string
    {
        return match($this) {
            self::VOITURE => '#3b82f6',      // Bleu
            self::CAMION => '#8b5cf6',       // Violet
            self::MOTO => '#10b981',         // Vert
            self::ENGIN => '#f59e0b',        // Jaune/Orange
            self::FOURGONNETTE => '#6366f1', // Indigo
            self::BUS => '#ec4899',          // Rose
            self::VUL => '#06b6d4',          // Cyan
            self::SEMI_REMORQUE => '#f97316',// Orange
            self::AUTRE => '#6b7280',        // Gris
        };
    }

    /**
     * Icône FontAwesome/Heroicons
     */
    public function icon(): string
    {
        return match($this) {
            self::VOITURE => 'car',
            self::CAMION => 'truck',
            self::MOTO => 'motorcycle',
            self::ENGIN => 'tractor',
            self::FOURGONNETTE => 'shuttle-van',
            self::BUS => 'bus',
            self::VUL => 'truck-moving',
            self::SEMI_REMORQUE => 'trailer',
            self::AUTRE => 'question-circle',
        };
    }

    /**
     * Classes CSS Tailwind complètes pour badge
     */
    public function badgeClasses(): string
    {
        $base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

        $colorClasses = match($this) {
            self::VOITURE => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::CAMION => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            self::MOTO => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::ENGIN => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::FOURGONNETTE => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            self::BUS => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
            self::VUL => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
            self::SEMI_REMORQUE => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            self::AUTRE => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };

        return "{$base} {$colorClasses}";
    }

    // =========================================================================
    // BUSINESS RULES - CHARACTERISTICS
    // =========================================================================

    /**
     * Nécessite un permis spécial (poids lourd, etc.) ?
     */
    public function requiresSpecialLicense(): bool
    {
        return in_array($this, [
            self::CAMION,
            self::BUS,
            self::SEMI_REMORQUE,
            self::ENGIN,
        ]);
    }

    /**
     * Catégorie de permis requis
     */
    public function requiredLicenseCategory(): ?string
    {
        return match($this) {
            self::VOITURE, self::FOURGONNETTE, self::VUL => 'B',
            self::MOTO => 'A',
            self::CAMION => 'C',
            self::BUS => 'D',
            self::SEMI_REMORQUE => 'CE',
            self::ENGIN => 'CACES',
            self::AUTRE => null,
        };
    }

    /**
     * Coût de maintenance estimé (1=faible, 5=très élevé)
     */
    public function maintenanceCostLevel(): int
    {
        return match($this) {
            self::MOTO => 2,
            self::VOITURE => 2,
            self::VUL => 3,
            self::FOURGONNETTE => 3,
            self::CAMION => 4,
            self::BUS => 4,
            self::SEMI_REMORQUE => 5,
            self::ENGIN => 5,
            self::AUTRE => 3,
        };
    }

    /**
     * Capacité moyenne de transport (en tonnes)
     */
    public function averageCapacityTons(): ?float
    {
        return match($this) {
            self::MOTO => 0.2,
            self::VOITURE => 0.5,
            self::VUL => 1.5,
            self::FOURGONNETTE => 2.0,
            self::CAMION => 12.0,
            self::SEMI_REMORQUE => 24.0,
            self::BUS => null, // Transport de personnes
            self::ENGIN => null, // Variable selon type
            self::AUTRE => null,
        };
    }

    /**
     * Type de véhicule pour transport de personnes ?
     */
    public function isPassengerTransport(): bool
    {
        return in_array($this, [self::BUS, self::VOITURE]);
    }

    /**
     * Type de véhicule pour transport de marchandises ?
     */
    public function isCargoTransport(): bool
    {
        return in_array($this, [
            self::CAMION,
            self::VUL,
            self::FOURGONNETTE,
            self::SEMI_REMORQUE,
        ]);
    }

    /**
     * Véhicule spécialisé nécessitant formation spécifique ?
     */
    public function requiresSpecializedTraining(): bool
    {
        return in_array($this, [self::ENGIN, self::SEMI_REMORQUE, self::BUS]);
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
     * Retourne les types standards (hors engins et autres)
     */
    public static function standardTypes(): array
    {
        return [
            self::VOITURE,
            self::CAMION,
            self::MOTO,
            self::FOURGONNETTE,
            self::BUS,
            self::VUL,
        ];
    }

    /**
     * Retourne les types pour transport lourd
     */
    public static function heavyDuty(): array
    {
        return [
            self::CAMION,
            self::SEMI_REMORQUE,
            self::ENGIN,
        ];
    }

    /**
     * Options pour select dropdown (label => value)
     */
    public static function selectOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }

    /**
     * Options groupées par catégorie
     */
    public static function groupedOptions(): array
    {
        return [
            'Véhicules légers' => [
                self::VOITURE->value => self::VOITURE->label(),
                self::MOTO->value => self::MOTO->label(),
                self::VUL->value => self::VUL->label(),
                self::FOURGONNETTE->value => self::FOURGONNETTE->label(),
            ],
            'Poids lourds' => [
                self::CAMION->value => self::CAMION->label(),
                self::SEMI_REMORQUE->value => self::SEMI_REMORQUE->label(),
            ],
            'Transport collectif' => [
                self::BUS->value => self::BUS->label(),
            ],
            'Engins spécialisés' => [
                self::ENGIN->value => self::ENGIN->label(),
                self::AUTRE->value => self::AUTRE->label(),
            ],
        ];
    }

    /**
     * Ordre de tri recommandé pour l'affichage
     */
    public function sortOrder(): int
    {
        return match($this) {
            self::VOITURE => 1,
            self::MOTO => 2,
            self::VUL => 3,
            self::FOURGONNETTE => 4,
            self::CAMION => 5,
            self::SEMI_REMORQUE => 6,
            self::BUS => 7,
            self::ENGIN => 8,
            self::AUTRE => 9,
        };
    }
}
