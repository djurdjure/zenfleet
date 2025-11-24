{{-- ====================================================================
🔧 FORMULAIRE CRÉATION OPÉRATION DE MAINTENANCE - ENTERPRISE GRADE V3
====================================================================

Design ultra-professionnel harmonisé avec assignments/create:
✨ Header enterprise-grade avec gradient et statistiques
✨ SlimSelect ZenFleet avec hauteur standardisée (42px)
✨ Section Informations Principales avec fond gris clair professionnel
✨ Sections uniformisées avec style form-section
✨ Footer avec gradient et boutons alignés
✨ Auto-complétion intelligente depuis maintenance_types
✨ Validation temps réel avec feedback visuel
✨ Layout responsive et accessible (WAI-ARIA)

@version 3.1-Enterprise-Grade-Iconify
@since 2025-11-23
@author ZenFleet Architecture Team - Expert Système Senior
==================================================================== --}}

<div x-data="maintenanceOperationFormValidation()" class="space-y-6 fade-in">

    {{-- ===============================================
    ALERTES GLOBALES DE VALIDATION
    =============================================== --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl shadow-lg" role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-red-600" />
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-3">Erreurs de validation</h3>
                    <p class="text-sm text-red-700 mb-2">Veuillez corriger les erreurs suivantes avant de continuer :</p>
                    <ul class="mt-2 ml-5 list-disc text-sm space-y-1 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(count($errors_list) > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-xl shadow-lg" role="alert">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-red-600" />
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-red-900 mb-3">{{ count($errors_list) }} erreur(s) détectée(s)</h3>
                    <ul class="mt-2 space-y-2 text-sm">
                        @foreach($errors_list as $error)
                            <li class="flex items-start gap-2">
                                <x-iconify icon="lucide:alert-circle" class="w-4 h-4 flex-shrink-0 mt-0.5 text-red-600" />
                                <span class="text-red-700">{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ===============================================
    FORMULAIRE PRINCIPAL
    =============================================== --}}
    <form wire:submit="save" class="space-y-6">

        {{-- ===============================================
        SECTION 1: INFORMATIONS PRINCIPALES (FOND GRIS PROFESSIONNEL)
        =============================================== --}}
        <x-card class="bg-gradient-to-br from-gray-50 to-slate-50 border-2 border-gray-200">
            <div class="space-y-6">
                <div class="pb-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                        <x-iconify icon="heroicons:wrench" class="w-5 h-5 text-gray-700" />
                        Informations Principales
                    </h2>
                    <p class="text-sm text-gray-600">Sélectionnez le véhicule, le type et le fournisseur pour cette opération.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Sélection Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:truck" class="w-4 h-4 text-gray-500" />
                                Véhicule
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div wire:ignore id="vehicle-select-wrapper">
                            <select
                                id="vehicle_id"
                                name="vehicle_id"
                                class="slimselect-vehicle w-full"
                                required>
                            {{-- Option placeholder avec data-placeholder pour SlimSelect --}}
                            <option data-placeholder="true" value=""></option>
                            @foreach($vehicleOptions as $vehicle)
                                <option
                                    value="{{ $vehicle->id }}"
                                    data-mileage="{{ $vehicle->current_mileage ?? 0 }}"
                                    data-brand="{{ $vehicle->brand }}"
                                    data-model="{{ $vehicle->model }}"
                                    @selected($vehicle_id == $vehicle->id)>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        @error('vehicle_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Sélectionnez le véhicule concerné par la maintenance</p>

                        {{-- Indicateur kilométrage actuel --}}
                        <div
                            id="mileage-display-section"
                            class="mt-3 p-4 bg-white border-2 border-blue-200 rounded-lg shadow-sm"
                            style="display: {{ $current_vehicle_mileage ? 'block' : 'none' }};">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-start gap-2.5">
                                    <x-iconify icon="heroicons:gauge" class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <p class="font-semibold text-blue-900 text-sm">Kilométrage du véhicule</p>
                                        <p class="text-xs text-blue-600 mt-0.5">
                                            Actuel: <strong class="font-bold" id="current-mileage-display">{{ number_format($current_vehicle_mileage ?? 0) }} km</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Champ de saisie du kilométrage --}}
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <input
                                        type="number"
                                        wire:model.live="mileage_at_maintenance"
                                        id="mileage_at_maintenance_input"
                                        class="flex-1 px-3 py-2 text-sm border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Entrer le kilométrage lors de la maintenance"
                                        min="{{ $current_vehicle_mileage ?? 0 }}">
                                    <span class="text-sm font-medium text-gray-600">km</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sélection Type de Maintenance --}}
                    <div>
                        <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:cog" class="w-4 h-4 text-gray-500" />
                                Type de Maintenance
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div wire:ignore id="type-select-wrapper">
                            <select
                                id="maintenance_type_id"
                                name="maintenance_type_id"
                                class="slimselect-type w-full"
                                required>
                                <option data-placeholder="true" value=""></option>
                                @foreach($maintenanceTypeOptions as $type)
                                    <option
                                        value="{{ $type->id }}"
                                        data-category="{{ $type->category }}"
                                        data-duration-minutes="{{ $type->estimated_duration_minutes ?? '' }}"
                                        data-duration-hours="{{ $type->estimated_duration_hours ?? '' }}"
                                        data-cost="{{ $type->estimated_cost ?? '' }}"
                                        data-description="{{ $type->description ?? '' }}"
                                        @selected($maintenance_type_id == $type->id)>
                                        {{ $type->display_text }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('maintenance_type_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Sélectionnez le type d'intervention à effectuer</p>

                        {{-- Affichage données auto-remplies --}}
                        @if($selectedMaintenanceType)
                            <div class="mt-2 flex items-center gap-2 text-sm text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                                <x-iconify icon="heroicons:check-circle" class="w-4 h-4" />
                                <span>Durée et coût estimés auto-remplis depuis le type sélectionné</span>
                            </div>
                        @endif
                    </div>

                    {{-- Sélection Fournisseur --}}
                    <div>
                        <label for="provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:building-storefront" class="w-4 h-4 text-gray-500" />
                                Fournisseur
                                <span class="text-gray-400">(Optionnel)</span>
                            </div>
                        </label>
                        <div wire:ignore id="provider-select-wrapper">
                            <select
                                id="provider_id"
                                name="provider_id"
                                class="slimselect-provider w-full">
                                <option data-placeholder="true" value=""></option>
                                @foreach($providerOptions as $provider)
                                    <option
                                        value="{{ $provider->id }}"
                                        data-type="{{ $provider->supplier_type ?? '' }}"
                                        data-rating="{{ $provider->rating ?? '' }}"
                                        @selected($provider_id == $provider->id)>
                                        {{ $provider->display_text }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('provider_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Sélectionnez le garage ou atelier qui effectuera la maintenance</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- ===============================================
        SECTION 2: DATES ET PLANIFICATION
        =============================================== --}}
        <x-card>
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                        <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-blue-600" />
                        Dates et Planification
                    </h2>
                    <p class="text-sm text-gray-600">Définissez les dates de planification et de réalisation de la maintenance.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Date planifiée --}}
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:calendar" class="w-4 h-4 text-gray-500" />
                                Date Planifiée
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <div wire:ignore>
                            <input type="text"
                                   id="scheduled_date"
                                   wire:model="scheduled_date"
                                   class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 flatpickr-input"
                                   placeholder="Sélectionnez une date...">
                        </div>
                        @error('scheduled_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Date prévue pour l'intervention</p>
                    </div>

                    {{-- Date de completion --}}
                    <div>
                        <label for="completed_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-gray-500" />
                                Date de Completion
                                <span class="text-gray-400">(Optionnel)</span>
                            </div>
                        </label>
                        <div wire:ignore>
                            <input type="text"
                                   id="completed_date"
                                   wire:model="completed_date"
                                   class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 flatpickr-input"
                                   placeholder="Sélectionnez une date...">
                        </div>
                        @error('completed_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Date effective de fin d'intervention</p>
                    </div>

                    {{-- Statut --}}
                    <div class="col-span-full">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:signal" class="w-4 h-4 text-gray-500" />
                                Statut
                                <span class="text-red-500">*</span>
                            </div>
                        </label>
                        <select wire:model="status"
                                id="status"
                                class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">État actuel de l'opération</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- ===============================================
        SECTION 3: DÉTAILS OPÉRATIONNELS
        =============================================== --}}
        <x-card>
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                        <x-iconify icon="heroicons:cog-6-tooth" class="w-5 h-5 text-blue-600" />
                        Détails Opérationnels
                    </h2>
                    <p class="text-sm text-gray-600">Renseignez la durée et le coût estimé de l'opération.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Durée --}}
                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-500" />
                                Durée
                                <span class="text-gray-400">(heures)</span>
                            </div>
                        </label>
                        <input type="number"
                               wire:model.live="duration_hours"
                               id="duration_hours"
                               min="0"
                               step="0.25"
                               class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400"
                               placeholder="Ex: 2.5">
                        @error('duration_minutes')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        @if($duration_hours)
                            <p class="mt-1.5 text-xs text-gray-500">
                                ≈ {{ number_format($duration_minutes ?? 0) }} minute{{ ($duration_minutes ?? 0) > 1 ? 's' : '' }}
                            </p>
                        @else
                            <p class="mt-1.5 text-xs text-gray-500">Durée estimée de l'intervention (ex: 2.5h = 2h30)</p>
                        @endif
                    </div>

                    {{-- Coût total --}}
                    <div>
                        <label for="total_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="heroicons:currency-dollar" class="w-4 h-4 text-gray-500" />
                                Coût Total
                                <span class="text-gray-400">(DA)</span>
                            </div>
                        </label>
                        <input type="number"
                               wire:model="total_cost"
                               id="total_cost"
                               min="0"
                               step="0.01"
                               class="bg-gray-50 border text-gray-900 text-sm rounded-lg block w-full p-2.5 transition-colors duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400"
                               placeholder="Ex: 5000.00">
                        @error('total_cost')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        @if($total_cost)
                            <p class="mt-1.5 text-xs text-gray-500">
                                {{ number_format($total_cost, 2, ',', ' ') }} DA
                            </p>
                        @else
                            <p class="mt-1.5 text-xs text-gray-500">Coût total de l'intervention</p>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        {{-- ===============================================
        SECTION 4: DESCRIPTION ET NOTES
        =============================================== --}}
        <x-card>
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                        <x-iconify icon="heroicons:document-text" class="w-5 h-5 text-blue-600" />
                        Description et Notes
                    </h2>
                    <p class="text-sm text-gray-600">Ajoutez une description détaillée et des notes sur l'opération.</p>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                        <span class="text-gray-400">(Optionnel)</span>
                    </label>
                    <textarea wire:model="description"
                              id="description"
                              rows="3"
                              maxlength="1000"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                              placeholder="Décrivez l'opération de maintenance..."></textarea>
                    <div class="flex justify-between mt-1.5">
                        @error('description')
                            <p class="text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @else
                            <p class="text-xs text-gray-500">Décrivez les travaux à effectuer ou effectués</p>
                        @enderror
                        <p class="text-xs text-gray-400">{{ strlen($description) }}/1000</p>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes Additionnelles
                        <span class="text-gray-400">(Optionnel)</span>
                    </label>
                    <textarea wire:model="notes"
                              id="notes"
                              rows="3"
                              maxlength="2000"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                              placeholder="Ajoutez des notes supplémentaires..."></textarea>
                    <div class="flex justify-between mt-1.5">
                        @error('notes')
                            <p class="text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @else
                            <p class="text-xs text-gray-500">Informations complémentaires, observations, recommandations...</p>
                        @enderror
                        <p class="text-xs text-gray-400">{{ strlen($notes) }}/2000</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- ===============================================
        BOUTONS D'ACTION
        =============================================== --}}
        <div class="flex items-center justify-end gap-3 pt-4">
            <a
                href="{{ route('admin.maintenance.operations.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                <span>Annuler</span>
            </a>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">

                <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                    <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
                    Créer l'opération
                </span>

                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Sauvegarde en cours...</span>
                </span>
            </button>
        </div>

    </form>
</div>

{{-- ===============================================
SCRIPTS ALPINE.JS & INITIALISATION SLIMSELECT/FLATPICKR
=============================================== --}}
@push('styles')
<style>
/**
 * 🎨 ZENFLEET SLIMSELECT - Variables CSS Natives
 * Cohérence visuelle avec Tailwind sans surcharge @apply
 * Styles identiques à la page assignments/create pour uniformité
 */

/* ========================================
   VARIABLES SLIMSELECT PERSONNALISÉES
   ======================================== */
:root {
    /* Couleurs alignées sur Tailwind/ZenFleet */
    --ss-primary-color: #2563eb;              /* blue-600 - couleur principale */
    --ss-bg-color: #ffffff;                   /* blanc */
    --ss-font-color: #1f2937;                 /* gray-800 - texte principal */
    --ss-font-placeholder-color: #9ca3af;     /* gray-400 - placeholder */
    --ss-disabled-color: #f3f4f6;             /* gray-100 - désactivé */
    --ss-border-color: #d1d5db;               /* gray-300 - bordure par défaut */
    --ss-highlight-color: #fef3c7;            /* yellow-100 - surlignage recherche */
    --ss-success-color: #16a34a;              /* green-600 */
    --ss-error-color: #dc2626;                /* red-600 */
    --ss-focus-color: #3b82f6;                /* blue-500 - focus ring */

    /* Dimensions cohérentes avec form-input */
    --ss-main-height: 42px;                   /* Même hauteur que les autres inputs */
    --ss-content-height: 280px;               /* Hauteur max dropdown */
    --ss-spacing-l: 12px;                     /* px-3 = 0.75rem = 12px */
    --ss-spacing-m: 8px;                      /* py-2 = 0.5rem = 8px */
    --ss-spacing-s: 4px;                      /* petit espacement */
    --ss-animation-timing: 0.2s;              /* transition fluide */
    --ss-border-radius: 8px;                  /* rounded-lg = 0.5rem = 8px */
}

/* ========================================
   AJUSTEMENTS MINIMAUX (sans @apply)
   ======================================== */

/* Container principal - alignement avec autres champs */
.ss-main {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    transition: all var(--ss-animation-timing) ease;
}

/* Focus state avec ring effect */
.ss-main:focus,
.ss-main.ss-open-below,
.ss-main.ss-open-above {
    border-color: var(--ss-focus-color);
    box-shadow:
        0 0 0 3px rgba(59, 130, 246, 0.1),      /* ring-blue-500/10 */
        0 1px 2px 0 rgba(0, 0, 0, 0.05);         /* shadow-sm */
}

/* Valeur sélectionnée - meilleur padding */
.ss-main .ss-values .ss-single {
    font-size: 0.875rem;                          /* text-sm = 14px */
    line-height: 1.25rem;                         /* leading-5 */
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
        0 10px 15px -3px rgba(0, 0, 0, 0.1),     /* shadow-lg */
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-color: #e5e7eb;                        /* gray-200 */
}

/* Champ de recherche */
.ss-content .ss-search {
    background-color: #f9fafb;                    /* gray-50 */
    border-bottom: 1px solid #e5e7eb;             /* gray-200 */
    padding: var(--ss-spacing-m);
}

.ss-content .ss-search input {
    font-size: 0.875rem;
    padding: 10px 12px;
    border-radius: 6px;                           /* rounded-md */
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
    background-color: #eff6ff;                    /* blue-50 */
    color: var(--ss-font-color);                  /* Garder texte lisible */
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

/* État d'erreur de validation Livewire */
.slimselect-error .ss-main {
    border-color: var(--ss-error-color) !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important; /* ring-red-600/10 */
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
        --ss-main-height: 44px;                   /* Plus grand pour touch */
        --ss-content-height: 240px;
    }

    .ss-content .ss-list .ss-option {
        padding: 12px var(--ss-spacing-l);        /* Touch-friendly */
        min-height: 44px;                         /* iOS minimum */
    }

    .ss-content .ss-search input {
        padding: 12px;
        font-size: 16px;                          /* Évite zoom iOS */
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
@endpush

@push('scripts')
<script>
function maintenanceOperationFormValidation() {
    return {
        // Initialisation des composants UI
        init() {
            console.log('[MaintenanceOperationForm] Initialisation démarrée...');

            // Initialiser SlimSelect et Flatpickr après un court délai pour s'assurer que le DOM est prêt
            setTimeout(() => {
                this.initSlimSelect();
                this.initFlatpickr();
            }, 100);

            console.log('[MaintenanceOperationForm] Initialisation complète avec succès');
        },

        // ✅ Initialiser SlimSelect pour les selects (STYLE ZENFLEET ASSIGNMENTS)
        initSlimSelect() {
            console.log('[MaintenanceOperationForm] Initialisation SlimSelect...');

            // SlimSelect pour véhicule
            const vehicleSelect = new SlimSelect({
                select: '#vehicle_id',
                settings: {
                    placeholderText: 'Sélectionnez un véhicule...',
                    searchText: 'Aucun résultat trouvé',
                    searchPlaceholder: 'Rechercher un véhicule...',
                    searchHighlight: true,
                },
                events: {
                    afterChange: (newVal) => {
                        const value = newVal[0]?.value || '';
                        @this.set('vehicle_id', value);

                        // Afficher section kilométrage si véhicule sélectionné
                        const mileageSection = document.getElementById('mileage-display-section');
                        if (value && mileageSection) {
                            mileageSection.style.display = 'block';
                        } else if (mileageSection) {
                            mileageSection.style.display = 'none';
                        }
                    }
                }
            });

            // SlimSelect pour type de maintenance
            const typeSelect = new SlimSelect({
                select: '#maintenance_type_id',
                settings: {
                    placeholderText: 'Sélectionnez un type de maintenance...',
                    searchText: 'Aucun résultat trouvé',
                    searchPlaceholder: 'Rechercher un type...',
                    searchHighlight: true,
                },
                events: {
                    afterChange: (newVal) => {
                        @this.set('maintenance_type_id', newVal[0]?.value || '');
                    }
                }
            });

            // SlimSelect pour fournisseur
            const providerSelect = new SlimSelect({
                select: '#provider_id',
                settings: {
                    placeholderText: 'Sélectionnez un fournisseur...',
                    searchText: 'Aucun résultat trouvé',
                    searchPlaceholder: 'Rechercher un fournisseur...',
                    searchHighlight: true,
                    allowDeselect: true,
                },
                events: {
                    afterChange: (newVal) => {
                        @this.set('provider_id', newVal[0]?.value || '');
                    }
                }
            });

            console.log('[MaintenanceOperationForm] SlimSelect initialisé avec succès');
        },

        // Initialiser Flatpickr pour les dates
        initFlatpickr() {
            console.log('[MaintenanceOperationForm] Initialisation Flatpickr...');

            // Flatpickr pour date planifiée
            flatpickr("#scheduled_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    @this.set('scheduled_date', dateStr);
                },
            });

            // Flatpickr pour date de completion
            flatpickr("#completed_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "fr",
                onChange: (selectedDates, dateStr) => {
                    @this.set('completed_date', dateStr);
                },
            });

            console.log('[MaintenanceOperationForm] Flatpickr initialisé avec succès');
        },
    };
}
</script>
@endpush
