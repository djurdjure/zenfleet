{{-- resources/views/admin/assignments/create-enterprise.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Affectation Enterprise - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* 🎨 ULTRA-PRO ENTERPRISE ASSIGNMENT INTERFACE */

/* Animations globales avancées */
@keyframes slideInUp {
 from {
 opacity: 0;
 transform: translateY(30px) scale(0.98);
 filter: blur(3px);
 }
 to {
 opacity: 1;
 transform: translateY(0) scale(1);
 filter: blur(0);
 }
}

@keyframes pulseSuccess {
 0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
 50% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
}

.animate-slide-up {
 animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Section cards ultra-moderne */
.enterprise-card {
 background: linear-gradient(145deg, #ffffff 0%, #fafbfc 100%);
 border: 2px solid rgba(226, 232, 240, 0.6);
 border-radius: 24px;
 padding: 2rem;
 margin-bottom: 2rem;
 transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
 position: relative;
 overflow: hidden;
}

.enterprise-card::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
 transition: left 0.6s ease;
}

.enterprise-card:hover::before {
 left: 100%;
}

.enterprise-card:hover {
 transform: translateY(-4px);
 border-color: rgba(99, 102, 241, 0.3);
 box-shadow: 0 12px 40px rgba(0,0,0,0.08), 0 6px 20px rgba(0,0,0,0.06);
}

.enterprise-card-header {
 display: flex;
 align-items: center;
 gap: 1rem;
 margin-bottom: 2rem;
 padding-bottom: 1rem;
 border-bottom: 3px solid transparent;
 background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, transparent 100%);
 background-size: 100% 3px;
 background-repeat: no-repeat;
 background-position: bottom;
}

.enterprise-card-icon {
 width: 48px;
 height: 48px;
 background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
 border-radius: 16px;
 display: flex;
 align-items: center;
 justify-center;
 box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.enterprise-input {
 width: 100%;
 padding: 1rem 1.25rem;
 border: 2px solid rgba(229, 231, 235, 0.8);
 border-radius: 16px;
 font-size: 0.875rem;
 transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
 background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
 backdrop-filter: blur(5px);
}

.enterprise-input:focus {
 transform: scale(1.01);
 border-color: #6366f1;
 box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 4px 20px rgba(0,0,0,0.08);
 background: white;
 outline: none;
}

.enterprise-input:hover:not(:focus) {
 border-color: rgba(156, 163, 175, 0.6);
}

/* Tom Select Enterprise Customization */
.ts-control {
 border: 2px solid rgba(229, 231, 235, 0.8) !important;
 border-radius: 16px !important;
 background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%) !important;
 padding: 1rem 1.25rem !important;
 font-size: 0.875rem !important;
 transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
 min-height: 56px !important;
}

.ts-control.focus {
 transform: scale(1.01) !important;
 border-color: #6366f1 !important;
 box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1), 0 4px 20px rgba(0,0,0,0.08) !important;
 background: white !important;
}

.ts-dropdown {
 border: 2px solid rgba(229, 231, 235, 0.8) !important;
 border-radius: 16px !important;
 box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
 backdrop-filter: blur(20px) !important;
}

.ts-dropdown .option {
 padding: 1rem !important;
 border-bottom: 1px solid rgba(229, 231, 235, 0.5) !important;
 transition: all 0.2s ease !important;
}

.ts-dropdown .option:hover {
 background: rgba(99, 102, 241, 0.08) !important;
 transform: translateX(4px) !important;
}

.ts-dropdown .option:last-child {
 border-bottom: none !important;
}

/* Assignment Type Cards */
.assignment-type-card {
 background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
 border: 3px solid rgba(229, 231, 235, 0.8);
 border-radius: 20px;
 padding: 1.75rem;
 cursor: pointer;
 transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
 position: relative;
 overflow: hidden;
}

.assignment-type-card::after {
 content: '';
 position: absolute;
 top: 50%;
 left: 50%;
 width: 0;
 height: 0;
 border-radius: 50%;
 background: rgba(99, 102, 241, 0.1);
 transform: translate(-50%, -50%);
 transition: width 0.5s, height 0.5s;
}

.assignment-type-card:hover::after {
 width: 300px;
 height: 300px;
}

.assignment-type-card:hover {
 transform: translateY(-6px) scale(1.02);
 box-shadow: 0 12px 40px rgba(0,0,0,0.1);
}

.assignment-type-card.selected-open {
 background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
 border-color: #22c55e;
 box-shadow: 0 8px 30px rgba(34, 197, 94, 0.2), 0 4px 15px rgba(34, 197, 94, 0.1);
}

.assignment-type-card.selected-scheduled {
 background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
 border-color: #a855f7;
 box-shadow: 0 8px 30px rgba(168, 85, 247, 0.2), 0 4px 15px rgba(168, 85, 247, 0.1);
}

/* Buttons Enterprise */
.btn-enterprise-primary {
 background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
 color: white;
 padding: 1.25rem 3rem;
 border: none;
 border-radius: 18px;
 font-weight: 700;
 font-size: 0.875rem;
 cursor: pointer;
 transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
 box-shadow: 0 10px 35px rgba(59, 130, 246, 0.4);
 position: relative;
 overflow: hidden;
}

.btn-enterprise-primary::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
 transition: left 0.6s ease;
}

.btn-enterprise-primary:hover::before {
 left: 100%;
}

.btn-enterprise-primary:hover {
 transform: translateY(-3px) scale(1.03);
 box-shadow: 0 12px 45px rgba(59, 130, 246, 0.5), 0 6px 25px rgba(99, 102, 241, 0.3);
}

.btn-enterprise-secondary {
 background: white;
 color: #64748b;
 padding: 1rem 2.5rem;
 border: 2px solid rgba(226, 232, 240, 0.8);
 border-radius: 16px;
 font-weight: 600;
 font-size: 0.875rem;
 cursor: pointer;
 transition: all 0.3s ease;
}

.btn-enterprise-secondary:hover {
 border-color: #cbd5e1;
 background: #f8fafc;
 transform: translateY(-2px);
}

/* Alert Enterprise */
.alert-enterprise {
 background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
 border: 2px solid #ef4444;
 border-radius: 16px;
 padding: 1.5rem;
 display: flex;
 align-items: center;
 gap: 1rem;
 animation: slideInUp 0.5s ease;
}

.alert-enterprise-icon {
 width: 48px;
 height: 48px;
 background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
 border-radius: 12px;
 display: flex;
 align-items: center;
 justify-content: center;
 box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
 flex-shrink: 0;
}

/* Stats Dashboard */
.stats-card {
 background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
 backdrop-filter: blur(10px);
 border: 2px solid rgba(255, 255, 255, 0.5);
 border-radius: 18px;
 padding: 1.5rem;
 transition: all 0.3s ease;
}

.stats-card:hover {
 transform: translateY(-4px);
 box-shadow: 0 8px 30px rgba(0,0,0,0.1);
 border-color: rgba(99, 102, 241, 0.3);
}

/* Conditional Section Animation */
#end-assignment-section {
 transform: translateY(20px);
 opacity: 0;
 max-height: 0;
 overflow: hidden;
 transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

#end-assignment-section.show {
 transform: translateY(0);
 opacity: 1;
 max-height: 1000px;
}

/* Responsive Design */
@media (max-width: 768px) {
 .enterprise-card {
 padding: 1.5rem;
 border-radius: 18px;
 }

 .enterprise-card-icon {
 width: 40px;
 height: 40px;
 }

 .btn-enterprise-primary {
 padding: 1rem 2rem;
 font-size: 0.8125rem;
 }
}

/* Loading State */
.loading-spinner {
 display: inline-block;
 width: 20px;
 height: 20px;
 border: 3px solid rgba(255,255,255,0.3);
 border-radius: 50%;
 border-top-color: white;
 animation: spin 0.8s linear infinite;
}

@keyframes spin {
 to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="space-y-8 animate-slide-up">
 {{-- HERO HEADER ULTRA-PRO --}}
 <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-3xl p-8 border-2 border-blue-200 shadow-2xl">
 <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
 <div class="flex-1">
 <div class="flex items-center space-x-4 mb-6">
 <div class="w-20 h-20 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center shadow-2xl transform hover:rotate-6 transition-transform duration-300">
 <i class="fas fa-exchange-alt text-white text-3xl"></i>
 </div>
 <div>
 <h1 class="text-3xl font-black text-gray-900 tracking-tight">
 Nouvelle Affectation Enterprise
 </h1>
 <p class="text-gray-600 mt-2 font-medium">
 Système intelligent d'assignation véhicule ↔ chauffeur
 </p>
 </div>
 </div>

 {{-- STATS REAL-TIME --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div class="stats-card group">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
 <i class="fas fa-car text-white text-lg"></i>
 </div>
 <div>
 <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Véhicules</p>
 <p class="text-2xl font-black text-green-600">{{ count($availableVehicles) }}</p>
 </div>
 </div>
 </div>

 <div class="stats-card group">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
 <i class="fas fa-user-tie text-white text-lg"></i>
 </div>
 <div>
 <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Chauffeurs</p>
 <p class="text-2xl font-black text-blue-600">{{ count($availableDrivers) }}</p>
 </div>
 </div>
 </div>

 <div class="stats-card group">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
 <i class="fas fa-clock text-white text-lg"></i>
 </div>
 <div>
 <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Créée le</p>
 <p class="text-sm font-bold text-purple-600">{{ now()->format('d/m/Y H:i') }}</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div>
 <a href="{{ route('admin.assignments.index') }}" 
 class="btn-enterprise-secondary inline-flex items-center">
 <i class="fas fa-arrow-left mr-2"></i>
 Retour
 </a>
 </div>
 </div>
 </div>

 {{-- ERREURS --}}
 @if ($errors->any())
 <div class="alert-enterprise">
 <div class="alert-enterprise-icon">
 <i class="fas fa-exclamation-triangle text-white text-xl"></i>
 </div>
 <div class="flex-1">
 <h3 class="text-lg font-bold text-red-900 mb-2">Erreurs de validation</h3>
 <ul class="space-y-1 text-sm text-red-700">
 @foreach ($errors->all() as $error)
 <li class="flex items-center">
 <i class="fas fa-times-circle mr-2"></i>
 {{ $error }}
 </li>
 @endforeach
 </ul>
 </div>
 </div>
 @endif

 {{-- FORMULAIRE PRINCIPAL --}}
 <form method="POST" action="{{ route('admin.assignments.store') }}" id="assignment-form" class="space-y-6">
 @csrf

 {{-- SECTION VÉHICULE --}}
 <div class="enterprise-card">
 <div class="enterprise-card-header">
 <div class="enterprise-card-icon">
 <i class="fas fa-car text-white text-xl"></i>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">Sélection du Véhicule</h3>
 <p class="text-sm text-gray-600">Véhicules disponibles et prêts à l'assignation</p>
 </div>
 </div>

 @if(count($availableVehicles) == 0)
 <div class="alert-enterprise mb-6">
 <div class="alert-enterprise-icon">
 <i class="fas fa-car-crash text-white text-xl"></i>
 </div>
 <div>
 <p class="font-bold text-red-900 text-lg">Aucun véhicule disponible</p>
 <p class="text-red-700">Tous les véhicules sont affectés ou indisponibles</p>
 </div>
 </div>
 @endif

 <div>
 <label for="select-vehicle" class="block text-sm font-semibold text-gray-700 mb-2">
 <span class="text-red-500">*</span> Véhicule ({{ count($availableVehicles) }} disponible{{ count($availableVehicles) > 1 ? 's' : '' }})
 </label>
 <select name="vehicle_id" id="select-vehicle" required {{ count($availableVehicles) == 0 ? 'disabled' : '' }}>
 <option value="">{{ count($availableVehicles) > 0 ? 'Sélectionnez un véhicule' : 'Aucun disponible' }}</option>
 @foreach($availableVehicles as $vehicle)
 <option value="{{ $vehicle->id }}"
 data-plate="{{ $vehicle->registration_plate }}"
 data-brand="{{ $vehicle->brand }}"
 data-model="{{ $vehicle->model }}"
 data-mileage="{{ $vehicle->current_mileage ?? 0 }}"
 {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ number_format($vehicle->current_mileage ?? 0, 0, ',', ' ') }} km)
 </option>
 @endforeach
 </select>
 @error('vehicle_id')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- SECTION CHAUFFEUR --}}
 <div class="enterprise-card">
 <div class="enterprise-card-header">
 <div class="enterprise-card-icon">
 <i class="fas fa-user-tie text-white text-xl"></i>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">Sélection du Chauffeur</h3>
 <p class="text-sm text-gray-600">Chauffeurs actifs et disponibles</p>
 </div>
 </div>

 @if(count($availableDrivers) == 0)
 <div class="alert-enterprise mb-6">
 <div class="alert-enterprise-icon">
 <i class="fas fa-user-slash text-white text-xl"></i>
 </div>
 <div>
 <p class="font-bold text-red-900 text-lg">Aucun chauffeur disponible</p>
 <p class="text-red-700">Tous les chauffeurs sont affectés ou inactifs</p>
 </div>
 </div>
 @endif

 <div>
 <label for="select-driver" class="block text-sm font-semibold text-gray-700 mb-2">
 <span class="text-red-500">*</span> Chauffeur ({{ count($availableDrivers) }} libre{{ count($availableDrivers) > 1 ? 's' : '' }})
 </label>
 <select name="driver_id" id="select-driver" required {{ count($availableDrivers) == 0 ? 'disabled' : '' }}>
 <option value="">{{ count($availableDrivers) > 0 ? 'Sélectionnez un chauffeur' : 'Aucun disponible' }}</option>
 @foreach($availableDrivers as $driver)
 <option value="{{ $driver->id }}"
 data-name="{{ $driver->first_name }} {{ $driver->last_name }}"
 data-phone="{{ $driver->personal_phone }}"
 data-license="{{ $driver->license_number }}"
 {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
 {{ $driver->first_name }} {{ $driver->last_name }}
 @if($driver->personal_phone) - {{ $driver->personal_phone }} @endif
 </option>
 @endforeach
 </select>
 @error('driver_id')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- SECTION PROGRAMMATION --}}
 <div class="enterprise-card">
 <div class="enterprise-card-header">
 <div class="enterprise-card-icon">
 <i class="fas fa-calendar-alt text-white text-xl"></i>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">Programmation</h3>
 <p class="text-sm text-gray-600">Dates et horaires de l'affectation</p>
 </div>
 </div>

 {{-- DATE/HEURE DÉBUT --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <span class="text-red-500">*</span> Date de début
 </label>
 <input type="date"
 name="start_date"
 id="start_date"
 class="enterprise-input"
 value="{{ old('start_date', now()->format('Y-m-d')) }}"
 min="{{ now()->format('Y-m-d') }}"
 required>
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <span class="text-red-500">*</span> Heure de début
 </label>
 <input type="time"
 name="start_time"
 id="start_time"
 class="enterprise-input"
 value="{{ old('start_time', now()->format('H:i')) }}"
 required>
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <span class="text-red-500">*</span> Kilométrage initial
 </label>
 <input type="number"
 name="start_mileage"
 id="start_mileage"
 class="enterprise-input"
 value="{{ old('start_mileage') }}"
 min="0"
 required>
 <p class="mt-1 text-xs text-blue-600" id="mileage-hint">Sélectionnez d'abord un véhicule</p>
 </div>
 </div>

 {{-- TYPE D'AFFECTATION --}}
 <div class="mb-8">
 <label class="block text-sm font-semibold text-gray-700 mb-4">Type d'affectation</label>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div class="assignment-type-card" id="open-card">
 <input type="radio" name="assignment_type" id="type-open" value="open" 
 class="sr-only" {{ old('assignment_type', 'open') === 'open' ? 'checked' : '' }}>
 <label for="type-open" class="cursor-pointer flex items-center space-x-4">
 <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
 <i class="fas fa-infinity text-white text-2xl"></i>
 </div>
 <div>
 <h4 class="text-lg font-bold text-gray-900">Ouverte</h4>
 <p class="text-sm text-gray-600">Durée indéterminée</p>
 </div>
 </label>
 </div>

 <div class="assignment-type-card" id="scheduled-card">
 <input type="radio" name="assignment_type" id="type-scheduled" value="scheduled" 
 class="sr-only" {{ old('assignment_type') === 'scheduled' ? 'checked' : '' }}>
 <label for="type-scheduled" class="cursor-pointer flex items-center space-x-4">
 <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center">
 <i class="fas fa-calendar-check text-white text-2xl"></i>
 </div>
 <div>
 <h4 class="text-lg font-bold text-gray-900">Programmée</h4>
 <p class="text-sm text-gray-600">Date de fin précise</p>
 </div>
 </label>
 </div>
 </div>
 </div>

 {{-- DATE/HEURE FIN (conditionnel) --}}
 <div id="end-assignment-section">
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
 <input type="date"
 name="end_date"
 id="end_date"
 class="enterprise-input"
 value="{{ old('end_date') }}"
 min="{{ now()->format('Y-m-d') }}">
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Heure de fin</label>
 <input type="time"
 name="end_time"
 id="end_time"
 class="enterprise-input"
 value="{{ old('end_time') }}">
 </div>
 </div>
 </div>
 </div>

 {{-- SECTION INFORMATIONS COMPLÉMENTAIRES --}}
 <div class="enterprise-card">
 <div class="enterprise-card-header">
 <div class="enterprise-card-icon">
 <i class="fas fa-sticky-note text-white text-xl"></i>
 </div>
 <div>
 <h3 class="text-xl font-bold text-gray-900">Informations complémentaires</h3>
 <p class="text-sm text-gray-600">Détails et observations</p>
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Motif</label>
 <select name="purpose" class="enterprise-input">
 <option value="">Sélectionnez (optionnel)</option>
 <option value="mission">Mission professionnelle</option>
 <option value="formation">Formation</option>
 <option value="maintenance">Maintenance</option>
 <option value="deplacement">Déplacement administratif</option>
 <option value="urgence">Urgence</option>
 <option value="autre">Autre</option>
 </select>
 </div>

 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
 <textarea name="notes"
 rows="3"
 class="enterprise-input resize-none"
 placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
 </div>
 </div>
 </div>

 {{-- ACTIONS --}}
 <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-3xl p-8">
 <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
 <div class="flex items-center space-x-3">
 <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
 <span class="text-sm font-medium text-gray-700">Formulaire prêt</span>
 </div>

 <div class="flex space-x-4">
 <a href="{{ route('admin.assignments.index') }}" 
 class="btn-enterprise-secondary">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </a>

 <button type="submit" 
 class="btn-enterprise-primary"
 id="submit-btn">
 <i class="fas fa-rocket mr-2"></i>
 <span id="submit-text">Créer l'Affectation</span>
 </button>
 </div>
 </div>
 </div>
 </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
 console.log('🚀 Initialisation Enterprise Assignment Form');

 // TomSelect pour véhicules
 new TomSelect('#select-vehicle', {
 create: false,
 sortField: 'text',
 placeholder: 'Rechercher un véhicule...',
 onChange: function(value) {
 const option = this.options[value];
 if (option) {
 const mileage = option['data-mileage'] || 0;
 document.getElementById('start_mileage').value = mileage;
 document.getElementById('mileage-hint').textContent = 
 `Kilométrage actuel: ${parseInt(mileage).toLocaleString('fr-FR')} km`;
 }
 }
 });

 // TomSelect pour chauffeurs
 new TomSelect('#select-driver', {
 create: false,
 sortField: 'text',
 placeholder: 'Rechercher un chauffeur...'
 });

 // Gestion du type d'affectation
 const openCard = document.getElementById('open-card');
 const scheduledCard = document.getElementById('scheduled-card');
 const openRadio = document.getElementById('type-open');
 const scheduledRadio = document.getElementById('type-scheduled');
 const endSection = document.getElementById('end-assignment-section');

 function updateAssignmentType() {
 const isScheduled = scheduledRadio.checked;
 
 if (isScheduled) {
 endSection.classList.add('show');
 scheduledCard.classList.add('selected-scheduled');
 scheduledCard.classList.remove('selected-open');
 openCard.classList.remove('selected-open', 'selected-scheduled');
 } else {
 endSection.classList.remove('show');
 openCard.classList.add('selected-open');
 openCard.classList.remove('selected-scheduled');
 scheduledCard.classList.remove('selected-open', 'selected-scheduled');
 }
 }

 openCard.addEventListener('click', () => {
 openRadio.checked = true;
 updateAssignmentType();
 });

 scheduledCard.addEventListener('click', () => {
 scheduledRadio.checked = true;
 updateAssignmentType();
 });

 openRadio.addEventListener('change', updateAssignmentType);
 scheduledRadio.addEventListener('change', updateAssignmentType);

 // Initialiser
 updateAssignmentType();

 // Animation de soumission
 document.getElementById('assignment-form').addEventListener('submit', function() {
 const submitBtn = document.getElementById('submit-btn');
 const submitText = document.getElementById('submit-text');
 submitText.innerHTML = '<span class="loading-spinner"></span> Création...';
 submitBtn.disabled = true;
 });
});
</script>
@endpush
