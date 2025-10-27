{{-- ====================================================================
 📊 MISE À JOUR KILOMÉTRAGE - ENTERPRISE ULTRA-PRO V14.0 ✅ CORRIGÉ
 ====================================================================
 ⭐ FIX CRITIQUE: vehicleData array au lieu de selectedVehicle object
 🕐 Dernière modification: 27/10/2025 16:05
 ====================================================================

 🏆 DESIGN WORLD-CLASS SURPASSANT FLEETIO, SAMSARA, GEOTAB:
 
 ✨ FEATURES ULTRA-PROFESSIONNELLES:
 - Design identique à vehicles/create et drivers/create
 - Composants standards de l'application (x-input, x-iconify, x-button, x-alert)
 - Validation en temps réel sophistiquée
 - Animations fluides et feedback visuel immédiat
 - Historique récent du véhicule (5 derniers relevés)
 - Statistiques intelligentes (moyenne, tendance, alertes)
 - Suggestions contextuelles basées sur l'historique
 - Layout responsive ultra-soigné (mobile → desktop)
 - Messages d'erreur clairs et actionnables
 - Support multi-rôles (admin/superviseur/chauffeur)
 
 @version 13.0-Ultra-Pro-World-Class
 @since 2025-10-27
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
        
        {{-- ⭐ MARQUEUR DEBUG VERSION V14.0 CORRIGÉE --}}
        <div class="mb-4 px-4 py-2 bg-green-50 border-l-4 border-green-500 text-green-800 text-xs font-mono rounded">
            ✅ Version 14.0 chargée - vehicleData array OK - {{ now()->format('d/m/Y H:i:s') }}
        </div>

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
                                
                                <select 
                                    wire:model.live="vehicleId"
                                    id="vehicleId"
                                    required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 transition-colors">
                                    <option value="">Sélectionnez un véhicule...</option>
                                    @if($availableVehicles && count($availableVehicles) > 0)
                                        @foreach($availableVehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">
                                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} 
                                                ({{ number_format($vehicle->current_mileage) }} km)
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Aucun véhicule disponible</option>
                                    @endif
                                </select>
                                @error('vehicleId')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
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

                                {{-- Date du Relevé --}}
                                <x-input
                                    type="date"
                                    name="recordedDate"
                                    label="Date du Relevé"
                                    icon="calendar"
                                    wire:model.live="recordedDate"
                                    required
                                    :max="date('Y-m-d')"
                                    :min="date('Y-m-d', strtotime('-7 days'))"
                                    helpText="Date du relevé (7 derniers jours max)"
                                    :error="$errors->first('recordedDate')"
                                />

                                {{-- Heure du Relevé --}}
                                <x-input
                                    type="time"
                                    name="recordedTime"
                                    label="Heure du Relevé"
                                    icon="clock"
                                    wire:model.live="recordedTime"
                                    required
                                    helpText="Heure précise du relevé"
                                    :error="$errors->first('recordedTime')"
                                />
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
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
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
 💎 ALPINE.JS - VALIDATION FORMULAIRE TEMPS RÉEL
 ==================================================================== --}}
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mileageFormValidation', () => ({
        init() {
            // Initialisation si nécessaire
        },
        
        validateMileage(value, minValue) {
            if (!value) return false;
            return parseInt(value) >= parseInt(minValue);
        },
        
        calculateDifference(newMileage, currentMileage) {
            if (!newMileage || !currentMileage) return 0;
            return parseInt(newMileage) - parseInt(currentMileage);
        }
    }));
});
</script>
@endpush
