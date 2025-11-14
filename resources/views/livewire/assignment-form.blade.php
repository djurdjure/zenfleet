{{-- ====================================================================
🎯 FORMULAIRE D'AFFECTATION V2 - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Design surpassant Fleetio, Samsara et Verizon Connect:
✨ Design épuré inspiré de la page show
✨ SlimSelect pour sélecteurs professionnels
✨ Kilométrage initial auto-chargé
✨ Toasts optimisés sans texte inutile
✨ Layout responsive et moderne
✨ Validation temps réel avec feedback visuel

@version 2.0-Enterprise-Grade
@since 2025-11-14
==================================================================== --}}

<div x-data="assignmentFormComponent()" class="bg-gray-50">
    {{-- ===============================================
    HEADER DU FORMULAIRE
    =============================================== --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2.5">
                    <x-iconify icon="lucide:clipboard-check" class="w-6 h-6 text-blue-600" />
                    {{ $isEditing ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isEditing ? 'Modifiez les informations de cette affectation véhicule ↔ chauffeur.' : 'Créez une nouvelle affectation pour assigner un véhicule à un chauffeur.' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ===============================================
    ALERTES DE VALIDATION
    =============================================== --}}
    @if($hasConflicts && !$forceCreate)
        <div class="mx-6 mt-6 rounded-lg bg-red-50 border border-red-200 p-4" role="alert" aria-live="polite" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600" />
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-red-900">
                        {{ count($conflicts) === 1 ? 'Conflit détecté' : count($conflicts) . ' conflits détectés' }}
                    </h3>
                    <div class="mt-2 text-sm text-red-800">
                        <ul class="list-disc space-y-1.5 pl-5">
                            @foreach($conflicts as $conflict)
                                <li>
                                    <strong class="font-medium">{{ $conflict['resource_label'] }}</strong>
                                    déjà affecté du {{ $conflict['period']['start'] }} au {{ $conflict['period']['end'] }}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 ml-1">
                                        {{ $conflict['status'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Suggestions de créneaux --}}
                    @if(count($suggestions) > 0)
                        <div class="mt-4 pt-4 border-t border-red-200">
                            <h4 class="text-sm font-medium text-red-900 mb-2">Créneaux disponibles :</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($suggestions as $index => $suggestion)
                                    <button
                                        type="button"
                                        wire:click="applySuggestion({{ $index }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 text-sm font-medium rounded-lg text-red-800 bg-white hover:bg-red-50 transition-colors">
                                        <x-iconify icon="lucide:calendar-check" class="w-4 h-4" />
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
                            <x-iconify icon="lucide:alert-triangle" class="w-4 h-4" />
                            Ignorer les conflits
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($forceCreate)
        <div class="mx-6 mt-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:shield-alert" class="w-5 h-5 text-yellow-600" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-yellow-900">Mode force activé</h3>
                    <p class="mt-1 text-sm text-yellow-800">Les conflits seront ignorés lors de la sauvegarde de cette affectation.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ===============================================
    FORMULAIRE PRINCIPAL
    =============================================== --}}
    <form wire:submit="save" class="p-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6 space-y-6">
                {{-- ===============================================
                SECTION : VÉHICULE ET CHAUFFEUR
                =============================================== --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:users" class="w-4 h-4 text-gray-500" />
                        Ressources à affecter
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Sélection Véhicule --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:car" class="w-4 h-4 text-gray-500" />
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
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror

                            {{-- Indicateur kilométrage actuel --}}
                            @if($current_vehicle_mileage)
                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                    <x-iconify icon="lucide:gauge" class="w-4 h-4 text-purple-600" />
                                    <span>Kilométrage actuel : <strong class="font-semibold text-gray-900">{{ number_format($current_vehicle_mileage) }} km</strong></span>
                                </div>
                            @endif
                        </div>

                        {{-- Sélection Chauffeur --}}
                        <div>
                            <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:user" class="w-4 h-4 text-gray-500" />
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
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200"></div>

                {{-- ===============================================
                SECTION : PÉRIODE D'AFFECTATION
                =============================================== --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:calendar-range" class="w-4 h-4 text-gray-500" />
                        Période d'affectation
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Date/heure de début --}}
                        <div>
                            <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:calendar-clock" class="w-4 h-4 text-gray-500" />
                                    Date et heure de remise
                                    <span class="text-red-500">*</span>
                                </div>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model.live="start_datetime"
                                id="start_datetime"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('start_datetime') border-red-300 @enderror"
                                required>
                            @error('start_datetime')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Date/heure de fin --}}
                        <div>
                            <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:calendar-x" class="w-4 h-4 text-gray-500" />
                                    Date et heure de restitution
                                    <span class="text-xs font-normal text-gray-500">(optionnel)</span>
                                </div>
                            </label>
                            <input
                                type="datetime-local"
                                wire:model.live="end_datetime"
                                id="end_datetime"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('end_datetime') border-red-300 @enderror">
                            @error('end_datetime')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror

                            {{-- Affichage durée --}}
                            @if($this->duration_hours !== null)
                                <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                                    <x-iconify icon="lucide:timer" class="w-4 h-4 text-blue-600" />
                                    <span>Durée : <strong class="font-semibold text-gray-900">{{ $this->formatted_duration }}</strong></span>
                                </div>
                            @elseif($start_datetime && !$end_datetime)
                                <div class="mt-2 flex items-center gap-2 text-sm text-blue-600">
                                    <x-iconify icon="lucide:infinity" class="w-4 h-4" />
                                    <span class="font-medium">Durée indéterminée</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action suggérer créneau --}}
                    @if($start_datetime)
                        <div class="mt-4">
                            <button
                                type="button"
                                wire:click="suggestNextSlot"
                                {{ empty($vehicle_id) || empty($driver_id) ? 'disabled' : '' }}
                                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                                <x-iconify icon="lucide:sparkles" class="w-4 h-4 text-blue-600" />
                                <span>Suggérer un créneau libre à partir du {{ \Carbon\Carbon::parse($start_datetime)->format('d/m/Y H:i') }}</span>
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200"></div>

                {{-- ===============================================
                SECTION : KILOMÉTRAGE ET DÉTAILS
                =============================================== --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-iconify icon="lucide:file-text" class="w-4 h-4 text-gray-500" />
                        Détails de l'affectation
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Kilométrage initial --}}
                        <div>
                            <label for="start_mileage" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:gauge" class="w-4 h-4 text-gray-500" />
                                    Kilométrage initial
                                </div>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model="start_mileage"
                                    id="start_mileage"
                                    min="0"
                                    step="1"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-12 @error('start_mileage') border-red-300 @enderror"
                                    placeholder="Ex: 125000">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 text-sm font-medium">km</span>
                                </div>
                            </div>
                            @error('start_mileage')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            @if($current_vehicle_mileage)
                                <p class="mt-1.5 text-xs text-gray-500">
                                    Le kilométrage actuel du véhicule est pré-rempli, vous pouvez le modifier si nécessaire.
                                </p>
                            @endif
                        </div>

                        {{-- Motif --}}
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="lucide:tag" class="w-4 h-4 text-gray-500" />
                                    Motif de l'affectation
                                </div>
                            </label>
                            <input
                                type="text"
                                wire:model="reason"
                                id="reason"
                                maxlength="500"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('reason') border-red-300 @enderror"
                                placeholder="Ex: Mission commerciale, formation, maintenance...">
                            @error('reason')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Notes complémentaires --}}
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="lucide:message-square-text" class="w-4 h-4 text-gray-500" />
                                Notes complémentaires
                            </div>
                        </label>
                        <textarea
                            wire:model="notes"
                            id="notes"
                            rows="4"
                            maxlength="1000"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('notes') border-red-300 @enderror"
                            placeholder="Informations supplémentaires, instructions particulières, remarques..."></textarea>
                        @error('notes')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500 text-right">{{ strlen($notes) }} / 1000 caractères</p>
                    </div>
                </div>

                {{-- ===============================================
                ERREURS GÉNÉRALES
                =============================================== --}}
                @if($errors->has('business_validation') || $errors->has('save'))
                    <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-900 mb-2">Erreur lors de la sauvegarde</h3>
                                <ul class="text-sm text-red-800 space-y-1">
                                    @foreach($errors->get('business_validation') as $error)
                                        <li class="flex items-start gap-2">
                                            <x-iconify icon="lucide:x" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                    @foreach($errors->get('save') as $error)
                                        <li class="flex items-start gap-2">
                                            <x-iconify icon="lucide:x" class="w-4 h-4 flex-shrink-0 mt-0.5" />
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ===============================================
            FOOTER: ACTIONS
            =============================================== --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3 rounded-b-lg">
                <button
                    type="button"
                    wire:click="$dispatch('close-form')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <x-iconify icon="lucide:x" class="w-4 h-4" />
                    <span>Annuler</span>
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium text-white transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed
                    {{ $hasConflicts && !$forceCreate ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }}">

                    <span wire:loading.remove wire:target="save">
                        @if($hasConflicts && !$forceCreate)
                            <x-iconify icon="lucide:shield-alert" class="w-5 h-5" />
                            Créer malgré les conflits
                        @else
                            <x-iconify icon="lucide:save" class="w-5 h-5" />
                            {{ $isEditing ? 'Enregistrer les modifications' : 'Créer l\'affectation' }}
                        @endif
                    </span>

                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Sauvegarde...</span>
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ====================================================================
SCRIPTS SLIMSELECT ET INTERACTIONS
==================================================================== --}}
@push('scripts')
<script>
function assignmentFormComponent() {
    return {
        init() {
            this.initSlimSelect();
            this.setupLivewireListeners();
        },

        initSlimSelect() {
            // SlimSelect est déjà chargé via CDN dans le layout
            if (typeof SlimSelect !== 'undefined') {
                // Véhicule select
                if (document.querySelector('.slimselect-vehicle')) {
                    new SlimSelect({
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
                    new SlimSelect({
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
                this.showToast('Mode force activé - Les conflits seront ignorés', 'warning');
            });

            Livewire.on('assignment-created', (event) => {
                this.showToast('Affectation créée avec succès', 'success');
            });

            Livewire.on('assignment-updated', (event) => {
                this.showToast('Affectation mise à jour avec succès', 'success');
            });
        },

        showToast(message, type = 'info') {
            const icons = {
                success: 'lucide:check-circle',
                error: 'lucide:x-circle',
                warning: 'lucide:alert-triangle',
                info: 'lucide:info'
            };

            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                info: 'bg-blue-600'
            };

            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white ${colors[type]} transform transition-all duration-300`;
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';

            toast.innerHTML = `
                <iconify-icon icon="${icons[type]}" class="text-2xl"></iconify-icon>
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
STYLES SLIMSELECT PERSONNALISÉS
==================================================================== --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slim-select@2/dist/slimselect.css">
<style>
/* Personnalisation SlimSelect pour ZenFleet */
.ss-main {
    @apply rounded-lg border-gray-300 shadow-sm;
}

.ss-main:focus-within {
    @apply border-blue-500 ring-1 ring-blue-500;
}

.ss-single {
    @apply px-3 py-2;
}

.ss-content {
    @apply rounded-lg border border-gray-200 shadow-lg mt-1;
}

.ss-search input {
    @apply px-3 py-2 text-sm;
}

.ss-option {
    @apply px-3 py-2 text-sm hover:bg-blue-50;
}

.ss-option.ss-highlighted {
    @apply bg-blue-600 text-white;
}

.ss-option.ss-disabled {
    @apply opacity-50 cursor-not-allowed;
}
</style>
@endpush
