@extends('layouts.admin.catalyst')

@section('title', 'Modifier le Chauffeur - ' . $driver->first_name . ' ' . $driver->last_name)

@section('content')
{{-- ====================================================================
 👤 FORMULAIRE ÉDITION CHAUFFEUR - ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Design aligné 100% avec create-refactored et vehicles/edit
 - Validation en temps réel
 - Pré-remplissage automatique des données
 - Composants: x-iconify, x-input, x-select, x-datepicker, x-stepper
 
 @version 2.0-Enterprise
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
 class="fixed top-4 right-4 z-50 max-w-md">
 <x-alert type="success" title="Succès" dismissible>
 {{ session('success') }}
 </x-alert>
 </div>
@endif

<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- Header COMPACT et MODERNE --}}
 <div class="mb-6">
 {{-- Breadcrumb --}}
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <x-iconify icon="heroicons:home" class="w-4 h-4" />
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
 Gestion des Chauffeurs
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <a href="{{ route('admin.drivers.show', $driver) }}" class="hover:text-blue-600 transition-colors">
 {{ $driver->first_name }} {{ $driver->last_name }}
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <span class="font-semibold text-gray-900">Modifier</span>
 </nav>

 <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
 <x-iconify icon="heroicons:pencil" class="w-6 h-6 text-blue-600" />
 Modifier le Chauffeur
 </h1>
 <p class="text-sm text-gray-600 ml-8.5">
 {{ $driver->first_name }} {{ $driver->last_name }} • Matricule: {{ $driver->employee_number ?? 'N/A' }}
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
 <div x-data="driverFormValidation()" x-init="init()">

 <x-card padding="p-0" margin="mb-6">
 {{-- Stepper --}}
 <x-stepper
 :steps="[
 ['label' => 'Informations Personnelles', 'icon' => 'user'],
 ['label' => 'Informations Professionnelles', 'icon' => 'briefcase'],
 ['label' => 'Permis de Conduire', 'icon' => 'identification'],
 ['label' => 'Compte & Urgence', 'icon' => 'link']
 ]"
 currentStepVar="currentStep"
 />

 {{-- Formulaire --}}
 <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" enctype="multipart/form-data" @submit="onSubmit" class="p-6">
 @csrf
 @method('PUT')
 <input type="hidden" name="current_step" x-model="currentStep">

 {{-- PHASE 1: INFORMATIONS PERSONNELLES --}}
 <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:user" class="w-5 h-5 text-blue-600" />
 Informations Personnelles
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-input
 name="first_name"
 label="Prénom"
 icon="user"
 placeholder="Ex: Ahmed"
 :value="old('first_name', $driver->first_name)"
 required
 :error="$errors->first('first_name')"
 @blur="validateField('first_name', $event.target.value)"
 />

 <x-input
 name="last_name"
 label="Nom"
 icon="user"
 placeholder="Ex: Benali"
 :value="old('last_name', $driver->last_name)"
 required
 :error="$errors->first('last_name')"
 @blur="validateField('last_name', $event.target.value)"
 />

 <x-datepicker
 name="birth_date"
 label="Date de naissance"
 :value="old('birth_date', $driver->birth_date ? $driver->birth_date->format('Y-m-d') : '')"
 format="d/m/Y"
 :error="$errors->first('birth_date')"
 placeholder="Choisir une date"
 :maxDate="date('Y-m-d')"
 />

 <x-input
 name="personal_phone"
 type="tel"
 label="Téléphone personnel"
 icon="phone"
 placeholder="Ex: 0555123456"
 :value="old('personal_phone', $driver->personal_phone)"
 :error="$errors->first('personal_phone')"
 />

 <x-input
 name="personal_email"
 type="email"
 label="Email personnel"
 icon="envelope"
 placeholder="Ex: ahmed.benali@email.com"
 :value="old('personal_email', $driver->personal_email)"
 :error="$errors->first('personal_email')"
 />

 {{-- Groupe sanguin avec affichage de la valeur actuelle --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Groupe sanguin
 @if($driver->blood_type)
 <span class="ml-2 text-xs text-gray-500 font-normal">
 (Actuel: {{ $driver->blood_type }})
 </span>
 @endif
 </label>
 <select 
 name="blood_type"
 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('blood_type') border-red-500 @enderror">
 <option value="">Sélectionner</option>
 <option value="A+" {{ old('blood_type', $driver->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
 <option value="A-" {{ old('blood_type', $driver->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
 <option value="B+" {{ old('blood_type', $driver->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
 <option value="B-" {{ old('blood_type', $driver->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
 <option value="AB+" {{ old('blood_type', $driver->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
 <option value="AB-" {{ old('blood_type', $driver->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
 <option value="O+" {{ old('blood_type', $driver->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
 <option value="O-" {{ old('blood_type', $driver->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
 </select>
 @error('blood_type')
 <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
 <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
 {{ $message }}
 </p>
 @enderror
 </div>

 <div class="md:col-span-2">
 <x-textarea
 name="address"
 label="Adresse"
 rows="3"
 placeholder="Adresse complète du chauffeur..."
 :value="old('address', $driver->address)"
 :error="$errors->first('address')"
 />
 </div>

 {{-- Photo --}}
 <div class="md:col-span-2">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Photo du chauffeur
 </label>
 <div class="flex items-center gap-6">
 {{-- Prévisualisation --}}
 <div class="flex-shrink-0">
 @if($driver->photo)
 <div x-show="!photoPreview">
 <img src="{{ asset('storage/' . $driver->photo) }}" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Photo actuelle">
 </div>
 @else
 <div x-show="!photoPreview" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
 <x-iconify icon="heroicons:user" class="w-12 h-12 text-gray-400" />
 </div>
 @endif
 <img x-show="photoPreview" :src="photoPreview" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Nouvelle photo" x-cloak>
 </div>
 {{-- Input file --}}
 <div class="flex-1">
 <input
 type="file"
 name="photo"
 id="photo"
 accept="image/*"
 @change="updatePhotoPreview($event)"
 class="block w-full text-sm text-gray-500
 file:mr-4 file:py-2 file:px-4
 file:rounded-lg file:border-0
 file:text-sm file:font-medium
 file:bg-blue-50 file:text-blue-700
 hover:file:bg-blue-100
 cursor-pointer">
 <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB. Laissez vide pour conserver la photo actuelle.</p>
 @error('photo')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- PHASE 2: INFORMATIONS PROFESSIONNELLES --}}
 <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0" 
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:briefcase" class="w-5 h-5 text-blue-600" />
 Informations Professionnelles
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-input
 name="employee_number"
 label="Matricule"
 icon="identification"
 placeholder="Ex: EMP-2024-001"
 :value="old('employee_number', $driver->employee_number)"
 :error="$errors->first('employee_number')"
 />

 <x-datepicker
 name="recruitment_date"
 label="Date de recrutement"
 :value="old('recruitment_date', $driver->recruitment_date ? $driver->recruitment_date->format('Y-m-d') : '')"
 format="d/m/Y"
 :error="$errors->first('recruitment_date')"
 placeholder="Choisir une date"
 :maxDate="date('Y-m-d')"
 />

 <x-datepicker
 name="contract_end_date"
 label="Fin de contrat"
 :value="old('contract_end_date', $driver->contract_end_date ? $driver->contract_end_date->format('Y-m-d') : '')"
 format="d/m/Y"
 :error="$errors->first('contract_end_date')"
 placeholder="Choisir une date"
 :minDate="date('Y-m-d')"
 helpText="Date de fin du contrat (optionnel)"
 />

 <x-tom-select
 name="status_id"
 label="Statut du Chauffeur"
 :options="$driverStatuses->pluck('name', 'id')->toArray()"
 :selected="old('status_id', $driver->status_id)"
 placeholder="Sélectionnez un statut..."
 required
 :error="$errors->first('status_id')"
 @change="validateField('status_id', $event.target.value)"
 />

 <div class="md:col-span-2">
 <x-textarea
 name="notes"
 label="Notes professionnelles"
 rows="4"
 placeholder="Informations complémentaires sur le chauffeur..."
 :value="old('notes', $driver->notes)"
 :error="$errors->first('notes')"
 helpText="Compétences, formations, remarques, etc."
 />
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- PHASE 3: PERMIS DE CONDUIRE --}}
 <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0" 
 style="display: none;">
 <div class="space-y-6">
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:identification" class="w-5 h-5 text-blue-600" />
 Permis de Conduire
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-input
 name="license_number"
 label="Numéro de permis"
 icon="identification"
 placeholder="Ex: 123456789"
 :value="old('license_number', $driver->license_number)"
 required
 :error="$errors->first('license_number')"
 />

 {{-- Catégories de permis multiples avec TomSelect --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Catégories de permis <span class="text-red-500">*</span>
 @if($driver->license_categories && count($driver->license_categories) > 0)
 <span class="ml-2 text-xs text-gray-500 font-normal">
 (Actuelles: {{ implode(', ', $driver->license_categories) }})
 </span>
 @elseif($driver->license_category)
 <span class="ml-2 text-xs text-gray-500 font-normal">
 (Actuelle: {{ $driver->license_category }})
 </span>
 @endif
 </label>
 <select 
 name="license_categories[]" 
 id="license_categories_edit"
 multiple
 class="w-full"
 required>
 <option value="">Sélectionner les catégories</option>
 @php
 $selectedCategories = old('license_categories', $driver->license_categories ?: ($driver->license_category ? [$driver->license_category] : []));
 @endphp
 <option value="A1" {{ in_array('A1', $selectedCategories) ? 'selected' : '' }}>A1 - Motocyclettes légères</option>
 <option value="A" {{ in_array('A', $selectedCategories) ? 'selected' : '' }}>A - Motocyclettes</option>
 <option value="B" {{ in_array('B', $selectedCategories) ? 'selected' : '' }}>B - Véhicules légers</option>
 <option value="BE" {{ in_array('BE', $selectedCategories) ? 'selected' : '' }}>B(E) - Véhicules légers avec remorque</option>
 <option value="C1" {{ in_array('C1', $selectedCategories) ? 'selected' : '' }}>C1 - Poids lourds légers</option>
 <option value="C1E" {{ in_array('C1E', $selectedCategories) ? 'selected' : '' }}>C1(E) - Poids lourds légers avec remorque</option>
 <option value="C" {{ in_array('C', $selectedCategories) ? 'selected' : '' }}>C - Poids lourds</option>
 <option value="CE" {{ in_array('CE', $selectedCategories) ? 'selected' : '' }}>C(E) - Poids lourds avec remorque</option>
 <option value="D" {{ in_array('D', $selectedCategories) ? 'selected' : '' }}>D - Transport de personnes</option>
 <option value="DE" {{ in_array('DE', $selectedCategories) ? 'selected' : '' }}>D(E) - Transport de personnes avec remorque</option>
 <option value="F" {{ in_array('F', $selectedCategories) ? 'selected' : '' }}>F - Véhicules agricoles</option>
 </select>
 @error('license_categories')
 <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
 <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
 {{ $message }}
 </p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">Maintenez Ctrl pour sélectionner plusieurs catégories</p>
 </div>

 <x-datepicker
 name="license_issue_date"
 label="Date de délivrance"
 :value="old('license_issue_date', $driver->license_issue_date ? $driver->license_issue_date->format('Y-m-d') : '')"
 format="d/m/Y"
 :error="$errors->first('license_issue_date')"
 placeholder="Choisir une date"
 :maxDate="date('Y-m-d')"
 required
 />

 <x-datepicker
 name="license_expiry_date"
 label="Date d'expiration"
 :value="old('license_expiry_date', $driver->license_expiry_date ? $driver->license_expiry_date->format('Y-m-d') : '')"
 format="d/m/Y"
 :error="$errors->first('license_expiry_date')"
 placeholder="Choisir une date"
 :minDate="date('Y-m-d')"
 required
 />

 <x-input
 name="license_authority"
 label="Autorité de délivrance"
 icon="building-office-2"
 placeholder="Ex: Wilaya d'Alger"
 :value="old('license_authority', $driver->license_authority)"
 :error="$errors->first('license_authority')"
 />

 <div class="flex items-center h-full pt-6">
 <label class="inline-flex items-center cursor-pointer">
 <input
 type="checkbox"
 name="license_verified"
 value="1"
 {{ old('license_verified', $driver->license_verified) ? 'checked' : '' }}
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-700 font-medium">
 <x-iconify icon="heroicons:check-badge" class="w-4 h-4 inline text-blue-600" />
 Permis vérifié
 </span>
 </label>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- PHASE 4: COMPTE & CONTACT D'URGENCE --}}
 <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 transform translate-x-4"
 x-transition:enter-end="opacity-100 transform translate-x-0" 
 style="display: none;">
 <div class="space-y-6">
 {{-- Compte Utilisateur --}}
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:user-circle" class="w-5 h-5 text-blue-600" />
 Compte Utilisateur (Optionnel)
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Compte utilisateur avec TomSelect --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Compte utilisateur
 @if($driver->user)
 <span class="ml-2 text-xs text-gray-500 font-normal">
 (Actuel: {{ $driver->user->name }})
 </span>
 @endif
 </label>
 <select 
 name="user_id" 
 id="user_id"
 class="w-full">
 <option value="">Aucun compte associé</option>
 @foreach($linkableUsers ?? [] as $user)
 <option value="{{ $user->id }}" 
 data-email="{{ $user->email }}"
 data-name="{{ $user->name }}"
 {{ old('user_id', $driver->user_id) == $user->id ? 'selected' : '' }}>
 {{ $user->name }} ({{ $user->email }})
 </option>
 @endforeach
 </select>
 @error('user_id')
 <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
 <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
 {{ $message }}
 </p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">
 Sélectionnez un compte existant ou laissez vide (optionnel)
 </p>
 </div>
 </div>
 </div>

 {{-- Contact d'Urgence --}}
 <div>
 <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:phone" class="w-5 h-5 text-red-600" />
 Contact d'Urgence
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <x-input
 name="emergency_contact_name"
 label="Nom du contact"
 icon="user"
 placeholder="Ex: Fatima Benali"
 :value="old('emergency_contact_name', $driver->emergency_contact_name)"
 :error="$errors->first('emergency_contact_name')"
 />

 <x-input
 name="emergency_contact_phone"
 type="tel"
 label="Téléphone du contact"
 icon="phone"
 placeholder="Ex: 0555987654"
 :value="old('emergency_contact_phone', $driver->emergency_contact_phone)"
 :error="$errors->first('emergency_contact_phone')"
 />

 <x-input
 name="emergency_contact_relationship"
 label="Lien de parenté"
 icon="users"
 placeholder="Ex: Épouse, Frère, Mère"
 :value="old('emergency_contact_relationship', $driver->emergency_contact_relationship)"
 :error="$errors->first('emergency_contact_relationship')"
 />
 </div>
 </div>
 </div>
 </div>

 {{-- ACTIONS FOOTER --}}
 <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
 <div>
 <button
 type="button"
 @click="prevStep()"
 x-show="currentStep > 1"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
 <x-iconify icon="heroicons:arrow-left" class="w-4 h-4" />
 Précédent
 </button>
 </div>

 <div class="flex items-center gap-4">
 <a href="{{ route('admin.drivers.show', $driver) }}"
 class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
 Annuler
 </a>

 <button
 type="button"
 @click="nextStep()"
 x-show="currentStep < 4"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
 Suivant
 <x-iconify icon="heroicons:arrow-right" class="w-4 h-4" />
 </button>

 <button
 type="submit"
 x-show="currentStep === 4"
 x-cloak
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
 <x-iconify icon="heroicons:check" class="w-5 h-5" />
 Enregistrer les Modifications
 </button>
 </div>
 </div>
 </form>
 </x-card>

 </div>
 </div>
</section>

@push('scripts')
<script>
function driverFormValidation() {
 return {
 currentStep: {{ old('current_step', 1) }},
 photoPreview: null,
 errors: {},
 touched: {},

 init() {
 @if ($errors->any())
 this.handleValidationErrors();
 @endif
 },

 updatePhotoPreview(event) {
 const file = event.target.files[0];
 if (file) {
 const reader = new FileReader();
 reader.onload = (e) => {
 this.photoPreview = e.target.result;
 };
 reader.readAsDataURL(file);
 }
 },

 validateField(fieldName, value) {
 this.touched[fieldName] = true;
 // Validation logic similar to create
 },

 nextStep() {
 if (this.currentStep < 4) {
 this.currentStep++;
 window.scrollTo({ top: 0, behavior: 'smooth' });
 }
 },

 prevStep() {
 if (this.currentStep > 1) {
 this.currentStep--;
 window.scrollTo({ top: 0, behavior: 'smooth' });
 }
 },

 onSubmit(event) {
 // Conversion automatique des dates du format dd/mm/yyyy vers yyyy-mm-dd
 this.convertDatesBeforeSubmit(event);
 },

 /**
 * 🔄 Conversion Enterprise-Grade des dates avant soumission
 * Convertit automatiquement tous les champs de date du format d/m/Y vers Y-m-d
 */
 convertDatesBeforeSubmit(event) {
 const form = event.target;
 
 // Liste des champs de date à convertir
 const dateFields = [
 'birth_date',
 'recruitment_date', 
 'contract_end_date',
 'license_issue_date',
 'license_expiry_date'
 ];

 dateFields.forEach(fieldName => {
 const input = form.querySelector(`[name="${fieldName}"]`);
 if (input && input.value) {
 const convertedDate = this.convertDateFormat(input.value);
 if (convertedDate) {
 input.value = convertedDate;
 }
 }
 });
 },

 /**
 * 📅 Convertit une date du format dd/mm/yyyy vers yyyy-mm-dd
 * Gère plusieurs formats d'entrée de manière robuste
 */
 convertDateFormat(dateString) {
 if (!dateString) return null;

 // Si déjà au format yyyy-mm-dd, retourner tel quel
 if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
 return dateString;
 }

 // Conversion depuis dd/mm/yyyy ou d/m/yyyy
 const match = dateString.match(/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/);
 if (match) {
 const day = match[1].padStart(2, '0');
 const month = match[2].padStart(2, '0');
 const year = match[3];
 
 // Validation basique de la date
 const date = new Date(`${year}-${month}-${day}`);
 if (date && !isNaN(date.getTime())) {
 return `${year}-${month}-${day}`;
 }
 }

 // Si format non reconnu, retourner null et logger une erreur
 console.error('Format de date non reconnu:', dateString);
 return null;
 },

 handleValidationErrors() {
 const fieldToStepMap = {
 'first_name': 1, 'last_name': 1, 'birth_date': 1, 'personal_phone': 1, 'address': 1,
 'blood_type': 1, 'personal_email': 1, 'photo': 1,
 'employee_number': 2, 'recruitment_date': 2, 'contract_end_date': 2, 'status_id': 2, 'notes': 2,
 'license_number': 3, 'license_categories': 3, 'license_issue_date': 3, 'license_expiry_date': 3,
 'license_authority': 3, 'license_verified': 3,
 'user_id': 4, 'emergency_contact_name': 4, 'emergency_contact_phone': 4, 'emergency_contact_relationship': 4
 };

 const errors = @json($errors->keys());
 let firstErrorStep = null;

 for (const field of errors) {
 if (fieldToStepMap[field]) {
 firstErrorStep = fieldToStepMap[field];
 break;
 }
 }

 if (firstErrorStep) {
 this.currentStep = firstErrorStep;
 }
 }
 }
}

// ═══════════════════════════════════════════════════════════════════════════
// 🔄 INITIALISATION TOMSELECT POUR LES CATÉGORIES DE PERMIS
// ═══════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
 // Initialisation de TomSelect pour les catégories de permis
 const licenseCategoriesSelect = document.getElementById('license_categories_edit');
 if (licenseCategoriesSelect) {
 new TomSelect('#license_categories_edit', {
 plugins: ['remove_button', 'clear_button'],
 placeholder: 'Sélectionner une ou plusieurs catégories',
 maxItems: null,
 closeAfterSelect: false,
 create: false,
 render: {
 option: function(data, escape) {
 return '<div class="py-2 px-3 hover:bg-gray-50 cursor-pointer">' +
 '<span class="font-semibold text-gray-900">' + escape(data.value) + '</span>' +
 '<span class="text-gray-600 text-sm ml-2">' + escape(data.text.split(' - ')[1] || '') + '</span>' +
 '</div>';
 },
 item: function(data, escape) {
 return '<div class="py-1 px-2 bg-blue-100 text-blue-800 rounded">' + 
 escape(data.value) + 
 '</div>';
 }
 }
 });
 }
 
 // Initialisation de TomSelect pour le user_id
 const userIdSelect = document.getElementById('user_id');
 if (userIdSelect) {
 new TomSelect('#user_id', {
 plugins: ['clear_button'],
 placeholder: 'Rechercher un utilisateur...',
 valueField: 'value',
 labelField: 'text',
 searchField: ['text'],
 create: false,
 maxItems: 1,
 render: {
 option: function(data, escape) {
 // Extraire le nom et l'email depuis le texte
 const match = data.text.match(/(.+?) \((.+?)\)/);
 if (match) {
 const name = match[1];
 const email = match[2];
 return '<div class="py-2 px-3 hover:bg-gray-50 cursor-pointer">' +
 '<div class="font-semibold text-gray-900">' + escape(name) + '</div>' +
 '<div class="text-xs text-gray-500">' + escape(email) + '</div>' +
 '</div>';
 }
 return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
 },
 item: function(data, escape) {
 const match = data.text.match(/(.+?) \((.+?)\)/);
 if (match) {
 return '<div>' + escape(match[1]) + '</div>';
 }
 return '<div>' + escape(data.text) + '</div>';
 }
 }
 });
 }
});
</script>
@endpush
@endsection
