{{-- ====================================================================
 💰 GESTION DES DÉPENSES V2.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 🚀 Design cohérent avec gestion véhicules et chauffeurs:
 ✅ Fond gris clair premium (bg-gray-50)
 ✅ Header compact moderne (py-4 lg:py-6)
 ✅ 7 Cards métriques simples et lisibles
 ✅ Bouton filtre collapsible avec Alpine.js
 ✅ Barre recherche + filtres + actions sur 1 ligne
 ✅ Table ultra-lisible avec hover states
 ✅ Pagination séparée en bas
 ✅ Composants Design System cohérents

 @version 2.0-World-Class-Cohérent
 @since 2025-10-27
 @author Expert Fullstack Developer (20+ ans)
 ==================================================================== --}}

@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Dépenses')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER ULTRA-COMPACT (comme véhicules)
        =============================================== --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:credit-card" class="w-6 h-6 text-blue-600" />
                Gestion des Dépenses
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ isset($expenses) ? $expenses->total() : 0 }})
                </span>
            </h1>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO (7 capsules)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Dépenses --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total dépenses</p>
                        <p class="text-xl font-bold text-blue-700 mt-1">
                            {{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stats['total_count'] ?? 0 }} dépenses
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:coins" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- En Attente --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">En attente</p>
                        <p class="text-xl font-bold text-yellow-600 mt-1">
                            {{ $stats['pending_count'] ?? 0 }}
                        </p>
                        @if(($stats['pending_count'] ?? 0) > 0)
                        <p class="text-xs text-yellow-600 mt-1 flex items-center gap-1">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                            Action requise
                        </p>
                        @else
                        <p class="text-xs text-green-600 mt-1">✓ À jour</p>
                        @endif
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-5 h-5 text-yellow-600" />
                    </div>
                </div>
            </div>

            {{-- Approuvées --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Approuvées</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            {{ $stats['approved_count'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Validées</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Rejetées --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Rejetées</p>
                        <p class="text-xl font-bold text-red-600 mt-1">
                            {{ $stats['rejected_count'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Refusées</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:x-circle" class="w-5 h-5 text-red-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            STATS SUPPLÉMENTAIRES (3 cards gradient)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Ce Mois --}}
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg border border-purple-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Ce mois</p>
                        <p class="text-xl font-bold text-purple-900 mt-1">
                            {{ number_format($stats['this_month_amount'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                        <p class="text-xs text-purple-700 mt-1">{{ now()->format('F Y') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-5 h-5 text-purple-700" />
                    </div>
                </div>
            </div>

            {{-- Moyenne par dépense --}}
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-lg border border-teal-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-teal-600 uppercase tracking-wide">Moyenne</p>
                        <p class="text-xl font-bold text-teal-900 mt-1">
                            {{ number_format($stats['average_amount'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                        <p class="text-xs text-teal-700 mt-1">Par dépense</p>
                    </div>
                    <div class="w-10 h-10 bg-teal-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:bar-chart-3" class="w-5 h-5 text-teal-700" />
                    </div>
                </div>
            </div>

            {{-- Budget utilisé --}}
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg border border-indigo-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Budget</p>
                        <p class="text-xl font-bold text-indigo-900 mt-1">
                            {{ $stats['budget_percentage'] ?? 85 }}%
                        </p>
                        <p class="text-xs text-indigo-700 mt-1">Utilisé</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:chart-pie" class="w-5 h-5 text-indigo-700" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            BARRE RECHERCHE + FILTRES + ACTIONS (1 ligne)
        =============================================== --}}
        <div class="mb-6" x-data="{ showFilters: false }">
            {{-- Ligne principale --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">

                    {{-- Recherche rapide --}}
                    <div class="flex-1 w-full lg:w-auto">
                        <form action="{{ route('admin.expenses.index') }}" method="GET" id="searchForm">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                                </div>
                                <input
                                    type="text"
                                    name="search"
                                    id="quickSearch"
                                    value="{{ $filters['search'] ?? '' }}"
                                    placeholder="Rechercher par référence, description, véhicule..."
                                    class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                                    onchange="document.getElementById('searchForm').submit()">
                            </div>
                        </form>
                    </div>

                    {{-- Bouton Filtres --}}
                    <button
                        @click="showFilters = !showFilters"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md"
                        :class="showFilters ? 'ring-2 ring-blue-500 bg-blue-50' : ''">
                        <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                        <span class="font-medium text-gray-700">Filtres</span>
                        @php
                        $activeFiltersCount = count(array_filter($filters ?? [], fn($v) => !empty($v)));
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

                    {{-- Boutons Actions --}}
                    <div class="flex items-center gap-2">
                        <button
                            onclick="exportExpenses()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                            <span class="hidden lg:inline font-medium text-gray-700">Export</span>
                        </button>

                        @can('create expenses')
                        <a href="{{ route('admin.expenses.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:plus-circle" class="w-5 h-5" />
                            <span class="hidden sm:inline">Nouvelle dépense</span>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- ===============================================
                FILTRES COLLAPSIBLES (Alpine.js)
            =============================================== --}}
            <div x-show="showFilters"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 style="display: none;"
                 class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">

                <form method="GET" action="{{ route('admin.expenses.index') }}">
                    {{-- Ligne 1: Filtres principaux --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        {{-- Véhicule --}}
                        <div>
                            <label for="vehicle-filter" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:car" class="w-4 h-4 inline mr-1" />
                                Véhicule
                            </label>
                            <select
                                name="vehicle_id"
                                id="vehicle-filter"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les véhicules</option>
                                @foreach($vehicles ?? [] as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ ($filters['vehicle_id'] ?? '') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Type de dépense --}}
                        <div>
                            <label for="type-filter" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:tags" class="w-4 h-4 inline mr-1" />
                                Type
                            </label>
                            <select
                                name="expense_type"
                                id="type-filter"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les types</option>
                                @foreach($expenseTypes ?? [] as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['expense_type'] ?? '') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 inline mr-1" />
                                Statut
                            </label>
                            <select
                                name="status"
                                id="status-filter"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="all" {{ ($filters['status'] ?? 'all') == 'all' ? 'selected' : '' }}>Tous</option>
                                <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>Approuvées</option>
                                <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>Rejetées</option>
                            </select>
                        </div>

                        {{-- Nombre par page --}}
                        <div>
                            <label for="per-page-filter" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:list" class="w-4 h-4 inline mr-1" />
                                Par page
                            </label>
                            <select
                                name="per_page"
                                id="per-page-filter"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="10" {{ ($filters['per_page'] ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($filters['per_page'] ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ ($filters['per_page'] ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($filters['per_page'] ?? 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    {{-- Ligne 2: Dates et montants --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        {{-- Date début --}}
                        <div>
                            <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                                Date début
                            </label>
                            <input
                                type="date"
                                name="date_from"
                                id="date-from"
                                value="{{ $filters['date_from'] ?? '' }}"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Date fin --}}
                        <div>
                            <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                                Date fin
                            </label>
                            <input
                                type="date"
                                name="date_to"
                                id="date-to"
                                value="{{ $filters['date_to'] ?? '' }}"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Montant min --}}
                        <div>
                            <label for="amount-min" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:coins" class="w-4 h-4 inline mr-1" />
                                Montant min
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="amount_min"
                                    id="amount-min"
                                    value="{{ $filters['amount_min'] ?? '' }}"
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                    class="block w-full pr-12 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">DA</span>
                            </div>
                        </div>

                        {{-- Montant max --}}
                        <div>
                            <label for="amount-max" class="block text-sm font-medium text-gray-700 mb-1">
                                <x-iconify icon="lucide:coins" class="w-4 h-4 inline mr-1" />
                                Montant max
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="amount_max"
                                    id="amount-max"
                                    value="{{ $filters['amount_max'] ?? '' }}"
                                    step="0.01"
                                    min="0"
                                    placeholder="999999.99"
                                    class="block w-full pr-12 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">DA</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer filtres --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                <x-iconify icon="lucide:search" class="w-4 h-4" />
                                Appliquer
                            </button>
                            <a href="{{ route('admin.expenses.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                                <x-iconify icon="lucide:x" class="w-4 h-4" />
                                Réinitialiser
                            </a>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $expenses->total() }} résultat(s)
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===============================================
            TABLE DES DÉPENSES ULTRA-PRO
        =============================================== --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Référence
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Véhicule
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            {{-- Référence --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <x-iconify icon="lucide:receipt-text" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $expense->reference_number ?? 'EXP-' . $expense->id }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $expense->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Véhicule --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <x-iconify icon="lucide:car" class="w-4 h-4 text-gray-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $expense->vehicle->registration_plate ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $expense->vehicle->brand ?? '' }} {{ $expense->vehicle->model ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $expense->category_label ?? 'Non défini' }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : 'N/A' }}
                            </td>

                            {{-- Montant --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($expense->total_ttc ?? 0, 2, ',', ' ') }} DA
                                </div>
                                @if($expense->amount_ht != $expense->total_ttc)
                                <div class="text-xs text-gray-500">
                                    HT: {{ number_format($expense->amount_ht ?? 0, 2, ',', ' ') }} DA
                                </div>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                $status = $expense->status_badge ?? ['color' => 'gray', 'label' => 'Inconnu'];
                                $statusClass = match($status['color']) {
                                    'green' => 'bg-green-100 text-green-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                    'red' => 'bg-red-100 text-red-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.expenses.show', $expense) }}"
                                       class="p-1.5 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Voir">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4" />
                                    </a>
                                    @if($expense->needs_approval && !$expense->approved)
                                    <button onclick="approveExpense({{ $expense->id }})"
                                            class="p-1.5 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Approuver">
                                        <x-iconify icon="lucide:check" class="w-4 h-4" />
                                    </button>
                                    @endif
                                    @can('update expenses')
                                    <a href="{{ route('admin.expenses.edit', $expense) }}"
                                       class="p-1.5 text-gray-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                       title="Modifier">
                                        <x-iconify icon="lucide:pencil" class="w-4 h-4" />
                                    </a>
                                    @endcan
                                    @can('delete expenses')
                                    <button onclick="deleteExpense({{ $expense->id }})"
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-iconify icon="lucide:inbox" class="w-10 h-10 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune dépense trouvée</h3>
                                    <p class="text-gray-600 mb-4">Commencez par ajouter une nouvelle dépense</p>
                                    @can('create expenses')
                                    <a href="{{ route('admin.expenses.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                        Nouvelle dépense
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($expenses->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-sm text-gray-700">
                        Affichage de <span class="font-medium">{{ $expenses->firstItem() }}</span> à
                        <span class="font-medium">{{ $expenses->lastItem() }}</span> sur
                        <span class="font-medium">{{ $expenses->total() }}</span> dépenses
                    </div>
                    <div>
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
</section>

{{-- ===============================================
    STYLES CUSTOM
=============================================== --}}
@push('styles')
<style>
[x-cloak] {
    display: none !important;
}
</style>
@endpush

{{-- ===============================================
    SCRIPTS JAVASCRIPT
=============================================== --}}
@push('scripts')
<script>
// Export des dépenses
function exportExpenses() {
    console.log('Export des dépenses...');
    // TODO: Implémenter l'export
}

// Approbation d'une dépense
function approveExpense(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette dépense ?')) {
        console.log('Approbation de la dépense:', expenseId);
        // TODO: Implémenter l'approbation
    }
}

// Suppression d'une dépense
function deleteExpense(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.')) {
        console.log('Suppression de la dépense:', expenseId);
        // TODO: Implémenter la suppression
    }
}
</script>
@endpush

@endsection
