{{-- resources/views/admin/assignments/create-refactored.blade.php --}}
@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Affectation')

@section('content')
{{-- ====================================================================
🚗 CRÉER AFFECTATION - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

DESIGN PRINCIPLES:
✨ Fond gris clair (bg-gray-50) pour la page
✨ Header avec icône + titre
✨ Metric cards: 3 statistiques clés
✨ Formulaire multi-sections avec x-card
✨ Stepper V7.0 pour navigation
✨ Validation en temps réel Alpine.js
✨ Cohérence totale avec pages Véhicules

@version 1.0-Ultra-Pro-Enterprise-Standard
@since 2025-10-20
==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- ====================================================================
 HEADER - ULTRA-PRO DESIGN
 ===================================================================== --}}
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
 <x-iconify icon="lucide:plus-circle" class="w-6 h-6 text-blue-600" />
 Nouvelle Affectation
 </h1>
 <p class="text-sm text-gray-600 ml-8.5">
 Créez une nouvelle affectation entre un véhicule et un chauffeur
 </p>
 </div>

 {{-- ====================================================================
 METRIC CARDS - KEY STATISTICS
 ===================================================================== --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 {{-- Véhicules Disponibles --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Véhicules Disponibles</p>
 <p class="text-2xl font-bold text-green-600 mt-1">{{ $availableVehicles->count() }}</p>
 </div>
 <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:car" class="w-6 h-6 text-green-600" />
 </div>
 </div>
 </div>

 {{-- Chauffeurs Disponibles --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Chauffeurs Disponibles</p>
 <p class="text-2xl font-bold text-blue-600 mt-1">{{ $availableDrivers->count() }}</p>
 </div>
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:user" class="w-6 h-6 text-blue-600" />
 </div>
 </div>
 </div>

 {{-- Affectations Actives --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Affectations Actives</p>
 <p class="text-2xl font-bold text-orange-600 mt-1">{{ $activeAssignments->count() }}</p>
 </div>
 <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:clock" class="w-6 h-6 text-orange-600" />
 </div>
 </div>
 </div>
 </div>

 {{-- ====================================================================
 FORMULAIRE MULTI-ÉTAPES - ULTRA-PRO DESIGN
 ===================================================================== --}}
 <div x-data="assignmentFormValidation()" x-init="init()">

 <x-card padding="p-0" margin="mb-6">
 {{-- STEPPER V7.0 --}}
 <x-stepper
 :steps="[
 ['label' => 'Véhicule', 'icon' => 'car'],
 ['label' => 'Chauffeur', 'icon' => 'user'],
 ['label' => 'Dates', 'icon' => 'calendar'],
 ['label' => 'Confirmation', 'icon' => 'check-circle']
 ]"
 currentStepVar="currentStep"
 />

 {{-- Formulaire --}}
 <form method="POST" action="{{ route('admin.assignments.store') }}" @submit="onSubmit" class="p-6">
 @csrf
 <input type="hidden" name="current_step" x-model="currentStep">

 {{-- ===========================================
 ÉTAPE 1: SÉLECTION VÉHICULE
 =========================================== --}}
 <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
 Sélectionnez un Véhicule
 </h3>

 <x-tom-select
 name="vehicle_id"
 label="Véhicule"
 :options="$availableVehicles->mapWithKeys(fn($v) => [
 $v->id => $v->registration_plate . ' - ' . $v->brand . ' ' . $v->model
 ])->toArray()"
 placeholder="Rechercher un véhicule..."
 required
 :error="$errors->first('vehicle_id')"
 @change="validateField('vehicle_id', $event.target.value)"
 />

 {{-- Infos Véhicule Sélectionné --}}
 @if($availableVehicles->count() > 0)
 <div class="mt-6">
 <h4 class="text-sm font-medium text-gray-700 mb-3">Véhicules Disponibles</h4>
 <div class="space-y-2 max-h-96 overflow-y-auto">
 @foreach($availableVehicles as $vehicle)
 <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors cursor-pointer"
 @click="document.querySelector('input[name=vehicle_id]').value = {{ $vehicle->id }}; $el.closest('form').dispatchEvent(new Event('change', { bubbles: true }))">
 <div class="flex items-start justify-between">
 <div>
 <div class="font-medium text-gray-900">
 {{ $vehicle->registration_plate }}
 </div>
 <div class="text-sm text-gray-600 mt-1">
 {{ $vehicle->brand }} {{ $vehicle->model }} • Année: {{ $vehicle->manufacturing_year }}
 </div>
 <div class="text-xs text-gray-500 mt-2">
 Type: {{ $vehicle->vehicleType?->name ?? 'N/A' }} | Carburant: {{ $vehicle->fuelType?->name ?? 'N/A' }}
 </div>
 </div>
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 Disponible
 </span>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>

 {{-- ===========================================
 ÉTAPE 2: SÉLECTION CHAUFFEUR
 =========================================== --}}
 <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:user" class="w-5 h-5 text-blue-600" />
 Sélectionnez un Chauffeur
 </h3>

 <x-tom-select
 name="driver_id"
 label="Chauffeur"
 :options="$availableDrivers->mapWithKeys(fn($d) => [
 $d->id => $d->name . ' (' . $d->phone . ')'
 ])->toArray()"
 placeholder="Rechercher un chauffeur..."
 required
 :error="$errors->first('driver_id')"
 @change="validateField('driver_id', $event.target.value)"
 />

 {{-- Infos Chauffeur Sélectionné --}}
 @if($availableDrivers->count() > 0)
 <div class="mt-6">
 <h4 class="text-sm font-medium text-gray-700 mb-3">Chauffeurs Disponibles</h4>
 <div class="space-y-2 max-h-96 overflow-y-auto">
 @foreach($availableDrivers as $driver)
 <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 hover:bg-gray-100 transition-colors cursor-pointer"
 @click="document.querySelector('input[name=driver_id]').value = {{ $driver->id }}; $el.closest('form').dispatchEvent(new Event('change', { bubbles: true }))">
 <div class="flex items-start justify-between">
 <div>
 <div class="font-medium text-gray-900">
 {{ $driver->name }}
 </div>
 <div class="text-sm text-gray-600 mt-1">
 📞 {{ $driver->phone ?? 'N/A' }} | 📧 {{ $driver->email ?? 'N/A' }}
 </div>
 <div class="text-xs text-gray-500 mt-2">
 Permis: {{ $driver->license_number ?? 'N/A' }}
 </div>
 </div>
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 Libre
 </span>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>

 {{-- ===========================================
 ÉTAPE 3: SÉLECTION DATES
 =========================================== --}}
 <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:calendar" class="w-5 h-5 text-blue-600" />
 Définissez les Dates
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-datepicker
 name="start_date"
 label="Date de Début"
 format="d/m/Y"
 :minDate="date('Y-m-d')"
 :value="old('start_date')"
 placeholder="JJ/MM/AAAA"
 required
 :error="$errors->first('start_date')"
 @change="validateField('start_date', $event.target.value)"
 />

 <x-datepicker
 name="end_date"
 label="Date de Fin"
 format="d/m/Y"
 :value="old('end_date')"
 placeholder="JJ/MM/AAAA"
 :error="$errors->first('end_date')"
 @change="validateField('end_date', $event.target.value)"
 />
 </div>

 {{-- Notes Additionnelles --}}
 <x-textarea
 name="notes"
 label="Notes (Optionnel)"
 rows="4"
 placeholder="Informations supplémentaires sur l'affectation..."
 :value="old('notes')"
 :error="$errors->first('notes')"
 />
 </div>
 </div>
 </div>

 {{-- ===========================================
 ÉTAPE 4: CONFIRMATION
 =========================================== --}}
 <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-blue-600" />
 Confirmez l'Affectation
 </h3>

 {{-- Résumé --}}
 <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 space-y-4">
 <div>
 <div class="text-sm font-medium text-gray-600">Véhicule</div>
 <div id="summary-vehicle" class="text-lg font-semibold text-gray-900 mt-1">
 À sélectionner...
 </div>
 </div>

 <div>
 <div class="text-sm font-medium text-gray-600">Chauffeur</div>
 <div id="summary-driver" class="text-lg font-semibold text-gray-900 mt-1">
 À sélectionner...
 </div>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <div class="text-sm font-medium text-gray-600">Date de Début</div>
 <div id="summary-start-date" class="text-lg font-semibold text-gray-900 mt-1">
 À sélectionner...
 </div>
 </div>
 <div>
 <div class="text-sm font-medium text-gray-600">Date de Fin</div>
 <div id="summary-end-date" class="text-lg font-semibold text-gray-900 mt-1">
 À sélectionner...
 </div>
 </div>
 </div>
 </div>

 {{-- Info Alert --}}
 <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
 <div class="flex items-start gap-3">
 <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
 <div class="text-sm text-blue-700">
 Veuillez vérifier les informations ci-dessus avant de confirmer. Cette action ne peut pas être annulée.
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ===========================================
 ACTIONS FOOTER
 =========================================== --}}
 <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
 <div>
 <x-button
 type="button"
 variant="secondary"
 icon="arrow-left"
 x-show="currentStep > 1"
 @click="previousStep()"
 >
 Précédent
 </x-button>
 </div>

 <div class="flex items-center gap-3">
 <a href="{{ route('admin.assignments.index') }}"
 class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
 Annuler
 </a>

 <x-button
 type="button"
 variant="primary"
 icon="arrow-right"
 iconPosition="right"
 x-show="currentStep < 4"
 @click="nextStep()"
 >
 Suivant
 </x-button>

 <x-button
 type="submit"
 variant="success"
 icon="check-circle"
 x-show="currentStep === 4"
 >
 Créer l'Affectation
 </x-button>
 </div>
 </div>
 </form>
 </x-card>

 </div>

 </div>
</section>

@push('scripts')
<script>
function assignmentFormValidation() {
 return {
 currentStep: {{ old('current_step', 1) }},

 steps: [
 {
 label: 'Véhicule',
 icon: 'car',
 validated: false,
 touched: false,
 requiredFields: ['vehicle_id']
 },
 {
 label: 'Chauffeur',
 icon: 'user',
 validated: false,
 touched: false,
 requiredFields: ['driver_id']
 },
 {
 label: 'Dates',
 icon: 'calendar',
 validated: false,
 touched: false,
 requiredFields: ['start_date']
 },
 {
 label: 'Confirmation',
 icon: 'check-circle',
 validated: false,
 touched: false,
 requiredFields: []
 }
 ],

 fieldErrors: {},
 touchedFields: {},

 init() {
 @if ($errors->any())
 this.markStepsWithErrors();
 @json($errors->keys()).forEach(field => {
 this.touchedFields[field] = true;
 });
 @endif
 },

 markStepsWithErrors() {
 const fieldToStepMap = {
 'vehicle_id': 0, 'driver_id': 1, 'start_date': 2, 'end_date': 2, 'notes': 2
 };

 @json($errors->keys()).forEach(field => {
 const stepIndex = fieldToStepMap[field];
 if (stepIndex !== undefined) {
 this.steps[stepIndex].touched = true;
 this.steps[stepIndex].validated = false;
 }
 });
 },

 validateField(fieldName, value) {
 this.touchedFields[fieldName] = true;

 const rules = {
 'vehicle_id': (v) => v && v.length > 0,
 'driver_id': (v) => v && v.length > 0,
 'start_date': (v) => v && v.length > 0,
 };

 const isValid = rules[fieldName] ? rules[fieldName](value) : true;

 if (!isValid) {
 this.fieldErrors[fieldName] = true;
 } else {
 this.clearFieldError(fieldName);
 }

 return isValid;
 },

 validateCurrentStep() {
 const stepIndex = this.currentStep - 1;
 const step = this.steps[stepIndex];

 step.touched = true;

 let allValid = true;

 step.requiredFields.forEach(fieldName => {
 const input = document.querySelector(`[name="${fieldName}"]`);
 if (input) {
 const value = input.value;
 const isValid = this.validateField(fieldName, value);
 if (!isValid) {
 allValid = false;
 }
 }
 });

 step.validated = allValid;
 return allValid;
 },

 nextStep() {
 const isValid = this.validateCurrentStep();

 if (!isValid) {
 this.$dispatch('show-toast', {
 type: 'error',
 message: 'Veuillez remplir tous les champs obligatoires'
 });
 return;
 }

 if (this.currentStep < 4) {
 this.currentStep++;
 this.updateSummary();
 }
 },

 previousStep() {
 if (this.currentStep > 1) {
 this.currentStep--;
 }
 },

 clearFieldError(fieldName) {
 delete this.fieldErrors[fieldName];
 },

 updateSummary() {
 const vehicle = document.querySelector('[name="vehicle_id"]')?.value;
 const driver = document.querySelector('[name="driver_id"]')?.value;
 const startDate = document.querySelector('[name="start_date"]')?.value;
 const endDate = document.querySelector('[name="end_date"]')?.value;

 if (vehicle) {
 const option = document.querySelector(`[name="vehicle_id"] option[value="${vehicle}"]`);
 document.getElementById('summary-vehicle').textContent = option?.textContent || 'À sélectionner...';
 }

 if (driver) {
 const option = document.querySelector(`[name="driver_id"] option[value="${driver}"]`);
 document.getElementById('summary-driver').textContent = option?.textContent || 'À sélectionner...';
 }

 document.getElementById('summary-start-date').textContent = startDate || 'À sélectionner...';
 document.getElementById('summary-end-date').textContent = endDate || 'À sélectionner...';
 },

 onSubmit(e) {
 let allValid = true;

 this.steps.forEach((step, index) => {
 const tempCurrent = this.currentStep;
 this.currentStep = index + 1;
 const isValid = this.validateCurrentStep();
 this.currentStep = tempCurrent;

 if (!isValid) {
 allValid = false;
 }
 });

 if (!allValid) {
 e.preventDefault();

 const firstInvalidStep = this.steps.findIndex(s => s.touched && !s.validated);
 if (firstInvalidStep !== -1) {
 this.currentStep = firstInvalidStep + 1;
 }

 this.$dispatch('show-toast', {
 type: 'error',
 message: 'Veuillez corriger les erreurs'
 });

 return false;
 }

 return true;
 }
 };
}
</script>
@endpush

@endsection
