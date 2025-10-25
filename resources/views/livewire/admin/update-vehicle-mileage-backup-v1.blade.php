{{-- resources/views/livewire/admin/update-vehicle-mileage.blade.php --}}
<div class="fade-in">
 {{-- En-tête --}}
 <div class="mb-8">
 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
 Mettre à jour le kilométrage
 </h1>
 <p class="mt-2 text-sm text-gray-600">
 @if($mode === 'fixed' && $selectedVehicle)
 Mise à jour du kilométrage pour <strong>{{ $selectedVehicle->registration_plate }}</strong>
 @else
 Sélectionnez un véhicule et entrez le nouveau kilométrage
 @endif
 </p>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4">
 <a href="{{ route('admin.mileage-readings.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm transition-colors duration-200">
 <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
 </svg>
 Retour
 </a>
 </div>
 </div>
 </div>

 {{-- Flash Messages --}}
 @if (session()->has('success'))
 <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
 </div>
 </div>
 </div>
 @endif

 @if (session()->has('error'))
 <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
 </div>
 </div>
 </div>
 @endif

 @if (session()->has('warning'))
 <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
 </div>
 </div>
 </div>
 @endif

 {{-- Formulaire principal --}}
 <div class="bg-white shadow-lg rounded-lg overflow-hidden">
 <div class="p-6 sm:p-8">
 <form wire:submit.prevent="save" class="space-y-6">

 {{-- Sélection du véhicule (MODE SELECT) avec TomSelect --}}
 @if($mode === 'select')
 <div x-data="vehicleSelector()">
 @php
 $vehicleOptions = [];
 foreach($availableVehicles as $vehicle) {
 $vehicleOptions[$vehicle->id] = $vehicle->registration_plate . ' - ' . $vehicle->brand . ' ' . $vehicle->model . ' (' . number_format($vehicle->current_mileage) . ' km)';
 }
 @endphp
 
 <x-tom-select
 name="vehicleId"
 label="Véhicule"
 :options="$vehicleOptions"
 placeholder="Rechercher un véhicule par plaque, marque ou modèle..."
 required
 wire:model.live="vehicleId"
 x-on:change="loadVehicleMileage($event.target.value)"
 :error="$errors->first('vehicleId')"
 />
 </div>
 @endif

 {{-- Informations du véhicule sélectionné --}}
 @if($selectedVehicle)
 <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
 <div class="flex items-start">
 <div class="flex-shrink-0">
 <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 </div>
 <div class="ml-3 flex-1">
 <h3 class="text-sm font-semibold text-blue-900">
 {{ $selectedVehicle->brand }} {{ $selectedVehicle->model }}
 </h3>
 <div class="mt-2 text-sm text-blue-800 space-y-1">
 <p><strong>Plaque:</strong> {{ $selectedVehicle->registration_plate }}</p>
 <p><strong>Kilométrage actuel:</strong> <span class="font-bold text-lg">{{ number_format($selectedVehicle->current_mileage) }} km</span></p>
 </div>
 </div>
 </div>
 </div>

 {{-- Nouveau kilométrage --}}
 <div>
 <label for="newMileage" class="block text-sm font-medium text-gray-700 mb-2">
 Nouveau kilométrage (km) <span class="text-red-500">*</span>
 </label>
 <div class="relative">
 <input
 type="number"
 id="newMileage"
 wire:model.live="newMileage"
 min="{{ $selectedVehicle->current_mileage }}"
 max="9999999"
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('newMileage') border-red-500 @enderror"
 placeholder="Ex: {{ number_format($selectedVehicle->current_mileage + 100) }}"
 >
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-500 font-medium">km</span>
 </div>
 </div>
 @error('newMileage')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @else
 @if($newMileage > $selectedVehicle->current_mileage)
 <p class="mt-2 text-sm text-green-600 flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
 </svg>
 Distance parcourue: <strong class="ml-1">{{ number_format($newMileage - $selectedVehicle->current_mileage) }} km</strong>
 </p>
 @endif
 @enderror
 </div>

 {{-- Date et heure --}}
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ recordedDate: '{{ now()->format('Y-m-d') }}', recordedTime: '{{ now()->format('H:i') }}' }">
 <div>
 <x-datepicker
 name="recordedDate"
 label="Date du relevé"
 placeholder="JJ/MM/AAAA"
 :maxDate="date('Y-m-d')"
 :minDate="date('Y-m-d', strtotime('-7 days'))"
 required
 x-model="recordedDate"
 x-on:change="updateRecordedAt()"
 helpText="Maximum 7 jours dans le passé"
 />
 </div>
 
 <div>
 <x-time-picker
 name="recordedTime"
 label="Heure du relevé"
 placeholder="HH:MM"
 required
 x-model="recordedTime"
 x-on:change="updateRecordedAt()"
 helpText="Format 24 heures (HH:MM)"
 />
 </div>
 
 <input type="hidden" wire:model="recordedAt" x-bind:value="recordedDate + 'T' + recordedTime">
 
 @error('recordedAt')
 <p class="col-span-2 mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Notes --}}
 <div>
 <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
 Notes (optionnel)
 </label>
 <textarea
 id="notes"
 wire:model="notes"
 rows="3"
 maxlength="500"
 class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('notes') border-red-500 @enderror"
 placeholder="Ajoutez des notes si nécessaire (ex: raison de la mise à jour, anomalies détectées, etc.)"
 ></textarea>
 <div class="mt-1 flex justify-between text-xs text-gray-500">
 <span>{{ strlen($notes) }}/500 caractères</span>
 </div>
 @error('notes')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Boutons d'action --}}
 <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
 <button
 type="button"
 wire:click="refresh"
 class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200"
 >
 <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
 </svg>
 Actualiser
 </button>

 <button
 type="submit"
 class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-colors duration-200"
 wire:loading.attr="disabled"
 wire:target="save"
 >
 <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="save">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
 </svg>
 <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span wire:loading.remove wire:target="save">Enregistrer</span>
 <span wire:loading wire:target="save">Enregistrement...</span>
 </button>
 </div>
 @endif

 {{-- Message si aucun véhicule sélectionné (MODE SELECT) --}}
 @if($mode === 'select' && !$selectedVehicle)
 <div class="text-center py-12">
 <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 <p class="mt-4 text-sm text-gray-600">Sélectionnez un véhicule pour commencer</p>
 </div>
 @endif

 </form>
 </div>
 </div>

 {{-- Aide contextuelle --}}
 <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
 <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
 <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
 </svg>
 Informations importantes
 </h3>
 <ul class="space-y-2 text-sm text-gray-700">
 <li class="flex items-start">
 <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
 </svg>
 Le nouveau kilométrage doit être supérieur au kilométrage actuel
 </li>
 <li class="flex items-start">
 <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
 </svg>
 La date ne peut pas être dans le futur ni dépasser 7 jours dans le passé
 </li>
 <li class="flex items-start">
 <svg class="w-4 h-4 mr-2 mt-0.5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
 </svg>
 Toutes les mises à jour sont enregistrées dans l'historique
 </li>
 @if(auth()->user()->hasRole('Chauffeur'))
 <li class="flex items-start">
 <svg class="w-4 h-4 mr-2 mt-0.5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 Vous ne pouvez mettre à jour que le kilométrage de votre véhicule assigné
 </li>
 @endif
 </ul>
 </div>
</div>

@push('scripts')
<script>
// Alpine.js component for vehicle selector
document.addEventListener('alpine:init', () => {
    Alpine.data('vehicleSelector', () => ({
        loadVehicleMileage(vehicleId) {
            if (!vehicleId) return;
            
            // Le composant Livewire gère déjà le chargement via wire:model.live="vehicleId"
            // Cette fonction est un placeholder pour des actions futures si nécessaire
            console.log('Vehicle selected:', vehicleId);
        }
    }));
});

// Function to update combined recordedAt from date and time
function updateRecordedAt() {
    const dateInput = document.querySelector('input[name="recordedDate"]');
    const timeInput = document.querySelector('input[name="recordedTime"]');
    const recordedAtInput = document.querySelector('input[wire\\:model="recordedAt"]');
    
    if (dateInput && timeInput && recordedAtInput) {
        const date = dateInput.value;
        const time = timeInput.value;
        if (date && time) {
            recordedAtInput.value = date + 'T' + time;
            recordedAtInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
}
</script>
@endpush
