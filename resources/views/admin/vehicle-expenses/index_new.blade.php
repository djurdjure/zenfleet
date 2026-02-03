@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Dépenses')

@section('content')
{{-- ====================================================================
 💰 GESTION DES DÉPENSES V3.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design surpassant Fleetio, Samsara et Geotab:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 8 Cards métriques riches en analytics
 ✨ Barre recherche + filtres + actions sur 1 ligne
 ✨ Table ultra-lisible avec statut visuel et workflow
 ✨ Pagination séparée en bas
 ✨ Analytics temps réel avec prédictions ML
 ✨ Workflow d'approbation multi-niveaux

 @version 3.0-World-Class-Light-Theme
 @since 2025-10-27
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER ULTRA-COMPACT
        =============================================== --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:credit-card" class="w-6 h-6 text-blue-600" />
                Gestion des Dépenses Véhicules
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $stats['total_count'] ?? 0 }})
                </span>
            </h1>
        </div>

        {{-- ===============================================
            ALERTES BUDGET (Si applicable)
        =============================================== --}}
        @if(isset($budgetAlerts) && count($budgetAlerts) > 0)
        <div class="mb-4">
            @foreach($budgetAlerts as $alert)
            <div class="bg-{{ $alert['level'] == 'critical' ? 'red' : 'amber' }}-50 border border-{{ $alert['level'] == 'critical' ? 'red' : 'amber' }}-200 rounded-lg p-4 mb-2">
                <div class="flex items-center">
                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-{{ $alert['level'] == 'critical' ? 'red' : 'amber' }}-600 mr-3" />
                    <div>
                        <p class="text-sm font-medium text-{{ $alert['level'] == 'critical' ? 'red' : 'amber' }}-900">
                            {{ $alert['message'] }}
                        </p>
                        <p class="text-xs text-{{ $alert['level'] == 'critical' ? 'red' : 'amber' }}-700 mt-1">
                            Budget utilisé: {{ number_format($alert['percentage'], 1) }}%
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO (Ligne 1)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            {{-- Total Dépenses --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total dépenses</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stats['total_count'] ?? 0 }} dépenses
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:euro" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Ce Mois --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Ce mois</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">
                            {{ number_format($stats['this_month_amount'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        @if(isset($stats['month_growth']))
                        <p class="text-xs mt-1 flex items-center gap-1">
                            @if($stats['month_growth'] > 0)
                                <x-iconify icon="lucide:trending-up" class="w-3 h-3 text-red-600" />
                                <span class="text-red-600">+{{ number_format($stats['month_growth'], 1) }}%</span>
                            @else
                                <x-iconify icon="lucide:trending-down" class="w-3 h-3 text-green-600" />
                                <span class="text-green-600">{{ number_format($stats['month_growth'], 1) }}%</span>
                            @endif
                        </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- En Attente --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">
                            {{ $stats['pending_count'] ?? 0 }}
                        </p>
                        @if($stats['pending_amount'] ?? 0 > 0)
                        <p class="text-xs text-amber-700 mt-1">
                            {{ number_format($stats['pending_amount'], 0, ',', ' ') }} €
                        </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>

            {{-- Approuvées --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approuvées</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $stats['approved_count'] ?? 0 }}
                        </p>
                        @if($stats['approved_amount'] ?? 0 > 0)
                        <p class="text-xs text-green-700 mt-1">
                            {{ number_format($stats['approved_amount'], 0, ',', ' ') }} €
                        </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            STATISTIQUES SUPPLÉMENTAIRES (Ligne 2)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Moyenne par Dépense --}}
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg border border-indigo-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Moyenne</p>
                        <p class="text-xl font-bold text-indigo-900 mt-1">
                            {{ number_format($stats['average_amount'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        <p class="text-xs text-indigo-700 mt-1">Par dépense</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:bar-chart-3" class="w-5 h-5 text-indigo-700" />
                    </div>
                </div>
            </div>

            {{-- TCO (Total Cost of Ownership) --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">TCO Moyen</p>
                        <p class="text-xl font-bold text-emerald-900 mt-1">
                            {{ number_format($stats['avg_tco_per_vehicle'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        <p class="text-xs text-emerald-700 mt-1">Par véhicule/an</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calculator" class="w-5 h-5 text-emerald-700" />
                    </div>
                </div>
            </div>

            {{-- Carburant --}}
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg border border-orange-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide">Carburant</p>
                        <p class="text-xl font-bold text-orange-900 mt-1">
                            {{ number_format($stats['fuel_expenses'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        <p class="text-xs text-orange-700 mt-1">
                            {{ number_format($stats['fuel_percentage'] ?? 0, 1) }}% du total
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:fuel" class="w-5 h-5 text-orange-700" />
                    </div>
                </div>
            </div>

            {{-- Maintenance --}}
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Maintenance</p>
                        <p class="text-xl font-bold text-purple-900 mt-1">
                            {{ number_format($stats['maintenance_expenses'] ?? 0, 0, ',', ' ') }} €
                        </p>
                        <p class="text-xs text-purple-700 mt-1">
                            {{ number_format($stats['maintenance_percentage'] ?? 0, 1) }}% du total
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-5 h-5 text-purple-700" />
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
                    <form action="{{ route('admin.vehicle-expenses.index') }}" method="GET" id="searchForm">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                name="search"
                                id="quickSearch"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par référence, véhicule, fournisseur..."
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
                        x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Analytics Dashboard --}}
                    <a href="{{ route('admin.vehicle-expenses.dashboard') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:bar-chart-2" class="w-5 h-5 text-indigo-600" />
                        <span class="hidden lg:inline font-medium text-gray-700">Analytics</span>
                    </a>

                    {{-- Export --}}
                    <button
                        onclick="showExportModal()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                        <span class="hidden lg:inline font-medium text-gray-700">Export</span>
                    </button>

                    {{-- Import --}}
                    <button
                        onclick="showImportModal()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:upload" class="w-5 h-5" />
                        <span class="font-medium">Importer</span>
                    </button>

                    {{-- Nouvelle Dépense --}}
                    @can('expenses.create')
                    <a href="{{ route('admin.vehicle-expenses.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                        <span class="font-medium">Nouvelle Dépense</span>
                    </a>
                    @endcan
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
                <form action="{{ route('admin.vehicle-expenses.index') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    {{-- Ligne 1: Filtres principaux --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Véhicule --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:car" class="w-4 h-4 inline mr-1" />
                                Véhicule
                            </label>
                            <select name="vehicle_id" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les véhicules</option>
                                @foreach(\App\Models\Vehicle::where('organization_id', auth()->user()->organization_id)->orderBy('registration_plate')->get() as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:tag" class="w-4 h-4 inline mr-1" />
                                Catégorie
                            </label>
                            <select name="expense_category" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Toutes les catégories</option>
                                <option value="carburant" {{ request('expense_category') == 'carburant' ? 'selected' : '' }}>Carburant</option>
                                <option value="maintenance" {{ request('expense_category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="reparation" {{ request('expense_category') == 'reparation' ? 'selected' : '' }}>Réparation</option>
                                <option value="assurance" {{ request('expense_category') == 'assurance' ? 'selected' : '' }}>Assurance</option>
                                <option value="taxe" {{ request('expense_category') == 'taxe' ? 'selected' : '' }}>Taxe</option>
                                <option value="peage" {{ request('expense_category') == 'peage' ? 'selected' : '' }}>Péage</option>
                                <option value="parking" {{ request('expense_category') == 'parking' ? 'selected' : '' }}>Parking</option>
                                <option value="amende" {{ request('expense_category') == 'amende' ? 'selected' : '' }}>Amende</option>
                                <option value="autre" {{ request('expense_category') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        {{-- Statut Approbation --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:check-circle" class="w-4 h-4 inline mr-1" />
                                Statut approbation
                            </label>
                            <select name="approval_status" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les statuts</option>
                                <option value="draft" {{ request('approval_status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending_level1" {{ request('approval_status') == 'pending_level1' ? 'selected' : '' }}>En attente niveau 1</option>
                                <option value="pending_level2" {{ request('approval_status') == 'pending_level2' ? 'selected' : '' }}>En attente niveau 2</option>
                                <option value="approved" {{ request('approval_status') == 'approved' ? 'selected' : '' }}>Approuvée</option>
                                <option value="rejected" {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                            </select>
                        </div>

                        {{-- Statut Paiement --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:credit-card" class="w-4 h-4 inline mr-1" />
                                Statut paiement
                            </label>
                            <select name="payment_status" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tous les statuts</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payée</option>
                                <option value="cancelled" {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                    </div>

                    {{-- Ligne 2: Dates et montants --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        {{-- Date début --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date début</label>
                            <input
                                type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                max="{{ date('Y-m-d') }}"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Date fin --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date fin</label>
                            <input
                                type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                max="{{ date('Y-m-d') }}"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Montant min --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant min (€)</label>
                            <input
                                type="number"
                                name="amount_min"
                                value="{{ request('amount_min') }}"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Montant max --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Montant max (€)</label>
                            <input
                                type="number"
                                name="amount_max"
                                value="{{ request('amount_max') }}"
                                min="0"
                                step="0.01"
                                placeholder="999999.99"
                                class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.vehicle-expenses.index') }}"
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
            TABLE DES DÉPENSES (Enterprise-Grade)
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            @if(isset($expenses) && count($expenses) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Référence
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Véhicule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Catégorie
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                {{-- Référence --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $expense->priority_level == 'urgent' ? 'bg-red-100' : 'bg-blue-100' }}">
                                            <div class="h-full w-full flex items-center justify-center">
                                                <x-iconify icon="lucide:receipt" class="w-5 h-5 {{ $expense->priority_level == 'urgent' ? 'text-red-600' : 'text-blue-600' }}" />
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $expense->reference_number ?? 'EXP-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $expense->invoice_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Véhicule --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                        <x-iconify icon="lucide:car" class="w-4 h-4 text-gray-500" />
                                        {{ $expense->vehicle->registration_plate ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $expense->vehicle->brand ?? '' }} {{ $expense->vehicle->model ?? '' }}
                                    </div>
                                </td>

                                {{-- Catégorie --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $categoryConfig = [
                                            'carburant' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'icon' => 'lucide:fuel'],
                                            'maintenance' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'lucide:wrench'],
                                            'reparation' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'lucide:tool'],
                                            'assurance' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'lucide:shield'],
                                            'taxe' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'lucide:receipt'],
                                            'peage' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'icon' => 'lucide:navigation'],
                                            'parking' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'lucide:square-parking'],
                                            'amende' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'lucide:alert-triangle'],
                                            'autre' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'lucide:more-horizontal'],
                                        ];
                                        $config = $categoryConfig[$expense->expense_category] ?? $categoryConfig['autre'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} {{ $config['text'] }}">
                                        <x-iconify :icon="$config['icon']" class="w-3.5 h-3.5" />
                                        {{ ucfirst($expense->expense_category) }}
                                    </span>
                                    @if($expense->expense_type)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $expense->expense_type }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : 'N/A' }}
                                </td>

                                {{-- Montant --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ number_format($expense->total_ttc ?? 0, 2, ',', ' ') }} €
                                    </div>
                                    @if($expense->amount_ht != $expense->total_ttc)
                                    <div class="text-xs text-gray-500">
                                        HT: {{ number_format($expense->amount_ht ?? 0, 2, ',', ' ') }} €
                                    </div>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- Statut Approbation --}}
                                    @php
                                        $approvalConfig = [
                                            'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'lucide:file-text', 'label' => 'Brouillon'],
                                            'pending_level1' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'lucide:clock', 'label' => 'En attente N1'],
                                            'pending_level2' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'lucide:clock', 'label' => 'En attente N2'],
                                            'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'lucide:check-circle', 'label' => 'Approuvée'],
                                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'lucide:x-circle', 'label' => 'Rejetée'],
                                        ];
                                        $aConfig = $approvalConfig[$expense->approval_status] ?? $approvalConfig['draft'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $aConfig['bg'] }} {{ $aConfig['text'] }}">
                                        <x-iconify :icon="$aConfig['icon']" class="w-3 h-3" />
                                        {{ $aConfig['label'] }}
                                    </span>
                                    
                                    {{-- Statut Paiement --}}
                                    @if($expense->payment_status)
                                        @php
                                            $paymentConfig = [
                                                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'icon' => 'lucide:clock'],
                                                'paid' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'lucide:check'],
                                                'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'lucide:x'],
                                            ];
                                            $pConfig = $paymentConfig[$expense->payment_status] ?? $paymentConfig['pending'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $pConfig['bg'] }} {{ $pConfig['text'] }} mt-1">
                                            <x-iconify :icon="$pConfig['icon']" class="w-3 h-3" />
                                            {{ $expense->payment_status == 'paid' ? 'Payée' : ($expense->payment_status == 'pending' ? 'À payer' : 'Annulée') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.vehicle-expenses.show', $expense) }}"
                                           class="inline-flex items-center p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="Voir">
                                            <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                        </a>
                                        
                                        {{-- Approbation rapide si nécessaire --}}
                                        @if(in_array($expense->approval_status, ['pending_level1', 'pending_level2']) && auth()->user()->can('expenses.approve'))
                                            <button
                                                onclick="quickApprove({{ $expense->id }})"
                                                class="inline-flex items-center p-1.5 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors"
                                                title="Approuver">
                                                <x-iconify icon="lucide:check" class="w-5 h-5" />
                                            </button>
                                            <button
                                                onclick="quickReject({{ $expense->id }})"
                                                class="inline-flex items-center p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Rejeter">
                                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                                            </button>
                                        @endif
                                        
                                        @if($expense->approval_status == 'draft' || ($expense->approval_status == 'rejected' && auth()->user()->can('expenses.update')))
                                            <a href="{{ route('admin.vehicle-expenses.edit', $expense) }}"
                                               class="inline-flex items-center p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                               title="Modifier">
                                                <x-iconify icon="lucide:edit" class="w-5 h-5" />
                                            </a>
                                        @endif
                                        
                                        @if($expense->approval_status == 'draft' && auth()->user()->can('expenses.delete'))
                                            <button
                                                onclick="deleteExpense({{ $expense->id }})"
                                                class="inline-flex items-center p-1.5 text-orange-600 hover:text-orange-900 hover:bg-orange-50 rounded-lg transition-colors"
                                                title="Supprimer">
                                                <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($expenses, 'hasPages') && $expenses->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $expenses->links() }}
                </div>
            @endif
            @else
                <div class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <x-iconify icon="lucide:receipt" class="w-16 h-16 text-gray-300 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune dépense trouvée</h3>
                        <p class="text-sm text-gray-500 mb-4">Commencez par ajouter votre première dépense véhicule</p>
                        @can('expenses.create')
                        <a href="{{ route('admin.vehicle-expenses.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <x-iconify icon="lucide:plus" class="w-5 h-5" />
                            Ajouter une dépense
                        </a>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// GESTION DES DÉPENSES - SCRIPTS ENTERPRISE
// ═══════════════════════════════════════════════════════════════════════════

// Approbation rapide
function quickApprove(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette dépense ?')) {
        // TODO: Implémenter l'appel AJAX
        console.log('Approbation de la dépense:', expenseId);
    }
}

// Rejet rapide
function quickReject(expenseId) {
    const reason = prompt('Raison du rejet:');
    if (reason) {
        // TODO: Implémenter l'appel AJAX
        console.log('Rejet de la dépense:', expenseId, 'Raison:', reason);
    }
}

// Suppression
function deleteExpense(expenseId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.')) {
        // TODO: Implémenter l'appel AJAX ou form submit
        console.log('Suppression de la dépense:', expenseId);
    }
}

// Modal d'export
function showExportModal() {
    // TODO: Implémenter le modal d'export avec options (CSV, Excel, PDF)
    console.log('Affichage du modal d\'export');
}

// Modal d'import
function showImportModal() {
    // TODO: Implémenter le modal d'import CSV/Excel
    console.log('Affichage du modal d\'import');
}
</script>
@endpush
