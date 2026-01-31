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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="heroicons:user-plus" class="w-6 h-6 text-blue-600" />
                Ajouter un Nouveau Chauffeur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les sections ci-dessous pour enregistrer un chauffeur
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
 FORMULAIRE SINGLE-PAGE AVEC VALIDATION ALPINE.JS
 ==================================================================== --}}
        <div x-data="driverFormValidationCreate()" x-init="init()">

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('admin.drivers.store') }}" enctype="multipart/form-data" @submit="onSubmit" class="space-y-8">
                @csrf

                {{-- ===========================================
 PHASE 1: INFORMATIONS PERSONNELLES
 =========================================== --}}
                <x-form-section
                    title="Informations Personnelles"
                    icon="heroicons:user"
                    subtitle="Identité, coordonnées et informations de base du chauffeur">
                    <x-field-group :columns="2">
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
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">

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

                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-textarea
                            name="address"
                            label="Adresse"
                            rows="3"
                            placeholder="Adresse complète du chauffeur..."
                            :value="old('address')"
                            :error="$errors->first('address')" />
                    </div>

                    {{-- Photo --}}
                    <div class="col-span-6 mt-6">
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
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
                </x-form-section>

                {{-- ===========================================
 PHASE 2: INFORMATIONS PROFESSIONNELLES
 =========================================== --}}
                <x-form-section
                    title="Informations Professionnelles"
                    icon="heroicons:briefcase"
                    subtitle="Matricule, statut et informations de recrutement">
                    <x-field-group :columns="2">
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

                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-textarea
                            name="notes"
                            label="Notes professionnelles"
                            rows="4"
                            placeholder="Informations complémentaires sur le chauffeur..."
                            :value="old('notes')"
                            :error="$errors->first('notes')"
                            helpText="Compétences, formations, remarques, etc." />
                    </div>
                </x-form-section>

                {{-- ===========================================
 PHASE 3: PERMIS DE CONDUIRE
 =========================================== --}}
                <x-form-section
                    title="Permis de Conduire"
                    icon="heroicons:identification"
                    subtitle="Numéro, catégories et dates de validité">
                    <x-field-group :columns="2">
                        <x-input
                            name="license_number"
                            label="Numéro de permis"
                            icon="identification"
                            placeholder="Ex: 123456789"
                            :value="old('license_number')"
                            required
                            :error="$errors->first('license_number')"
                            helpText="Numéro du permis de conduire" />

                        {{-- Catégories de permis - Solution Enterprise SlimSelect Multi-Select --}}
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
                        $selectedCategories = old('license_categories', []);
                        if (!is_array($selectedCategories)) $selectedCategories = [];
                        @endphp

                        <div>
                            <x-multi-checkbox-select
                                name="license_categories"
                                label="Catégories de permis"
                                :options="$licenseOptions"
                                :selected="$selectedCategories"
                                placeholder="Sélectionnez les catégories de permis..."
                                required
                                :error="$errors->first('license_categories')"
                                @change="validateField('license_categories', $event.detail.selected)"
                                helpText="Sélectionnez toutes les catégories de permis détenues par le chauffeur" />
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
                    </x-field-group>
                </x-form-section>

                {{-- ===========================================
 PHASE 4: COMPTE & CONTACT D'URGENCE
 =========================================== --}}
                <x-form-section
                    title="Compte & Contact d'Urgence"
                    icon="heroicons:link"
                    subtitle="Accès applicatif optionnel et personne à contacter">
                    <div class="space-y-6">
                        {{-- Compte Utilisateur --}}
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                                <x-iconify icon="heroicons:user-circle" class="w-4 h-4 text-blue-600" />
                                Compte Utilisateur (Optionnel)
                            </h4>

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
                            <h4 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                                <x-iconify icon="heroicons:phone" class="w-4 h-4 text-red-600" />
                                Contact d'Urgence
                            </h4>

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
                </x-form-section>

                {{-- ===========================================
 ACTIONS FOOTER
 =========================================== --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between">
                    <a href="{{ route('admin.drivers.index') }}"
                        class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                        <x-iconify icon="heroicons:check" class="w-5 h-5" />
                        Créer le Chauffeur
                    </button>
                </div>
            </form>

        </div>
    </div>
</section>

@push('scripts')
<script>
    window.zenfleetDriverErrors = @json($errors -> messages());
</script>
@endpush
@endsection