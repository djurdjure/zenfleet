@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Chauffeurs')

@section('content')
{{-- ====================================================================
 👥 GESTION DES CHAUFFEURS V8.0 - WORLD-CLASS ENTERPRISE-GRADE
 ====================================================================

 Design unifié avec vehicles/index.blade.php
 ✨ Composants réutilisables (x-page-header, x-stat-card, x-data-table)
 ✨ Design system ZenFleet strict (.zenfleet-*)
 ✨ x-iconify exclusivement
 ✨ Animations fluides et transitions
 ✨ Accessibilité WCAG 2.1 AA
 ✨ Responsive perfectionné

 @version 8.0-World-Class-Unified-Design
 @since 2025-01-20
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen animate-fade-in">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-8">

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 animate-fade-in">
            <x-alert type="success" title="Succès" dismissible>
                {{ session('success') }}
            </x-alert>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 animate-fade-in">
            <x-alert type="danger" title="Erreur" dismissible>
                {{ session('error') }}
            </x-alert>
        </div>
        @endif

        {{-- Page Header --}}
        <x-page-header
            title="Gestion des Chauffeurs"
            subtitle="Gérez votre équipe de {{ isset($drivers) ? $drivers->total() : 0 }} chauffeurs"
            icon="heroicons:user-group"
            :breadcrumbs="[
 ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
 ['label' => 'Chauffeurs', 'url' => null]
 ]">
            <x-slot name="actions">
                @can('export drivers')
                <button type="button"
                    class="zenfleet-btn bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-zenfleet"
                    title="Exporter (Ctrl+E)">
                    <x-iconify icon="heroicons:arrow-down-tray" class="w-5 h-5" />
                    <span class="hidden xl:inline">Exporter</span>
                </button>
                @endcan

                @can('import drivers')
                <a href="{{ route('admin.drivers.import.show') }}"
                    class="zenfleet-btn bg-green-600 text-white hover:bg-green-700 shadow-zenfleet">
                    <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
                    <span class="hidden xl:inline">Importer</span>
                </a>
                @endcan

                <a href="{{ route('admin.drivers.archived') }}"
                    class="zenfleet-btn bg-amber-600 text-white hover:bg-amber-700 shadow-zenfleet">
                    <x-iconify icon="heroicons:archive-box" class="w-5 h-5" />
                    <span class="hidden xl:inline">Archives</span>
                </a>

                @can('create drivers')
                <a href="{{ route('admin.drivers.create') }}"
                    class="zenfleet-btn bg-blue-600 text-white hover:bg-blue-700 shadow-zenfleet-lg">
                    <x-iconify icon="heroicons:plus-circle" class="w-5 h-5" />
                    <span>Nouveau chauffeur</span>
                </a>
                @endcan
            </x-slot>
        </x-page-header>

        {{-- ===============================================
 METRIC CARDS - ENTERPRISE STATISTICS
 =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <x-stat-card
                title="Total chauffeurs"
                :value="$analytics['total_drivers'] ?? 0"
                subtitle="vs mois dernier"
                icon="heroicons:user-group"
                color="blue"
                trend="up"
                trendValue="+8%"
                animate />

            <x-stat-card
                title="Actifs"
                :value="$analytics['active_drivers'] ?? 0"
                subtitle="Chauffeurs disponibles"
                icon="heroicons:check-circle"
                color="green"
                animate />

            <x-stat-card
                title="En mission"
                :value="$analytics['assigned_drivers'] ?? 0"
                subtitle="Affectés à un véhicule"
                icon="heroicons:truck"
                color="orange"
                animate />

            <x-stat-card
                title="Inactifs"
                :value="$analytics['inactive_drivers'] ?? 0"
                subtitle="Nécessitent attention"
                icon="heroicons:exclamation-triangle"
                color="red"
                animate />
        </div>

        {{-- ===============================================
 SECONDARY STATS - GRADIENT CARDS
 =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Ancienneté Moyenne --}}
            <div class="zenfleet-card p-6 bg-gradient-to-br from-blue-50 via-white to-blue-50 border-blue-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <x-iconify icon="heroicons:calendar-days" class="w-5 h-5 text-blue-600" />
                            </div>
                            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Ancienneté moyenne</p>
                        </div>
                        <p class="text-2xl font-bold text-blue-900">
                            {{ number_format($analytics['avg_seniority_years'] ?? 0, 1) }} <span class="text-lg">ans</span>
                        </p>
                        <p class="text-xs text-blue-700 mt-1">Dans l'entreprise</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center">
                        <x-iconify icon="heroicons:clock" class="w-8 h-8 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Taux de Disponibilité --}}
            <div class="zenfleet-card p-6 bg-gradient-to-br from-emerald-50 via-white to-emerald-50 border-emerald-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <x-iconify icon="heroicons:chart-pie" class="w-5 h-5 text-emerald-600" />
                            </div>
                            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Disponibilité</p>
                        </div>
                        <p class="text-2xl font-bold text-emerald-900">
                            {{ number_format($analytics['availability_rate'] ?? 0, 0) }}<span class="text-lg">%</span>
                        </p>
                        <p class="text-xs text-emerald-700 mt-1">Taux de disponibilité</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl flex items-center justify-center">
                        <x-iconify icon="heroicons:user-circle" class="w-8 h-8 text-emerald-600" />
                    </div>
                </div>
            </div>

            {{-- Permis Valides --}}
            <div class="zenfleet-card p-6 bg-gradient-to-br from-purple-50 via-white to-purple-50 border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <x-iconify icon="heroicons:identification" class="w-5 h-5 text-purple-600" />
                            </div>
                            <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide">Permis valides</p>
                        </div>
                        <p class="text-2xl font-bold text-purple-900">
                            {{ number_format($analytics['valid_licenses'] ?? 0, 0) }}
                        </p>
                        <p class="text-xs text-purple-700 mt-1">Sur {{ $analytics['total_drivers'] ?? 0 }} chauffeurs</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                        <x-iconify icon="heroicons:shield-check" class="w-8 h-8 text-purple-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
 SEARCH + FILTERS
 =============================================== --}}
        <div class="mb-6" x-data="{ showFilters: false }">
            <div class="zenfleet-card p-6">
                {{-- Search Row --}}
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    {{-- Search Input --}}
                    <div class="flex-1 w-full">
                        <form action="{{ route('admin.drivers.index') }}" method="GET" id="searchForm">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <x-iconify icon="heroicons:magnifying-glass" class="w-5 h-5 text-gray-400" />
                                </div>
                                <input
                                    type="search"
                                    name="search"
                                    id="quickSearch"
                                    value="{{ request('search') }}"
                                    placeholder="Rechercher par nom, téléphone, email, permis..."
                                    class="zenfleet-input pl-11 pr-4"
                                    autocomplete="off">
                                @if(request('search'))
                                <button type="button"
                                    onclick="document.getElementById('quickSearch').value=''; document.getElementById('searchForm').submit();"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Filters Button --}}
                        <button
                            @click="showFilters = !showFilters"
                            type="button"
                            class="zenfleet-btn bg-white text-gray-700 border-gray-300 hover:bg-gray-50 shadow-zenfleet relative">
                            <x-iconify icon="heroicons:funnel" class="w-5 h-5" />
                            <span>Filtres</span>
                            @php
                            $activeFiltersCount = count(request()->except(['page', 'per_page', 'search']));
                            @endphp
                            @if($activeFiltersCount > 0)
                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-blue-600 rounded-full ring-2 ring-white">
                                {{ $activeFiltersCount }}
                            </span>
                            @endif
                            <x-iconify
                                icon="heroicons:chevron-down"
                                class="w-4 h-4 transition-transform duration-200"
                                ::class="{ 'rotate-180': showFilters }" />
                        </button>

                        {{-- View Toggle --}}
                        <div class="flex items-center gap-1 border border-gray-300 rounded-lg p-1 bg-white">
                            <button type="button" class="p-2 text-blue-600 bg-blue-50 rounded hover:bg-blue-100 transition-colors" title="Vue tableau">
                                <x-iconify icon="heroicons:table-cells" class="w-5 h-5" />
                            </button>
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-100 transition-colors" title="Vue grille">
                                <x-iconify icon="heroicons:squares-2x2" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filters Panel --}}
                <div x-show="showFilters"
                    x-collapse
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="mt-6 pt-6 border-t border-gray-200">

                    <form action="{{ route('admin.drivers.index') }}" method="GET">
                        @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Status Filter --}}
                            <div>
                                <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <x-iconify icon="heroicons:signal" class="w-4 h-4 inline mr-1" />
                                    Statut
                                </label>
                                <select name="status_id" id="status_id" class="zenfleet-input">
                                    <option value="">Tous les statuts</option>
                                    @foreach($driverStatuses ?? [] as $status)
                                    <option value="{{ $status->id }}" @selected(request('status_id')==$status->id)>
                                        {{ $status->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- License Type --}}
                            <div>
                                <label for="license_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    <x-iconify icon="heroicons:identification" class="w-4 h-4 inline mr-1" />
                                    Type de permis
                                </label>
                                <select name="license_type" id="license_type" class="zenfleet-input">
                                    <option value="">Tous les permis</option>
                                    <option value="B" @selected(request('license_type')=='B' )>Permis B</option>
                                    <option value="C" @selected(request('license_type')=='C' )>Permis C</option>
                                    <option value="D" @selected(request('license_type')=='D' )>Permis D</option>
                                    <option value="EC" @selected(request('license_type')=='EC' )>Permis EC</option>
                                </select>
                            </div>

                            {{-- Assignment Status --}}
                            <div>
                                <label for="assignment_status" class="block text-sm font-medium text-gray-700 mb-2">
                                    <x-iconify icon="heroicons:truck" class="w-4 h-4 inline mr-1" />
                                    Affectation
                                </label>
                                <select name="assignment_status" id="assignment_status" class="zenfleet-input">
                                    <option value="">Tous</option>
                                    <option value="assigned" @selected(request('assignment_status')=='assigned' )>Affecté</option>
                                    <option value="available" @selected(request('assignment_status')=='available' )>Disponible</option>
                                </select>
                            </div>

                            {{-- Per Page --}}
                            <div>
                                <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                    <x-iconify icon="heroicons:document-duplicate" class="w-4 h-4 inline mr-1" />
                                    Par page
                                </label>
                                <select name="per_page" id="per_page" class="zenfleet-input">
                                    @foreach(['20', '50', '100'] as $value)
                                    <option value="{{ $value }}" @selected(request('per_page', '20' )==$value)>
                                        {{ $value }} résultats
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Filter Actions --}}
                        <div class="mt-6 flex items-center justify-between">
                            <a href="{{ route('admin.drivers.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                <x-iconify icon="heroicons:arrow-path" class="w-4 h-4" />
                                Réinitialiser
                            </a>
                            <div class="flex items-center gap-3">
                                <button type="button" @click="showFilters = false"
                                    class="zenfleet-btn bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                                    Annuler
                                </button>
                                <button type="submit"
                                    class="zenfleet-btn bg-blue-600 text-white hover:bg-blue-700 shadow-zenfleet-lg">
                                    <x-iconify icon="heroicons:check" class="w-5 h-5" />
                                    Appliquer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ===============================================
 DRIVERS TABLE - WORLD-CLASS
 =============================================== --}}
        @if($drivers && $drivers->count() > 0)
        <x-data-table
            :headers="[
 ['label' => 'Chauffeur', 'icon' => 'heroicons:user'],
 ['label' => 'Contact', 'icon' => 'heroicons:phone'],
 ['label' => 'Permis', 'icon' => 'heroicons:identification'],
 ['label' => 'Véhicule', 'icon' => 'heroicons:truck'],
 ['label' => 'Statut', 'icon' => 'heroicons:signal'],
 ['label' => 'Actions', 'align' => 'center']
 ]">
            @foreach($drivers as $index => $driver)
            <tr>
                {{-- Driver Info --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center group-hover:scale-[1.02] transition-transform duration-200">
                        <div class="flex-shrink-0 h-12 w-12">
                            @if($driver->user && $driver->user->profile_photo_path)
                            <img src="{{ Storage::url($driver->user->profile_photo_path) }}"
                                alt="{{ $driver->first_name }} {{ $driver->last_name }}"
                                class="h-12 w-12 rounded-full object-cover ring-2 ring-blue-200 shadow-sm">
                            @else
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-200 shadow-sm">
                                <span class="text-base font-bold text-white">
                                    {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900">
                                {{ $driver->first_name }} {{ $driver->last_name }}
                            </div>
                            <div class="text-sm text-gray-600">
                                ID: {{ $driver->id }}
                            </div>
                        </div>
                    </div>
                </td>

                {{-- Contact --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        <div class="flex items-center gap-1.5 mb-1">
                            <x-iconify icon="heroicons:phone" class="w-4 h-4 text-gray-400" />
                            {{ $driver->personal_phone ?? 'N/A' }}
                        </div>
                        @if($driver->user && $driver->user->email)
                        <div class="flex items-center gap-1.5 text-gray-600">
                            <x-iconify icon="heroicons:envelope" class="w-4 h-4 text-gray-400" />
                            {{ Str::limit($driver->user->email, 25) }}
                        </div>
                        @endif
                    </div>
                </td>

                {{-- License --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm">
                        <div class="font-semibold text-gray-900">
                            {{ $driver->license_number ?? 'N/A' }}
                        </div>
                        @if($driver->license_expiry_date)
                        <div class="text-xs text-gray-600 mt-1">
                            Exp: {{ \Carbon\Carbon::parse($driver->license_expiry_date)->format('d/m/Y') }}
                        </div>
                        @endif
                    </div>
                </td>

                {{-- Vehicle Assignment --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                    $activeAssignment = $driver->assignments->where('end_datetime', null)->first();
                    $vehicle = $activeAssignment->vehicle ?? null;
                    @endphp
                    @if($vehicle)
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <x-iconify icon="heroicons:truck" class="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $vehicle->registration_plate }}
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </div>
                        </div>
                    </div>
                    @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        Non affecté
                    </span>
                    @endif
                </td>

                {{-- Status --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                    $statusColors = [
                    'Actif' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'ring' => 'ring-green-200', 'icon' => 'heroicons:check-circle'],
                    'Inactif' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-200', 'icon' => 'heroicons:x-circle'],
                    'En congé' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'ring' => 'ring-orange-200', 'icon' => 'heroicons:calendar'],
                    'Suspendu' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'ring' => 'ring-red-200', 'icon' => 'heroicons:no-symbol']
                    ];
                    $statusName = $driver->driverStatus->name ?? 'Inconnu';
                    $colorConfig = $statusColors[$statusName] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-200', 'icon' => 'heroicons:question-mark-circle'];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $colorConfig['bg'] }} {{ $colorConfig['text'] }} ring-1 {{ $colorConfig['ring'] }}">
                        <x-iconify :icon="$colorConfig['icon']" class="w-3.5 h-3.5" />
                        {{ $statusName }}
                    </span>
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-1">
                        @can('view drivers')
                        <a href="{{ route('admin.drivers.show', $driver) }}"
                            class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-all duration-200 group/btn"
                            title="Voir les détails">
                            <x-iconify icon="heroicons:eye" class="w-5 h-5 group-hover/btn:scale-110 transition-transform" />
                        </a>
                        @endcan
                        @can('edit drivers')
                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                            class="p-2 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-all duration-200 group/btn"
                            title="Modifier">
                            <x-iconify icon="heroicons:pencil-square" class="w-5 h-5 group-hover/btn:scale-110 transition-transform" />
                        </a>
                        @endcan
                        @can('delete drivers')
                        <button
                            onclick="deleteDriver({{ $driver->id }}, '{{ $driver->first_name }} {{ $driver->last_name }}')"
                            class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-all duration-200 group/btn"
                            title="Archiver">
                            <x-iconify icon="heroicons:archive-box" class="w-5 h-5 group-hover/btn:scale-110 transition-transform" />
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforeach
        </x-data-table>

        {{-- Pagination --}}
        <div class="mt-6">
            <div class="zenfleet-card p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <p class="text-sm text-gray-700">
                            Affichage de
                            <span class="font-semibold text-gray-900">{{ $drivers->firstItem() ?? 0 }}</span>
                            à
                            <span class="font-semibold text-gray-900">{{ $drivers->lastItem() ?? 0 }}</span>
                            sur
                            <span class="font-semibold text-gray-900">{{ $drivers->total() }}</span>
                            chauffeurs
                        </p>
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                            <x-iconify icon="heroicons:document-text" class="w-4 h-4 text-blue-600" />
                            <span class="text-xs font-semibold text-blue-700">
                                Page {{ $drivers->currentPage() }} / {{ $drivers->lastPage() }}
                            </span>
                        </div>
                    </div>
                    <div>
                        {{ $drivers->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>

        @else
        {{-- Empty State --}}
        <div class="zenfleet-card p-12">
            <div class="max-w-md mx-auto text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full flex items-center justify-center ring-8 ring-blue-50/50">
                    <x-iconify icon="heroicons:user-group" class="w-12 h-12 text-blue-600 animate-pulse-slow" />
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Aucun chauffeur trouvé
                </h3>

                <p class="text-sm text-gray-600 mb-8">
                    @if(request()->has('search') || request()->except(['page', 'per_page', 'search']))
                    Aucun chauffeur ne correspond à vos critères de recherche.
                    <br>Essayez de modifier vos filtres.
                    @else
                    Commencez par ajouter votre premier chauffeur pour gérer votre équipe.
                    @endif
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if(request()->has('search') || request()->except(['page', 'per_page', 'search']))
                    <a href="{{ route('admin.drivers.index') }}"
                        class="zenfleet-btn bg-white text-gray-700 border-gray-300 hover:bg-gray-50">
                        <x-iconify icon="heroicons:arrow-path" class="w-5 h-5" />
                        Réinitialiser les filtres
                    </a>
                    @endif

                    @can('create drivers')
                    <a href="{{ route('admin.drivers.create') }}"
                        class="zenfleet-btn bg-blue-600 text-white hover:bg-blue-700 shadow-zenfleet-lg">
                        <x-iconify icon="heroicons:plus-circle" class="w-5 h-5" />
                        Ajouter un chauffeur
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endif

    </div>
</section>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
            <div class="bg-white px-6 pt-6 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-full bg-red-100 sm:mx-0 ring-4 ring-red-50">
                        <x-iconify icon="heroicons:exclamation-triangle" class="h-8 w-8 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">
                            Archiver le chauffeur
                        </h3>
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 mb-4">
                                Voulez-vous archiver ce chauffeur ? Cette action peut être annulée ultérieurement depuis la liste des chauffeurs archivés.
                            </p>
                            <div id="driverInfo" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-iconify icon="heroicons:user" class="h-7 w-7 text-blue-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="modalDriverName" class="font-bold text-blue-900 text-base truncate"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                <button
                    type="button"
                    id="confirmDeleteBtn"
                    class="zenfleet-btn bg-red-600 text-white hover:bg-red-700 shadow-zenfleet w-full sm:w-auto">
                    <x-iconify icon="heroicons:archive-box" class="w-5 h-5" />
                    Archiver
                </button>
                <button
                    type="button"
                    onclick="closeModal()"
                    class="zenfleet-btn bg-white text-gray-700 border-gray-300 hover:bg-gray-50 w-full sm:w-auto mt-3 sm:mt-0">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentDriverId = null;

    function deleteDriver(driverId, driverName) {
        currentDriverId = driverId;
        document.getElementById('modalDriverName').textContent = driverName;

        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');

        document.getElementById('confirmDeleteBtn').onclick = () => confirmDelete(driverId);
    }

    function confirmDelete(driverId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/drivers/${driverId}`;
        form.innerHTML = `
 @csrf
 @method('DELETE')
 `;
        document.body.appendChild(form);
        closeModal();
        setTimeout(() => form.submit(), 200);
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('opacity-0');
            currentDriverId = null;
        }, 200);
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            console.log('Export triggered');
        }

        if (e.key === 'Escape') {
            closeModal();
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            document.getElementById('quickSearch').focus();
        }
    });

    // Auto-submit search
    let searchTimeout;
    document.getElementById('quickSearch')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                document.getElementById('searchForm').submit();
            }
        }, 500);
    });
</script>
@endpush