{{-- ====================================================================
🎯 FORMULAIRE D'AFFECTATION V3 - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Design surpassant Fleetio, Samsara et Verizon Connect:
✨ Composants Blade uniformes (x-input, x-select, x-datepicker)
✨ Icônes Iconify cohérentes avec le reste de l'application
✨ Single-page (pas de multi-steps)
✨ Gestion d'erreurs enterprise-grade
✨ Validation temps réel avec feedback visuel
✨ Layout responsive et moderne
✨ Kilométrage initial auto-chargé
✨ Suggestions de créneaux intelligentes

@version 3.0-Enterprise-Grade
@since 2025-11-15
==================================================================== --}}

<div x-data="assignmentFormValidation()" class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- ===============================================
        HEADER AVEC GRADIENT ET ICÔNE
        =============================================== --}}
        <div class="mb-6">
            <div class="flex items-start gap-4">
                {{-- Icône avec gradient box --}}
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-iconify icon="heroicons:clipboard-document-check" class="w-8 h-8 text-white" />
                    </div>
                </div>

                {{-- Titre et description --}}
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                        {{ $isEditing ? 'Modifier l\'Affectation' : 'Nouvelle Affectation' }}
                    </h1>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $isEditing
                            ? 'Modifiez les informations de cette affectation véhicule ↔ chauffeur.'
                            : 'Assignez un véhicule à un chauffeur pour une période donnée. Les conflits seront détectés automatiquement.'
                        }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ===============================================
        ALERTES GLOBALES DE VALIDATION
        =============================================== --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes avant de continuer :
                <ul class="mt-2 ml-5 list-disc text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- Alerte de conflits détectés --}}
        @if($hasConflicts && !$forceCreate)
            <x-alert type="error" title="{{ count($conflicts) === 1 ? 'Conflit détecté' : count($conflicts) . ' conflits détectés' }}" class="mb-6">
                <ul class="mt-2 space-y-2 text-sm">
                    @foreach($conflicts as $conflict)
                        <li class="flex items-start gap-2">
                            <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                            <span>
                                <strong class="font-medium">{{ $conflict['resource_label'] }}</strong>
                                déjà affecté du {{ $conflict['period']['start'] }} au {{ $conflict['period']['end'] }}
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">
                                    {{ $conflict['status'] }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ul>

                {{-- Suggestions de créneaux --}}
                @if(count($suggestions) > 0)
                    <div class="mt-4 pt-4 border-t border-red-200">
                        <h4 class="text-sm font-medium text-red-900 mb-2">Créneaux disponibles suggérés :</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($suggestions as $index => $suggestion)
                                <button
                                    type="button"
                                    wire:click="applySuggestion({{ $index }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 text-sm font-medium rounded-lg text-red-800 bg-white hover:bg-red-50 transition-colors">
                                    <x-iconify icon="heroicons:calendar-days" class="w-4 h-4" />
                                    {{ $suggestion['description'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Bouton forcer --}}
                <div class="mt-4">
                    <button
                        type="button"
                        wire:click="toggleForceCreate"
                        class="inline-flex items-center gap-2 px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-white hover:bg-red-50 transition-colors shadow-sm">
                        <x-iconify icon="heroicons:shield-exclamation" class="w-4 h-4" />
                        Ignorer les conflits et continuer
                    </button>
                </div>
            </x-alert>
        @endif

        {{-- Alerte mode force activé --}}
        @if($forceCreate)
            <x-alert type="warning" title="Mode force activé" class="mb-6">
                <p class="text-sm">Les conflits seront ignorés lors de la sauvegarde de cette affectation.</p>
                <button
                    type="button"
                    wire:click="toggleForceCreate"
                    class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-yellow-700 hover:text-yellow-800">
                    <x-iconify icon="heroicons:x-mark" class="w-4 h-4" />
                    Désactiver le mode force
                </button>
            </x-alert>
        @endif

        {{-- ===============================================
        FORMULAIRE PRINCIPAL
        =============================================== --}}
        <form wire:submit="save" class="space-y-6">
            {{-- ===============================================
            SECTION 1: RESSOURCES À AFFECTER
            =============================================== --}}
            <x-card>
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                            <x-iconify icon="heroicons:users" class="w-5 h-5 text-blue-600" />
                            Ressources à Affecter
                        </h2>
                        <p class="text-sm text-gray-600">Sélectionnez le véhicule et le chauffeur pour cette affectation.</p>
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
                            <div wire:ignore>
                                <select
                                    wire:model="vehicle_id"
                                    id="vehicle_id"
                                    class="slimselect-vehicle w-full"
                                    required>
                                    <option value="">Sélectionnez un véhicule</option>
                                    @foreach($vehicleOptions as $vehicle)
                                        <option value="{{ $vehicle->id }}" @selected($vehicle_id == $vehicle->id)>
                                            {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vehicle_id')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">Sélectionnez le véhicule à affecter</p>

                            {{-- Indicateur kilométrage actuel --}}
                            @if($current_vehicle_mileage)
                                <div class="mt-3 flex items-start gap-2.5 p-3 bg-purple-50 border border-purple-100 rounded-lg">
                                    <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" />
                                    <div class="text-sm">
                                        <p class="font-medium text-purple-900">Kilométrage actuel</p>
                                        <p class="text-purple-700 mt-0.5">
                                            <strong class="font-semibold">{{ number_format($current_vehicle_mileage) }} km</strong>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Sélection Chauffeur --}}
                        <div>
                            <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="heroicons:user" class="w-4 h-4 text-gray-500" />
                                    Chauffeur
                                    <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <div wire:ignore>
                                <select
                                    wire:model="driver_id"
                                    id="driver_id"
                                    class="slimselect-driver w-full"
                                    required>
                                    <option value="">Sélectionnez un chauffeur</option>
                                    @foreach($driverOptions as $driver)
                                        <option value="{{ $driver->id }}" @selected($driver_id == $driver->id)>
                                            {{ $driver->first_name }} {{ $driver->last_name }}
                                            @if($driver->license_number)
                                                ({{ $driver->license_number }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('driver_id')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1.5 text-xs text-gray-500">Sélectionnez le chauffeur assigné</p>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- ===============================================
            SECTION 2: PÉRIODE D'AFFECTATION
            =============================================== --}}
            <x-card>
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                            <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-blue-600" />
                            Période d'Affectation
                        </h2>
                        <p class="text-sm text-gray-600">Définissez la période de remise et de restitution du véhicule.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Date/heure de début --}}
                        <div>
                            <x-datepicker
                                name="start_datetime"
                                wire:model.live="start_datetime"
                                label="Date et heure de remise"
                                icon="calendar-days"
                                type="datetime-local"
                                required
                                :value="$start_datetime"
                                :error="$errors->first('start_datetime')"
                                helpText="Quand le chauffeur récupère le véhicule"
                            />
                        </div>

                        {{-- Date/heure de fin --}}
                        <div>
                            <x-datepicker
                                name="end_datetime"
                                wire:model.live="end_datetime"
                                label="Date et heure de restitution"
                                icon="calendar-days"
                                type="datetime-local"
                                :value="$end_datetime"
                                :error="$errors->first('end_datetime')"
                                helpText="Laisser vide pour une durée indéterminée"
                            />

                            {{-- Affichage durée calculée --}}
                            @if($this->duration_hours !== null)
                                <div class="mt-3 flex items-center gap-2 text-sm text-gray-600 bg-blue-50 border border-blue-100 rounded-lg p-3">
                                    <x-iconify icon="heroicons:clock" class="w-5 h-5 text-blue-600" />
                                    <span>Durée : <strong class="font-semibold text-gray-900">{{ $this->formatted_duration }}</strong></span>
                                </div>
                            @elseif($start_datetime && !$end_datetime)
                                <div class="mt-3 flex items-center gap-2 text-sm text-blue-600 bg-blue-50 border border-blue-100 rounded-lg p-3">
                                    <x-iconify icon="heroicons:arrow-path" class="w-5 h-5" />
                                    <span class="font-medium">Durée indéterminée</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton suggérer créneau --}}
                    @if($start_datetime && $vehicle_id && $driver_id)
                        <div class="pt-4 border-t border-gray-200">
                            <button
                                type="button"
                                wire:click="suggestNextSlot"
                                class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                                <x-iconify icon="heroicons:sparkles" class="w-5 h-5 text-yellow-500" />
                                <span>Suggérer un créneau libre</span>
                            </button>
                            <p class="mt-2 text-xs text-gray-500">Recherche automatique du prochain créneau disponible pour ces ressources</p>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- ===============================================
            SECTION 3: DÉTAILS DE L'AFFECTATION
            =============================================== --}}
            <x-card>
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">
                            <x-iconify icon="heroicons:document-text" class="w-5 h-5 text-blue-600" />
                            Détails de l'Affectation
                        </h2>
                        <p class="text-sm text-gray-600">Informations complémentaires et suivi du kilométrage.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kilométrage initial --}}
                        <div>
                            <x-input
                                name="start_mileage"
                                wire:model="start_mileage"
                                type="number"
                                label="Kilométrage initial"
                                icon="gauge"
                                placeholder="Ex: 125000"
                                :value="$start_mileage"
                                :error="$errors->first('start_mileage')"
                                helpText="Kilométrage au moment de la remise du véhicule"
                                min="0"
                                step="1"
                            >
                                <x-slot name="suffix">
                                    <span class="text-gray-500 text-sm font-medium">km</span>
                                </x-slot>
                            </x-input>
                            @if($current_vehicle_mileage && $start_mileage)
                                <p class="mt-2 text-xs text-gray-500">
                                    💡 Le kilométrage actuel du véhicule ({{ number_format($current_vehicle_mileage) }} km) a été pré-rempli automatiquement.
                                </p>
                            @endif
                        </div>

                        {{-- Motif --}}
                        <div>
                            <x-input
                                name="reason"
                                wire:model="reason"
                                label="Motif de l'affectation"
                                icon="tag"
                                placeholder="Ex: Mission commerciale, formation..."
                                :value="$reason"
                                :error="$errors->first('reason')"
                                helpText="Raison de cette affectation"
                                maxlength="500"
                            />
                        </div>
                    </div>

                    {{-- Notes complémentaires --}}
                    <div>
                        <x-textarea
                            name="notes"
                            wire:model="notes"
                            label="Notes complémentaires"
                            rows="4"
                            placeholder="Informations supplémentaires, instructions particulières, remarques..."
                            :value="$notes"
                            :error="$errors->first('notes')"
                            helpText="Informations additionnelles (optionnel)"
                            maxlength="1000"
                        />
                        <p class="mt-1.5 text-xs text-gray-500 text-right">{{ strlen($notes ?? '') }} / 1000 caractères</p>
                    </div>
                </div>
            </x-card>

            {{-- ===============================================
            FOOTER: ACTIONS DU FORMULAIRE
            =============================================== --}}
            <div class="flex items-center justify-end gap-3 pt-4">
                <a
                    href="{{ route('admin.assignments.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                    <span>Annuler</span>
                </a>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-medium text-white transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed
                    {{ $hasConflicts && !$forceCreate ? 'bg-red-600 hover:bg-red-700 shadow-red-500/20' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20' }}">

                    <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                        @if($hasConflicts && !$forceCreate)
                            <x-iconify icon="heroicons:shield-exclamation" class="w-5 h-5" />
                            Créer malgré les conflits
                        @else
                            <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
                            {{ $isEditing ? 'Enregistrer les modifications' : 'Créer l\'affectation' }}
                        @endif
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
</div>

{{-- ====================================================================
SCRIPTS SLIMSELECT ET ALPINE.JS
==================================================================== --}}
@push('scripts')
<script>
function assignmentFormValidation() {
    return {
        vehicleSlimSelect: null,
        driverSlimSelect: null,

        init() {
            this.initSlimSelect();
            this.setupLivewireListeners();
        },

        initSlimSelect() {
            // SlimSelect est chargé via CDN dans le layout
            if (typeof SlimSelect !== 'undefined') {
                // Véhicule select
                if (document.querySelector('.slimselect-vehicle')) {
                    this.vehicleSlimSelect = new SlimSelect({
                        select: '.slimselect-vehicle',
                        settings: {
                            searchPlaceholder: 'Rechercher un véhicule...',
                            searchText: 'Aucun véhicule trouvé',
                            searchingText: 'Recherche...',
                            placeholderText: 'Sélectionnez un véhicule',
                        },
                        events: {
                            afterChange: (newVal) => {
                                @this.set('vehicle_id', newVal[0]?.value || '');
                            }
                        }
                    });
                }

                // Chauffeur select
                if (document.querySelector('.slimselect-driver')) {
                    this.driverSlimSelect = new SlimSelect({
                        select: '.slimselect-driver',
                        settings: {
                            searchPlaceholder: 'Rechercher un chauffeur...',
                            searchText: 'Aucun chauffeur trouvé',
                            searchingText: 'Recherche...',
                            placeholderText: 'Sélectionnez un chauffeur',
                        },
                        events: {
                            afterChange: (newVal) => {
                                @this.set('driver_id', newVal[0]?.value || '');
                            }
                        }
                    });
                }
            } else {
                console.error('SlimSelect library not loaded. Please check the CDN link in layout.');
            }
        },

        setupLivewireListeners() {
            // Toast pour suggestions
            Livewire.on('suggestion-applied', (event) => {
                this.showToast('Créneau appliqué avec succès', 'success');
            });

            Livewire.on('slot-suggested', (event) => {
                this.showToast('Créneau libre trouvé et appliqué', 'info');
            });

            Livewire.on('force-mode-enabled', (event) => {
                this.showToast('Mode force activé', 'warning');
            });

            Livewire.on('force-mode-disabled', (event) => {
                this.showToast('Mode force désactivé', 'info');
            });

            Livewire.on('assignment-created', (event) => {
                this.showToast('✓ Affectation créée avec succès', 'success');
                // Redirection après 1.5 secondes
                setTimeout(() => {
                    window.location.href = '{{ route("admin.assignments.index") }}';
                }, 1500);
            });

            Livewire.on('assignment-updated', (event) => {
                this.showToast('✓ Affectation mise à jour', 'success');
            });

            Livewire.on('conflicts-detected', (event) => {
                // Animation d'alerte visuelle
                const alertBox = document.querySelector('[role="alert"]');
                if (alertBox) {
                    alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            Livewire.on('conflicts-cleared', (event) => {
                this.showToast('✓ Aucun conflit détecté', 'success');
            });
        },

        showToast(message, type = 'info') {
            const icons = {
                success: 'heroicons:check-circle',
                error: 'heroicons:x-circle',
                warning: 'heroicons:exclamation-triangle',
                info: 'heroicons:information-circle'
            };

            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                info: 'bg-blue-600'
            };

            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 flex items-center gap-3 px-5 py-3 rounded-lg shadow-xl text-white ${colors[type]} transform transition-all duration-300`;
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';

            toast.innerHTML = `
                <iconify-icon icon="${icons[type]}" class="text-xl flex-shrink-0"></iconify-icon>
                <span class="font-medium">${message}</span>
            `;

            document.body.appendChild(toast);

            // Animation d'entrée
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            });

            // Suppression après 4 secondes
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    };
}
</script>
@endpush

{{-- ====================================================================
STYLES SLIMSELECT PERSONNALISÉS - ZENFLEET ENTERPRISE
==================================================================== --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.css">
<style>
/* 🎨 Personnalisation SlimSelect pour ZenFleet Enterprise */

/* Container principal */
.ss-main {
    @apply rounded-lg border-gray-300 shadow-sm;
}

.ss-main:focus-within {
    @apply border-blue-500 ring-1 ring-blue-500;
}

/* Champ de sélection unique */
.ss-single {
    @apply px-3 py-2.5 bg-white;
}

/* Dropdown content */
.ss-content {
    @apply rounded-lg border border-gray-200 shadow-lg mt-1 bg-white;
    max-height: 300px !important;
}

/* Champ de recherche */
.ss-search input {
    @apply px-3 py-2.5 text-sm border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0;
}

/* Options */
.ss-option {
    @apply px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer transition-colors;
}

.ss-option.ss-highlighted {
    @apply bg-blue-600 text-white;
}

.ss-option.ss-disabled {
    @apply opacity-50 cursor-not-allowed bg-gray-50;
}

.ss-option:not(.ss-disabled):hover {
    @apply bg-blue-50;
}

/* Textes de recherche */
.ss-search::placeholder,
.ss-disabled,
.ss-list .ss-option.ss-disabled {
    @apply text-gray-400;
}

/* Flèche dropdown */
.ss-arrow {
    @apply text-gray-400;
}

.ss-main.ss-open-above .ss-arrow,
.ss-main.ss-open-below .ss-arrow {
    @apply text-blue-600;
}

/* État d'erreur (pour Livewire validation) */
.slimselect-vehicle.error .ss-main,
.slimselect-driver.error .ss-main {
    @apply border-red-300 ring-1 ring-red-300;
}

/* Loading state */
.ss-searching {
    @apply text-blue-600 text-sm px-3 py-2;
}

/* No results */
.ss-search-noresults {
    @apply text-gray-500 text-sm px-3 py-2 italic;
}

/* Multiple selects (si besoin futur) */
.ss-values .ss-value {
    @apply bg-blue-100 text-blue-800 rounded px-2 py-1 text-sm;
}

.ss-values .ss-value .ss-value-delete {
    @apply text-blue-600 hover:text-blue-800;
}

/* Animation smooth */
.ss-content {
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 640px) {
    .ss-content {
        max-height: 250px !important;
    }
}
</style>
@endpush
