<div>
{{-- ====================================================================
 👤 DRIVERS TABLE LIVEWIRE - WORLD-CLASS ENTERPRISE GRADE
 ====================================================================

 Composant Livewire ultra-professionnel avec:
 ✨ Recherche en temps réel
 ✨ Filtres avancés animés
 ✨ Tri des colonnes
 ✨ Sélection multiple
 ✨ Actions en masse
 ✨ Design épuré et moderne

 @version 1.0-World-Class
 @since 2025-01-19
 ==================================================================== --}}

{{-- ===============================================
 CARDS MÉTRIQUES
 =============================================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
 {{-- Total Chauffeurs --}}
 <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Total chauffeurs</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_drivers'] ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:users" class="w-6 h-6 text-blue-600" />
 </div>
 </div>
 </div>

 {{-- Disponibles --}}
 <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Disponibles</p>
 <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['available_drivers'] ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 </div>
 </div>

 {{-- En Mission --}}
 <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">En mission</p>
 <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['on_mission_drivers'] ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:truck" class="w-6 h-6 text-orange-600" />
 </div>
 </div>
 </div>

 {{-- En Repos --}}
 <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">En repos</p>
 <p class="text-2xl font-bold text-red-600 mt-1">{{ $analytics['resting_drivers'] ?? 0 }}</p>
 </div>
 <div class="w-12 h-12 bg-red-100 border border-red-200 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:moon" class="w-6 h-6 text-red-600" />
 </div>
 </div>
 </div>
</div>

{{-- ===============================================
 STATISTIQUES AVANCÉES
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

 {{-- Permis Valides --}}
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
 BARRE DE RECHERCHE ET FILTRES
 =============================================== --}}
<div class="mb-6">
 <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
 {{-- Recherche --}}
 <div class="flex-1 w-full lg:w-auto">
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <x-iconify icon="heroicons:magnifying-glass" class="w-5 h-5 text-gray-400" />
 </div>
 <input
 type="text"
 wire:model.live.debounce.500ms="search"
 placeholder="Rechercher par nom, prénom, matricule, email..."
 wire:loading.attr="aria-busy"
 wire:target="search"
 class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 
 {{-- Loading Indicator --}}
 <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
 <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
 </div>
 </div>
 </div>

 {{-- Bouton Filtres --}}
 <button
 wire:click="toggleFilters"
 type="button"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:funnel" class="w-5 h-5 text-gray-500" />
 <span class="font-medium text-gray-700">Filtres</span>
 @if($statusFilter || $dateFrom || $dateTo || $licenseCategory || $includeArchived)
 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
 {{ collect([$statusFilter, $dateFrom, $dateTo, $licenseCategory, $includeArchived])->filter()->count() }}
 </span>
 @endif
 <x-iconify
 icon="heroicons:chevron-down"
 class="w-4 h-4 text-gray-400 transition-transform duration-200"
 :class="{ 'rotate-180': showFilters }" />
 </button>

 {{-- Actions en Masse (si sélection) --}}
 @if(count($selectedDrivers) > 0)
 <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
 <span class="text-sm font-medium text-blue-900">
 {{ count($selectedDrivers) }} sélectionné(s)
 </span>
 <button
 wire:click="bulkArchive"
 wire:confirm="Êtes-vous sûr de vouloir archiver ces chauffeurs ?"
 class="text-sm font-medium text-red-600 hover:text-red-800">
 Archiver
 </button>
 <button
 wire:click="deselectAll"
 class="text-sm font-medium text-gray-600 hover:text-gray-800">
 Désélectionner
 </button>
 </div>
 @endif
 </div>

 {{-- Panel Filtres Avancés --}}
 @if($showFilters)
 <div
 x-data="{ show: @entangle('showFilters') }"
 x-show="show"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform -translate-y-2"
 x-transition:enter-end="opacity-100 transform translate-y-0"
 class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 {{-- Statut --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
 <select
 wire:model.live="statusFilter"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les statuts</option>
 @foreach($driverStatuses as $status)
 <option value="{{ $status->id }}">{{ $status->name }}</option>
 @endforeach
 </select>
 </div>

 {{-- Date Début --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-1">Créé à partir du</label>
 <input
 type="date"
 wire:model.live="dateFrom"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>

 {{-- Date Fin --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-1">Jusqu'au</label>
 <input
 type="date"
 wire:model.live="dateTo"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>

 {{-- Catégorie Permis --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie Permis</label>
 <select
 wire:model.live="licenseCategory"
 class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Toutes</option>
 <option value="B">Catégorie B</option>
 <option value="C">Catégorie C</option>
 <option value="D">Catégorie D</option>
 <option value="E">Catégorie E</option>
 </select>
 </div>
 </div>

 {{-- Options --}}
 <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-200">
 <label class="inline-flex items-center cursor-pointer">
 <input
 type="checkbox"
 wire:model.live="includeArchived"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 <span class="ml-2 text-sm text-gray-700 font-medium">Inclure les chauffeurs archivés</span>
 </label>

 <button
 wire:click="resetFilters"
 class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
 <x-iconify icon="heroicons:arrow-path" class="w-4 h-4" />
 Réinitialiser
 </button>
 </div>
 </div>
 @endif
</div>

{{-- ===============================================
 TABLE DES CHAUFFEURS
 =============================================== --}}
<x-card padding="p-0" margin="mb-6">
 @if($drivers->count() > 0)
 {{-- Loading Overlay --}}
 <div wire:loading.delay wire:target="sortBy,statusFilter,search" class="absolute inset-0 bg-white/75 z-10 flex items-center justify-center">
 <div class="flex items-center gap-3">
 <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span class="text-sm font-medium text-gray-700">Chargement...</span>
 </div>
 </div>

 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 {{-- Checkbox Select All --}}
 <th scope="col" class="px-6 py-3 w-12">
 <input
 type="checkbox"
 wire:model.live="selectAll"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 </th>

 {{-- Chauffeur --}}
 <th scope="col" class="px-6 py-3 text-left">
 <button
 wire:click="sortBy('first_name')"
 class="group inline-flex items-center gap-2 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
 Chauffeur
 <x-iconify
 icon="heroicons:bars-arrow-up"
 class="w-4 h-4"
 :class="{ 'text-blue-600': sortField === 'first_name' && sortDirection === 'asc', 'rotate-180': sortField === 'first_name' && sortDirection === 'desc' }" />
 </button>
 </th>

 {{-- Informations --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Informations
 </th>

 {{-- Statut --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>

 {{-- Contact --}}
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Contact
 </th>

 {{-- Date Création --}}
 <th scope="col" class="px-6 py-3 text-left">
 <button
 wire:click="sortBy('created_at')"
 class="group inline-flex items-center gap-2 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
 Date création
 <x-iconify
 icon="heroicons:bars-arrow-up"
 class="w-4 h-4"
 :class="{ 'text-blue-600': sortField === 'created_at' && sortDirection === 'asc', 'rotate-180': sortField === 'created_at' && sortDirection === 'desc' }" />
 </button>
 </th>

 {{-- Actions --}}
 <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($drivers as $driver)
 <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $driver->deleted_at ? 'opacity-60 bg-gray-50' : '' }}">
 {{-- Checkbox --}}
 <td class="px-6 py-4">
 <input
 type="checkbox"
 wire:model.live="selectedDrivers"
 value="{{ $driver->id }}"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
 </td>

 {{-- Chauffeur (suite dans le prochain message car limite de caractères) --}}
