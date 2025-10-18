@extends('layouts.admin.catalyst')

@section('title', 'Ajouter un Nouveau Véhicule')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header avec titre et icône --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <x-iconify icon="heroicons:truck" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ajouter un Nouveau Véhicule</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Complétez les 3 étapes pour enregistrer un véhicule</p>
            </div>
        </div>
    </div>

    {{-- Formulaire multi-étapes --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
         x-data="{
            currentStep: {{ old('current_step', 1) }}
        }"
        x-init="
            @if ($errors->any())
                let errors = {{ json_encode($errors->messages()) }};
                let firstErrorStep = null;
                const fieldToStepMap = {
                    'registration_plate': 1, 'vin': 1, 'brand': 1, 'model': 1, 'color': 1,
                    'vehicle_type_id': 2, 'fuel_type_id': 2, 'transmission_type_id': 2, 'manufacturing_year': 2, 'seats': 2, 'power_hp': 2, 'engine_displacement_cc': 2,
                    'acquisition_date': 3, 'purchase_price': 3, 'current_value': 3, 'initial_mileage': 3, 'status_id': 3, 'notes': 3
                };
                for (const field in fieldToStepMap) {
                    if (errors.hasOwnProperty(field)) {
                        firstErrorStep = fieldToStepMap[field];
                        break;
                    }
                }
                if (firstErrorStep) { currentStep = firstErrorStep; }
            @endif
        ">

        {{-- Progress Stepper --}}
        <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
            <ol class="flex items-center w-full">
                {{-- Étape 1: Identification --}}
                <li class="flex w-full items-center relative" :class="currentStep > 1 ? 'after:border-blue-600' : 'after:border-gray-300 dark:after:border-gray-600'" class="after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block after:absolute after:top-5 after:left-1/2">
                    <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 px-4">
                        <span class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200" :class="currentStep >= 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                            <x-iconify icon="heroicons:identification" class="w-6 h-6" />
                        </span>
                        <span class="mt-2 text-xs font-medium text-gray-900 dark:text-white" :class="currentStep >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Identification</span>
                    </div>
                </li>

                {{-- Étape 2: Caractéristiques --}}
                <li class="flex w-full items-center relative" :class="currentStep > 2 ? 'after:border-blue-600' : 'after:border-gray-300 dark:after:border-gray-600'" class="after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block after:absolute after:top-5 after:left-1/2">
                    <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 px-4">
                        <span class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200" :class="currentStep >= 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                            <x-iconify icon="heroicons:cog-6-tooth" class="w-6 h-6" />
                        </span>
                        <span class="mt-2 text-xs font-medium" :class="currentStep >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Caractéristiques</span>
                    </div>
                </li>

                {{-- Étape 3: Acquisition --}}
                <li class="flex items-center">
                    <div class="flex flex-col items-center relative z-10 bg-white dark:bg-gray-800 px-4">
                        <span class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-200" :class="currentStep === 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900/30' : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400'">
                            <x-iconify icon="heroicons:currency-dollar" class="w-6 h-6" />
                        </span>
                        <span class="mt-2 text-xs font-medium" :class="currentStep === 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">Acquisition</span>
                    </div>
                </li>
            </ol>
        </div>

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <input type="hidden" name="current_step" x-model="currentStep">

            {{-- ÉTAPE 1: IDENTIFICATION --}}
            <div x-show="currentStep === 1" class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <x-iconify icon="heroicons:identification" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Identification du Véhicule</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input
                        name="registration_plate"
                        label="Immatriculation"
                        icon="identification"
                        placeholder="Ex: 16-12345-23"
                        :value="old('registration_plate')"
                        required
                        :error="$errors->first('registration_plate')"
                        helpText="Numéro d'immatriculation officiel du véhicule"
                    />

                    <x-input
                        name="vin"
                        label="Numéro de série (VIN)"
                        icon="finger-print"
                        placeholder="Ex: 1HGBH41JXMN109186"
                        :value="old('vin')"
                        :error="$errors->first('vin')"
                        helpText="Vehicle Identification Number - 17 caractères"
                    />

                    <x-input
                        name="brand"
                        label="Marque"
                        icon="building-storefront"
                        placeholder="Ex: Renault, Peugeot, Toyota..."
                        :value="old('brand')"
                        required
                        :error="$errors->first('brand')"
                    />

                    <x-input
                        name="model"
                        label="Modèle"
                        icon="truck"
                        placeholder="Ex: Clio, 208, Corolla..."
                        :value="old('model')"
                        required
                        :error="$errors->first('model')"
                    />

                    <div class="md:col-span-2">
                        <x-input
                            name="color"
                            label="Couleur"
                            icon="swatch"
                            placeholder="Ex: Blanc, Noir, Gris métallisé..."
                            :value="old('color')"
                            :error="$errors->first('color')"
                        />
                    </div>
                </div>
            </div>

            {{-- ÉTAPE 2: CARACTÉRISTIQUES TECHNIQUES --}}
            <div x-show="currentStep === 2" style="display: none;" class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <x-iconify icon="heroicons:cog-6-tooth" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Caractéristiques Techniques</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Type de Véhicule avec TomSelect --}}
                    <x-tom-select
                        name="vehicle_type_id"
                        label="Type de Véhicule"
                        :options="$vehicleTypes->pluck('name', 'id')->toArray()"
                        :selected="old('vehicle_type_id')"
                        placeholder="Sélectionnez un type..."
                        required
                        :error="$errors->first('vehicle_type_id')"
                    />

                    {{-- Type de Carburant avec TomSelect --}}
                    <x-tom-select
                        name="fuel_type_id"
                        label="Type de Carburant"
                        :options="$fuelTypes->pluck('name', 'id')->toArray()"
                        :selected="old('fuel_type_id')"
                        placeholder="Sélectionnez un carburant..."
                        required
                        :error="$errors->first('fuel_type_id')"
                    />

                    {{-- Type de Transmission avec TomSelect --}}
                    <x-tom-select
                        name="transmission_type_id"
                        label="Type de Transmission"
                        :options="$transmissionTypes->pluck('name', 'id')->toArray()"
                        :selected="old('transmission_type_id')"
                        placeholder="Sélectionnez une transmission..."
                        required
                        :error="$errors->first('transmission_type_id')"
                    />

                    <x-input
                        type="number"
                        name="manufacturing_year"
                        label="Année de Fabrication"
                        icon="calendar"
                        placeholder="Ex: 2024"
                        :value="old('manufacturing_year')"
                        :error="$errors->first('manufacturing_year')"
                        min="1900"
                        max="2099"
                    />

                    <x-input
                        type="number"
                        name="seats"
                        label="Nombre de places"
                        icon="user-group"
                        placeholder="Ex: 5"
                        :value="old('seats')"
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
                        :value="old('power_hp')"
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
                            :value="old('engine_displacement_cc')"
                            :error="$errors->first('engine_displacement_cc')"
                            helpText="Capacité du moteur en centimètres cubes"
                            min="0"
                        />
                    </div>
                </div>
            </div>

            {{-- ÉTAPE 3: ACQUISITION & STATUT --}}
            <div x-show="currentStep === 3" style="display: none;" class="space-y-6">
                <div class="flex items-center gap-3 mb-6">
                    <x-iconify icon="heroicons:currency-dollar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Acquisition & Statut</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-datepicker
                        name="acquisition_date"
                        label="Date d'acquisition"
                        :value="old('acquisition_date')"
                        format="d/m/Y"
                        :error="$errors->first('acquisition_date')"
                        placeholder="Choisir une date"
                    />

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
                        helpText="Prix d'achat en Dinars Algériens"
                    />

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
                        helpText="Valeur estimée actuelle"
                    />

                    <x-input
                        type="number"
                        name="initial_mileage"
                        label="Kilométrage Initial"
                        icon="chart-bar"
                        placeholder="Ex: 0"
                        :value="old('initial_mileage', 0)"
                        :error="$errors->first('initial_mileage')"
                        min="0"
                        helpText="Kilométrage au moment de l'acquisition"
                    />

                    {{-- Statut Initial avec TomSelect --}}
                    <div class="md:col-span-2">
                        <x-tom-select
                            name="status_id"
                            label="Statut Initial"
                            :options="$vehicleStatuses->pluck('name', 'id')->toArray()"
                            :selected="old('status_id')"
                            placeholder="Sélectionnez un statut..."
                            required
                            :error="$errors->first('status_id')"
                        />
                    </div>

                    {{-- Utilisateurs Autorisés avec TomSelect Multiple --}}
                    <div class="md:col-span-2">
                        <x-tom-select
                            name="users"
                            label="Utilisateurs Autorisés"
                            :options="$users->mapWithKeys(fn($user) => [$user->id => $user->name . ' (' . $user->email . ')'])->toArray()"
                            :selected="old('users', [])"
                            placeholder="Rechercher des utilisateurs..."
                            :multiple="true"
                            :error="$errors->first('users')"
                            helpText="Recherchez et sélectionnez les utilisateurs autorisés à utiliser ce véhicule"
                        />
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <x-textarea
                            name="notes"
                            label="Notes"
                            rows="4"
                            placeholder="Informations complémentaires sur le véhicule..."
                            :value="old('notes')"
                            :error="$errors->first('notes')"
                            helpText="Ajoutez toute information utile (état, équipements, historique...)"
                        />
                    </div>
                </div>
            </div>

            {{-- Actions Footer --}}
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <x-button
                        type="button"
                        variant="secondary"
                        icon="arrow-left"
                        x-show="currentStep > 1"
                        @click="currentStep--"
                    >
                        Précédent
                    </x-button>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.vehicles.index') }}"
                       class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Annuler
                    </a>

                    <x-button
                        type="button"
                        variant="primary"
                        icon="arrow-right"
                        x-show="currentStep < 3"
                        @click="currentStep++"
                    >
                        Suivant
                    </x-button>

                    <x-button
                        type="submit"
                        variant="success"
                        icon="check-circle"
                        x-show="currentStep === 3"
                    >
                        Enregistrer le Véhicule
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
