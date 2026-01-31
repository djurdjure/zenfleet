@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Chauffeurs')

@section('content')
{{-- ====================================================================
 👨‍💼 GESTION DES CHAUFFEURS V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design surpassant Airbnb, Stripe et Salesforce:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 7 Cards métriques riches en information
 ✨ Barre recherche + filtres + actions sur 1 ligne
 ✨ Table ultra-lisible avec statut visuel
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
                <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                Gestion des Chauffeurs
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ isset($drivers) ? $drivers->total() : 0 }})
                </span>
            </h1>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Total Chauffeurs --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total chauffeurs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $analytics['total_drivers'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Disponibles --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disponibles</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $analytics['available_drivers'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:user-check" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- En Mission --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En mission</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">
                            {{ $analytics['active_drivers'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:briefcase" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- En Repos --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En repos</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">
                            {{ $analytics['resting_drivers'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:pause-circle" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            STATISTIQUES SUPPLÉMENTAIRES (Enterprise-Grade)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Âge Moyen --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Âge moyen</p>
                        <p class="text-xl font-bold text-blue-900 mt-1">
                            {{ number_format($analytics['avg_age'] ?? 0, 0) }} ans
                        </p>
                        <p class="text-xs text-blue-700 mt-1">Expérience moyenne</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:user" class="w-5 h-5 text-blue-700" />
                    </div>
                </div>
            </div>

            {{-- Permis Valides --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Permis valides</p>
                        <p class="text-xl font-bold text-emerald-900 mt-1">
                            {{ $analytics['valid_licenses'] ?? 0 }}
                        </p>
                        <p class="text-xs text-emerald-700 mt-1">{{ number_format($analytics['valid_licenses_percent'] ?? 0, 1) }}% de la flotte</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:badge-check" class="w-5 h-5 text-emerald-700" />
                    </div>
                </div>
            </div>

            {{-- Ancienneté Moyenne --}}
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Ancienneté</p>
                        <p class="text-xl font-bold text-purple-900 mt-1">
                            {{ number_format($analytics['avg_seniority'] ?? 0, 1) }} ans
                        </p>
                        <p class="text-xs text-purple-700 mt-1">Dans l'entreprise</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:award" class="w-5 h-5 text-purple-700" />
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
                    <form action="{{ route('admin.drivers.index') }}" method="GET" id="searchForm">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                name="search"
                                id="quickSearch"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par nom, prénom, matricule..."
                                class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg text-sm shadow-sm transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400"
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
                        x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Bouton Archives (filtre visibility=archived) --}}
                    @if(request('visibility') === 'archived')
                    <a href="{{ route('admin.drivers.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:list" class="w-5 h-5" />
                        <span class="hidden lg:inline font-medium">Voir Actifs</span>
                    </a>
                    @else
                    <a href="{{ route('admin.drivers.index', ['visibility' => 'archived']) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                        <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>
                    </a>
                    @endif

                    {{-- Export Dropdown --}}
                    <div class="relative" x-data="{ exportOpen: false }">
                        <button
                            @click="exportOpen = !exportOpen"
                            @click.away="exportOpen = false"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                            <span class="hidden lg:inline font-medium text-gray-700">Export</span>
                            <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                        </button>

                        <div
                            x-show="exportOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('admin.drivers.export.pdf', request()->all()) }}"
                                    class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-text" class="w-4 h-4 text-red-600" />
                                    <span>Export PDF</span>
                                </a>
                                <a href="{{ route('admin.drivers.export.csv', request()->all()) }}"
                                    class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-spreadsheet" class="w-4 h-4 text-green-600" />
                                    <span>Export CSV</span>
                                </a>
                                <a href="{{ route('admin.drivers.export.excel', request()->all()) }}"
                                    class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-bar-chart" class="w-4 h-4 text-blue-600" />
                                    <span>Export Excel</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Import --}}
                    <a href="{{ route('admin.drivers.import.show') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:upload" class="w-5 h-5" />
                        <span class="font-medium">Importer</span>
                    </a>

                    {{-- Nouveau Chauffeur --}}
                    <a href="{{ route('admin.drivers.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                        <span class="font-medium">Nouveau Chauffeur</span>
                    </a>
                </div>
            </div>

            {{-- Panel Filtres Avancés (Collapsible) --}}
            <div
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
                style="display: none;">
                <form action="{{ route('admin.drivers.index') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Visibilité --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:eye" class="w-4 h-4 inline mr-1" />
                                Visibilité
                            </label>
                            <x-slim-select name="visibility" placeholder="Actifs uniquement">
                                <option value="active" {{ request('visibility', 'active') == 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                                <option value="archived" {{ request('visibility') == 'archived' ? 'selected' : '' }}>Archivés uniquement</option>
                                <option value="all" {{ request('visibility') == 'all' ? 'selected' : '' }}>Tous</option>
                            </x-slim-select>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                            <x-slim-select name="status_id" placeholder="Tous les statuts">
                                <option value="">Tous les statuts</option>
                                @foreach($driverStatuses ?? [] as $status)
                                <option value="{{ $status['id'] ?? $status->id }}" {{ request('status_id') == ($status['id'] ?? $status->id) ? 'selected' : '' }}>
                                    {{ $status['name'] ?? $status->name }}
                                </option>
                                @endforeach
                            </x-slim-select>
                        </div>

                        {{-- Catégorie Permis --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie permis</label>
                            <x-slim-select name="license_category" placeholder="Toutes les catégories">
                                <option value="">Toutes les catégories</option>
                                <option value="A1" {{ request('license_category') == 'A1' ? 'selected' : '' }}>A1</option>
                                <option value="A" {{ request('license_category') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ request('license_category') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="BE" {{ request('license_category') == 'BE' ? 'selected' : '' }}>BE</option>
                                <option value="C1" {{ request('license_category') == 'C1' ? 'selected' : '' }}>C1</option>
                                <option value="C1E" {{ request('license_category') == 'C1E' ? 'selected' : '' }}>C1E</option>
                                <option value="C" {{ request('license_category') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="CE" {{ request('license_category') == 'CE' ? 'selected' : '' }}>CE</option>
                                <option value="D" {{ request('license_category') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="DE" {{ request('license_category') == 'DE' ? 'selected' : '' }}>DE</option>
                                <option value="F" {{ request('license_category') == 'F' ? 'selected' : '' }}>F</option>
                            </x-slim-select>
                        </div>

                        {{-- Date d'embauche --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Embauché après</label>
                            <input
                                type="text"
                                name="hired_after"
                                id="hired_after_flatpickr"
                                value="{{ request('hired_after') }}"
                                placeholder="Sélectionner une date"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer">
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.drivers.index') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Réinitialiser
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <x-iconify icon="lucide:filter" class="w-4 h-4" />
                            Appliquer les filtres
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===============================================
            TABLE DES CHAUFFEURS (Enterprise-Grade)
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chauffeur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permis
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Véhicule Actuel
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($drivers as $driver)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- Chauffeur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        @if($driver->photo)
                                        <img
                                            src="{{ asset('storage/' . $driver->photo) }}"
                                            alt="{{ $driver->first_name }} {{ $driver->last_name }}"
                                            class="h-full w-full object-cover {{ $driver->deleted_at ? 'opacity-50 grayscale' : '' }}"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'h-10 w-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center\'><span class=\'text-sm font-semibold text-blue-700\'>{{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}</span></div>';" />
                                        @else
                                        <div class="h-10 w-10 {{ $driver->deleted_at ? 'bg-gradient-to-br from-gray-300 to-gray-400 opacity-70' : 'bg-gradient-to-br from-blue-100 to-indigo-100' }} rounded-full flex items-center justify-center">
                                            <span class="text-sm font-semibold {{ $driver->deleted_at ? 'text-gray-600' : 'text-blue-700' }}">
                                                {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium {{ $driver->deleted_at ? 'text-gray-500' : 'text-gray-900' }} flex items-center gap-2">
                                            {{ $driver->first_name }} {{ $driver->last_name }}
                                            @if($driver->deleted_at)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-700">
                                                <x-iconify icon="lucide:archive" class="w-3 h-3 mr-1" />
                                                Archivé
                                            </span>
                                            @endif
                                        </div>
                                        <div class="text-sm {{ $driver->deleted_at ? 'text-gray-400' : 'text-gray-500' }}">
                                            #{{ $driver->employee_number ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 flex items-center gap-1.5">
                                    <x-iconify icon="lucide:phone" class="w-4 h-4 text-gray-400" />
                                    {{ $driver->personal_phone ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500 flex items-center gap-1.5">
                                    <x-iconify icon="lucide:mail" class="w-4 h-4 text-gray-400" />
                                    {{ $driver->personal_email ?? 'N/A' }}
                                </div>
                            </td>

                            {{-- Permis --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $driver->license_number }}
                                </div>
                                @if($driver->license_category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Catégorie {{ $driver->license_category }}
                                </span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @livewire('admin.driver-status-badge-ultra-pro', ['driver' => $driver], key('status-'.$driver->id))
                            </td>

                            {{-- Véhicule Actuel --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($driver->activeAssignment && $driver->activeAssignment->vehicle)
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:car" class="w-4 h-4 text-blue-600" />
                                    <span class="font-medium text-gray-900">
                                        {{ $driver->activeAssignment->vehicle->registration_plate }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $driver->activeAssignment->vehicle->brand ?? '' }} {{ $driver->activeAssignment->vehicle->model ?? '' }}
                                </div>
                                @else
                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if($driver->deleted_at)
                                    {{-- Actions pour chauffeurs ARCHIVÉS --}}
                                    <button
                                        onclick="restoreDriver({{ $driver->id }}, '{{ $driver->first_name }} {{ $driver->last_name }}', '{{ $driver->employee_number }}')"
                                        class="inline-flex items-center p-1.5 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors"
                                        title="Restaurer">
                                        <x-iconify icon="lucide:rotate-ccw" class="w-5 h-5" />
                                    </button>
                                    <button
                                        onclick="permanentDeleteDriver({{ $driver->id }}, '{{ $driver->first_name }} {{ $driver->last_name }}', '{{ $driver->employee_number }}')"
                                        class="inline-flex items-center p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Supprimer définitivement">
                                        <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                                    </button>
                                    @else
                                    {{-- Actions pour chauffeurs ACTIFS --}}
                                    <a href="{{ route('admin.drivers.show', $driver) }}"
                                        class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Voir">
                                        <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                    </a>
                                    <a href="{{ route('admin.drivers.edit', $driver) }}"
                                        class="inline-flex items-center p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                        title="Modifier">
                                        <x-iconify icon="lucide:edit" class="w-5 h-5" />
                                    </a>
                                    <button
                                        onclick="archiveDriver({{ $driver->id }}, '{{ $driver->first_name }} {{ $driver->last_name }}', '{{ $driver->employee_number }}')"
                                        class="inline-flex items-center p-1.5 text-orange-600 hover:text-orange-900 hover:bg-orange-50 rounded-lg transition-colors"
                                        title="Archiver">
                                        <x-iconify icon="lucide:archive" class="w-5 h-5" />
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="lucide:users" class="w-16 h-16 text-gray-300 mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
                                    <p class="text-sm text-gray-500 mb-4">Commencez par ajouter votre premier chauffeur</p>
                                    <a href="{{ route('admin.drivers.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                                        Ajouter un chauffeur
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($drivers->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $drivers->links() }}
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // ═══════════════════════════════════════════════════════════════════════════
    // SUPPRESSION CHAUFFEUR AVEC MODAL STYLÉE - STANDARD ENTERPRISE
    // ═══════════════════════════════════════════════════════════════════════════

    function archiveDriver(driverId, driverName, employeeNumber) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeDriverModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m4.5-6.75v6.75M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Archiver le chauffeur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir archiver ce chauffeur ? Il sera déplacé dans les archives et ne sera plus visible dans la liste principale.
                            </p>
                            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">${driverName}</p>
                                        <p class="text-xs text-gray-500">Matricule: ${employeeNumber || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmArchiveDriver(${driverId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 hover:bg-orange-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Archiver
                    </button>
                    <button
                        type="button"
                        onclick="closeDriverModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    function confirmArchiveDriver(driverId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/drivers/${driverId}`;

        // Ajouter le token CSRF (correctement généré par Blade)
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
        closeDriverModal();
        setTimeout(() => form.submit(), 200);
    }

    function closeDriverModal() {
        const modal = document.querySelector('.fixed.inset-0.z-50');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => modal.remove(), 200);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // RESTAURATION CHAUFFEUR - MODAL ULTRA PRO
    // ═══════════════════════════════════════════════════════════════════════════

    function restoreDriver(driverId, driverName, employeeNumber) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeDriverModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Restaurer le chauffeur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir restaurer ce chauffeur ? Il sera réactivé et visible dans la liste principale.
                            </p>
                            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">${driverName}</p>
                                        <p class="text-xs text-gray-600">Matricule: ${employeeNumber}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmRestoreDriver(${driverId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Restaurer
                    </button>
                    <button
                        type="button"
                        onclick="closeDriverModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    function confirmRestoreDriver(driverId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/drivers/${driverId}/restore`;

        // Ajouter le token CSRF (correctement généré par Blade)
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Ajouter la méthode PATCH
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        closeDriverModal();
        setTimeout(() => form.submit(), 200);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SUPPRESSION DÉFINITIVE CHAUFFEUR - MODAL ULTRA PRO
    // ═══════════════════════════════════════════════════════════════════════════

    function permanentDeleteDriver(driverId, driverName, employeeNumber) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeDriverModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer définitivement le chauffeur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                <strong class="text-red-600">⚠️ ATTENTION : Cette action est IRRÉVERSIBLE !</strong><br>
                                Toutes les données de ce chauffeur seront définitivement supprimées de la base de données. Cette action ne peut pas être annulée.
                            </p>
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">${driverName}</p>
                                        <p class="text-xs text-gray-600">Matricule: ${employeeNumber}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmPermanentDeleteDriver(${driverId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer définitivement
                    </button>
                    <button
                        type="button"
                        onclick="closeDriverModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    function confirmPermanentDeleteDriver(driverId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/drivers/${driverId}/force-delete`;

        // Ajouter le token CSRF (correctement généré par Blade)
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
        closeDriverModal();
        setTimeout(() => form.submit(), 200);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 📅 FLATPICKR INITIALIZATION - ENTERPRISE-GRADE DATE PICKER
    // ═══════════════════════════════════════════════════════════════════════════
    document.addEventListener('DOMContentLoaded', function() {
        const flatpickrConfig = {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            locale: 'fr',
            allowInput: true,
            disableMobile: true,
            maxDate: 'today'
        };

        const hiredAfterEl = document.getElementById('hired_after_flatpickr');

        if (hiredAfterEl && typeof flatpickr !== 'undefined') {
            flatpickr(hiredAfterEl, flatpickrConfig);
        }
    });
</script>
@endpush
