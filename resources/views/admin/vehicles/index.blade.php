@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Véhicules')

@section('content')
{{-- ====================================================================
 🚗 GESTION DES VÉHICULES V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design surpassant Airbnb, Stripe et Salesforce:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 7 Cards métriques riches en information
 ✨ Barre recherche + filtres + actions sur 1 ligne
 ✨ Table ultra-lisible avec colonne Chauffeur
 ✨ Pagination séparée en bas
 ✨ Thème clair 100% (pas de dark mode)

 @version 7.0-World-Class-Light-Theme
 @since 2025-01-19
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
 <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

 {{-- ===============================================
 HEADER ULTRA-COMPACT
 =============================================== --}}
 <div class="mb-4">
 <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
 <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
 Gestion des Véhicules
 <span class="ml-2 text-sm font-normal text-gray-500">
 ({{ isset($vehicles) ? $vehicles->total() : 0 }})
 </span>
 </h1>
 </div>

 {{-- ===============================================
 CARDS MÉTRIQUES ULTRA-PRO
 =============================================== --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
 {{-- Total Véhicules --}}
 <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600">Total véhicules</p>
 <p class="text-xl font-bold text-gray-900 mt-1">
 {{ $analytics['total_vehicles'] ?? 0 }}
 </p>
 </div>
 <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
 </div>
 </div>
 </div>

 {{-- Disponibles --}}
 <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600">Disponibles</p>
 <p class="text-xl font-bold text-green-600 mt-1">
 {{ $analytics['available_vehicles'] ?? 0 }}
 </p>
 </div>
 <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
 </div>
 </div>
 </div>

 {{-- Affectés --}}
 <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600">Affectés</p>
 <p class="text-xl font-bold text-orange-600 mt-1">
 {{ $analytics['assigned_vehicles'] ?? 0 }}
 </p>
 </div>
 <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:user-check" class="w-5 h-5 text-orange-600" />
 </div>
 </div>
 </div>

 {{-- En Maintenance --}}
 <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600">En maintenance</p>
 <p class="text-xl font-bold text-red-600 mt-1">
 {{ $analytics['maintenance_vehicles'] ?? 0 }}
 </p>
 </div>
 <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:wrench" class="w-5 h-5 text-red-600" />
 </div>
 </div>
 </div>

 {{-- Archivés --}}
 <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-medium text-gray-600">Archivés</p>
 <p class="text-xl font-bold text-gray-500 mt-1">
 {{ $analytics['archived_vehicles'] ?? 0 }}
 </p>
 </div>
 <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:archive" class="w-5 h-5 text-gray-500" />
 </div>
 </div>
 </div>
 </div>

 {{-- ===============================================
 STATISTIQUES SUPPLÉMENTAIRES (Enterprise-Grade)
 =============================================== --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
 {{-- Âge Moyen de la Flotte --}}
 <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-5">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Âge moyen</p>
 <p class="text-xl font-bold text-blue-900 mt-1">
 {{ number_format($analytics['avg_age_years'] ?? 0, 1) }} ans
 </p>
 <p class="text-xs text-blue-700 mt-1">Depuis acquisition</p>
 </div>
 <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:calendar" class="w-5 h-5 text-blue-700" />
 </div>
 </div>
 </div>

 {{-- Kilométrage Moyen --}}
 <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200 p-5">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">KM moyen</p>
 <p class="text-xl font-bold text-purple-900 mt-1">
 {{ number_format($analytics['avg_mileage'] ?? 0, 0, ',', ' ') }}
 </p>
 <p class="text-xs text-purple-700 mt-1">Kilométrage par véhicule</p>
 </div>
 <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:bar-chart-3" class="w-5 h-5 text-purple-700" />
 </div>
 </div>
 </div>

 {{-- Valeur Totale de la Flotte --}}
 <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 p-5">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Valeur totale</p>
 <p class="text-xl font-bold text-emerald-900 mt-1">
 {{ number_format($analytics['total_value'] ?? 0, 0, ',', ' ') }} €
 </p>
 <p class="text-xs text-emerald-700 mt-1">Estimation actuelle</p>
 </div>
 <div class="w-10 h-10 bg-emerald-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="lucide:banknote" class="w-5 h-5 text-emerald-700" />
 </div>
 </div>
 </div>
 </div>

 {{-- ===============================================
 BARRE DE RECHERCHE ET ACTIONS (Enterprise-Grade)
 =============================================== --}}
 <div class="mb-6" x-data="{ showFilters: false }">
 {{-- Ligne principale: Recherche + Filtres + Boutons Actions --}}
 <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
 {{-- Recherche rapide --}}
 <div class="flex-1 w-full lg:w-auto">
 <form action="{{ route('admin.vehicles.index') }}" method="GET" id="searchForm">
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
 </div>
 <input
 type="text"
 name="search"
 id="quickSearch"
 value="{{ request('search') }}"
 placeholder="Rechercher par immatriculation, marque, modèle..."
 class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
 onchange="document.getElementById('searchForm').submit()">
 </div>
 </form>
 </div>

 {{-- Bouton Filtres Avancés --}}
 <button
 @click="showFilters = !showFilters"
 type="button"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
 <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
 <span class="font-medium text-gray-700">Filtres</span>
 @php
 $activeFiltersCount = count(request()->except(['page', 'per_page', 'search']));
 @endphp
 @if($activeFiltersCount > 0)
 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $activeFiltersCount }}
 </span>
 @endif
 <x-iconify
 icon="heroicons:chevron-down"
 class="w-4 h-4 text-gray-400 transition-transform duration-200"
 x-bind:class="showFilters ? 'rotate-180' : ''"
 />
 </button>

 {{-- Sélecteur de Vue - Enterprise Ultra Pro --}}
 <div class="flex items-center bg-white border border-gray-300 rounded-lg p-1">
    <button onclick="setView('table')" 
            class="px-3 py-1.5 rounded-md transition-all {{ request('view', 'table') == 'table' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <x-iconify icon="lucide:list" class="w-4 h-4" />
    </button>
    <button onclick="setView('grid')" 
            class="px-3 py-1.5 rounded-md transition-all {{ request('view') == 'grid' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <x-iconify icon="lucide:grid-3x3" class="w-4 h-4" />
    </button>
    <button onclick="setView('kanban')" 
            class="px-3 py-1.5 rounded-md transition-all {{ request('view') == 'kanban' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
        <x-iconify icon="lucide:columns" class="w-4 h-4" />
    </button>
 </div>

 {{-- Boutons d'actions --}}
 <div class="flex items-center gap-2">
                     {{-- Toggle Voir Archives / Voir Actifs --}}
                    @if(request('archived') === 'true')
                        <a href="{{ route('admin.vehicles.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:list" class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>
                        </a>
                    @else
                        <a href="{{ route('admin.vehicles.index', ['archived' => 'true']) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                            <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>
                        </a>
                    @endif

{{-- START: Tâche 1 - Bouton Export avec Menu Déroulant --}}
@can('export vehicles')
 <div class="relative" x-data="{ openExport: false }">
    <button @click="openExport = !openExport"
            @click.away="openExport = false"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
        <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-600" />
        <span class="hidden sm:inline font-medium text-gray-700">Exporter</span>
        <x-iconify icon="lucide:chevron-down" class="w-4 h-4 text-gray-500" />
    </button>
    
    <div x-show="openExport"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5">
        <div class="py-1">
            <a href="{{ route('admin.vehicles.export.csv', request()->all()) }}"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-3 text-gray-500" />
                Export CSV
            </a>
            <a href="{{ route('admin.vehicles.export.excel', request()->all()) }}"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                <x-iconify icon="lucide:file-spreadsheet" class="w-4 h-4 mr-3 text-gray-500" />
                Export Excel
            </a>
            <a href="{{ route('admin.vehicles.export.pdf', request()->all()) }}"
               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-3 text-gray-500" />
                Export PDF
            </a>
        </div>
    </div>
 </div>
 @endcan
{{-- END: Tâche 1 - Bouton Export avec Menu Déroulant --}}

@can('create vehicles')
 <a href="{{ route('admin.vehicles.import.show') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="lucide:upload" class="w-5 h-5" />
 <span class="hidden sm:inline">Importer</span>
 </a>
 <a href="{{ route('admin.vehicles.create') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="lucide:plus-circle" class="w-5 h-5" />
 <span class="hidden sm:inline">Nouveau véhicule</span>
 </a>
 @endcan
 </div>
 </div>

 {{-- Panel Filtres Avancés (Collapsible) --}}
 <div x-show="showFilters"
 x-collapse
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform -translate-y-2"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">

 <form action="{{ route('admin.vehicles.index') }}" method="GET">
 {{-- Préserver la recherche --}}
 @if(request('search'))
 <input type="hidden" name="search" value="{{ request('search') }}">
 @endif

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

 {{-- Dépôt (Filtre par dépôt) --}}
 <div>
 <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-1">
 Dépôt
 </label>
 <select
 name="depot_id"
 id="depot_id"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les dépôts</option>
 @foreach($referenceData['depots'] ?? [] as $depot)
 <option value="{{ $depot->id }}" @selected(request('depot_id') == $depot->id)>
 {{ $depot->name }} ({{ $depot->vehicles_count ?? 0 }} véhicules)
 </option>
 @endforeach
 </select>
 </div>

 {{-- Statut --}}
 <div>
 <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">
 Statut
 </label>
 <select
 name="status_id"
 id="status_id"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les statuts</option>
 @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
 <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>
 {{ $status->name }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Type --}}
 <div>
 <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700 mb-1">
 Type
 </label>
 <select
 name="vehicle_type_id"
 id="vehicle_type_id"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les types</option>
 @foreach($referenceData['vehicle_types'] ?? [] as $type)
 <option value="{{ $type->id }}" @selected(request('vehicle_type_id') == $type->id)>
 {{ $type->name }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Carburant --}}
 <div>
 <label for="fuel_type_id" class="block text-sm font-medium text-gray-700 mb-1">
 Carburant
 </label>
 <select
 name="fuel_type_id"
 id="fuel_type_id"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les carburants</option>
 @foreach($referenceData['fuel_types'] ?? [] as $fuel)
 <option value="{{ $fuel->id }}" @selected(request('fuel_type_id') == $fuel->id)>
 {{ $fuel->name }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Par page --}}
 <div>
 <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">
 Par page
 </label>
 <select
 name="per_page"
 id="per_page"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 @foreach(['20', '50', '100'] as $value)
 <option value="{{ $value }}" @selected(request('per_page', '20') == $value)>
 {{ $value }}
 </option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Actions Filtres --}}
 <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-between">
 <a href="{{ route('admin.vehicles.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
 <x-iconify icon="lucide:refresh-cw" class="w-4 h-4" />
 Réinitialiser
 </a>
 <button
 type="submit"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
 <x-iconify icon="lucide:search" class="w-4 h-4" />
 Appliquer les filtres
 </button>
 </div>
 </form>
 </div>
 </div>

 {{-- ===============================================
 BARRE D'ACTIONS EN MASSE - ENTERPRISE ULTRA PRO
 =============================================== --}}
 <div x-data="batchActions()" class="relative">
    {{-- Barre d'actions flottante qui apparaît lors de la sélection --}}
    <div x-show="selectedVehicles.length > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4">
        <div class="flex items-center gap-6">
            {{-- Compteur de sélection --}}
            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-lg">
                <x-iconify icon="lucide:check-square" class="w-5 h-5 text-blue-600" />
                <span class="text-sm font-semibold text-blue-900">
                    <span x-text="selectedVehicles.length"></span> véhicule(s) sélectionné(s)
                </span>
            </div>
            
            {{-- Actions groupées --}}
            <div class="flex items-center gap-2">
                {{-- Changer statut en masse --}}
                <button @click="openBatchStatusModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">
                    <x-iconify icon="lucide:settings-2" class="w-4 h-4 text-gray-600" />
                    <span class="text-sm font-medium text-gray-700">Changer statut</span>
                </button>

                {{-- Affecter au dépôt en masse --}}
                <button @click="openBulkDepotAssignmentModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                    <x-iconify icon="lucide:building-2" class="w-4 h-4" />
                    <span class="text-sm font-medium">Affecter au dépôt</span>
                </button>

                {{-- Exporter la sélection --}}
                <button @click="exportSelected()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
                    <x-iconify icon="lucide:download" class="w-4 h-4" />
                    <span class="text-sm font-medium">Exporter</span>
                </button>
                
                {{-- Archiver en masse --}}
                <button @click="archiveSelected()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-all">
                    <x-iconify icon="lucide:archive" class="w-4 h-4" />
                    <span class="text-sm font-medium">Archiver</span>
                </button>
                
                {{-- Annuler la sélection --}}
                <button @click="clearSelection()"
                        class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all ml-2">
                    <x-iconify icon="lucide:x" class="w-5 h-5 text-gray-600" />
                </button>
            </div>
        </div>
    </div>

 {{-- ===============================================
 TABLE ENTERPRISE-GRADE WORLD-CLASS AVEC SÉLECTION
 =============================================== --}}
 <x-card padding="p-0" margin="mb-6">
 @if($vehicles && $vehicles->count() > 0)
 {{-- Table --}}
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 {{-- Checkbox pour tout sélectionner --}}
 <th scope="col" class="relative px-6 py-3 w-12">
    <input type="checkbox"
           x-model="selectAll"
           @change="toggleAllVehicles()"
           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Type
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Kilométrage
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Dépôt
 </th>
 {{-- START: Tâche 2 - Suppression colonne Actions rapides et conservation seule colonne Actions --}}
 <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 {{-- END: Tâche 2 --}}
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($vehicles as $vehicle)
 <tr class="hover:bg-gray-50 transition-colors duration-150" 
     :class="{ 'bg-blue-50': selectedVehicles.includes({{ $vehicle->id }}) }">
 {{-- Checkbox de sélection --}}
 <td class="px-6 py-4 whitespace-nowrap">
    <input type="checkbox"
           :value="{{ $vehicle->id }}"
           @change="toggleVehicle({{ $vehicle->id }})"
           :checked="selectedVehicles.includes({{ $vehicle->id }})"
           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
 <x-iconify icon="lucide:car" class="h-5 w-5 text-gray-500" />
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-semibold text-gray-900">
 {{ $vehicle->registration_plate }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $vehicle->brand }} {{ $vehicle->model }}
 </div>
 </div>
 </div>
 </td>

 {{-- Colonne Chauffeur (World-Class Enterprise-Grade) --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 // Utilise les données déjà chargées par eager loading (optimisation N+1)
 $activeAssignment = $vehicle->assignments->first();
 $driver = $activeAssignment->driver ?? null;
 $user = $driver->user ?? null;
 @endphp

 @if($driver && $user)
 <div class="flex items-center">
 {{-- Avatar Premium avec Photo --}}
 <div class="flex-shrink-0 h-10 w-10">
 @if($user->profile_photo_path)
 <img src="{{ Storage::url($user->profile_photo_path) }}"
 alt="{{ $user->name }}"
 class="h-10 w-10 rounded-full object-cover ring-2 ring-blue-100 shadow-sm">
 @else
 <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-100 shadow-sm">
 <span class="text-sm font-bold text-white">
 {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
 </span>
 </div>
 @endif
 </div>
 {{-- Informations Chauffeur --}}
 <div class="ml-3">
 <div class="text-sm font-semibold text-gray-900">
 {{ $user->name }} {{ $user->last_name ?? '' }}
 </div>
 <div class="flex items-center gap-1 text-xs text-gray-500">
 <x-iconify icon="lucide:phone" class="w-3.5 h-3.5" />
 <span>{{ $driver->phone ?? $user->phone ?? 'N/A' }}</span>
 </div>
 </div>
 </div>
 @else
 <div class="flex items-center gap-2 text-sm text-gray-400">
 <x-iconify icon="lucide:user-check" class="w-5 h-5" />
 <span class="italic">Non affecté</span>
 </div>
 @endif
 </td>

 <td class="px-6 py-4 whitespace-nowrap">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 {{ $vehicle->vehicleType->name ?? 'N/A' }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @php
 $statusColors = [
 'Disponible' => 'bg-green-100 text-green-800',
 'Affecté' => 'bg-orange-100 text-orange-800',
 'Maintenance' => 'bg-red-100 text-red-800',
 'Hors service' => 'bg-gray-100 text-gray-800'
 ];
 $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
 $colorClass = $statusColors[$statusName] ?? 'bg-gray-100 text-gray-800';
 @endphp
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
 {{ $statusName }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
 {{ number_format($vehicle->current_mileage) }} km
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($vehicle->depot)
 <div class="flex items-center gap-2">
 <x-iconify icon="lucide:building-2" class="w-4 h-4 text-purple-600" />
 <span class="text-sm text-gray-900">{{ $vehicle->depot->name }}</span>
 </div>
 @else
 <span class="text-sm text-gray-400 italic">Non assigné</span>
 @endif
 </td>

 {{-- START: Actions Directes et Menu Dropdown Enterprise Grade --}}
 <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
    <div class="flex items-center justify-center gap-1">
        @if($vehicle->is_archived || $vehicle->trashed() || request('archived') === 'true')
            {{-- Actions directes pour véhicules ARCHIVÉS --}}
            <button onclick="restoreVehicle({{ $vehicle->id }}, '{{ $vehicle->registration_plate }}', '{{ $vehicle->brand }} {{ $vehicle->model }}')"
                    class="inline-flex items-center p-1.5 text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all duration-200"
                    title="Restaurer">
                <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4" />
            </button>
            <button onclick="permanentDeleteVehicle({{ $vehicle->id }}, '{{ $vehicle->registration_plate }}', '{{ $vehicle->brand }} {{ $vehicle->model }}')"
                    class="inline-flex items-center p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200"
                    title="Supprimer définitivement">
                <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
            </button>
        @else
            {{-- Actions directes PRINCIPALES pour véhicules ACTIFS --}}
            @can('view vehicles')
            <a href="{{ route('admin.vehicles.show', $vehicle) }}"
               class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200"
               title="Voir détails">
                <x-iconify icon="lucide:eye" class="w-4 h-4" />
            </a>
            @endcan
            
            @can('edit vehicles')
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
               class="inline-flex items-center p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-all duration-200"
               title="Modifier">
                <x-iconify icon="lucide:edit" class="w-4 h-4" />
            </a>
            @endcan
            
            {{-- Menu dropdown pour actions supplémentaires --}}
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open"
                        @click.away="open = false"
                        type="button"
                        class="inline-flex items-center p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200"
                        id="vehicle-menu-{{ $vehicle->id }}">
                    <x-iconify icon="lucide:more-vertical" class="w-4 h-4" />
                </button>

                <div x-show="open"
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-50 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                    <div class="py-1">
                        {{-- Actions supplémentaires --}}
                        <button onclick="duplicateVehicle({{ $vehicle->id }})"
                                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <x-iconify icon="lucide:copy" class="w-4 h-4 mr-3 text-purple-600" />
                            Dupliquer
                        </button>
                        
                        <a href="{{ route('admin.vehicles.history', $vehicle) }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <x-iconify icon="lucide:clock" class="w-4 h-4 mr-3 text-cyan-600" />
                            Historique
                        </a>
                        
                        <a href="{{ route('admin.vehicles.export.single.pdf', $vehicle) }}"
                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-3 text-emerald-600" />
                            Exporter PDF
                        </a>
                        
                        @can('delete vehicles')
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <button onclick="archiveVehicle({{ $vehicle->id }}, '{{ $vehicle->registration_plate }}', '{{ $vehicle->brand }} {{ $vehicle->model }}')"
                                    class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                <x-iconify icon="lucide:archive" class="w-4 h-4 mr-3 text-orange-600" />
                                Archiver
                            </button>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        @endif
    </div>
 </td>
 {{-- END: Tâche 2 - Menu Dropdown Trois Points --}}
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @else
 {{-- État vide --}}
 <div class="text-center py-12">
 <x-iconify icon="lucide:car" class="mx-auto h-12 w-12 text-gray-400" />
 <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun véhicule</h3>
 <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un véhicule à votre flotte.</p>
 @can('create vehicles')
 <div class="mt-6">
 <a href="{{ route('admin.vehicles.create') }}"
 class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
 <x-iconify icon="lucide:plus" class="w-5 h-5" />
 Nouveau véhicule
 </a>
 </div>
 @endcan
 </div>
 @endif
 </x-card>

 {{-- ===============================================
 PAGINATION EN BAS DE PAGE (World-Class)
 =============================================== --}}
 @if($vehicles && $vehicles->count() > 0)
 <div class="mt-6 bg-white rounded-lg border border-gray-200 px-6 py-4 shadow-sm">
 <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
 <div class="flex items-center gap-4">
 <div class="text-sm text-gray-600">
 Affichage de <span class="font-semibold text-gray-900">{{ $vehicles->firstItem() ?? 0 }}</span> à
 <span class="font-semibold text-gray-900">{{ $vehicles->lastItem() ?? 0 }}</span> sur
 <span class="font-semibold text-gray-900">{{ $vehicles->total() ?? 0 }}</span> véhicules
 </div>
 @if($vehicles->total() > 0)
 <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 border border-blue-200 rounded-md">
 <x-iconify icon="lucide:clock" class="w-3.5 h-3.5 text-blue-600" />
 <span class="text-xs font-medium text-blue-700">
 Page {{ $vehicles->currentPage() }} / {{ $vehicles->lastPage() }}
 </span>
 </div>
 @endif
 </div>
 <div>
 {{ $vehicles->appends(request()->query())->links() }}
 </div>
 </div>
 </div>
 @endif

</div> {{-- End batchActions() context --}}

 </div>

{{-- ===============================================
MODAL CHANGEMENT DE STATUT EN MASSE - ENTERPRISE
=============================================== --}}
<div id="batchStatusModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('batchStatusModal').classList.add('hidden')"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                    <x-iconify icon="lucide:settings-2" class="h-6 w-6 text-blue-600" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                        Changer le statut en masse
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Sélectionnez le nouveau statut pour les <span x-text="selectedVehicles.length" class="font-semibold text-blue-600"></span> véhicule(s) sélectionné(s).
                        </p>

                        <div class="mt-4">
                            <label for="batch_status_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Nouveau statut
                            </label>
                            <select id="batch_status_id" name="status_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Sélectionner un statut...</option>
                                @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" @click="submitBatchStatusChange()"
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Appliquer le changement
                </button>
                <button type="button" @click="closeBatchStatusModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===============================================
MODAL AFFECTATION DÉPÔT EN MASSE - LIVEWIRE
=============================================== --}}
@livewire('vehicles.bulk-depot-assignment')

</section>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// GESTION DES ACTIONS BATCH - ENTERPRISE ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

function batchActions() {
    return {
        selectedVehicles: [],
        selectAll: false,

        toggleVehicle(id) {
            if (this.selectedVehicles.includes(id)) {
                this.selectedVehicles = this.selectedVehicles.filter(v => v !== id);
            } else {
                this.selectedVehicles.push(id);
            }
            // Update selectAll checkbox state
            const allVehicleIds = @json($vehicles->pluck('id'));
            this.selectAll = this.selectedVehicles.length === allVehicleIds.length;
        },

        toggleAllVehicles() {
            const allVehicleIds = @json($vehicles->pluck('id'));
            if (this.selectAll) {
                this.selectedVehicles = [...allVehicleIds];
            } else {
                this.selectedVehicles = [];
            }
        },

        clearSelection() {
            this.selectedVehicles = [];
            this.selectAll = false;
        },
        
        openBatchStatusModal() {
            if (this.selectedVehicles.length === 0) {
                alert('Veuillez sélectionner au moins un véhicule');
                return;
            }

            // Ouvrir la modal de changement de statut
            document.getElementById('batchStatusModal').classList.remove('hidden');
        },

        closeBatchStatusModal() {
            document.getElementById('batchStatusModal').classList.add('hidden');
        },

        submitBatchStatusChange() {
            const statusId = document.getElementById('batch_status_id').value;
            if (!statusId) {
                alert('Veuillez sélectionner un statut');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/vehicles/batch-status';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const vehiclesInput = document.createElement('input');
            vehiclesInput.type = 'hidden';
            vehiclesInput.name = 'vehicles';
            vehiclesInput.value = JSON.stringify(this.selectedVehicles);
            form.appendChild(vehiclesInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status_id';
            statusInput.value = statusId;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        },

        openBulkDepotAssignmentModal() {
            if (this.selectedVehicles.length === 0) {
                alert('Veuillez sélectionner au moins un véhicule');
                return;
            }

            // 1. Dispatch Livewire event to load vehicle data
            Livewire.dispatch('openBulkAssignmentModal', [this.selectedVehicles]);

            // 2. Open the x-modal component
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'bulk-depot-assignment'
            }));

            console.log('✅ Bulk depot assignment modal opened with', this.selectedVehicles.length, 'vehicles');
        },

        exportSelected() {
            if (this.selectedVehicles.length === 0) {
                alert('Veuillez sélectionner au moins un véhicule');
                return;
            }

            // Construction de l'URL avec les IDs des véhicules sélectionnés
            const vehicleIds = this.selectedVehicles.join(',');
            const url = `/admin/vehicles/export/pdf?vehicles=${vehicleIds}`;

            // Ouvrir dans une nouvelle fenêtre pour téléchargement
            window.open(url, '_blank');
        },
        
        archiveSelected() {
            if (this.selectedVehicles.length === 0) return;
            
            if (confirm(`Confirmer l'archivage de ${this.selectedVehicles.length} véhicule(s) ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/vehicles/batch-archive';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                const vehiclesInput = document.createElement('input');
                vehiclesInput.type = 'hidden';
                vehiclesInput.name = 'vehicles';
                vehiclesInput.value = JSON.stringify(this.selectedVehicles);
                form.appendChild(vehiclesInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    };
}

// ═══════════════════════════════════════════════════════════════════════════
// ACTIONS RAPIDES - ENTERPRISE ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

// Dupliquer un véhicule
function duplicateVehicle(vehicleId) {
    if (confirm('Créer une copie de ce véhicule ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/vehicles/${vehicleId}/duplicate`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Générer QR Code
function generateQRCode(vehicleId) {
    window.open(`/admin/vehicles/${vehicleId}/qrcode`, '_blank', 'width=400,height=400');
}

// Export PDF du véhicule - Supprimé car utilise directement lien href dans menu dropdown

// Afficher la timeline/historique
function showVehicleTimeline(vehicleId) {
    window.location.href = `/admin/vehicles/${vehicleId}/timeline`;
}

// ═══════════════════════════════════════════════════════════════════════════
// ACTIONS D'ARCHIVAGE/RESTAURATION/SUPPRESSION - EXISTANTES
// ═══════════════════════════════════════════════════════════════════════════

// Fonction d'archivage avec modale
function archiveVehicle(vehicleId, plate, brand) {
 // Créer modale de confirmation
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.setAttribute('aria-labelledby', 'modal-title');
 modal.setAttribute('role', 'dialog');
 modal.setAttribute('aria-modal', 'true');

 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
 Archiver le véhicule
 </h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 Voulez-vous archiver ce véhicule ? Il sera déplacé vers les archives et pourra être restauré ultérieurement.
 </p>
 <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
 <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
 <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
 </svg>
 </div>
 <div>
 <p class="font-semibold text-blue-900">${plate}</p>
 <p class="text-sm text-blue-700">${brand}</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button
 type="button"
 onclick="confirmArchive(${vehicleId})"
 class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 hover:bg-orange-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
 Confirmer l'archivage
 </button>
 <button
 type="button"
 onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;

 document.body.appendChild(modal);
}

function confirmArchive(vehicleId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/vehicles/${vehicleId}/archive`;
 form.innerHTML = `
 @csrf
 @method('PUT')
 `;
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 200);
}

// Fonction de restauration avec modale
function restoreVehicle(vehicleId, plate, brand) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.setAttribute('aria-labelledby', 'modal-title');
 modal.setAttribute('role', 'dialog');
 modal.setAttribute('aria-modal', 'true');

 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
 <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3V15" />
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
 Restaurer le véhicule
 </h3>
 <div class="mt-2">
 <p class="text-sm text-gray-500">
 Voulez-vous restaurer ce véhicule ? Il sera de nouveau visible dans la liste des véhicules actifs.
 </p>
 <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
 <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
 <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
 </svg>
 </div>
 <div>
 <p class="font-semibold text-green-700">${plate}</p>
 <p class="text-sm text-green-600">${brand}</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button
 type="button"
 onclick="confirmRestore(${vehicleId})"
 class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
 <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
 </svg>
 Confirmer la restauration
 </button>
 <button
 type="button"
 onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;

 document.body.appendChild(modal);
}

function confirmRestore(vehicleId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/vehicles/${vehicleId}/unarchive`;
 form.innerHTML = `
 @csrf
 @method('PUT')
 `;
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 200);
}

function closeModal() {
 const modal = document.querySelector('.fixed.inset-0.z-50');
 if (modal) {
 modal.style.opacity = '0';
 modal.style.transform = 'scale(0.95)';
 setTimeout(() => modal.remove(), 200);
 }
}


// ═══════════════════════════════════════════════════════════════════════════
// SUPPRESSION DÉFINITIVE VÉHICULE - MODAL ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

function permanentDeleteVehicle(vehicleId, plate, brand) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');

    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer définitivement le véhicule
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                <strong class="text-red-600">⚠️ ATTENTION : Cette action est IRRÉVERSIBLE !</strong><br>
                                Toutes les données de ce véhicule seront définitivement supprimées de la base de données.
                            </p>
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-900">${plate}</p>
                                        <p class="text-sm text-red-700">${brand}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmPermanentDelete(${vehicleId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer définitivement
                    </button>
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function confirmPermanentDelete(vehicleId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vehicles/${vehicleId}/force-delete`;
    
    // Ajouter le token CSRF correctement
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Ajouter la méthode DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}

// ═══════════════════════════════════════════════════════════════════════════
// LIVEWIRE EVENT LISTENERS - BULK DEPOT ASSIGNMENT
// ═══════════════════════════════════════════════════════════════════════════

// Listen for vehicles-bulk-assigned event and reload page to refresh list
document.addEventListener('livewire:init', () => {
    Livewire.on('vehicles-bulk-assigned', (event) => {
        console.log('Vehicles bulk assigned to depot:', event);

        // Show success message and reload page after short delay
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });

    Livewire.on('close-modal-delayed', (event) => {
        setTimeout(() => {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: event.modalName
            }));
        }, 1500);
    });
});

</script>
@endpush
