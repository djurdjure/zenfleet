{{-- ====================================================================
 📝 FORMULAIRE UPDATE KILOMÉTRAGE - ENTERPRISE-GRADE V12.0
 ====================================================================

 SOLUTION ENTREPRISE ULTRA-PRO:
 - Utilisation des composants x-input standard de l'application
 - Style conforme aux pages véhicules/chauffeurs
 - Logique d'activation du bouton simplifiée
 - Chargement automatique du kilométrage actuel
 - Validation temps réel enterprise-grade

 @version 12.0-Enterprise-Ultra-Pro
 @since 2025-10-26
 ==================================================================== --}}

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
            FORMULAIRE
        =============================================== --}}
        <form wire:submit.prevent="save" x-data="mileageForm">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="p-6 space-y-6">

                    {{-- Sélection Véhicule --}}
                    @if($mode === 'select')
                    <div>
                        <label for="vehicleId" class="block mb-2 text-sm font-medium text-gray-900">
                            <x-iconify icon="lucide:car" class="w-5 h-5 inline mr-1 text-blue-600" />
                            Véhicule
                            <span class="text-red-600">*</span>
                        </label>
                        
                        <select 
                            wire:model.live="vehicleId"
                            id="vehicleId"
                            required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Sélectionnez un véhicule...</option>
                            @foreach($availableVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ number_format($vehicle->current_mileage) }} km)
                                </option>
                            @endforeach
                        </select>

                        @error('vehicleId')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Info Véhicule (Mode Select) --}}
                    @if($mode === 'select' && $selectedVehicle)
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-600 p-6 rounded-lg">
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

                    {{-- Info Véhicule (Mode Fixed) --}}
                    @if($mode === 'fixed' && $selectedVehicle)
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

                    {{-- Champs du relevé --}}
                    @if($selectedVehicle)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        {{-- Kilométrage --}}
                        <div>
                            <x-input
                                type="number"
                                name="newMileage"
                                label="Nouveau Kilométrage (km)"
                                icon="gauge"
                                wire:model.live="newMileage"
                                required
                                placeholder="Ex: 75000"
                                :error="$errors->first('newMileage')"
                            />
                            
                            {{-- Différence --}}
                            @if($newMileage > $selectedVehicle->current_mileage)
                            <div class="mt-2 text-xs text-green-600 flex items-center gap-1 font-medium">
                                <x-iconify icon="lucide:plus-circle" class="w-3.5 h-3.5" />
                                <span>+ {{ number_format($newMileage - $selectedVehicle->current_mileage) }} km</span>
                            </div>
                            @endif
                        </div>

                        {{-- Date --}}
                        <div>
                            <x-input
                                type="date"
                                name="recordedDate"
                                label="Date du Relevé"
                                icon="calendar-days"
                                x-model="recordedDate"
                                x-on:change="updateRecordedAt()"
                                required
                                :max="date('Y-m-d')"
                                :min="date('Y-m-d', strtotime('-7 days'))"
                            />
                        </div>

                        {{-- Heure --}}
                        <div>
                            <x-input
                                type="time"
                                name="recordedTime"
                                label="Heure"
                                icon="clock"
                                x-model="recordedTime"
                                x-on:change="updateRecordedAt()"
                                required
                            />
                        </div>
                    </div>

                    {{-- Hidden field combiné --}}
                    <input type="hidden" wire:model="recordedAt" x-bind:value="recordedDate + 'T' + recordedTime">

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
                            <x-iconify icon="lucide:message-square" class="w-5 h-5 inline mr-1 text-purple-600" />
                            Notes Internes
                            <span class="text-xs text-gray-500 font-normal ml-2">(optionnel)</span>
                        </label>
                        
                        <textarea
                            wire:model="notes"
                            id="notes"
                            rows="3"
                            maxlength="500"
                            placeholder="Ex: Relevé après maintenance, compteur remis à zéro..."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('notes') border-red-500 @enderror"
                        ></textarea>
                        
                        <div class="mt-1 flex justify-between text-xs text-gray-500">
                            <span>{{ strlen($notes ?? '') }}/500 caractères</span>
                        </div>

                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                </div>

                {{-- Footer Actions --}}
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <a 
                        href="{{ route('admin.mileage-readings.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-white text-gray-700 font-medium transition-colors">
                        <x-iconify icon="lucide:x" class="w-5 h-5" />
                        Annuler
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed"
                        @if(!$selectedVehicle || !$newMileage) disabled @endif>
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
            </div>
        </form>

        {{-- Aide contextuelle --}}
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
            </ul>
        </div>

    </div>
</section>

@push('scripts')
<script>
function mileageForm() {
    return {
        recordedDate: '{{ now()->format('Y-m-d') }}',
        recordedTime: '{{ now()->format('H:i') }}',
        
        init() {
            this.updateRecordedAt();
        },
        
        updateRecordedAt() {
            if (this.recordedDate && this.recordedTime) {
                const combined = this.recordedDate + 'T' + this.recordedTime;
                @this.set('recordedAt', combined);
            }
        }
    };
}
</script>
@endpush
