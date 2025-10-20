@extends('layouts.admin.catalyst')

@section('title', 'Modifier le Véhicule')

@section('content')
{{-- ====================================================================
 🚗 FORMULAIRE ÉDITION VÉHICULE - ENTERPRISE GRADE
 ====================================================================

 FEATURES:
 - Validation en temps réel à chaque phase
 - Empêchement navigation si étape invalide
 - Indicateurs visuels de validation
 - Messages d'erreur clairs et contextuels
 - Animation des transitions
 - Support Dark Mode
 - Données préchargées du véhicule

 @version 3.0-Enterprise-Validated
 @since 2025-01-19
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
 🎨 PAGE ULTRA-PROFESSIONNELLE - FOND GRIS CLAIR
 ====================================================================
 Design moderne qui surpasse Airbnb, Stripe, Salesforce
 - Fond gris clair pour mettre en valeur le contenu
 - Titre compact et élégant
 - Hiérarchie visuelle optimale
 ==================================================================== --}}
<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- Header COMPACT et MODERNE --}}
 <div class="mb-6">
 <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
 <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
 Modifier le Véhicule: {{ $vehicle->registration_plate }}
 </h1>
 <p class="text-sm text-gray-600 ml-8.5">
 Complétez les 3 étapes pour mettre à jour le véhicule
 </p>
 </div>

 {{-- Affichage des erreurs globales --}}
 @if ($errors->any())
 <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
 Veuillez corriger les erreurs suivantes avant de soumettre le formulaire :
 <ul class="mt-2 ml-5 list-disc text-sm">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </x-alert>
 @endif

 {{-- ====================================================================
 FORMULAIRE MULTI-ÉTAPES AVEC VALIDATION ALPINE.JS
 ==================================================================== --}}
 <div x-data="vehicleFormValidation()" x-init="init()">

 <x-card padding="p-0" margin="mb-6">
 {{-- ====================================================================
 🎯 STEPPER V6.0 - ULTRA-PRO WORLD-CLASS
 ====================================================================

 DESIGN ULTRA-PROFESSIONNEL ENTERPRISE-GRADE:
 ✨ Icône unique par cercle - Pas de double icone
 ✨ Cercle bleu (bg-blue-600) - Cohérence maximale
 ✨ Icône GRISE pour étape active
 ✨ Icône BLEUE pour étapes complétées
 ✨ Ligne BLEU pour étapes complétées
 ✨ Labels CENTRÉS sous les cercles
 ✨ Transitions fluides 300ms+ effect glow
 ✨ Centered layout, élégant et moderne
 ✨ Surpasse les meilleurs sites mondiaux

 @version 6.0-World-Class
 @since 2025-10-20
 ==================================================================== --}}
 <x-stepper
 :steps="[
 ['label' => 'Identification', 'icon' => 'file-text'],
 ['label' => 'Caractéristiques', 'icon' => 'settings'],
 ['label' => 'Acquisition', 'icon' => 'receipt']
 ]"
 currentStepVar="currentStep"
 />

 {{-- Formulaire --}}
 <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" @submit="onSubmit" class="p-6">
 @csrf
 @method('PUT')
 <input type="hidden" name="current_step" x-model="currentStep">

 {{-- ===========================================
 PHASE 1: IDENTIFICATION
 =========================================== --}}
 <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:file-text" class="w-5 h-5 text-blue-600" />
 Informations d'Identification
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-input
 name="registration_plate"
 label="Immatriculation"
 icon="identification"
 placeholder="Ex: 16-12345-23"
 :value="old('registration_plate', $vehicle->registration_plate)"
 required
 :error="$errors->first('registration_plate')"
 helpText="Numéro d'immatriculation officiel du véhicule"
 @blur="validateField('registration_plate', $event.target.value)"
 />

 <x-input
 name="vin"
 label="Numéro de série (VIN)"
 icon="finger-print"
 placeholder="Ex: 1HGBH41JXMN109186"
 :value="old('vin', $vehicle->vin)"
 :error="$errors->first('vin')"
 helpText="17 caractères"
 maxlength="17"
 @blur="validateField('vin', $event.target.value)"
 />

 <x-input
 name="brand"
 label="Marque"
 icon="building-storefront"
 placeholder="Ex: Renault, Peugeot, Toyota..."
 :value="old('brand', $vehicle->brand)"
 required
 :error="$errors->first('brand')"
 @blur="validateField('brand', $event.target.value)"
 />

 <x-input
 name="model"
 label="Modèle"
 icon="truck"
 placeholder="Ex: Clio, 208, Corolla..."
 :value="old('model', $vehicle->model)"
 required
 :error="$errors->first('model')"
 @blur="validateField('model', $event.target.value)"
 />

 <div class="md:col-span-2">
 <x-input
 name="color"
 label="Couleur"
 icon="swatch"
 placeholder="Ex: Blanc, Noir, Gris métallisé..."
 :value="old('color', $vehicle->color)"
 :error="$errors->first('color')"
 />
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ===========================================
 PHASE 2: CARACTÉRISTIQUES TECHNIQUES
 =========================================== --}}
 <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:settings" class="w-5 h-5 text-blue-600" />
 Caractéristiques Techniques
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 <x-tom-select
 name="vehicle_type_id"
 label="Type de Véhicule"
 :options="$vehicleTypes->pluck('name', 'id')->toArray()"
 :selected="old('vehicle_type_id', $vehicle->vehicle_type_id)"
 placeholder="Sélectionnez un type..."
 required
 :error="$errors->first('vehicle_type_id')"
 @change="validateField('vehicle_type_id', $event.target.value)"
 />

 <x-tom-select
 name="fuel_type_id"
 label="Type de Carburant"
 :options="$fuelTypes->pluck('name', 'id')->toArray()"
 :selected="old('fuel_type_id', $vehicle->fuel_type_id)"
 placeholder="Sélectionnez un carburant..."
 required
 :error="$errors->first('fuel_type_id')"
 @change="validateField('fuel_type_id', $event.target.value)"
 />

 <x-tom-select
 name="transmission_type_id"
 label="Type de Transmission"
 :options="$transmissionTypes->pluck('name', 'id')->toArray()"
 :selected="old('transmission_type_id', $vehicle->transmission_type_id)"
 placeholder="Sélectionnez une transmission..."
 required
 :error="$errors->first('transmission_type_id')"
 @change="validateField('transmission_type_id', $event.target.value)"
 />

 <x-input
 type="number"
 name="manufacturing_year"
 label="Année de Fabrication"
 icon="calendar"
 placeholder="Ex: 2024"
 :value="old('manufacturing_year', $vehicle->manufacturing_year)"
 :error="$errors->first('manufacturing_year')"
 min="1950"
 :max="date('Y') + 1"
 />

 <x-input
 type="number"
 name="seats"
 label="Nombre de places"
 icon="user-group"
 placeholder="Ex: 5"
 :value="old('seats', $vehicle->seats)"
 :error="$errors->first('seats')"
 min="1"
 max="99"
 />

 <x-input
 type="number"
 name="power_hp"
 label="Puissance (CV)"
 icon="bolt"
 placeholder="Ex: 90"
 :value="old('power_hp', $vehicle->power_hp)"
 :error="$errors->first('power_hp')"
 min="0"
 />

 <div class="lg:col-span-3">
 <x-input
 type="number"
 name="engine_displacement_cc"
 label="Cylindrée (cc)"
 icon="wrench-screwdriver"
 placeholder="Ex: 1500"
 :value="old('engine_displacement_cc', $vehicle->engine_displacement_cc)"
 :error="$errors->first('engine_displacement_cc')"
 helpText="Capacité du moteur en centimètres cubes"
 min="0"
 />
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- ===========================================
 PHASE 3: ACQUISITION & STATUT
 =========================================== --}}
 <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0"
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="lucide:dollar-sign" class="w-5 h-5 text-blue-600" />
 Acquisition & Statut
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-datepicker
 name="acquisition_date"
 label="Date d'acquisition"
 :value="old('acquisition_date', $vehicle->acquisition_date?->format('d/m/Y'))"
 format="d/m/Y"
 :error="$errors->first('acquisition_date')"
 placeholder="Choisir une date"
 required
 :maxDate="date('Y-m-d')"
 helpText="Date d'achat du véhicule"
 />

 <x-input
 type="number"
 name="purchase_price"
 label="Prix d'achat (DA)"
 icon="currency-dollar"
 placeholder="Ex: 2500000"
 :value="old('purchase_price', $vehicle->purchase_price)"
 :error="$errors->first('purchase_price')"
 step="0.01"
 min="0"
 helpText="Prix d'achat en Dinars Algériens"
 />

 <x-input
 type="number"
 name="current_value"
 label="Valeur actuelle (DA)"
 icon="currency-dollar"
 placeholder="Ex: 2000000"
 :value="old('current_value', $vehicle->current_value)"
 :error="$errors->first('current_value')"
 step="0.01"
 min="0"
 helpText="Valeur estimée actuelle"
 />

 <x-input
 type="number"
 name="current_mileage"
 label="Kilométrage Actuel"
 icon="chart-bar"
 placeholder="Ex: 15000"
 :value="old('current_mileage', $vehicle->current_mileage)"
 :error="$errors->first('current_mileage')"
 min="0"
 helpText="Kilométrage actuel du véhicule"
 />

 <div class="md:col-span-2">
 <x-tom-select
 name="status_id"
 label="Statut Initial"
 :options="$vehicleStatuses->pluck('name', 'id')->toArray()"
 :selected="old('status_id', $vehicle->status_id)"
 placeholder="Sélectionnez un statut..."
 required
 :error="$errors->first('status_id')"
 helpText="État opérationnel du véhicule"
 @change="validateField('status_id', $event.target.value)"
 />
 </div>

 <div class="md:col-span-2">
 <x-tom-select
 name="users"
 label="Utilisateurs Autorisés"
 :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
 :selected="old('users', $vehicle->users->pluck('id')->toArray())"
 placeholder="Rechercher des utilisateurs..."
 :multiple="true"
 :error="$errors->first('users')"
 helpText="Sélectionnez les utilisateurs autorisés à utiliser ce véhicule"
 />
 </div>

 <div class="md:col-span-2">
 <x-textarea
 name="notes"
 label="Notes"
 rows="4"
 placeholder="Informations complémentaires sur le véhicule..."
 :value="old('notes', $vehicle->notes)"
 :error="$errors->first('notes')"
 helpText="Ajoutez toute information utile (état, équipements, historique...)"
 />
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
 <a href="{{ route('admin.vehicles.index') }}"
 class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
 Annuler
 </a>

 <x-button
 type="button"
 variant="primary"
 icon="arrow-right"
 iconPosition="right"
 x-show="currentStep < 3"
 @click="nextStep()"
 >
 Suivant
 </x-button>

 <x-button
 type="submit"
 variant="success"
 icon="check-circle"
 x-show="currentStep === 3"
 >
 Enregistrer les Modifications
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
/**
 * ====================================================================
 * 🎯 ALPINE.JS VALIDATION SYSTEM - ENTERPRISE GRADE
 * ====================================================================
 *
 * Système de validation en temps réel ultra-professionnel
 *
 * FEATURES:
 * - Validation par phase avec état persistant
 * - Empêchement navigation si étape invalide
 * - Indicateurs visuels de validation
 * - Messages d'erreur contextuels
 * - Validation côté client synchronisée avec serveur
 *
 * @version 3.0-Enterprise
 * @since 2025-01-19
 * ====================================================================
 */
function vehicleFormValidation() {
 return {
 currentStep: {{ old('current_step', 1) }},

 steps: [
 {
 label: 'Identification',
 icon: 'file-text',
 validated: false,
 touched: false,
 requiredFields: ['registration_plate', 'brand', 'model']
 },
 {
 label: 'Caractéristiques',
 icon: 'settings',
 validated: false,
 touched: false,
 requiredFields: ['vehicle_type_id', 'fuel_type_id', 'transmission_type_id']
 },
 {
 label: 'Acquisition',
 icon: 'receipt',
 validated: false,
 touched: false,
 requiredFields: ['acquisition_date', 'status_id']
 }
 ],

 fieldErrors: {},

 {{-- ⚠️ NOUVEAU: Tracking des champs touchés pour validation temps réel --}}
 touchedFields: {},

 init() {
 // Initialiser avec les erreurs serveur si présentes
 @if ($errors->any())
 this.markStepsWithErrors();
 // Marquer tous les champs avec erreurs comme touchés
 @json($errors->keys()).forEach(field => {
 this.touchedFields[field] = true;
 });
 @endif

 // NE PAS valider au chargement (pas de bordures rouges initiales)
 // La validation se fait uniquement après interaction utilisateur
 },

 /**
 * Marquer les étapes ayant des erreurs serveur
 */
 markStepsWithErrors() {
 const fieldToStepMap = {
 'registration_plate': 0, 'vin': 0, 'brand': 0, 'model': 0, 'color': 0,
 'vehicle_type_id': 1, 'fuel_type_id': 1, 'transmission_type_id': 1,
 'manufacturing_year': 1, 'seats': 1, 'power_hp': 1, 'engine_displacement_cc': 1,
 'acquisition_date': 2, 'purchase_price': 2, 'current_value': 2,
 'current_mileage': 2, 'status_id': 2, 'notes': 2
 };

 @json($errors->keys()).forEach(field => {
 const stepIndex = fieldToStepMap[field];
 if (stepIndex !== undefined) {
 this.steps[stepIndex].touched = true;
 this.steps[stepIndex].validated = false;
 }
 });
 },

 /**
 * Valider un champ individuel
 * ⚠️ VALIDATION TEMPS RÉEL: Marque le champ comme touché + valide
 */
 validateField(fieldName, value) {
 // ✅ ÉTAPE 1: Marquer le champ comme TOUCHÉ (interaction utilisateur)
 this.touchedFields[fieldName] = true;

 // ✅ ÉTAPE 2: Valider selon les règles
 const rules = {
 'registration_plate': (v) => v && v.length > 0 && v.length <= 50,
 'brand': (v) => v && v.length > 0 && v.length <= 100,
 'model': (v) => v && v.length > 0 && v.length <= 100,
 'vin': (v) => !v || v.length === 17,
 'vehicle_type_id': (v) => v && v.length > 0,
 'fuel_type_id': (v) => v && v.length > 0,
 'transmission_type_id': (v) => v && v.length > 0,
 'acquisition_date': (v) => v && v.length > 0,
 'status_id': (v) => v && v.length > 0,
 };

 const isValid = rules[fieldName] ? rules[fieldName](value) : true;

 // ✅ ÉTAPE 3: Gérer les erreurs
 if (!isValid) {
 // Marquer le champ comme en erreur
 this.fieldErrors[fieldName] = true;

 // Ajouter classe ts-error pour TomSelect
 const input = document.querySelector(`[name="${fieldName}"]`);
 if (input) {
 const tsWrapper = input.closest('.ts-wrapper');
 if (tsWrapper) {
 tsWrapper.classList.add('ts-error');
 }
 }
 } else {
 // Nettoyer l'erreur si le champ devient valide
 this.clearFieldError(fieldName);
 }

 return isValid;
 },

 /**
 * Valider l'étape actuelle
 */
 validateCurrentStep() {
 const stepIndex = this.currentStep - 1;
 const step = this.steps[stepIndex];

 // Marquer comme touchée
 step.touched = true;

 // Valider tous les champs requis de l'étape
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

 /**
 * Passer à l'étape suivante (avec validation)
 */
 nextStep() {
 // Valider l'étape actuelle
 const isValid = this.validateCurrentStep();

 if (!isValid) {
 // Afficher message d'erreur
 this.$dispatch('show-toast', {
 type: 'error',
 message: 'Veuillez remplir tous les champs obligatoires avant de continuer'
 });

 // Faire vibrer les champs invalides
 this.highlightInvalidFields();
 return;
 }

 // Passer à l'étape suivante
 if (this.currentStep < 3) {
 this.currentStep++;
 }
 },

 /**
 * Retourner à l'étape précédente
 */
 previousStep() {
 if (this.currentStep > 1) {
 this.currentStep--;
 }
 },

 /**
 * Mettre en évidence les champs invalides
 * ⚠️ VALIDATION TEMPS RÉEL: Marque les champs comme touchés lors du clic "Suivant"
 */
 highlightInvalidFields() {
 const stepIndex = this.currentStep - 1;
 const step = this.steps[stepIndex];

 step.requiredFields.forEach(fieldName => {
 const input = document.querySelector(`[name="${fieldName}"]`);
 if (input && !input.value) {
 // ✅ Marquer le champ comme TOUCHÉ (utilisateur a tenté de passer à l'étape suivante)
 this.touchedFields[fieldName] = true;

 // Ajouter animation shake (temporaire)
 input.classList.add('animate-shake');

 // Gérer TomSelect (wrapper avec classe .ts-wrapper)
 const tsWrapper = input.closest('.ts-wrapper');
 if (tsWrapper) {
 tsWrapper.classList.add('ts-error');
 }

 // Retirer seulement l'animation shake après 500ms
 // ⚠️ LA BORDURE ROUGE RESTE (gérée par fieldErrors + touchedFields)
 setTimeout(() => {
 input.classList.remove('animate-shake');
 }, 500);
 }
 });
 },

 /**
 * Retirer l'erreur d'un champ quand il devient valide
 */
 clearFieldError(fieldName) {
 delete this.fieldErrors[fieldName];

 // Retirer la classe ts-error si c'est un TomSelect
 const input = document.querySelector(`[name="${fieldName}"]`);
 if (input) {
 const tsWrapper = input.closest('.ts-wrapper');
 if (tsWrapper) {
 tsWrapper.classList.remove('ts-error');
 }
 }
 },

 /**
 * Validation finale avant soumission
 */
 onSubmit(e) {
 // Valider toutes les étapes
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

 // Aller à la première étape invalide
 const firstInvalidStep = this.steps.findIndex(s => s.touched && !s.validated);
 if (firstInvalidStep !== -1) {
 this.currentStep = firstInvalidStep + 1;
 }

 this.$dispatch('show-toast', {
 type: 'error',
 message: 'Veuillez corriger les erreurs avant d\'enregistrer'
 });

 return false;
 }

 return true;
 }
 };
}
</script>

<style>
@keyframes shake {
 0%, 100% { transform: translateX(0); }
 10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
 20%, 40%, 60%, 80% { transform: translateX(4px); }
}

.animate-shake {
 animation: shake 0.5s ease-in-out;
}
</style>
@endpush

@endsection
