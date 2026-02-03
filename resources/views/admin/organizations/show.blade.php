@extends('layouts.admin.catalyst')
@section('title', $organization->name . ' - ZenFleet')

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

.hero-section {
 background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
 border: 1px solid #bae6fd;
 transition: all 0.3s ease;
}

.hero-section:hover {
 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
 border-color: #7dd3fc;
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
 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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
 padding: 0.5rem 1rem;
 border-radius: 9999px;
 font-size: 0.875rem;
 font-weight: 600;
 text-transform: uppercase;
 letter-spacing: 0.05em;
}

.status-active {
 background: linear-gradient(135deg, #10b981 0%, #059669 100%);
 color: white;
}

.status-inactive {
 background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
 color: white;
}

.status-suspended {
 background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
 color: white;
}

.progress-ring {
 width: 120px;
 height: 120px;
 transform: rotate(-90deg);
}

.progress-ring-circle {
 fill: none;
 stroke-width: 8;
 stroke-linecap: round;
}

.progress-ring-bg {
 stroke: #e5e7eb;
}

.progress-ring-fill {
 stroke-dasharray: 283;
 stroke-dashoffset: 283;
 transition: stroke-dashoffset 1s ease-in-out;
}

.grid-stats {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
 gap: 1.5rem;
}

.activity-timeline {
 position: relative;
}

.timeline-item {
 position: relative;
 padding-left: 3rem;
 padding-bottom: 1.5rem;
}

.timeline-item::before {
 content: '';
 position: absolute;
 left: 1rem;
 top: 0;
 bottom: 0;
 width: 2px;
 background: #e2e8f0;
}

.timeline-item:last-child::before {
 bottom: 1.5rem;
}

.timeline-dot {
 position: absolute;
 left: 0.5rem;
 top: 0.5rem;
 width: 2rem;
 height: 2rem;
 border-radius: 50%;
 background: white;
 border: 3px solid #3b82f6;
 display: flex;
 align-items: center;
 justify-content: center;
}

.chart-container {
 position: relative;
 height: 300px;
}

.performance-indicator {
 display: flex;
 align-items: center;
 justify-content: space-between;
 padding: 1rem;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
 border-radius: 0.75rem;
 border: 1px solid #e2e8f0;
 margin-bottom: 1rem;
}

.org-avatar {
 width: 5rem;
 height: 5rem;
 border-radius: 1rem;
 background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
 display: flex;
 align-items: center;
 justify-content: center;
 color: white;
 font-size: 1.5rem;
 font-weight: bold;
 box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.enterprise-hover:hover {
 transform: scale(1.02);
 transition: all 0.2s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
 .grid-stats {
 grid-template-columns: 1fr;
 }

 .hero-section .flex {
 flex-direction: column;
 text-align: center;
 gap: 1rem;
 }
}
</style>
@endpush

@section('content')
<div class="fade-in">
 {{-- Navigation breadcrumb --}}
 <div class="mb-8">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.organizations.index') }}" class="hover:text-blue-600 transition-colors enterprise-hover inline-flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
 </svg>
 Organisations
 </a>
 <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
 </svg>
 <span class="text-blue-600 font-semibold">{{ $organization->name }}</span>
 </nav>

 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate">{{ $organization->name }}</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
 </svg>
 {{ $organization->organization_type ?? 'Type non défini' }}
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
 </svg>
 {{ $organization->city }}, {{ $organization->wilaya }}
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 @can('organizations.update')
 <a href="{{ route('admin.organizations.edit', $organization) }}" class="btn-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
 </svg>
 Modifier
 </a>
 @endcan

 <a href="{{ route('admin.organizations.index') }}" class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 Retour
 </a>
 </div>
 </div>
 </div>

 {{-- Hero section de l'organisation --}}
 <div class="hero-section rounded-lg p-6 mb-8">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-6">
 <div class="flex-shrink-0">
 @if($organization->logo_path && Storage::disk('public')->exists($organization->logo_path))
 <img src="{{ Storage::disk('public')->url($organization->logo_path) }}"
 alt="{{ $organization->name }}"
 class="h-20 w-20 rounded-lg object-cover shadow-lg ring-2 ring-blue-500/20">
 @else
 <div class="org-avatar">
 {{ strtoupper(substr($organization->name, 0, 2)) }}
 </div>
 @endif
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h2>
 @if($organization->legal_name && $organization->legal_name !== $organization->name)
 <p class="text-lg text-gray-600">{{ $organization->legal_name }}</p>
 @endif
 @if($organization->description)
 <p class="text-gray-600 mt-1">{{ Str::limit($organization->description, 100) }}</p>
 @endif
 <div class="mt-3">
 @php
 $statusClass = match($organization->status) {
 'active' => 'status-active',
 'inactive' => 'status-inactive',
 'suspended' => 'status-suspended',
 default => 'status-inactive'
 };
 @endphp
 <span class="status-badge {{ $statusClass }}">
 <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <circle cx="10" cy="10" r="10"></circle>
 </svg>
 {{ ucfirst($organization->status) }}
 </span>
 </div>
 </div>
 </div>
 <div class="text-right">
 <div class="text-3xl font-bold text-gray-900">{{ $stats['users']['total'] }}</div>
 <div class="text-sm text-gray-500">utilisateurs</div>
 <div class="text-sm text-gray-500 mt-1">Créée le {{ $organization->created_at->format('d/m/Y') }}</div>
 </div>
 </div>
 </div>

 {{-- Statistiques principales --}}
 <div class="grid-stats mb-8" id="stats-grid">
 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center justify-between">
 <div>
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Utilisateurs</p>
 <p class="text-3xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
 <p class="text-sm text-green-600">{{ $stats['users']['active'] }} actifs</p>
 </div>
 </div>
 </div>
 <div class="flex-shrink-0">
 <div class="relative w-16 h-16">
 <svg class="w-16 h-16 transform -rotate-90">
 <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"/>
 <circle cx="32" cy="32" r="28" stroke="#3b82f6" stroke-width="4" fill="none"
 stroke-dasharray="175.92"
 stroke-dashoffset="{{ 175.92 - (175.92 * ($stats['users']['active'] / max($stats['users']['total'], 1))) }}"
 stroke-linecap="round"/>
 </svg>
 <div class="absolute inset-0 flex items-center justify-center">
 <span class="text-xs font-bold text-blue-600">
 {{ $stats['users']['total'] > 0 ? round(($stats['users']['active'] / $stats['users']['total']) * 100) : 0 }}%
 </span>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center justify-between">
 <div>
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Véhicules</p>
 <p class="text-3xl font-bold text-gray-900">{{ $stats['vehicles']['total'] }}</p>
 <p class="text-sm text-green-600">{{ $stats['vehicles']['available'] }} disponibles</p>
 </div>
 </div>
 </div>
 <div class="flex-shrink-0">
 <div class="relative w-16 h-16">
 <svg class="w-16 h-16 transform -rotate-90">
 <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"/>
 <circle cx="32" cy="32" r="28" stroke="#10b981" stroke-width="4" fill="none"
 stroke-dasharray="175.92"
 stroke-dashoffset="{{ 175.92 - (175.92 * ($stats['vehicles']['available'] / max($stats['vehicles']['total'], 1))) }}"
 stroke-linecap="round"/>
 </svg>
 <div class="absolute inset-0 flex items-center justify-center">
 <span class="text-xs font-bold text-green-600">
 {{ $stats['vehicles']['total'] > 0 ? round(($stats['vehicles']['available'] / $stats['vehicles']['total']) * 100) : 0 }}%
 </span>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center justify-between">
 <div>
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Chauffeurs</p>
 <p class="text-3xl font-bold text-gray-900">{{ $stats['drivers']['total'] }}</p>
 <p class="text-sm text-green-600">{{ $stats['drivers']['active'] }} actifs</p>
 </div>
 </div>
 </div>
 <div class="flex-shrink-0">
 <div class="relative w-16 h-16">
 <svg class="w-16 h-16 transform -rotate-90">
 <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"/>
 <circle cx="32" cy="32" r="28" stroke="#f97316" stroke-width="4" fill="none"
 stroke-dasharray="175.92"
 stroke-dashoffset="{{ 175.92 - (175.92 * ($stats['drivers']['active'] / max($stats['drivers']['total'], 1))) }}"
 stroke-linecap="round"/>
 </svg>
 <div class="absolute inset-0 flex items-center justify-center">
 <span class="text-xs font-bold text-orange-600">
 {{ $stats['drivers']['total'] > 0 ? round(($stats['drivers']['active'] / $stats['drivers']['total']) * 100) : 0 }}%
 </span>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-lg p-6 enterprise-hover">
 <div class="flex items-center justify-between">
 <div>
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Affectations</p>
 <p class="text-3xl font-bold text-gray-900">{{ $stats['assignments']['active'] }}</p>
 <p class="text-sm text-gray-600">en cours</p>
 </div>
 </div>
 </div>
 <div class="flex-shrink-0">
 <div class="relative w-16 h-16">
 <svg class="w-16 h-16 transform -rotate-90">
 <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"/>
 <circle cx="32" cy="32" r="28" stroke="#8b5cf6" stroke-width="4" fill="none"
 stroke-dasharray="175.92"
 stroke-dashoffset="{{ 175.92 - (175.92 * ($stats['assignments']['active'] / max($stats['vehicles']['total'], 1))) }}"
 stroke-linecap="round"/>
 </svg>
 <div class="absolute inset-0 flex items-center justify-center">
 <span class="text-xs font-bold text-purple-600">
 {{ $stats['vehicles']['total'] > 0 ? round(($stats['assignments']['active'] / $stats['vehicles']['total']) * 100) : 0 }}%
 </span>
 </div>
 </div>
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

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div class="space-y-4">
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Raison sociale</p>
 <p class="text-base text-gray-900">{{ $organization->legal_name ?? $organization->name }}</p>
 </div>
 </div>

 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
 <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Email principal</p>
 <p class="text-base text-gray-900">{{ $organization->email }}</p>
 </div>
 </div>

 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Téléphone</p>
 <p class="text-base text-gray-900">{{ $organization->phone_number }}</p>
 </div>
 </div>
 </div>

 <div class="space-y-4">
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Adresse</p>
 <p class="text-base text-gray-900">{{ $organization->address }}</p>
 <p class="text-sm text-gray-600">{{ $organization->city }}, {{ $organization->wilaya }}</p>
 </div>
 </div>

 @if($organization->website)
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.559-.499-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.559.499.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.497-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.148.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Site web</p>
 <a href="{{ $organization->website }}" target="_blank"
 class="text-base text-blue-600 hover:text-blue-500">
 {{ $organization->website }}
 <svg class="w-3 h-3 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
 <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
 <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
 </svg>
 </a>
 </div>
 </div>
 @endif

 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
 <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 1a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Secteur d'activité</p>
 <p class="text-base text-gray-900">{{ $organization->industry ?? 'Non spécifié' }}</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Informations légales --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-green-500 to-green-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 100-2H7a1 1 0 100 2h6zm-6 4a1 1 0 100-2h6a1 1 0 100 2H7z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Informations légales</h3>
 </div>

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div class="space-y-4">
 <div class="performance-indicator">
 <div class="flex items-center">
 <div class="flex-shrink-0 mr-3">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
 </svg>
 </div>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Registre de Commerce</p>
 <p class="text-base font-semibold text-gray-900">{{ $organization->trade_register }}</p>
 </div>
 </div>
 </div>

 <div class="performance-indicator">
 <div class="flex items-center">
 <div class="flex-shrink-0 mr-3">
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
 </svg>
 </div>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">NIF</p>
 <p class="text-base font-semibold text-gray-900">{{ $organization->nif }}</p>
 </div>
 </div>
 </div>
 </div>

 <div class="space-y-4">
 @if($organization->ai)
 <div class="performance-indicator">
 <div class="flex items-center">
 <div class="flex-shrink-0 mr-3">
 <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">AI</p>
 <p class="text-base font-semibold text-gray-900">{{ $organization->ai }}</p>
 </div>
 </div>
 </div>
 @endif

 @if($organization->nis)
 <div class="performance-indicator">
 <div class="flex items-center">
 <div class="flex-shrink-0 mr-3">
 <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 001.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">NIS</p>
 <p class="text-base font-semibold text-gray-900">{{ $organization->nis }}</p>
 </div>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>

 {{-- Représentant légal --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-amber-500 to-amber-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
 </svg>
 </div>
 <h3 class="text-lg font-medium text-gray-900">Représentant légal</h3>
 </div>

 <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
 <div class="space-y-4">
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Nom complet</p>
 <p class="text-base font-semibold text-gray-900">
 {{ $organization->manager_first_name }} {{ $organization->manager_last_name }}
 </p>
 </div>
 </div>

 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
 <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">NIN</p>
 <p class="text-base font-mono text-gray-900">{{ $organization->manager_nin }}</p>
 </div>
 </div>
 </div>

 <div class="space-y-4">
 @if($organization->manager_phone_number)
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Téléphone</p>
 <p class="text-base text-gray-900">{{ $organization->manager_phone_number }}</p>
 </div>
 </div>
 @endif

 @if($organization->manager_dob)
 <div class="flex items-start">
 <div class="flex-shrink-0 mr-3">
 <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-500">Date de naissance</p>
 <p class="text-base text-gray-900">{{ $organization->manager_dob->format('d/m/Y') }}</p>
 </div>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>

 {{-- Sidebar --}}
 <div class="space-y-6">
 {{-- Métriques de performance --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Performance</h4>
 </div>

 <div class="space-y-4">
 <div class="performance-indicator">
 <div class="flex justify-content-between align-items-center">
 <span class="text-sm font-medium text-gray-600">Efficacité globale</span>
 <span class="text-sm font-bold text-green-600">{{ $performanceData['efficiency_score'] }}%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
 <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-1000"
 style="width: {{ $performanceData['efficiency_score'] }}%"></div>
 </div>
 </div>

 <div class="performance-indicator">
 <div class="flex justify-content-between align-items-center">
 <span class="text-sm font-medium text-gray-600">Taux d'utilisation</span>
 <span class="text-sm font-bold text-blue-600">{{ $performanceData['utilization_rate'] }}%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
 <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-1000"
 style="width: {{ $performanceData['utilization_rate'] }}%"></div>
 </div>
 </div>

 <div class="performance-indicator">
 <div class="flex justify-content-between align-items-center">
 <span class="text-sm font-medium text-gray-600">Conformité maintenance</span>
 <span class="text-sm font-bold text-orange-600">{{ $performanceData['maintenance_compliance'] }}%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
 <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-1000"
 style="width: {{ $performanceData['maintenance_compliance'] }}%"></div>
 </div>
 </div>

 <div class="performance-indicator">
 <div class="flex justify-content-between align-items-center">
 <span class="text-sm font-medium text-gray-600">Satisfaction chauffeurs</span>
 <span class="text-sm font-bold text-purple-600">{{ $performanceData['driver_satisfaction'] }}%</span>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
 <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-1000"
 style="width: {{ $performanceData['driver_satisfaction'] }}%"></div>
 </div>
 </div>
 </div>
 </div>

 {{-- Activité récente --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-blue-500 to-blue-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Activité récente</h4>
 </div>

 <div class="activity-timeline">
 @foreach($recentActivity as $activity)
 <div class="timeline-item">
 <div class="timeline-dot">
 <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <circle cx="10" cy="10" r="3"></circle>
 </svg>
 </div>
 <div>
 <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
 <p class="text-xs text-gray-500">{{ $activity['date']->diffForHumans() }}</p>
 </div>
 </div>
 @endforeach
 </div>
 </div>

 {{-- Actions rapides --}}
 <div class="info-section rounded-lg p-6">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-gray-500 to-gray-600">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
 </svg>
 </div>
 <h4 class="text-lg font-medium text-gray-900">Actions rapides</h4>
 </div>

 <div class="space-y-3">
 <button onclick="generateReport()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
 </svg>
 Générer rapport
 </button>

 <button onclick="exportData()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 </svg>
 Exporter données
 </button>

 <button onclick="sendNotification()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
 </svg>
 Envoyer notification
 </button>

 <button onclick="scheduleAudit()" class="w-full btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-md hover:shadow-md transition-all">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
 <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 1a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
 </svg>
 Programmer audit
 </button>
 </div>
 </div>

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
 <span>ID Organisation:</span>
 <span class="font-mono">{{ $organization->id }}</span>
 </div>
 <div class="flex justify-between">
 <span>UUID:</span>
 <span class="font-mono text-xs">{{ Str::limit($organization->uuid, 8) }}...</span>
 </div>
 <div class="flex justify-between">
 <span>Créée le:</span>
 <span>{{ $organization->created_at->format('d/m/Y H:i') }}</span>
 </div>
 <div class="flex justify-between">
 <span>Modifiée le:</span>
 <span>{{ $organization->updated_at->format('d/m/Y H:i') }}</span>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Enterprise-grade functionality
function generateReport() {
 Swal.fire({
 title: 'Générer un Rapport',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Type de rapport</label>
 <select id="report-type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option value="complete">Rapport complet</option>
 <option value="financial">Analyse financière</option>
 <option value="performance">Performance</option>
 <option value="compliance">Conformité</option>
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
 Swal.fire('Rapport généré!', 'Le rapport a été généré avec succès.', 'success');
 }
 });
}

function exportData() {
 Swal.fire({
 title: 'Exporter les Données',
 text: 'Voulez-vous exporter toutes les données de cette organisation ?',
 icon: 'question',
 showCancelButton: true,
 confirmButtonText: 'Exporter',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#10b981'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Export en cours...', 'Les données sont en cours d\'exportation.', 'info');
 }
 });
}

function sendNotification() {
 Swal.fire({
 title: 'Envoyer une Notification',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Destinataires</label>
 <select id="recipients" class="w-full px-3 py-2 border border-gray-300 rounded-md">
 <option value="all">Tous les utilisateurs</option>
 <option value="admins">Administrateurs seulement</option>
 <option value="drivers">Chauffeurs seulement</option>
 </select>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
 <textarea id="message" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="4" placeholder="Votre message..."></textarea>
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Envoyer',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#3b82f6'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Notification envoyée!', 'La notification a été envoyée avec succès.', 'success');
 }
 });
}

function scheduleAudit() {
 Swal.fire({
 title: 'Programmer un Audit',
 html: `
 <div class="text-left">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Type d'audit</label>
 <select id="audit-type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
 <option value="compliance">Conformité réglementaire</option>
 <option value="financial">Audit financier</option>
 <option value="security">Sécurité</option>
 <option value="performance">Performance</option>
 </select>
 </div>
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">Date prévue</label>
 <input type="date" id="audit-date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
 </div>
 </div>
 `,
 showCancelButton: true,
 confirmButtonText: 'Programmer',
 cancelButtonText: 'Annuler',
 confirmButtonColor: '#8b5cf6'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Audit programmé!', 'L\'audit a été programmé avec succès.', 'success');
 }
 });
}

// Auto-hide success notifications
document.addEventListener('DOMContentLoaded', function() {
 // Animation des métriques au chargement
 const metricCards = document.querySelectorAll('.metric-card');
 metricCards.forEach((card, index) => {
 card.style.opacity = '0';
 card.style.transform = 'translateY(20px)';

 setTimeout(() => {
 card.style.transition = 'all 0.5s ease';
 card.style.opacity = '1';
 card.style.transform = 'translateY(0)';
 }, index * 100);
 });

 // Animation des barres de progression
 const progressBars = document.querySelectorAll('.w-full.bg-gray-200 div');
 progressBars.forEach((bar, index) => {
 const width = bar.style.width;
 bar.style.width = '0%';

 setTimeout(() => {
 bar.style.width = width;
 }, 500 + (index * 200));
 });

 // Animation des cercles de progression
 const progressCircles = document.querySelectorAll('circle[stroke="#3b82f6"], circle[stroke="#10b981"], circle[stroke="#f97316"], circle[stroke="#8b5cf6"]');
 progressCircles.forEach((circle, index) => {
 const originalOffset = circle.style.strokeDashoffset || circle.getAttribute('stroke-dashoffset');
 circle.style.strokeDashoffset = '175.92';

 setTimeout(() => {
 circle.style.transition = 'stroke-dashoffset 1s ease-in-out';
 circle.style.strokeDashoffset = originalOffset;
 }, 800 + (index * 300));
 });
});
</script>
@endpush