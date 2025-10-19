<x-app-layout>
 <x-slot name="header">
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ __('Programmer un Plan de Maintenance') }}
 </h2>
 </x-slot>

 <div class="py-12">
 <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
 <script>
 // On prépare les données PHP pour les rendre accessibles à notre script Alpine.js
 const vehiclesData = @json($vehicles->keyBy('id')->map->only('current_mileage'));
 const recurrenceUnitsData = @json($recurrenceUnits);
 </script>

 <div class="p-8 text-gray-900"
 x-data="{
 selectedVehicleId: '{{ old('vehicle_id') }}',
 selectedRecurrenceUnitId: '{{ old('recurrence_unit_id') }}',
 recurrenceValue: {{ old('recurrence_value', 10000) }},
 currentMileageText: 'N/A',
 recurrenceUnitName: '',

 init() {
 this.updateAllDisplays();
 // Initialisation des sélecteurs TomSelect
 let tomVehicle = new TomSelect('#select-vehicle', {create: false});
 let tomMaintenance = new TomSelect('#select-maintenance-type', {create: false});

 // Synchroniser Alpine avec TomSelect pour les véhicules
 tomVehicle.on('change', (value) => { this.selectedVehicleId = value; this.updateAllDisplays(); });
 if(this.selectedVehicleId) { tomVehicle.setValue(this.selectedVehicleId, true); }
 },
 updateAllDisplays() {
 this.updateMileageDisplay();
 this.updateRecurrenceUnitNameDisplay();
 this.calculateAndSetDueDate();
 },
 updateMileageDisplay() {
 const vehicle = vehiclesData[this.selectedVehicleId];
 this.currentMileageText = vehicle ? new Intl.NumberFormat('fr-FR').format(vehicle.current_mileage) + ' km' : 'N/A';
 },
 updateRecurrenceUnitNameDisplay() {
 const unit = recurrenceUnitsData.find(u => u.id == this.selectedRecurrenceUnitId);
 this.recurrenceUnitName = unit ? unit.name : '';
 },
 calculateAndSetDueDate() {
 if (this.recurrenceUnitName === 'Kilomètres') {
 const vehicle = vehiclesData[this.selectedVehicleId];
 const mileageInput = document.getElementById('next_due_mileage');
 if (vehicle && mileageInput && this.recurrenceValue > 0) {
 mileageInput.value = parseInt(vehicle.current_mileage) + parseInt(this.recurrenceValue);
 }
 }
 }
 }"
 x-init="init()">

 <h3 class="text-xl font-semibold text-gray-700 mb-6">Nouveau Plan de Maintenance Préventive</h3>

 @if ($errors->any())
 <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
 <p class="font-bold">Veuillez corriger les erreurs :</p>
 <ul class="mt-2 list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
 </div>
 @endif

 <form method="POST" action="{{ route('admin.maintenance.plans.store') }}">
 @csrf
 <div class="space-y-6">
 <div>
 <label for="select-vehicle" class="block font-medium text-sm text-gray-700">Véhicule <span class="text-red-500">*</span></label>
 <select name="vehicle_id" id="select-vehicle" required>
 <option value="">Sélectionnez un véhicule</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_plate }})</option>
 @endforeach
 </select>
 </div>
 <div>
 <label for="select-maintenance-type" class="block font-medium text-sm text-gray-700">Type de Maintenance <span class="text-red-500">*</span></label>
 <select name="maintenance_type_id" id="select-maintenance-type" placeholder="Rechercher un type..." required>
 <option value="">Sélectionnez un type</option>
 @foreach($maintenanceTypes as $type)
 <option value="{{ $type->id }}" @selected(old('maintenance_type_id') == $type->id)>{{ $type->name }}</option>
 @endforeach
 </select>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label for="recurrence_value" class="block font-medium text-sm text-gray-700">Répéter tous les <span class="text-red-500">*</span></label>
 <input type="number" name="recurrence_value" id="recurrence_value" x-model.number="recurrenceValue" @input.debounce.500ms="calculateAndSetDueDate()" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
 </div>
 <div>
 <label for="recurrence_unit_id" class="block font-medium text-sm text-gray-700">Unité <span class="text-red-500">*</span></label>
 <select name="recurrence_unit_id" id="recurrence_unit_id" x-model="selectedRecurrenceUnitId" @change="updateRecurrenceUnitNameDisplay(); calculateAndSetDueDate();" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
 <option value="">Sélectionnez</option>
 @foreach($recurrenceUnits as $unit)
 <option value="{{ $unit->id }}">{{ $unit->name }}</option>
 @endforeach
 </select>
 </div>
 </div>

 <div x-show="recurrenceUnitName === 'Jours' || recurrenceUnitName === 'Mois'" style="display: none;" x-transition>
 <label for="next_due_date" class="block font-medium text-sm text-gray-700">Prochaine Échéance (Date)</label>
 <p class="text-xs text-gray-500">Laissez vide pour un calcul automatique.</p>
 <input type="date" name="next_due_date" id="next_due_date" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>
 <div x-show="recurrenceUnitName === 'Kilomètres'" style="display: none;" x-transition>
 <label for="next_due_mileage" class="block font-medium text-sm text-gray-700">Prochaine Échéance (Kilométrage)</label>
 <p class="mt-1 text-xs text-gray-500">Référence (km actuel) : <span class="font-semibold" x-text="currentMileageText"></span></p>
 <input type="number" name="next_due_mileage" id="next_due_mileage" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>
 {{-- AJOUT DU CHAMP NOTES --}}
 <div>
 <label for="notes" class="block font-medium text-sm text-gray-700">Notes / Instructions</label>
 <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
 </div>
 </div>

 <div class="mt-8 flex items-center justify-end gap-4 border-t border-gray-200 pt-6">
 <a href="{{ route('admin.maintenance.plans.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
 <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">Enregistrer le Plan</button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</x-app-layout>
