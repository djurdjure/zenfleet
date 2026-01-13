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
 ['label' => 'Permis de Conduire', 'icon' => 'identification'],
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

                                    <x-select
                                        name="license_category"
                                        label="Catégorie de permis"
                                        :options="[
 '' => 'Sélectionner une catégorie',
 'B' => 'Catégorie B - Véhicules légers',
 'C' => 'Catégorie C - Poids lourds',
 'D' => 'Catégorie D - Transport de personnes',
 'E' => 'Catégorie E - Remorques'
 ]"
                                        :selected="old('license_category')"
                                        required
                                        :error="$errors->first('license_category')" />

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
                                    <x-slim-select
                                        name="user_id"
                                        label="Compte utilisateur"
                                        :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
                                        :selected="old('user_id')"
                                        placeholder="Rechercher un utilisateur..."
                                        :error="$errors->first('user_id')"
                                        helpText="Sélectionnez un compte existant ou laissez vide" />
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
    function driverFormValidation() {
        return {
            currentStep: {
                {
                    old('current_step', 1)
                }
            },
            photoPreview: null,
            errors: {
                first_name: '',
                last_name: '',
                status_id: ''
            },
            touched: {
                first_name: false,
                last_name: false,
                status_id: false
            },

            init() {
                @if($errors - > any())
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

                switch (fieldName) {
                    case 'first_name':
                        if (!value || value.trim() === '') {
                            this.errors.first_name = 'Le prénom est obligatoire';
                        } else if (value.trim().length < 2) {
                            this.errors.first_name = 'Le prénom doit contenir au moins 2 caractères';
                        } else {
                            this.errors.first_name = '';
                        }
                        break;

                    case 'last_name':
                        if (!value || value.trim() === '') {
                            this.errors.last_name = 'Le nom est obligatoire';
                        } else if (value.trim().length < 2) {
                            this.errors.last_name = 'Le nom doit contenir au moins 2 caractères';
                        } else {
                            this.errors.last_name = '';
                        }
                        break;

                    case 'status_id':
                        if (!value || value === '' || value === '0') {
                            this.errors.status_id = 'Le statut du chauffeur est obligatoire';
                        } else {
                            this.errors.status_id = '';
                        }
                        break;
                }
            },

            hasError(fieldName) {
                return this.touched[fieldName] && this.errors[fieldName] !== '';
            },

            validateStep(step) {
                let isValid = true;

                if (step === 1) {
                    const firstName = document.getElementById('first_name');
                    const lastName = document.getElementById('last_name');

                    if (firstName && lastName) {
                        this.validateField('first_name', firstName.value);
                        this.validateField('last_name', lastName.value);

                        if (this.errors.first_name || this.errors.last_name) {
                            isValid = false;
                        }
                    }
                }

                if (step === 2) {
                    const statusSelect = document.querySelector('select[name="status_id"]');
                    const statusInput = document.querySelector('input[name="status_id"]');
                    const statusValue = statusSelect?.value || statusInput?.value;

                    if (statusValue) {
                        this.validateField('status_id', statusValue);
                        if (this.errors.status_id) {
                            isValid = false;
                        }
                    }
                }

                return isValid;
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
            },

            handleValidationErrors() {
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
                    'license_category': 3,
                    'license_issue_date': 3,
                    'license_expiry_date': 3,
                    'license_authority': 3,
                    'license_verified': 3,
                    'user_id': 4,
                    'emergency_contact_name': 4,
                    'emergency_contact_phone': 4,
                    'emergency_contact_relationship': 4
                };

                const errors = @json($errors - > keys());
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
</script>
@endpush
@endsection