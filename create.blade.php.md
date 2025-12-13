@extends('layouts.admin.catalyst')

@section('title', 'Ajouter un Nouveau Chauffeur')

@section('content')
{{-- ====================================================================
 👤 FORMULAIRE CRÉATION CHAUFFEUR - ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Validation en temps réel à chaque phase
 - Empêchement navigation si étape invalide
 - Indicateurs visuels de validation
 - Messages d'erreur clairs et contextuels
 - Animation des transitions
 - Composants: x-iconify, x-input, x-select, x-datepicker, x-stepper
 - Design aligné 100% avec vehicles/create
 
 @version 3.0-Enterprise-Validated
 @since 2025-01-19
 ==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
<div x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(function() { show = false; }, 5000)"
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
                <x-iconify icon="heroicons:user-plus" class="w-6 h-6 text-blue-600" />
                Ajouter un Nouveau Chauffeur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 4 étapes pour enregistrer un chauffeur
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
                {{-- ====================================================================
 🎯 STEPPER V7.0 - ULTRA-PRO WORLD-CLASS
 ==================================================================== --}}
                <x-stepper
                    :steps="[
 ['label' => 'Informations Personnelles', 'icon' => 'user'],
 ['label' => 'Informations Professionnelles', 'icon' => 'briefcase'],
 ['label' => 'Permis de Conduire', 'icon' => 'id-card'],
 ['label' => 'Compte & Urgence', 'icon' => 'link']
 ]"
                    currentStepVar="currentStep" />

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('admin.drivers.store') }}" enctype="multipart/form-data" @submit="onSubmit" class="p-6">
                    @csrf
                    <input type="hidden" name="current_step" x-model="currentStep">

                    {{-- ===========================================
 PHASE 1: INFORMATIONS PERSONNELLES
 =========================================== --}}
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
                                        :value="old('first_name')"
                                        required
                                        :error="$errors->first('first_name')"
                                        helpText="Prénom du chauffeur"
                                        @blur="validateField('first_name', $event.target.value)" />

                                    <x-input
                                        name="last_name"
                                        label="Nom"
                                        icon="user"
                                        placeholder="Ex: Benali"
                                        :value="old('last_name')"
                                        required
                                        :error="$errors->first('last_name')"
                                        @blur="validateField('last_name', $event.target.value)" />

                                    <x-datepicker
                                        name="birth_date"
                                        label="Date de naissance"
                                        :value="old('birth_date')"
                                        format="d/m/Y"
                                        :error="$errors->first('birth_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')"
                                        helpText="Date de naissance du chauffeur" />

                                    <x-input
                                        name="personal_phone"
                                        type="tel"
                                        label="Téléphone personnel"
                                        icon="phone"
                                        placeholder="Ex: 0555123456"
                                        :value="old('personal_phone')"
                                        :error="$errors->first('personal_phone')" />

                                    <x-input
                                        name="personal_email"
                                        type="email"
                                        label="Email personnel"
                                        icon="envelope"
                                        placeholder="Ex: ahmed.benali@email.com"
                                        :value="old('personal_email')"
                                        :error="$errors->first('personal_email')" />

                                    <x-select
                                        name="blood_type"
                                        label="Groupe sanguin"
                                        :options="[
 '' => 'Sélectionner',
 'A+' => 'A+',
 'A-' => 'A-',
 'B+' => 'B+',
 'B-' => 'B-',
 'AB+' => 'AB+',
 'AB-' => 'AB-',
 'O+' => 'O+',
 'O-' => 'O-'
 ]"
                                        :selected="old('blood_type')"
                                        :error="$errors->first('blood_type')" />

                                    <div class="md:col-span-2">
                                        <x-textarea
                                            name="address"
                                            label="Adresse"
                                            rows="3"
                                            placeholder="Adresse complète du chauffeur..."
                                            :value="old('address')"
                                            :error="$errors->first('address')" />
                                    </div>

                                    {{-- Photo --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Photo du chauffeur
                                        </label>
                                        <div class="flex items-center gap-6">
                                            {{-- Prévisualisation --}}
                                            <div class="flex-shrink-0">
                                                <div x-show="!photoPreview" class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <x-iconify icon="heroicons:user" class="w-12 h-12 text-gray-400" />
                                                </div>
                                                <img x-show="photoPreview" :src="photoPreview" class="h-24 w-24 rounded-full object-cover ring-2 ring-blue-100" alt="Prévisualisation" x-cloak>
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
                                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB</p>
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

                    {{-- ===========================================
 PHASE 2: INFORMATIONS PROFESSIONNELLES
 =========================================== --}}
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
                                        :value="old('employee_number')"
                                        :error="$errors->first('employee_number')"
                                        helpText="Numéro matricule unique" />

                                    <x-datepicker
                                        name="recruitment_date"
                                        label="Date de recrutement"
                                        :value="old('recruitment_date')"
                                        format="d/m/Y"
                                        :error="$errors->first('recruitment_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')" />

                                    <x-datepicker
                                        name="contract_end_date"
                                        label="Fin de contrat"
                                        :value="old('contract_end_date')"
                                        format="d/m/Y"
                                        :error="$errors->first('contract_end_date')"
                                        placeholder="Choisir une date"
                                        :minDate="date('Y-m-d')"
                                        helpText="Date de fin du contrat (optionnel)" />

                                    <x-slim-select
                                        name="status_id"
                                        label="Statut du Chauffeur"
                                        :options="$driverStatuses->pluck('name', 'id')->toArray()"
                                        :selected="old('status_id')"
                                        placeholder="Sélectionnez un statut..."
                                        required
                                        :error="$errors->first('status_id')"
                                        @change="validateField('status_id', $event.target.value)" />

                                    <div class="md:col-span-2">
                                        <x-textarea
                                            name="notes"
                                            label="Notes professionnelles"
                                            rows="4"
                                            placeholder="Informations complémentaires sur le chauffeur..."
                                            :value="old('notes')"
                                            :error="$errors->first('notes')"
                                            helpText="Compétences, formations, remarques, etc." />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===========================================
 PHASE 3: PERMIS DE CONDUIRE
 =========================================== --}}
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
                                        :value="old('license_number')"
                                        required
                                        :error="$errors->first('license_number')"
                                        helpText="Numéro du permis de conduire" />

                                    {{-- Catégories de permis - Solution Enterprise avec checkboxes --}}
                                    @php
                                    $licenseOptions = [
                                    'A1' => 'A1 - Motocyclettes légères',
                                    'A' => 'A - Motocyclettes',
                                    'B' => 'B - Véhicules légers',
                                    'BE' => 'B(E) - Véhicules légers avec remorque',
                                    'C1' => 'C1 - Poids lourds légers',
                                    'C1E' => 'C1(E) - Poids lourds légers avec remorque',
                                    'C' => 'C - Poids lourds',
                                    'CE' => 'C(E) - Poids lourds avec remorque',
                                    'D' => 'D - Transport de personnes',
                                    'DE' => 'D(E) - Transport de personnes avec remorque',
                                    'F' => 'F - Véhicules agricoles'
                                    ];
                                    $oldCategories = old('license_categories', []);
                                    if (!is_array($oldCategories)) $oldCategories = [];
                                    @endphp

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Catégories de permis <span class="text-red-500">*</span>
                                        </label>

                                        {{-- Grille de checkboxes pour sélection multiple --}}
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            @foreach($licenseOptions as $value => $label)
                                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white p-2 rounded transition-colors">
                                                <input
                                                    type="checkbox"
                                                    name="license_categories[]"
                                                    value="{{ $value }}"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    {{ in_array($value, $oldCategories) ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-700">{{ $label }}</span>
                                            </label>
                                            @endforeach
                                        </div>

                                        @error('license_categories')
                                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                            <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">Cochez les catégories de permis détenues par le chauffeur</p>
                                    </div>

                                    <x-datepicker
                                        name="license_issue_date"
                                        label="Date de délivrance"
                                        :value="old('license_issue_date')"
                                        format="d/m/Y"
                                        :error="$errors->first('license_issue_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')"
                                        required />

                                    <x-datepicker
                                        name="license_expiry_date"
                                        label="Date d'expiration"
                                        :value="old('license_expiry_date')"
                                        format="d/m/Y"
                                        :error="$errors->first('license_expiry_date')"
                                        placeholder="Choisir une date"
                                        :minDate="date('Y-m-d')"
                                        required
                                        helpText="Date d'expiration du permis" />

                                    <x-input
                                        name="license_authority"
                                        label="Autorité de délivrance"
                                        icon="building-office-2"
                                        placeholder="Ex: Wilaya d'Alger"
                                        :value="old('license_authority')"
                                        :error="$errors->first('license_authority')" />

                                    <div class="flex items-center h-full pt-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="license_verified"
                                                value="1"
                                                {{ old('license_verified') ? 'checked' : '' }}
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

                    {{-- ===========================================
 PHASE 4: COMPTE & CONTACT D'URGENCE
 =========================================== --}}
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

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex">
                                        <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">Création de compte optionnelle</p>
                                            <p class="text-xs text-blue-700 mt-1">
                                                Si vous associez un compte utilisateur, le chauffeur pourra se connecter à l'application.
                                                Vous pouvez aussi le faire plus tard.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @php
                                    $userOptions = isset($linkableUsers)
                                    ? $linkableUsers->mapWithKeys(function($user) {
                                    return [$user->id => $user->name . ' (' . $user->email . ')'];
                                    })->toArray()
                                    : [];
                                    @endphp
                                    <x-slim-select
                                        name="user_id"
                                        label="Compte utilisateur"
                                        :options="$userOptions"
                                        :selected="old('user_id')"
                                        placeholder="Rechercher un utilisateur..."
                                        :error="$errors->first('user_id')"
                                        helpText="Sélectionnez un compte existant ou laissez vide (optionnel)" />
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
                                        :value="old('emergency_contact_name')"
                                        :error="$errors->first('emergency_contact_name')" />

                                    <x-input
                                        name="emergency_contact_phone"
                                        type="tel"
                                        label="Téléphone du contact"
                                        icon="phone"
                                        placeholder="Ex: 0555987654"
                                        :value="old('emergency_contact_phone')"
                                        :error="$errors->first('emergency_contact_phone')" />

                                    <x-input
                                        name="emergency_contact_relationship"
                                        label="Lien de parenté"
                                        icon="users"
                                        placeholder="Ex: Épouse, Frère, Mère"
                                        :value="old('emergency_contact_relationship')"
                                        :error="$errors->first('emergency_contact_relationship')" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===========================================
 ACTIONS FOOTER
 =========================================== --}}
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
                            <a href="{{ route('admin.drivers.index') }}"
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
                                Créer le Chauffeur
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('driverFormValidation', () => ({
                        currentStep: {{ old('current_step', 1) }},
            photoPreview: null,
            fieldErrors: {
                // Phase 1: Informations Personnelles
                first_name: '',
                last_name: '',
                birth_date: '',
                personal_phone: '',
                personal_email: '',
                blood_type: '',
                address: '',
                // Phase 2: Informations Professionnelles
                employee_number: '',
                recruitment_date: '',
                status_id: '',
                // Phase 3: Permis de Conduire
                license_number: '',
                license_categories: '',
                license_issue_date: '',
                license_expiry_date: ''
            },
            touchedFields: {
                first_name: false,
                last_name: false,
                birth_date: false,
                personal_phone: false,
                personal_email: false,
                status_id: false,
                employee_number: false,
                license_number: false,
                license_categories: false
            },
            formValid: false,

            init() {
                                @if($errors->any())
                                this.handleValidationErrors(@json($errors->messages()));
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
                this.touchedFields[fieldName] = true;

                switch (fieldName) {
                    case 'first_name':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.first_name = 'Le prénom est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else if (value.trim().length < 2) {
                            this.fieldErrors.first_name = 'Le prénom doit contenir au moins 2 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                            this.fieldErrors.first_name = 'Le prénom ne doit contenir que des lettres';
                            this.showFieldError(fieldName, this.fieldErrors.first_name);
                        } else {
                            this.fieldErrors.first_name = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'last_name':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.last_name = 'Le nom est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else if (value.trim().length < 2) {
                            this.fieldErrors.last_name = 'Le nom doit contenir au moins 2 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(value.trim())) {
                            this.fieldErrors.last_name = 'Le nom ne doit contenir que des lettres';
                            this.showFieldError(fieldName, this.fieldErrors.last_name);
                        } else {
                            this.fieldErrors.last_name = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'birth_date':
                        if (!value) {
                            this.fieldErrors.birth_date = 'La date de naissance est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.birth_date);
                        } else {
                            const birthDate = new Date(value);
                            const today = new Date();
                            const age = today.getFullYear() - birthDate.getFullYear();
                            if (age < 18) {
                                this.fieldErrors.birth_date = 'Le chauffeur doit être majeur (18 ans minimum)';
                                this.showFieldError(fieldName, this.fieldErrors.birth_date);
                            } else if (age > 70) {
                                this.fieldErrors.birth_date = 'L\'âge maximum est de 70 ans';
                                this.showFieldError(fieldName, this.fieldErrors.birth_date);
                            } else {
                                this.fieldErrors.birth_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;

                    case 'personal_phone':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.personal_phone = 'Le téléphone est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                        } else if (!/^(0[567])[0-9]{8}$/.test(value.replace(/\s/g, ''))) {
                            this.fieldErrors.personal_phone = 'Format invalide (ex: 0555123456)';
                            this.showFieldError(fieldName, this.fieldErrors.personal_phone);
                        } else {
                            this.fieldErrors.personal_phone = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'personal_email':
                        if (value && value.trim() !== '') {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailRegex.test(value)) {
                                this.fieldErrors.personal_email = 'Format email invalide';
                                this.showFieldError(fieldName, this.fieldErrors.personal_email);
                            } else {
                                this.fieldErrors.personal_email = '';
                                this.removeFieldError(fieldName);
                            }
                        } else {
                            this.fieldErrors.personal_email = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'employee_number':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.employee_number = 'Le matricule est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.employee_number);
                        } else if (value.trim().length < 3) {
                            this.fieldErrors.employee_number = 'Le matricule doit contenir au moins 3 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.employee_number);
                        } else {
                            this.fieldErrors.employee_number = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'recruitment_date':
                        if (!value) {
                            this.fieldErrors.recruitment_date = 'La date de recrutement est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                        } else {
                            const recruitDate = new Date(value);
                            const today = new Date();
                            if (recruitDate > today) {
                                this.fieldErrors.recruitment_date = 'La date ne peut pas être dans le futur';
                                this.showFieldError(fieldName, this.fieldErrors.recruitment_date);
                            } else {
                                this.fieldErrors.recruitment_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;

                    case 'status_id':
                        if (!value || value === '' || value === '0') {
                            this.fieldErrors.status_id = 'Le statut du chauffeur est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.status_id);
                        } else {
                            this.fieldErrors.status_id = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_number':
                        if (!value || value.trim() === '') {
                            this.fieldErrors.license_number = 'Le numéro de permis est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_number);
                        } else if (value.trim().length < 5) {
                            this.fieldErrors.license_number = 'Le numéro de permis doit contenir au moins 5 caractères';
                            this.showFieldError(fieldName, this.fieldErrors.license_number);
                        } else {
                            this.fieldErrors.license_number = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_categories':
                        if (!value || value.length === 0) {
                            this.fieldErrors.license_categories = 'Au moins une catégorie de permis est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_categories);
                        } else {
                            this.fieldErrors.license_categories = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_issue_date':
                        if (!value) {
                            this.fieldErrors.license_issue_date = 'La date de délivrance est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_issue_date);
                        } else {
                            this.fieldErrors.license_issue_date = '';
                            this.removeFieldError(fieldName);
                        }
                        break;

                    case 'license_expiry_date':
                        if (!value) {
                            this.fieldErrors.license_expiry_date = 'La date d\'expiration est obligatoire';
                            this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                        } else {
                            const expiryDate = new Date(value);
                            const today = new Date();
                            if (expiryDate < today) {
                                this.fieldErrors.license_expiry_date = 'Le permis est expiré';
                                this.showFieldError(fieldName, this.fieldErrors.license_expiry_date);
                            } else {
                                this.fieldErrors.license_expiry_date = '';
                                this.removeFieldError(fieldName);
                            }
                        }
                        break;
                }

                this.updateFormValidity();
            },

            showFieldError(fieldName, message) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.remove('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');

                    // Ajouter ou mettre à jour le message d'erreur
                    let errorDiv = field.parentElement.querySelector('.field-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('p');
                        errorDiv.className = 'field-error mt-1.5 text-sm text-red-600 flex items-center gap-1';
                        errorDiv.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span>${message}</span>`;
                        field.parentElement.appendChild(errorDiv);
                    } else {
                        errorDiv.querySelector('span').textContent = message;
                    }
                }
            },

            removeFieldError(fieldName) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                    field.classList.add('border-gray-300', 'focus:border-blue-500', 'focus:ring-blue-500');

                    const errorDiv = field.parentElement.querySelector('.field-error');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            },

            updateFormValidity() {
                let hasErrors = false;
                for (let key in this.fieldErrors) {
                    if (this.fieldErrors[key] !== '') {
                        hasErrors = true;
                        break;
                    }
                }
                this.formValid = !hasErrors;
            },

            hasError(fieldName) {
                return this.touchedFields[fieldName] && this.fieldErrors[fieldName] !== '';
            },

            validateStep(step) {
                let isValid = true;
                let fieldsToValidate = [];

                switch (step) {
                    case 1: // Informations Personnelles
                        fieldsToValidate = [
                            'first_name',
                            'last_name',
                            'birth_date',
                            'personal_phone'
                        ];
                        // Email est optionnel
                        const emailField = document.querySelector('[name="personal_email"]');
                        if (emailField && emailField.value) {
                            fieldsToValidate.push('personal_email');
                        }
                        break;

                    case 2: // Informations Professionnelles
                        fieldsToValidate = [
                            'employee_number',
                            'recruitment_date',
                            'status_id'
                        ];
                        break;

                    case 3: // Permis de Conduire
                        fieldsToValidate = [
                            'license_number',
                            'license_categories',
                            'license_issue_date',
                            'license_expiry_date'
                        ];
                        break;

                    case 4: // Compte & Urgence
                        // Tous les champs sont optionnels dans cette étape
                        break;
                }

                // Valider chaque champ de l'étape
                fieldsToValidate.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        this.validateField(fieldName, field.value);
                        if (this.fieldErrors[fieldName]) {
                            isValid = false;
                        }
                    }
                });

                // Si des erreurs existent, afficher une alerte
                if (!isValid) {
                    this.showStepErrors(step, fieldsToValidate);
                }

                return isValid;
            },

            showStepErrors(step, fields) {
                let errorMessages = [];
                fields.forEach(fieldName => {
                    if (this.fieldErrors[fieldName]) {
                        const label = document.querySelector(`[name="${fieldName}"]`)?.parentElement?.querySelector('label')?.textContent || fieldName;
                        errorMessages.push(`• ${label}: ${this.fieldErrors[fieldName]}`);
                    }
                });

                if (errorMessages.length > 0) {
                    // Créer ou mettre à jour le message d'erreur global
                    let alertDiv = document.querySelector('.step-validation-alert');
                    if (!alertDiv) {
                        alertDiv = document.createElement('div');
                        alertDiv.className = 'step-validation-alert fixed top-4 right-4 z-50 max-w-md bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg';
                        alertDiv.innerHTML = `
 <div class="flex items-start gap-3">
 <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
 </svg>
 <div class="flex-1">
 <h3 class="text-sm font-semibold text-red-800">Veuillez corriger les erreurs suivantes :</h3>
 <div class="mt-2 text-sm text-red-700 error-list"></div>
 </div>
 <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
 </svg>
 </button>
 </div>
 `;
                        document.body.appendChild(alertDiv);
                    }
                    alertDiv.querySelector('.error-list').innerHTML = errorMessages.join('<br>');

                    // Auto-fermer après 5 secondes
                    setTimeout(() => {
                        if (alertDiv) {
                            alertDiv.remove();
                        }
                    }, 5000);
                }
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    if (this.currentStep < 4) {
                        this.currentStep++;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            },

            onSubmit(event) {
                if (!this.validateStep(4)) {
                    event.preventDefault();
                    return false;
                }

                // Conversion automatique des dates avant soumission
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

            handleValidationErrors(errors) {
                console.log('Server Errors:', errors);
                // Map server errors to fieldErrors
                Object.keys(errors).forEach(field => {
                    this.fieldErrors[field] = errors[field][0];
                    this.touchedFields[field] = true;
                });

                const fieldToStepMap = {
                    'first_name': 1,
                    'last_name': 1,
                    'birth_date': 1,
                    'personal_phone': 1,
                    'address': 1,
                    'blood_type': 1,
                    'personal_email': 1,
                    'photo': 1,
                    'employee_number': 2,
                    'recruitment_date': 2,
                    'contract_end_date': 2,
                    'status_id': 2,
                    'notes': 2,
                    'license_number': 3,
                    'license_categories': 3,
                    'license_issue_date': 3,
                    'license_expiry_date': 3,
                    'license_authority': 3,
                    'license_verified': 3,
                    'user_id': 4,
                    'emergency_contact_name': 4,
                    'emergency_contact_phone': 4,
                    'emergency_contact_relationship': 4
                };

                // Determine the first step with an error
                const errorFields = Object.keys(errors);
                let firstErrorStep = null;

                for (const field of errorFields) {
                    if (fieldToStepMap[field]) {
                        if (firstErrorStep === null || fieldToStepMap[field] < firstErrorStep) {
                            firstErrorStep = fieldToStepMap[field];
                        }
                    }
                }

                if (firstErrorStep) {
                    this.currentStep = firstErrorStep;
                }
            }
        }));
    });
</script>
@endpush
@endsection
