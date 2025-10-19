<div class="space-y-6" x-data="{
 indefiniteDuration: @entangle('indefinite_duration'),
 showConflicts: @entangle('showConflicts'),
 isValidating: @entangle('isValidating')
}">

 {{-- En-tête --}}
 <div class="flex items-center justify-between">
 <div>
 <h3 class="text-lg leading-6 font-medium text-gray-900">
 {{ $isEdit ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}
 </h3>
 <p class="mt-1 text-sm text-gray-600">
 {{ $isEdit ? 'Modifiez les détails de l\'affectation véhicule ↔ chauffeur' : 'Créez une nouvelle affectation véhicule ↔ chauffeur' }}
 </p>
 </div>

 {{-- Indicateur de validation --}}
 <div x-show="isValidating" class="flex items-center space-x-2 text-blue-600">
 <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span class="text-sm">Vérification...</span>
 </div>
 </div>

 {{-- Messages de conflit --}}
 <div x-show="showConflicts" x-transition class="rounded-md bg-red-50 p-4 border border-red-200">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
 </svg>
 </div>
 <div class="ml-3 flex-1">
 <h3 class="text-sm font-medium text-red-800">Conflits détectés</h3>
 @if($validationMessages)
 <div class="mt-2 text-sm text-red-700">
 <ul class="list-disc pl-5 space-y-1">
 @foreach($validationMessages as $message)
 <li>{{ $message }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 {{-- Suggestions de créneaux --}}
 @if($suggestedSlots)
 <div class="mt-4">
 <h4 class="text-sm font-medium text-red-800 mb-2">Créneaux libres suggérés :</h4>
 <div class="space-y-2">
 @foreach($suggestedSlots as $slot)
 <button
 wire:click="applySuggestedSlot({{ json_encode($slot) }})"
 class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
 >
 <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 {{ $slot['start_formatted'] }}
 @if($slot['end_formatted'])
 → {{ $slot['end_formatted'] }}
 @else
 (durée indéterminée)
 @endif
 </button>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Action recherche prochain créneau --}}
 <div class="mt-4">
 <button
 wire:click="findNextAvailableSlot"
 class="inline-flex items-center px-3 py-1 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
 >
 <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
 </svg>
 Trouver le prochain créneau libre
 </button>
 </div>
 </div>
 </div>
 </div>

 {{-- Formulaire --}}
 <form wire:submit="save" class="space-y-6">

 {{-- Ligne 1: Véhicule et Chauffeur --}}
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 {{-- Véhicule --}}
 <div>
 <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1">
 Véhicule <span class="text-red-500">*</span>
 </label>
 <select wire:model.live="vehicle_id" id="vehicle_id"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('vehicle_id') border-red-300 @enderror">
 <option value="">Sélectionner un véhicule</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle['id'] }}">
 {{ $vehicle['label'] }} - {{ $vehicle['details'] }}
 @if($vehicle['mileage'])
 ({{ number_format($vehicle['mileage']) }} km)
 @endif
 </option>
 @endforeach
 </select>
 @error('vehicle_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Chauffeur --}}
 <div>
 <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-1">
 Chauffeur <span class="text-red-500">*</span>
 </label>
 <select wire:model.live="driver_id" id="driver_id"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('driver_id') border-red-300 @enderror">
 <option value="">Sélectionner un chauffeur</option>
 @foreach($drivers as $driver)
 <option value="{{ $driver['id'] }}">
 {{ $driver['label'] }}
 @if($driver['phone'])
 - {{ $driver['phone'] }}
 @endif
 </option>
 @endforeach
 </select>
 @error('driver_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Ligne 2: Dates et heures --}}
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 {{-- Date/Heure de remise --}}
 <div>
 <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">
 Date/Heure de remise <span class="text-red-500">*</span>
 </label>
 <input wire:model.live.debounce.500ms="start_datetime" type="datetime-local" id="start_datetime"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('start_datetime') border-red-300 @enderror">
 @error('start_datetime')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Date/Heure de restitution --}}
 <div>
 <div class="flex items-center justify-between mb-1">
 <label for="end_datetime" class="block text-sm font-medium text-gray-700">
 Date/Heure de restitution
 <span x-show="!indefiniteDuration" class="text-red-500">*</span>
 </label>
 <label class="flex items-center">
 <input wire:model.live="indefinite_duration" type="checkbox"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-600">Durée indéterminée</span>
 </label>
 </div>
 <input wire:model.live.debounce.500ms="end_datetime" type="datetime-local" id="end_datetime"
 x-bind:disabled="indefiniteDuration"
 x-bind:class="indefiniteDuration ? 'bg-gray-100 cursor-not-allowed' : ''"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('end_datetime') border-red-300 @enderror">
 @error('end_datetime')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Ligne 3: Kilométrage --}}
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
 {{-- Kilométrage de début --}}
 <div>
 <label for="start_mileage" class="block text-sm font-medium text-gray-700 mb-1">
 Kilométrage de début
 </label>
 <div class="relative">
 <input wire:model.live="start_mileage" type="number" id="start_mileage" min="0"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('start_mileage') border-red-300 @enderror">
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-500 sm:text-sm">km</span>
 </div>
 </div>
 @error('start_mileage')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Kilométrage de fin --}}
 <div>
 <label for="end_mileage" class="block text-sm font-medium text-gray-700 mb-1">
 Kilométrage de fin
 </label>
 <div class="relative">
 <input wire:model.live="end_mileage" type="number" id="end_mileage" min="0"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('end_mileage') border-red-300 @enderror">
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-500 sm:text-sm">km</span>
 </div>
 </div>
 @error('end_mileage')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Durée estimée --}}
 <div>
 <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">
 Durée estimée
 </label>
 <div class="relative">
 <input wire:model.live="estimated_duration_hours" type="number" id="estimated_duration_hours" min="0" step="0.5"
 x-bind:readonly="indefiniteDuration"
 x-bind:class="indefiniteDuration ? 'bg-gray-100' : ''"
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('estimated_duration_hours') border-red-300 @enderror">
 <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
 <span class="text-gray-500 sm:text-sm">h</span>
 </div>
 </div>
 @error('estimated_duration_hours')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 @if($estimated_duration_hours)
 <p class="mt-1 text-xs text-gray-500">
 @if($estimated_duration_hours < 24)
 {{ number_format($estimated_duration_hours, 1) }} heures
 @else
 {{ floor($estimated_duration_hours / 24) }} jour(s) {{ number_format($estimated_duration_hours % 24, 1) }} heures
 @endif
 </p>
 @endif
 </div>
 </div>

 {{-- Ligne 4: Motif et Notes --}}
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 {{-- Motif --}}
 <div>
 <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
 Motif de l'affectation
 </label>
 <input wire:model.live="reason" type="text" id="reason" maxlength="500"
 placeholder="Transport commercial, mission, formation..."
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('reason') border-red-300 @enderror">
 @error('reason')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">{{ strlen($reason ?? '') }}/500 caractères</p>
 </div>

 {{-- Notes --}}
 <div>
 <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
 Notes complémentaires
 </label>
 <textarea wire:model.live="notes" id="notes" rows="3" maxlength="1000"
 placeholder="Instructions particulières, équipements spéciaux..."
 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('notes') border-red-300 @enderror"></textarea>
 @error('notes')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">{{ strlen($notes ?? '') }}/1000 caractères</p>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
 <button type="button" wire:click="cancel"
 class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 Annuler
 </button>

 <button type="submit"
 x-bind:disabled="isValidating || showConflicts"
 x-bind:class="(isValidating || showConflicts) ? 'opacity-50 cursor-not-allowed' : ''"
 class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">

 <span x-show="isValidating" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Validation...
 </span>

 <span x-show="!isValidating">
 {{ $isEdit ? 'Modifier l\'affectation' : 'Créer l\'affectation' }}
 </span>
 </button>
 </div>
 </form>

 {{-- Détails des conflits (développable) --}}
 @if($conflicts && (count($conflicts['vehicle'] ?? []) > 0 || count($conflicts['driver'] ?? []) > 0))
 <div x-data="{ showDetails: false }" class="border-t border-gray-200 pt-4">
 <button @click="showDetails = !showDetails" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
 <svg x-show="!showDetails" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
 </svg>
 <svg x-show="showDetails" class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
 </svg>
 Voir les détails des conflits
 </button>

 <div x-show="showDetails" x-transition class="mt-3 space-y-3">
 @if(count($conflicts['vehicle'] ?? []) > 0)
 <div>
 <h4 class="text-sm font-medium text-gray-900 mb-2">Conflits véhicule :</h4>
 <div class="space-y-2">
 @foreach($conflicts['vehicle'] as $conflict)
 <div class="flex items-center p-2 bg-red-50 rounded-md text-sm">
 <svg class="h-4 w-4 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
 </svg>
 <div class="flex-1">
 <span class="font-medium">{{ $conflict->vehicle_display }}</span>
 déjà affecté à <span class="font-medium">{{ $conflict->driver_display }}</span>
 du {{ $conflict->start_datetime->format('d/m/Y H:i') }}
 @if($conflict->end_datetime)
 au {{ $conflict->end_datetime->format('d/m/Y H:i') }}
 @else
 <span class="text-orange-600">(en cours)</span>
 @endif
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 @if(count($conflicts['driver'] ?? []) > 0)
 <div>
 <h4 class="text-sm font-medium text-gray-900 mb-2">Conflits chauffeur :</h4>
 <div class="space-y-2">
 @foreach($conflicts['driver'] as $conflict)
 <div class="flex items-center p-2 bg-red-50 rounded-md text-sm">
 <svg class="h-4 w-4 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
 </svg>
 <div class="flex-1">
 <span class="font-medium">{{ $conflict->driver_display }}</span>
 déjà affecté au véhicule <span class="font-medium">{{ $conflict->vehicle_display }}</span>
 du {{ $conflict->start_datetime->format('d/m/Y H:i') }}
 @if($conflict->end_datetime)
 au {{ $conflict->end_datetime->format('d/m/Y H:i') }}
 @else
 <span class="text-orange-600">(en cours)</span>
 @endif
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>
 </div>
 @endif
</div>

{{-- Scripts Alpine.js pour notifications --}}
<script>
document.addEventListener('livewire:initialized', () => {
 Livewire.on('slot-applied', (message) => {
 // Notification simple - peut être remplacée par une lib de notifications
 const notification = document.createElement('div');
 notification.className = 'fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg';
 notification.innerHTML = `
 <div class="flex items-center">
 <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.3a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
 </svg>
 ${message}
 </div>
 `;
 document.body.appendChild(notification);
 setTimeout(() => notification.remove(), 3000);
 });

 Livewire.on('slot-found', (message) => {
 const notification = document.createElement('div');
 notification.className = 'fixed top-4 right-4 z-50 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded shadow-lg';
 notification.innerHTML = `
 <div class="flex items-center">
 <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
 </svg>
 ${message}
 </div>
 `;
 document.body.appendChild(notification);
 setTimeout(() => notification.remove(), 3000);
 });

 Livewire.on('no-slot-found', (message) => {
 const notification = document.createElement('div');
 notification.className = 'fixed top-4 right-4 z-50 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-lg';
 notification.innerHTML = `
 <div class="flex items-center">
 <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
 </svg>
 ${message}
 </div>
 `;
 document.body.appendChild(notification);
 setTimeout(() => notification.remove(), 4000);
 });

 Livewire.on('validation-error', (message) => {
 const notification = document.createElement('div');
 notification.className = 'fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg';
 notification.innerHTML = `
 <div class="flex items-center">
 <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
 </svg>
 ${message}
 </div>
 `;
 document.body.appendChild(notification);
 setTimeout(() => notification.remove(), 4000);
 });
});
</script>