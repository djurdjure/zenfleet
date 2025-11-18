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
            SECTION 1: RESSOURCES À AFFECTER (ENTERPRISE V3 - FOND BLEU)
            =============================================== --}}
            <x-card class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200">
                <div class="space-y-6">
                    <div class="pb-4 border-b border-blue-200">
                        <h2 class="text-lg font-semibold text-blue-900 mb-1 flex items-center gap-2">
                            <x-iconify icon="heroicons:users" class="w-5 h-5 text-blue-600" />
                            Ressources à Affecter
                        </h2>
                        <p class="text-sm text-blue-700">Sélectionnez le véhicule et le chauffeur pour cette affectation.</p>
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
                            {{-- wire:ignore car SlimSelect gère le DOM, pas de wire:model pour éviter conflit --}}
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
                                            @selected($vehicle_id == $vehicle->id)>
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

                            {{-- 🆕 ENTERPRISE V3: Indicateur kilométrage actuel ÉDITABLE (affiché dès la sélection) --}}
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

                                {{-- Champ de mise à jour du kilométrage --}}
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="number"
                                            wire:model.live="start_mileage"
                                            id="start_mileage_input"
                                            class="flex-1 px-3 py-2 text-sm border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Entrer le nouveau kilométrage"
                                            min="{{ $current_vehicle_mileage ?? 0 }}">
                                        <span class="text-sm font-medium text-gray-600">km</span>
                                    </div>

                                    {{-- Checkbox pour mettre à jour le véhicule --}}
                                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="updateVehicleMileage"
                                            class="w-4 h-4 text-blue-600 border-blue-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-700">
                                            Mettre à jour le kilométrage du véhicule et créer une entrée dans l'historique
                                        </span>
                                    </label>

                                    {{-- Indicateur de modification --}}
                                    @if($mileageModified && $start_mileage > $current_vehicle_mileage)
                                        <div class="flex items-center gap-1.5 text-xs text-green-700 bg-green-50 px-2 py-1 rounded">
                                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4" />
                                            <span>Nouveau kilométrage: {{ number_format($start_mileage) }} km (+{{ number_format($start_mileage - $current_vehicle_mileage) }} km)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
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
                            {{-- wire:ignore car SlimSelect gère le DOM, pas de wire:model pour éviter conflit --}}
                            <div wire:ignore id="driver-select-wrapper">
                                <select
                                    id="driver_id"
                                    name="driver_id"
                                    class="slimselect-driver w-full"
                                    required>
                                    {{-- Option placeholder avec data-placeholder pour SlimSelect --}}
                                    <option data-placeholder="true" value=""></option>
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
            SECTION 2: PÉRIODE D'AFFECTATION (ENTERPRISE V3 - DATE/HEURE SÉPARÉES)
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
                        {{-- DÉBUT : Date + Heure --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:play" class="w-4 h-4 text-green-600" />
                                Début d'affectation
                            </h3>

                            {{-- Date + Heure de début (côte à côte) --}}
                            <div class="flex items-start gap-3">
                                {{-- Date de début (largeur réduite) --}}
                                <div class="flex-1 min-w-0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de remise *</label>
                                    <x-datepicker
                                        name="start_date"
                                        wire:model.live="start_date"
                                        :value="$start_date"
                                        :error="$errors->first('start_date')"
                                        placeholder="Choisir une date"
                                        format="d/m/Y"
                                        required
                                    />
                                </div>

                                {{-- Heure de début (petite largeur) --}}
                                <div class="w-32 flex-shrink-0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure *</label>
                                    <div wire:ignore id="start-time-wrapper">
                                        <select
                                            id="start_time"
                                            name="start_time"
                                            class="slimselect-time-start w-full"
                                            required>
                                            <option data-placeholder="true" value=""></option>
                                            @foreach($this->timeOptions as $time)
                                                <option value="{{ $time['value'] }}" @selected($start_time == $time['value'])>
                                                    {{ $time['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- FIN : Date + Heure (optionnel) --}}
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:stop" class="w-4 h-4 text-red-600" />
                                Fin d'affectation (optionnel)
                            </h3>

                            {{-- Date + Heure de fin (côte à côte) --}}
                            <div class="flex items-start gap-3">
                                {{-- Date de fin (largeur réduite) --}}
                                <div class="flex-1 min-w-0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de restitution</label>
                                    <x-datepicker
                                        name="end_date"
                                        wire:model.live="end_date"
                                        :value="$end_date"
                                        :error="$errors->first('end_date')"
                                        placeholder="Laisser vide si indéterminée"
                                        format="d/m/Y"
                                        :minDate="$start_date"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">Laisser vide pour une durée indéterminée</p>
                                </div>

                                {{-- Heure de fin (petite largeur) - Affiché seulement si date de fin --}}
                                <div class="w-32 flex-shrink-0">
                                    @if($end_date)
                                        <div wire:ignore id="end-time-wrapper">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Heure</label>
                                            <select
                                                id="end_time"
                                                name="end_time"
                                                class="slimselect-time-end w-full">
                                                <option data-placeholder="true" value=""></option>
                                                @foreach($this->timeOptions as $time)
                                                    <option value="{{ $time['value'] }}" @selected($end_time == $time['value'])>
                                                        {{ $time['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('end_time')
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @else
                                        {{-- Placeholder vide pour maintenir l'alignement --}}
                                        <div class="h-[42px] mb-2">&nbsp;</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Affichage durée calculée --}}
                            @if($this->duration_hours !== null)
                                <div class="flex items-center gap-2 text-sm text-gray-600 bg-blue-50 border border-blue-100 rounded-lg p-3 mt-3">
                                    <x-iconify icon="heroicons:clock" class="w-5 h-5 text-blue-600" />
                                    <span>Durée : <strong class="font-semibold text-gray-900">{{ $this->formatted_duration }}</strong></span>
                                </div>
                            @elseif($start_date && !$end_date)
                                <div class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 border border-blue-100 rounded-lg p-3 mt-3">
                                    <x-iconify icon="heroicons:arrow-path" class="w-5 h-5" />
                                    <span class="font-medium">Durée indéterminée</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton suggérer créneau --}}
                    @if($start_date && $vehicle_id && $driver_id)
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
                        <p class="text-sm text-gray-600">Informations complémentaires sur cette affectation.</p>
                    </div>

                    <div class="space-y-6">
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
        startTimeSlimSelect: null,
        endTimeSlimSelect: null,
        isUpdating: false,  // Flag anti-boucle infinie

        init() {
            this.$nextTick(() => {
                this.initSlimSelect();
                this.initTimeSelects();
                this.setupLivewireListeners();
            });
        },

        initSlimSelect() {
            // Vérifier que SlimSelect est chargé
            if (typeof SlimSelect === 'undefined') {
                console.error('❌ SlimSelect library not loaded');
                return;
            }

            // Véhicule select
            const vehicleEl = document.getElementById('vehicle_id');
            if (vehicleEl && !this.vehicleSlimSelect) {
                try {
                    this.vehicleSlimSelect = new SlimSelect({
                        select: vehicleEl,
                        settings: {
                            showSearch: true,
                            searchHighlight: true,
                            closeOnSelect: true,
                            allowDeselect: true,  // Permet de revenir au placeholder
                            placeholderText: 'Sélectionnez un véhicule',
                            searchPlaceholder: 'Rechercher un véhicule...',
                            searchText: 'Aucun véhicule trouvé',
                            searchingText: 'Recherche en cours...',
                        },
                        events: {
                            afterChange: (newVal) => {
                                // Protection anti-boucle infinie
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '';
                                console.log('🚗 Véhicule sélectionné:', value);

                                // Mettre à jour Livewire sans déclencher de re-render
                                @this.set('vehicle_id', value, false);

                                // Retirer l'état d'erreur
                                if (value) {
                                    document.getElementById('vehicle-select-wrapper')?.classList.remove('slimselect-error');
                                }

                                // 🆕 ENTERPRISE GRADE: Afficher le kilométrage immédiatement (UX réactive)
                                this.updateMileageDisplay(newVal[0]);

                                // 🔥 CORRECTIF: Charger le kilométrage depuis le serveur pour synchroniser Livewire
                                if (value) {
                                    @this.call('loadVehicleMileage').then(() => {
                                        console.log('✅ Kilométrage synchronisé avec Livewire depuis le serveur');
                                    }).catch(error => {
                                        console.error('❌ Erreur lors du chargement du kilométrage:', error);
                                    });
                                }

                                // Réinitialiser le flag après un court délai
                                setTimeout(() => { this.isUpdating = false; }, 100);
                            }
                        }
                    });
                    console.log('✅ Véhicule SlimSelect initialisé');
                } catch (error) {
                    console.error('❌ Erreur init véhicule SlimSelect:', error);
                }
            }

            // Chauffeur select
            const driverEl = document.getElementById('driver_id');
            if (driverEl && !this.driverSlimSelect) {
                try {
                    this.driverSlimSelect = new SlimSelect({
                        select: driverEl,
                        settings: {
                            showSearch: true,
                            searchHighlight: true,
                            closeOnSelect: true,
                            allowDeselect: true,  // Permet de revenir au placeholder
                            placeholderText: 'Sélectionnez un chauffeur',
                            searchPlaceholder: 'Rechercher un chauffeur...',
                            searchText: 'Aucun chauffeur trouvé',
                            searchingText: 'Recherche en cours...',
                        },
                        events: {
                            afterChange: (newVal) => {
                                // Protection anti-boucle infinie
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '';
                                console.log('👤 Chauffeur sélectionné:', value);

                                // Mettre à jour Livewire sans déclencher de re-render
                                @this.set('driver_id', value, false);

                                // Retirer l'état d'erreur
                                if (value) {
                                    document.getElementById('driver-select-wrapper')?.classList.remove('slimselect-error');
                                }

                                // Réinitialiser le flag après un court délai
                                setTimeout(() => { this.isUpdating = false; }, 100);
                            }
                        }
                    });
                    console.log('✅ Chauffeur SlimSelect initialisé');
                } catch (error) {
                    console.error('❌ Erreur init chauffeur SlimSelect:', error);
                }
            }
        },

        /**
         * 🆕 ENTERPRISE V3: Initialisation des time selectors
         */
        initTimeSelects() {
            if (typeof SlimSelect === 'undefined') return;

            // Heure de début
            const startTimeEl = document.getElementById('start_time');
            if (startTimeEl && !this.startTimeSlimSelect) {
                try {
                    this.startTimeSlimSelect = new SlimSelect({
                        select: startTimeEl,
                        settings: {
                            showSearch: true,
                            searchHighlight: false,
                            closeOnSelect: true,
                            allowDeselect: false,
                            placeholderText: 'Sélectionner l\'heure',
                        },
                        events: {
                            afterChange: (newVal) => {
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '08:00';
                                console.log('🕐 Heure début:', value);

                                @this.set('start_time', value, false);

                                setTimeout(() => { this.isUpdating = false; }, 100);
                            }
                        }
                    });
                    console.log('✅ Time Start SlimSelect initialisé');
                } catch (error) {
                    console.error('❌ Erreur init time start SlimSelect:', error);
                }
            }

            // Heure de fin (si élément existe)
            const endTimeEl = document.getElementById('end_time');
            if (endTimeEl && !this.endTimeSlimSelect) {
                try {
                    this.endTimeSlimSelect = new SlimSelect({
                        select: endTimeEl,
                        settings: {
                            showSearch: true,
                            searchHighlight: false,
                            closeOnSelect: true,
                            allowDeselect: false,
                            placeholderText: 'Sélectionner l\'heure',
                        },
                        events: {
                            afterChange: (newVal) => {
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '18:00';
                                console.log('🕐 Heure fin:', value);

                                @this.set('end_time', value, false);

                                setTimeout(() => { this.isUpdating = false; }, 100);
                            }
                        }
                    });
                    console.log('✅ Time End SlimSelect initialisé');
                } catch (error) {
                    console.error('❌ Erreur init time end SlimSelect:', error);
                }
            }

            // Observer pour réinitialiser le sélecteur de fin quand end_date change
            Livewire.on('reinit-end-time', () => {
                if (this.endTimeSlimSelect) {
                    this.endTimeSlimSelect.destroy();
                    this.endTimeSlimSelect = null;
                }
                setTimeout(() => this.initTimeSelects(), 100);
            });
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

            // Gestion des erreurs de validation pour SlimSelect
            this.setupValidationErrorHandling();
        },

        setupValidationErrorHandling() {
            // Observer les erreurs de validation Livewire
            const checkForErrors = () => {
                // Véhicule
                const vehicleHasError = {{ $errors->has('vehicle_id') ? 'true' : 'false' }};
                const vehicleWrapper = document.getElementById('vehicle-select-wrapper');
                if (vehicleWrapper) {
                    vehicleWrapper.classList.toggle('slimselect-error', vehicleHasError);
                }

                // Chauffeur
                const driverHasError = {{ $errors->has('driver_id') ? 'true' : 'false' }};
                const driverWrapper = document.getElementById('driver-select-wrapper');
                if (driverWrapper) {
                    driverWrapper.classList.toggle('slimselect-error', driverHasError);
                }
            };

            // Vérifier au chargement initial
            checkForErrors();
        },

        /**
         * 🆕 ENTERPRISE GRADE: Affiche le kilométrage du véhicule sélectionné immédiatement
         * 🔥 CORRECTIF: Amélioration du diagnostic et de la récupération du kilométrage
         */
        updateMileageDisplay(selectedOption) {
            const mileageSection = document.getElementById('mileage-display-section');
            const mileageDisplay = document.getElementById('current-mileage-display');
            const mileageInput = document.getElementById('start_mileage_input');

            if (selectedOption && selectedOption.value) {
                // Récupérer le kilométrage depuis l'option sélectionnée
                const select = document.getElementById('vehicle_id');
                const option = select?.querySelector(`option[value="${selectedOption.value}"]`);

                if (!option) {
                    console.warn('⚠️ Option non trouvée pour le véhicule ID:', selectedOption.value);
                    return;
                }

                const mileageAttr = option.getAttribute('data-mileage');
                const mileage = mileageAttr ? parseInt(mileageAttr, 10) : 0;

                console.log('📊 Kilométrage récupéré:', {
                    vehicleId: selectedOption.value,
                    mileageAttr: mileageAttr,
                    mileageParsed: mileage
                });

                // Afficher la section
                if (mileageSection) {
                    mileageSection.style.display = 'block';
                }

                // Mettre à jour l'affichage du kilométrage actuel
                if (mileageDisplay) {
                    mileageDisplay.textContent = new Intl.NumberFormat('fr-FR').format(mileage) + ' km';
                }

                // Pré-remplir le champ de kilométrage
                if (mileageInput) {
                    mileageInput.value = mileage;
                    mileageInput.setAttribute('min', mileage);
                }

                // Notifier Livewire du changement (sans déclencher re-render)
                @this.set('current_vehicle_mileage', mileage, false);
                @this.set('start_mileage', mileage, false);

                console.log('✅ Kilométrage affiché avec succès:', mileage, 'km');
            } else {
                // Cacher la section si aucun véhicule sélectionné
                if (mileageSection) {
                    mileageSection.style.display = 'none';
                }

                @this.set('current_vehicle_mileage', null, false);
                @this.set('start_mileage', null, false);
            }
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
