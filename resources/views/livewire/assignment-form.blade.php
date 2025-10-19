{{-- 📝 Formulaire d'Affectation Enterprise-Grade --}}
<div class="space-y-6">
 {{-- En-tête du formulaire --}}
 <div class="border-b border-gray-200 pb-4">
 <h3 class="text-lg font-medium text-gray-900">
 {{ $isEditing ? 'Modifier l\'affectation' : 'Nouvelle affectation' }}
 </h3>
 <p class="mt-1 text-sm text-gray-600">
 {{ $isEditing ? 'Modifiez les détails de cette affectation.' : 'Créez une nouvelle affectation véhicule ↔ chauffeur.' }}
 </p>
 </div>

 {{-- Alerte de validation en temps réel --}}
 @if($hasConflicts && !$forceCreate)
 <div class="rounded-md bg-red-50 p-4 border border-red-200" role="alert" aria-live="polite">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-red-800">
 {{ count($conflicts) === 1 ? 'Conflit détecté' : count($conflicts) . ' conflits détectés' }}
 </h3>
 <div class="mt-2 text-sm text-red-700">
 <ul class="list-disc space-y-1 pl-5">
 @foreach($conflicts as $conflict)
 <li>
 <strong>{{ $conflict['resource_label'] }}</strong>
 déjà affecté du {{ $conflict['period']['start'] }} au {{ $conflict['period']['end'] }}
 <span class="text-xs text-red-600">({{ $conflict['status'] }})</span>
 </li>
 @endforeach
 </ul>
 </div>

 {{-- Suggestions de créneaux --}}
 @if(count($suggestions) > 0)
 <div class="mt-3">
 <h4 class="text-sm font-medium text-red-800">Créneaux libres suggérés:</h4>
 <div class="mt-2 space-y-1">
 @foreach($suggestions as $index => $suggestion)
 <button
 type="button"
 wire:click="applySuggestion({{ $index }})"
 class="inline-flex items-center px-2.5 py-1.5 border border-red-300 text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
 >
 {{ $suggestion['description'] }}
 </button>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Option force --}}
 <div class="mt-4">
 <button
 type="button"
 wire:click="toggleForceCreate"
 class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
 >
 @if($forceCreate)
 <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
 </svg>
 Mode force activé
 @else
 <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 14.5c-.77.833.192 2.5 1.732 2.5z"/>
 </svg>
 Ignorer les conflits
 @endif
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 @if($forceCreate)
 <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-yellow-800">Mode force activé</h3>
 <p class="mt-1 text-sm text-yellow-700">Les conflits seront ignorés lors de la sauvegarde.</p>
 </div>
 </div>
 </div>
 @endif

 {{-- Indicateur de validation en cours --}}
 @if($isValidating)
 <div class="rounded-md bg-blue-50 p-4 border border-blue-200">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="animate-spin h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm text-blue-700">Vérification des conflits en cours...</p>
 </div>
 </div>
 </div>
 @endif

 {{-- Formulaire principal --}}
 <form wire:submit="save" class="space-y-6">
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 {{-- Sélection véhicule --}}
 <div>
 <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
 Véhicule <span class="text-red-500">*</span>
 </label>
 <select
 wire:model.live="vehicle_id"
 id="vehicle_id"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('vehicle_id') border-red-300 @enderror"
 aria-describedby="vehicle_id_error"
 required
 >
 <option value="">Sélectionnez un véhicule</option>
 @foreach($vehicleOptions as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 </option>
 @endforeach
 </select>
 @error('vehicle_id')
 <p class="mt-1 text-sm text-red-600" id="vehicle_id_error">{{ $message }}</p>
 @enderror
 </div>

 {{-- Sélection chauffeur --}}
 <div>
 <label for="driver_id" class="block text-sm font-medium text-gray-700">
 Chauffeur <span class="text-red-500">*</span>
 </label>
 <select
 wire:model.live="driver_id"
 id="driver_id"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('driver_id') border-red-300 @enderror"
 aria-describedby="driver_id_error"
 required
 >
 <option value="">Sélectionnez un chauffeur</option>
 @foreach($driverOptions as $driver)
 <option value="{{ $driver->id }}">
 {{ $driver->first_name }} {{ $driver->last_name }}
 @if($driver->license_number)
 ({{ $driver->license_number }})
 @endif
 </option>
 @endforeach
 </select>
 @error('driver_id')
 <p class="mt-1 text-sm text-red-600" id="driver_id_error">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 {{-- Date/heure de début --}}
 <div>
 <label for="start_datetime" class="block text-sm font-medium text-gray-700">
 Date et heure de remise <span class="text-red-500">*</span>
 </label>
 <input
 type="datetime-local"
 wire:model.live="start_datetime"
 id="start_datetime"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('start_datetime') border-red-300 @enderror"
 aria-describedby="start_datetime_error"
 required
 >
 @error('start_datetime')
 <p class="mt-1 text-sm text-red-600" id="start_datetime_error">{{ $message }}</p>
 @enderror
 </div>

 {{-- Date/heure de fin --}}
 <div>
 <label for="end_datetime" class="block text-sm font-medium text-gray-700">
 Date et heure de restitution
 <span class="text-xs text-gray-500">(optionnel pour durée indéterminée)</span>
 </label>
 <input
 type="datetime-local"
 wire:model.live="end_datetime"
 id="end_datetime"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('end_datetime') border-red-300 @enderror"
 aria-describedby="end_datetime_error"
 >
 @error('end_datetime')
 <p class="mt-1 text-sm text-red-600" id="end_datetime_error">{{ $message }}</p>
 @enderror

 {{-- Affichage durée calculée --}}
 @if($this->duration_hours !== null)
 <p class="mt-1 text-sm text-gray-600">
 Durée: {{ $this->formatted_duration }}
 </p>
 @elseif($start_datetime && !$end_datetime)
 <p class="mt-1 text-sm text-blue-600">
 <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 Durée indéterminée
 </p>
 @endif
 </div>
 </div>

 {{-- Actions rapides --}}
 <div class="flex items-center space-x-4">
 <button
 type="button"
 wire:click="suggestNextSlot"
 class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
 {{ empty($vehicle_id) || empty($driver_id) ? 'disabled' : '' }}
 >
 <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
 </svg>
 Suggérer un créneau libre
 </button>

 @if($start_datetime)
 <span class="text-sm text-gray-500">
 à partir du {{ \Carbon\Carbon::parse($start_datetime)->format('d/m/Y H:i') }}
 </span>
 @endif
 </div>

 {{-- Motif de l'affectation --}}
 <div>
 <label for="reason" class="block text-sm font-medium text-gray-700">
 Motif de l'affectation
 </label>
 <input
 type="text"
 wire:model="reason"
 id="reason"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('reason') border-red-300 @enderror"
 placeholder="Ex: Mission commerciale, formation, maintenance..."
 maxlength="500"
 aria-describedby="reason_error"
 >
 @error('reason')
 <p class="mt-1 text-sm text-red-600" id="reason_error">{{ $message }}</p>
 @enderror
 </div>

 {{-- Notes complémentaires --}}
 <div>
 <label for="notes" class="block text-sm font-medium text-gray-700">
 Notes complémentaires
 </label>
 <textarea
 wire:model="notes"
 id="notes"
 rows="3"
 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('notes') border-red-300 @enderror"
 placeholder="Informations supplémentaires, instructions particulières..."
 maxlength="1000"
 aria-describedby="notes_error"
 ></textarea>
 @error('notes')
 <p class="mt-1 text-sm text-red-600" id="notes_error">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-sm text-gray-500">{{ strlen($notes) }}/1000 caractères</p>
 </div>

 {{-- Erreurs générales --}}
 @if($errors->has('business_validation') || $errors->has('save'))
 <div class="rounded-md bg-red-50 p-4 border border-red-200">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
 </svg>
 </div>
 <div class="ml-3">
 <ul class="text-sm text-red-700 space-y-1">
 @foreach($errors->get('business_validation') as $error)
 <li>{{ $error }}</li>
 @endforeach
 @foreach($errors->get('save') as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 </div>
 </div>
 @endif

 {{-- Actions du formulaire --}}
 <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
 <button
 type="button"
 wire:click="$dispatch('close-form')"
 class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
 >
 Annuler
 </button>

 <button
 type="submit"
 class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed
 {{ $hasConflicts && !$forceCreate ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' }}"
 wire:loading.attr="disabled"
 wire:target="save"
 >
 <span wire:loading.remove wire:target="save">
 @if($hasConflicts && !$forceCreate)
 <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
 </svg>
 Créer malgré les conflits
 @else
 {{ $isEditing ? 'Modifier' : 'Créer' }} l'affectation
 @endif
 </span>
 <span wire:loading wire:target="save" class="flex items-center">
 <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Sauvegarde...
 </span>
 </button>
 </div>
 </form>
</div>

{{-- Scripts Alpine.js pour interactions avancées --}}
<script>
document.addEventListener('livewire:init', () => {
 // Écouter les événements de validation
 Livewire.on('conflicts-detected', (event) => {
 // Animation d'alerte
 const alertElement = document.querySelector('[role="alert"]');
 if (alertElement) {
 alertElement.classList.add('animate-pulse');
 setTimeout(() => {
 alertElement.classList.remove('animate-pulse');
 }, 1000);
 }
 });

 Livewire.on('suggestion-applied', (event) => {
 // Notification toast
 showToast(event.message, 'success');
 });

 Livewire.on('slot-suggested', (event) => {
 showToast(event.message, 'info');
 });

 Livewire.on('force-mode-enabled', (event) => {
 showToast(event.message, 'warning');
 });
});

function showToast(message, type = 'info') {
 // Implémentation simple de toast (peut être remplacée par une librairie)
 const toast = document.createElement('div');
 toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 text-white ${
 type === 'success' ? 'bg-green-500' :
 type === 'warning' ? 'bg-yellow-500' :
 type === 'error' ? 'bg-red-500' : 'bg-blue-500'
 }`;
 toast.textContent = message;

 document.body.appendChild(toast);

 setTimeout(() => {
 toast.remove();
 }, 5000);
}
</script>