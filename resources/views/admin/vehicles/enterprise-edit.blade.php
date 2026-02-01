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
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                Modifier le Véhicule: {{ $vehicle->registration_plate }}
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les sections ci-dessous pour mettre à jour le véhicule
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
        <div x-data="vehicleFormValidationEdit()" x-init="init()">

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" @submit="onSubmit" class="space-y-8">
                @csrf
                @method('PUT')

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
                            :value="old('registration_plate', $vehicle->registration_plate)"
                            required
                            :error="$errors->first('registration_plate')"
                            helpText="Numéro d'immatriculation officiel du véhicule"
                            @blur="validateField('registration_plate', $event.target.value)" />

                        <x-input
                            name="vin"
                            label="Numéro de série (VIN)"
                            icon="finger-print"
                            placeholder="Ex: 1HGBH41JXMN109186"
                            :value="old('vin', $vehicle->vin)"
                            :error="$errors->first('vin')"
                            helpText="17 caractères"
                            maxlength="17"
                            @blur="validateField('vin', $event.target.value)" />

                        <x-input
                            name="brand"
                            label="Marque"
                            icon="building-storefront"
                            placeholder="Ex: Renault, Peugeot, Toyota..."
                            :value="old('brand', $vehicle->brand)"
                            required
                            :error="$errors->first('brand')"
                            @blur="validateField('brand', $event.target.value)" />

                        <x-input
                            name="model"
                            label="Modèle"
                            icon="truck"
                            placeholder="Ex: Clio, 208, Corolla..."
                            :value="old('model', $vehicle->model)"
                            :error="$errors->first('model')"
                            @blur="validateField('model', $event.target.value)" />

                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-input
                            name="color"
                            label="Couleur"
                            icon="swatch"
                            placeholder="Ex: Blanc, Noir, Gris métallisé..."
                            :value="old('color', $vehicle->color)"
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
                            :selected="old('vehicle_type_id', $vehicle->vehicle_type_id)"
                            placeholder="Sélectionnez un type..."
                            :error="$errors->first('vehicle_type_id')"
                            @change="validateField('vehicle_type_id', $event.target.value)" />

                        <x-slim-select
                            name="fuel_type_id"
                            label="Type de Carburant"
                            :options="$fuelTypes->pluck('name', 'id')->toArray()"
                            :selected="old('fuel_type_id', $vehicle->fuel_type_id)"
                            placeholder="Sélectionnez un carburant..."
                            required
                            :error="$errors->first('fuel_type_id')"
                            @change="validateField('fuel_type_id', $event.target.value)" />

                        <x-slim-select
                            name="transmission_type_id"
                            label="Type de Transmission"
                            :options="$transmissionTypes->pluck('name', 'id')->toArray()"
                            :selected="old('transmission_type_id', $vehicle->transmission_type_id)"
                            placeholder="Sélectionnez une transmission..."
                            :error="$errors->first('transmission_type_id')"
                            @change="validateField('transmission_type_id', $event.target.value)" />

                        <x-input
                            type="number"
                            name="manufacturing_year"
                            label="Année de Fabrication"
                            icon="calendar"
                            placeholder="Ex: 2024"
                            :value="old('manufacturing_year', $vehicle->manufacturing_year)"
                            :error="$errors->first('manufacturing_year')"
                            min="1950"
                            :max="date('Y') + 1" />

                        <x-input
                            type="number"
                            name="seats"
                            label="Nombre de places"
                            icon="user-group"
                            placeholder="Ex: 5"
                            :value="old('seats', $vehicle->seats)"
                            :error="$errors->first('seats')"
                            min="1"
                            max="99" />

                        <x-input
                            type="number"
                            name="power_hp"
                            label="Puissance (CV)"
                            icon="bolt"
                            placeholder="Ex: 90"
                            :value="old('power_hp', $vehicle->power_hp)"
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
                            :value="old('engine_displacement_cc', $vehicle->engine_displacement_cc)"
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
                            :value="old('acquisition_date', $vehicle->acquisition_date?->format('d/m/Y'))"
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
                            :value="old('purchase_price', $vehicle->purchase_price)"
                            :error="$errors->first('purchase_price')"
                            step="0.01"
                            min="0"
                            helpText="Prix d'achat en Dinars Algériens" />

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
                            helpText="Valeur estimée actuelle" />

                        <x-input
                            type="number"
                            name="current_mileage"
                            label="Kilométrage Actuel"
                            icon="chart-bar"
                            placeholder="Ex: 15000"
                            :value="old('current_mileage', $vehicle->current_mileage)"
                            :error="$errors->first('current_mileage')"
                            min="0"
                            helpText="Kilométrage actuel du véhicule" />

                    </x-field-group>

                    <div class="col-span-6 mt-6">
                        <x-slim-select
                            name="status_id"
                            label="Statut Initial"
                            :options="$vehicleStatuses->pluck('name', 'id')->toArray()"
                            :selected="old('status_id', $vehicle->status_id)"
                            placeholder="Sélectionnez un statut..."
                            :error="$errors->first('status_id')"
                            helpText="État opérationnel du véhicule"
                            @change="validateField('status_id', $event.target.value)" />
                    </div>

                    <div class="md:col-span-2">
                        <x-slim-select
                            name="users"
                            label="Utilisateurs Autorisés"
                            :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
                            :selected="old('users', $vehicle->users->pluck('id')->toArray())"
                            placeholder="Rechercher des utilisateurs..."
                            :multiple="true"
                            :error="$errors->first('users')"
                            helpText="Sélectionnez les utilisateurs autorisés à utiliser ce véhicule" />
                    </div>

                    <div class="md:col-span-2">
                        <x-textarea
                            name="notes"
                            label="Notes"
                            rows="4"
                            placeholder="Informations complémentaires sur le véhicule..."
                            :value="old('notes', $vehicle->notes)"
                            :error="$errors->first('notes')"
                            helpText="Ajoutez toute information utile (état, équipements, historique...)" />
                    </div>
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
                Enregistrer les Modifications
            </x-button>
        </div>
        </form>

    </div>

    </div>
</section>

<div id="zenfleet-errors-data"
    data-has-errors="{{ $errors->any() ? '1' : '0' }}"
    data-keys='@json($errors->keys())'
    class="hidden"></div>

@push('scripts')
<script>
    (() => {
        const errorsData = document.getElementById('zenfleet-errors-data');
        window.zenfleetErrors = {
            hasErrors: errorsData ? errorsData.dataset.hasErrors === '1' : false,
            keys: errorsData && errorsData.dataset.keys ? JSON.parse(errorsData.dataset.keys) : []
        };
    })();
</script>
@endpush

@endsection
