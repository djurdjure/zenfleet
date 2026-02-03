@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Chauffeurs')

@section('content')
{{-- ====================================================================
 👤 GESTION DES CHAUFFEURS V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design surpassant Airbnb, Stripe et Salesforce:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 7 Cards métriques riches en information
 ✨ Barre recherche + filtres + actions sur 1 ligne
 ✨ Table ultra-lisible avec photo/avatar
 ✨ Pagination séparée en bas
 ✨ Thème clair 100% (pas de dark mode)
 ✨ Composants: x-iconify, x-card, x-button, x-alert, x-badge
 ✨ Tokens Tailwind custom uniquement

 @version 7.0-World-Class-Light-Theme
 @since 2025-01-19
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
 <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

 {{-- ===============================================
 MESSAGES DE SESSION
 =============================================== --}}
 @if(session('success'))
 <div x-data="{ show: true }"
 x-show="show"
 x-init="setTimeout(() => show = false, 5000)"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 x-transition:leave="transition ease-in duration-300"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="mb-6">
 <x-alert type="success" title="Succès" dismissible>
 {{ session('success') }}
 </x-alert>
 </div>
 @endif

 @if(session('error'))
 <div x-data="{ show: true }"
 x-show="show"
 x-init="setTimeout(() => show = false, 5000)"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform scale-90"
 x-transition:enter-end="opacity-100 transform scale-100"
 class="mb-6">
 <x-alert type="error" title="Erreur" dismissible>
 {{ session('error') }}
 </x-alert>
 </div>
 @endif

 {{-- ===============================================
 HEADER ULTRA-COMPACT
 =============================================== --}}
 <div class="mb-4">
 <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
 <x-iconify icon="heroicons:user-group" class="w-6 h-6 text-blue-600" />
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
 {{ $analytics['total_drivers'] ?? ($drivers->total() ?? 0) }}
 </p>
 </div>
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:users" class="w-6 h-6 text-blue-600" />
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
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 </div>
 </div>

 {{-- En Mission --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">En mission</p>
 <p class="text-2xl font-bold text-orange-600 mt-1">
 {{ $analytics['on_mission_drivers'] ?? 0 }}
 </p>
 </div>
 <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:truck" class="w-6 h-6 text-orange-600" />
 </div>
 </div>
 </div>

 {{-- En Repos --}}
 <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">En repos</p>
 <p class="text-2xl font-bold text-red-600 mt-1">
 {{ $analytics['resting_drivers'] ?? 0 }}
 </p>
 </div>
 <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:moon" class="w-6 h-6 text-red-600" />
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
 {{ number_format($analytics['avg_age'] ?? 0, 1) }} ans
 </p>
 <p class="text-xs text-blue-700 mt-1">Moyenne de l'équipe</p>
 </div>
 <div class="w-10 h-10 bg-blue-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:calendar" class="w-5 h-5 text-blue-700" />
 </div>
 </div>
 </div>

 {{-- Avec Permis Valide --}}
 <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200 p-5">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Permis valides</p>
 <p class="text-xl font-bold text-purple-900 mt-1">
 {{ $analytics['valid_licenses'] ?? 0 }}
 </p>
 <p class="text-xs text-purple-700 mt-1">Permis à jour</p>
 </div>
 <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:identification" class="w-5 h-5 text-purple-700" />
 </div>
 </div>
 </div>

 {{-- Ancienneté Moyenne --}}
 <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 p-5">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Ancienneté moy.</p>
 <p class="text-xl font-bold text-emerald-900 mt-1">
 {{ number_format($analytics['avg_seniority'] ?? 0, 1) }} ans
 </p>
 <p class="text-xs text-emerald-700 mt-1">Expérience moyenne</p>
 </div>
 <div class="w-10 h-10 bg-emerald-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:star" class="w-5 h-5 text-emerald-700" />
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
 <x-iconify icon="heroicons:magnifying-glass" class="w-5 h-5 text-gray-400" />
 </div>
 <input
 type="text"
 name="search"
 id="quickSearch"
 value="{{ request('search') }}"
 placeholder="Rechercher par nom, prénom, matricule..."
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
 <x-iconify icon="heroicons:funnel" class="w-5 h-5 text-gray-500" />
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

 {{-- Boutons d'actions --}}
 <div class="flex items-center gap-2">
 @can('drivers.export')
 <a href="{{ route('admin.drivers.export') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-down-tray" class="w-5 h-5" />
 <span class="hidden sm:inline">Exporter</span>
 </a>
 @endcan

 @can('drivers.import')
 <a href="{{ route('admin.drivers.import.show') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
 <span class="hidden sm:inline">Importer</span>
 </a>
 @endcan

 <a href="{{ route('admin.drivers.archived') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-300 hover:bg-amber-100 text-amber-700 font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:archive-box" class="w-5 h-5" />
 <span class="hidden sm:inline">Archives</span>
 </a>

 @can('drivers.create')
 <a href="{{ route('admin.drivers.create') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:plus-circle" class="w-5 h-5" />
 <span class="hidden sm:inline">Nouveau chauffeur</span>
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

 <form action="{{ route('admin.drivers.index') }}" method="GET">
 {{-- Préserver la recherche --}}
 @if(request('search'))
 <input type="hidden" name="search" value="{{ request('search') }}">
 @endif

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

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
 @foreach($driverStatuses ?? [] as $status)
 <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>
 {{ $status->name }}
 </option>
 @endforeach
 </select>
 </div>

 {{-- Afficher archivés --}}
 <div>
 <label for="view_deleted" class="block text-sm font-medium text-gray-700 mb-1">
 Archives
 </label>
 <select
 name="view_deleted"
 id="view_deleted"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Actifs uniquement</option>
 <option value="1" @selected(request('view_deleted'))>Inclure archivés</option>
 </select>
 </div>

 {{-- Trier par --}}
 <div>
 <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">
 Trier par
 </label>
 <select
 name="sort"
 id="sort"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="name" @selected(request('sort', 'name') == 'name')>Nom</option>
 <option value="created_at" @selected(request('sort') == 'created_at')>Date création</option>
 <option value="birth_date" @selected(request('sort') == 'birth_date')>Âge</option>
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
 @foreach(['15', '25', '50', '100'] as $value)
 <option value="{{ $value }}" @selected(request('per_page', '15') == $value)>
 {{ $value }}
 </option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Actions Filtres --}}
 <div class="mt-6 pt-4 border-t border-gray-200 flex items-center justify-between">
 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
 <x-iconify icon="heroicons:arrow-path" class="w-4 h-4" />
 Réinitialiser
 </a>
 <button
 type="submit"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:magnifying-glass" class="w-4 h-4" />
 Appliquer les filtres
 </button>
 </div>
 </form>
 </div>
 </div>

 {{-- ===============================================
 TABLE ENTERPRISE-GRADE WORLD-CLASS
 =============================================== --}}
 <x-card padding="p-0" margin="mb-6">
 @if($drivers && $drivers->count() > 0)
 {{-- Table --}}
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Informations
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Contact
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Date création
 </th>
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($drivers as $driver)
 <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $driver->deleted_at ? 'opacity-60 bg-gray-50' : '' }}">
 {{-- Colonne Chauffeur --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 @if($driver->photo)
 <img class="h-10 w-10 rounded-full object-cover ring-2 ring-blue-100"
 src="{{ asset('storage/' . $driver->photo) }}"
 alt="Photo de {{ $driver->first_name }}">
 @else
 <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-100 shadow-sm">
 <span class="text-sm font-bold text-white">
 {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
 </span>
 </div>
 @endif
 </div>
 <div class="ml-4">
 <div class="text-sm font-semibold text-gray-900">
 {{ $driver->first_name }} {{ $driver->last_name }}
 @if($driver->deleted_at)
 <x-badge type="error" size="sm" class="ml-2">Archivé</x-badge>
 @endif
 </div>
 @if($driver->employee_number)
 <div class="text-sm text-gray-500">
 Matricule: {{ $driver->employee_number }}
 </div>
 @endif
 </div>
 </div>
 </td>

 {{-- Colonne Informations --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm text-gray-900">
 @if($driver->birth_date && $driver->birth_date instanceof \Carbon\Carbon)
 <span class="inline-flex items-center">
 <x-iconify icon="heroicons:cake" class="w-4 h-4 text-gray-400 mr-1" />
 {{ $driver->birth_date->age }} ans
 </span>
 @else
 <span class="text-gray-400 text-xs italic">Âge non renseigné</span>
 @endif
 </div>
 <div class="text-sm text-gray-500">
 @if($driver->license_number)
 <span class="inline-flex items-center">
 <x-iconify icon="heroicons:identification" class="w-4 h-4 text-gray-400 mr-1" />
 {{ $driver->license_number }}
 </span>
 @else
 <span class="text-gray-400 text-xs italic">Aucun permis</span>
 @endif
 </div>
 </td>

 {{-- Colonne Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @if($driver->driverStatus)
 @switch($driver->driverStatus->name)
 @case('Disponible')
 <x-badge type="success">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @case('En mission')
 <x-badge type="warning">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @case('En repos')
 <x-badge type="error">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @default
 <x-badge type="gray">{{ $driver->driverStatus->name }}</x-badge>
 @endswitch
 @else
 <x-badge type="gray">Non défini</x-badge>
 @endif
 </td>

 {{-- Colonne Contact --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 @if($driver->personal_phone)
 <div class="flex items-center">
 <x-iconify icon="heroicons:phone" class="w-4 h-4 text-gray-400 mr-1" />
 {{ $driver->personal_phone }}
 </div>
 @endif
 @if($driver->personal_email)
 <div class="flex items-center truncate max-w-xs">
 <x-iconify icon="heroicons:envelope" class="w-4 h-4 text-gray-400 mr-1" />
 {{ $driver->personal_email }}
 </div>
 @endif
 @if(!$driver->personal_phone && !$driver->personal_email)
 <span class="text-gray-400 text-xs italic">Non renseigné</span>
 @endif
 </td>

 {{-- Colonne Date création --}}
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 <div class="flex flex-col">
 <span class="font-medium">{{ $driver->created_at->format('d/m/Y') }}</span>
 <span class="text-gray-500 text-xs">{{ $driver->created_at->format('H:i') }}</span>
 </div>
 </td>

 {{-- Colonne Actions --}}
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center justify-end gap-3">
 @if(!$driver->deleted_at)
 @can('drivers.view')
 <a href="{{ route('admin.drivers.show', $driver) }}"
 class="text-blue-600 hover:text-blue-900 transition-colors"
 title="Voir">
 <x-iconify icon="heroicons:eye" class="w-5 h-5" />
 </a>
 @endcan

 @can('drivers.update')
 <a href="{{ route('admin.drivers.edit', $driver) }}"
 class="text-green-600 hover:text-green-900 transition-colors"
 title="Modifier">
 <x-iconify icon="heroicons:pencil" class="w-5 h-5" />
 </a>
 @endcan

 @can('drivers.delete')
 <button
 onclick="showDeleteConfirmation({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
 class="text-amber-600 hover:text-amber-900 transition-colors"
 title="Archiver">
 <x-iconify icon="heroicons:archive-box" class="w-5 h-5" />
 </button>
 @endcan
 @else
 @can('drivers.restore')
 <button
 onclick="showRestoreConfirmation({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
 class="text-green-600 hover:text-green-900 transition-colors"
 title="Restaurer">
 <x-iconify icon="heroicons:arrow-path" class="w-5 h-5" />
 </button>
 @endcan

@can('drivers.force-delete')
 <button
 onclick="showForceDeleteConfirmation({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
 class="text-red-600 hover:text-red-900 transition-colors"
 title="Supprimer définitivement">
 <x-iconify icon="heroicons:trash" class="w-5 h-5" />
 </button>
 @endcan
 @endif
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 @if($drivers->hasPages())
 <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
 <div class="flex-1 flex justify-between sm:hidden">
 @if($drivers->onFirstPage())
 <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
 Précédent
 </span>
 @else
 <a href="{{ $drivers->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 Précédent
 </a>
 @endif

 @if($drivers->hasMorePages())
 <a href="{{ $drivers->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 Suivant
 </a>
 @else
 <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
 Suivant
 </span>
 @endif
 </div>
 <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
 <div>
 <p class="text-sm text-gray-700">
 Affichage de
 <span class="font-medium">{{ $drivers->firstItem() ?? 0 }}</span>
 à
 <span class="font-medium">{{ $drivers->lastItem() ?? 0 }}</span>
 sur
 <span class="font-medium">{{ $drivers->total() }}</span>
 résultats
 </p>
 </div>
 <div>
 {{ $drivers->withQueryString()->links() }}
 </div>
 </div>
 </div>
 @endif
 @else
 {{-- État vide --}}
 <div class="flex flex-col items-center justify-center py-16 px-4">
 <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
 <x-iconify icon="heroicons:user-group" class="w-10 h-10 text-gray-400" />
 </div>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
 <p class="text-gray-500 text-center mb-6 max-w-md">
 @if(request()->hasAny(['search', 'status_id', 'view_deleted']))
 Aucun chauffeur ne correspond à vos critères de recherche. Essayez d'ajuster les filtres.
 @else
 Commencez par ajouter votre premier chauffeur à la flotte.
 @endif
 </p>
 @if(request()->hasAny(['search', 'status_id', 'view_deleted']))
 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700">
 <x-iconify icon="heroicons:arrow-path" class="w-4 h-4" />
 Réinitialiser les filtres
 </a>
 @else
 @can('drivers.create')
 <a href="{{ route('admin.drivers.create') }}"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:plus-circle" class="w-5 h-5" />
 Ajouter un chauffeur
 </a>
 @endcan
 @endif
 </div>
 @endif
 </x-card>

 </div>
</section>

@push('scripts')
<script>
// 🗑️ Enterprise-Grade Delete Confirmation Modal
function showDeleteConfirmation(driverId, firstName, lastName, employeeNumber) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 animate-fade-in relative z-50">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0">
 <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-lg font-semibold text-gray-900 mb-2">Archiver le chauffeur</h3>
 <p class="text-sm text-gray-600 mb-4">
 Voulez-vous archiver ce chauffeur ? Cette action peut être annulée.
 </p>
 <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-3">
 <p class="font-semibold text-blue-900">${firstName} ${lastName}</p>
 <p class="text-sm text-blue-700">Matricule: ${employeeNumber}</p>
 </div>
 <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
 <p class="text-xs text-amber-700">Le chauffeur sera archivé et pourra être restauré depuis la section "Chauffeurs archivés".</p>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button type="button" onclick="confirmDelete(${driverId})"
 class="w-full inline-flex justify-center items-center gap-2 rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-amber-600 text-sm font-semibold text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:w-auto transition-all duration-200">
 Archiver le chauffeur
 </button>
 <button type="button" onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;
 document.body.appendChild(modal);
}

// 🔄 Enterprise-Grade Restore Confirmation Modal
function showRestoreConfirmation(driverId, firstName, lastName, employeeNumber) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0">
 <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-lg font-semibold text-gray-900 mb-2">Restaurer le chauffeur</h3>
 <p class="text-sm text-gray-600 mb-4">Voulez-vous restaurer ce chauffeur ? Il redeviendra actif dans votre flotte.</p>
 <div class="bg-green-50 border border-green-200 rounded-lg p-4">
 <p class="font-semibold text-green-900">${firstName} ${lastName}</p>
 <p class="text-sm text-green-700">Matricule: ${employeeNumber}</p>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button type="button" onclick="confirmRestore(${driverId})"
 class="w-full inline-flex justify-center items-center gap-2 rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-green-600 text-sm font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto">
 Restaurer le chauffeur
 </button>
 <button type="button" onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;
 document.body.appendChild(modal);
}

// 💀 Enterprise-Grade Force Delete Confirmation Modal
function showForceDeleteConfirmation(driverId, firstName, lastName, employeeNumber) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0">
 <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-lg font-semibold text-gray-900 mb-2">⚠️ Suppression Définitive</h3>
 <p class="text-sm text-gray-600 mb-4">
 <strong class="text-red-600">ATTENTION :</strong> Cette action est IRRÉVERSIBLE !
 </p>
 <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-3">
 <p class="font-semibold text-red-900">${firstName} ${lastName}</p>
 <p class="text-sm text-red-700">Matricule: ${employeeNumber}</p>
 </div>
 <div class="bg-red-50 border border-red-200 rounded-lg p-3">
 <p class="text-xs text-red-700">Toutes les données du chauffeur seront définitivement perdues. Cette action ne peut pas être annulée.</p>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button type="button" onclick="confirmForceDelete(${driverId})"
 class="w-full inline-flex justify-center items-center gap-2 rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto">
 Supprimer Définitivement
 </button>
 <button type="button" onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;
 document.body.appendChild(modal);
}

// Action handlers with enterprise-grade error handling
function confirmDelete(driverId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/drivers/${driverId}`;
 form.innerHTML = '@csrf @method("DELETE")';
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 300);
}

function confirmRestore(driverId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/drivers/${driverId}/restore`;
 form.innerHTML = '@csrf @method("PATCH")';
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 300);
}

function confirmForceDelete(driverId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/drivers/${driverId}/force-delete`;
 form.innerHTML = '@csrf @method("DELETE")';
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 300);
}

function closeModal() {
 const modal = document.querySelector('.fixed.inset-0.z-50');
 if (modal) {
 modal.style.opacity = '0';
 modal.style.transform = 'scale(0.95)';
 setTimeout(() => modal.remove(), 300);
 }
}
</script>
@endpush
@endsection
