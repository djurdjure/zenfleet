<div class="space-y-6" x-data="{ showFilters: false }">
 {{-- Header avec barre d'actions --}}
 <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
 <div class="px-6 py-4">
 <div class="flex items-center justify-between flex-wrap gap-4">
 <div>
 <h2 class="text-2xl font-bold text-gray-900">Demandes de Réparation</h2>
 <p class="text-sm text-gray-600 mt-1">Workflow de validation à 2 niveaux - Vue Kanban</p>
 </div>
 <div class="flex items-center gap-3">
 <button
 @click="showFilters = !showFilters"
 class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
 </svg>
 Filtres
 <span x-show="showFilters" class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Actifs</span>
 </button>
 @can('repair-requests.create')
 <button
 wire:click="openCreateModal"
 class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition-colors">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 Nouvelle Demande
 </button>
 @endcan
 </div>
 </div>

 {{-- Panneau de filtres déroulant --}}
 <div x-show="showFilters"
 x-transition:enter="transition ease-out duration-200"
 x-transition:enter-start="opacity-0 -translate-y-2"
 x-transition:enter-end="opacity-100 translate-y-0"
 x-transition:leave="transition ease-in duration-150"
 x-transition:leave-start="opacity-100 translate-y-0"
 x-transition:leave-end="opacity-0 -translate-y-2"
 class="mt-4 pt-4 border-t border-gray-200">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
 <input
 type="text"
 wire:model.live="search"
 placeholder="Rechercher..."
 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
 <select
 wire:model.live="filterStatus"
 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les statuts</option>
 <option value="pending_supervisor">En attente superviseur</option>
 <option value="approved_supervisor">Approuvé superviseur</option>
 <option value="pending_fleet_manager">En attente gestionnaire</option>
 <option value="approved_final">Approuvé final</option>
 <option value="rejected_supervisor">Rejeté superviseur</option>
 <option value="rejected_final">Rejeté final</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Urgence</label>
 <select
 wire:model.live="filterPriority"
 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Toutes urgences</option>
 <option value="critical">Critique</option>
 <option value="high">Haute</option>
 <option value="normal">Normale</option>
 <option value="low">Basse</option>
 </select>
 </div>
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Véhicule</label>
 <select
 wire:model.live="filterVehicle"
 class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les véhicules</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }}</option>
 @endforeach
 </select>
 </div>
 </div>
 </div>
 </div>

 {{-- Statistiques rapides enrichies --}}
 <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
 <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
 {{-- Total --}}
 <div class="bg-white p-4 rounded-lg border-2 border-gray-200 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
 </div>
 <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- En attente --}}
 <div class="bg-white p-4 rounded-lg border-2 border-yellow-200 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-yellow-700 uppercase tracking-wide">En attente</p>
 <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $stats['pending'] }}</p>
 </div>
 <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- Urgentes --}}
 <div class="bg-white p-4 rounded-lg border-2 border-red-200 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-red-700 uppercase tracking-wide">Urgentes</p>
 <p class="text-2xl font-bold text-red-900 mt-1">{{ $stats['urgent'] }}</p>
 </div>
 <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center animate-pulse">
 <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- Approuvées --}}
 <div class="bg-white p-4 rounded-lg border-2 border-green-200 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-green-700 uppercase tracking-wide">Approuvées</p>
 <p class="text-2xl font-bold text-green-900 mt-1">{{ $stats['approved'] }}</p>
 </div>
 <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- Rejetées --}}
 <div class="bg-white p-4 rounded-lg border-2 border-gray-300 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">Rejetées</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['rejected'] }}</p>
 </div>
 <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- Budget estimé --}}
 <div class="bg-white p-4 rounded-lg border-2 border-blue-200 hover:shadow-md transition-shadow cursor-pointer">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-blue-700 uppercase tracking-wide">Budget Moy.</p>
 <p class="text-lg font-bold text-blue-900 mt-1">{{ number_format($stats['avg_estimated_cost'], 0, ',', ' ') }}</p>
 <p class="text-xs text-blue-600">DA</p>
 </div>
 <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
 <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Tableau Kanban --}}
 <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-lg shadow-inner">
 <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 overflow-x-auto">

 {{-- Colonne 1: En Attente Superviseur --}}
 <div class="bg-white rounded-xl shadow-md border-t-4 border-yellow-400 min-h-[400px]">
 <div class="px-4 py-3 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-t-lg">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
 <span class="text-white font-bold text-sm">1</span>
 </div>
 <h3 class="font-bold text-gray-900 text-sm">En Attente Superviseur</h3>
 </div>
 <span class="bg-yellow-200 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">
 {{ count($kanbanData['pending_supervisor']) }}
 </span>
 </div>
 </div>
 <div class="p-3 space-y-3 max-h-[700px] overflow-y-auto">
 @forelse($kanbanData['pending_supervisor'] as $request)
 <div wire:click="openDetailsModal({{ $request->id }})"
 class="bg-white border-2 border-yellow-200 rounded-xl p-4 hover:shadow-lg hover:border-yellow-400 transition-all cursor-pointer transform hover:-translate-y-1">
 <div class="space-y-3">
 {{-- Header avec plaque et urgence --}}
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-2">
 <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-500">{{ $request->vehicle->brand }} {{ $request->vehicle->model }}</p>
 </div>
 </div>
 <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
 {{ $request->urgency === 'critical' ? 'bg-red-100 text-red-800 ring-2 ring-red-400 animate-pulse' : '' }}
 {{ $request->urgency === 'high' ? 'bg-orange-100 text-orange-800 ring-1 ring-orange-400' : '' }}
 {{ $request->urgency === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
 {{ $request->urgency === 'low' ? 'bg-gray-100 text-gray-700' : '' }}">
 {{ $request->urgency_label }}
 </span>
 </div>

 {{-- Description --}}
 <p class="text-sm text-gray-700 line-clamp-3 leading-relaxed">
 {{ $request->description }}
 </p>

 {{-- Informations supplémentaires --}}
 <div class="flex items-center justify-between pt-2 border-t border-gray-100">
 <div class="flex items-center gap-4 text-xs">
 @if($request->estimated_cost)
 <span class="inline-flex items-center gap-1 text-blue-600 font-semibold">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 {{ number_format($request->estimated_cost, 0, ',', ' ') }} DA
 </span>
 @endif
 </div>
 <span class="text-xs text-gray-500 flex items-center gap-1">
 <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 {{ $request->created_at->diffForHumans() }}
 </span>
 </div>
 </div>
 </div>
 @empty
 <div class="text-center py-12">
 <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <p class="text-sm text-gray-500 font-medium">Aucune demande</p>
 </div>
 @endforelse
 </div>
 </div>

 {{-- Colonne 2: Approuvé Superviseur --}}
 <div class="bg-white rounded-xl shadow-md border-t-4 border-blue-400 min-h-[400px]">
 <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-t-lg">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
 <span class="text-white font-bold text-sm">2</span>
 </div>
 <h3 class="font-bold text-gray-900 text-sm">Approuvé Superviseur</h3>
 </div>
 <span class="bg-blue-200 text-blue-900 text-xs font-bold px-3 py-1 rounded-full">
 {{ count($kanbanData['approved_supervisor']) }}
 </span>
 </div>
 </div>
 <div class="p-3 space-y-3 max-h-[700px] overflow-y-auto">
 @forelse($kanbanData['approved_supervisor'] as $request)
 <div wire:click="openDetailsModal({{ $request->id }})"
 class="bg-white border-2 border-blue-200 rounded-xl p-4 hover:shadow-lg hover:border-blue-400 transition-all cursor-pointer transform hover:-translate-y-1">
 <div class="space-y-3">
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-2">
 <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-500">{{ $request->vehicle->brand ?? 'N/A' }}</p>
 </div>
 </div>
 </div>
 <p class="text-sm text-gray-700 line-clamp-2">{{ $request->description }}</p>
 @if($request->supervisor)
 <div class="flex items-center gap-2 text-xs text-blue-700 bg-blue-50 px-3 py-2 rounded-lg">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
 </svg>
 <span class="font-medium">{{ $request->supervisor->name }}</span>
 </div>
 @endif
 @if($request->supervisor_approved_at)
 <p class="text-xs text-gray-500">{{ $request->supervisor_approved_at->diffForHumans() }}</p>
 @endif
 </div>
 </div>
 @empty
 <div class="text-center py-12">
 <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <p class="text-sm text-gray-500 font-medium">Aucune demande</p>
 </div>
 @endforelse
 </div>
 </div>

 {{-- Colonne 3: En Attente Gestionnaire --}}
 <div class="bg-white rounded-xl shadow-md border-t-4 border-purple-400 min-h-[400px]">
 <div class="px-4 py-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-t-lg">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-purple-400 rounded-full flex items-center justify-center">
 <span class="text-white font-bold text-sm">3</span>
 </div>
 <h3 class="font-bold text-gray-900 text-sm">En Attente Gestionnaire</h3>
 </div>
 <span class="bg-purple-200 text-purple-900 text-xs font-bold px-3 py-1 rounded-full">
 {{ count($kanbanData['pending_fleet_manager']) }}
 </span>
 </div>
 </div>
 <div class="p-3 space-y-3 max-h-[700px] overflow-y-auto">
 @forelse($kanbanData['pending_fleet_manager'] as $request)
 <div wire:click="openDetailsModal({{ $request->id }})"
 class="bg-white border-2 border-purple-200 rounded-xl p-4 hover:shadow-lg hover:border-purple-400 transition-all cursor-pointer transform hover:-translate-y-1">
 <div class="space-y-3">
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-2">
 <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-500">{{ $request->vehicle->brand ?? 'N/A' }}</p>
 </div>
 </div>
 <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
 {{ $request->urgency === 'critical' ? 'bg-red-100 text-red-800' : '' }}
 {{ $request->urgency === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
 {{ $request->urgency === 'normal' ? 'bg-blue-100 text-blue-800' : '' }}
 {{ $request->urgency === 'low' ? 'bg-gray-100 text-gray-700' : '' }}">
 {{ $request->urgency_label }}
 </span>
 </div>
 <p class="text-sm text-gray-700 line-clamp-2">{{ $request->description }}</p>
 @if($request->estimated_cost)
 <div class="flex items-center gap-2 text-xs font-semibold text-blue-700">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 {{ number_format($request->estimated_cost, 0, ',', ' ') }} DA
 </div>
 @endif
 @if($request->supervisor)
 <div class="flex items-center gap-2 text-xs text-green-700 bg-green-50 px-3 py-2 rounded-lg">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <span class="font-medium">Validé par {{ $request->supervisor->name }}</span>
 </div>
 @endif
 </div>
 </div>
 @empty
 <div class="text-center py-12">
 <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <p class="text-sm text-gray-500 font-medium">Aucune demande</p>
 </div>
 @endforelse
 </div>
 </div>

 {{-- Colonne 4: Approuvées (Validation Finale) --}}
 <div class="bg-white rounded-xl shadow-md border-t-4 border-green-400 min-h-[400px]">
 <div class="px-4 py-3 bg-gradient-to-r from-green-50 to-green-100 rounded-t-lg">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
 </svg>
 </div>
 <h3 class="font-bold text-gray-900 text-sm">Approuvées</h3>
 </div>
 <span class="bg-green-200 text-green-900 text-xs font-bold px-3 py-1 rounded-full">
 {{ count($kanbanData['approved_final']) }}
 </span>
 </div>
 </div>
 <div class="p-3 space-y-3 max-h-[700px] overflow-y-auto">
 @forelse($kanbanData['approved_final'] as $request)
 <div wire:click="openDetailsModal({{ $request->id }})"
 class="bg-white border-2 border-green-200 rounded-xl p-4 hover:shadow-lg hover:border-green-400 transition-all cursor-pointer transform hover:-translate-y-1">
 <div class="space-y-3">
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-2">
 <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-500">{{ $request->vehicle->brand ?? 'N/A' }}</p>
 </div>
 </div>
 </div>
 <p class="text-sm text-gray-700 line-clamp-2">{{ $request->description }}</p>
 @if($request->estimated_cost)
 <div class="flex items-center gap-2 text-xs font-bold text-green-700 bg-green-50 px-3 py-2 rounded-lg">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 Budget: {{ number_format($request->estimated_cost, 0, ',', ' ') }} DA
 </div>
 @endif
 @if($request->fleetManager)
 <div class="flex items-center gap-2 text-xs text-green-700">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
 </svg>
 <span class="font-medium">{{ $request->fleetManager->name }}</span>
 </div>
 @endif
 @if($request->final_approved_at)
 <p class="text-xs text-gray-500">Approuvé {{ $request->final_approved_at->diffForHumans() }}</p>
 @endif
 </div>
 </div>
 @empty
 <div class="text-center py-12">
 <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <p class="text-sm text-gray-500 font-medium">Aucune demande</p>
 </div>
 @endforelse
 </div>
 </div>

 {{-- Colonne 5: Rejetées --}}
 <div class="bg-white rounded-xl shadow-md border-t-4 border-red-400 min-h-[400px]">
 <div class="px-4 py-3 bg-gradient-to-r from-red-50 to-red-100 rounded-t-lg">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-red-400 rounded-full flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </div>
 <h3 class="font-bold text-gray-900 text-sm">Rejetées</h3>
 </div>
 <span class="bg-red-200 text-red-900 text-xs font-bold px-3 py-1 rounded-full">
 {{ count($kanbanData['rejected']) }}
 </span>
 </div>
 </div>
 <div class="p-3 space-y-3 max-h-[700px] overflow-y-auto">
 @forelse($kanbanData['rejected'] as $request)
 <div wire:click="openDetailsModal({{ $request->id }})"
 class="bg-white border-2 border-red-200 rounded-xl p-4 hover:shadow-lg hover:border-red-400 transition-all cursor-pointer transform hover:-translate-y-1 opacity-75 hover:opacity-100">
 <div class="space-y-3">
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-2">
 <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
 </svg>
 </div>
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $request->vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-500">{{ $request->vehicle->brand ?? 'N/A' }}</p>
 </div>
 </div>
 <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
 Rejetée
 </span>
 </div>
 <p class="text-sm text-gray-700 line-clamp-2">{{ $request->description }}</p>
 @if($request->rejection_reason)
 <div class="bg-red-50 border border-red-200 rounded-lg p-3">
 <p class="text-xs text-red-700 flex items-start gap-2">
 <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <span class="font-medium">{{ Str::limit($request->rejection_reason, 80) }}</span>
 </p>
 </div>
 @endif
 @if($request->rejectedBy)
 <p class="text-xs text-gray-600">Rejeté par <span class="font-medium">{{ $request->rejectedBy->name }}</span></p>
 @endif
 @if($request->rejected_at)
 <p class="text-xs text-gray-500">{{ $request->rejected_at->diffForHumans() }}</p>
 @endif
 </div>
 </div>
 @empty
 <div class="text-center py-12">
 <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <p class="text-sm text-gray-500 font-medium">Aucune demande</p>
 </div>
 @endforelse
 </div>
 </div>

 </div>
 </div>

 {{-- Légende du workflow enrichie --}}
 <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Workflow de Validation</h3>
 <span class="text-xs text-gray-500">Processus en {{ count($kanbanData['pending_supervisor']) + count($kanbanData['approved_supervisor']) + count($kanbanData['pending_fleet_manager']) + count($kanbanData['approved_final']) + count($kanbanData['rejected']) }} étapes actives</span>
 </div>
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6 text-xs flex-wrap">
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 bg-yellow-400 rounded-full"></div>
 <span class="text-gray-700 font-medium">1. Chauffeur → Demande</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 bg-blue-400 rounded-full"></div>
 <span class="text-gray-700 font-medium">2. Superviseur (L1)</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 bg-purple-400 rounded-full"></div>
 <span class="text-gray-700 font-medium">3. En attente L2</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 bg-green-400 rounded-full"></div>
 <span class="text-gray-700 font-medium">4. Gestionnaire (L2)</span>
 </div>
 <div class="flex items-center gap-2">
 <div class="w-4 h-4 bg-red-400 rounded-full"></div>
 <span class="text-gray-700 font-medium">⚠ Rejet possible</span>
 </div>
 </div>
 <div class="flex items-center gap-2 text-xs text-gray-500">
 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
 </svg>
 <span>Cliquez sur une carte pour voir les détails</span>
 </div>
 </div>
 </div>

 {{-- 🔧 MODALS - Formulaires de gestion ULTRA ENTERPRISE GRADE --}}
 @include('livewire.admin.repair-request-modals-enterprise')
</div>
