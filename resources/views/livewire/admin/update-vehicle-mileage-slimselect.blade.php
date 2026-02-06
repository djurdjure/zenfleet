{{-- ====================================================================
    📊 MISE À JOUR KILOMÉTRAGE - SLIMSELECT EDITION
    ====================================================================

    ✨ FEATURES:
    - SlimSelect pour recherche intelligente des véhicules
    - Filtrage en temps réel (plaque, marque, modèle)
    - Design standardisé
    - Affichage conditionnel robuste
    
    @version 15.0-SlimSelect
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

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- HEADER --}}
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
                        Recherchez et sélectionnez un véhicule pour mettre à jour son kilométrage
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

        {{-- FLASH MESSAGES --}}
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

        {{-- FORMULAIRE PRINCIPAL --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLONNE PRINCIPALE (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                <form wire:submit.prevent="save">
                    <x-card padding="p-6">
                        <div class="space-y-6">

                            {{-- SÉLECTION VÉHICULE AVEC SLIMSELECT --}}
                            @if($mode === 'select')
                            <div>
                                <x-slim-select
                                    name="vehicleId"
                                    wire:model.live="vehicleId"
                                    label="Rechercher un véhicule"
                                    placeholder="Rechercher par plaque, marque ou modèle..."
                                    required>
                                    <option data-placeholder="true"></option>
                                    @foreach($availableVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ number_format($vehicle->current_mileage) }} km)
                                    </option>
                                    @endforeach
                                </x-slim-select>

                                @error('vehicleId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif

                            {{-- CARTE INFO VÉHICULE --}}
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- FORMULAIRE RELEVÉ --}}
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
                                        :error="$errors->first('newMileage')" />

                                    {{-- Badge Différence --}}
                                    @if($newMileage && $newMileage >= $vehicleData['current_mileage'])
                                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                                        <x-iconify icon="heroicons:arrow-trending-up" class="w-5 h-5 text-green-600" />
                                        <span class="text-sm font-semibold text-green-800">
                                            Augmentation : +{{ number_format($newMileage - $vehicleData['current_mileage']) }} km
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Date --}}
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
                                    :error="$errors->first('recordedDate')" />

                                {{-- Heure --}}
                                <x-input
                                    type="time"
                                    name="recordedTime"
                                    label="Heure du Relevé"
                                    icon="clock"
                                    wire:model.live="recordedTime"
                                    required
                                    helpText="Heure précise du relevé"
                                    :error="$errors->first('recordedTime')" />
                            </div>

                            {{-- Notes --}}
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
                                    placeholder="Ex: Relevé effectué après le plein d'essence"></textarea>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ strlen($notes) }}/500 caractères
                                </p>
                            </div>

                            {{-- BOUTONS D'ACTION --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <button
                                    type="button"
                                    wire:click="resetForm"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                                    <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                                    Réinitialiser
                                </button>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    @if(!$vehicleData || !$newMileage || !$recordedDate || !$recordedTime) disabled @endif
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
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

            {{-- SIDEBAR (1/3) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- HISTORIQUE RÉCENT --}}
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
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $reading->recorded_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-card>
                @endif

                {{-- CONSEILS --}}
                <x-card padding="p-4" class="bg-blue-50 border-blue-200">
                    <div class="flex items-start gap-3">
                        <x-iconify icon="heroicons:information-circle" class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Conseils d'utilisation</h4>
                            <ul class="text-xs text-blue-800 space-y-1.5">
                                <li class="flex items-start gap-1.5">
                                    <span class="text-blue-600 mt-0.5">•</span>
                                    <span>Utilisez la recherche pour trouver rapidement un véhicule par plaque, marque ou modèle</span>
                                </li>
                                <li class="flex items-start gap-1.5">
                                    <span class="text-blue-600 mt-0.5">•</span>
                                    <span>Vérifiez le compteur du véhicule pour éviter les erreurs</span>
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