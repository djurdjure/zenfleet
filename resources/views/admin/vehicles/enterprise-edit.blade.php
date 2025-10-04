{{-- resources/views/admin/vehicles/enterprise-edit.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Modifier Véhicule - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.form-section:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}

.form-input {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.form-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background: white;
}

.tom-select .ts-control {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.tom-select.focus .ts-control {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.section-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.75rem;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    color: white;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
}

.btn-secondary {
    background: white;
    border: 2px solid #e2e8f0;
    color: #6b7280;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    border-color: #6366f1;
    color: #6366f1;
    transform: translateY(-1px);
}

.vehicle-info-badge {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
}
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- En-tête compact --}}
    <div class="mb-8">
        <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
            <a href="{{ route('admin.vehicles.index') }}" class="hover:text-indigo-600 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                </svg>
                Véhicules
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="hover:text-indigo-600 transition-colors">
                {{ $vehicle->registration_plate }}
            </a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-indigo-600 font-semibold">Modifier</span>
        </nav>

        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-semibold leading-6 text-gray-900">Modifier {{ $vehicle->registration_plate }}</h1>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                        </svg>
                        {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacturing_year }})
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Voir détails
                </a>
                <a href="{{ route('admin.vehicles.index') }}" class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
            </div>
        </div>
    </div>

    {{-- Informations véhicule --}}
    <div class="vehicle-info-badge rounded-lg p-4 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-900">Véhicule actuel</h3>
                    <p class="text-sm text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }} • {{ number_format($vehicle->current_mileage) }} km</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">VIN: {{ $vehicle->vin }}</p>
                <p class="text-sm text-gray-500">Créé le {{ $vehicle->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Résumé des erreurs --}}
    <x-form-error-summary />

    {{-- Formulaire --}}
    <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" id="vehicle-form" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Section Informations principales --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-blue-500 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Informations principales</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-vehicle-form-field
                    name="registration_plate"
                    label="Plaque d'immatriculation"
                    type="text"
                    value="{{ $vehicle->registration_plate }}"
                    placeholder="Ex: 123 ABC 01"
                    required="true"
                    icon="fas fa-id-card" />

                <x-vehicle-form-field
                    name="vin"
                    label="Numéro VIN"
                    type="text"
                    value="{{ $vehicle->vin }}"
                    placeholder="17 caractères (optionnel)"
                    help="Numéro d'identification unique du véhicule"
                    icon="fas fa-barcode" />

                <x-vehicle-form-field
                    name="vehicle_name"
                    label="Nom du véhicule"
                    type="text"
                    value="{{ $vehicle->vehicle_name }}"
                    placeholder="Ex: Camion Livraison 1"
                    help="Nom unique pour identifier facilement ce véhicule"
                    icon="fas fa-tag" />

                {{-- Category Select --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-layer-group mr-1 text-gray-500"></i>
                        Catégorie
                    </label>
                    <select name="category_id" id="category_id" class="form-input w-full rounded-lg">
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($referenceData['categories'] ?? [] as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', $vehicle->category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Depot Select --}}
                <div>
                    <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse mr-1 text-gray-500"></i>
                        Dépôt
                    </label>
                    <select name="depot_id" id="depot_id" class="form-input w-full rounded-lg">
                        <option value="">-- Sélectionner un dépôt --</option>
                        @foreach($referenceData['depots'] ?? [] as $depot)
                            <option value="{{ $depot->id }}" {{ (old('depot_id', $vehicle->depot_id) == $depot->id) ? 'selected' : '' }}>
                                {{ $depot->name }} - {{ $depot->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('depot_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-vehicle-form-field
                    name="brand"
                    label="Marque"
                    type="text"
                    value="{{ $vehicle->brand }}"
                    placeholder="Ex: Toyota, Renault..."
                    icon="fas fa-trademark" />

                <x-vehicle-form-field
                    name="model"
                    label="Modèle"
                    type="text"
                    value="{{ $vehicle->model }}"
                    placeholder="Ex: Corolla, Clio..."
                    icon="fas fa-car" />

                <x-vehicle-form-field
                    name="color"
                    label="Couleur"
                    type="text"
                    value="{{ $vehicle->color }}"
                    placeholder="Ex: Blanc, Noir..."
                    icon="fas fa-palette" />

                <x-vehicle-form-field
                    name="manufacturing_year"
                    label="Année de fabrication"
                    type="number"
                    value="{{ $vehicle->manufacturing_year }}"
                    placeholder="{{ date('Y') }}"
                    icon="fas fa-calendar-alt" />
            </div>
        </div>

        {{-- Section Configuration technique --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-purple-500 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Configuration technique</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <x-vehicle-form-field
                    name="vehicle_type_id"
                    label="Type de véhicule"
                    type="select"
                    value="{{ $vehicle->vehicle_type_id }}"
                    placeholder="Sélectionner un type..."
                    :options="collect($referenceData['vehicle_types'] ?? [])->pluck('name', 'id')->toArray()"
                    icon="fas fa-truck" />

                <x-vehicle-form-field
                    name="fuel_type_id"
                    label="Type de carburant"
                    type="select"
                    value="{{ $vehicle->fuel_type_id }}"
                    placeholder="Sélectionner un carburant..."
                    :options="collect($referenceData['fuel_types'] ?? [])->pluck('name', 'id')->toArray()"
                    icon="fas fa-gas-pump" />

                <x-vehicle-form-field
                    name="transmission_type_id"
                    label="Transmission"
                    type="select"
                    value="{{ $vehicle->transmission_type_id }}"
                    placeholder="Sélectionner une transmission..."
                    :options="collect($referenceData['transmission_types'] ?? [])->pluck('name', 'id')->toArray()"
                    icon="fas fa-cogs" />

                <x-vehicle-form-field
                    name="status_id"
                    label="Statut"
                    type="select"
                    value="{{ $vehicle->status_id }}"
                    placeholder="Sélectionner un statut..."
                    :options="collect($referenceData['vehicle_statuses'] ?? [])->pluck('name', 'id')->toArray()"
                    help="Par défaut: Disponible"
                    icon="fas fa-info-circle" />

                <x-vehicle-form-field
                    name="engine_displacement_cc"
                    label="Cylindrée (cc)"
                    type="number"
                    value="{{ $vehicle->engine_displacement_cc }}"
                    placeholder="Ex: 1600"
                    icon="fas fa-engine" />

                <x-vehicle-form-field
                    name="power_hp"
                    label="Puissance (HP)"
                    type="number"
                    value="{{ $vehicle->power_hp }}"
                    placeholder="Ex: 120"
                    icon="fas fa-tachometer-alt" />

                <x-vehicle-form-field
                    name="seats"
                    label="Nombre de places"
                    type="number"
                    value="{{ $vehicle->seats }}"
                    placeholder="Ex: 5"
                    icon="fas fa-user-friends" />
            </div>
        </div>

        {{-- Section Informations financières --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-green-500 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Informations financières et kilométrage</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <x-vehicle-form-field
                    name="acquisition_date"
                    label="Date d'acquisition"
                    type="date"
                    value="{{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('Y-m-d') : '' }}"
                    icon="fas fa-calendar" />

                <x-vehicle-form-field
                    name="purchase_price"
                    label="Prix d'achat (€)"
                    type="number"
                    value="{{ $vehicle->purchase_price }}"
                    placeholder="Ex: 25000.00"
                    icon="fas fa-euro-sign" />

                <x-vehicle-form-field
                    name="current_value"
                    label="Valeur actuelle (€)"
                    type="number"
                    value="{{ $vehicle->current_value }}"
                    placeholder="Ex: 20000.00"
                    help="Valeur estimée actuelle du véhicule"
                    icon="fas fa-chart-line" />

                <x-vehicle-form-field
                    name="initial_mileage"
                    label="Kilométrage initial"
                    type="number"
                    value="{{ $vehicle->initial_mileage }}"
                    placeholder="Ex: 10000"
                    icon="fas fa-road" />

                <x-vehicle-form-field
                    name="current_mileage"
                    label="Kilométrage actuel"
                    type="number"
                    value="{{ $vehicle->current_mileage }}"
                    placeholder="Ex: 15000"
                    icon="fas fa-tachometer-alt" />
            </div>
        </div>

        {{-- Section Notes --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-yellow-500 text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Notes et observations</h3>
            </div>

            <x-vehicle-form-field
                name="notes"
                label="Notes"
                type="textarea"
                value="{{ $vehicle->notes }}"
                placeholder="Informations complémentaires, observations, etc..."
                help="Champ optionnel pour des informations supplémentaires"
                icon="fas fa-sticky-note" />
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Annuler
            </a>
            <button type="submit" class="btn-primary inline-flex items-center px-6 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="submit-btn">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span id="submit-text">Sauvegarder les modifications</span>
            </button>
        </div>
    </form>

    {{-- Recommandations --}}
    @if(isset($changeRecommendations) && !empty($changeRecommendations))
    <div class="mt-8 bg-orange-50 rounded-lg p-6 border border-orange-200">
        <div class="flex items-center mb-4">
            <div class="w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h4 class="text-lg font-medium text-gray-900">Recommandations de modification</h4>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach($changeRecommendations as $recommendation)
                <div class="bg-white rounded-lg p-4 border border-orange-100">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $recommendation['title'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $recommendation['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TomSelect for all select elements
    ['tom-select-type', 'tom-select-fuel', 'tom-select-transmission', 'tom-select-status'].forEach(className => {
        const element = document.querySelector('.' + className);
        if (element) {
            new TomSelect(element, {
                plugins: ['clear_button'],
                placeholder: 'Sélectionner...',
                allowEmptyOption: true,
                searchField: ['text'],
                render: {
                    no_results: function() {
                        return '<div class="no-results">Aucun résultat trouvé</div>';
                    }
                }
            });
        }
    });

    const form = document.getElementById('vehicle-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');

    // Form submission avec état de loading
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.textContent = 'Sauvegarde en cours...';
    });

    // Validation du VIN
    const vinInput = document.getElementById('vin');
    vinInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validation des kilométrages
    const initialMileage = document.getElementById('initial_mileage');
    const currentMileage = document.getElementById('current_mileage');

    function validateMileage() {
        const initial = parseInt(initialMileage.value) || 0;
        const current = parseInt(currentMileage.value) || 0;

        if (current < initial) {
            currentMileage.setCustomValidity('Le kilométrage actuel ne peut pas être inférieur au kilométrage initial');
        } else {
            currentMileage.setCustomValidity('');
        }
    }

    initialMileage.addEventListener('input', validateMileage);
    currentMileage.addEventListener('input', validateMileage);

    // Validation du prix et valeur
    const purchasePrice = document.getElementById('purchase_price');
    const currentValue = document.getElementById('current_value');

    function validatePrice() {
        const purchase = parseFloat(purchasePrice.value) || 0;
        const current = parseFloat(currentValue.value) || 0;

        if (current > purchase && current > 0) {
            currentValue.setCustomValidity('La valeur actuelle ne peut pas être supérieure au prix d\'achat');
        } else {
            currentValue.setCustomValidity('');
        }
    }

    purchasePrice.addEventListener('input', validatePrice);
    currentValue.addEventListener('input', validatePrice);
});
</script>
@endpush