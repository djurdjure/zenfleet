{{-- resources/views/admin/maintenance/operations/create.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Opération de Maintenance - ZenFleet')

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
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    background: white;
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
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    color: white;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
    background: white;
    border: 2px solid #e2e8f0;
    color: #6b7280;
    transition: all 0.2s ease;
}

.btn-secondary:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
}

.tom-select .ts-control {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.tom-select.focus .ts-control {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.cost-calculator {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 2px solid #3b82f6;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.cost-calculator:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- Messages de notification --}}
    @if(session('success'))
        <div id="success-alert" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm fade-in mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="document.getElementById('success-alert').remove()"
                            class="text-green-400 hover:text-green-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="error-alert" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm fade-in mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                </div>
                <div class="ml-3">
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="document.getElementById('error-alert').remove()"
                            class="text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="mb-8">
        <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
            <a href="{{ route('admin.maintenance.dashboard') }}" class="hover:text-blue-600 transition-colors">
                <i class="fas fa-wrench mr-1"></i> Maintenance
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="{{ route('admin.maintenance.operations.index') }}" class="hover:text-blue-600 transition-colors">
                Opérations
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-blue-600 font-semibold">Nouvelle opération</span>
        </nav>

        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-6 text-gray-900">Nouvelle Opération de Maintenance</h1>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                        </svg>
                        Système de gestion ultra-professionnel
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('admin.maintenance.operations.index') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
            </div>
        </div>
    </div>

    {{-- Résumé des erreurs --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Formulaire --}}
    <form action="{{ route('admin.maintenance.operations.store') }}" method="POST" id="operation-form" class="space-y-8">
        @csrf

        {{-- Section Informations principales --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-blue-500 text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Informations principales</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-car text-blue-500 mr-2"></i>Véhicule <span class="text-red-500">*</span>
                    </label>
                    <select id="vehicle_id" name="vehicle_id" required class="form-input w-full px-3 py-2 rounded-md">
                        <option value="">Sélectionner un véhicule</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cogs text-purple-500 mr-2"></i>Type de maintenance <span class="text-red-500">*</span>
                    </label>
                    <select id="maintenance_type_id" name="maintenance_type_id" required class="form-input w-full px-3 py-2 rounded-md">
                        <option value="">Sélectionner un type</option>
                        @php
                            $lastCategory = null;
                        @endphp
                        @foreach($maintenanceTypes as $type)
                            @if($lastCategory !== $type->category)
                                @if($lastCategory !== null)
                                    </optgroup>
                                @endif
                                <optgroup label="{{ ucfirst($type->category) }}">
                                @php $lastCategory = $type->category; @endphp
                            @endif
                            <option value="{{ $type->id }}" {{ old('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                                @if($type->estimated_cost)
                                    (≈ {{ number_format($type->estimated_cost, 0, ',', ' ') }} DZD)
                                @endif
                            </option>
                        @endforeach
                        @if($lastCategory !== null)
                            </optgroup>
                        @endif
                    </select>
                    @error('maintenance_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="maintenance_provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-green-500 mr-2"></i>Fournisseur
                    </label>
                    <select id="maintenance_provider_id" name="maintenance_provider_id" class="form-input w-full px-3 py-2 rounded-md">
                        <option value="">Sélectionner un fournisseur</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('maintenance_provider_id') == $provider->id ? 'selected' : '' }}>
                                {{ $provider->name }}
                                @if($provider->phone)
                                    - {{ $provider->phone }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('maintenance_provider_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Planification --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-indigo-500 text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Planification et statut</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle text-gray-500 mr-2"></i>Statut <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required class="form-input w-full px-3 py-2 rounded-md">
                        <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planifiée</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i>Priorité <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required class="form-input w-full px-3 py-2 rounded-md">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Faible</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critique</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar text-blue-500 mr-2"></i>Date prévue <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" required class="form-input w-full px-3 py-2 rounded-md">
                    @error('scheduled_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clock text-indigo-500 mr-2"></i>Durée estimée (min)
                    </label>
                    <input type="number" id="estimated_duration_minutes" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes') }}" min="1" class="form-input w-full px-3 py-2 rounded-md" placeholder="Ex: 120">
                    @error('estimated_duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section Détails techniques --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-purple-500 text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Détails techniques</h3>
            </div>

            <div class="space-y-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-alt text-gray-500 mr-2"></i>Description détaillée <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required class="form-input w-full px-3 py-2 rounded-md" placeholder="Décrivez en détail l'opération de maintenance à effectuer...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="parts_needed" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-cog text-gray-500 mr-2"></i>Pièces nécessaires
                        </label>
                        <textarea id="parts_needed" name="parts_needed" rows="3" class="form-input w-full px-3 py-2 rounded-md" placeholder="Liste des pièces nécessaires...">{{ old('parts_needed') }}</textarea>
                        @error('parts_needed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="work_performed" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-wrench text-gray-500 mr-2"></i>Travaux effectués
                        </label>
                        <textarea id="work_performed" name="work_performed" rows="3" class="form-input w-full px-3 py-2 rounded-md" placeholder="Détails des travaux réalisés...">{{ old('work_performed') }}</textarea>
                        @error('work_performed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Coûts --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-green-500 text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Coûts et facturation</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="labor_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-cog text-blue-500 mr-2"></i>Coût main-d'œuvre (DZD)
                    </label>
                    <input type="number" id="labor_cost" name="labor_cost" value="{{ old('labor_cost') }}" step="0.01" min="0" class="form-input w-full px-3 py-2 rounded-md" placeholder="0.00">
                    @error('labor_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parts_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cogs text-purple-500 mr-2"></i>Coût pièces (DZD)
                    </label>
                    <input type="number" id="parts_cost" name="parts_cost" value="{{ old('parts_cost') }}" step="0.01" min="0" class="form-input w-full px-3 py-2 rounded-md" placeholder="0.00">
                    @error('parts_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="external_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-orange-500 mr-2"></i>Coût externe (DZD)
                    </label>
                    <input type="number" id="external_cost" name="external_cost" value="{{ old('external_cost') }}" step="0.01" min="0" class="form-input w-full px-3 py-2 rounded-md" placeholder="0.00">
                    @error('external_cost')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cost-calculator p-4">
                    <label class="block text-sm font-medium text-blue-700 mb-2">
                        <i class="fas fa-calculator text-blue-600 mr-2"></i>Coût total calculé
                    </label>
                    <div id="total_cost_display" class="text-2xl font-bold text-blue-800">0.00 DZD</div>
                    <input type="hidden" id="total_cost" name="total_cost" value="{{ old('total_cost', 0) }}">
                </div>
            </div>
        </div>

        {{-- Section Notes et observations --}}
        <div class="form-section rounded-lg p-6">
            <div class="section-header">
                <div class="section-icon bg-yellow-500 text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Notes et observations</h3>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note text-gray-500 mr-2"></i>Notes additionnelles
                </label>
                <textarea id="notes" name="notes" rows="3" class="form-input w-full px-3 py-2 rounded-md" placeholder="Notes, observations, recommandations...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.maintenance.operations.index') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="btn-primary inline-flex items-center px-6 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="submit-btn">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span id="submit-text">Créer l'opération</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TomSelect for all select elements
    const selectElements = ['vehicle_id', 'maintenance_type_id', 'maintenance_provider_id', 'status', 'priority'];

    selectElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            new TomSelect(element, {
                create: false,
                placeholder: 'Sélectionner...',
                searchField: ['text'],
                allowEmptyOption: true,
                render: {
                    no_results: function() {
                        return '<div class="no-results p-3 text-center text-gray-500">Aucun résultat trouvé</div>';
                    }
                }
            });
        }
    });

    // Form submission avec état de loading
    const form = document.getElementById('operation-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');

    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitText.textContent = 'Création en cours...';
    });

    // Calculateur de coût automatique
    const laborCost = document.getElementById('labor_cost');
    const partsCost = document.getElementById('parts_cost');
    const externalCost = document.getElementById('external_cost');
    const totalCostDisplay = document.getElementById('total_cost_display');
    const totalCostInput = document.getElementById('total_cost');

    function calculateTotalCost() {
        const labor = parseFloat(laborCost.value) || 0;
        const parts = parseFloat(partsCost.value) || 0;
        const external = parseFloat(externalCost.value) || 0;
        const total = labor + parts + external;

        totalCostDisplay.textContent = total.toFixed(2) + ' DZD';
        totalCostInput.value = total.toFixed(2);
    }

    // Écouter les changements sur les champs de coût
    [laborCost, partsCost, externalCost].forEach(input => {
        if (input) {
            input.addEventListener('input', calculateTotalCost);
        }
    });

    // Validation des dates
    const scheduledDate = document.getElementById('scheduled_date');
    if (scheduledDate) {
        // Définir la date minimale à aujourd'hui
        const now = new Date();
        const offset = now.getTimezoneOffset();
        const localISOTime = new Date(now.getTime() - (offset * 60 * 1000)).toISOString().slice(0, 16);
        scheduledDate.min = localISOTime;

        scheduledDate.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const currentDate = new Date();

            if (selectedDate < currentDate) {
                this.setCustomValidity('La date ne peut pas être dans le passé');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Calcul initial
    calculateTotalCost();

    // Auto-hide success messages after 5 seconds
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = 'opacity 0.5s ease-out';
            successAlert.style.opacity = '0';
            setTimeout(function() {
                successAlert.remove();
            }, 500);
        }, 5000);
    }
});
</script>
@endpush