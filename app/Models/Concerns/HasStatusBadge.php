<?php

namespace App\Models\Concerns;

use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use App\Enums\VehicleTypeEnum;

/**
 * 🎨 HAS STATUS BADGE TRAIT - Enterprise-Grade UI Helpers
 *
 * Trait pour ajouter des méthodes d'affichage de badges Tailwind CSS
 * aux modèles utilisant des statuts et types.
 *
 * Fonctionnalités:
 * - Génération automatique de badges HTML
 * - Styles Tailwind CSS cohérents
 * - Support dark mode
 * - Icônes FontAwesome intégrées
 * - Extensible et personnalisable
 *
 * Usage:
 * ```php
 * use HasStatusBadge;
 *
 * // Dans une vue Blade:
 * {!! $vehicle->statusBadge() !!}
 * {!! $vehicle->typeBadge() !!}
 * {!! $driver->statusBadge() !!}
 * ```
 *
 * @version 2.0-Enterprise
 */
trait HasStatusBadge
{
    // =========================================================================
    // STATUS BADGE METHODS
    // =========================================================================

    /**
     * Génère le badge HTML pour le statut de l'entité
     *
     * @param array $options Options de personnalisation (size, icon, etc.)
     * @return string HTML du badge
     */
    public function statusBadge(array $options = []): string
    {
        $statusEnum = $this->getStatusEnum();

        if (!$statusEnum) {
            return $this->defaultBadge('Inconnu', $options);
        }

        $showIcon = $options['icon'] ?? true;
        $size = $options['size'] ?? 'default'; // default, sm, lg

        return $this->renderBadge(
            label: $statusEnum->label(),
            classes: $statusEnum->badgeClasses(),
            icon: $showIcon ? $statusEnum->icon() : null,
            size: $size
        );
    }

    /**
     * Récupère l'Enum du statut actuel
     *
     * @return VehicleStatusEnum|DriverStatusEnum|null
     */
    protected function getStatusEnum()
    {
        // Si le modèle utilise un cast Enum direct
        if (isset($this->status) && (
            $this->status instanceof VehicleStatusEnum ||
            $this->status instanceof DriverStatusEnum
        )) {
            return $this->status;
        }

        // Sinon, récupérer depuis la relation
        if (method_exists($this, 'vehicleStatus') && $this->vehicleStatus) {
            $slug = \Str::slug($this->vehicleStatus->name);
            return VehicleStatusEnum::tryFrom($slug);
        }

        if (method_exists($this, 'driverStatus') && $this->driverStatus) {
            $slug = \Str::slug($this->driverStatus->name);
            return DriverStatusEnum::tryFrom($slug);
        }

        return null;
    }

    // =========================================================================
    // TYPE BADGE METHODS (for Vehicles)
    // =========================================================================

    /**
     * Génère le badge HTML pour le type de véhicule
     *
     * @param array $options Options de personnalisation
     * @return string HTML du badge
     */
    public function typeBadge(array $options = []): string
    {
        if (!method_exists($this, 'vehicleType')) {
            return '';
        }

        $typeEnum = $this->getTypeEnum();

        if (!$typeEnum) {
            return $this->defaultBadge('Type inconnu', $options);
        }

        $showIcon = $options['icon'] ?? true;
        $size = $options['size'] ?? 'default';

        return $this->renderBadge(
            label: $typeEnum->label(),
            classes: $typeEnum->badgeClasses(),
            icon: $showIcon ? $typeEnum->icon() : null,
            size: $size
        );
    }

    /**
     * Récupère l'Enum du type actuel
     *
     * @return VehicleTypeEnum|null
     */
    protected function getTypeEnum(): ?VehicleTypeEnum
    {
        // Si le modèle utilise un cast Enum direct
        if (isset($this->type) && $this->type instanceof VehicleTypeEnum) {
            return $this->type;
        }

        // Sinon, récupérer depuis la relation
        if (method_exists($this, 'vehicleType') && $this->vehicleType) {
            $slug = \Str::slug($this->vehicleType->name);
            return VehicleTypeEnum::tryFrom($slug);
        }

        return null;
    }

    // =========================================================================
    // RENDERING METHODS
    // =========================================================================

    /**
     * Génère le HTML d'un badge avec Tailwind CSS
     *
     * @param string $label Texte du badge
     * @param string $classes Classes CSS Tailwind
     * @param string|null $icon Nom de l'icône FontAwesome (sans 'fa-')
     * @param string $size Taille: sm, default, lg
     * @return string
     */
    protected function renderBadge(
        string $label,
        string $classes,
        ?string $icon = null,
        string $size = 'default'
    ): string {
        // Classes de base selon la taille
        $sizeClasses = match($size) {
            'sm' => 'px-2 py-0.5 text-xs',
            'lg' => 'px-3 py-1 text-sm',
            default => 'px-2.5 py-0.5 text-xs',
        };

        // Structure du badge
        $badgeClasses = "inline-flex items-center rounded-full font-medium {$sizeClasses} {$classes}";

        // Icône (si fournie)
        $iconHtml = $icon
            ? '<svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><use href="#icon-' . $icon . '"/></svg>'
            : '';

        // Alternative avec FontAwesome (si disponible dans le projet)
        if ($icon && config('app.use_fontawesome', true)) {
            $iconHtml = '<i class="fas fa-' . $icon . ' mr-1"></i>';
        }

        return sprintf(
            '<span class="%s">%s%s</span>',
            $badgeClasses,
            $iconHtml,
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Badge par défaut pour valeur inconnue
     */
    protected function defaultBadge(string $label, array $options = []): string
    {
        $size = $options['size'] ?? 'default';
        $defaultClasses = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';

        return $this->renderBadge(
            label: $label,
            classes: $defaultClasses,
            icon: $options['icon'] ?? null,
            size: $size
        );
    }

    // =========================================================================
    // TEXT-ONLY HELPERS (no HTML)
    // =========================================================================

    /**
     * Retourne le label du statut (texte seul)
     */
    public function statusLabel(): string
    {
        $statusEnum = $this->getStatusEnum();
        return $statusEnum ? $statusEnum->label() : 'Inconnu';
    }

    /**
     * Retourne le label du type (texte seul)
     */
    public function typeLabel(): string
    {
        $typeEnum = $this->getTypeEnum();
        return $typeEnum ? $typeEnum->label() : 'Inconnu';
    }

    /**
     * Retourne la couleur hexadécimale du statut (pour graphiques)
     */
    public function statusColor(): string
    {
        $statusEnum = $this->getStatusEnum();
        return $statusEnum ? $statusEnum->hexColor() : '#6b7280';
    }

    /**
     * Retourne la couleur hexadécimale du type (pour graphiques)
     */
    public function typeColor(): string
    {
        $typeEnum = $this->getTypeEnum();
        return $typeEnum ? $typeEnum->hexColor() : '#6b7280';
    }

    // =========================================================================
    // TAILWIND CSS HELPER - Direct classes access
    // =========================================================================

    /**
     * Retourne les classes CSS Tailwind pour le statut
     *
     * Utile pour Alpine.js ou Livewire bindings directs
     */
    public function statusTailwindClasses(): string
    {
        $statusEnum = $this->getStatusEnum();
        return $statusEnum ? $statusEnum->badgeClasses() : 'bg-gray-50 text-gray-700 border border-gray-200';
    }

    /**
     * Retourne les classes CSS Tailwind pour le type
     */
    public function typeTailwindClasses(): string
    {
        $typeEnum = $this->getTypeEnum();
        return $typeEnum ? $typeEnum->badgeClasses() : 'bg-gray-50 text-gray-700 border border-gray-200';
    }

    // =========================================================================
    // ADVANCED BADGE METHODS - Multiple badges
    // =========================================================================

    /**
     * Génère un badge combiné statut + type (pour véhicules)
     *
     * @param array $options
     * @return string HTML avec les deux badges
     */
    public function statusAndTypeBadges(array $options = []): string
    {
        $spacing = $options['spacing'] ?? 'space-x-2';
        $statusBadge = $this->statusBadge($options);
        $typeBadge = method_exists($this, 'vehicleType') ? $this->typeBadge($options) : '';

        if (!$typeBadge) {
            return $statusBadge;
        }

        return sprintf(
            '<div class="inline-flex items-center %s">%s%s</div>',
            $spacing,
            $statusBadge,
            $typeBadge
        );
    }

    /**
     * Badge avec tooltip (nécessite Alpine.js ou Tippy.js)
     *
     * @param array $options
     * @return string
     */
    public function statusBadgeWithTooltip(array $options = []): string
    {
        $statusEnum = $this->getStatusEnum();

        if (!$statusEnum) {
            return $this->statusBadge($options);
        }

        $badge = $this->statusBadge($options);
        $tooltip = $statusEnum->description();

        // Version Alpine.js
        return sprintf(
            '<div x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false" class="relative inline-block">
                %s
                <div x-show="tooltip" x-transition class="absolute z-10 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip dark:bg-gray-700 bottom-full left-1/2 transform -translate-x-1/2 mb-2 whitespace-nowrap">
                    %s
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>',
            $badge,
            htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8')
        );
    }
}
