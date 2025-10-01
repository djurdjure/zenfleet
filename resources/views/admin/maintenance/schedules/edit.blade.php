{{-- resources/views/admin/maintenance/schedules/edit.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Modifier Planification - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .slide-in {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .form-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .input-focus {
        transition: all 0.2s ease;
    }

    .input-focus:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }

    .stepper {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .step {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .step-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .step.active .step-circle {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
    }

    .step.completed .step-circle {
        background: #10b981;
        color: white;
    }

    .step.inactive .step-circle {
        background: #f3f4f6;
        color: #6b7280;
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #e5e7eb;
        margin: 0 1rem;
    }

    .step.completed .step-line {
        background: #10b981;
    }
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- En-tête avec breadcrumb --}}
    <div class="mb-8">
        <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4 slide-in">
            <a href="{{ route('admin.maintenance.dashboard') }}" class="hover:text-indigo-600 transition-colors">
                <i class="fas fa-wrench mr-1"></i> Maintenance
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="{{ route('admin.maintenance.schedules.index') }}" class="hover:text-indigo-600 transition-colors">
                Planifications
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-indigo-600 font-semibold">Nouvelle planification</span>
        </nav>

        <div class="gradient-bg rounded-xl p-6 text-white slide-in">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-6">Planifier une Maintenance</h1>
                    <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="flex items-center text-indigo-100">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Configuration d'une nouvelle planification de maintenance
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 md:ml-4">
                    <div class="flex items-center space-x-3 text-indigo-100">
                        <i class="fas fa-info-circle"></i>
                        <span class="text-sm">Assistant intelligent de planification</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stepper de progression --}}
    <div class="stepper mb-8">
        <div class="step active">
            <div class="step-circle">1</div>
            <div class="step-line"></div>
        </div>
        <div class="step inactive">
            <div class="step-circle">2</div>
            <div class="step-line"></div>
        </div>
        <div class="step inactive">
            <div class="step-circle">3</div>
            <div class="step-line"></div>
        </div>
        <div class="step inactive">
            <div class="step-circle">4</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.maintenance.schedules.update', $schedule->id) }}" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Étape 1: Sélection du véhicule et type de maintenance --}}
        <div class="form-section p-6" id="step-1">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-car text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Sélection du Véhicule et Type</h3>
                    <p class="text-sm text-gray-600">Choisissez le véhicule et le type de maintenance à planifier</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Sélection du véhicule --}}
                <div class="space-y-2">
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-car mr-2 text-blue-500"></i>Véhicule
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="vehicle_id" id="vehicle_id" required
                            class="input-focus block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionner un véhicule...</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}"
                                    data-mileage="{{ $vehicle->current_mileage ?? 0 }}"
                                    data-brand="{{ $vehicle->brand }}"
                                    data-model="{{ $vehicle->model }}"
                                    {{ old('vehicle_id', $schedule->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                @if($vehicle->current_mileage)
                                    ({{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sélection du type de maintenance --}}
                <div class="space-y-2">
                    <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-wrench mr-2 text-green-500"></i>Type de Maintenance
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="maintenance_type_id" id="maintenance_type_id" required
                            class="input-focus block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Sélectionner un type...</option>
                        @php $lastCategory = null; @endphp
                        @foreach($maintenanceTypes as $type)
                            @if($lastCategory !== $type->category)
                                @if($lastCategory !== null)
                                    </optgroup>
                                @endif
                                <optgroup label="{{ ucfirst($type->category) }}">
                                @php $lastCategory = $type->category; @endphp
                            @endif
                            <option value="{{ $type->id }}"
                                    data-interval-km="{{ $type->default_interval_km ?? 0 }}"
                                    data-interval-days="{{ $type->default_interval_days ?? 0 }}"
                                    data-duration="{{ $type->estimated_duration_minutes ?? 0 }}"
                                    {{ old('maintenance_type_id', $schedule->maintenance_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                        @if($lastCategory !== null)
                            </optgroup>
                        @endif
                    </select>
                    @error('maintenance_type_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Informations sur le véhicule sélectionné --}}
            <div id="vehicle-info" class="mt-6 hidden">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Informations Véhicule</h4>
                            <div class="mt-2 text-sm text-blue-700" id="vehicle-details">
                                {{-- Détails du véhicule chargés dynamiquement --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 2: Configuration des intervalles --}}
        <div class="form-section p-6" id="step-2">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Configuration des Intervalles</h3>
                    <p class="text-sm text-gray-600">Définissez les intervalles de maintenance et les alertes</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Intervalles de temps --}}
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-calendar mr-2 text-blue-500"></i>Intervalles Temporels
                    </h4>

                    <div class="space-y-2">
                        <label for="interval_days" class="block text-sm font-medium text-gray-700">
                            Intervalle en jours
                        </label>
                        <input type="number" name="interval_days" id="interval_days" min="1"
                               class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                               placeholder="Ex: 90 jours">
                        @error('interval_days')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="alert_days_before" class="block text-sm font-medium text-gray-700">
                            Alerte avant (jours)
                        </label>
                        <input type="number" name="alert_days_before" id="alert_days_before" min="1"
                               class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                               placeholder="Ex: 7 jours avant">
                        @error('alert_days_before')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Intervalles kilométriques --}}
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900 flex items-center">
                        <i class="fas fa-tachometer-alt mr-2 text-green-500"></i>Intervalles Kilométriques
                    </h4>

                    <div class="space-y-2">
                        <label for="interval_km" class="block text-sm font-medium text-gray-700">
                            Intervalle en kilomètres
                        </label>
                        <input type="number" name="interval_km" id="interval_km" min="1"
                               class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                               placeholder="Ex: 10000 km">
                        @error('interval_km')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="alert_km_before" class="block text-sm font-medium text-gray-700">
                            Alerte avant (km)
                        </label>
                        <input type="number" name="alert_km_before" id="alert_km_before" min="1"
                               class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                               placeholder="Ex: 500 km avant">
                        @error('alert_km_before')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 3: Prochaines échéances --}}
        <div class="form-section p-6" id="step-3">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Prochaines Échéances</h3>
                    <p class="text-sm text-gray-600">Définissez les prochaines dates et kilométrages de maintenance</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Prochaine échéance date --}}
                <div class="space-y-2">
                    <label for="next_due_date" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-calendar mr-2 text-blue-500"></i>Prochaine échéance (date)
                    </label>
                    <input type="date" name="next_due_date" id="next_due_date"
                           class="input-focus block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('next_due_date')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Prochaine échéance kilométrage --}}
                <div class="space-y-2">
                    <label for="next_due_mileage" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-tachometer-alt mr-2 text-green-500"></i>Prochaine échéance (km)
                    </label>
                    <input type="number" name="next_due_mileage" id="next_due_mileage" min="0"
                           class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                           placeholder="Ex: 25000 km">
                    @error('next_due_mileage')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Calculatrice automatique --}}
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-calculator mr-2 text-indigo-500"></i>Calcul Automatique des Échéances
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-gray-600">Kilométrage actuel</div>
                        <div id="current-mileage" class="font-semibold text-lg text-gray-900">-</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-gray-600">Prochaine maintenance (date)</div>
                        <div id="calculated-date" class="font-semibold text-lg text-blue-600">-</div>
                    </div>
                    <div class="bg-white p-3 rounded-lg">
                        <div class="text-gray-600">Prochaine maintenance (km)</div>
                        <div id="calculated-mileage" class="font-semibold text-lg text-green-600">-</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Étape 4: Options avancées --}}
        <div class="form-section p-6" id="step-4">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cogs text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Options Avancées</h3>
                    <p class="text-sm text-gray-600">Configuration des options et paramètres supplémentaires</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Statut et activation --}}
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900">Statut et Activation</h4>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Activer cette planification immédiatement
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="auto_create_operations" id="auto_create_operations" value="1"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="auto_create_operations" class="ml-2 block text-sm text-gray-900">
                            Créer automatiquement les opérations de maintenance
                        </label>
                    </div>
                </div>

                {{-- Notes et commentaires --}}
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Notes et Commentaires
                    </label>
                    <textarea name="notes" id="notes" rows="4"
                              class="input-focus block w-full rounded-lg border-gray-300 shadow-sm"
                              placeholder="Ajoutez des notes spécifiques pour cette planification..."></textarea>
                    @error('notes')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Résumé de la planification --}}
        <div class="form-section p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-indigo-200">
            <h3 class="text-lg font-semibold text-indigo-900 mb-4 flex items-center">
                <i class="fas fa-clipboard-check mr-2"></i>Résumé de la Planification
            </h3>
            <div id="planning-summary" class="text-sm text-indigo-800">
                <p>Veuillez remplir les informations ci-dessus pour voir le résumé.</p>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('admin.maintenance.schedules.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Annuler
            </a>

            <div class="flex items-center space-x-3">
                <button type="button" id="save-draft"
                        class="inline-flex items-center px-4 py-2 border border-indigo-300 shadow-sm text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Sauvegarder le brouillon
                </button>

                <button type="submit"
                        class="btn-primary inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white shadow-sm">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Créer la Planification
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des sélecteurs
    const vehicleSelect = new TomSelect('#vehicle_id', {
        placeholder: 'Rechercher un véhicule...',
        searchField: ['text', 'value'],
        loadThrottle: 300,
        load: function(query, callback) {
            if (!query.length) return callback();

            fetch(`/admin/vehicles/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    callback(data.map(vehicle => ({
                        value: vehicle.id,
                        text: `${vehicle.brand} ${vehicle.model} - ${vehicle.license_plate}`
                    })));
                })
                .catch(() => callback());
        }
    });

    const typeSelect = new TomSelect('#maintenance_type_id', {
        placeholder: 'Sélectionner un type...',
        searchField: ['text'],
        load: function(query, callback) {
            fetch('/admin/maintenance/types/all')
                .then(response => response.json())
                .then(data => {
                    callback(data.map(type => ({
                        value: type.id,
                        text: `${type.name} (${type.category})`
                    })));
                });
        }
    });

    // Gestion des étapes
    const steps = document.querySelectorAll('.step');
    let currentStep = 0;

    function updateStepVisual(stepIndex, status) {
        const step = steps[stepIndex];
        step.className = `step ${status}`;
    }

    // Calcul automatique des échéances
    function calculateDueDates() {
        const intervalDays = parseInt(document.getElementById('interval_days').value) || 0;
        const intervalKm = parseInt(document.getElementById('interval_km').value) || 0;
        const currentMileage = 15000; // À récupérer dynamiquement

        if (intervalDays > 0) {
            const nextDate = new Date();
            nextDate.setDate(nextDate.getDate() + intervalDays);
            document.getElementById('calculated-date').textContent = nextDate.toLocaleDateString('fr-FR');
            document.getElementById('next_due_date').value = nextDate.toISOString().split('T')[0];
        }

        if (intervalKm > 0) {
            const nextMileage = currentMileage + intervalKm;
            document.getElementById('calculated-mileage').textContent = nextMileage.toLocaleString() + ' km';
            document.getElementById('next_due_mileage').value = nextMileage;
        }

        document.getElementById('current-mileage').textContent = currentMileage.toLocaleString() + ' km';
    }

    // Écouteurs d'événements
    document.getElementById('interval_days').addEventListener('input', calculateDueDates);
    document.getElementById('interval_km').addEventListener('input', calculateDueDates);

    vehicleSelect.on('change', function(value) {
        if (value) {
            // Charger les informations du véhicule
            fetch(`/admin/vehicles/${value}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('vehicle-info').classList.remove('hidden');
                    document.getElementById('vehicle-details').innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Marque:</strong> ${data.brand}</div>
                            <div><strong>Modèle:</strong> ${data.model}</div>
                            <div><strong>Immatriculation:</strong> ${data.license_plate}</div>
                            <div><strong>Kilométrage:</strong> ${data.mileage} km</div>
                        </div>
                    `;
                    calculateDueDates();
                });
        } else {
            document.getElementById('vehicle-info').classList.add('hidden');
        }
    });

    // Mise à jour du résumé
    function updateSummary() {
        const vehicleText = vehicleSelect.getValue() ? document.querySelector(`#vehicle_id option[value="${vehicleSelect.getValue()}"]`)?.text : 'Non sélectionné';
        const typeText = typeSelect.getValue() ? document.querySelector(`#maintenance_type_id option[value="${typeSelect.getValue()}"]`)?.text : 'Non sélectionné';
        const intervalDays = document.getElementById('interval_days').value || 'Non défini';
        const intervalKm = document.getElementById('interval_km').value || 'Non défini';

        document.getElementById('planning-summary').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <strong>Véhicule:</strong> ${vehicleText}<br>
                    <strong>Type de maintenance:</strong> ${typeText}
                </div>
                <div>
                    <strong>Intervalle temporel:</strong> ${intervalDays} jours<br>
                    <strong>Intervalle kilométrique:</strong> ${intervalKm} km
                </div>
            </div>
        `;
    }

    // Écouteurs pour la mise à jour du résumé
    [vehicleSelect, typeSelect].forEach(select => {
        select.on('change', updateSummary);
    });

    ['interval_days', 'interval_km', 'next_due_date', 'next_due_mileage'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateSummary);
    });

    // Animation d'entrée des sections
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.form-section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });

    // Initialisation
    calculateDueDates();
    updateSummary();
});
</script>
@endpush