<x-app-layout>
 <x-slot name="header">
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ __('Plans de Maintenance Préventive') }}
 </h2>
 </x-slot>

 {{-- SOLUTION AMÉLIORÉE : Injection des données avant l'initialisation d'Alpine.js --}}
 <script>
 // Données injectées de manière sécurisée
 window.maintenancePlansData = {!! json_encode($plansForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
 window.recurrenceUnits = {!! json_encode($recurrenceUnits->keyBy('id'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!};
 
 // Définition UNIQUE du composant Alpine.js
 document.addEventListener('alpine:init', () => {
 Alpine.data('maintenancePlansPage', () => ({
 // État des modales
 showEditModal: false,
 showLogModal: false,
 
 // Données des plans
 plans: window.maintenancePlansData || {},
 recurrenceUnits: window.recurrenceUnits || {},
 
 // Plan en cours d'édition/logging
 planToEdit: {},
 planToLog: {},
 
 // URLs des formulaires
 editFormUrl: '',
 logFormUrl: '',

 // Initialisation des composants
 init() {
 this.$nextTick(() => {
 this.initTomSelect();
 });
 },

 // Initialisation de TomSelect
 initTomSelect() {
 if (window.TomSelect && this.$refs.vehicleFilter) {
 new TomSelect(this.$refs.vehicleFilter, { 
 create: false, 
 placeholder: 'Rechercher un véhicule...' 
 });
 }
 if (window.TomSelect && this.$refs.typeFilter) {
 new TomSelect(this.$refs.typeFilter, { 
 create: false, 
 placeholder: 'Rechercher un type...' 
 });
 }
 },

 // Ouverture de la modale d'édition
 openEditModal(planId) {
 const plan = this.plans[planId];
 if (!plan) {
 console.error('Plan non trouvé:', planId);
 return;
 }
 
 // Crée une copie profonde pour l'édition
 this.planToEdit = JSON.parse(JSON.stringify(plan));
 
 // S'assurer que l'unité de récurrence est correctement définie
 if (!this.planToEdit.recurrence_unit_id && plan.recurrence_unit_id) {
 this.planToEdit.recurrence_unit_id = plan.recurrence_unit_id;
 }
 
 this.editFormUrl = `/admin/maintenance/plans/${planId}`;
 this.showEditModal = true;
 },

 // Ouverture de la modale de logging
 openLogModal(planId) {
 const plan = this.plans[planId];
 if (!plan) {
 console.error('Plan non trouvé:', planId);
 return;
 }
 
 // Crée une copie profonde pour le logging
 this.planToLog = JSON.parse(JSON.stringify(plan));
 this.logFormUrl = '/admin/maintenance/logs';
 this.showLogModal = true;
 },

 // Fermeture des modales
 closeEditModal() {
 this.showEditModal = false;
 this.planToEdit = {};
 this.editFormUrl = '';
 },

 closeLogModal() {
 this.showLogModal = false;
 this.planToLog = {};
 this.logFormUrl = '';
 },

 // Met à jour le nom de l'unité quand l'ID change dans la modale d'édition
 updateEditRecurrenceUnitName() {
 const unit = this.recurrenceUnits[this.planToEdit.recurrence_unit_id];
 if (unit) {
 if (!this.planToEdit.recurrence_unit) {
 this.planToEdit.recurrence_unit = {};
 }
 this.planToEdit.recurrence_unit.name = unit.name;
 }
 },

 // Obtient le nom de l'unité de récurrence pour l'affichage
 getRecurrenceUnitName(unitId) {
 const unit = this.recurrenceUnits[unitId];
 return unit ? unit.name : '';
 },

 // Formate le kilométrage avec des espaces
 formatMileage(mileage) {
 if (!mileage) return '0';
 return new Intl.NumberFormat('fr-FR').format(mileage);
 },

 // Soumission du formulaire d'édition
 submitEditForm() {
 if (this.editFormUrl && this.$refs.editForm) {
 this.$refs.editForm.submit();
 }
 },

 // Soumission du formulaire de logging
 submitLogForm() {
 if (this.logFormUrl && this.$refs.logForm) {
 this.$refs.logForm.submit();
 }
 }
 }));
 });
 </script>

 <div x-data="maintenancePlansPage" x-init="init()" class="py-12">
 <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

 {{-- Section des Filtres --}}
 <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
 <form action="{{ route('admin.maintenance.plans.index') }}" method="GET">
 <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
 <div class="flex-grow">
 <label for="select-vehicle-filter" class="block text-sm font-medium text-gray-700">Filtrer par Véhicule</label>
 <select x-ref="vehicleFilter" name="vehicle_id" class="mt-1">
 <option value="">Tous les véhicules</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}" @selected(($filters['vehicle_id'] ?? '') == $vehicle->id)>
 {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_plate }})
 </option>
 @endforeach
 </select>
 </div>
 <div class="flex-grow">
 <label for="select-maintenance-type-filter" class="block text-sm font-medium text-gray-700">Filtrer par Type</label>
 <select x-ref="typeFilter" name="maintenance_type_id" class="mt-1">
 <option value="">Tous les types</option>
 @foreach($maintenanceTypes as $type)
 <option value="{{ $type->id }}" @selected(($filters['maintenance_type_id'] ?? '') == $type->id)>{{ $type->name }}</option>
 @endforeach
 </select>
 </div>
 <div class="flex-shrink-0 flex space-x-2">
 <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">Filtrer</button>
 <a href="{{ route('admin.maintenance.plans.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
 </div>
 </div>
 </form>
 </div>

 @if (session('success'))
 <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
 <p class="font-bold">{{ session('success') }}</p>
 </div>
 @endif

 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
 <div class="p-6 text-gray-900">
 <div class="flex justify-between items-center mb-6">
 <h3 class="text-xl font-semibold text-gray-700">Tous les Plans Programmés</h3>
 @can('maintenance.plans.manage')
 <a href="{{ route('admin.maintenance.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
 Ajouter un Plan
 </a>
 @endcan
 </div>

 <div class="overflow-x-auto border border-gray-200 rounded-lg">
 <table class="w-full divide-y divide-gray-200">
 <thead class="bg-gray-100">
 <tr>
 <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Véhicule</th>
 <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Plan</th>
 <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Échéance</th>
 <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse ($plans as $plan)
 <tr class="hover:bg-gray-50">
 <td class="px-6 py-3">
 <div class="text-sm font-medium text-gray-900">{{ $plan->vehicle?->brand }} {{ $plan->vehicle?->model }}</div>
 <div class="text-xs text-gray-500 font-mono">{{ $plan->vehicle?->registration_plate }}</div>
 </td>
 <td class="px-6 py-3">
 <div class="text-sm text-gray-800">{{ $plan->maintenanceType?->name }}</div>
 <div class="text-xs text-gray-500">Tous les {{ $plan->recurrence_value }} {{ $plan->recurrenceUnit?->name }}</div>
 </td>
 <td class="px-6 py-3 whitespace-nowrap text-sm">
 @php
 $isDateOverdue = $plan->next_due_date && $plan->next_due_date->isPast();
 $isMileageOverdue = $plan->next_due_mileage && $plan->vehicle?->current_mileage >= $plan->next_due_mileage;
 $isUrgent = $plan->next_due_date && !$isDateOverdue && $plan->next_due_date->isBefore(now()->addDays(30));
 $overdueClass = 'text-red-600 font-bold';
 $urgentClass = 'text-orange-600 font-semibold';
 $defaultClass = 'text-gray-600';
 @endphp

 @if($plan->next_due_date)
 <div class="{{ $isDateOverdue ? $overdueClass : ($isUrgent ? $urgentClass : $defaultClass) }}">
 {{ $plan->next_due_date->format('d/m/Y') }}
 @if($isDateOverdue) <span class="text-xs">(Dépassé)</span> @endif
 @if($isUrgent) <span class="text-xs">(Urgent)</span> @endif
 </div>
 @endif
 @if($plan->next_due_mileage)
 <div class="text-xs {{ $isMileageOverdue ? $overdueClass : 'text-gray-500' }}">
 (à {{ number_format($plan->next_due_mileage, 0, ',', ' ') }} km)
 @if($isMileageOverdue) <span class="font-bold">(Dépassé)</span> @endif
 </div>
 @endif
 </td>
 <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center justify-end space-x-2">
 @can('maintenance.log')
 <button type="button" @click="openLogModal({{ $plan->id }})" title="Enregistrer une intervention" class="p-2 rounded-full text-gray-400 hover:bg-green-100 hover:text-green-600">
 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </button>
 @endcan
 @can('maintenance.plans.manage')
 <button type="button" @click="openEditModal({{ $plan->id }})" title="Modifier le plan" class="p-2 rounded-full text-gray-400 hover:bg-violet-100 hover:text-violet-600">
 <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
 <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" />
 </svg>
 </button>
 @endcan
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Aucun plan de maintenance trouvé.</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class="mt-6 px-6 pb-6">{{ $plans->withQueryString()->links() }}</div>
 </div>
 </div>
 </div>

 {{-- Modale pour Modifier un Plan --}}
 <div x-show="showEditModal" 
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 transform scale-100"
 x-transition:leave-end="opacity-0 transform scale-90"
 class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" 
 style="display: none;">
 <div @click.away="closeEditModal()" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
 <div class="flex justify-between items-center mb-4">
 <h3 class="text-lg font-semibold text-gray-900">
 Modifier le Plan #<span x-text="planToEdit.id"></span>
 </h3>
 <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600">
 <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
 </svg>
 </button>
 </div>

 {{-- Informations du véhicule --}}
 <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
 <div class="flex items-center">
 <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 <div class="text-sm">
 <span class="font-medium text-blue-900">
 <span x-text="planToEdit.vehicle?.brand"></span> 
 <span x-text="planToEdit.vehicle?.model"></span>
 (<span x-text="planToEdit.vehicle?.registration_plate"></span>)
 </span>
 <span class="text-blue-700 ml-2">
 • Kilométrage actuel : <span x-text="formatMileage(planToEdit.vehicle?.current_mileage)"></span> km
 </span>
 </div>
 </div>
 </div>

 <form x-ref="editForm" :action="editFormUrl" method="POST" class="space-y-4">
 @csrf
 @method('PATCH')

 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-gray-700">Type de Maintenance</label>
 <div class="mt-1 p-2 bg-gray-50 rounded-md text-sm text-gray-600">
 <span x-text="planToEdit.maintenance_type?.name"></span>
 </div>
 </div>

 <div>
 <label for="edit_recurrence_value" class="block text-sm font-medium text-gray-700">Valeur de Récurrence <span class="text-red-500">*</span></label>
 <input id="edit_recurrence_value" 
 type="number" 
 name="recurrence_value" 
 x-model="planToEdit.recurrence_value"
 min="1" 
 required 
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>

 <div class="md:col-span-2">
 <label for="edit_recurrence_unit_id" class="block text-sm font-medium text-gray-700">Unité de Récurrence <span class="text-red-500">*</span></label>
 <select id="edit_recurrence_unit_id" 
 name="recurrence_unit_id" 
 x-model="planToEdit.recurrence_unit_id"
 @change="updateEditRecurrenceUnitName()"
 required 
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 <option value="">Sélectionner une unité</option>
 @foreach($recurrenceUnits as $unit)
 <option value="{{ $unit->id }}">{{ $unit->name }}</option>
 @endforeach
 </select>
 </div>

 <div>
 <label for="edit_next_due_date" class="block text-sm font-medium text-gray-700">Prochaine Échéance (Date)</label>
 <input id="edit_next_due_date" 
 type="date" 
 name="next_due_date" 
 x-model="planToEdit.next_due_date"
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>

 <div>
 <label for="edit_next_due_mileage" class="block text-sm font-medium text-gray-700">Prochaine Échéance (Kilométrage)</label>
 <input id="edit_next_due_mileage" 
 type="number" 
 name="next_due_mileage" 
 x-model="planToEdit.next_due_mileage"
 min="0" 
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>
 </div>

 <div>
 <label for="edit_notes" class="block text-sm font-medium text-gray-700">Notes</label>
 <textarea id="edit_notes" 
 name="notes" 
 x-model="planToEdit.notes"
 rows="3" 
 maxlength="2000"
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm"></textarea>
 </div>

 <div class="flex justify-end space-x-3 pt-4">
 <button type="button" @click="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 Annuler
 </button>
 <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">
 Mettre à jour
 </button>
 </div>
 </form>
 </div>
 </div>

 {{-- Modale pour Enregistrer une Intervention --}}
 <div x-show="showLogModal" 
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 transform scale-100"
 x-transition:leave-end="opacity-0 transform scale-90"
 class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" 
 style="display: none;">
 <div @click.away="closeLogModal()" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
 <div class="sm:flex sm:items-start mb-4">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
 </svg>
 </div>
 <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-grow">
 <h3 class="text-lg font-semibold leading-6 text-gray-900">Enregistrer une Intervention</h3>
 <div class="mt-2">
 <p class="text-sm text-gray-600">
 Intervention de type <strong x-text="planToLog.maintenance_type?.name"></strong> 
 pour le véhicule <strong x-text="planToLog.vehicle?.registration_plate"></strong>.
 </p>
 </div>
 </div>
 <button @click="closeLogModal()" class="text-gray-400 hover:text-gray-600">
 <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
 </svg>
 </button>
 </div>

 {{-- Informations du véhicule --}}
 <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
 <div class="flex items-center">
 <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0" />
 </svg>
 <div class="text-sm">
 <span class="font-medium text-green-900">
 <span x-text="planToLog.vehicle?.brand"></span> 
 <span x-text="planToLog.vehicle?.model"></span>
 (<span x-text="planToLog.vehicle?.registration_plate"></span>)
 </span>
 <span class="text-green-700 ml-2">
 • Kilométrage actuel : <span x-text="formatMileage(planToLog.vehicle?.current_mileage)"></span> km
 </span>
 </div>
 </div>
 </div>

 <form x-ref="logForm" :action="logFormUrl" method="POST" class="space-y-4">
 @csrf
 {{-- Champs cachés pour passer les IDs nécessaires au contrôleur --}}
 <input type="hidden" name="maintenance_plan_id" x-model="planToLog.id">
 <input type="hidden" name="vehicle_id" x-model="planToLog.vehicle_id">
 <input type="hidden" name="maintenance_type_id" x-model="planToLog.maintenance_type_id">

 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label for="performed_on_date" class="block font-medium text-sm text-gray-700">Date de l'intervention <span class="text-red-500">*</span></label>
 <input id="performed_on_date" 
 type="date" 
 name="performed_on_date" 
 value="{{ now()->format('Y-m-d') }}" 
 required 
 class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>

 <div>
 <label for="performed_at_mileage" class="block font-medium text-sm text-gray-700">Kilométrage lors de l'intervention <span class="text-red-500">*</span></label>
 <input id="performed_at_mileage" 
 type="number" 
 name="performed_at_mileage" 
 x-model="planToLog.vehicle?.current_mileage"
 required 
 class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>

 <div>
 <label for="cost" class="block font-medium text-sm text-gray-700">Coût (DA)</label>
 <input id="cost" 
 type="number" 
 name="cost" 
 step="0.01" 
 class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>

 <div>
 <label for="performed_by" class="block font-medium text-sm text-gray-700">Réalisé par (ex: Garage ABC)</label>
 <input id="performed_by" 
 type="text" 
 name="performed_by" 
 class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
 </div>
 </div>

 <div>
 <label for="details" class="block font-medium text-sm text-gray-700">Détails de l'intervention / Notes</label>
 <textarea name="details" 
 id="details" 
 rows="3" 
 class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm"></textarea>
 </div>

 <div class="flex justify-end space-x-3 pt-4">
 <button type="button" @click="closeLogModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 Annuler
 </button>
 <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
 Enregistrer l'Intervention
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
</x-app-layout>
