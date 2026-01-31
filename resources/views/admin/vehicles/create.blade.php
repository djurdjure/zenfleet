@extends('layouts.admin.catalyst')

@section('title', 'Ajouter un Nouveau Véhicule')

@section('content')
{{-- ====================================================================
 🚗 FORMULAIRE CRÉATION VÉHICULE - ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Validation en temps réel à chaque phase
 - Empêchement navigation si étape invalide
 - Indicateurs visuels de validation
 - Messages d'erreur clairs et contextuels
 - Animation des transitions
 - Support Dark Mode
 
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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="heroicons:truck" class="w-6 h-6 text-blue-600" />
                Ajouter un Nouveau Véhicule
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les sections ci-dessous pour enregistrer un véhicule
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
        <div x-data="vehicleFormValidationCreate()" x-init="init()">

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('admin.vehicles.store') }}" @submit="onSubmit" class="space-y-8">
                @csrf

                {{-- ===========================================
 PHASE 1: IDENTIFICATION
 =========================================== --}}
                <x-form-section
                    title="Informations d'Identification"
                    icon="heroicons:identification"
                    subtitle="Données essentielles pour identifier le véhicule">
                    <x-field-group :columns="2">
                        <x-input
                            name="registration_plate"
                            label="Immatriculation"
                            icon="identification"
                            placeholder="Ex: 16-12345-23"
                            :value="old('registration_plate')"
                            required
                            :error="$errors->first('registration_plate')"
                            helpText="Numéro d'immatriculation officiel du véhicule"
                            @blur="validateField('registration_plate', $event.target.value)" />

                        <x-input
                            name="vin"
                            label="Numéro de série (VIN)"
                            icon="finger-print"
                            placeholder="Ex: 1HGBH41JXMN109186"
                            :value="old('vin')"
                            :error="$errors->first('vin')"
                            helpText="17 caractères"
                            maxlength="17"
                            @blur="validateField('vin', $event.target.value)" />
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">
                        <x-input
                            name="brand"
                            label="Marque"
                            icon="building-storefront"
                            placeholder="Ex: Renault, Peugeot, Toyota..."
                            :value="old('brand')"
                            required
                            :error="$errors->first('brand')"
                            @blur="validateField('brand', $event.target.value)" />

                        <x-input
                            name="model"
                            label="Modèle"
                            icon="truck"
                            placeholder="Ex: Clio, 208, Corolla..."
                            :value="old('model')"
                            :error="$errors->first('model')"
                            @blur="validateField('model', $event.target.value)" />
                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-input
                            name="color"
                            label="Couleur"
                            icon="swatch"
                            placeholder="Ex: Blanc, Noir, Gris métallisé..."
                            :value="old('color')"
                            :error="$errors->first('color')" />
                    </div>
                </x-form-section>

                {{-- ===========================================
 PHASE 2: CARACTÉRISTIQUES TECHNIQUES
 =========================================== --}}
                <x-form-section
                    title="Caractéristiques Techniques"
                    icon="heroicons:cog-6-tooth"
                    subtitle="Spécifications du véhicule et configuration mécanique">
                    <x-field-group :columns="3" :divided="false">
                        <x-slim-select
                            name="vehicle_type_id"
                            label="Type de Véhicule"
                            :options="$vehicleTypes->pluck('name', 'id')->toArray()"
                            :selected="old('vehicle_type_id')"
                            placeholder="Sélectionnez un type..."
                            :error="$errors->first('vehicle_type_id')"
                            @change="validateField('vehicle_type_id', $event.target.value)" />

                        <x-slim-select
                            name="fuel_type_id"
                            label="Type de Carburant"
                            :options="$fuelTypes->pluck('name', 'id')->toArray()"
                            :selected="old('fuel_type_id')"
                            placeholder="Sélectionnez un carburant..."
                            required
                            :error="$errors->first('fuel_type_id')"
                            @change="validateField('fuel_type_id', $event.target.value)" />

                        <x-slim-select
                            name="transmission_type_id"
                            label="Type de Transmission"
                            :options="$transmissionTypes->pluck('name', 'id')->toArray()"
                            :selected="old('transmission_type_id')"
                            placeholder="Sélectionnez une transmission..."
                            :error="$errors->first('transmission_type_id')"
                            @change="validateField('transmission_type_id', $event.target.value)" />
                    </x-field-group>

                    <x-field-group :columns="3" :divided="false" class="mt-6">
                        <x-input
                            type="number"
                            name="manufacturing_year"
                            label="Année de Fabrication"
                            icon="calendar"
                            placeholder="Ex: 2024"
                            :value="old('manufacturing_year')"
                            :error="$errors->first('manufacturing_year')"
                            min="1950"
                            :max="date('Y') + 1" />

                        <x-input
                            type="number"
                            name="seats"
                            label="Nombre de places"
                            icon="user-group"
                            placeholder="Ex: 5"
                            :value="old('seats')"
                            :error="$errors->first('seats')"
                            min="1"
                            max="99" />

                        <x-input
                            type="number"
                            name="power_hp"
                            label="Puissance (CV)"
                            icon="bolt"
                            placeholder="Ex: 90"
                            :value="old('power_hp')"
                            :error="$errors->first('power_hp')"
                            min="0" />
                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-input
                            type="number"
                            name="engine_displacement_cc"
                            label="Cylindrée (cc)"
                            icon="wrench-screwdriver"
                            placeholder="Ex: 1500"
                            :value="old('engine_displacement_cc')"
                            :error="$errors->first('engine_displacement_cc')"
                            helpText="Capacité du moteur en centimètres cubes"
                            min="0" />
                    </div>
                </x-form-section>

                {{-- ===========================================
 PHASE 3: ACQUISITION & STATUT
 =========================================== --}}
                <x-form-section
                    title="Acquisition & Statut"
                    icon="heroicons:currency-dollar"
                    subtitle="Valeur d'achat, statut initial et informations d'usage">
                    <x-field-group :columns="2">
                        <x-datepicker
                            name="acquisition_date"
                            label="Date d'acquisition"
                            :value="old('acquisition_date')"
                            format="d/m/Y"
                            :error="$errors->first('acquisition_date')"
                            placeholder="Choisir une date"
                            :maxDate="date('Y-m-d')"
                            helpText="Date d'achat du véhicule" />

                        <x-input
                            type="number"
                            name="purchase_price"
                            label="Prix d'achat (DA)"
                            icon="currency-dollar"
                            placeholder="Ex: 2500000"
                            :value="old('purchase_price')"
                            :error="$errors->first('purchase_price')"
                            step="0.01"
                            min="0"
                            helpText="Prix d'achat en Dinars Algériens" />
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">
                        <x-input
                            type="number"
                            name="current_value"
                            label="Valeur actuelle (DA)"
                            icon="currency-dollar"
                            placeholder="Ex: 2000000"
                            :value="old('current_value')"
                            :error="$errors->first('current_value')"
                            step="0.01"
                            min="0"
                            helpText="Valeur estimée actuelle" />

                        <x-input
                            type="number"
                            name="initial_mileage"
                            label="Kilométrage Initial"
                            icon="chart-bar"
                            placeholder="Ex: 0"
                            :value="old('initial_mileage', 0)"
                            :error="$errors->first('initial_mileage')"
                            min="0"
                            helpText="Kilométrage au moment de l'acquisition" />
                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-slim-select
                            name="status_id"
                            label="Statut Initial"
                            :options="$vehicleStatuses->pluck('name', 'id')->toArray()"
                            :selected="old('status_id')"
                            placeholder="Sélectionnez un statut..."
                            :error="$errors->first('status_id')"
                            helpText="État opérationnel du véhicule"
                            @change="validateField('status_id', $event.target.value)" />
                    </div>

                    <div class="col-span-6 mt-6">
                        <x-slim-select
                            name="users"
                            label="Utilisateurs Autorisés"
                            :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
                            :selected="old('users', [])"
                            placeholder="Rechercher des utilisateurs..."
                            :multiple="true"
                            :error="$errors->first('users')"
                            helpText="Sélectionnez les utilisateurs autorisés à utiliser ce véhicule" />
                    </div>

                    <div class="col-span-6 mt-6">
                        <x-textarea
                            name="notes"
                            label="Notes"
                            rows="4"
                            placeholder="Informations complémentaires sur le véhicule..."
                            :value="old('notes')"
                            :error="$errors->first('notes')"
                            helpText="Ajoutez toute information utile (état, équipements, historique...)" />
                    </div>
                </x-form-section>

                {{-- ===========================================
 ACTIONS FOOTER
 =========================================== --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between">
                    <a href="{{ route('admin.vehicles.index') }}"
                        class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                        Annuler
                    </a>

                    <x-button
                        type="submit"
                        variant="success"
                        icon="check-circle">
                        Enregistrer le Véhicule
                    </x-button>
                </div>
            </form>

        </div>

    </div>
</section>

@push('scripts')
<script>
    window.zenfleetErrors = {
        hasErrors: @json($errors -> any()),
        keys: @json($errors -> keys())
    };
</script>
@endpush

@endsection