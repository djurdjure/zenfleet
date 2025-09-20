@extends('layouts.admin.catalyst')

@section('title', 'Modifier Véhicule Enterprise')

@section('content')
{{-- 🚗 Header Enterprise Ultra-Professionnel --}}
<div class="zenfleet-header-enterprise">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
                <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors">
                    <i class="fas fa-car mr-1"></i>
                    Véhicules
                </a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition-colors">
                    {{ $vehicle->registration_plate }}
                </a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-blue-600 font-bold">Modification</span>
            </nav>

            <h1 class="text-4xl font-black leading-tight text-gray-900 sm:text-5xl">
                <span class="bg-gradient-to-r from-amber-600 via-orange-600 to-red-600 bg-clip-text text-transparent">
                    <i class="fas fa-edit mr-3"></i>
                    Modifier {{ $vehicle->registration_plate }}
                </span>
            </h1>

            <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    <i class="fas fa-car mr-2 h-5 w-5 text-blue-500"></i>
                    {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacturing_year }})
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    <i class="fas fa-shield-check mr-2 h-5 w-5 text-emerald-500"></i>
                    Validation Enterprise Avancée
                </div>
            </div>
        </div>

        <div class="mt-5 lg:ml-4 lg:mt-0 flex space-x-3">
            <a href="{{ route('admin.vehicles.show', $vehicle) }}"
               class="zenfleet-btn-enterprise-secondary">
                <i class="fas fa-eye mr-2"></i>
                Voir Détails
            </a>
            <a href="{{ route('admin.vehicles.index') }}"
               class="zenfleet-btn-enterprise-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

{{-- 🔥 Formulaire Enterprise Ultra-Moderne --}}
<div class="zenfleet-form-enterprise zenfleet-fade-in">
    <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST" id="vehicle-form" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Section Informations Principales --}}
        <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-6 border border-blue-200/50">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Informations Principales</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                {{-- Plaque d'Immatriculation --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="registration_plate" class="zenfleet-label-enterprise">
                        <i class="fas fa-hashtag mr-1 text-blue-600"></i>
                        Plaque d'Immatriculation
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text"
                           name="registration_plate"
                           id="registration_plate"
                           value="{{ old('registration_plate', $vehicle->registration_plate) }}"
                           placeholder="Ex: 123 ABC 01"
                           class="zenfleet-input-premium @error('registration_plate') border-red-500 @enderror"
                           required>
                    @error('registration_plate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- VIN --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="vin" class="zenfleet-label-enterprise">
                        <i class="fas fa-barcode mr-1 text-blue-600"></i>
                        Numéro VIN
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text"
                           name="vin"
                           id="vin"
                           value="{{ old('vin', $vehicle->vin) }}"
                           placeholder="17 caractères (Ex: 1HGBH41JXMN109186)"
                           maxlength="17"
                           class="zenfleet-input-premium @error('vin') border-red-500 @enderror"
                           required>
                    @error('vin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Marque --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="brand" class="zenfleet-label-enterprise">
                        <i class="fas fa-industry mr-1 text-blue-600"></i>
                        Marque
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text"
                           name="brand"
                           id="brand"
                           value="{{ old('brand', $vehicle->brand) }}"
                           placeholder="Ex: Toyota, Renault, Peugeot..."
                           class="zenfleet-input-premium @error('brand') border-red-500 @enderror"
                           required>
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Modèle --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="model" class="zenfleet-label-enterprise">
                        <i class="fas fa-car mr-1 text-blue-600"></i>
                        Modèle
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text"
                           name="model"
                           id="model"
                           value="{{ old('model', $vehicle->model) }}"
                           placeholder="Ex: Corolla, Clio, 308..."
                           class="zenfleet-input-premium @error('model') border-red-500 @enderror"
                           required>
                    @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Couleur --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="color" class="zenfleet-label-enterprise">
                        <i class="fas fa-palette mr-1 text-blue-600"></i>
                        Couleur
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text"
                           name="color"
                           id="color"
                           value="{{ old('color', $vehicle->color) }}"
                           placeholder="Ex: Blanc, Noir, Rouge..."
                           class="zenfleet-input-premium @error('color') border-red-500 @enderror"
                           required>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Année de Fabrication --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="manufacturing_year" class="zenfleet-label-enterprise">
                        <i class="fas fa-calendar mr-1 text-blue-600"></i>
                        Année de Fabrication
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="manufacturing_year"
                           id="manufacturing_year"
                           value="{{ old('manufacturing_year', $vehicle->manufacturing_year) }}"
                           min="1990"
                           max="2030"
                           placeholder="{{ date('Y') }}"
                           class="zenfleet-input-premium @error('manufacturing_year') border-red-500 @enderror"
                           required>
                    @error('manufacturing_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Configuration Technique --}}
        <div class="bg-gradient-to-r from-purple-50 via-pink-50 to-rose-50 rounded-2xl p-6 border border-purple-200/50">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Configuration Technique</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Type de Véhicule --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="vehicle_type_id" class="zenfleet-label-enterprise">
                        <i class="fas fa-tag mr-1 text-purple-600"></i>
                        Type de Véhicule
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="vehicle_type_id"
                            id="vehicle_type_id"
                            class="zenfleet-input-premium @error('vehicle_type_id') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un type</option>
                        @foreach($referenceData['vehicle_types'] ?? [] as $type)
                            <option value="{{ $type->id }}" {{ old('vehicle_type_id', $vehicle->vehicle_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type de Carburant --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="fuel_type_id" class="zenfleet-label-enterprise">
                        <i class="fas fa-gas-pump mr-1 text-purple-600"></i>
                        Type de Carburant
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="fuel_type_id"
                            id="fuel_type_id"
                            class="zenfleet-input-premium @error('fuel_type_id') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un carburant</option>
                        @foreach($referenceData['fuel_types'] ?? [] as $fuel)
                            <option value="{{ $fuel->id }}" {{ old('fuel_type_id', $vehicle->fuel_type_id) == $fuel->id ? 'selected' : '' }}>
                                {{ $fuel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('fuel_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type de Transmission --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="transmission_type_id" class="zenfleet-label-enterprise">
                        <i class="fas fa-cogs mr-1 text-purple-600"></i>
                        Transmission
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="transmission_type_id"
                            id="transmission_type_id"
                            class="zenfleet-input-premium @error('transmission_type_id') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner une transmission</option>
                        @foreach($referenceData['transmission_types'] ?? [] as $transmission)
                            <option value="{{ $transmission->id }}" {{ old('transmission_type_id', $vehicle->transmission_type_id) == $transmission->id ? 'selected' : '' }}>
                                {{ $transmission->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('transmission_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Statut --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="status_id" class="zenfleet-label-enterprise">
                        <i class="fas fa-traffic-light mr-1 text-purple-600"></i>
                        Statut
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="status_id"
                            id="status_id"
                            class="zenfleet-input-premium @error('status_id') border-red-500 @enderror"
                            required>
                        <option value="">Sélectionner un statut</option>
                        @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
                            <option value="{{ $status->id }}" {{ old('status_id', $vehicle->status_id) == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('status_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cylindrée --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="engine_displacement_cc" class="zenfleet-label-enterprise">
                        <i class="fas fa-tachometer-alt mr-1 text-purple-600"></i>
                        Cylindrée (cc)
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="engine_displacement_cc"
                           id="engine_displacement_cc"
                           value="{{ old('engine_displacement_cc', $vehicle->engine_displacement_cc) }}"
                           min="50"
                           max="10000"
                           placeholder="Ex: 1600"
                           class="zenfleet-input-premium @error('engine_displacement_cc') border-red-500 @enderror"
                           required>
                    @error('engine_displacement_cc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Puissance --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="power_hp" class="zenfleet-label-enterprise">
                        <i class="fas fa-bolt mr-1 text-purple-600"></i>
                        Puissance (HP)
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="power_hp"
                           id="power_hp"
                           value="{{ old('power_hp', $vehicle->power_hp) }}"
                           min="1"
                           max="2000"
                           placeholder="Ex: 120"
                           class="zenfleet-input-premium @error('power_hp') border-red-500 @enderror"
                           required>
                    @error('power_hp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nombre de Places --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="seats" class="zenfleet-label-enterprise">
                        <i class="fas fa-chair mr-1 text-purple-600"></i>
                        Nombre de Places
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="seats"
                           id="seats"
                           value="{{ old('seats', $vehicle->seats) }}"
                           min="1"
                           max="100"
                           placeholder="Ex: 5"
                           class="zenfleet-input-premium @error('seats') border-red-500 @enderror"
                           required>
                    @error('seats')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Informations Financières --}}
        <div class="bg-gradient-to-r from-emerald-50 via-green-50 to-teal-50 rounded-2xl p-6 border border-emerald-200/50">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-euro-sign text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Informations Financières</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Date d'Acquisition --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="acquisition_date" class="zenfleet-label-enterprise">
                        <i class="fas fa-calendar-plus mr-1 text-emerald-600"></i>
                        Date d'Acquisition
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="date"
                           name="acquisition_date"
                           id="acquisition_date"
                           value="{{ old('acquisition_date', $vehicle->acquisition_date ? $vehicle->acquisition_date->format('Y-m-d') : '') }}"
                           max="{{ date('Y-m-d') }}"
                           class="zenfleet-input-premium @error('acquisition_date') border-red-500 @enderror"
                           required>
                    @error('acquisition_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prix d'Achat --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="purchase_price" class="zenfleet-label-enterprise">
                        <i class="fas fa-money-bill mr-1 text-emerald-600"></i>
                        Prix d'Achat (€)
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="purchase_price"
                           id="purchase_price"
                           value="{{ old('purchase_price', $vehicle->purchase_price) }}"
                           min="0"
                           step="0.01"
                           placeholder="Ex: 25000.00"
                           class="zenfleet-input-premium @error('purchase_price') border-red-500 @enderror"
                           required>
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Valeur Actuelle --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="current_value" class="zenfleet-label-enterprise">
                        <i class="fas fa-chart-line mr-1 text-emerald-600"></i>
                        Valeur Actuelle (€)
                    </label>
                    <input type="number"
                           name="current_value"
                           id="current_value"
                           value="{{ old('current_value', $vehicle->current_value) }}"
                           min="0"
                           step="0.01"
                           placeholder="Ex: 20000.00"
                           class="zenfleet-input-premium @error('current_value') border-red-500 @enderror">
                    @error('current_value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kilométrage Initial --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="initial_mileage" class="zenfleet-label-enterprise">
                        <i class="fas fa-road mr-1 text-emerald-600"></i>
                        Kilométrage Initial
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="initial_mileage"
                           id="initial_mileage"
                           value="{{ old('initial_mileage', $vehicle->initial_mileage) }}"
                           min="0"
                           placeholder="Ex: 10000"
                           class="zenfleet-input-premium @error('initial_mileage') border-red-500 @enderror"
                           required>
                    @error('initial_mileage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kilométrage Actuel --}}
                <div class="zenfleet-form-group-enterprise">
                    <label for="current_mileage" class="zenfleet-label-enterprise">
                        <i class="fas fa-tachometer-alt mr-1 text-emerald-600"></i>
                        Kilométrage Actuel
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="number"
                           name="current_mileage"
                           id="current_mileage"
                           value="{{ old('current_mileage', $vehicle->current_mileage) }}"
                           min="0"
                           placeholder="Ex: 15000"
                           class="zenfleet-input-premium @error('current_mileage') border-red-500 @enderror"
                           required>
                    @error('current_mileage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Notes --}}
        <div class="bg-gradient-to-r from-amber-50 via-yellow-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-sticky-note text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Notes et Observations</h3>
            </div>

            <div class="zenfleet-form-group-enterprise">
                <label for="notes" class="zenfleet-label-enterprise">
                    <i class="fas fa-comment mr-1 text-amber-600"></i>
                    Notes (Optionnel)
                </label>
                <textarea name="notes"
                          id="notes"
                          rows="4"
                          placeholder="Informations complémentaires, observations, etc..."
                          class="zenfleet-input-premium @error('notes') border-red-500 @enderror">{{ old('notes', $vehicle->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
            <a href="{{ route('admin.vehicles.show', $vehicle) }}"
               class="zenfleet-btn-enterprise-secondary">
                <i class="fas fa-times mr-2"></i>
                Annuler
            </a>

            <button type="submit"
                    class="zenfleet-btn-enterprise-primary"
                    id="submit-btn">
                <i class="fas fa-save mr-2"></i>
                <span id="submit-text">Sauvegarder les Modifications</span>
                <div id="submit-loading" class="zenfleet-loading ml-2 hidden"></div>
            </button>
        </div>
    </form>
</div>

{{-- Recommandations Enterprise --}}
@if(isset($changeRecommendations) && !empty($changeRecommendations))
<div class="mt-8 bg-gradient-to-r from-indigo-50 via-blue-50 to-cyan-50 rounded-2xl p-6 border border-indigo-200/50">
    <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-lightbulb text-white text-sm"></i>
        </div>
        <h4 class="text-lg font-bold text-gray-900">Recommandations de Modification</h4>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($changeRecommendations as $recommendation)
            <div class="bg-white rounded-lg p-4 border border-indigo-200/30">
                <div class="flex items-start">
                    <i class="{{ $recommendation['icon'] }} text-indigo-600 mr-2 mt-1"></i>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $recommendation['title'] }}</p>
                        <p class="text-xs text-gray-600 mt-1">{{ $recommendation['description'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vehicle-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitLoading = document.getElementById('submit-loading');

    // Form submission avec état de loading
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.textContent = 'Sauvegarde en cours...';
        submitLoading.classList.remove('hidden');
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