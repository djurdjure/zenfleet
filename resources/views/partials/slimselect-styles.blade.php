{{-- ====================================================================
🎨 SLIMSELECT ENTERPRISE STYLES - REUSABLE PARTIAL
====================================================================
Styles enterprise-grade pour SlimSelect, harmonisés avec le design
de l'application (assignment-form.blade.php comme référence).

USAGE:
@include('partials.slimselect-styles')

ou dans un push:
@push('styles')
    @include('partials.slimselect-styles')
@endpush

@version 1.0-Enterprise
@since 2025-12-05
==================================================================== --}}

<style>
    /* ========================================
   ZENFLEET SLIMSELECT ENTERPRISE THEME
   ======================================== */

    :root {
        --ss-main-height: 42px;
        --ss-primary-color: #2563eb;
        /* blue-600 */
        --ss-bg-color: #ffffff;
        --ss-font-color: #111827;
        /* gray-900 */
        --ss-font-placeholder-color: #9ca3af;
        /* gray-400 */
        --ss-border-color: #d1d5db;
        /* gray-300 */
        --ss-border-radius: 0.5rem;
        /* rounded-lg */
        --ss-spacing-l: 10px;
        --ss-spacing-m: 8px;
        --ss-spacing-s: 4px;
        --ss-animation-timing: 0.2s;
        --ss-focus-color: #3b82f6;
        /* blue-500 */
        --ss-error-color: #dc2626;
        /* red-600 */
    }

    /* Main container styling */
    .ss-main {
        background-color: #f9fafb;
        /* gray-50 */
        border-color: #d1d5db;
        /* gray-300 */
        color: #111827;
        /* gray-900 */
        border-radius: 0.5rem;
        /* rounded-lg */
        padding: 2px 0;
        /* Ajustement padding */
        min-height: 42px;
        /* Hauteur minimale */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        /* shadow-sm */
    }

    /* Focus state */
    .ss-main:focus-within {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 1px var(--ss-focus-color);
        /* ring-1 */
        background-color: #ffffff;
    }

    /* Values styling */
    .ss-main .ss-values .ss-single {
        padding: 4px var(--ss-spacing-l);
        font-size: 0.875rem;
        /* text-sm = 14px */
        line-height: 1.25rem;
        /* leading-5 */
        font-weight: 400;
    }

    /* Placeholder styling */
    .ss-main .ss-values .ss-placeholder {
        font-size: 0.875rem;
        font-style: normal;
    }

    /* Dropdown content - ombre plus prononcée */
    .ss-content {
        margin-top: 4px;
        box-shadow:
            0 10px 15px -3px rgba(0, 0, 0, 0.1),
            /* shadow-lg */
            0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-color: #e5e7eb;
        /* gray-200 */
    }

    /* Champ de recherche */
    .ss-content .ss-search {
        background-color: #f9fafb;
        /* gray-50 */
        border-bottom: 1px solid #e5e7eb;
        /* gray-200 */
        padding: var(--ss-spacing-m);
    }

    .ss-content .ss-search input {
        font-size: 0.875rem;
        padding: 10px 12px;
        border-radius: 6px;
        /* rounded-md */
    }

    .ss-content .ss-search input:focus {
        border-color: var(--ss-focus-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Options - style hover amélioré */
    .ss-content .ss-list .ss-option {
        font-size: 0.875rem;
        padding: 10px var(--ss-spacing-l);
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .ss-content .ss-list .ss-option:hover {
        background-color: #eff6ff;
        /* blue-50 */
        color: var(--ss-font-color);
        /* Garder texte lisible */
    }

    /* Option sélectionnée - fond plus subtil */
    .ss-content .ss-list .ss-option.ss-highlighted,
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
        background-color: var(--ss-primary-color);
        color: #ffffff;
    }

    /* Option sélectionnée avec checkmark */
    .ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected::after {
        content: '✓';
        margin-left: auto;
        font-weight: 600;
    }

    /* État d'erreur de validation */
    .slimselect-error .ss-main {
        border-color: var(--ss-error-color) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        /* ring-red-600/10 */
    }

    /* Cacher le placeholder dans la liste des options */
    .ss-content .ss-list .ss-option[data-placeholder="true"] {
        display: none !important;
    }

    /* Message d'erreur */
    .ss-content .ss-list .ss-error {
        font-size: 0.875rem;
        padding: var(--ss-spacing-l);
    }

    /* Message de recherche en cours */
    .ss-content .ss-list .ss-searching {
        font-size: 0.875rem;
        color: var(--ss-primary-color);
        padding: var(--ss-spacing-l);
    }

    /* Flèche de dropdown */
    .ss-main .ss-arrow path {
        stroke-width: 14;
    }

    /* Animation d'ouverture du dropdown */
    .ss-content.ss-open-below,
    .ss-content.ss-open-above {
        animation: zenfleetSlideIn var(--ss-animation-timing) ease-out;
    }

    @keyframes zenfleetSlideIn {
        from {
            opacity: 0;
            transform: scaleY(0.95) translateY(-4px);
        }

        to {
            opacity: 1;
            transform: scaleY(1) translateY(0);
        }
    }

    /* ========================================
   RESPONSIVE MOBILE
   ======================================== */
    @media (max-width: 640px) {
        :root {
            --ss-main-height: 44px;
            /* Plus grand pour touch */
            --ss-content-height: 240px;
        }

        .ss-content .ss-list .ss-option {
            padding: 12px var(--ss-spacing-l);
            /* Touch-friendly */
            min-height: 44px;
            /* iOS minimum */
        }

        .ss-content .ss-search input {
            padding: 12px;
            font-size: 16px;
            /* Évite zoom iOS */
        }
    }

    /* ========================================
   ACCESSIBILITÉ
   ======================================== */
    @media (prefers-reduced-motion: reduce) {

        .ss-main,
        .ss-content,
        .ss-option {
            transition: none !important;
            animation: none !important;
        }
    }
</style>