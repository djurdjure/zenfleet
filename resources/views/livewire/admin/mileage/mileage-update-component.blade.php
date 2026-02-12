{{-- ====================================================================
    🚀 MILEAGE UPDATE PAGE - ENTERPRISE ULTRA-PRO V3.0
    ====================================================================
    🎯 AMÉLIORATIONS V3.0 (21/11/2025):
    - SlimSelect pour sélection de véhicule (style identique aux affectations)
    - Flatpickr via x-datepicker pour date (déjà présent, amélioré)
    - Champ heure natif (prérempli + modifiable)
    - CSS enterprise-grade cohérent avec le module affectations
    - Initialisation Livewire hooks robuste avec gestion d'erreurs
    - Support complet Livewire + wire:ignore pour éviter conflits
    ====================================================================

    Design System ZenFleet Compliant:
    - Structure section > container > cards
    - Icônes Heroicons
    - Composants <x-button>, <x-datepicker>, <x-input>
    - SlimSelect avec recherche pour la sélection véhicule
    - Couleurs et espacements standardisés
    - Responsive mobile/tablet/desktop

    @version 3.0-Enterprise-SlimSelect-Flatpickr
    @since 2025-11-21
    @author Expert Fullstack Senior (20+ ans)
    ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">
        
        {{-- ====================================================================
            EN-TÊTE DE LA PAGE - STYLE ZENFLEET
        ==================================================================== --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                        <x-iconify icon="heroicons:chart-bar" class="w-6 h-6 text-blue-600" />
                        Mise à Jour du Kilométrage
                    </h1>
                    <p class="text-sm text-gray-600 ml-8.5">
                        Enregistrez le nouveau kilométrage d'un véhicule de manière simple et rapide
                    </p>
                </div>
                <x-button 
                    href="{{ route('admin.mileage-readings.index') }}" 
                    variant="secondary" 
                    icon="list-bullet"
                    size="sm">
                    Voir l'historique
                </x-button>
            </div>
        </div>

        {{-- ====================================================================
            MESSAGES FLASH - STYLE ZENFLEET ALERT COMPONENT
        ==================================================================== --}}
        @if (session('success'))
            <x-alert type="success" title="Succès" dismissible class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Erreur" dismissible class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        {{-- ====================================================================
            LAYOUT PRINCIPAL - 2 COLONNES
        ==================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLONNE PRINCIPALE - FORMULAIRE --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    
                    {{-- En-tête de la carte - STYLE ZENFLEET --}}
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:pencil-square" class="w-5 h-5 text-blue-600" />
                            Enregistrer un Relevé Kilométrique
                        </h2>
                    </div>

                    {{-- Corps du formulaire --}}
                    <form wire:submit.prevent="save" class="p-6 space-y-6">
                        
                        {{-- 1. SÉLECTION DU VÉHICULE - SLIMSELECT ENTERPRISE --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="heroicons:truck" class="w-4 h-4 text-gray-500" />
                                    Véhicule
                                    <span class="text-red-500">*</span>
                                </div>
                            </label>
                            {{-- wire:ignore car SlimSelect gère le DOM, pas de wire:model pour éviter conflit --}}
                            <div wire:ignore id="vehicle-select-wrapper">
                                <select
                                    id="vehicle_id"
                                    name="vehicle_id"
                                    class="slimselect-vehicle w-full"
                                    required>
                                    {{-- Option placeholder avec data-placeholder pour SlimSelect --}}
                                    <option data-placeholder="true" value=""></option>
                                    @foreach($availableVehicles as $vehicle)
                                        <option
                                            value="{{ $vehicle->id }}"
                                            data-mileage="{{ $vehicle->current_mileage ?? 0 }}"
                                            data-registration="{{ $vehicle->registration_plate }}"
                                            data-brand="{{ $vehicle->brand }}"
                                            data-model="{{ $vehicle->model }}"
                                            @selected($vehicle_id == $vehicle->id)>
                                            {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km)
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
                        </div>

                        {{-- INFORMATIONS DU VÉHICULE SÉLECTIONNÉ --}}
                        @if($vehicleData)
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="heroicons:truck" class="w-6 h-6 text-blue-600" />
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-blue-900">
                                                {{ $vehicleData['registration_plate'] }}
                                            </span>
                                            <span class="text-sm text-blue-700">
                                                {{ $vehicleData['brand'] }} {{ $vehicleData['model'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            @if($vehicleData['category_name'])
                                                <div>
                                                    <span class="text-blue-600 font-medium">Catégorie:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['category_name'] }}</span>
                                                </div>
                                            @endif
                                            @if($vehicleData['fuel_type'])
                                                <div>
                                                    <span class="text-blue-600 font-medium">Carburant:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['fuel_type'] }}</span>
                                                </div>
                                            @endif
                                            @if($vehicleData['depot_name'])
                                                <div class="col-span-2">
                                                    <span class="text-blue-600 font-medium">Dépôt:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['depot_name'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="pt-2 border-t border-blue-200">
                                            <div class="flex items-center gap-2">
                                                <x-iconify icon="heroicons:chart-bar" class="w-5 h-5 text-blue-600" />
                                                <span class="text-sm text-blue-700 font-medium">Kilométrage actuel:</span>
                                                <span class="text-lg font-bold text-blue-900">
                                                    {{ number_format($vehicleData['current_mileage'], 0, ',', ' ') }} km
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 2. DATE ET HEURE DE LA LECTURE --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Date - FLATPICKR ENTERPRISE (déjà correct) --}}
                            <div>
                                <x-datepicker
                                    name="date"
                                    wire:model.live="date"
                                    :value="$date"
                                    label="Date de la lecture"
                                    :maxDate="date('Y-m-d')"
                                    :minDate="date('Y-m-d', strtotime('-30 days'))"
                                    :error="$errors->first('date')"
                                    placeholder="Sélectionner la date"
                                    format="d/m/Y"
                                    required
                                />
                            </div>
                            {{-- Heure - Champ natif prérempli et modifiable --}}
                            <div>
                                <label for="time" class="block text-sm font-medium text-gray-700 mb-2">
                                    <div class="flex items-center gap-2">
                                        <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-500" />
                                        Heure de la lecture
                                        <span class="text-red-500">*</span>
                                    </div>
                                </label>
                                <input
                                    type="time"
                                    id="time"
                                    name="time"
                                    wire:model.live="time"
                                    step="60"
                                    required
                                    class="block w-full px-3 py-2.5 bg-white border rounded-lg shadow-sm text-sm text-gray-900 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 {{ $errors->has('time') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
                                />
                                @error('time')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1.5 text-xs text-gray-500">Préchargée à l'heure actuelle, modifiable.</p>
                            </div>
                        </div>

                        {{-- 3. NOUVEAU KILOMÉTRAGE --}}
                        <div>
                            <x-input
                                type="number"
                                name="mileage"
                                wire:model.live="mileage"
                                label="Nouveau kilométrage (km)"
                                placeholder="Ex: 125000"
                                :error="$errors->first('mileage')"
                                icon="chart-bar"
                                required
                                min="0"
                                step="1"
                            />
                            
                            {{-- Message de validation en temps réel --}}
                            @if($validationMessage && $vehicleData)
                                <div class="mt-2 p-3 rounded-lg {{ 
                                    $validationType === 'success' ? 'bg-green-50 border border-green-200' : 
                                    ($validationType === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 
                                    'bg-red-50 border border-red-200')
                                }}">
                                    <p class="text-sm font-medium {{ 
                                        $validationType === 'success' ? 'text-green-800' : 
                                        ($validationType === 'warning' ? 'text-yellow-800' : 
                                        'text-red-800')
                                    }}">
                                        {{ $validationMessage }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- 4. NOTES OPTIONNELLES --}}
                        <div>
                            <x-textarea
                                name="notes"
                                wire:model="notes"
                                label="Notes (optionnel)"
                                placeholder="Commentaires ou observations particulières..."
                                :error="$errors->first('notes')"
                                rows="3"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Maximum 500 caractères
                            </p>
                        </div>

                        {{-- BOUTONS D'ACTION - STYLE ZENFLEET --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <x-button
                                type="button"
                                wire:click="resetForm"
                                variant="secondary"
                                icon="arrow-path"
                                size="md">
                                Réinitialiser
                            </x-button>

                            <x-button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                variant="primary"
                                icon="check"
                                size="md">
                                <span wire:loading.remove wire:target="save">Enregistrer la Lecture</span>
                                <span wire:loading wire:target="save">Enregistrement...</span>
                            </x-button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- COLONNE LATÉRALE - INFORMATIONS --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- STATISTIQUES DU VÉHICULE --}}
                @if($vehicleData && $vehicleStats)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:chart-bar" class="w-4 h-4 text-gray-600" />
                                Statistiques
                            </h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Moyenne journalière</span>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($vehicleStats['daily_average'], 0, ',', ' ') }} km/jour
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Moyenne mensuelle</span>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($vehicleStats['monthly_average'], 0, ',', ' ') }} km/mois
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Ce mois-ci</span>
                                <span class="text-sm font-bold text-blue-600">
                                    {{ number_format($vehicleStats['km_this_month'], 0, ',', ' ') }} km
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600">Dernier relevé</span>
                                <span class="text-xs text-gray-500">
                                    {{ $vehicleStats['last_reading_date'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- HISTORIQUE RÉCENT --}}
                @if($vehicleData && $recentReadings->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-600" />
                                Historique Récent
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($recentReadings as $reading)
                                <div class="p-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ number_format($reading->mileage, 0, ',', ' ') }} km
                                                </span>
                                                @if($reading->recording_method === 'manual')
                                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">
                                                        Manuel
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">
                                                        Auto
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $reading->recorded_at->format('d/m/Y à H:i') }}
                                            </p>
                                            @if($reading->recordedBy)
                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    Par {{ $reading->recordedBy->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($reading->notes)
                                        <p class="mt-2 text-xs text-gray-600 italic">
                                            "{{ Str::limit($reading->notes, 50) }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- AIDE / INSTRUCTIONS --}}
                <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 overflow-hidden">
                    <div class="px-4 py-3 bg-blue-100 border-b border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:information-circle" class="w-4 h-4" />
                            Instructions
                        </h3>
                    </div>
                    <div class="p-4 space-y-2 text-sm text-blue-900">
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>Le kilométrage doit être <strong>supérieur</strong> au dernier relevé</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>La date ne peut pas dépasser <strong>30 jours dans le passé</strong></p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>Les notes sont optionnelles mais recommandées pour les relevés inhabituels</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:exclamation-triangle" class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0" />
                            <p>Une augmentation de <strong>+10 000 km</strong> déclenchera un avertissement</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

{{-- ====================================================================
 💎 ALPINE.JS + SLIMSELECT - ENTERPRISE GRADE INITIALIZATION
 ==================================================================== --}}
@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    // Initialiser SlimSelect après le chargement de Livewire
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => {
            // Réinitialiser SlimSelect après chaque mise à jour Livewire
            initMileageSlimSelects();
        });
    });

    // Initialiser au chargement de la page
    initMileageSlimSelects();
});

function initMileageSlimSelects() {
    // Vérifier que SlimSelect est chargé
    if (typeof SlimSelect === 'undefined') {
        console.error('❌ SlimSelect library not loaded');
        return;
    }

    // 🚗 Véhicule select
    const vehicleEl = document.getElementById('vehicle_id');
    if (vehicleEl && !vehicleEl.slim) {
        try {
            const vehicleSlimSelect = new SlimSelect({
                select: vehicleEl,
                settings: {
                    showSearch: true,
                    searchHighlight: true,
                    closeOnSelect: true,
                    allowDeselect: true,
                    placeholderText: 'Sélectionnez un véhicule',
                    searchPlaceholder: 'Rechercher un véhicule...',
                    searchText: 'Aucun véhicule trouvé',
                    searchingText: 'Recherche en cours...',
                },
                events: {
                    afterChange: (newVal) => {
                        const value = newVal[0]?.value || '';
                        console.log('🚗 Véhicule sélectionné:', value);

                        // Mettre à jour Livewire
                        @this.set('vehicle_id', value);

                        // Retirer l'état d'erreur
                        if (value) {
                            document.getElementById('vehicle-select-wrapper')?.classList.remove('slimselect-error');
                        }
                    }
                }
            });
            vehicleEl.slim = vehicleSlimSelect; // Stocker pour éviter réinitialisation
            console.log('✅ Véhicule SlimSelect initialisé');
        } catch (error) {
            console.error('❌ Erreur init véhicule SlimSelect:', error);
        }
    }

}
</script>
@endpush

{{-- ====================================================================
STYLES SLIMSELECT PERSONNALISÉS - ZENFLEET ENTERPRISE GRADE
Utilisation des variables CSS natives SlimSelect (--ss-*)
Note: Le CSS SlimSelect de base est déjà chargé via CDN dans layouts/admin/catalyst.blade.php
==================================================================== --}}
@push('styles')
<style>
/**
 * 🎨 ZENFLEET SLIMSELECT - Variables CSS Natives
 * Cohérence visuelle avec Tailwind sans surcharge @apply
 * Basé sur la palette ZenFleet et les standards du formulaire
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

    /* Dimensions cohérentes avec x-input et x-datepicker */
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

.ss-content .ss-list .ss-option.ss-highlighted,
.ss-content .ss-list .ss-option:not(.ss-disabled).ss-selected {
    background-color: #2563eb;                    /* blue-600 */
    color: #ffffff;                               /* blanc */
    font-weight: 600;
}

/* Option désactivée */
.ss-content .ss-list .ss-option.ss-disabled {
    background-color: #f9fafb;                    /* gray-50 */
    color: #d1d5db;                               /* gray-300 */
    cursor: not-allowed;
    opacity: 0.6;
}

/* Flèche du select */
.ss-main .ss-arrow {
    transition: transform var(--ss-animation-timing) ease;
}

.ss-main.ss-open-below .ss-arrow,
.ss-main.ss-open-above .ss-arrow {
    transform: rotate(180deg);
}

/* 🔴 STATE ERREUR - Affichage cohérent avec x-input et x-datepicker */
.slimselect-error .ss-main {
    border-color: var(--ss-error-color) !important;
    background-color: #fef2f2 !important;        /* red-50 */
    box-shadow:
        0 0 0 3px rgba(220, 38, 38, 0.1),        /* ring-red-600/10 */
        0 1px 2px 0 rgba(0, 0, 0, 0.05);         /* shadow-sm */
}

.slimselect-error .ss-main .ss-values .ss-placeholder {
    color: #dc2626;                              /* red-600 */
}

/* 📱 RESPONSIVE - Adaptation mobile */
@media (max-width: 640px) {
    .ss-content {
        max-height: 250px;                       /* Réduire hauteur sur mobile */
    }

    .ss-content .ss-list .ss-option {
        padding: 12px var(--ss-spacing-l);       /* Plus d'espacement tactile */
    }
}

/* ⚡ PERFORMANCE - Will-change pour animations fluides */
.ss-main,
.ss-content,
.ss-content .ss-list .ss-option {
    will-change: transform, opacity;
}

/* 🎯 ACCESSIBILITÉ - Focus visible pour navigation au clavier */
.ss-main:focus-visible {
    outline: 2px solid var(--ss-primary-color);
    outline-offset: 2px;
}

.ss-content .ss-list .ss-option:focus-visible {
    outline: 2px solid var(--ss-primary-color);
    outline-offset: -2px;
}
</style>
@endpush
