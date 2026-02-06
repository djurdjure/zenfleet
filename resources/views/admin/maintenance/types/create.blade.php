{{-- resources/views/admin/maintenance/types/create.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Nouveau Type de Maintenance - ZenFleet')

@push('styles')
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
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
 transition: left 0.6s ease;
}

.form-section:hover::before {
 left: 100%;
}

.form-section:hover {
 box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

.section-header {
 display: flex;
 align-items: center;
 margin-bottom: 1.5rem;
 padding-bottom: 0.75rem;
 border-bottom: 1px solid #e2e8f0;
}

.section-icon {
 width: 2.5rem;
 height: 2.5rem;
 border-radius: 0.75rem;
 display: flex;
 align-items: center;
 justify-content: center;
 margin-right: 0.75rem;
 box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-primary {
 background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
 border: none;
 color: white;
 transition: all 0.2s ease;
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
 transform: translateY(-2px);
 box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
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

.category-selector {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
 gap: 1rem;
}

.category-option {
 position: relative;
 cursor: pointer;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
 border: 2px solid #e2e8f0;
 border-radius: 12px;
 padding: 1.5rem;
 transition: all 0.3s ease;
}

.category-option:hover {
 transform: translateY(-4px);
 box-shadow: 0 8px 25px rgba(0,0,0,0.1);
 border-color: #6366f1;
}

.category-option.selected {
 background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
 border-color: #8b5cf6;
 transform: translateY(-4px);
 box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
}

.interval-calculator {
 background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
 border: 2px solid #0284c7;
 border-radius: 12px;
 transition: all 0.3s ease;
}

.interval-calculator:hover {
 transform: translateY(-2px);
 box-shadow: 0 8px 25px rgba(2, 132, 199, 0.15);
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
 </div>
 </div>
 @endif

 {{-- En-tête --}}
 <div class="mb-8">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.maintenance.dashboard') }}" class="hover:text-indigo-600 transition-colors">
 <i class="fas fa-wrench mr-1"></i> Maintenance
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.maintenance.types.index') }}" class="hover:text-indigo-600 transition-colors">
 Types de maintenance
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-indigo-600 font-semibold">Nouveau type</span>
 </nav>

 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-2xl font-bold leading-6 text-gray-900">Nouveau Type de Maintenance</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 Configuration des types de maintenance enterprise
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4">
 <a href="{{ route('admin.maintenance.types.index') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
 <form action="{{ route('admin.maintenance.types.store') }}" method="POST" id="type-form" class="space-y-8">
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

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div>
 <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-tag text-blue-500 mr-2"></i>Nom du type <span class="text-red-500">*</span>
 </label>
 <input type="text" id="name" name="name" value="{{ old('name') }}" required
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Ex: Vidange, Révision générale, Contrôle technique...">
 @error('name')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-code text-purple-500 mr-2"></i>Code unique
 </label>
 <input type="text" id="code" name="code" value="{{ old('code') }}"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Ex: VID, REV_GEN, CT...">
 @error('code')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-xs text-gray-500">Code unique pour identification dans le système</p>
 </div>
 </div>

 <div class="mt-6">
 <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-file-alt text-gray-500 mr-2"></i>Description détaillée
 </label>
 <textarea id="description" name="description" rows="3"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Décrivez en détail ce type de maintenance...">{{ old('description') }}</textarea>
 @error('description')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Section Catégorie --}}
 <div class="form-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-purple-500 text-white">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-semibold text-gray-900">Catégorie de maintenance</h3>
 </div>

 <div class="category-selector">
 <label class="category-option" for="category_preventive">
 <input type="radio" id="category_preventive" name="category" value="preventive"
 {{ old('category') == 'preventive' ? 'checked' : '' }} class="sr-only">
 <div class="text-center">
 <div class="w-12 h-12 mx-auto mb-3 bg-green-100 border border-green-200 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="font-semibold text-gray-900 mb-2">Préventive</h4>
 <p class="text-sm text-gray-600">Maintenance planifiée pour prévenir les pannes</p>
 </div>
 </label>

 <label class="category-option" for="category_corrective">
 <input type="radio" id="category_corrective" name="category" value="corrective"
 {{ old('category') == 'corrective' ? 'checked' : '' }} class="sr-only">
 <div class="text-center">
 <div class="w-12 h-12 mx-auto mb-3 bg-orange-100 border border-orange-200 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="font-semibold text-gray-900 mb-2">Corrective</h4>
 <p class="text-sm text-gray-600">Maintenance suite à une panne ou dysfonctionnement</p>
 </div>
 </label>

 <label class="category-option" for="category_predictive">
 <input type="radio" id="category_predictive" name="category" value="predictive"
 {{ old('category') == 'predictive' ? 'checked' : '' }} class="sr-only">
 <div class="text-center">
 <div class="w-12 h-12 mx-auto mb-3 bg-blue-100 border border-blue-200 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
 </svg>
 </div>
 <h4 class="font-semibold text-gray-900 mb-2">Prédictive</h4>
 <p class="text-sm text-gray-600">Maintenance basée sur l'analyse des données</p>
 </div>
 </label>

 <label class="category-option" for="category_mandatory">
 <input type="radio" id="category_mandatory" name="category" value="mandatory"
 {{ old('category') == 'mandatory' ? 'checked' : '' }} class="sr-only">
 <div class="text-center">
 <div class="w-12 h-12 mx-auto mb-3 bg-red-100 border border-red-200 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="font-semibold text-gray-900 mb-2">Obligatoire</h4>
 <p class="text-sm text-gray-600">Maintenance légalement obligatoire</p>
 </div>
 </label>
 </div>
 @error('category')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 {{-- Section Configuration des intervalles --}}
 <div class="form-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-indigo-500 text-white">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-semibold text-gray-900">Configuration des intervalles</h3>
 </div>

 <div class="space-y-6">
 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div>
 <label for="frequency_type" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-clock text-indigo-500 mr-2"></i>Type de fréquence
 </label>
 <select id="frequency_type" name="frequency_type" class="form-input w-full px-3 py-2 rounded-md">
 <option value="">Sélectionner un type</option>
 <option value="time" {{ old('frequency_type') == 'time' ? 'selected' : '' }}>Basée sur le temps</option>
 <option value="mileage" {{ old('frequency_type') == 'mileage' ? 'selected' : '' }}>Basée sur le kilométrage</option>
 <option value="both" {{ old('frequency_type') == 'both' ? 'selected' : '' }}>Les deux (premier atteint)</option>
 </select>
 @error('frequency_type')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="is_mandatory" class="flex items-center">
 <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1"
 {{ old('is_mandatory') ? 'checked' : '' }}
 class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-700">
 <i class="fas fa-gavel text-red-500 mr-1"></i>
 Maintenance obligatoire/légale
 </span>
 </label>
 <p class="mt-1 text-xs text-gray-500">Cochez si cette maintenance est requise par la loi</p>
 </div>
 </div>

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2" id="interval-inputs">
 <div id="time-interval" style="display: none;">
 <label for="time_interval_days" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Intervalle en jours
 </label>
 <input type="number" id="time_interval_days" name="time_interval_days"
 value="{{ old('time_interval_days') }}" min="1"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Ex: 365 pour annuel">
 @error('time_interval_days')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div id="mileage-interval" style="display: none;">
 <label for="mileage_interval_km" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-road text-green-500 mr-2"></i>Intervalle en kilomètres
 </label>
 <input type="number" id="mileage_interval_km" name="mileage_interval_km"
 value="{{ old('mileage_interval_km') }}" min="1"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Ex: 10000">
 @error('mileage_interval_km')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="interval-calculator p-4" style="display: none;" id="calculator">
 <h4 class="font-semibold text-blue-900 mb-3">
 <i class="fas fa-calculator text-blue-600 mr-2"></i>Calculateur d'intervalles
 </h4>
 <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
 <div>
 <label class="block text-sm font-medium text-blue-800 mb-1">Fréquence temps</label>
 <div id="time-display" class="text-lg font-bold text-blue-900">-</div>
 </div>
 <div>
 <label class="block text-sm font-medium text-blue-800 mb-1">Fréquence kilométrage</label>
 <div id="mileage-display" class="text-lg font-bold text-blue-900">-</div>
 </div>
 <div>
 <label class="block text-sm font-medium text-blue-800 mb-1">Recommandation</label>
 <div id="recommendation" class="text-sm text-blue-800">-</div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Section Coûts estimés --}}
 <div class="form-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-green-500 text-white">
 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-semibold text-gray-900">Coûts estimés et informations supplémentaires</h3>
 </div>

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div>
 <label for="estimated_cost_min" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-euro-sign text-green-500 mr-2"></i>Coût minimum estimé (€)
 </label>
 <input type="number" id="estimated_cost_min" name="estimated_cost_min"
 value="{{ old('estimated_cost_min') }}" step="0.01" min="0"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="0.00">
 @error('estimated_cost_min')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="estimated_cost_max" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-euro-sign text-green-500 mr-2"></i>Coût maximum estimé (€)
 </label>
 <input type="number" id="estimated_cost_max" name="estimated_cost_max"
 value="{{ old('estimated_cost_max') }}" step="0.01" min="0"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="0.00">
 @error('estimated_cost_max')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-clock text-indigo-500 mr-2"></i>Durée estimée (minutes)
 </label>
 <input type="number" id="estimated_duration_minutes" name="estimated_duration_minutes"
 value="{{ old('estimated_duration_minutes') }}" min="1"
 class="form-input w-full px-3 py-2 rounded-md"
 placeholder="Ex: 120">
 @error('estimated_duration_minutes')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="requires_vehicle_downtime" class="flex items-center">
 <input type="checkbox" id="requires_vehicle_downtime" name="requires_vehicle_downtime" value="1"
 {{ old('requires_vehicle_downtime') ? 'checked' : '' }}
 class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-700">
 <i class="fas fa-pause-circle text-orange-500 mr-1"></i>
 Nécessite une immobilisation du véhicule
 </span>
 </label>
 <p class="mt-1 text-xs text-gray-500">Le véhicule ne peut pas être utilisé pendant cette maintenance</p>
 </div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
 <a href="{{ route('admin.maintenance.types.index') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 Annuler
 </a>
 <button type="submit" class="btn-primary inline-flex items-center px-6 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="submit-btn">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
 </svg>
 <span id="submit-text">Créer le type</span>
 </button>
 </div>
 </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
 const form = document.getElementById('type-form');
 const submitBtn = document.getElementById('submit-btn');
 const submitText = document.getElementById('submit-text');

 // Form submission avec état de loading
 form.addEventListener('submit', function() {
 submitBtn.disabled = true;
 submitText.textContent = 'Création en cours...';
 });

 // Gestion des catégories
 const categoryOptions = document.querySelectorAll('input[name="category"]');
 categoryOptions.forEach(option => {
 option.addEventListener('change', function() {
 // Retirer la classe selected de toutes les options
 document.querySelectorAll('.category-option').forEach(opt => {
 opt.classList.remove('selected');
 });
 // Ajouter la classe selected à l'option sélectionnée
 this.closest('.category-option').classList.add('selected');
 });
 });

 // Initialiser la sélection si une valeur est déjà sélectionnée
 const selectedCategory = document.querySelector('input[name="category"]:checked');
 if (selectedCategory) {
 selectedCategory.closest('.category-option').classList.add('selected');
 }

 // Gestion des intervalles
 const frequencyType = document.getElementById('frequency_type');
 const timeInterval = document.getElementById('time-interval');
 const mileageInterval = document.getElementById('mileage-interval');
 const calculator = document.getElementById('calculator');

 function updateIntervalInputs() {
 const type = frequencyType.value;

 // Cacher tous les inputs
 timeInterval.style.display = 'none';
 mileageInterval.style.display = 'none';
 calculator.style.display = 'none';

 // Afficher les inputs appropriés
 if (type === 'time') {
 timeInterval.style.display = 'block';
 calculator.style.display = 'block';
 } else if (type === 'mileage') {
 mileageInterval.style.display = 'block';
 calculator.style.display = 'block';
 } else if (type === 'both') {
 timeInterval.style.display = 'block';
 mileageInterval.style.display = 'block';
 calculator.style.display = 'block';
 }

 updateCalculator();
 }

 frequencyType.addEventListener('change', updateIntervalInputs);

 // Calculateur d'intervalles
 function updateCalculator() {
 const timeInput = document.getElementById('time_interval_days');
 const mileageInput = document.getElementById('mileage_interval_km');
 const timeDisplay = document.getElementById('time-display');
 const mileageDisplay = document.getElementById('mileage-display');
 const recommendation = document.getElementById('recommendation');

 const timeDays = parseInt(timeInput.value) || 0;
 const mileageKm = parseInt(mileageInput.value) || 0;

 // Affichage du temps
 if (timeDays > 0) {
 if (timeDays >= 365) {
 const years = Math.floor(timeDays / 365);
 const remainingDays = timeDays % 365;
 timeDisplay.textContent = remainingDays > 0 ? `${years} an${years > 1 ? 's' : ''} ${remainingDays}j` : `${years} an${years > 1 ? 's' : ''}`;
 } else if (timeDays >= 30) {
 const months = Math.floor(timeDays / 30);
 const remainingDays = timeDays % 30;
 timeDisplay.textContent = remainingDays > 0 ? `${months} mois ${remainingDays}j` : `${months} mois`;
 } else {
 timeDisplay.textContent = `${timeDays} jour${timeDays > 1 ? 's' : ''}`;
 }
 } else {
 timeDisplay.textContent = '-';
 }

 // Affichage du kilométrage
 if (mileageKm > 0) {
 mileageDisplay.textContent = `${mileageKm.toLocaleString()} km`;
 } else {
 mileageDisplay.textContent = '-';
 }

 // Recommandations
 let rec = '';
 if (timeDays > 0 && mileageKm > 0) {
 rec = 'Premier échéance atteinte';
 } else if (timeDays > 0) {
 rec = 'Maintenance périodique';
 } else if (mileageKm > 0) {
 rec = 'Maintenance par usage';
 } else {
 rec = 'Configuration incomplète';
 }
 recommendation.textContent = rec;
 }

 document.getElementById('time_interval_days')?.addEventListener('input', updateCalculator);
 document.getElementById('mileage_interval_km')?.addEventListener('input', updateCalculator);

 // Validation des coûts
 const costMin = document.getElementById('estimated_cost_min');
 const costMax = document.getElementById('estimated_cost_max');

 function validateCosts() {
 const min = parseFloat(costMin.value) || 0;
 const max = parseFloat(costMax.value) || 0;

 if (max > 0 && min > max) {
 costMax.setCustomValidity('Le coût maximum ne peut pas être inférieur au coût minimum');
 } else {
 costMax.setCustomValidity('');
 }
 }

 costMin?.addEventListener('input', validateCosts);
 costMax?.addEventListener('input', validateCosts);

 // Initialisation
 updateIntervalInputs();

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
@endsection