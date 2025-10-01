{{-- resources/views/admin/assignments/create.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Affectation - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Ultra-Pro Enterprise Assignment Creation Styles */

/* Global animations */
.fade-in {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.98);
        filter: blur(2px);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }
}

/* Ultra-modern section cards */
.form-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.form-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s ease;
}

.form-section:hover::before {
    left: 100%;
}

.form-section:hover {
    transform: translateY(-2px) scale(1.005);
    border-color: rgba(99, 102, 241, 0.2);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06), 0 4px 10px rgba(0,0,0,0.04);
}

.form-section h3 {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid transparent;
    background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
    background-size: 100% 3px;
    background-repeat: no-repeat;
    background-position: bottom;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 1rem;
    border: 2px solid rgba(229, 231, 235, 0.8);
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    backdrop-filter: blur(5px);
}

.form-input:focus {
    transform: scale(1.01);
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), 0 4px 12px rgba(0,0,0,0.06);
    background: white;
    outline: none;
}

.form-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.08);
    background: white;
    outline: none;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    resize: vertical;
    min-height: 100px;
}

.form-textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.08);
    background: white;
    outline: none;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
    color: white;
    padding: 1rem 2.5rem;
    border: none;
    border-radius: 16px;
    font-weight: 700;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-1px) scale(1.02);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3), 0 4px 10px rgba(99, 102, 241, 0.2);
}

.btn-secondary {
    background: white;
    color: #6b7280;
    padding: 0.75rem 2rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-secondary:hover {
    border-color: #d1d5db;
    background: #f9fafb;
    transform: translateY(-1px);
    color: #374151;
    text-decoration: none;
}

.error-message {
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

.required::after {
    content: '*';
    color: #ef4444;
    margin-left: 0.25rem;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Ultra-modern resource cards */
.resource-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid rgba(226, 232, 240, 0.6);
    border-radius: 16px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(5px);
}

.resource-card:hover {
    transform: translateY(-1px);
    border-color: rgba(99, 102, 241, 0.3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.resource-unavailable {
    opacity: 0.6;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border-color: rgba(239, 68, 68, 0.3);
}

/* Tom Select Ultra-Modern customization */
.ts-control {
    border: 2px solid rgba(229, 231, 235, 0.8) !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%) !important;
    padding: 0.75rem !important;
    font-size: 0.875rem !important;
    backdrop-filter: blur(5px) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.ts-control.focus {
    transform: scale(1.01) !important;
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), 0 4px 12px rgba(0,0,0,0.06) !important;
    background: white !important;
}

.ts-dropdown {
    border: 2px solid #e5e7eb !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

/* Ultra-Pro Assignment Type Cards */
.assignment-type-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 3px solid rgba(229, 231, 235, 0.8);
    border-radius: 20px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.assignment-type-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s ease;
}

.assignment-type-card:hover::before {
    left: 100%;
}

.assignment-type-card:hover {
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06), 0 4px 10px rgba(0,0,0,0.04);
    border-color: rgba(99, 102, 241, 0.3);
}

.assignment-type-card.selected-open {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-color: #22c55e;
    transform: translateY(-1px) scale(1.005);
    box-shadow: 0 6px 15px rgba(34, 197, 94, 0.15), 0 3px 8px rgba(34, 197, 94, 0.08);
}

.assignment-type-card.selected-scheduled {
    background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
    border-color: #a855f7;
    transform: translateY(-1px) scale(1.005);
    box-shadow: 0 6px 15px rgba(168, 85, 247, 0.15), 0 3px 8px rgba(168, 85, 247, 0.08);
}

/* Styles d'amélioration pour les sections conditionnelles */
#end-assignment-section {
    transform: translateY(10px);
    opacity: 0;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

#end-assignment-section.show {
    transform: translateY(0);
    opacity: 1;
}

/* Animation de validation temps réel */
.validation-success {
    animation: successPulse 0.6s ease-out;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(34, 197, 94, 0.3); }
    100% { transform: scale(1); }
}

/* Styles pour les indicateurs de statut en temps réel */
.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.status-indicator.success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #10b981;
}

.status-indicator.warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.status-indicator.error {
    background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
    color: #991b1b;
    border: 1px solid #ef4444;
}
</style>
@endpush

@section('content')
<div class="space-y-8 fade-in">
    {{-- Ultra-Pro Header avec Statistiques Temps Réel --}}
    <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-8 border border-blue-200 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-exchange-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Nouvelle Affectation Enterprise
                        </h1>
                        <p class="text-gray-600 mt-1">Assignation intelligente véhicule ↔ chauffeur</p>
                    </div>
                </div>

                {{-- Statistiques en temps réel --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-car text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Véhicules disponibles</p>
                                <p class="text-xl font-bold text-green-600">{{ count($availableVehicles) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-tie text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Chauffeurs libres</p>
                                <p class="text-xl font-bold text-blue-600">{{ count($availableDrivers) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Créée le</p>
                                <p class="text-sm font-semibold text-purple-600">{{ now()->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('admin.assignments.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>

    {{-- Formulaire de création --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Détails de l'Affectation</h2>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Veuillez corriger les erreurs suivantes :
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-6">
                @csrf

                {{-- Section Véhicule Ultra-Pro --}}
                <div class="form-section">
                    <h3>
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-car text-white text-sm"></i>
                        </div>
                        Sélection du Véhicule Disponible
                    </h3>

                    @if(count($availableVehicles) == 0)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-red-800">Aucun véhicule disponible</p>
                                    <p class="text-sm text-red-600">Tous les véhicules sont actuellement affectés ou en maintenance.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="select-vehicle" class="form-label required">Véhicule disponible ({{ count($availableVehicles) }} disponible{{ count($availableVehicles) > 1 ? 's' : '' }})</label>
                        <select name="vehicle_id" id="select-vehicle" class="form-select" required {{ count($availableVehicles) == 0 ? 'disabled' : '' }}>
                            <option value="">{{ count($availableVehicles) > 0 ? 'Sélectionnez un véhicule disponible' : 'Aucun véhicule disponible' }}</option>
                            @foreach($availableVehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                        data-brand="{{ $vehicle->brand }}"
                                        data-model="{{ $vehicle->model }}"
                                        data-plate="{{ $vehicle->registration_plate }}"
                                        data-mileage="{{ $vehicle->current_mileage }}"
                                        data-type="{{ $vehicle->vehicleType->name ?? 'N/A' }}"
                                        data-status="{{ $vehicle->vehicleStatus->name ?? 'N/A' }}"
                                        {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                    ({{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km) - {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center space-x-2 text-sm text-blue-700">
                                <i class="fas fa-info-circle"></i>
                                <span><strong>Filtré intelligemment :</strong> Seuls les véhicules avec statut "Disponible" et sans affectation en cours</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Chauffeur Ultra-Pro --}}
                <div class="form-section">
                    <h3>
                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-tie text-white text-sm"></i>
                        </div>
                        Sélection du Chauffeur Disponible
                    </h3>

                    @if(count($availableDrivers) == 0)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-slash text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-orange-800">Aucun chauffeur disponible</p>
                                    <p class="text-sm text-orange-600">Tous les chauffeurs sont actuellement en mission ou inactifs.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="select-driver" class="form-label required">Chauffeur disponible ({{ count($availableDrivers) }} libre{{ count($availableDrivers) > 1 ? 's' : '' }})</label>
                        <select name="driver_id" id="select-driver" class="form-select" required {{ count($availableDrivers) == 0 ? 'disabled' : '' }}>
                            <option value="">{{ count($availableDrivers) > 0 ? 'Sélectionnez un chauffeur disponible' : 'Aucun chauffeur disponible' }}</option>
                            @foreach($availableDrivers as $driver)
                                <option value="{{ $driver->id }}"
                                        data-name="{{ $driver->first_name }} {{ $driver->last_name }}"
                                        data-phone="{{ $driver->personal_phone }}"
                                        data-license="{{ $driver->driver_license_number }}"
                                        data-email="{{ $driver->email ?? '' }}"
                                        {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->first_name }} {{ $driver->last_name }}
                                    @if($driver->personal_phone) - Tél: {{ $driver->personal_phone }} @endif
                                    @if($driver->driver_license_number) - Permis: {{ $driver->driver_license_number }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center space-x-2 text-sm text-green-700">
                                <i class="fas fa-shield-alt"></i>
                                <span><strong>Vérifié automatiquement :</strong> Chauffeurs actifs sans affectation en cours, avec permis valide</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Programmation Ultra-Pro --}}
                <div class="form-section">
                    <h3>
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-sm"></i>
                        </div>
                        Programmation de l'Affectation
                    </h3>

                    {{-- Début de l'affectation (Obligatoire) --}}
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 border border-blue-200">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-play text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-blue-900">Début de l'Affectation (Obligatoire)</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            {{-- Date de début --}}
                            <div class="form-group">
                                <label for="start_date" class="form-label required flex items-center space-x-2">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                    <span>Date de début</span>
                                </label>
                                <input type="date"
                                       name="start_date"
                                       id="start_date"
                                       class="form-input"
                                       value="{{ old('start_date', now()->format('Y-m-d')) }}"
                                       min="{{ now()->format('Y-m-d') }}"
                                       required>
                                @error('start_date')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Heure de début --}}
                            <div class="form-group">
                                <label for="start_time" class="form-label required flex items-center space-x-2">
                                    <i class="fas fa-clock text-blue-600"></i>
                                    <span>Heure de début [HH:MM]</span>
                                </label>
                                <div class="relative">
                                    <input type="time"
                                           name="start_time"
                                           id="start_time"
                                           class="form-input pr-12"
                                           value="{{ old('start_time', now()->format('H:i')) }}"
                                           min="06:00"
                                           max="22:00"
                                           step="900"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-clock text-gray-400"></i>
                                    </div>
                                </div>
                                @error('start_time')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                <div class="mt-1 text-xs text-blue-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Saisissez directement ou utilisez les boutons. Plage: 06:00 - 22:00
                                </div>
                            </div>

                            {{-- Kilométrage de début --}}
                            <div class="form-group">
                                <label for="start_mileage" class="form-label required flex items-center space-x-2">
                                    <i class="fas fa-tachometer-alt text-blue-600"></i>
                                    <span>Kilométrage initial</span>
                                </label>
                                <input type="number"
                                       name="start_mileage"
                                       id="start_mileage"
                                       class="form-input"
                                       value="{{ old('start_mileage') }}"
                                       min="0"
                                       step="1"
                                       placeholder="Kilométrage actuel"
                                       required>
                                @error('start_mileage')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                <div class="mt-1 text-xs text-blue-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <span id="vehicle-mileage-hint">Sélectionnez d'abord un véhicule</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Type d'affectation --}}
                    <div class="mb-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-route text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900">Type d'Affectation</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="assignment-type-card hover:border-green-400 cursor-pointer" id="open-assignment-card">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <input type="radio" id="assignment_type_open" name="assignment_type" value="open"
                                               class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                               {{ old('assignment_type', 'open') === 'open' ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                                <i class="fas fa-infinity text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-green-800">Affectation Ouverte</h5>
                                                <p class="text-sm text-green-600">Durée indéterminée - À terminer manuellement</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                        <i class="fas fa-check mr-1"></i>Recommandé
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="assignment-type-card hover:border-purple-400 cursor-pointer" id="scheduled-assignment-card">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <input type="radio" id="assignment_type_scheduled" name="assignment_type" value="scheduled"
                                               class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                               {{ old('assignment_type') === 'scheduled' ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                                <i class="fas fa-calendar-check text-white"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-purple-800">Affectation Programmée</h5>
                                                <p class="text-sm text-purple-600">Avec date et heure de fin précises</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                                        <i class="fas fa-calendar mr-1"></i>Planifiée
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fin de l'affectation (Conditionnelle) --}}
                    <div id="end-assignment-section" class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200" style="display: none;">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-700 rounded-xl flex items-center justify-center">
                                <i class="fas fa-stop text-white"></i>
                            </div>
                            <h4 class="text-lg font-bold text-purple-900">Fin de l'Affectation (Optionnel)</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            {{-- Date de fin --}}
                            <div class="form-group">
                                <label for="end_date" class="form-label flex items-center space-x-2">
                                    <i class="fas fa-calendar text-purple-600"></i>
                                    <span>Date de fin</span>
                                </label>
                                <input type="date"
                                       name="end_date"
                                       id="end_date"
                                       class="form-input"
                                       value="{{ old('end_date') }}"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('end_date')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Heure de fin --}}
                            <div class="form-group">
                                <label for="end_time" class="form-label flex items-center space-x-2">
                                    <i class="fas fa-clock text-purple-600"></i>
                                    <span>Heure de fin [HH:MM]</span>
                                </label>
                                <div class="relative">
                                    <input type="time"
                                           name="end_time"
                                           id="end_time"
                                           class="form-input pr-12"
                                           value="{{ old('end_time') }}"
                                           min="06:00"
                                           max="22:00"
                                           step="900">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-clock text-gray-400"></i>
                                    </div>
                                </div>
                                @error('end_time')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                <div class="mt-1 text-xs text-purple-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Saisissez directement ou utilisez les boutons
                                </div>
                            </div>

                            {{-- Kilométrage de fin estimé --}}
                            <div class="form-group">
                                <label for="estimated_end_mileage" class="form-label flex items-center space-x-2">
                                    <i class="fas fa-route text-purple-600"></i>
                                    <span>Kilométrage estimé</span>
                                </label>
                                <input type="number"
                                       name="estimated_end_mileage"
                                       id="estimated_end_mileage"
                                       class="form-input"
                                       value="{{ old('estimated_end_mileage') }}"
                                       min="0"
                                       step="1"
                                       placeholder="Kilométrage final prévu">
                                @error('estimated_end_mileage')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                                <div class="mt-1 text-xs text-purple-600">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Estimation pour le planning
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-4 bg-white/50 rounded-lg border border-purple-200">
                            <div class="flex items-center space-x-2 text-sm text-purple-700">
                                <i class="fas fa-lightbulb text-purple-500"></i>
                                <span><strong>Conseil :</strong> Une affectation programmée permet un meilleur planning des ressources</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Informations complémentaires --}}
                <div class="form-section">
                    <h3><i class="fas fa-sticky-note mr-2 text-amber-600"></i>Informations Complémentaires</h3>
                    <div class="form-group">
                        <label for="purpose" class="form-label">Motif de l'affectation</label>
                        <select name="purpose" id="purpose" class="form-select">
                            <option value="">Sélectionnez un motif (optionnel)</option>
                            <option value="mission" {{ old('purpose') == 'mission' ? 'selected' : '' }}>Mission professionnelle</option>
                            <option value="formation" {{ old('purpose') == 'formation' ? 'selected' : '' }}>Formation</option>
                            <option value="maintenance" {{ old('purpose') == 'maintenance' ? 'selected' : '' }}>Maintenance/Contrôle</option>
                            <option value="deplacement" {{ old('purpose') == 'deplacement' ? 'selected' : '' }}>Déplacement administratif</option>
                            <option value="urgence" {{ old('purpose') == 'urgence' ? 'selected' : '' }}>Urgence</option>
                            <option value="autre" {{ old('purpose') == 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('purpose')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">Notes et observations</label>
                        <textarea name="notes"
                                  id="notes"
                                  class="form-textarea"
                                  placeholder="Informations complémentaires, itinéraire prévu, contacts, etc.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions Ultra-Pro --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 mt-8">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-600">Formulaire prêt à soumettre</span>
                        </div>

                        <div class="flex space-x-4">
                            <a href="{{ route('admin.assignments.index') }}"
                               class="btn-secondary group">
                                <i class="fas fa-times mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                Annuler
                            </a>

                            <button type="button"
                                    onclick="resetFormWithAnimation()"
                                    class="btn-secondary group">
                                <i class="fas fa-undo mr-2 group-hover:rotate-180 transition-transform duration-500"></i>
                                Réinitialiser
                            </button>

                            <button type="submit"
                                    class="btn-primary group"
                                    id="submit-btn">
                                <i class="fas fa-rocket mr-2 group-hover:translate-x-1 transition-transform duration-300"></i>
                                <span id="submit-text">Créer l'Affectation</span>
                                <div id="submit-loading" class="hidden">
                                    <i class="fas fa-spinner animate-spin mr-2"></i>
                                    Création en cours...
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Indicateur de validation en temps réel --}}
                    <div id="form-validation-indicator" class="mt-4 hidden">
                        <div class="flex items-center justify-center space-x-2 text-sm">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-green-700 font-medium">Tous les champs requis sont remplis</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation Ultra-Pro du formulaire d\'affectation enterprise');

    // Animation séquentielle des sections au chargement
    const sections = document.querySelectorAll('.form-section');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        setTimeout(() => {
            section.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Configuration Tom Select pour les véhicules
    const vehicleSelect = new TomSelect('#select-vehicle', {
        create: false,
        sortField: 'text',
        placeholder: 'Recherchez par immatriculation, marque ou modèle...',
        searchField: ['text', 'data-plate', 'data-brand', 'data-model'],
        render: {
            option: function(data, escape) {
                const plate = data.dataset?.plate || '';
                const brand = data.dataset?.brand || '';
                const model = data.dataset?.model || '';
                const mileage = data.dataset?.mileage || '0';
                const type = data.dataset?.type || 'N/A';
                const status = data.dataset?.status || 'N/A';

                return `<div class="py-3 px-4 hover:bg-blue-50 transition-colors duration-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <i class="fas fa-car text-white"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-sm text-gray-900">${escape(plate)}</div>
                            <div class="font-medium text-sm text-gray-700">${escape(brand)} ${escape(model)}</div>
                            <div class="flex items-center space-x-3 text-xs text-gray-500 mt-1">
                                <span><i class="fas fa-tachometer-alt mr-1"></i>${parseInt(mileage).toLocaleString('fr-FR')} km</span>
                                <span><i class="fas fa-tag mr-1"></i>${escape(type)}</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full"><i class="fas fa-check-circle mr-1"></i>${escape(status)}</span>
                            </div>
                        </div>
                    </div>
                </div>`;
            },
            item: function(data, escape) {
                const plate = data.dataset?.plate || '';
                const brand = data.dataset?.brand || '';
                const model = data.dataset?.model || '';
                return `<div>${escape(plate)} - ${escape(brand)} ${escape(model)}</div>`;
            }
        },
        onChange: function(value) {
            // Mise à jour du kilométrage suggéré
            const option = this.options[value];
            if (option && option.dataset?.mileage) {
                const mileage = option.dataset.mileage;
                document.getElementById('start_mileage').value = mileage;
                document.getElementById('vehicle-mileage-hint').textContent =
                    `Kilométrage actuel: ${parseInt(mileage).toLocaleString('fr-FR')} km`;
            }
        }
    });

    // Configuration Tom Select pour les chauffeurs
    const driverSelect = new TomSelect('#select-driver', {
        create: false,
        sortField: 'text',
        placeholder: 'Recherchez par nom, téléphone ou n° de permis...',
        searchField: ['text', 'data-name', 'data-phone', 'data-license'],
        render: {
            option: function(data, escape) {
                const name = data.dataset?.name || '';
                const phone = data.dataset?.phone || '';
                const license = data.dataset?.license || '';

                return `<div class="py-2 px-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user-tie text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-sm">${escape(name)}</div>
                            <div class="text-xs text-gray-500">
                                ${phone ? `<i class="fas fa-phone mr-1"></i>${escape(phone)}` : ''}
                                ${license ? `<i class="fas fa-id-card ml-2 mr-1"></i>Permis: ${escape(license)}` : ''}
                            </div>
                        </div>
                    </div>
                </div>`;
            },
            item: function(data, escape) {
                const name = data.dataset?.name || '';
                return `<div>${escape(name)}</div>`;
            }
        }
    });

    // Validation en temps réel
    const form = document.querySelector('form');
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });

        field.addEventListener('input', function() {
            clearFieldError(this);
        });
    });

    // Validation du kilométrage
    const startMileageInput = document.getElementById('start_mileage');
    startMileageInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        if (value < 0) {
            showFieldError(this, 'Le kilométrage ne peut pas être négatif');
        } else {
            clearFieldError(this);
        }
    });

    // Gestion des types d'affectation
    const openAssignmentCard = document.getElementById('open-assignment-card');
    const scheduledAssignmentCard = document.getElementById('scheduled-assignment-card');
    const endAssignmentSection = document.getElementById('end-assignment-section');
    const openRadio = document.getElementById('assignment_type_open');
    const scheduledRadio = document.getElementById('assignment_type_scheduled');

    // Gestion des cartes cliquables
    if (openAssignmentCard) {
        openAssignmentCard.addEventListener('click', function() {
            openRadio.checked = true;
            updateAssignmentTypeDisplay();
        });
    }

    if (scheduledAssignmentCard) {
        scheduledAssignmentCard.addEventListener('click', function() {
            scheduledRadio.checked = true;
            updateAssignmentTypeDisplay();
        });
    }

    // Gestion du changement de type d'affectation
    if (openRadio) openRadio.addEventListener('change', updateAssignmentTypeDisplay);
    if (scheduledRadio) scheduledRadio.addEventListener('change', updateAssignmentTypeDisplay);

    function updateAssignmentTypeDisplay() {
        const isScheduled = scheduledRadio && scheduledRadio.checked;

        if (endAssignmentSection) {
            if (isScheduled) {
                // Affichage de la section avec animation fluide
                endAssignmentSection.style.display = 'block';
                endAssignmentSection.classList.add('show');

                // Mise à jour visuelle des cartes avec les nouvelles classes
                if (scheduledAssignmentCard) {
                    scheduledAssignmentCard.classList.add('selected-scheduled');
                    scheduledAssignmentCard.classList.remove('selected-open');
                }
                if (openAssignmentCard) {
                    openAssignmentCard.classList.remove('selected-open');
                    openAssignmentCard.classList.remove('selected-scheduled');
                }

                // Rendre les champs de fin requis
                const endDate = document.getElementById('end_date');
                const endTime = document.getElementById('end_time');
                if (endDate && endTime) {
                    endDate.setAttribute('required', 'required');
                    endTime.setAttribute('required', 'required');
                }
            } else {
                // Masquage avec animation
                endAssignmentSection.classList.remove('show');
                setTimeout(() => {
                    endAssignmentSection.style.display = 'none';
                }, 300);

                // Mise à jour visuelle des cartes
                if (openAssignmentCard) {
                    openAssignmentCard.classList.add('selected-open');
                    openAssignmentCard.classList.remove('selected-scheduled');
                }
                if (scheduledAssignmentCard) {
                    scheduledAssignmentCard.classList.remove('selected-scheduled');
                    scheduledAssignmentCard.classList.remove('selected-open');
                }

                // Réinitialiser les champs de fin et retirer l'obligation
                const endDate = document.getElementById('end_date');
                const endTime = document.getElementById('end_time');
                const endMileage = document.getElementById('estimated_end_mileage');
                if (endDate) {
                    endDate.value = '';
                    endDate.removeAttribute('required');
                }
                if (endTime) {
                    endTime.value = '';
                    endTime.removeAttribute('required');
                }
                if (endMileage) endMileage.value = '';
            }
        }

        // Animation de validation pour les cartes sélectionnées
        setTimeout(() => {
            const selectedCard = isScheduled ? scheduledAssignmentCard : openAssignmentCard;
            if (selectedCard) {
                selectedCard.classList.add('validation-success');
                setTimeout(() => selectedCard.classList.remove('validation-success'), 600);
            }
        }, 100);
    }

    // Initialiser l'affichage
    updateAssignmentTypeDisplay();

    // Validation des dates et heures avec nouveaux champs time
    const startDateInput = document.getElementById('start_date');
    const startTimeInput = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeInput = document.getElementById('end_time');

    function validateDateTime() {
        const startDate = startDateInput ? startDateInput.value : '';
        const startTime = startTimeInput ? startTimeInput.value : '';
        const endDate = endDateInput ? endDateInput.value : '';
        const endTime = endTimeInput ? endTimeInput.value : '';

        if (!startDate || !startTime) return true;

        const startDateTime = new Date(`${startDate}T${startTime}`);
        const now = new Date();
        now.setMinutes(now.getMinutes() - 5); // Tolérance de 5 minutes

        // Validation date/heure de début
        if (startDateTime < now) {
            showFieldError(startDateInput, 'La date de début ne peut pas être antérieure à maintenant');
            return false;
        } else {
            clearFieldError(startDateInput);
            clearFieldError(startTimeInput);
        }

        // Validation des heures dans la plage autorisée (6h-22h)
        const startHour = parseInt(startTime.split(':')[0]);
        if (startHour < 6 || startHour > 22) {
            showFieldError(startTimeInput, 'L\'heure doit être comprise entre 06:00 et 22:00');
            return false;
        } else {
            clearFieldError(startTimeInput);
        }

        // Validation date/heure de fin si programmée
        if (scheduledRadio && scheduledRadio.checked && endDate && endTime) {
            const endDateTime = new Date(`${endDate}T${endTime}`);
            const endHour = parseInt(endTime.split(':')[0]);

            // Validation plage horaire pour heure de fin
            if (endHour < 6 || endHour > 22) {
                showFieldError(endTimeInput, 'L\'heure doit être comprise entre 06:00 et 22:00');
                return false;
            }

            // Validation logique temporelle
            if (endDateTime <= startDateTime) {
                showFieldError(endDateInput, 'La date de fin doit être postérieure au début');
                return false;
            } else {
                clearFieldError(endDateInput);
                clearFieldError(endTimeInput);
            }
        }

        return true;
    }

    // Validation en temps réel pour les nouveaux champs
    if (startDateInput) startDateInput.addEventListener('change', validateDateTime);
    if (startTimeInput) {
        startTimeInput.addEventListener('change', validateDateTime);
        startTimeInput.addEventListener('blur', validateDateTime);
    }
    if (endDateInput) endDateInput.addEventListener('change', validateDateTime);
    if (endTimeInput) {
        endTimeInput.addEventListener('change', validateDateTime);
        endTimeInput.addEventListener('blur', validateDateTime);
    }

    // Fonctions utilitaires de validation
    function validateField(field) {
        if (field.hasAttribute('required') && !field.value.trim()) {
            showFieldError(field, 'Ce champ est obligatoire');
            return false;
        }
        clearFieldError(field);
        return true;
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        const errorElement = document.createElement('p');
        errorElement.className = 'error-message';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);
        field.style.borderColor = '#ef4444';
    }

    function clearFieldError(field) {
        const errorElement = field.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
        field.style.borderColor = '';
    }

    // Validation en temps réel et indicateur de statut
    function updateFormValidation() {
        const vehicleSelected = document.getElementById('select-vehicle') ? document.getElementById('select-vehicle').value : '';
        const driverSelected = document.getElementById('select-driver') ? document.getElementById('select-driver').value : '';
        const startDate = document.getElementById('start_date') ? document.getElementById('start_date').value : '';
        const startTime = document.getElementById('start_time') ? document.getElementById('start_time').value : '';
        const startMileage = document.getElementById('start_mileage') ? document.getElementById('start_mileage').value : '';

        const indicator = document.getElementById('form-validation-indicator');

        if (vehicleSelected && driverSelected && startDate && startTime && startMileage) {
            if (indicator) {
                indicator.classList.remove('hidden');
                const pulseElement = indicator.querySelector('.bg-green-500');
                if (pulseElement) {
                    pulseElement.classList.add('animate-pulse');
                }
            }
        } else {
            if (indicator) {
                indicator.classList.add('hidden');
            }
        }
    }

    // Surveiller les changements pour la validation temps réel
    ['select-vehicle', 'select-driver', 'start_date', 'start_time', 'start_mileage'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updateFormValidation);
            element.addEventListener('input', updateFormValidation);
        }
    });

    // Fonction de réinitialisation avec animation
    window.resetFormWithAnimation = function() {
        const form = document.querySelector('form');
        const sections = document.querySelectorAll('.form-section');

        // Animation de sortie
        sections.forEach((section, index) => {
            setTimeout(() => {
                section.style.transform = 'translateX(-100%)';
                section.style.opacity = '0.5';
            }, index * 100);
        });

        // Réinitialisation après animation
        setTimeout(() => {
            form.reset();
            if (vehicleSelect) vehicleSelect.clear();
            if (driverSelect) driverSelect.clear();

            // Animation de retour
            sections.forEach((section, index) => {
                setTimeout(() => {
                    section.style.transform = 'translateX(0)';
                    section.style.opacity = '1';
                }, index * 100);
            });

            updateFormValidation();
        }, sections.length * 100 + 200);
    };

    // Validation avant soumission avec animation
    form.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        const submitLoading = document.getElementById('submit-loading');

        let isValid = true;
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();

            // Animation d'erreur
            submitBtn.style.transform = 'shake';
            submitBtn.classList.add('animate-pulse');
            setTimeout(() => {
                submitBtn.classList.remove('animate-pulse');
            }, 1000);

            const firstError = form.querySelector('.error-message');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Animation de soumission
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.style.transform = 'scale(0.98)';

            // Progress simulation (pour l'UX)
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 90) {
                    clearInterval(progressInterval);
                }
            }, 200);
        }
    });

    // Validation temps réel des ressources
    const availableVehicles = {{ count($availableVehicles) }};
    const availableDrivers = {{ count($availableDrivers) }};

    if (availableVehicles === 0 || availableDrivers === 0) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-ban mr-2"></i>Ressources indisponibles';
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    console.log('✅ Formulaire d\'affectation Ultra-Pro initialisé avec succès');
    console.log(`📊 Ressources disponibles: ${availableVehicles} véhicules, ${availableDrivers} chauffeurs`);
});
</script>
@endpush