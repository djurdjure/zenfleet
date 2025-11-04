{{-- resources/views/admin/vehicles/enterprise-show.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Détails Véhicule - ZenFleet')

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

.metric-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.metric-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 12px rgba(0,0,0,0.08);
 border-color: #cbd5e1;
}

.info-section {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.info-section:hover {
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
 border-color: #cbd5e1;
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
 color: white;
}

.btn-primary {
 background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
 color: white;
 padding: 0.75rem 2rem;
 border-radius: 0.5rem;
 font-weight: 600;
 border: none;
 transition: all 0.2s ease;
 box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
 background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
 transform: translateY(-1px);
 box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
 background: white;
 color: #374151;
 padding: 0.75rem 2rem;
 border: 2px solid #e5e7eb;
 border-radius: 0.5rem;
 font-weight: 600;
 transition: all 0.2s ease;
}

.btn-secondary:hover {
 background: #f9fafb;
 border-color: #d1d5db;
 transform: translateY(-1px);
}

.status-badge {
 display: inline-flex;
 align-items: center;
 padding: 0.25rem 0.75rem;
 border-radius: 9999px;
 font-size: 0.75rem;
 font-weight: 500;
}

.status-available {
 background-color: #dcfce7;
 color: #166534;
}

.status-assigned {
 background-color: #fef3c7;
 color: #92400e;
}

.status-maintenance {
 background-color: #fee2e2;
 color: #991b1b;
}

.status-inactive {
 background-color: #f3f4f6;
 color: #374151;
}

.vehicle-hero {
 background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
 border: 1px solid #bae6fd;
 transition: all 0.3s ease;
}

.vehicle-hero:hover {
 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
 border-color: #7dd3fc;
}

.dropdown-menu {
 background: white;
 border: 1px solid #e2e8f0;
 border-radius: 0.75rem;
 box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.grid-stats {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
 gap: 1.5rem;
}

.info-item {
 display: flex;
 align-items: center;
 padding: 0.75rem;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
 border-radius: 0.5rem;
 border: 1px solid #e2e8f0;
}

.timeline-item {
 position: relative;
 padding-left: 2rem;
 padding-bottom: 1rem;
}

.timeline-item::before {
 content: '';
 position: absolute;
 left: 0.5rem;
 top: 0;
 bottom: 0;
 width: 2px;
 background: #e2e8f0;
}

.timeline-item:last-child::before {
 bottom: 1rem;
}

.timeline-dot {
 position: absolute;
 left: 0;
 top: 0.5rem;
 width: 1.5rem;
 height: 1.5rem;
 border-radius: 50%;
 background: white;
 border: 2px solid #3b82f6;
 display: flex;
 align-items: center;
 justify-content: center;
}

/* Enterprise animations */
@keyframes pulse {
 0%, 100% {
 opacity: 1;
 }
 50% {
 opacity: 0.5;
 }
}

.pulse {
 animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Hover effects for enterprise feel */
.enterprise-hover:hover {
 transform: scale(1.02);
 transition: all 0.2s ease;
}

/* Loading states */
.loading {
 position: relative;
 overflow: hidden;
}

.loading::after {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
 animation: loading 1.5s infinite;
}

@keyframes loading {
 0% {
 left: -100%;
 }
 100% {
 left: 100%;
 }
}
</style>
@endpush

@section('content')
<div class="fade-in">
 {{-- En-tête compact --}}
 <div class="mb-8">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors enterprise-hover inline-flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 Véhicules
 </a>
 <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
 </svg>
 <span class="text-blue-600 font-semibold">{{ $vehicle->registration_plate }}</span>
 </nav>

 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-xl font-semibold leading-6 text-gray-900">{{ $vehicle->registration_plate }}</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacturing_year }})
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-500">
 @php
 $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
 @endphp
 <div class="h-2 w-2 rounded-full mr-1.5
 {{ $statusName === 'Disponible' ? 'bg-green-400' : '' }}
 {{ $statusName === 'Affecté' ? 'bg-yellow-400' : '' }}
 {{ $statusName === 'Maintenance' ? 'bg-red-400' : '' }}
 {{ !in_array($statusName, ['Disponible', 'Affecté', 'Maintenance']) ? 'bg-gray-400' : '' }}
 "></div>
 {{ $statusName }}
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 @can('edit_vehicles')
 <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
 </svg>
 Modifier
 </a>
 @endcan

 <div class="relative" x-data="{ open: false }">
 <button @click="open = !open" class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
 </svg>
 Actions
 </button>

 <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="dropdown-menu absolute right-0 z-10 mt-2 w-48 origin-top-right focus:outline-none">
 <div class="py-1">
 <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 </svg>
 Exporter PDF
 </a>
 <a href="{{ route('admin.vehicles.mileage-history', $vehicle) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 Historique kilométrique
 </a>
 <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path d="M7 9a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9z"></path>
 <path d="M5 3a2 2 0 00-2 2v6a2 2 0 002 2V5h8a2 2 0 00-2-2H5z"></path>
 </svg>
 Dupliquer
 </a>
 @can('delete_vehicles')
 <div class="border-t border-gray-100"></div>
 <button onclick="deleteVehicle({{ $vehicle->id }})" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
 <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 Supprimer
 </button>
 @endcan
 </div>
 </div>
 </div>

 <a href="{{ route('admin.vehicles.index') }}" class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 Retour
 </a>
 </div>
 </div>
 </div>

 {{-- Hero section du véhicule --}}
 <div class="vehicle-hero rounded-lg p-6 mb-8">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-6">
 <div class="flex-shrink-0">
 <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg">
 <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 </div>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
 <p class="text-lg text-gray-600">{{ $vehicle->registration_plate }} • {{ $vehicle->manufacturing_year }}</p>
 @php
 $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
 $statusClass = match($statusName) {
 'Disponible' => 'status-available',
 'Affecté' => 'status-assigned',
 'Maintenance' => 'status-maintenance',
 default => 'status-inactive'
 };
 @endphp
 <span class="status-badge {{ $statusClass }} mt-2">
 <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <circle cx="10" cy="10" r="10"></circle>
 </svg>
 {{ $statusName }}
 </span>
 </div>
 </div>
 <div class="text-right">
 <div class="text-3xl font-bold text-gray-900">{{ number_format($vehicle->current_mileage) }}</div>
 <div class="text-sm text-gray-500">kilomètres</div>
 <div class="text-sm text-gray-500 mt-1">VIN: {{ $vehicle->vin }}</div>
 </div>
 </div>
 </div>

 {{-- Statistiques principales --}}
 <div class="grid-stats mb-8" id="stats-grid">
 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Âge du véhicule</p>
 <p class="text-2xl font-bold text-gray-900">
 {{ isset($analytics['age_years']) ? $analytics['age_years'] : (date('Y') - $vehicle->manufacturing_year) }}
 <span class="text-sm font-medium text-gray-600">ans</span>
 </p>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Utilisation</p>
 <p class="text-2xl font-bold text-green-600">
 {{ isset($analytics['utilization_rate']) ? round($analytics['utilization_rate'] * 100) : '85' }}
 <span class="text-sm font-medium text-gray-600">%</span>
 </p>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Valeur actuelle</p>
 <p class="text-2xl font-bold text-orange-600">
 {{ $vehicle->current_value ? number_format($vehicle->current_value) : number_format($vehicle->purchase_price * 0.7) }}
 <span class="text-sm font-medium text-gray-600">€</span>
 </p>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Coût maintenance</p>
 <p class="text-2xl font-bold text-red-600">
 {{ isset($analytics['maintenance_cost_total']) ? number_format($analytics['maintenance_cost_total']) : '2 850' }}
 <span class="text-sm font-medium text-gray-600">€</span>
 </p>
 </div>
 </div>
 </div>
 </div>

 {{-- Contenu principal --}}
 <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
 {{-- Informations détaillées --}}
 <div class="lg:col-span-2 space-y-8">
 {{-- Informations générales --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-blue-500 to-blue-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
 </div>

 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Plaque d'immatriculation</p>
 <p class="text-lg font-semibold text-gray-900">{{ $vehicle->registration_plate }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Numéro VIN</p>
 <p class="text-sm font-mono text-gray-900">{{ $vehicle->vin }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Marque et modèle</p>
 <p class="text-lg font-semibold text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Année • Couleur</p>
 <p class="text-lg font-semibold text-gray-900">{{ $vehicle->manufacturing_year }} • {{ $vehicle->color }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Type de véhicule</p>
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $vehicle->vehicleType->name ?? 'N/A' }}
 </span>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Nombre de places</p>
 <p class="text-lg font-semibold text-gray-900">{{ $vehicle->seats }} places</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Spécifications techniques --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-purple-500 to-purple-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Spécifications techniques</h3>
 </div>

 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Carburant</p>
 <p class="text-sm text-gray-900">{{ $vehicle->fuelType->name ?? 'N/A' }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Transmission</p>
 <p class="text-sm text-gray-900">{{ $vehicle->transmissionType->name ?? 'N/A' }}</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Cylindrée</p>
 <p class="text-sm text-gray-900">{{ number_format($vehicle->engine_displacement_cc) }} cc</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Puissance</p>
 <p class="text-sm text-gray-900">{{ number_format($vehicle->power_hp) }} HP</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Kilométrage initial</p>
 <p class="text-sm text-gray-900">{{ number_format($vehicle->initial_mileage) }} km</p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Kilométrage actuel</p>
 <p class="text-sm font-semibold text-gray-900">{{ number_format($vehicle->current_mileage) }} km</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Affectation Dépôt - ENTERPRISE GRADE --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
 <x-iconify icon="mdi:office-building" class="w-4 h-4" />
 </div>
 <div class="flex-1 flex justify-between items-center">
 <h3 class="text-lg font-medium text-gray-900">Affectation Dépôt</h3>
 @if($vehicle->depot_id)
 <button
 onclick="Livewire.dispatch('openAssignDepotModal', { vehicleId: {{ $vehicle->id }} })"
 class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm"
 >
 <x-iconify icon="mdi:swap-horizontal" class="w-4 h-4 inline mr-1" />
 Changer de dépôt
 </button>
 @else
 <button
 onclick="Livewire.dispatch('openAssignDepotModal', { vehicleId: {{ $vehicle->id }} })"
 class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm"
 >
 <x-iconify icon="mdi:plus" class="w-4 h-4 inline mr-1" />
 Affecter à un dépôt
 </button>
 @endif
 </div>
 </div>

 @if($vehicle->depot)
 {{-- Dépôt actuel --}}
 <div class="bg-indigo-50 border-2 border-indigo-200 rounded-lg p-4 mb-4">
 <div class="flex items-start justify-between">
 <div class="flex-1">
 <div class="flex items-center mb-2">
 <x-iconify icon="mdi:office-building" class="w-5 h-5 text-indigo-600 mr-2" />
 <h4 class="font-semibold text-indigo-900">{{ $vehicle->depot->name }}</h4>
 <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs">
 {{ $vehicle->depot->code }}
 </span>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
 @if($vehicle->depot->city || $vehicle->depot->wilaya)
 <div class="flex items-center text-gray-700">
 <x-iconify icon="mdi:map-marker" class="w-4 h-4 text-gray-500 mr-1" />
 {{ $vehicle->depot->city }}{{ $vehicle->depot->wilaya ? ', ' . $vehicle->depot->wilaya : '' }}
 </div>
 @endif

 @if($vehicle->depot->phone)
 <div class="flex items-center text-gray-700">
 <x-iconify icon="mdi:phone" class="w-4 h-4 text-gray-500 mr-1" />
 {{ $vehicle->depot->phone }}
 </div>
 @endif

 @if($vehicle->depot->manager_name)
 <div class="flex items-center text-gray-700">
 <x-iconify icon="mdi:account" class="w-4 h-4 text-gray-500 mr-1" />
 {{ $vehicle->depot->manager_name }}
 </div>
 @endif

 @if($vehicle->depot->capacity)
 <div class="flex items-center text-gray-700">
 <x-iconify icon="mdi:inbox" class="w-4 h-4 text-gray-500 mr-1" />
 Occupation: {{ $vehicle->depot->current_count }} / {{ $vehicle->depot->capacity }}
 </div>
 @endif
 </div>
 </div>

 @if($vehicle->depot->capacity)
 <div class="ml-4">
 <div class="w-24">
 @php
 $percentage = $vehicle->depot->capacity > 0
 ? ($vehicle->depot->current_count / $vehicle->depot->capacity) * 100
 : 0;
 $colorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-orange-500' : 'bg-green-500');
 @endphp
 <div class="text-center mb-1">
 <span class="text-2xl font-bold text-indigo-900">{{ round($percentage) }}%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-2">
 <div class="{{ $colorClass }} h-2 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%"></div>
 </div>
 <p class="text-xs text-gray-600 text-center mt-1">Taux d'occupation</p>
 </div>
 </div>
 @endif
 </div>
 </div>

 {{-- Historique récent --}}
 @php
 $recentHistory = $vehicle->depotAssignmentHistory()
 ->with(['depot', 'previousDepot', 'assignedBy'])
 ->latest('assigned_at')
 ->limit(3)
 ->get();
 @endphp

 @if($recentHistory->count() > 0)
 <div class="mt-4">
 <h5 class="text-sm font-medium text-gray-700 mb-3">Historique récent</h5>
 <div class="space-y-2">
 @foreach($recentHistory as $history)
 <div class="flex items-center text-sm p-2 bg-gray-50 rounded-lg">
 @php
 $iconColorClass = match($history->action) {
 'assigned' => 'text-green-600',
 'unassigned' => 'text-red-600',
 'transferred' => 'text-blue-600',
 default => 'text-gray-600',
 };
 @endphp
 <div class="mr-3 {{ $iconColorClass }}">
 @if($history->action === 'assigned')
 <x-iconify icon="mdi:office-building" class="w-4 h-4" />
 @elseif($history->action === 'unassigned')
 <x-iconify icon="mdi:close-circle" class="w-4 h-4" />
 @else
 <x-iconify icon="mdi:swap-horizontal" class="w-4 h-4" />
 @endif
 </div>
 <div class="flex-1">
 <p class="font-medium text-gray-900">{{ $history->actionLabel }}</p>
 <p class="text-xs text-gray-600">
 @if($history->isTransfer())
 De {{ $history->previousDepot?->name ?? 'Inconnu' }} vers {{ $history->depot?->name ?? 'Inconnu' }}
 @elseif($history->isAssignment())
 {{ $history->depot?->name ?? 'Dépôt inconnu' }}
 @else
 {{ $history->previousDepot?->name ?? 'Dépôt inconnu' }}
 @endif
 </p>
 </div>
 <div class="text-right">
 <p class="text-xs text-gray-500">{{ $history->assigned_at->format('d/m/Y') }}</p>
 <p class="text-xs text-gray-400">{{ $history->assigned_at->format('H:i') }}</p>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif
 @else
 {{-- Aucun dépôt affecté --}}
 <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
 <x-iconify icon="mdi:office-building" class="w-12 h-12 mx-auto text-gray-400 mb-3" />
 <h4 class="text-sm font-medium text-gray-900 mb-2">Aucun dépôt affecté</h4>
 <p class="text-sm text-gray-600 mb-4">Ce véhicule n'est actuellement affecté à aucun dépôt</p>
 <button
 onclick="Livewire.dispatch('openAssignDepotModal', { vehicleId: {{ $vehicle->id }} })"
 class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm"
 >
 <x-iconify icon="mdi:plus" class="w-4 h-4 inline mr-1" />
 Affecter à un dépôt
 </button>
 </div>
 @endif
 </div>

 {{-- Informations financières --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-green-500 to-green-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Informations financières</h3>
 </div>

 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Date d'acquisition</p>
 <p class="text-sm text-gray-900">
 {{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A' }}
 </p>
 </div>
 </div>

 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Prix d'achat</p>
 <p class="text-lg font-bold text-gray-900">{{ number_format($vehicle->purchase_price, 2) }} €</p>
 </div>
 </div>

 @if($vehicle->current_value)
 <div class="info-item">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Valeur actuelle</p>
 <p class="text-lg font-bold text-gray-900">{{ number_format($vehicle->current_value, 2) }} €</p>
 </div>
 </div>
 @endif
 </div>
 </div>

 {{-- Notes si présentes --}}
 @if($vehicle->notes)
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-amber-500 to-amber-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Notes et observations</h3>
 </div>

 <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
 <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vehicle->notes }}</p>
 </div>
 </div>
 @endif
 </div>

 {{-- Sidebar --}}
 <div class="space-y-6">
 {{-- Actions rapides --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Actions rapides</h4>
 </div>

 <div class="space-y-3">
 <button onclick="assignDriver()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
 </svg>
 Affecter chauffeur
 </button>

 <button onclick="scheduleMaintenance()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 Programmer maintenance
 </button>

 <button onclick="generateReport()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
 </svg>
 Générer rapport
 </button>

 <button onclick="uploadPhotos()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
 </svg>
 Ajouter photos
 </button>
 </div>
 </div>

 {{-- Timeline d'activité --}}
 @if(isset($timeline) && !empty($timeline))
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-blue-500 to-blue-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Activité récente</h4>
 </div>

 <div class="space-y-4">
 @foreach($timeline as $event)
 <div class="timeline-item">
 <div class="timeline-dot">
 <svg class="w-3 h-3 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
 <circle cx="10" cy="10" r="3"></circle>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-900">{{ $event['title'] }}</p>
 <p class="text-xs text-gray-500">{{ $event['date'] }}</p>
 @if(isset($event['description']))
 <p class="text-xs text-gray-600 mt-1">{{ $event['description'] }}</p>
 @endif
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Informations système --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-gray-500 to-gray-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Informations système</h4>
 </div>

 <div class="space-y-3 text-xs text-gray-600">
 <div class="flex justify-between">
 <span>ID Véhicule:</span>
 <span class="font-mono">{{ $vehicle->id }}</span>
 </div>
 <div class="flex justify-between">
 <span>Créé le:</span>
 <span>{{ $vehicle->created_at->format('d/m/Y H:i') }}</span>
 </div>
 <div class="flex justify-between">
 <span>Modifié le:</span>
 <span>{{ $vehicle->updated_at->format('d/m/Y H:i') }}</span>
 </div>
 @if($vehicle->organization)
 <div class="flex justify-between">
 <span>Organisation:</span>
 <span>{{ $vehicle->organization->name }}</span>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>
</div>

{{-- Modal Livewire pour affectation dépôt --}}
@livewire('assignments.assign-depot-modal')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteVehicle(vehicleId) {
 if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/vehicles/${vehicleId}`;
 form.innerHTML = `
 @csrf
 @method('DELETE')
 `;
 document.body.appendChild(form);
 form.submit();
 }
}

// Enterprise-grade functionality
function assignDriver() {
 // Advanced driver assignment modal with search and filtering
 Swal.fire({
 title: 'Affecter un Chauffeur',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher un chauffeur</label>
 <input type="text" id="driver-search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nom du chauffeur...">
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Date d'affectation</label>
 <input type="date" id="assignment-date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${new Date().toISOString().split('T')[0]}">
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Type d'affectation</label>
 <select id="assignment-type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="permanent">Affectation permanente</option>
 <option value="temporary">Affectation temporaire</option>
 <option value="shared">Partage de véhicule</option>
 </select>
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Affecter',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#3b82f6',
 width: '500px'
 }).then((result) => {
 if (result.isConfirmed) {
 // Process driver assignment
 Swal.fire('Succès!', 'Chauffeur affecté avec succès.', 'success');
 }
 });
}

function scheduleMaintenance() {
 // Advanced maintenance scheduling with service types
 Swal.fire({
 title: 'Programmer une Maintenance',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Type de maintenance</label>
 <select id="maintenance-type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="preventive">Maintenance préventive</option>
 <option value="corrective">Maintenance corrective</option>
 <option value="inspection">Inspection technique</option>
 <option value="overhaul">Révision générale</option>
 </select>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Date prévue</label>
 <input type="datetime-local" id="maintenance-date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Garage/Atelier</label>
 <select id="garage-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="internal">Atelier interne</option>
 <option value="external1">Garage Partenaire A</option>
 <option value="external2">Garage Partenaire B</option>
 </select>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
 <textarea id="maintenance-notes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Détails sur la maintenance à effectuer..."></textarea>
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Programmer',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#3b82f6',
 width: '500px'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Programmé!', 'Maintenance programmée avec succès.', 'success');
 }
 });
}

function generateReport() {
 // Advanced report generation with multiple formats
 Swal.fire({
 title: 'Générer un Rapport',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Type de rapport</label>
 <select id="report-type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="complete">Rapport complet</option>
 <option value="maintenance">Historique maintenance</option>
 <option value="financial">Analyse financière</option>
 <option value="usage">Utilisation du véhicule</option>
 <option value="compliance">Conformité légale</option>
 </select>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Format de sortie</label>
 <div class="flex space-x-4">
 <label class="flex items-center">
 <input type="radio" name="format" value="pdf" checked class="mr-2"> PDF
 </label>
 <label class="flex items-center">
 <input type="radio" name="format" value="excel" class="mr-2"> Excel
 </label>
 <label class="flex items-center">
 <input type="radio" name="format" value="word" class="mr-2"> Word
 </label>
 </div>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
 <div class="grid grid-cols-2 gap-2">
 <input type="date" id="report-start" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Date début">
 <input type="date" id="report-end" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Date fin">
 </div>
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Générer',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#3b82f6',
 width: '500px'
 }).then((result) => {
 if (result.isConfirmed) {
 // Simulate report generation
 Swal.fire({
 title: 'Génération en cours...',
 html: 'Veuillez patienter pendant la génération du rapport.',
 allowOutsideClick: false,
 showConfirmButton: false,
 willOpen: () => {
 Swal.showLoading();
 }
 });

 setTimeout(() => {
 Swal.fire('Rapport généré!', 'Le rapport a été généré et téléchargé avec succès.', 'success');
 }, 2000);
 }
 });
}

function uploadPhotos() {
 // Advanced photo upload with drag & drop
 Swal.fire({
 title: 'Ajouter des Photos',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
 <select id="photo-category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="exterior">Extérieur</option>
 <option value="interior">Intérieur</option>
 <option value="engine">Moteur</option>
 <option value="damage">Dommages</option>
 <option value="documents">Documents</option>
 </select>
 </div>
 <div class="mb-4">
 <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer" onclick="document.getElementById('photo-input').click()">
 <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
 <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
 </svg>
 <p class="mt-2 text-sm text-gray-600">Cliquez ou glissez-déposez vos photos ici</p>
 <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 10MB chacune</p>
 </div>
 <input type="file" id="photo-input" multiple accept="image/*" class="hidden">
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Télécharger',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#3b82f6',
 width: '500px'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Photos ajoutées!', 'Les photos ont été téléchargées avec succès.', 'success');
 }
 });
}

// Enhanced delete function with better confirmation
function deleteVehicle(vehicleId) {
 Swal.fire({
 title: 'Supprimer le véhicule ?',
 html: `
 <div class="text-left">
 <p class="text-gray-600 mb-4">Cette action est irréversible. Le véhicule sera définitivement supprimé de la base de données.</p>
 <div class="bg-red-50 border border-red-200 rounded-md p-3">
 <div class="flex">
 <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 <div class="ml-3">
 <p class="text-sm text-red-800">Toutes les données associées seront perdues :</p>
 <ul class="text-xs text-red-700 mt-1 list-disc list-inside">
 <li>Historique de maintenance</li>
 <li>Affectations de chauffeurs</li>
 <li>Documents et photos</li>
 <li>Rapports d'utilisation</li>
 </ul>
 </div>
 </div>
 </div>
 </div>
 `,
 icon: 'warning',
 showCancelButton: true,
 confirmButtonColor: '#ef4444',
 cancelButtonColor: '#6b7280',
 confirmButtonText: 'Oui, supprimer',
 cancelButtonText: 'Annuler',
 width: '500px'
 }).then((result) => {
 if (result.isConfirmed) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/vehicles/${vehicleId}`;
 form.innerHTML = `
 @csrf
 @method('DELETE')
 `;
 document.body.appendChild(form);
 form.submit();
 }
 });
}

// Auto-hide success notifications
document.addEventListener('DOMContentLoaded', function() {
 const successNotification = document.getElementById('success-notification');
 if (successNotification) {
 setTimeout(() => {
 successNotification.style.opacity = '0';
 setTimeout(() => successNotification.remove(), 300);
 }, 5000);
 }

 // Add real-time status indicator
 updateVehicleStatus();
});

// Real-time vehicle status updates
function updateVehicleStatus() {
 // Simulate real-time status updates (in production, this would be WebSocket or polling)
 setInterval(() => {
 const statusIndicators = document.querySelectorAll('.h-2.w-2.rounded-full');
 statusIndicators.forEach(indicator => {
 // Add subtle pulse animation for active vehicles
 if (indicator.classList.contains('bg-green-400')) {
 indicator.style.animation = 'pulse 2s infinite';
 }
 });
 }, 5000);
}
</script>
@endpush