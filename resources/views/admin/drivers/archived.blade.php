@extends('layouts.admin.catalyst')

@section('title', 'Chauffeurs Archivés - ZenFleet')

@section('content')
{{-- ====================================================================
 🗄️ CHAUFFEURS ARCHIVÉS V3.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Améliorations V3.0:
 ✨ Filtres avancés (dates d'archivage, statuts)
 ✨ Export Excel/CSV des archives
 ✨ Sélection multiple et restauration en masse
 ✨ Statistiques enrichies
 ✨ Interface cohérente avec véhicules

 @version 3.0-Enhanced-Ultra-Pro
 @since 2025-01-20
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- Affichage message d'erreur si présent --}}
        @if(isset($error))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-iconify icon="lucide:alert-circle" class="h-5 w-5 text-red-500" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800 font-medium">
                        {{ $error }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- ===============================================
            HEADER ULTRA-COMPACT
        =============================================== --}}
        <div class="mb-4">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                    <x-iconify icon="lucide:home" class="w-4 h-4" />
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
                    Chauffeurs
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <span class="font-semibold text-gray-900">Archives</span>
            </nav>

            {{-- Title + Actions --}}
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <x-iconify icon="lucide:archive" class="w-6 h-6 text-amber-600" />
                    Chauffeurs Archivés
                    <span class="ml-2 text-sm font-normal text-gray-500">
                        ({{ $drivers->total() }})
                    </span>
                </h1>

                <div class="flex items-center gap-2">
                    {{-- Export Excel --}}
                    <a href="{{ route('admin.drivers.archived.export', request()->query()) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:download" class="w-5 h-5" />
                        <span class="font-medium">Export Excel</span>
                    </a>

                    {{-- Retour --}}
                    <a href="{{ route('admin.drivers.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:arrow-left" class="w-5 h-5 text-gray-500" />
                        <span class="font-medium text-gray-700">Retour</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
            {{-- Total Archivés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total archivés</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">
                            {{ $stats['total_archived'] }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:archive" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>

            {{-- Ce Mois --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ce mois</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">
                            {{ $stats['archived_this_month'] }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- Cette Année --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cette année</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ $stats['archived_this_year'] }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:trending-down" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Sélectionnés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sélectionnés</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1" id="selected-count">
                            0
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-square" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- Ancienneté Moyenne --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ancienneté moy.</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            {{ number_format($stats['avg_seniority'], 1) }} ans
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            FILTRES AVANCÉS + ACTIONS EN MASSE
        =============================================== --}}
        <div class="mb-6" x-data="{ showFilters: {{ request()->has('archived_from') || request()->has('archived_to') || request()->has('status_id') || request()->has('search') ? 'true' : 'false' }} }">
            {{-- Ligne principale --}}
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                {{-- Recherche --}}
                <div class="flex-1 w-full lg:w-auto">
                    <form action="{{ route('admin.drivers.archived') }}" method="GET">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par nom, prénom, matricule..."
                                class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                                onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

                {{-- Bouton Filtres --}}
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <span class="font-medium text-gray-700">Filtres</span>
                    @php
                        $activeFiltersCount = count(array_filter(request()->only(['archived_from', 'archived_to', 'status_id', 'search'])));
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

                {{-- Actions en masse --}}
                <button
                    id="bulk-restore-btn"
                    onclick="bulkRestore()"
                    style="display: none;"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:undo-2" class="w-5 h-5" />
                    <span class="font-medium">Restaurer Sélection</span>
                </button>
            </div>

            {{-- Panel Filtres Avancés --}}
            <div
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <form action="{{ route('admin.drivers.archived') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Date d'archivage (début) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:calendar-days" class="w-4 h-4 inline mr-1" />
                                Archivé depuis
                            </label>
                            <input
                                type="date"
                                name="archived_from"
                                value="{{ request('archived_from') }}"
                                max="{{ date('Y-m-d') }}"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Date d'archivage (fin) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:calendar-check" class="w-4 h-4 inline mr-1" />
                                Archivé jusqu'à
                            </label>
                            <input
                                type="date"
                                name="archived_to"
                                value="{{ request('archived_to') }}"
                                max="{{ date('Y-m-d') }}"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Statut au moment de l'archivage --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                            <select name="status_id" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les statuts</option>
                                @foreach($driverStatuses ?? [] as $status)
                                    <option value="{{ $status['id'] ?? $status->id }}" {{ request('status_id') == ($status['id'] ?? $status->id) ? 'selected' : '' }}>
                                        {{ $status['name'] ?? $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Boutons --}}
                        <div class="flex items-end">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <x-iconify icon="lucide:filter" class="w-4 h-4" />
                                Appliquer
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.drivers.archived') }}"
                           class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Réinitialiser les filtres
                        </a>
                        <p class="text-xs text-gray-500">
                            💡 Filtrez par période d'archivage et statut pour affiner vos résultats
                        </p>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===============================================
            TABLE DES CHAUFFEURS ARCHIVÉS
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @if($drivers && $drivers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input
                                        type="checkbox"
                                        id="select-all"
                                        onclick="toggleSelectAll(this)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Chauffeur
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Matricule
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Archivé le
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($drivers as $driver)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Checkbox --}}
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        class="driver-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        value="{{ $driver->id }}"
                                        onchange="updateSelectedCount()">
                                </td>

                                {{-- Chauffeur --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @if($driver->photo)
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-amber-200"
                                                 src="{{ asset('storage/' . $driver->photo) }}"
                                                 alt="Photo de {{ $driver->first_name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $driver->first_name }} {{ $driver->last_name }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $driver->personal_email ?? 'Pas d\'email' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Matricule --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ $driver->employee_number ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($driver->driverStatus)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $driver->driverStatus->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            N/A
                                        </span>
                                    @endif
                                </td>

                                {{-- Date Archivage --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-900">{{ $driver->deleted_at->format('d/m/Y') }}</span>
                                        <span class="text-xs text-gray-500">{{ $driver->deleted_at->format('H:i') }}</span>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Restaurer --}}
                                        <button
                                            onclick="showRestoreModal({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
                                            class="p-1.5 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Restaurer">
                                            <x-iconify icon="lucide:undo-2" class="w-5 h-5" />
                                        </button>

                                        {{-- Supprimer Définitivement --}}
                                        <button
                                            onclick="showForceDeleteModal({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
                                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Supprimer définitivement">
                                            <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($drivers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $drivers->links() }}
                    </div>
                @endif
            @else
                {{-- État vide --}}
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                        <x-iconify icon="lucide:archive" class="w-8 h-8 text-amber-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun chauffeur archivé</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        @if(request()->hasAny(['archived_from', 'archived_to', 'status_id', 'search']))
                            Aucun résultat ne correspond à vos filtres.
                        @else
                            Tous vos chauffeurs sont actifs.
                        @endif
                    </p>
                    <a href="{{ route('admin.drivers.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                        Retour aux chauffeurs
                    </a>
                </div>
            @endif
        </div>

    </div>
</section>

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// GESTION DE LA SÉLECTION MULTIPLE
// ═══════════════════════════════════════════════════════════════════════════
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.driver-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.driver-checkbox:checked');
    const count = checkboxes.length;
    
    document.getElementById('selected-count').textContent = count;
    
    // Afficher/masquer le bouton de restauration en masse
    const bulkRestoreBtn = document.getElementById('bulk-restore-btn');
    if (count > 0) {
        bulkRestoreBtn.style.display = 'inline-flex';
    } else {
        bulkRestoreBtn.style.display = 'none';
    }
}

function bulkRestore() {
    const checkboxes = document.querySelectorAll('.driver-checkbox:checked');
    const driverIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (driverIds.length === 0) {
        alert('Veuillez sélectionner au moins un chauffeur.');
        return;
    }
    
    // Créer modal de confirmation
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900">
                            Restauration en masse
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Voulez-vous vraiment restaurer <strong>${driverIds.length} chauffeur(s)</strong> ? Ils redeviendront actifs dans la flotte.
                            </p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-sm font-semibold text-green-900">${driverIds.length} chauffeur(s) sélectionné(s)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmBulkRestore(${JSON.stringify(driverIds)})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Restaurer
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

function confirmBulkRestore(driverIds) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('admin.drivers.archived.bulk-restore') }}';
    
    let inputs = `@csrf`;
    driverIds.forEach(id => {
        inputs += `<input type="hidden" name="driver_ids[]" value="${id}">`;
    });
    form.innerHTML = inputs;
    
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}

// ═══════════════════════════════════════════════════════════════════════════
// MODALES DE RESTAURATION ET SUPPRESSION INDIVIDUELLES
// ═══════════════════════════════════════════════════════════════════════════
function showRestoreModal(driverId, firstName, lastName, employeeNumber) {
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Restaurer le chauffeur
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Êtes-vous sûr de vouloir restaurer ce chauffeur ? Il redeviendra actif dans la flotte.
                            </p>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-green-600 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-white">
                                            ${firstName.charAt(0)}${lastName.charAt(0)}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-green-900">${firstName} ${lastName}</p>
                                        <p class="text-sm text-green-700">Matricule: ${employeeNumber}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmRestore(${driverId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 hover:bg-green-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Restaurer
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

function showForceDeleteModal(driverId, firstName, lastName, employeeNumber) {
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
                            ⚠️ Suppression Définitive
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">
                                <strong class="text-red-600">ATTENTION :</strong> Cette action est <strong>IRRÉVERSIBLE</strong>.
                            </p>
                            <p class="text-sm text-gray-500 mb-4">
                                Toutes les données du chauffeur (affectations, sanctions, etc.) seront <strong>définitivement supprimées</strong>.
                            </p>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-red-600 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-white">
                                            ${firstName.charAt(0)}${lastName.charAt(0)}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-red-900">${firstName} ${lastName}</p>
                                        <p class="text-sm text-red-700">Matricule: ${employeeNumber}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmForceDelete(${driverId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer Définitivement
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

function confirmRestore(driverId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/drivers/${driverId}/restore`;
    form.innerHTML = `
        @csrf
        @method('PATCH')
    `;
    document.body.appendChild(form);
    closeModal();
    setTimeout(() => form.submit(), 200);
}

function confirmForceDelete(driverId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/drivers/${driverId}/force-delete`;
    form.innerHTML = `
        @csrf
        @method('DELETE')
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
</script>
@endpush
@endsection
