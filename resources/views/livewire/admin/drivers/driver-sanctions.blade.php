<div>
{{-- ====================================================================
 ⚖️ DRIVER SANCTIONS - ULTRA PRO ENTERPRISE GRADE
 ====================================================================
 Aligné sur le style du module véhicules pour une cohérence parfaite
 @version 2.0-Ultra-Pro
 @since 2025-01-20
 ==================================================================== --}}

{{-- ===============================================
 STATISTIQUES - Style Véhicules
 =============================================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 
 {{-- Total Sanctions --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 </div>
 <div class="flex-1">
 <p class="text-sm font-medium text-gray-600">Total Sanctions</p>
 <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
 </div>
 </div>
 </div>

 {{-- Actives --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:shield-exclamation" class="w-6 h-6 text-amber-600" />
 </div>
 <div class="flex-1">
 <p class="text-sm font-medium text-gray-600">Actives</p>
 <p class="text-2xl font-bold text-amber-600">{{ $statistics['active'] }}</p>
 </div>
 </div>
 </div>

 {{-- Ce Mois --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:calendar" class="w-6 h-6 text-blue-600" />
 </div>
 <div class="flex-1">
 <p class="text-sm font-medium text-gray-600">Ce Mois</p>
 <p class="text-2xl font-bold text-blue-600">{{ $statistics['this_month'] }}</p>
 </div>
 </div>
 </div>

 {{-- Critiques --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:signal" class="w-6 h-6 text-purple-600" />
 </div>
 <div class="flex-1">
 <p class="text-sm font-medium text-gray-600">Critiques</p>
 <p class="text-2xl font-bold text-purple-600">{{ $statistics['by_severity']['critical'] ?? 0 }}</p>
 </div>
 </div>
 </div>

</div>

{{-- ===============================================
 RECHERCHE ET FILTRES - Style Véhicules
 =============================================== --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
 <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
 
 {{-- Recherche --}}
 <div class="flex-1 w-full lg:max-w-md">
 <div class="relative">
 <input
 type="text"
 wire:model.live.debounce.300ms="search"
 placeholder="Rechercher par chauffeur ou motif..."
 class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
 <x-iconify icon="heroicons:magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
 
 <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
 <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 </div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center gap-3 w-full lg:w-auto">
 <button
 onclick="toggleFilters()"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
 <x-iconify icon="heroicons:funnel" class="w-5 h-5" />
 Filtres
 @if($sanctionTypeFilter || $severityFilter || $dateFrom || $dateTo)
 <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
 @php
 $activeFilters = 0;
 if($sanctionTypeFilter) $activeFilters++;
 if($severityFilter) $activeFilters++;
 if($dateFrom) $activeFilters++;
 if($dateTo) $activeFilters++;
 @endphp
 {{ $activeFilters }}
 </span>
 @endif
 </button>

 <button
 onclick="openCreateSanctionModal()"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:plus" class="w-5 h-5" />
 Nouvelle Sanction
 </button>
 </div>
 
 </div>

 {{-- Panel Filtres (Collapsible) --}}
 <div id="filtersPanel" style="display: none;" class="mt-6 pt-6 border-t border-gray-200">
 
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
 
 {{-- Filtre Type --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Type de Sanction</label>
 <select wire:model.live="sanctionTypeFilter" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Tous les types</option>
 <option value="avertissement_verbal">Avertissement Verbal</option>
 <option value="avertissement_ecrit">Avertissement Écrit</option>
 <option value="mise_a_pied">Mise à Pied</option>
 <option value="suspension_permis">Suspension Permis</option>
 <option value="amende">Amende</option>
 <option value="blame">Blâme</option>
 <option value="licenciement">Licenciement</option>
 </select>
 </div>

 {{-- Filtre Gravité --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Gravité</label>
 <select wire:model.live="severityFilter" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="">Toutes</option>
 <option value="low">Faible</option>
 <option value="medium">Moyenne</option>
 <option value="high">Élevée</option>
 <option value="critical">Critique</option>
 </select>
 </div>

 {{-- Date Début --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Date Début</label>
 <input
 type="date"
 wire:model.live="dateFrom"
 class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>

 {{-- Date Fin --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">Date Fin</label>
 <input
 type="date"
 wire:model.live="dateTo"
 class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>
 
 </div>

 <div class="mt-4 flex items-center justify-between">
 <label class="flex items-center gap-2 cursor-pointer">
 <input type="checkbox" wire:model.live="showArchived" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <span class="text-sm text-gray-700">Inclure les sanctions archivées</span>
 </label>

 <button
 wire:click="resetFilters"
 class="text-sm text-blue-600 hover:text-blue-800 font-medium">
 Réinitialiser les filtres
 </button>
 </div>
 
 </div>
</div>

{{-- ===============================================
 TABLEAU SANCTIONS - Style Véhicules
 =============================================== --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
 <div class="relative">
 {{-- Loading Overlay --}}
 <div wire:loading.delay wire:target="search,sanctionTypeFilter,severityFilter,dateFrom,dateTo,showArchived,sortBy" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
 <div class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg shadow-lg border border-gray-200">
 <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span class="text-sm font-medium text-gray-700">Chargement...</span>
 </div>
 </div>

 {{-- Table --}}
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-6 py-3 text-left">
 <button wire:click="sortBy('driver_id')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
 Chauffeur
 @if($sortField === 'driver_id')
 <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
 @endif
 </button>
 </th>
 <th class="px-6 py-3 text-left">
 <button wire:click="sortBy('sanction_type')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
 Type
 @if($sortField === 'sanction_type')
 <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
 @endif
 </button>
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gravité</th>
 <th class="px-6 py-3 text-left">
 <button wire:click="sortBy('sanction_date')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
 Date
 @if($sortField === 'sanction_date')
 <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
 @endif
 </button>
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
 <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse($sanctions as $sanction)
 <tr class="hover:bg-gray-50 transition-colors">
 <td class="px-6 py-4">
 @if($sanction->driver)
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
 {{ substr($sanction->driver->first_name, 0, 1) }}{{ substr($sanction->driver->last_name, 0, 1) }}
 </div>
 <div>
 <p class="text-sm font-semibold text-gray-900">
 {{ $sanction->driver->first_name }} {{ $sanction->driver->last_name }}
 </p>
 <p class="text-xs text-gray-500">{{ $sanction->driver->employee_number }}</p>
 </div>
 </div>
 @else
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-semibold text-sm">
 <x-iconify icon="heroicons:user" class="w-5 h-5" />
 </div>
 <div>
 <p class="text-sm font-semibold text-gray-500 italic">
 Chauffeur supprimé
 </p>
 <p class="text-xs text-gray-400">ID: {{ $sanction->driver_id }}</p>
 </div>
 </div>
 @endif
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-3.5 h-3.5" />
 {{ match($sanction->sanction_type) {
 'avertissement_verbal' => 'Avertissement Verbal',
 'avertissement_ecrit' => 'Avertissement Écrit',
 'mise_a_pied' => 'Mise à Pied',
 'suspension_permis' => 'Suspension Permis',
 'amende' => 'Amende',
 'blame' => 'Blâme',
 'licenciement' => 'Licenciement',
 default => $sanction->sanction_type,
 } }}
 </span>
 </td>
 <td class="px-6 py-4">
 @php
 $severityColors = [
 'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
 'medium' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
 'high' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
 'critical' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
 ];
 $colors = $severityColors[$sanction->severity] ?? $severityColors['medium'];
 @endphp
 <span class="inline-flex items-center px-2.5 py-1 {{ $colors['bg'] }} {{ $colors['text'] }} text-xs font-medium rounded-full">
 {{ match($sanction->severity) {
 'low' => 'Faible',
 'medium' => 'Moyenne',
 'high' => 'Élevée',
 'critical' => 'Critique',
 default => $sanction->severity,
 } }}
 </span>
 </td>
 <td class="px-6 py-4 text-sm text-gray-900">
 {{ \Carbon\Carbon::parse($sanction->sanction_date)->format('d/m/Y') }}
 </td>
 <td class="px-6 py-4">
 <p class="text-sm text-gray-900 line-clamp-2">{{ $sanction->reason }}</p>
 </td>
 <td class="px-6 py-4">
 @php
 $statusColors = [
 'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
 'appealed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
 'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
 'archived' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500'],
 ];
 $statusColor = $statusColors[$sanction->status] ?? $statusColors['active'];
 @endphp
 <span class="inline-flex px-2.5 py-1 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} text-xs font-medium rounded-full">
 {{ match($sanction->status) {
 'active' => 'Active',
 'appealed' => 'Contestée',
 'cancelled' => 'Annulée',
 'archived' => 'Archivée',
 default => $sanction->status,
 } }}
 </span>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center justify-end gap-2">
 <button
 onclick="openEditSanctionModal({{ $sanction->id }})"
 class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
 title="Modifier">
 <x-iconify icon="heroicons:pencil" class="w-5 h-5" />
 </button>
 
 <button
 onclick="deleteSanctionModal({{ $sanction->id }}, '{{ $sanction->driver ? $sanction->driver->first_name . ' ' . $sanction->driver->last_name : 'Chauffeur supprimé' }}')"
 class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
 title="Supprimer">
 <x-iconify icon="heroicons:trash" class="w-5 h-5" />
 </button>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-12 text-center">
 <div class="flex flex-col items-center justify-center">
 <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
 <x-iconify icon="heroicons:shield-check" class="w-8 h-8 text-gray-400" />
 </div>
 <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune sanction trouvée</h3>
 <p class="text-sm text-gray-500">Aucune sanction ne correspond à vos critères de recherche.</p>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 @if($sanctions->hasPages())
 <div class="px-6 py-4 border-t border-gray-200">
 {{ $sanctions->links() }}
 </div>
 @endif
 </div>
</div>

{{-- ===============================================
 MODAL CRÉER/MODIFIER SANCTION - LIVEWIRE
 =============================================== --}}
@if($showModal)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div wire:click="closeModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
 
 {{-- Header --}}
 <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
 <x-iconify icon="heroicons:shield-exclamation" class="w-5 h-5 text-orange-600" />
 {{ $editMode ? 'Modifier la Sanction' : 'Nouvelle Sanction' }}
 </h3>
 <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors p-2 hover:bg-gray-100 rounded-lg">
 <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
 </button>
 </div>

 {{-- Formulaire --}}
 <form wire:submit.prevent="save" class="space-y-6">
 
 {{-- Chauffeur --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Chauffeur <span class="text-red-500">*</span>
 </label>
 <div wire:ignore>
 <select wire:model="driver_id" id="driver_id_sanction" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('driver_id') border-red-500 @enderror">
 <option value="">Sélectionner un chauffeur</option>
 @foreach($drivers as $driver)
 <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }} - {{ $driver->employee_number }}</option>
 @endforeach
 </select>
 </div>
 @error('driver_id') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>

 {{-- Type et Gravité --}}
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Type de Sanction <span class="text-red-500">*</span>
 </label>
 <select wire:model="sanction_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('sanction_type') border-red-500 @enderror">
 <option value="">Sélectionner</option>
 <option value="avertissement_verbal">Avertissement Verbal</option>
 <option value="avertissement_ecrit">Avertissement Écrit</option>
 <option value="mise_a_pied">Mise à Pied</option>
 <option value="suspension_permis">Suspension Permis</option>
 <option value="amende">Amende</option>
 <option value="blame">Blâme</option>
 <option value="licenciement">Licenciement</option>
 </select>
 @error('sanction_type') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Gravité <span class="text-red-500">*</span>
 </label>
 <select wire:model="severity" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('severity') border-red-500 @enderror">
 <option value="low">Faible</option>
 <option value="medium">Moyenne</option>
 <option value="high">Élevée</option>
 <option value="critical">Critique</option>
 </select>
 @error('severity') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>
 </div>

 {{-- Date et Durée --}}
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Date de Sanction <span class="text-red-500">*</span>
 </label>
 <input
 type="date"
 wire:model="sanction_date"
 max="{{ date('Y-m-d') }}"
 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('sanction_date') border-red-500 @enderror">
 @error('sanction_date') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>

 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Durée (jours)
 </label>
 <input
 type="number"
 wire:model="duration_days"
 min="1"
 max="365"
 placeholder="Nombre de jours"
 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('duration_days') border-red-500 @enderror">
 @error('duration_days') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>
 </div>

 {{-- Motif --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Motif <span class="text-red-500">*</span>
 </label>
 <textarea
 wire:model="reason"
 rows="3"
 placeholder="Décrivez le motif de la sanction (minimum 10 caractères)..."
 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('reason') border-red-500 @enderror"></textarea>
 @error('reason') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>

 {{-- Statut et Notes --}}
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Statut
 </label>
 <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <option value="active">Active</option>
 <option value="appealed">Contestée</option>
 <option value="cancelled">Annulée</option>
 </select>
 </div>

 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Notes
 </label>
 <input
 type="text"
 wire:model="notes"
 placeholder="Notes additionnelles"
 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 </div>
 </div>

 {{-- Pièce jointe --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Pièce jointe
 </label>
 @if($existingAttachment && !$attachment)
 <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
 <x-iconify icon="heroicons:paper-clip" class="w-4 h-4" />
 <span>Fichier actuel: {{ basename($existingAttachment) }}</span>
 </div>
 @endif
 <input
 type="file"
 wire:model="attachment"
 accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG, DOC, DOCX (max 10 MB)</p>
 @error('attachment') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
 </div>

 {{-- Boutons --}}
 <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
 <button
 type="button"
 wire:click="closeModal"
 class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
 Annuler
 </button>
 <button
 type="submit"
 wire:loading.attr="disabled"
 class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm disabled:opacity-50">
 <span wire:loading.remove>{{ $editMode ? 'Enregistrer' : 'Créer' }}</span>
 <span wire:loading>
 <svg class="animate-spin inline h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Traitement...
 </span>
 </button>
 </div>

 </form>

 </div>
 </div>
</div>
@endif

</div>

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// FONCTIONS JAVASCRIPT POUR LES MODALES - STYLE VÉHICULES
// ═══════════════════════════════════════════════════════════════════════════

// Toggle filtres
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}

// Fermer modal
function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.z-50');
    if (modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        setTimeout(() => modal.remove(), 200);
    }
}

// Initialiser TomSelect pour le select des chauffeurs
let driverTomSelect = null;

document.addEventListener('DOMContentLoaded', function() {
    // Écouter l'ouverture de la modale pour initialiser TomSelect
    window.addEventListener('show-modal', event => {
        setTimeout(() => {
            initDriverTomSelect();
        }, 100);
    });
});

function initDriverTomSelect() {
    const selectElement = document.getElementById('driver_id_sanction');
    if (selectElement && !selectElement.tomselect) {
        driverTomSelect = new TomSelect('#driver_id_sanction', {
            plugins: ['clear_button'],
            placeholder: 'Rechercher un chauffeur...',
            create: false,
            maxItems: 1,
            onItemAdd: function(value) {
                @this.set('driver_id', value);
            },
            onClear: function() {
                @this.set('driver_id', null);
            }
        });
    }
}

// Ouvrir modal création sanction
function openCreateSanctionModal() {
    @this.call('openCreateModal');
}

// Ouvrir modal édition sanction
function openEditSanctionModal(sanctionId) {
    @this.call('openEditModal', sanctionId);
}

// Modal de confirmation de suppression - Style véhicules
function deleteSanctionModal(sanctionId, driverName) {
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
                            Supprimer la sanction
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer cette sanction pour <strong class="font-semibold text-gray-900">${driverName}</strong> ?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Cette action est <strong class="font-semibold text-red-600">irréversible</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmDeleteSanction(${sanctionId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer
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

// Confirmer suppression
function confirmDeleteSanction(sanctionId) {
    @this.call('deleteSanction', sanctionId);
    closeModal();
}

// Écouter les notifications Livewire
window.addEventListener('notification', event => {
    const data = event.detail[0] || event.detail;
    
    // Créer une notification toast
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 max-w-md transform transition-all duration-300 ${
        data.type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
    } border rounded-lg shadow-lg p-4`;
    
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                ${data.type === 'success' 
                    ? '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    : '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                }
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium ${data.type === 'success' ? 'text-green-800' : 'text-red-800'}">
                    ${data.message}
                </p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="${data.type === 'success' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-fermer après 5 secondes
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
});
</script>
@endpush
