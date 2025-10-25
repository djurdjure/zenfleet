{{-- ====================================================================
 📊 RELEVÉS KILOMÉTRIQUES V9.0 - ULTRA-PRO ENTERPRISE-GRADE
 ====================================================================

 Design aligné avec pages Véhicules/Chauffeurs:
 ✨ Cards statistiques simples style blanc
 ✨ Icônes et pastilles cohérentes
 ✨ Pagination avancée 25/50/100
 ✨ Filtres ultra-pro avec badges actifs
 ✨ Table optimisée avec hover states
 ✨ Export multi-formats
 ✨ Performance maximale

 @version 9.0-Ultra-Pro-Enterprise
 @since 2025-10-25
 ==================================================================== --}}

<div>
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER PROFESSIONNEL
        =============================================== --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                    <x-iconify icon="lucide:gauge" class="w-7 h-7 text-blue-600" />
                    Historique Kilométrage
                </h1>
                <p class="text-sm text-gray-600 ml-9">
                    Gestion centralisée des relevés kilométriques de l'ensemble de la flotte
                </p>
            </div>
            
            <div class="flex gap-2">
                @can('create mileage readings')
                <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    Nouveau relevé
                </a>
                @endcan
                
                @can('export mileage readings')
                <button wire:click="exportData" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium rounded-lg shadow-sm transition-colors">
                    <x-iconify icon="lucide:download" class="w-5 h-5" />
                    Export
                </button>
                @endcan
            </div>
        </div>

        {{-- ===============================================
            CARDS STATISTIQUES STYLE VÉHICULES/CHAUFFEURS
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            
            {{-- 1. Total Relevés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total relevés</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($analytics['total_readings'] ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(($analytics['trend_30_days']['trend'] ?? '') === 'increasing')
                                <span class="text-green-600">
                                    <x-iconify icon="lucide:trending-up" class="w-3 h-3 inline" />
                                    +{{ $analytics['trend_30_days']['percentage'] ?? 0 }}%
                                </span>
                            @elseif(($analytics['trend_30_days']['trend'] ?? '') === 'decreasing')
                                <span class="text-red-600">
                                    <x-iconify icon="lucide:trending-down" class="w-3 h-3 inline" />
                                    {{ $analytics['trend_30_days']['percentage'] ?? 0 }}%
                                </span>
                            @else
                                <span>Stable</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:gauge" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- 2. Véhicules Suivis --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Véhicules</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">
                            {{ number_format($analytics['vehicles_tracked'] ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Actifs
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-5 h-5 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- 3. Relevés Manuels --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Manuels</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ number_format($analytics['manual_count'] ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $analytics['method_distribution']['manual_percentage'] ?? 0 }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:hand" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- 4. Relevés Automatiques --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Automatiques</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ number_format($analytics['automatic_count'] ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $analytics['method_distribution']['automatic_percentage'] ?? 0 }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:cpu" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- 5. KM Total Parcouru --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">KM Total</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">
                            @if(($analytics['total_mileage_covered'] ?? 0) > 999999)
                                {{ number_format(($analytics['total_mileage_covered'] ?? 0) / 1000000, 1) }}M
                            @else
                                {{ number_format($analytics['total_mileage_covered'] ?? 0) }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Parcourus
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:route" class="w-5 h-5 text-indigo-600" />
                    </div>
                </div>
            </div>

            {{-- 6. Moyenne Journalière --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Moy./jour</p>
                        <p class="text-2xl font-bold text-teal-600 mt-1">
                            {{ number_format($analytics['avg_daily_mileage'] ?? 0) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            km (30j)
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-range" class="w-5 h-5 text-teal-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            SECTION ANOMALIES (si présentes)
        =============================================== --}}
        @if(count($anomalies ?? []) > 0)
        <div class="bg-white rounded-lg border border-red-200 p-4 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-900 mb-2">
                        Anomalies détectées ({{ count($anomalies) }})
                    </h3>
                    <div class="space-y-2">
                        @foreach(array_slice($anomalies, 0, 3) as $anomaly)
                        <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $anomaly['severity'] === 'high' ? 'bg-red-600 text-white' : 
                                       ($anomaly['severity'] === 'medium' ? 'bg-orange-500 text-white' : 'bg-yellow-500 text-white') }}">
                                    {{ strtoupper($anomaly['severity']) }}
                                </span>
                                <span class="text-sm text-gray-700">
                                    {{ $anomaly['vehicle'] }} - {{ $anomaly['description'] }}
                                </span>
                            </div>
                            <a href="{{ route('admin.vehicles.show', $anomaly['vehicle_id']) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800">
                                Voir détails →
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @if(count($anomalies) > 3)
                    <button wire:click="$set('showAllAnomalies', true)" 
                            class="mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Voir toutes les anomalies ({{ count($anomalies) - 3 }} de plus)
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ===============================================
            FILTRES AVANCÉS ULTRA-PRO
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="p-6">
                {{-- Ligne 1: Recherche + Filtres principaux --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-7 mb-4">
                    {{-- Recherche --}}
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:search" class="w-4 h-4 inline mr-1" />
                            Rechercher
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input 
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                id="search"
                                placeholder="Véhicule, immatriculation, notes..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:car" class="w-4 h-4 inline mr-1" />
                            Véhicule
                        </label>
                        <select 
                            wire:model.live="vehicleFilter"
                            id="vehicle-filter"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Méthode --}}
                    <div>
                        <label for="method-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:settings" class="w-4 h-4 inline mr-1" />
                            Méthode
                        </label>
                        <select 
                            wire:model.live="methodFilter"
                            id="method-filter"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes</option>
                            <option value="manual">Manuel</option>
                            <option value="automatic">Automatique</option>
                        </select>
                    </div>

                    {{-- Date de --}}
                    <div>
                        <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Du
                        </label>
                        <input 
                            wire:model.live="dateFrom"
                            type="date"
                            id="date-from"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Date à --}}
                    <div>
                        <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Au
                        </label>
                        <input 
                            wire:model.live="dateTo"
                            type="date"
                            id="date-to"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- NOUVEAU: Pagination Par Page --}}
                    <div>
                        <label for="per-page" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:list" class="w-4 h-4 inline mr-1" />
                            Par page
                        </label>
                        <select 
                            wire:model.live="perPage"
                            id="per-page"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                {{-- Ligne 2: Filtres avancés --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Utilisateur --}}
                    <div>
                        <label for="author-filter" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:user" class="w-4 h-4 inline mr-1" />
                            Utilisateur
                        </label>
                        <select 
                            wire:model.live="authorFilter"
                            id="author-filter"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}">{{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KM Min --}}
                    <div>
                        <label for="mileage-min" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:gauge" class="w-4 h-4 inline mr-1" />
                            KM Min
                        </label>
                        <input 
                            wire:model.live.debounce.500ms="mileageMin"
                            type="number"
                            id="mileage-min"
                            placeholder="0"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- KM Max --}}
                    <div>
                        <label for="mileage-max" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:gauge" class="w-4 h-4 inline mr-1" />
                            KM Max
                        </label>
                        <input 
                            wire:model.live.debounce.500ms="mileageMax"
                            type="number"
                            id="mileage-max"
                            placeholder="999999"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    {{-- Tri rapide --}}
                    <div>
                        <label for="quick-sort" class="block text-sm font-medium text-gray-700 mb-1">
                            <x-iconify icon="lucide:arrow-up-down" class="w-4 h-4 inline mr-1" />
                            Tri rapide
                        </label>
                        <select 
                            wire:model.live="quickSort"
                            id="quick-sort"
                            class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="recent">Plus récents</option>
                            <option value="oldest">Plus anciens</option>
                            <option value="mileage_high">KM élevé</option>
                            <option value="mileage_low">KM faible</option>
                        </select>
                    </div>
                </div>

                {{-- Actions et indicateurs --}}
                <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-t border-gray-200 pt-4">
                    <div class="flex items-center gap-2">
                        <button 
                            wire:click="resetFilters"
                            class="inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>

                        @if($search || $vehicleFilter || $methodFilter || $dateFrom || $dateTo || $authorFilter || $mileageMin || $mileageMax)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <x-iconify icon="lucide:filter" class="w-3 h-3" />
                            Filtres actifs
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            <span class="font-semibold">{{ $readings->total() }}</span> résultat(s)
                        </div>
                        
                        @can('export mileage readings')
                        <div class="flex gap-2">
                            <button wire:click="exportCsv" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                <x-iconify icon="lucide:file-text" class="w-3.5 h-3.5" />
                                CSV
                            </button>
                            <button wire:click="exportExcel" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                <x-iconify icon="lucide:file-spreadsheet" class="w-3.5 h-3.5" />
                                Excel
                            </button>
                            <button wire:click="exportPdf" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                <x-iconify icon="lucide:file" class="w-3.5 h-3.5" />
                                PDF
                            </button>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            TABLE DONNÉES OPTIMISÉE
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th wire:click="sortBy('vehicle')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:car" class="w-4 h-4" />
                                    <span>Véhicule</span>
                                    @if($sortField === 'vehicle')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4 text-blue-600" />
                                    @endif
                                </div>
                            </th>

                            <th wire:click="sortBy('recorded_at')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:calendar" class="w-4 h-4" />
                                    <span>Date</span>
                                    @if($sortField === 'recorded_at')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4 text-blue-600" />
                                    @endif
                                </div>
                            </th>

                            <th wire:click="sortBy('mileage')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:gauge" class="w-4 h-4" />
                                    <span>Kilométrage</span>
                                    @if($sortField === 'mileage')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4 text-blue-600" />
                                    @endif
                                </div>
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:trending-up" class="w-4 h-4" />
                                    <span>Différence</span>
                                </div>
                            </th>

                            <th wire:click="sortBy('recording_method')"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:settings" class="w-4 h-4" />
                                    <span>Méthode</span>
                                    @if($sortField === 'recording_method')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-4 h-4 text-blue-600" />
                                    @endif
                                </div>
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:user" class="w-4 h-4" />
                                    <span>Auteur</span>
                                </div>
                            </th>

                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($readings as $reading)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <x-iconify icon="lucide:car" class="w-5 h-5 text-gray-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $reading->vehicle->registration_plate }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $reading->vehicle->brand }} {{ $reading->vehicle->model }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm text-gray-900">
                                        {{ $reading->recorded_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $reading->recorded_at->format('H:i') }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($reading->mileage) }} km
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $difference = $reading->previous_mileage 
                                        ? $reading->mileage - $reading->previous_mileage
                                        : null;
                                @endphp
                                @if($difference !== null)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <x-iconify icon="lucide:plus" class="w-3 h-3" />
                                        {{ number_format($difference) }} km
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">Premier relevé</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($reading->recording_method === 'manual')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <x-iconify icon="lucide:hand" class="w-3.5 h-3.5" />
                                        Manuel
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <x-iconify icon="lucide:cpu" class="w-3.5 h-3.5" />
                                        Automatique
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-2">
                                        <x-iconify icon="lucide:user" class="w-4 h-4 text-gray-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-900">
                                            {{ $reading->user->name ?? 'Système' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.vehicles.show', $reading->vehicle_id) }}" 
                                       class="p-1.5 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Voir détails">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4" />
                                    </a>
                                    @can('update mileage readings')
                                    <button wire:click="editReading({{ $reading->id }})"
                                            class="p-1.5 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Modifier">
                                        <x-iconify icon="lucide:edit" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @can('delete mileage readings')
                                    <button wire:click="confirmDelete({{ $reading->id }})"
                                            class="p-1.5 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Supprimer">
                                        <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12">
                                <div class="text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        <x-iconify icon="lucide:gauge" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun relevé trouvé</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if($search || $vehicleFilter || $methodFilter || $dateFrom || $dateTo)
                                            Aucun relevé ne correspond à vos critères de recherche.
                                        @else
                                            Aucun relevé kilométrique n'a été enregistré.
                                        @endif
                                    </p>
                                    @if($search || $vehicleFilter || $methodFilter || $dateFrom || $dateTo)
                                    <button wire:click="resetFilters" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                                        Effacer les filtres
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===============================================
                PAGINATION ULTRA-PRO
            =============================================== --}}
            @if($readings->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Affichage de 
                        <span class="font-semibold">{{ $readings->firstItem() }}</span>
                        à 
                        <span class="font-semibold">{{ $readings->lastItem() }}</span>
                        sur 
                        <span class="font-semibold">{{ $readings->total() }}</span>
                        relevés
                    </div>
                    
                    <div>
                        {{ $readings->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</section>

{{-- Loading State --}}
<div wire:loading.flex 
     wire:target="search, vehicleFilter, methodFilter, dateFrom, dateTo, authorFilter, mileageMin, mileageMax, perPage, sortBy, resetFilters"
     class="fixed inset-0 z-50 bg-black bg-opacity-25 items-center justify-center">
    <div class="bg-white rounded-lg px-6 py-4 shadow-xl">
        <div class="flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Chargement...</span>
        </div>
    </div>
</div>
</div>
