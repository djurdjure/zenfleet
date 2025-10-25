{{-- resources/views/livewire/admin/vehicle-mileage-history.blade.php --}}
<div class="fade-in">
 {{-- En-tête avec breadcrumb --}}
 <div class="mb-8">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors inline-flex items-center">
 <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 Véhicules
 </a>
 <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
 </svg>
 <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="hover:text-blue-600 transition-colors">
 {{ $vehicle->registration_plate }}
 </a>
 <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
 </svg>
 <span class="text-blue-600 font-semibold">Historique kilométrique</span>
 </nav>

 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
 Historique Kilométrique
 </h1>
 <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
 <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
 </svg>
 {{ $vehicle->brand }} {{ $vehicle->model }} • {{ $vehicle->registration_plate }}
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 Kilométrage actuel: <strong class="ml-1">{{ number_format($vehicle->current_mileage) }} km</strong>
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 @can('create mileage readings')
 <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition-colors duration-200">
 <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 Nouveau relevé
 </button>
 @endcan
 <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm transition-colors duration-200">
 <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
 </svg>
 Retour
 </a>
 </div>
 </div>
 </div>

 {{-- Flash Messages --}}
 @if (session()->has('success'))
 <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
 </div>
 </div>
 </div>
 @endif

 @if (session()->has('error'))
 <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
 </div>
 </div>
 </div>
 @endif

 @if (session()->has('info'))
 <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
 </div>
 </div>
 </div>
 @endif

 {{-- Statistiques rapides --}}
 <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
 <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
 <div class="p-5">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
 <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-5 w-0 flex-1">
 <dl>
 <dt class="text-sm font-medium text-gray-500 truncate">Total relevés</dt>
 <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_readings']) }}</dd>
 </dl>
 </div>
 </div>
 </div>
 </div>

 <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
 <div class="p-5">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-5 w-0 flex-1">
 <dl>
 <dt class="text-sm font-medium text-gray-500 truncate">Distance parcourue</dt>
 <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_distance']) }} km</dd>
 </dl>
 </div>
 </div>
 </div>
 </div>

 <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
 <div class="p-5">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
 </svg>
 </div>
 </div>
 <div class="ml-5 w-0 flex-1">
 <dl>
 <dt class="text-sm font-medium text-gray-500 truncate">Relevés manuels</dt>
 <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['manual_count']) }}</dd>
 </dl>
 </div>
 </div>
 </div>
 </div>

 <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
 <div class="p-5">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
 <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-5 w-0 flex-1">
 <dl>
 <dt class="text-sm font-medium text-gray-500 truncate">Relevés automatiques</dt>
 <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['automatic_count']) }}</dd>
 </dl>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Filtres et Actions --}}
 <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
 <div class="p-6">
 <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
 {{-- Recherche --}}
 <div class="lg:col-span-2">
 <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
 <input wire:model.live.debounce.300ms="search" type="text" id="search" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Kilométrage, notes, auteur...">
 </div>

 {{-- Filtre Méthode --}}
 <div>
 <label for="method-filter" class="block text-sm font-medium text-gray-700 mb-1">Méthode</label>
 <select wire:model.live="methodFilter" id="method-filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 <option value="">Toutes</option>
 <option value="manual">Manuel</option>
 <option value="automatic">Automatique</option>
 </select>
 </div>

 {{-- Filtre Date De --}}
 <div>
 <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">Date de</label>
 <input wire:model.live="dateFrom" type="date" id="date-from" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>

 {{-- Filtre Date À --}}
 <div>
 <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">Date à</label>
 <input wire:model.live="dateTo" type="date" id="date-to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
 </div>
 </div>

 <div class="mt-4 flex items-center justify-between">
 <button wire:click="resetFilters" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
 </svg>
 Réinitialiser filtres
 </button>

 @can('export mileage readings')
 <div class="flex space-x-2">
 <button wire:click="exportCsv" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 Export CSV
 </button>
 <button wire:click="exportExcel" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 Export Excel
 </button>
 </div>
 @endcan
 </div>
 </div>
 </div>

 {{-- Table des relevés --}}
 <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th wire:click="sortBy('recorded_at')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
 <div class="flex items-center space-x-1">
 <span>Date & Heure</span>
 @if ($sortField === 'recorded_at')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if ($sortDirection === 'asc')
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 @else
 <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th wire:click="sortBy('mileage')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
 <div class="flex items-center space-x-1">
 <span>Kilométrage</span>
 @if ($sortField === 'mileage')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if ($sortDirection === 'asc')
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 @else
 <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Auteur
 </th>
 <th wire:click="sortBy('recording_method')" scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
 <div class="flex items-center space-x-1">
 <span>Méthode</span>
 @if ($sortField === 'recording_method')
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 @if ($sortDirection === 'asc')
 <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 @else
 <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
 @endif
 </svg>
 @endif
 </div>
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Notes
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse ($readings as $reading)
 <tr class="hover:bg-gray-50 transition-colors">
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 <div class="flex flex-col">
 <span class="font-medium">{{ $reading->recorded_at->format('d/m/Y') }}</span>
 <span class="text-gray-500">{{ $reading->recorded_at->format('H:i') }}</span>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
 {{ number_format($reading->mileage) }} km
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 @if ($reading->recordedBy)
 <div class="flex items-center">
 <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
 <span class="text-blue-600 font-semibold text-xs">
 {{ substr($reading->recordedBy->name, 0, 2) }}
 </span>
 </div>
 <div>
 <div class="font-medium">{{ $reading->recordedBy->name }}</div>
 </div>
 </div>
 @else
 <span class="text-gray-400 italic">Système</span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm">
 @if ($reading->recording_method === 'manual')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
 </svg>
 Manuel
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
 <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
 </svg>
 Automatique
 </span>
 @endif
 </td>
 <td class="px-6 py-4 text-sm text-gray-500">
 @if ($reading->notes)
 <span class="truncate max-w-xs block" title="{{ $reading->notes }}">{{ $reading->notes }}</span>
 @else
 <span class="text-gray-400 italic">-</span>
 @endif
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-6 py-12 text-center">
 <div class="flex flex-col items-center justify-center text-gray-500">
 <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
 </svg>
 <p class="text-lg font-medium">Aucun relevé trouvé</p>
 <p class="text-sm mt-1">Essayez de modifier vos filtres de recherche</p>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 @if ($readings->hasPages())
 <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
 {{ $readings->links() }}
 </div>
 @endif
 </div>

 {{-- Modal Ajout Relevé --}}
 @if ($showAddModal)
 <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeAddModal"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div>
 <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
 <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-5">
 <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
 Nouveau Relevé Kilométrique
 </h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 {{ $vehicle->brand }} {{ $vehicle->model }} • {{ $vehicle->registration_plate }}
 </p>
 </div>
 </div>
 </div>

 <form wire:submit.prevent="saveReading" class="mt-6 space-y-4">
 {{-- Kilométrage --}}
 <div>
 <label for="new-mileage" class="block text-sm font-medium text-gray-700">Kilométrage (km) *</label>
 <input wire:model="newMileage" type="number" id="new-mileage" min="0" max="9999999" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('newMileage') border-red-300 @enderror">
 @error('newMileage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 {{-- Date et Heure --}}
 <div>
 <label for="new-recorded-at" class="block text-sm font-medium text-gray-700">Date et Heure *</label>
 <input wire:model="newRecordedAt" type="datetime-local" id="new-recorded-at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('newRecordedAt') border-red-300 @enderror">
 @error('newRecordedAt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 {{-- Méthode --}}
 <div>
 <label for="new-recording-method" class="block text-sm font-medium text-gray-700">Méthode *</label>
 <select wire:model="newRecordingMethod" id="new-recording-method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('newRecordingMethod') border-red-300 @enderror">
 <option value="manual">Manuel</option>
 @can('manage automatic mileage readings')
 <option value="automatic">Automatique</option>
 @endcan
 </select>
 @error('newRecordingMethod') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 </div>

 {{-- Notes --}}
 <div>
 <label for="new-notes" class="block text-sm font-medium text-gray-700">Notes (optionnel)</label>
 <textarea wire:model="newNotes" id="new-notes" rows="3" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('newNotes') border-red-300 @enderror" placeholder="Observations, contexte..."></textarea>
 @error('newNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
 <p class="mt-1 text-xs text-gray-500">{{ strlen($newNotes) }}/500 caractères</p>
 </div>

 {{-- Boutons --}}
 <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
 <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
 Enregistrer
 </button>
 <button type="button" wire:click="closeAddModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
 Annuler
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 @endif
</div>

@push('styles')
<style>
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
