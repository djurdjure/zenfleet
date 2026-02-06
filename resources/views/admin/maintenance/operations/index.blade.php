@extends('layouts.admin.catalyst')

@section('title', 'Gestion de la Maintenance')

@section('content')
{{-- ====================================================================
 🔧 GESTION DE LA MAINTENANCE V1.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design surpassant Fleetio, Samsara et Salesforce:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne (py-4 lg:py-6)
 ✨ 8 Cards métriques ultra-riches en information
 ✨ Barre recherche + filtres + actions + vues multiples
 ✨ Table ultra-lisible avec status visuel
 ✨ Vues alternatives: Kanban, Calendrier, Timeline
 ✨ Thème clair 100% cohérent avec le reste de l'app

 @version 1.0-World-Class-Enterprise
 @since 2025-10-23
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER ULTRA-COMPACT AVEC ACTIONS
        =============================================== --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <x-iconify icon="lucide:wrench" class="w-6 h-6 text-blue-600" />
                    Gestion de la Maintenance
                    <span class="ml-2 text-sm font-normal text-gray-500">
                        ({{ $operations->total() }})
                    </span>
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Planifiez, suivez et optimisez toutes vos opérations de maintenance
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Sélecteur de vue --}}
                <div class="flex bg-white rounded-lg border border-gray-200 p-1" x-data="{ view: 'list' }">
                    <button
                        @click="view = 'list'"
                        :class="view === 'list' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 flex items-center gap-1.5"
                        title="Vue Liste">
                        <x-iconify icon="lucide:list" class="w-4 h-4" />
                        Liste
                    </button>
                    <button
                        @click="view = 'kanban'; window.location.href='{{ route('admin.maintenance.operations.kanban') }}'"
                        :class="view === 'kanban' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 flex items-center gap-1.5"
                        title="Vue Kanban">
                        <x-iconify icon="lucide:columns-3" class="w-4 h-4" />
                        Kanban
                    </button>
                    <button
                        @click="view = 'calendar'; window.location.href='{{ route('admin.maintenance.operations.calendar') }}'"
                        :class="view === 'calendar' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-200 flex items-center gap-1.5"
                        title="Vue Calendrier">
                        <x-iconify icon="lucide:calendar" class="w-4 h-4" />
                        Calendrier
                    </button>
                </div>

                {{-- Bouton Nouvelle Maintenance --}}
                <a href="{{ route('admin.maintenance.operations.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-4 h-4" />
                    Nouvelle Maintenance
                </a>
            </div>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO (8 MÉTRIQUES)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Opérations --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total opérations</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ $analytics['total_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Ce mois: {{ $analytics['monthly_operations'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Planifiées --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Planifiées</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">
                            {{ $analytics['planned_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Prochains 7j: {{ $analytics['upcoming_count'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-clock" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- En Cours --}}
            <div class="bg-orange-50 rounded-lg border border-orange-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">En cours</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">
                            {{ $analytics['in_progress_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Véhicules: {{ $analytics['vehicles_in_maintenance'] ?? 0 }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:loader" class="w-5 h-5 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- En Retard --}}
            <div class="bg-red-50 rounded-lg border border-red-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">En retard</p>
                        <p class="text-xl font-bold text-red-600 mt-1">
                            {{ $analytics['overdue_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-red-500 mt-1">
                            <x-iconify icon="lucide:alert-triangle" class="w-3 h-3 inline" />
                            Nécessitent attention
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Complétées --}}
            <div class="bg-green-50 rounded-lg border border-green-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Complétées</p>
                        <p class="text-xl font-bold text-green-600 mt-1">
                            {{ $analytics['completed_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Taux: {{ number_format(($analytics['completed_operations'] ?? 0) / max($analytics['total_operations'] ?? 1, 1) * 100, 1) }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Coût Total --}}
            <div class="bg-purple-50 rounded-lg border border-purple-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Coût total</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">
                            {{ number_format($analytics['total_cost'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Moyen: {{ number_format($analytics['avg_cost'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:banknote" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- Durée Moyenne --}}
            <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Durée moyenne</p>
                        <p class="text-xl font-bold text-indigo-600 mt-1">
                            {{ number_format($analytics['avg_duration_minutes'] ?? 0, 0) }} min
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Total: {{ number_format($analytics['total_duration_hours'] ?? 0, 1) }}h
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-5 h-5 text-indigo-600" />
                    </div>
                </div>
            </div>

            {{-- Annulées --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Annulées</p>
                        <p class="text-xl font-bold text-gray-500 mt-1">
                            {{ $analytics['cancelled_operations'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Taux: {{ number_format(($analytics['cancelled_operations'] ?? 0) / max($analytics['total_operations'] ?? 1, 1) * 100, 1) }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:x-circle" class="w-5 h-5 text-gray-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            STATISTIQUES SUPPLÉMENTAIRES (TOP PERFORMERS)
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Top 5 Véhicules avec Plus de Maintenance --}}
            <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Véhicules à surveiller</p>
                        <p class="text-sm text-red-700 mt-0.5">Plus de maintenances</p>
                    </div>
                    <div class="w-10 h-10 bg-red-200 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:trending-up" class="w-5 h-5 text-red-700" />
                    </div>
                </div>
                @if(isset($analytics['top_vehicles']) && $analytics['top_vehicles']->count() > 0)
                <ul class="space-y-2">
                    @foreach($analytics['top_vehicles']->take(5) as $item)
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-red-900 font-medium truncate">{{ $item->vehicle->registration_plate }}</span>
                        <span class="text-red-700 font-bold">{{ $item->count }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-red-700">Aucune donnée disponible</p>
                @endif
            </div>

            {{-- Top 5 Types de Maintenance --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Types fréquents</p>
                        <p class="text-sm text-blue-700 mt-0.5">Maintenances courantes</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-200 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:bar-chart" class="w-5 h-5 text-blue-700" />
                    </div>
                </div>
                @if(isset($analytics['top_types']) && $analytics['top_types']->count() > 0)
                <ul class="space-y-2">
                    @foreach($analytics['top_types']->take(5) as $item)
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-blue-900 font-medium truncate">{{ $item->maintenanceType->name }}</span>
                        <span class="text-blue-700 font-bold">{{ $item->count }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-blue-700">Aucune donnée disponible</p>
                @endif
            </div>

            {{-- Prédiction & Alertes --}}
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg border border-yellow-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide">Alertes & Prédictions</p>
                        <p class="text-sm text-yellow-700 mt-0.5">Maintenances à venir</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-200 border border-yellow-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:bell" class="w-5 h-5 text-yellow-700" />
                    </div>
                </div>
                <ul class="space-y-2">
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">Prochains 7 jours</span>
                        <span class="text-yellow-700 font-bold">{{ $analytics['upcoming_count'] ?? 0 }}</span>
                    </li>
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">En retard</span>
                        <span class="text-red-700 font-bold">{{ $analytics['overdue_operations'] ?? 0 }}</span>
                    </li>
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">Planifié ce mois</span>
                        <span class="text-yellow-700 font-bold">{{ $analytics['cost_planned'] ?? 0 }} DA</span>
                    </li>
                </ul>
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
                    <form action="{{ route('admin.maintenance.operations.index') }}" method="GET" id="searchForm">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                            </div>
                            <input
                                type="text"
                                name="search"
                                id="quickSearch"
                                value="{{ request('search') }}"
                                placeholder="Rechercher par véhicule, type de maintenance, fournisseur..."
                                class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                                onchange="document.getElementById('searchForm').submit()">
                        </div>
                    </form>
                </div>

                {{-- Bouton Filtres Avancés --}}
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <x-iconify icon="lucide:filter" class="w-4 h-4" />
                    Filtres avancés
                    @if(request()->hasAny(['status', 'maintenance_type_id', 'provider_id', 'category', 'date_from', 'date_to']))
                    <span class="bg-blue-50 text-blue-700 border border-blue-200 text-xs font-semibold px-2 py-0.5 rounded-full">
                        Actifs
                    </span>
                    @endif
                </button>

                {{-- Boutons Actions Rapides --}}
                <div class="flex items-center gap-2">
                    {{-- Export --}}
                    <a href="{{ route('admin.maintenance.operations.export', request()->all()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <x-iconify icon="lucide:download" class="w-4 h-4" />
                        Export
                    </a>

                    {{-- Refresh --}}
                    <button
                        onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <x-iconify icon="lucide:refresh-cw" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- ===============================================
                FILTRES AVANCÉS (COLLAPSIBLE)
            =============================================== --}}
            <div
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="mt-4 bg-white rounded-lg border border-gray-200 p-5 shadow-sm"
                style="display: none;">

                <form action="{{ route('admin.maintenance.operations.index') }}" method="GET" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Filtre Statut --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:info" class="w-4 h-4 inline" /> Statut
                            </label>
                            <select name="status" id="status" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les statuts</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planifiée</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>

                        {{-- Filtre Type de Maintenance --}}
                        <div>
                            <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:tag" class="w-4 h-4 inline" /> Type de maintenance
                            </label>
                            <select name="maintenance_type_id" id="maintenance_type_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les types</option>
                                @foreach($maintenanceTypes as $type)
                                <option value="{{ $type->id }}" {{ request('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtre Véhicule --}}
                        <div>
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:car" class="w-4 h-4 inline" /> Véhicule
                            </label>
                            <select name="vehicle_id" id="vehicle_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les véhicules</option>
                                @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtre Fournisseur --}}
                        <div>
                            <label for="provider_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:building" class="w-4 h-4 inline" /> Fournisseur
                            </label>
                            <select name="provider_id" id="provider_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Tous les fournisseurs</option>
                                @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ request('provider_id') == $provider->id ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filtre Date De --}}
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:calendar" class="w-4 h-4 inline" /> Date de début
                            </label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Filtre Date À --}}
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:calendar" class="w-4 h-4 inline" /> Date de fin
                            </label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        {{-- Filtre Catégorie --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">
                                <x-iconify icon="lucide:layers" class="w-4 h-4 inline" /> Catégorie
                            </label>
                            <select name="category" id="category" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Toutes catégories</option>
                                <option value="preventive" {{ request('category') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                                <option value="corrective" {{ request('category') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                                <option value="inspection" {{ request('category') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="emergency" {{ request('category') == 'emergency' ? 'selected' : '' }}>Urgence</option>
                            </select>
                        </div>

                        {{-- Filtre En Retard --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">&nbsp;</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="overdue" value="1" {{ request('overdue') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4 inline text-red-500" />
                                    Seulement en retard
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Boutons Actions Filtres --}}
                    <div class="flex items-center justify-between mt-5 pt-5 border-t border-gray-200">
                        <button
                            type="button"
                            onclick="document.getElementById('filterForm').reset(); window.location.href='{{ route('admin.maintenance.operations.index') }}'"
                            class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                            <x-iconify icon="lucide:x" class="w-4 h-4 inline" /> Réinitialiser
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200">
                            <x-iconify icon="lucide:filter" class="w-4 h-4" />
                            Appliquer les filtres
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===============================================
            TABLE ULTRA-LISIBLE
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

            {{-- Header Table avec Tri --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:list" class="w-4 h-4" />
                        Liste des Opérations
                        <span class="text-xs font-normal text-gray-500">({{ $operations->total() }} résultats)</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-600">Trier par:</label>
                        <select
                            onchange="window.location.href='{{ route('admin.maintenance.operations.index') }}?' + new URLSearchParams(Object.fromEntries(new URLSearchParams(window.location.search).entries())).toString() + '&sort=' + this.value.split(':')[0] + '&direction=' + this.value.split(':')[1]"
                            class="text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="scheduled_date:desc" {{ request('sort') == 'scheduled_date' && request('direction') == 'desc' ? 'selected' : '' }}>Date (récent)</option>
                            <option value="scheduled_date:asc" {{ request('sort') == 'scheduled_date' && request('direction') == 'asc' ? 'selected' : '' }}>Date (ancien)</option>
                            <option value="total_cost:desc" {{ request('sort') == 'total_cost' && request('direction') == 'desc' ? 'selected' : '' }}>Coût (élevé)</option>
                            <option value="total_cost:asc" {{ request('sort') == 'total_cost' && request('direction') == 'asc' ? 'selected' : '' }}>Coût (faible)</option>
                            <option value="status:asc" {{ request('sort') == 'status' ? 'selected' : '' }}>Statut</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                V

                                échicule
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type de maintenance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date planifiée
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fournisseur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Coût
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($operations as $operation)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- Véhicule --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $operation->vehicle->registration_plate }}</div>
                                        <div class="text-xs text-gray-500">{{ $operation->vehicle->brand }} {{ $operation->vehicle->model }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $operation->maintenanceType->getCategoryColor() }}"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $operation->maintenanceType->name }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($operation->maintenanceType->category ?? '') }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $operation->scheduled_date?->format('d/m/Y') ?? 'Non définie' }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($operation->scheduled_date)
                                    {{ $operation->scheduled_date->diffForHumans() }}
                                    @endif
                                </div>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $operation->getStatusBadge() !!}
                            </td>

                            {{-- Fournisseur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($operation->provider)
                                <div class="text-sm text-gray-900">{{ $operation->provider->name }}</div>
                                <div class="text-xs text-gray-500">{{ $operation->provider->contact_phone ?? '' }}</div>
                                @else
                                <span class="text-sm text-gray-400">Non défini</span>
                                @endif
                            </td>

                            {{-- Coût --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($operation->total_cost)
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($operation->total_cost, 0, ',', ' ') }} DA</div>
                                @else
                                <span class="text-sm text-gray-400">Non défini</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Voir --}}
                                    <a href="{{ route('admin.maintenance.operations.show', $operation) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir détails">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>

                                    {{-- Éditer --}}
                                    @can('update', $operation)
                                    <a href="{{ route('admin.maintenance.operations.edit', $operation) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan

                                    {{-- Actions rapides selon statut --}}
                                    @if($operation->status === 'planned')
                                    <form action="{{ route('admin.maintenance.operations.start', $operation) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-200 group"
                                            title="Démarrer">
                                            <x-iconify icon="lucide:play" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Supprimer --}}
                                    @can('delete', $operation)
                                    <form action="{{ route('admin.maintenance.operations.destroy', $operation) }}" method="POST"
                                        onsubmit="return confirm('Confirmer la suppression ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group"
                                            title="Supprimer">
                                            <x-iconify icon="lucide:trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="lucide:inbox" class="w-12 h-12 text-gray-400 mb-3" />
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune opération trouvée</h3>
                                    <p class="text-sm text-gray-500 mb-4">Commencez par créer une nouvelle opération de maintenance.</p>
                                    <a href="{{ route('admin.maintenance.operations.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                        Nouvelle Maintenance
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <x-pagination-standard :paginator="$operations" :records-per-page="request('per_page', 15)" />
            </div>
        </div>

    </div>
</section>

{{-- Script Alpine.js pour interactions --}}
@push('scripts')
<script>
    // Auto-submit form on filter change
    document.querySelectorAll('#filterForm select, #filterForm input[type="date"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush

@endsection