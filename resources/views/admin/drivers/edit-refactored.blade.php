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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

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
 FORMULAIRE SINGLE-PAGE AVEC VALIDATION ALPINE.JS
 ==================================================================== --}}
        <div x-data="driverFormValidationEdit()" x-init="init()">

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" enctype="multipart/form-data" @submit="onSubmit" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- PHASE 1: INFORMATIONS PERSONNELLES --}}
                    <x-form-section
                        title="Informations Personnelles"
                        icon="heroicons:user"
                        subtitle="Identité, coordonnées et informations de base du chauffeur">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-input
                                        name="first_name"
                                        label="Prénom"
                                        icon="user"
                                        placeholder="Ex: Ahmed"
                                        :value="old('first_name', $driver->first_name)"
                                        required
                                        :error="$errors->first('first_name')"
                                        @blur="validateField('first_name', $event.target.value)" />

                                    <x-input
                                        name="last_name"
                                        label="Nom"
                                        icon="user"
                                        placeholder="Ex: Benali"
                                        :value="old('last_name', $driver->last_name)"
                                        required
                                        :error="$errors->first('last_name')"
                                        @blur="validateField('last_name', $event.target.value)" />

                                    <x-datepicker
                                        name="birth_date"
                                        label="Date de naissance"
                                        :value="old('birth_date', $driver->birth_date ? $driver->birth_date->format('Y-m-d') : '')"
                                        format="d/m/Y"
                                        :error="$errors->first('birth_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')" />

                                    <x-input
                                        name="personal_phone"
                                        type="tel"
                                        label="Téléphone personnel"
                                        icon="phone"
                                        placeholder="Ex: 0555123456"
                                        :value="old('personal_phone', $driver->personal_phone)"
                                        :error="$errors->first('personal_phone')" />

                                    <x-input
                                        name="personal_email"
                                        type="email"
                                        label="Email personnel"
                                        icon="envelope"
                                        placeholder="Ex: ahmed.benali@email.com"
                                        :value="old('personal_email', $driver->personal_email)"
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
                                        :selected="old('blood_type', $driver->blood_type)"
                                        :error="$errors->first('blood_type')" />

                                    <div class="md:col-span-2">
                                        <x-textarea
                                            name="address"
                                            label="Adresse"
                                            rows="3"
                                            placeholder="Adresse complète du chauffeur..."
                                            :value="old('address', $driver->address)"
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
                    </x-form-section>

                    {{-- PHASE 2: INFORMATIONS PROFESSIONNELLES --}}
                    <x-form-section
                        title="Informations Professionnelles"
                        icon="heroicons:briefcase"
                        subtitle="Matricule, statut et informations de recrutement">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-input
                                        name="employee_number"
                                        label="Matricule"
                                        icon="identification"
                                        placeholder="Ex: EMP-2024-001"
                                        :value="old('employee_number', $driver->employee_number)"
                                        :error="$errors->first('employee_number')" />

                                    <x-datepicker
                                        name="recruitment_date"
                                        label="Date de recrutement"
                                        :value="old('recruitment_date', $driver->recruitment_date ? $driver->recruitment_date->format('Y-m-d') : '')"
                                        format="d/m/Y"
                                        :error="$errors->first('recruitment_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')" />

                                    <x-datepicker
                                        name="contract_end_date"
                                        label="Fin de contrat"
                                        :value="old('contract_end_date', $driver->contract_end_date ? $driver->contract_end_date->format('Y-m-d') : '')"
                                        format="d/m/Y"
                                        :error="$errors->first('contract_end_date')"
                                        placeholder="Choisir une date"
                                        :minDate="date('Y-m-d')"
                                        helpText="Date de fin du contrat (optionnel)" />

                                    <x-slim-select
                                        name="status_id"
                                        label="Statut du Chauffeur"
                                        :options="$driverStatuses->pluck('name', 'id')->toArray()"
                                        :selected="old('status_id', $driver->status_id)"
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
                                            :value="old('notes', $driver->notes)"
                                            :error="$errors->first('notes')"
                                            helpText="Compétences, formations, remarques, etc." />
                                    </div>
                        </div>
                    </x-form-section>

                    {{-- PHASE 3: PERMIS DE CONDUIRE --}}
                    <x-form-section
                        title="Permis de Conduire"
                        icon="heroicons:identification"
                        subtitle="Numéro, catégories et dates de validité">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-input
                                        name="license_number"
                                        label="Numéro de permis"
                                        icon="identification"
                                        placeholder="Ex: 123456789"
                                        :value="old('license_number', $driver->license_number)"
                                        required
                                        :error="$errors->first('license_number')" />

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
                                        :selected="old('license_category', $driver->license_category)"
                                        required
                                        :error="$errors->first('license_category')" />

                                    <x-datepicker
                                        name="license_issue_date"
                                        label="Date de délivrance"
                                        :value="old('license_issue_date', $driver->license_issue_date ? $driver->license_issue_date->format('Y-m-d') : '')"
                                        format="d/m/Y"
                                        :error="$errors->first('license_issue_date')"
                                        placeholder="Choisir une date"
                                        :maxDate="date('Y-m-d')"
                                        required />

                                    <x-datepicker
                                        name="license_expiry_date"
                                        label="Date d'expiration"
                                        :value="old('license_expiry_date', $driver->license_expiry_date ? $driver->license_expiry_date->format('Y-m-d') : '')"
                                        format="d/m/Y"
                                        :error="$errors->first('license_expiry_date')"
                                        placeholder="Choisir une date"
                                        :minDate="date('Y-m-d')"
                                        required />

                                    <x-input
                                        name="license_authority"
                                        label="Autorité de délivrance"
                                        icon="building-office-2"
                                        placeholder="Ex: Wilaya d'Alger"
                                        :value="old('license_authority', $driver->license_authority)"
                                        :error="$errors->first('license_authority')" />

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
                    </x-form-section>

                    {{-- PHASE 4: COMPTE & CONTACT D'URGENCE --}}
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

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <x-slim-select
                                        name="user_id"
                                        label="Compte utilisateur"
                                        :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
                                        :selected="old('user_id', $driver->user_id)"
                                        placeholder="Rechercher un utilisateur..."
                                        :error="$errors->first('user_id')"
                                        helpText="Sélectionnez un compte existant ou laissez vide" />
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
                                        :value="old('emergency_contact_name', $driver->emergency_contact_name)"
                                        :error="$errors->first('emergency_contact_name')" />

                                    <x-input
                                        name="emergency_contact_phone"
                                        type="tel"
                                        label="Téléphone du contact"
                                        icon="phone"
                                        placeholder="Ex: 0555987654"
                                        :value="old('emergency_contact_phone', $driver->emergency_contact_phone)"
                                        :error="$errors->first('emergency_contact_phone')" />

                                    <x-input
                                        name="emergency_contact_relationship"
                                        label="Lien de parenté"
                                        icon="users"
                                        placeholder="Ex: Épouse, Frère, Mère"
                                        :value="old('emergency_contact_relationship', $driver->emergency_contact_relationship)"
                                        :error="$errors->first('emergency_contact_relationship')" />
                                </div>
                            </div>
                        </div>
                    </x-form-section>

                    {{-- ACTIONS FOOTER --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between">
                        <a href="{{ route('admin.drivers.show', $driver) }}"
                            class="text-gray-600 hover:text-gray-900 font-medium text-sm transition-colors">
                            Annuler
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm">
                            <x-iconify icon="heroicons:check" class="w-5 h-5" />
                            Enregistrer les Modifications
                        </button>
                    </div>
            </form>

        </div>
    </div>
</section>

@push('scripts')
<script>
    window.zenfleetDriverErrors = @json($errors->messages());
</script>
@endpush
@endsection
