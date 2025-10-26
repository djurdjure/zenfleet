{{-- ====================================================================
 📝 FORMULAIRE UPDATE KILOMÉTRAGE - ENTERPRISE-GRADE V8.0
 ====================================================================

 SOLUTION AU PROBLÈME: TOUS LES CHAMPS VISIBLES DÈS LE DÉBUT
 - Sélection véhicule intégrée dans le formulaire
 - Champs disabled si pas de véhicule (au lieu de cachés)
 - Card info véhicule dynamique Alpine.js (x-show)
 - Calcul différence kilométrique temps réel
 - Section informations système (dates created_at, updated_at)
 - Validation temps réel
 - UX professionnelle world-class

 @version 8.0-Enterprise-World-Class
 @since 2025-10-24
 ==================================================================== --}}

<div class="fade-in" x-data="mileageForm()">
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-4xl lg:py-6">

        {{-- ===============================================
            HEADER
        =============================================== --}}
        <div class="mb-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                        <x-iconify icon="lucide:gauge" class="w-6 h-6 text-blue-600" />
                        Mettre à jour le kilométrage
                    </h1>
                    <p class="text-sm text-gray-600 ml-8.5">
                        @if($mode === 'fixed' && $selectedVehicle)
                            Mise à jour pour <strong>{{ $selectedVehicle->registration_plate }}</strong>
                        @else
                            Sélectionnez un véhicule et entrez le nouveau kilométrage
                        @endif
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('admin.mileage-readings.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-200">
                        <x-iconify icon="lucide:arrow-left" class="w-5 h-5" />
                        Retour
                    </a>
                </div>
            </div>
        </div>

        {{-- ===============================================
            FLASH MESSAGES
        =============================================== --}}
        @if (session()->has('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-center">
                <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600 mr-3" />
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('warning'))
        <div class="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4">
            <div class="flex items-center">
                <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-amber-600 mr-3" />
                <p class="text-sm font-medium text-amber-800">{{ session('warning') }}</p>
            </div>
        </div>
        @endif

        {{-- ===============================================
            FORMULAIRE COMPLET - TOUS CHAMPS VISIBLES ⭐
        =============================================== --}}
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <form wire:submit.prevent="save" class="space-y-8">

                    {{-- =============================================
                        SECTION 1: SÉLECTION VÉHICULE
                    ============================================= --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                            Sélection du Véhicule
                        </h3>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Véhicule - TOUJOURS VISIBLE --}}
                            @if($mode === 'select')
                            <div>
                                <label for="vehicleId" class="block mb-2 text-sm font-medium text-gray-900">
                                    Véhicule <span class="text-red-600">*</span>
                                </label>
                                
                                <select
                                    id="vehicleId"
                                    name="vehicleId"
                                    wire:model.live="vehicleId"
                                    class="vehicle-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 @error('vehicleId') border-red-500 @enderror"
                                    required>
                                    <option value="">Sélectionnez un véhicule...</option>
                                    @foreach($availableVehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ number_format($vehicle->current_mileage) }} km)
                                        </option>
                                    @endforeach
                                </select>

                                <p class="mt-2 text-sm text-gray-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:info" class="w-4 h-4" />
                                    Choisissez le véhicule dont vous souhaitez mettre à jour le kilométrage
                                </p>
                            </div>
                            @else
                            {{-- Mode Fixed: Afficher le véhicule pré-sélectionné --}}
                            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg">
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                        <x-iconify icon="lucide:car" class="w-7 h-7 text-blue-600" />
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-blue-900">
                                            {{ $selectedVehicle->brand }} {{ $selectedVehicle->model }}
                                        </h4>
                                        <div class="mt-3 grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-blue-700 font-medium">Plaque</p>
                                                <p class="text-sm font-semibold text-blue-900">{{ $selectedVehicle->registration_plate }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 font-medium">Kilométrage Actuel</p>
                                                <p class="text-xl font-bold text-blue-900">
                                                    {{ number_format($selectedVehicle->current_mileage) }} km
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Info Véhicule Dynamique (MODE SELECT) - Alpine.js x-show --}}
                            @if($mode === 'select')
                            <div 
                                x-show="$wire.selectedVehicle"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600 p-6 rounded-lg">
                                
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
                                        <x-iconify icon="lucide:car" class="w-7 h-7 text-blue-600" />
                                    </div>
                                    <div class="flex-1" x-show="$wire.selectedVehicle">
                                        <h4 class="text-lg font-bold text-blue-900">
                                            <template x-if="$wire.selectedVehicle">
                                                <span x-text="$wire.selectedVehicle.brand + ' ' + $wire.selectedVehicle.model"></span>
                                            </template>
                                        </h4>
                                        <div class="mt-3 grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-blue-700 font-medium">Plaque</p>
                                                <p class="text-sm font-semibold text-blue-900">
                                                    <template x-if="$wire.selectedVehicle">
                                                        <span x-text="$wire.selectedVehicle.registration_plate"></span>
                                                    </template>
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-blue-700 font-medium">Kilométrage Actuel</p>
                                                <p class="text-xl font-bold text-blue-900">
                                                    <template x-if="$wire.selectedVehicle">
                                                        <span x-text="Number($wire.selectedVehicle.current_mileage).toLocaleString()"></span>
                                                    </template>
                                                    km
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- =============================================
                        SECTION 2: NOUVEAU RELEVÉ - TOUS CHAMPS VISIBLES ⭐
                    ============================================= --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:edit-3" class="w-5 h-5 text-blue-600" />
                            Nouveau Relevé
                        </h3>

                        {{-- Ligne 1: Kilométrage, Date, Heure sur une seule ligne --}}
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
                            {{-- Nouveau Kilométrage (4 colonnes) --}}
                            <div class="lg:col-span-4">
                                <label for="newMileage" class="block mb-2 text-sm font-semibold text-gray-900">
                                    <x-iconify icon="lucide:gauge" class="w-4 h-4 inline mr-1 text-blue-600" />
                                    Kilométrage <span class="text-red-600">*</span>
                                </label>
                                
                                <div class="relative">
                                    <input
                                        type="number"
                                        id="newMileage"
                                        wire:model.live="newMileage"
                                        min="{{ $selectedVehicle ? $selectedVehicle->current_mileage : 0 }}"
                                        max="9999999"
                                        required
                                        placeholder="Ex: 75 000"
                                        class="bg-white border-2 text-gray-900 text-base font-semibold rounded-lg block w-full p-3.5 transition-all duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 pr-12 @error('newMileage') border-red-500 @enderror"
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-semibold text-sm">km</span>
                                    </div>
                                </div>

                                {{-- Différence calculée --}}
                                @if($selectedVehicle)
                                <div x-show="$wire.newMileage > {{ $selectedVehicle->current_mileage }}" 
                                     class="mt-2 text-xs text-green-600 flex items-center gap-1 font-medium">
                                    <x-iconify icon="lucide:plus-circle" class="w-3.5 h-3.5" />
                                    <span x-text="'+ ' + ($wire.newMileage - {{ $selectedVehicle->current_mileage }}).toLocaleString() + ' km'"></span>
                                </div>
                                @endif

                                @error('newMileage')
                                    <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date du Relevé (5 colonnes) --}}
                            <div class="lg:col-span-5">
                                <label for="recordedDate" class="block mb-2 text-sm font-semibold text-gray-900">
                                    <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1 text-green-600" />
                                    Date du Relevé <span class="text-red-600">*</span>
                                </label>
                                
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-iconify icon="lucide:calendar-days" class="w-5 h-5 text-gray-400" />
                                    </div>
                                    
                                    <input
                                        type="date"
                                        id="recordedDate"
                                        x-model="recordedDate"
                                        x-bind:max="new Date().toISOString().split('T')[0]"
                                        x-bind:min="new Date(Date.now() - 7*24*60*60*1000).toISOString().split('T')[0]"
                                        required
                                        x-on:change="updateRecordedAt()"
                                        class="bg-white border-2 text-gray-900 text-sm rounded-lg block w-full p-3.5 transition-all duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    />
                                </div>
                                
                                <p class="mt-2 text-xs text-gray-500">
                                    Max 7 jours dans le passé
                                </p>
                            </div>

                            {{-- Heure du Relevé (3 colonnes) --}}
                            <div class="lg:col-span-3">
                                <label for="recordedTime" class="block mb-2 text-sm font-semibold text-gray-900">
                                    <x-iconify icon="lucide:clock" class="w-4 h-4 inline mr-1 text-orange-600" />
                                    Heure <span class="text-red-600">*</span>
                                </label>
                                
                                <input
                                    type="time"
                                    id="recordedTime"
                                    x-model="recordedTime"
                                    required
                                    x-on:change="updateRecordedAt()"
                                    class="bg-white border-2 text-gray-900 text-sm font-mono rounded-lg block w-full p-3.5 transition-all duration-200 border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="HH:MM"
                                />
                                
                                <p class="mt-2 text-xs text-gray-500">
                                    Format 24h
                                </p>
                            </div>

                            {{-- Hidden field for combined recordedAt --}}
                            <input 
                                type="hidden" 
                                wire:model="recordedAt" 
                                x-bind:value="recordedDate + 'T' + recordedTime"
                            />
                        </div>

                        @error('recordedAt')
                            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- Ligne 2: Notes --}}
                        <div class="mb-6">

                            {{-- Notes --}}
                            <div>
                                <label for="notes" class="block mb-2 text-sm font-semibold text-gray-900">
                                    <x-iconify icon="lucide:message-square" class="w-4 h-4 inline mr-1 text-purple-600" />
                                    Notes Internes
                                    <span class="text-xs text-gray-500 font-normal ml-2">(optionnel)</span>
                                </label>
                                
                                <textarea
                                    id="notes"
                                    wire:model="notes"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Ex: Relevé après maintenance, compteur remis à zéro, anomalie détectée..."
                                    class="bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 @error('notes') border-red-500 @enderror"
                                ></textarea>
                                
                                <div class="mt-2 flex justify-between text-xs text-gray-500">
                                    <span x-text="($wire.notes?.length ?? 0) + '/500 caractères'"></span>
                                    <span class="text-gray-400">Entrée pour nouvelle ligne</span>
                                </div>

                                @error('notes')
                                    <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                        SECTION 3: INFORMATIONS SYSTÈME
                    ============================================= --}}
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <x-iconify icon="lucide:database" class="w-4 h-4" />
                            Informations Système
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="text-xs text-gray-600 mb-2 font-medium">Date/Heure Enregistrement</p>
                                <p class="font-semibold text-gray-900 flex items-center gap-2">
                                    <x-iconify icon="lucide:clock" class="w-4 h-4 text-gray-400" />
                                    Automatique (à la soumission)
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Sera: {{ now()->format('d/m/Y à H:i:s') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-2 font-medium">Enregistré par</p>
                                <p class="font-semibold text-gray-900 flex items-center gap-2">
                                    <x-iconify icon="lucide:user" class="w-4 h-4 text-gray-400" />
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Méthode: Manuel
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- =============================================
                        ACTIONS
                    ============================================= --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a 
                            href="{{ route('admin.mileage-readings.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 font-medium transition-colors">
                            <x-iconify icon="lucide:x" class="w-5 h-5" />
                            Annuler
                        </a>

                        <button
                            type="submit"
                            x-bind:disabled="!$wire.selectedVehicle || !$wire.newMileage"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:shadow-none">
                            <x-iconify 
                                icon="lucide:check-circle" 
                                class="w-5 h-5"
                                wire:loading.remove 
                            />
                            <svg 
                                class="animate-spin w-5 h-5" 
                                fill="none" 
                                viewBox="0 0 24 24"
                                wire:loading>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Enregistrer le Relevé</span>
                            <span wire:loading>Enregistrement...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- ===============================================
            AIDE CONTEXTUELLE
        =============================================== --}}
        <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                Informations importantes
            </h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" />
                    <span>Le nouveau kilométrage doit être supérieur au kilométrage actuel</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" />
                    <span>La date ne peut pas être dans le futur ni dépasser 7 jours dans le passé</span>
                </li>
                <li class="flex items-start gap-2">
                    <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 mt-0.5 text-blue-600 flex-shrink-0" />
                    <span>Toutes les mises à jour sont enregistrées dans l'historique</span>
                </li>
                @if(auth()->user()->hasRole('Chauffeur'))
                <li class="flex items-start gap-2">
                    <x-iconify icon="lucide:alert-triangle" class="w-4 h-4 mt-0.5 text-amber-600 flex-shrink-0" />
                    <span>Vous ne pouvez mettre à jour que le kilométrage de votre véhicule assigné</span>
                </li>
                @endif
            </ul>
        </div>

    </div>
</section>
</div>

@push('scripts')
<script>
// Alpine.js component for mileage form
function mileageForm() {
    return {
        recordedDate: '{{ now()->format('Y-m-d') }}',
        recordedTime: '{{ now()->format('H:i') }}',
        
        init() {
            // Initialize with current date/time
            this.updateRecordedAt();
        },
        
        updateRecordedAt() {
            if (this.recordedDate && this.recordedTime) {
                const combined = this.recordedDate + 'T' + this.recordedTime;
                // Trigger Livewire update
                @this.set('recordedAt', combined);
            }
        }
    };
}
</script>
@endpush

@push('styles')
<style>
/* Animation fade-in */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}
</style>
@endpush
