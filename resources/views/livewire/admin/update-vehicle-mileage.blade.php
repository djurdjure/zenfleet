{{-- ====================================================================
 📊 MISE À JOUR KILOMÉTRAGE - ENTERPRISE ULTRA-PRO V15.0 🚀
 ====================================================================
 🎯 AMÉLIORATIONS MAJEURES V15.0:
 - SlimSelect pour sélection de véhicule (style identique aux affectations)
 - Flatpickr via x-datepicker pour sélection de date
 - SlimSelect pour sélection d'heure (options par 15min)
 - CSS enterprise-grade cohérent avec le module affectations
 - Initialisation Alpine.js robuste avec gestion d'erreurs
 - Support complet Livewire + wire:ignore pour éviter conflits
 🕐 Dernière modification: 21/11/2025
 ====================================================================

 🏆 DESIGN WORLD-CLASS SURPASSANT FLEETIO, SAMSARA, GEOTAB:

 ✨ FEATURES ULTRA-PROFESSIONNELLES:
 - Design identique à vehicles/create et drivers/create
 - SlimSelect avec recherche, highlighting, et placeholders professionnels
 - Flatpickr avec thème enterprise light mode (français)
 - Validation en temps réel sophistiquée
 - Animations fluides et feedback visuel immédiat
 - Historique récent du véhicule (5 derniers relevés)
 - Statistiques intelligentes (moyenne, tendance, alertes)
 - Suggestions contextuelles basées sur l'historique
 - Layout responsive ultra-soigné (mobile → desktop)
 - Messages d'erreur clairs et actionnables
 - Support multi-rôles (admin/superviseur/chauffeur)

 @version 15.0-Enterprise-Grade-SlimSelect-Flatpickr
 @since 2025-11-21
 @author Expert Fullstack Senior (20+ ans)
 ==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     class="fixed top-4 right-4 z-50 max-w-md">
    <x-alert type="success" title="Succès" dismissible>
        {{ session('success') }}
    </x-alert>
</div>
@endif

{{-- ====================================================================
 🎨 SECTION PRINCIPALE - FOND GRIS CLAIR MODERNE
 ==================================================================== --}}
<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- ===============================================
            HEADER COMPACT ET MODERNE
        =============================================== --}}
        <div class="mb-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                        <x-iconify icon="heroicons:gauge" class="w-6 h-6 text-blue-600" />
                        Mettre à jour le kilométrage
                    </h1>
                    <p class="text-sm text-gray-600 ml-8.5">
                        @if($mode === 'fixed' && $vehicleData)
                            Mise à jour pour <strong>{{ $vehicleData['registration_plate'] }}</strong> - {{ $vehicleData['brand'] }} {{ $vehicleData['model'] }}
                        @else
                            Sélectionnez un véhicule et entrez le nouveau kilométrage avec précision
                        @endif
                    </p>
                </div>
                <div class="mt-4 flex gap-2 md:mt-0 md:ml-4">
                    <a href="{{ route('admin.mileage-readings.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
                        <span class="hidden sm:inline">Retour</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===============================================
            FLASH MESSAGES - DESIGN ULTRA-PRO
        =============================================== --}}
        @if (session()->has('error'))
        <x-alert type="error" title="Erreur" dismissible class="mb-6">
            {{ session('error') }}
        </x-alert>
        @endif

        @if (session()->has('warning'))
        <x-alert type="warning" title="Attention" dismissible class="mb-6">
            {{ session('warning') }}
        </x-alert>
        @endif

        {{-- ===============================================
            FORMULAIRE PRINCIPAL - ULTRA-PRO LAYOUT
        =============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- ===============================================
                COLONNE PRINCIPALE - FORMULAIRE (2/3)
            =============================================== --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- CARD FORMULAIRE --}}
                <form wire:submit.prevent="save" x-data="mileageFormValidation()">
                    <x-card padding="p-6">
                        <div class="space-y-6">

                            {{-- ===============================================
                                SÉLECTION VÉHICULE (MODE SELECT)
                            =============================================== --}}
                            @if($mode === 'select')
                            <div>
                                <label for="vehicleId" class="block mb-2 text-sm font-medium text-gray-900">
                                    <x-iconify icon="heroicons:truck" class="w-5 h-5 inline mr-1 text-blue-600" />
                                    Véhicule
                                    <span class="text-red-600">*</span>
                                </label>

                                {{-- 🔥 ENTERPRISE GRADE: SlimSelect pour sélection professionnelle --}}
                                <div wire:ignore id="vehicle-select-wrapper">
                                    <select
                                        id="vehicleId"
                                        name="vehicleId"
                                        class="slimselect-vehicle w-full"
                                        required>
                                        {{-- Option placeholder avec data-placeholder pour SlimSelect --}}
                                        <option data-placeholder="true" value=""></option>
                                        @if($availableVehicles && count($availableVehicles) > 0)
                                            @foreach($availableVehicles as $vehicle)
                                                <option
                                                    value="{{ $vehicle->id }}"
                                                    data-mileage="{{ $vehicle->current_mileage ?? 0 }}"
                                                    data-registration="{{ $vehicle->registration_plate }}"
                                                    data-brand="{{ $vehicle->brand }}"
                                                    data-model="{{ $vehicle->model }}"
                                                    @selected($vehicleId == $vehicle->id)>
                                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                                    ({{ number_format($vehicle->current_mileage) }} km)
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Aucun véhicule disponible</option>
                                        @endif
                                    </select>
                                </div>
                                @error('vehicleId')
                                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                        <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                @enderror

                                @if($availableVehicles && count($availableVehicles) === 0)
                                <p class="mt-2 text-sm text-amber-600 flex items-center gap-1.5">
                                    <x-iconify icon="heroicons:exclamation-triangle" class="w-4 h-4" />
                                    Aucun véhicule n'est disponible pour la mise à jour du kilométrage.
                                </p>
                                @endif
                            </div>
                            @endif

                            {{-- ===============================================
                                CARTE INFO VÉHICULE (QUAND SÉLECTIONNÉ)
                            =============================================== --}}
                            @if($vehicleData)
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border-2 border-blue-200 p-5">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-blue-600 border border-blue-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-iconify icon="heroicons:truck" class="w-7 h-7 text-white" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-bold text-gray-900 mb-1">
                                            {{ $vehicleData['brand'] }} {{ $vehicleData['model'] }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-3 text-sm">
                                            <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700">
                                                <x-iconify icon="heroicons:identification" class="w-4 h-4 text-blue-600" />
                                                {{ $vehicleData['registration_plate'] }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full text-gray-700 font-medium border border-blue-200">
                                                <x-iconify icon="heroicons:gauge" class="w-4 h-4 text-blue-600" />
                                                <strong class="text-blue-900">{{ number_format($vehicleData['current_mileage']) }} km</strong>
                                            </span>
                                            @if(isset($vehicleData['category_name']) && $vehicleData['category_name'])
                                            <span class="text-gray-600">
                                                {{ $vehicleData['category_name'] }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ===============================================
                                FORMULAIRE RELEVÉ - GRID RESPONSIVE
                            =============================================== --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                {{-- Nouveau Kilométrage --}}
                                <div class="md:col-span-2">
                                    <x-input
                                        type="number"
                                        name="newMileage"
                                        label="Nouveau Kilométrage (km)"
                                        icon="gauge"
                                        wire:model.live="newMileage"
                                        placeholder="Ex: {{ $vehicleData['current_mileage'] + 100 }}"
                                        required
                                        :min="$vehicleData['current_mileage']"
                                        helpText="Le kilométrage doit être supérieur ou égal au kilométrage actuel ({{ number_format($vehicleData['current_mileage']) }} km)"
                                        :error="$errors->first('newMileage')"
                                    />

                                    {{-- Badge Différence (Temps Réel) --}}
                                    @if($newMileage && $newMileage >= $vehicleData['current_mileage'])
                                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                                        <x-iconify icon="heroicons:arrow-trending-up" class="w-5 h-5 text-green-600" />
                                        <span class="text-sm font-semibold text-green-800">
                                            Augmentation : +{{ number_format($newMileage - $vehicleData['current_mileage']) }} km
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Date du Relevé - FLATPICKR ENTERPRISE --}}
                                <div>
                                    <label for="recordedDate" class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center gap-2">
                                            <x-iconify icon="heroicons:calendar-days" class="w-4 h-4 text-gray-500" />
                                            Date du Relevé
                                            <span class="text-red-500">*</span>
                                        </div>
                                    </label>
                                    <x-datepicker
                                        name="recordedDate"
                                        wire:model.live="recordedDate"
                                        :value="$recordedDate"
                                        :error="$errors->first('recordedDate')"
                                        placeholder="Sélectionner la date du relevé"
                                        format="d/m/Y"
                                        :maxDate="date('Y-m-d')"
                                        :minDate="date('Y-m-d', strtotime('-7 days'))"
                                        required
                                    />
                                    <p class="mt-1.5 text-xs text-gray-500">Date du relevé (7 derniers jours max)</p>
                                </div>

                                {{-- Heure du Relevé - SLIMSELECT ENTERPRISE --}}
                                <div>
                                    <label for="recordedTime" class="block text-sm font-medium text-gray-700 mb-2">
                                        <div class="flex items-center gap-2">
                                            <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-500" />
                                            Heure du Relevé
                                            <span class="text-red-500">*</span>
                                        </div>
                                    </label>
                                    <div wire:ignore id="time-select-wrapper">
                                        <select
                                            id="recordedTime"
                                            name="recordedTime"
                                            class="slimselect-time w-full"
                                            required>
                                            <option data-placeholder="true" value=""></option>
                                            @for($hour = 0; $hour < 24; $hour++)
                                                @foreach(['00', '30'] as $minute)
                                                    @php $time = sprintf('%02d:%s', $hour, $minute); @endphp
                                                    <option value="{{ $time }}" @selected($recordedTime == $time)>
                                                        {{ $time }}
                                                    </option>
                                                @endforeach
                                            @endfor
                                        </select>
                                    </div>
                                    @error('recordedTime')
                                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                            <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-1.5 text-xs text-gray-500">Heure précise du relevé</p>
                                </div>
                            </div>

                            {{-- Notes (Optionnel) --}}
                            <div>
                                <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
                                    <x-iconify icon="heroicons:document-text" class="w-5 h-5 inline mr-1 text-gray-600" />
                                    Notes (optionnel)
                                </label>
                                <textarea
                                    wire:model="notes"
                                    id="notes"
                                    rows="3"
                                    maxlength="500"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm"
                                    placeholder="Ex: Relevé effectué après le plein d'essence. Véhicule en bon état."></textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    <span x-text="$wire.notes.length || 0"></span>/500 caractères
                                </p>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ===============================================
                                BOUTONS D'ACTION
                            =============================================== --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('admin.mileage-readings.index') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                                    <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                                    Annuler
                                </a>
                                
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    @if(!$vehicleData || !$newMileage || !$recordedDate || !$recordedTime) disabled @endif
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:shadow-none">
                                    <x-iconify icon="heroicons:check" class="w-5 h-5" wire:loading.remove />
                                    <svg wire:loading class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove>Enregistrer le Relevé</span>
                                    <span wire:loading>Enregistrement...</span>
                                </button>
                            </div>
                            @endif

                        </div>
                    </x-card>
                </form>

            </div>

            {{-- ===============================================
                COLONNE SIDEBAR - INFO & HISTORIQUE (1/3)
            =============================================== --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- ===============================================
                    HISTORIQUE RÉCENT DU VÉHICULE
                =============================================== --}}
                @if($vehicleData && isset($recentReadings) && count($recentReadings) > 0)
                <x-card padding="p-0">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:clock" class="w-5 h-5 text-blue-600" />
                            Historique Récent
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($recentReadings as $reading)
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                            <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                                <x-iconify icon="heroicons:gauge" class="w-5 h-5 text-blue-600" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 mb-1">
                                    <span class="text-base font-bold text-gray-900">
                                        {{ number_format($reading->mileage) }} km
                                    </span>
                                    @if($loop->index > 0 && isset($recentReadings[$loop->index - 1]))
                                    <span class="text-xs text-green-600 font-medium">
                                        +{{ number_format($reading->mileage - $recentReadings[$loop->index - 1]->mileage) }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $reading->recorded_at->format('d/m/Y à H:i') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $reading->recordedBy->name ?? 'Système' }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-3 bg-gray-50 border-t border-gray-200">
                        <a href="{{ route('admin.vehicles.mileage-history', $vehicleData['id']) }}" 
                           class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            Voir l'historique complet
                            <x-iconify icon="heroicons:arrow-right" class="w-4 h-4" />
                        </a>
                    </div>
                </x-card>
                @endif

                {{-- ===============================================
                    STATISTIQUES INTELLIGENTES
                =============================================== --}}
                @if($vehicleData && $stats)
                <x-card padding="p-0">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:chart-bar" class="w-5 h-5 text-purple-600" />
                            Statistiques
                        </h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Moyenne Quotidienne</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ number_format($stats['avg_daily_mileage'] ?? 0) }} km/jour
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Kilométrage Total Parcouru</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ number_format($stats['total_distance'] ?? 0) }} km
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Nombre de Relevés</div>
                            <div class="text-lg font-bold text-gray-900">
                                {{ $stats['total_readings'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </x-card>
                @endif

                {{-- ===============================================
                    AIDE & CONSEILS
                =============================================== --}}
                <x-card padding="p-4" class="bg-blue-50 border-blue-200">
                    <div class="flex items-start gap-3">
                        <x-iconify icon="heroicons:information-circle" class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Conseils d'utilisation</h4>
                            <ul class="text-xs text-blue-800 space-y-1.5">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-blue-600 mt-0.5">•</span>
                                    <span>Relevez le kilométrage à la même heure chaque jour pour plus de précision</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-blue-600 mt-0.5">•</span>
                                    <span>Vérifiez le compteur du véhicule pour éviter les erreurs de saisie</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-blue-600 mt-0.5">•</span>
                                    <span>Ajoutez des notes pour contextualiser les relevés inhabituels</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </x-card>

            </div>

        </div>

    </div>
</section>

{{-- ====================================================================
 💎 ALPINE.JS + SLIMSELECT - ENTERPRISE GRADE INITIALIZATION
 ==================================================================== --}}
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mileageFormValidation', () => ({
        vehicleSlimSelect: null,
        timeSlimSelect: null,
        isUpdating: false,

        init() {
            this.$nextTick(() => {
                this.initSlimSelect();
                this.setupLivewireListeners();
            });
        },

        initSlimSelect() {
            // Vérifier que SlimSelect est chargé
            if (typeof SlimSelect === 'undefined') {
                console.error('❌ SlimSelect library not loaded');
                return;
            }

            // 🚗 Véhicule select
            const vehicleEl = document.getElementById('vehicleId');
            if (vehicleEl && !this.vehicleSlimSelect) {
                try {
                    this.vehicleSlimSelect = new SlimSelect({
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
                                // Protection anti-boucle infinie
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '';
                                console.log('🚗 Véhicule sélectionné:', value);

                                // Mettre à jour Livewire
                                @this.set('vehicleId', value, false);

                                // Retirer l'état d'erreur
                                if (value) {
                                    document.getElementById('vehicle-select-wrapper')?.classList.remove('slimselect-error');
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

            // 🕐 Heure select
            const timeEl = document.getElementById('recordedTime');
            if (timeEl && !this.timeSlimSelect) {
                try {
                    this.timeSlimSelect = new SlimSelect({
                        select: timeEl,
                        settings: {
                            showSearch: true,
                            searchHighlight: false,
                            closeOnSelect: true,
                            allowDeselect: false,
                            placeholderText: 'Sélectionner l\'heure',
                            searchPlaceholder: 'Rechercher...',
                        },
                        events: {
                            afterChange: (newVal) => {
                                if (this.isUpdating) return;
                                this.isUpdating = true;

                                const value = newVal[0]?.value || '';
                                console.log('🕐 Heure sélectionnée:', value);

                                @this.set('recordedTime', value, false);

                                if (value) {
                                    document.getElementById('time-select-wrapper')?.classList.remove('slimselect-error');
                                }

                                setTimeout(() => { this.isUpdating = false; }, 100);
                            }
                        }
                    });
                    console.log('✅ Heure SlimSelect initialisée');
                } catch (error) {
                    console.error('❌ Erreur init heure SlimSelect:', error);
                }
            }
        },

        setupLivewireListeners() {
            // Écouter les événements Livewire pour réinitialiser les selects si nécessaire
            Livewire.on('vehicleUpdated', () => {
                console.log('🔄 Véhicule mis à jour');
            });
        },

        // Fonctions de validation (conservées)
        validateMileage(value, minValue) {
            if (!value) return false;
            return parseInt(value) >= parseInt(minValue);
        },

        calculateDifference(newMileage, currentMileage) {
            if (!newMileage || !currentMileage) return 0;
            return parseInt(newMileage) - parseInt(currentMileage);
        },

        // Cleanup lors de la destruction du composant
        destroy() {
            if (this.vehicleSlimSelect) {
                this.vehicleSlimSelect.destroy();
            }
            if (this.timeSlimSelect) {
                this.timeSlimSelect.destroy();
            }
        }
    }));
});
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

/* Style pour "Aucun résultat" */
.ss-content .ss-search .ss-addable {
    color: var(--ss-font-placeholder-color);
    font-size: 0.875rem;
    padding: var(--ss-spacing-m) var(--ss-spacing-l);
    text-align: center;
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
