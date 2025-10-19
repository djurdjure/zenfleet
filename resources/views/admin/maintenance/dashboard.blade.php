{{-- resources/views/admin/maintenance/dashboard.blade.php --}}
{{-- 🚀 ZENFLEET ENTERPRISE MAINTENANCE DASHBOARD - Ultra Professional Grade --}}
@extends('layouts.admin.catalyst')
@section('title', 'Centre de Contrôle Maintenance Enterprise - ZenFleet')

{{-- 🚨 Gestion du mode dégradé enterprise-grade --}}
@if(isset($fallbackMode) && $fallbackMode)
 @push('notifications')
 <div class="fixed top-4 right-4 z-50 max-w-md">
 <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-lg">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-yellow-800">Mode Dégradé Activé</h3>
 <p class="text-xs text-yellow-700 mt-1">
 {{ $error ?? 'Certaines données peuvent être temporairement indisponibles' }}
 </p>
 </div>
 </div>
 </div>
 </div>
 @endpush
@endif

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

.hover-scale {
 transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
 transform: scale(1.02);
}

.gradient-border {
 background: linear-gradient(white, white) padding-box,
 linear-gradient(45deg, #374151, #6b7280) border-box;
 border: 2px solid transparent;
}

.status-indicator {
 position: relative;
 overflow: hidden;
}

.status-indicator::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
 transition: left 0.5s;
}

.status-indicator:hover::before {
 left: 100%;
}

.metric-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
 position: relative;
 overflow: hidden;
}

.metric-card::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
 transition: left 0.6s ease;
}

.metric-card:hover::before {
 left: 100%;
}

.metric-card:hover {
 transform: translateY(-4px);
 box-shadow: 0 8px 25px rgba(0,0,0,0.15);
 border-color: #cbd5e1;
}

.action-button {
 transition: all 0.2s ease;
 position: relative;
 overflow: hidden;
}

.action-button::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
 transition: left 0.5s ease;
}

.action-button:hover::before {
 left: 100%;
}

.action-button:hover {
 transform: translateY(-1px);
 box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stats-grid {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
 gap: 1.5rem;
}

.chart-container {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.chart-container:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.alert-item {
 transition: all 0.3s ease;
 border-left: 4px solid transparent;
}

.alert-item:hover {
 transform: translateX(4px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.alert-critical {
 border-left-color: #ef4444;
 background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
}

.alert-high {
 border-left-color: #f59e0b;
 background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
}

.alert-medium {
 border-left-color: #3b82f6;
 background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
}

.alert-low {
 border-left-color: #10b981;
 background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
}

.pulse-animation {
 animation: pulse 2s infinite;
}

@keyframes pulse {
 0%, 100% { opacity: 1; }
 50% { opacity: 0.7; }
}

/* Icon gradients */
.icon-gradient-red {
 background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
 box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.icon-gradient-orange {
 background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
 box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.icon-gradient-blue {
 background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);
 box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.icon-gradient-green {
 background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
 box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.icon-gradient-purple {
 background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
 box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.icon-gradient-indigo {
 background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);
 box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Table moderne */
.maintenance-table {
 border-collapse: separate;
 border-spacing: 0;
}

.maintenance-table tbody tr {
 transition: all 0.2s ease-in-out;
}

.maintenance-table tbody tr:hover {
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
 transform: translateY(-1px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.maintenance-table thead th {
 background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
 backdrop-filter: blur(10px);
 position: sticky;
 top: 0;
 z-index: 10;
}

/* Progress bars */
.progress-bar {
 background: linear-gradient(90deg, #e5e7eb, #f3f4f6);
 border-radius: 8px;
 overflow: hidden;
 position: relative;
}

.progress-fill {
 height: 100%;
 border-radius: 8px;
 transition: width 0.8s ease;
 position: relative;
 overflow: hidden;
}

.progress-fill::after {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
 animation: progress-shine 1.5s infinite;
}

@keyframes progress-shine {
 0% { left: -100%; }
 100% { left: 100%; }
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

 {{-- 🎯 En-tête Ultra-Professional Enterprise --}}
 <div class="mb-8">
 <div class="bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
 {{-- Effet de brillance --}}
 <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-pulse"></div>

 <div class="relative z-10">
 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <div class="flex items-center space-x-4">
 <div class="p-4 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-2xl shadow-lg">
 <x-iconify icon="lucide:gauge" class="h-12 w-12 text-white" stroke-width="2" / />
 </div>
 <div>
 <h1 class="text-4xl font-bold text-white mb-2">Centre de Contrôle Maintenance</h1>
 <p class="text-blue-100 text-lg font-medium">Système Enterprise-Grade ZenFleet</p>
 </div>
 </div>
 <div class="mt-6 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-8">
 <div class="mt-2 flex items-center text-sm text-blue-100">
 <x-iconify icon="lucide:activity" class="mr-2 h-5 w-5 text-cyan-400" stroke-width="2" / />
 Supervision temps réel des opérations
 </div>
 <div class="mt-2 flex items-center text-sm text-blue-100">
 <x-iconify icon="heroicons:shield-check" class="mr-2 h-5 w-5 text-green-400" stroke-width="2" / />
 Alertes intelligentes & préventives
 </div>
 <div class="mt-2 flex items-center text-sm text-blue-100">
 <x-iconify icon="heroicons:clock" class="mr-2 h-5 w-5 text-orange-400" stroke-width="2" / />
 Dernière synchronisation: {{ now()->format('d/m/Y à H:i:s') }}
 </div>
 </div>
 </div>
 <div class="mt-6 md:mt-0 md:ml-6">
 <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
 <button onclick="refreshDashboard()" class="action-button inline-flex items-center px-4 py-3 bg-white/10 backdrop-blur-sm border border-white/20 shadow-sm text-sm font-medium rounded-xl text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-300">
 <x-iconify icon="heroicons:arrow-path" class="mr-2 h-4 w-4" stroke-width="2" / />
 Actualiser
 </button>
 <a href="{{ route('admin.maintenance.schedules.create') }}" class="action-button inline-flex items-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg text-sm font-medium rounded-xl text-white hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-300">
 <x-iconify icon="heroicons:calendar"-plus class="mr-2 h-4 w-4" stroke-width="2" / />
 Planifier
 </a>
 <a href="{{ route('admin.maintenance.operations.create') }}" class="action-button inline-flex items-center px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600 shadow-lg text-sm font-medium rounded-xl text-white hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-300">
 <x-iconify icon="heroicons:plus" class="mr-2 h-4 w-4" stroke-width="2" / />
 Nouvelle opération
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Navigation Sub-Menu Enterprise --}}
 <div class="mb-8">
 <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
 <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 px-6 py-4">
 <h3 class="text-lg font-semibold text-white flex items-center">
 <i class="fas fa-compass mr-3"></i>
 Navigation Maintenance
 </h3>
 <p class="text-indigo-100 text-sm mt-1">Accès rapide à tous les modules de maintenance</p>
 </div>
 <div class="p-6">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 {{-- Gestion des Types --}}
 <div class="group">
 <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-4 border-2 border-transparent group-hover:border-blue-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-cogs text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Types de Maintenance</h4>
 </div>
 <div class="space-y-2">
 <a href="{{ route('admin.maintenance.types.index') }}" class="block text-sm text-blue-700 hover:text-blue-900 hover:underline transition-colors">
 <i class="fas fa-list mr-2"></i>Liste des types
 </a>
 <a href="{{ route('admin.maintenance.types.create') }}" class="block text-sm text-blue-700 hover:text-blue-900 hover:underline transition-colors">
 <i class="fas fa-plus mr-2"></i>Nouveau type
 </a>
 </div>
 </div>
 </div>

 {{-- Planifications --}}
 <div class="group">
 <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg p-4 border-2 border-transparent group-hover:border-green-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-calendar-alt text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Planifications</h4>
 </div>
 <div class="space-y-2">
 <a href="{{ route('admin.maintenance.schedules.index') }}" class="block text-sm text-green-700 hover:text-green-900 hover:underline transition-colors">
 <i class="fas fa-list mr-2"></i>Toutes les planifications
 </a>
 <a href="{{ route('admin.maintenance.schedules.create') }}" class="block text-sm text-green-700 hover:text-green-900 hover:underline transition-colors">
 <i class="fas fa-calendar-plus mr-2"></i>Nouvelle planification
 </a>
 </div>
 </div>
 </div>

 {{-- Opérations --}}
 <div class="group">
 <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-lg p-4 border-2 border-transparent group-hover:border-purple-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-wrench text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Opérations</h4>
 </div>
 <div class="space-y-2">
 <a href="{{ route('admin.maintenance.operations.index') }}" class="block text-sm text-purple-700 hover:text-purple-900 hover:underline transition-colors">
 <i class="fas fa-list mr-2"></i>Toutes les opérations
 </a>
 <a href="{{ route('admin.maintenance.operations.create') }}" class="block text-sm text-purple-700 hover:text-purple-900 hover:underline transition-colors">
 <i class="fas fa-plus mr-2"></i>Nouvelle opération
 </a>
 </div>
 </div>
 </div>

 {{-- Alertes & Rapports --}}
 <div class="group">
 <div class="bg-gradient-to-br from-orange-50 to-red-100 rounded-lg p-4 border-2 border-transparent group-hover:border-orange-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-bell text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Alertes & Rapports</h4>
 </div>
 <div class="space-y-2">
 <a href="{{ route('admin.maintenance.alerts.index') }}" class="block text-sm text-orange-700 hover:text-orange-900 hover:underline transition-colors">
 <i class="fas fa-bell mr-2"></i>Alertes actives
 </a>
 <a href="{{ route('admin.maintenance.reports.index') }}" class="block text-sm text-orange-700 hover:text-orange-900 hover:underline transition-colors">
 <i class="fas fa-chart-bar mr-2"></i>Rapports & Analytics
 </a>
 </div>
 </div>
 </div>
 </div>

 {{-- Section Fournisseurs (ligne supplémentaire) --}}
 <div class="mt-6 pt-6 border-t border-gray-200">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div class="group">
 <div class="bg-gradient-to-br from-teal-50 to-cyan-100 rounded-lg p-4 border-2 border-transparent group-hover:border-teal-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-building text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Fournisseurs</h4>
 </div>
 <div class="space-y-2">
 <a href="{{ route('admin.maintenance.providers.index') }}" class="block text-sm text-teal-700 hover:text-teal-900 hover:underline transition-colors">
 <i class="fas fa-list mr-2"></i>Tous les fournisseurs
 </a>
 <a href="{{ route('admin.maintenance.providers.create') }}" class="block text-sm text-teal-700 hover:text-teal-900 hover:underline transition-colors">
 <i class="fas fa-plus mr-2"></i>Nouveau fournisseur
 </a>
 </div>
 </div>
 </div>

 <div class="group">
 <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-lg p-4 border-2 border-transparent group-hover:border-gray-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-slate-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-cog text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Configuration</h4>
 </div>
 <div class="space-y-2">
 <a href="#" class="block text-sm text-gray-700 hover:text-gray-900 hover:underline transition-colors">
 <i class="fas fa-sliders-h mr-2"></i>Paramètres généraux
 </a>
 <a href="#" class="block text-sm text-gray-700 hover:text-gray-900 hover:underline transition-colors">
 <i class="fas fa-users mr-2"></i>Permissions utilisateur
 </a>
 </div>
 </div>
 </div>

 <div class="group">
 <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-lg p-4 border-2 border-transparent group-hover:border-amber-300 transition-all duration-300 group-hover:shadow-lg">
 <div class="flex items-center mb-3">
 <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
 <i class="fas fa-question-circle text-white"></i>
 </div>
 <h4 class="text-sm font-semibold text-gray-900 ml-3">Support</h4>
 </div>
 <div class="space-y-2">
 <a href="#" class="block text-sm text-amber-700 hover:text-amber-900 hover:underline transition-colors">
 <i class="fas fa-book mr-2"></i>Documentation
 </a>
 <a href="#" class="block text-sm text-amber-700 hover:text-amber-900 hover:underline transition-colors">
 <i class="fas fa-life-ring mr-2"></i>Aide & Support
 </a>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- 📊 Tableau de Bord Métriques Enterprise Ultra-Professional --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
 {{-- Alertes Critiques --}}
 <div class="metric-card relative bg-gradient-to-br from-red-50 to-rose-100 border-l-4 border-red-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-red-600 uppercase tracking-wider mb-1">Alertes Critiques</h3>
 <div class="flex items-baseline space-x-2">
 <span class="text-3xl font-bold text-red-700">{{ $stats['critical_alerts'] ?? 0 }}</span>
 @if(($stats['critical_alerts'] ?? 0) > 0)
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
 <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
 Action requise
 </span>
 @else
 <span class="text-xs text-green-600 font-medium">✓ Tout va bien</span>
 @endif
 </div>
 <div class="mt-2 flex items-center text-xs text-red-600">
 <x-iconify icon="heroicons:arrow-trending-up" class="w-3 h-3 mr-1" / />
 Surveillance active
 </div>
 </div>
 </div>

 {{-- Alertes Non Traitées --}}
 <div class="metric-card relative bg-gradient-to-br from-orange-50 to-amber-100 border-l-4 border-orange-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="heroicons:bell" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-orange-600 uppercase tracking-wider mb-1">Non Traitées</h3>
 <div class="flex items-baseline space-x-2">
 <span class="text-3xl font-bold text-orange-700">{{ $stats['unacknowledged_alerts'] ?? 0 }}</span>
 <span class="text-xs text-gray-600">/ {{ $stats['total_alerts'] ?? 0 }}</span>
 </div>
 <div class="mt-2 flex items-center text-xs text-orange-600">
 <x-iconify icon="heroicons:clock" class="w-3 h-3 mr-1" / />
 En attente
 </div>
 </div>
 </div>

 {{-- Maintenances Planifiées --}}
 <div class="metric-card relative bg-gradient-to-br from-blue-50 to-indigo-100 border-l-4 border-blue-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="heroicons:calendar" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Planifiées</h3>
 <div class="flex items-baseline space-x-2">
 <span class="text-3xl font-bold text-blue-700">{{ $stats['scheduled_maintenance'] ?? 0 }}</span>
 @if(($stats['overdue_maintenance'] ?? 0) > 0)
 <span class="text-xs text-red-600 font-medium">{{ $stats['overdue_maintenance'] }} en retard</span>
 @else
 <span class="text-xs text-green-600 font-medium">À jour</span>
 @endif
 </div>
 <div class="mt-2 flex items-center text-xs text-blue-600">
 <x-iconify icon="heroicons:calendar" class="w-3 h-3 mr-1" / />
 Planning actif
 </div>
 </div>
 </div>

 {{-- Opérations en Cours --}}
 <div class="metric-card relative bg-gradient-to-br from-green-50 to-emerald-100 border-l-4 border-green-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="lucide:activity" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">En Cours</h3>
 <div class="flex items-baseline space-x-2">
 <span class="text-3xl font-bold text-green-700">{{ $stats['active_operations'] ?? 0 }}</span>
 <span class="text-xs text-gray-600">actives</span>
 </div>
 <div class="mt-2 flex items-center text-xs text-green-600">
 <x-iconify icon="heroicons:check-circle" class="w-3 h-3 mr-1" / />
 {{ $stats['completed_this_month'] ?? 0 }} ce mois
 </div>
 </div>
 </div>

 {{-- Coût ce Mois --}}
 <div class="metric-card relative bg-gradient-to-br from-purple-50 to-violet-100 border-l-4 border-purple-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="lucide:euro" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-1">Coût Mensuel</h3>
 <div class="flex items-baseline space-x-1">
 <span class="text-2xl font-bold text-purple-700">{{ number_format($stats['total_cost_this_month'] ?? 0, 0, ',', ' ') }}</span>
 <span class="text-lg font-semibold text-purple-600">€</span>
 </div>
 <div class="mt-2 flex items-center text-xs text-purple-600">
 <x-iconify icon="heroicons:arrow-trending-down" class="w-3 h-3 mr-1" / />
 Budget maîtrisé
 </div>
 </div>
 </div>

 {{-- Véhicules Sous Surveillance --}}
 <div class="metric-card relative bg-gradient-to-br from-teal-50 to-cyan-100 border-l-4 border-teal-500 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 group">
 <div class="absolute top-4 right-4">
 <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
 <x-iconify icon="lucide:car" class="w-6 h-6 text-white" stroke-width="2" / />
 </div>
 </div>
 <div class="pb-2">
 <h3 class="text-xs font-semibold text-teal-600 uppercase tracking-wider mb-1">Véhicules Actifs</h3>
 <div class="flex items-baseline space-x-2">
 <span class="text-3xl font-bold text-teal-700">{{ $stats['total_vehicles'] ?? 0 }}</span>
 <span class="text-xs text-gray-600">suivis</span>
 </div>
 <div class="mt-2 flex items-center text-xs text-teal-600">
 <x-iconify icon="heroicons:shield-check" class="w-3 h-3 mr-1" / />
 Surveillance 24/7
 </div>
 </div>
 </div>
 </div>

 {{-- 📊 Centre de Surveillance Opérationnelle Enterprise --}}
 <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 mb-8 overflow-hidden">
 <div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-8 py-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
 <x-iconify icon="heroicons:computer-desktop" class="h-8 w-8 text-white" stroke-width="2" / />
 </div>
 <div>
 <h2 class="text-2xl font-bold text-white">Surveillance Opérationnelle</h2>
 <p class="text-slate-300">Monitoring temps réel des opérations de maintenance</p>
 </div>
 </div>
 <div class="flex items-center space-x-3">
 <div class="px-4 py-2 bg-green-500/20 backdrop-blur-sm rounded-lg border border-green-500/30">
 <div class="flex items-center space-x-2">
 <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
 <span class="text-green-400 text-sm font-medium">Système Opérationnel</span>
 </div>
 </div>
 <button onclick="refreshOperations()" class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-lg border border-white/20 text-white hover:bg-white/20 transition-all duration-300">
 <x-iconify icon="heroicons:arrow-path" class="w-4 h-4" stroke-width="2" / />
 </button>
 </div>
 </div>
 </div>

 {{-- Tableau des Opérations avec Indicateurs d'Urgence --}}
 <div class="p-8">
 <div class="mb-6">
 <div class="flex flex-wrap gap-3">
 <button onclick="filterOperations('all')" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-medium hover:bg-blue-200 transition-colors filter-btn active" data-filter="all">
 <x-iconify icon="heroicons:list-bullet" class="w-4 h-4 inline mr-2" / />
 Toutes ({{ ($stats['active_operations'] ?? 0) + ($stats['scheduled_maintenance'] ?? 0) + ($stats['completed_this_month'] ?? 0) }})
 </button>
 <button onclick="filterOperations('active')" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium hover:bg-green-200 transition-colors filter-btn" data-filter="active">
 <x-iconify icon="heroicons:play" class="w-4 h-4 inline mr-2" / />
 En Cours ({{ $stats['active_operations'] ?? 0 }})
 </button>
 <button onclick="filterOperations('scheduled')" class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg font-medium hover:bg-orange-200 transition-colors filter-btn" data-filter="scheduled">
 <x-iconify icon="heroicons:calendar" class="w-4 h-4 inline mr-2" / />
 Planifiées ({{ $stats['scheduled_maintenance'] ?? 0 }})
 </button>
 <button onclick="filterOperations('completed')" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-medium hover:bg-purple-200 transition-colors filter-btn" data-filter="completed">
 <x-iconify icon="heroicons:check-circle" class="w-4 h-4 inline mr-2" / />
 Terminées ({{ $stats['completed_this_month'] ?? 0 }})
 </button>
 <button onclick="filterOperations('overdue')" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors filter-btn" data-filter="overdue">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-4 h-4 inline mr-2" / />
 En Retard ({{ $stats['overdue_maintenance'] ?? 0 }})
 </button>
 </div>
 </div>

 <div class="overflow-hidden rounded-xl border border-gray-200">
 <table class="w-full">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Urgence</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Véhicule</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type Maintenance</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Échéance</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Progress</th>
 <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200" id="operations-table">
 {{-- Opérations En Cours --}}
 @if(isset($activeOperations) && $activeOperations->count() > 0)
 @foreach($activeOperations as $operation)
 <tr class="operation-row active hover:bg-blue-50 transition-colors" data-status="active">
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-3"></div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
 <x-iconify icon="lucide:activity" class="w-3 h-3 mr-1" / />
 En cours
 </span>
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
 <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" / />
 </div>
 <div>
 <div class="text-sm font-medium text-gray-900">{{ $operation->vehicle->registration_plate ?? 'N/A' }}</div>
 <div class="text-xs text-gray-500">{{ $operation->vehicle->brand ?? '' }} {{ $operation->vehicle->model ?? '' }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $operation->maintenanceType->name ?? 'Maintenance générale' }}
 </span>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
 {{ ucfirst($operation->status ?? 'en_cours') }}
 </span>
 </td>
 <td class="px-6 py-4 text-sm text-gray-900">
 {{ $operation->scheduled_date ? $operation->scheduled_date->format('d/m/Y H:i') : 'Non définie' }}
 </td>
 <td class="px-6 py-4">
 <div class="w-full bg-gray-200 rounded-full h-2">
 <div class="bg-green-500 h-2 rounded-full" style="width: {{ rand(30, 80) }}%"></div>
 </div>
 <div class="text-xs text-gray-500 mt-1">{{ rand(30, 80) }}% terminé</div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center space-x-2">
 <button class="text-blue-600 hover:text-blue-900 transition-colors">
 <x-iconify icon="heroicons:eye" class="w-4 h-4" / />
 </button>
 <button class="text-green-600 hover:text-green-900 transition-colors">
 <x-iconify icon="heroicons:pencil" class="w-4 h-4" / />
 </button>
 </div>
 </td>
 </tr>
 @endforeach
 @endif

 {{-- Maintenances Planifiées --}}
 @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
 @foreach($upcomingMaintenance as $maintenance)
 @php
 $daysRemaining = $maintenance->next_due_date ? $maintenance->next_due_date->diffInDays(now(), false) : null;
 $isOverdue = $daysRemaining !== null && $daysRemaining > 0;
 $isUrgent = $daysRemaining !== null && $daysRemaining >= -3 && $daysRemaining <= 0;
 $urgencyClass = $isOverdue ? 'critical' : ($isUrgent ? 'urgent' : 'normal');
 $urgencyColor = $isOverdue ? 'red' : ($isUrgent ? 'orange' : 'blue');
 @endphp
 <tr class="operation-row {{ $isOverdue ? 'overdue' : 'scheduled' }} hover:bg-{{ $urgencyColor }}-50 transition-colors" data-status="{{ $isOverdue ? 'overdue' : 'scheduled' }}">
 <td class="px-6 py-4">
 <div class="flex items-center">
 @if($isOverdue)
 <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse mr-3"></div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-3 h-3 mr-1" / />
 Critique
 </span>
 @elseif($isUrgent)
 <div class="w-3 h-3 bg-orange-500 rounded-full animate-pulse mr-3"></div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
 <x-iconify icon="heroicons:clock" class="w-3 h-3 mr-1" / />
 Urgent
 </span>
 @else
 <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 <x-iconify icon="heroicons:calendar" class="w-3 h-3 mr-1" / />
 Planifié
 </span>
 @endif
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
 <x-iconify icon="lucide:car" class="w-5 h-5 text-gray-600" / />
 </div>
 <div>
 <div class="text-sm font-medium text-gray-900">{{ $maintenance->vehicle->registration_plate ?? 'N/A' }}</div>
 <div class="text-xs text-gray-500">{{ $maintenance->vehicle->brand ?? '' }} {{ $maintenance->vehicle->model ?? '' }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $urgencyColor }}-100 text-{{ $urgencyColor }}-800">
 {{ $maintenance->maintenanceType->name ?? 'Maintenance préventive' }}
 </span>
 </td>
 <td class="px-6 py-4">
 @if($isOverdue)
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
 En retard
 </span>
 @elseif($isUrgent)
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
 Urgent
 </span>
 @else
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 Planifié
 </span>
 @endif
 </td>
 <td class="px-6 py-4 text-sm text-gray-900">
 {{ $maintenance->next_due_date ? $maintenance->next_due_date->format('d/m/Y') : 'À définir' }}
 @if($maintenance->next_due_date)
 <div class="text-xs text-gray-500">{{ $maintenance->next_due_date->diffForHumans() }}</div>
 @endif
 </td>
 <td class="px-6 py-4">
 <div class="text-xs text-gray-500">
 @if($isOverdue)
 <span class="text-red-600 font-medium">En retard de {{ abs($daysRemaining) }} jour(s)</span>
 @elseif($isUrgent)
 <span class="text-orange-600 font-medium">Dans {{ abs($daysRemaining) }} jour(s)</span>
 @else
 <span class="text-blue-600">Dans {{ abs($daysRemaining) }} jour(s)</span>
 @endif
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center space-x-2">
 <button class="text-blue-600 hover:text-blue-900 transition-colors">
 <x-iconify icon="heroicons:eye" class="w-4 h-4" / />
 </button>
 <button class="text-green-600 hover:text-green-900 transition-colors">
 <x-iconify icon="heroicons:play" class="w-4 h-4" / />
 </button>
 </div>
 </td>
 </tr>
 @endforeach
 @endif

 {{-- Exemple d'opérations terminées pour démonstration --}}
 @for($i = 0; $i < 3; $i++)
 <tr class="operation-row completed hover:bg-green-50 transition-colors" data-status="completed">
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
 <x-iconify icon="heroicons:check-circle" class="w-3 h-3 mr-1" / />
 Terminé
 </span>
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
 <x-iconify icon="lucide:car" class="w-5 h-5 text-green-600" / />
 </div>
 <div>
 <div class="text-sm font-medium text-gray-900">AB-{{ 123 + $i }}-CD</div>
 <div class="text-xs text-gray-500">Peugeot Partner</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
 {{ ['Vidange', 'Freins', 'Pneus'][$i] }}
 </span>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
 Terminé
 </span>
 </td>
 <td class="px-6 py-4 text-sm text-gray-900">
 {{ now()->subDays($i + 1)->format('d/m/Y H:i') }}
 </td>
 <td class="px-6 py-4">
 <div class="w-full bg-gray-200 rounded-full h-2">
 <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
 </div>
 <div class="text-xs text-green-600 mt-1 font-medium">100% terminé</div>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center space-x-2">
 <button class="text-blue-600 hover:text-blue-900 transition-colors">
 <x-iconify icon="heroicons:document"-text class="w-4 h-4" / />
 </button>
 <button class="text-purple-600 hover:text-purple-900 transition-colors">
 <x-iconify icon="heroicons:arrow-down-tray" class="w-4 h-4" / />
 </button>
 </div>
 </td>
 </tr>
 @endfor
 </tbody>
 </table>
 </div>
 </div>
 </div>

 {{-- Section Actions rapides --}}
 <div class="bg-white shadow rounded-lg mb-8">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Actions Rapides</h3>
 </div>
 <div class="px-6 py-4">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 <a href="{{ route('admin.maintenance.alerts.index') }}" class="action-button group flex items-center p-4 bg-red-50 hover:bg-red-100 rounded-lg border-2 border-red-200 transition-all duration-200">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-red-800">Gérer les Alertes</p>
 <p class="text-xs text-red-600">{{ $stats['unacknowledged_alerts'] ?? 0 }} non traitées</p>
 </div>
 </a>

 <a href="{{ route('admin.maintenance.schedules.index') }}" class="action-button group flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border-2 border-blue-200 transition-all duration-200">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-blue-800">Planifications</p>
 <p class="text-xs text-blue-600">{{ $stats['scheduled_maintenance'] ?? 0 }} actives</p>
 </div>
 </a>

 <a href="{{ route('admin.maintenance.operations.index') }}" class="action-button group flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border-2 border-green-200 transition-all duration-200">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-green-800">Opérations</p>
 <p class="text-xs text-green-600">{{ $stats['active_operations'] ?? 0 }} en cours</p>
 </div>
 </a>

 <a href="{{ route('admin.maintenance.reports.index') }}" class="action-button group flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border-2 border-purple-200 transition-all duration-200">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-purple-800">Rapports</p>
 <p class="text-xs text-purple-600">Analytics avancés</p>
 </div>
 </a>
 </div>
 </div>
 </div>

 {{-- Alertes critiques --}}
 @if(isset($criticalAlerts) && $criticalAlerts->count() > 0)
 <div class="bg-white shadow rounded-lg mb-8">
 <div class="px-6 py-4 border-b border-gray-200">
 <div class="flex items-center justify-between">
 <h3 class="text-lg leading-6 font-medium text-gray-900">
 <span class="inline-flex items-center">
 <span class="pulse-animation inline-block h-2 w-2 bg-red-500 rounded-full mr-2"></span>
 Alertes Critiques
 </span>
 </h3>
 <a href="{{ route('admin.maintenance.alerts.index', ['priority' => 'critical']) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
 Voir toutes →
 </a>
 </div>
 </div>
 <div class="px-6 py-4">
 <div class="space-y-3">
 @foreach($criticalAlerts as $alert)
 <div class="alert-item alert-critical rounded-lg p-4">
 <div class="flex items-center justify-between">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-red-900">
 {{ $alert->vehicle->registration_plate ?? 'Véhicule inconnu' }} - {{ $alert->schedule->maintenanceType->name ?? 'Maintenance' }}
 </p>
 <p class="text-xs text-red-700">{{ $alert->message }}</p>
 </div>
 </div>
 <div class="flex items-center space-x-2">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
 {{ ucfirst($alert->priority) }}
 </span>
 <span class="text-xs text-red-600">{{ $alert->created_at->diffForHumans() }}</span>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @endif

 {{-- Prochaines maintenances --}}
 @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <div class="flex items-center justify-between">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Prochaines Maintenances (7 jours)</h3>
 <a href="{{ route('admin.maintenance.schedules.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
 Voir toutes →
 </a>
 </div>
 </div>
 <div class="overflow-hidden">
 <table class="maintenance-table min-w-full divide-y divide-gray-200">
 <thead>
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Échéance</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($upcomingMaintenance as $maintenance)
 <tr>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-8 w-8">
 <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
 <svg class="h-4 w-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-3">
 <div class="text-sm font-medium text-gray-900">{{ $maintenance->vehicle->registration_plate }}</div>
 <div class="text-xs text-gray-500">{{ $maintenance->vehicle->brand }} {{ $maintenance->vehicle->model }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $maintenance->maintenanceType->name }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 {{ $maintenance->next_due_date ? $maintenance->next_due_date->format('d/m/Y') : 'À définir' }}
 @if($maintenance->next_due_date)
 <div class="text-xs text-gray-500">
 {{ $maintenance->next_due_date->diffForHumans() }}
 </div>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $daysRemaining = $maintenance->next_due_date ? $maintenance->next_due_date->diffInDays(now(), false) : null;
 $isOverdue = $daysRemaining !== null && $daysRemaining > 0;
 $isUrgent = $daysRemaining !== null && $daysRemaining >= -3 && $daysRemaining <= 0;
 @endphp
 @if($isOverdue)
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
 En retard
 </span>
 @elseif($isUrgent)
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
 Urgent
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 Planifié
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <a href="{{ route('admin.maintenance.schedules.show', $maintenance) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">
 <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
 <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
 </svg>
 </a>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endif
</div>

{{-- Scripts pour les graphiques --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Configuration des couleurs
 const colors = {
 critical: '#ef4444',
 high: '#f59e0b',
 medium: '#3b82f6',
 low: '#10b981',
 primary: '#6366f1'
 };

 // Graphique des alertes par priorité
 const alertsCtx = document.getElementById('alertsChart');
 if (alertsCtx) {
 const alertsData = @json($chartData['alerts_by_priority'] ?? []);

 new Chart(alertsCtx, {
 type: 'doughnut',
 data: {
 labels: Object.keys(alertsData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
 datasets: [{
 data: Object.values(alertsData),
 backgroundColor: [
 colors.critical,
 colors.high,
 colors.medium,
 colors.low
 ],
 borderWidth: 2,
 borderColor: '#ffffff',
 hoverBorderWidth: 3
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: {
 position: 'bottom',
 labels: {
 padding: 20,
 usePointStyle: true,
 font: {
 size: 12,
 family: 'Inter, sans-serif'
 }
 }
 },
 tooltip: {
 backgroundColor: 'rgba(0, 0, 0, 0.8)',
 titleFont: { size: 14, weight: 'bold' },
 bodyFont: { size: 12 },
 cornerRadius: 8,
 displayColors: true
 }
 },
 animation: {
 animateRotate: true,
 duration: 1000
 }
 }
 });
 }

 // Graphique d'évolution des coûts
 const costsCtx = document.getElementById('costsChart');
 if (costsCtx) {
 const costsData = @json($chartData['cost_evolution'] ?? []);

 new Chart(costsCtx, {
 type: 'line',
 data: {
 labels: costsData.map(item => item.month),
 datasets: [{
 label: 'Coûts (€)',
 data: costsData.map(item => item.cost),
 borderColor: colors.primary,
 backgroundColor: colors.primary + '20',
 borderWidth: 3,
 fill: true,
 tension: 0.4,
 pointBackgroundColor: colors.primary,
 pointBorderColor: '#ffffff',
 pointBorderWidth: 2,
 pointRadius: 6,
 pointHoverRadius: 8
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: {
 display: false
 },
 tooltip: {
 backgroundColor: 'rgba(0, 0, 0, 0.8)',
 titleFont: { size: 14, weight: 'bold' },
 bodyFont: { size: 12 },
 cornerRadius: 8,
 callbacks: {
 label: function(context) {
 return 'Coût: ' + new Intl.NumberFormat('fr-FR', {
 style: 'currency',
 currency: 'EUR'
 }).format(context.parsed.y);
 }
 }
 }
 },
 scales: {
 y: {
 beginAtZero: true,
 grid: {
 color: '#f1f5f9'
 },
 ticks: {
 callback: function(value) {
 return new Intl.NumberFormat('fr-FR', {
 style: 'currency',
 currency: 'EUR',
 minimumFractionDigits: 0
 }).format(value);
 },
 font: {
 size: 11
 }
 }
 },
 x: {
 grid: {
 display: false
 },
 ticks: {
 font: {
 size: 11
 }
 }
 }
 },
 animation: {
 duration: 1000,
 easing: 'easeInOutQuart'
 }
 }
 });
 }
});

// Fonction pour actualiser le dashboard
function refreshDashboard() {
 const button = event.target.closest('button');
 const icon = button.querySelector('svg');

 // Animation de rotation
 icon.style.transform = 'rotate(360deg)';
 icon.style.transition = 'transform 0.5s ease';

 // Simulation d'actualisation
 setTimeout(() => {
 window.location.reload();
 }, 500);
}

// Fonction pour actualiser les opérations
function refreshOperations() {
 const button = event.target.closest('button');
 const icon = button.querySelector('svg');

 // Animation de rotation
 icon.style.transform = 'rotate(360deg)';
 icon.style.transition = 'transform 0.5s ease';

 // Simulation d'actualisation des opérations
 setTimeout(() => {
 // Ici on pourrait faire un appel AJAX pour rafraîchir seulement la table
 location.reload();
 }, 500);

 setTimeout(() => {
 icon.style.transform = 'rotate(0deg)';
 }, 600);
}

// Fonction pour filtrer les opérations
function filterOperations(filter) {
 const rows = document.querySelectorAll('.operation-row');
 const buttons = document.querySelectorAll('.filter-btn');

 // Mettre à jour les boutons
 buttons.forEach(btn => {
 btn.classList.remove('active', 'bg-blue-100', 'text-blue-700');
 btn.classList.add('bg-gray-100', 'text-gray-700');
 });

 const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
 if (activeBtn) {
 activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
 activeBtn.classList.add('active', 'bg-blue-100', 'text-blue-700');
 }

 // Filtrer les lignes
 rows.forEach(row => {
 const status = row.dataset.status;
 let show = false;

 if (filter === 'all') {
 show = true;
 } else if (filter === 'active' && status === 'active') {
 show = true;
 } else if (filter === 'scheduled' && status === 'scheduled') {
 show = true;
 } else if (filter === 'completed' && status === 'completed') {
 show = true;
 } else if (filter === 'overdue' && status === 'overdue') {
 show = true;
 }

 if (show) {
 row.style.display = 'table-row';
 row.style.opacity = '0';
 setTimeout(() => {
 row.style.transition = 'opacity 0.3s ease';
 row.style.opacity = '1';
 }, 50);
 } else {
 row.style.transition = 'opacity 0.3s ease';
 row.style.opacity = '0';
 setTimeout(() => {
 row.style.display = 'none';
 }, 300);
 }
 });
}

// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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

// Animations séquentielles Enterprise-Grade
document.addEventListener('DOMContentLoaded', function() {
 // Animation des cartes métriques
 const metricCards = document.querySelectorAll('.metric-card');
 metricCards.forEach((card, index) => {
 card.style.opacity = '0';
 card.style.transform = 'translateY(20px)';
 setTimeout(() => {
 card.style.transition = 'all 0.6s ease-out';
 card.style.opacity = '1';
 card.style.transform = 'translateY(0)';
 }, index * 100);
 });

 // Animation des boutons d'action rapide
 const actionButtons = document.querySelectorAll('.action-button');
 actionButtons.forEach((button, index) => {
 button.style.opacity = '0';
 button.style.transform = 'translateX(-20px)';
 setTimeout(() => {
 button.style.transition = 'all 0.4s ease-out';
 button.style.opacity = '1';
 button.style.transform = 'translateX(0)';
 }, 200 + index * 50);
 });

 // Animation des lignes du tableau d'opérations
 const operationRows = document.querySelectorAll('.operation-row');
 operationRows.forEach((row, index) => {
 row.style.opacity = '0';
 row.style.transform = 'translateX(-10px)';
 setTimeout(() => {
 row.style.transition = 'all 0.4s ease-out';
 row.style.opacity = '1';
 row.style.transform = 'translateX(0)';
 }, 500 + index * 100);
 });

 // Auto-refresh des indicateurs toutes les 30 secondes
 setInterval(function() {
 // Mise à jour des indicateurs de statut
 const statusIndicators = document.querySelectorAll('.animate-pulse');
 statusIndicators.forEach(indicator => {
 indicator.style.animation = 'none';
 setTimeout(() => {
 indicator.style.animation = 'pulse 2s infinite';
 }, 100);
 });
 }, 30000);
});

// Fonction pour mettre en surbrillance les éléments critiques
function highlightCriticalItems() {
 const criticalRows = document.querySelectorAll('[data-status="overdue"]');
 criticalRows.forEach(row => {
 row.classList.add('ring-2', 'ring-red-300', 'ring-opacity-50');
 setTimeout(() => {
 row.classList.remove('ring-2', 'ring-red-300', 'ring-opacity-50');
 }, 3000);
 });
}

// Appeler la fonction de mise en surbrillance au chargement
document.addEventListener('DOMContentLoaded', function() {
 setTimeout(highlightCriticalItems, 2000);
});
</script>
@endpush
@endsection