<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(auth()->check())
    <meta name="user-data" content="{{ json_encode(['id' => auth()->id(), 'name' => auth()->user()->name, 'role' => auth()->user()->getRoleNames()->first()]) }}">
    @endif

    <title>@yield('title', 'ZenFleet Admin') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    {{-- ====================================================================
 📦 ZENFLEET ENTERPRISE-GRADE ASSETS
 ==================================================================== 
 SlimSelect & Flatpickr: bundled locally via Vite (no CDN)
 Icons: Iconify runtime (loads icons from CDN, caches locally)
 FontAwesome: REMOVED - migrating to Iconify progressively
 @version 2.2 Enterprise-Ready
 ==================================================================== --}}

    {{-- Iconify CDN - Original runtime that renders icons from data-icon attributes --}}
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    {{-- Flatpickr Custom Styles ZenFleet --}}
    <style>
        /* 🎨 FLATPICKR ENTERPRISE-GRADE LIGHT MODE - ZenFleet Ultra-Pro */
        .flatpickr-calendar {
            background-color: white !important;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            font-family: inherit;
        }

        /* En-tête (mois/année) - Bleu blue-600 premium */
        .flatpickr-months {
            background: rgb(37 99 235) !important;
            border-radius: 0.75rem 0.75rem 0 0;
            padding: 0.875rem 0;
        }

        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background-color: transparent !important;
            color: white !important;
            font-weight: 600;
            font-size: 1rem;
        }

        /* Boutons navigation */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            fill: white !important;
            transition: all 0.2s;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            fill: rgb(219 234 254) !important;
            transform: scale(1.15);
        }

        /* Jours de la semaine */
        .flatpickr-weekdays {
            background-color: rgb(249 250 251) !important;
            padding: 0.625rem 0;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .flatpickr-weekday {
            color: rgb(107 114 128) !important;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Corps du calendrier */
        .flatpickr-days {
            background-color: white !important;
        }

        /* Jours du mois */
        .flatpickr-day {
            color: rgb(17 24 39) !important;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .flatpickr-day.today {
            border: 2px solid rgb(37 99 235) !important;
            font-weight: 700;
            color: rgb(37 99 235) !important;
            background-color: rgb(239 246 255) !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background-color: rgb(37 99 235) !important;
            border-color: rgb(37 99 235) !important;
            color: white !important;
            font-weight: 700;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
        }

        .flatpickr-day:hover:not(.selected):not(.flatpickr-disabled):not(.today) {
            background-color: rgb(243 244 246) !important;
            border-color: rgb(229 231 235) !important;
            color: rgb(17 24 39) !important;
            transform: scale(1.05);
        }

        .flatpickr-day.flatpickr-disabled {
            color: rgb(209 213 219) !important;
            opacity: 0.4;
        }
    </style>

    @vite(['resources/js/admin/app.js'])
    @stack('styles')
    @livewireStyles
</head>

<body class="h-full">
    <div class="min-h-full">
        {{-- Sidebar pour desktop - Style Ultra-Pro World-Class --}}
        <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="flex grow flex-col overflow-hidden bg-[#eef2f7] border-r border-gray-200/60 shadow-sm">
                {{-- En-tête avec logo Premium --}}
                <div class="w-full flex-none px-4 py-4 h-16 flex items-center border-b border-gray-300/50">
                    <div class="flex items-center w-full">
                        <div class="relative mr-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-md">
                                <x-iconify icon="mdi:truck-fast" class="w-5 h-5 text-white" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <span class="text-gray-800 text-lg font-bold tracking-tight">ZenFleet</span>
                            <div class="text-xs text-gray-600 font-medium">Fleet Management</div>
                        </div>
                    </div>
                </div>

                {{-- Navigation Enterprise --}}
                <div class="flex flex-col flex-1 overflow-hidden">
                    <ul class="grow overflow-x-hidden overflow-y-auto w-full px-2 py-4 mb-0 scrollbar-thin scrollbar-thumb-gray-400/30 scrollbar-track-transparent" role="tree">
                        {{-- Dashboard --}}
                        <li class="flex">
                            @php
                            $dashboardRoute = auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Supervisor'])
                            ? route('admin.dashboard')
                            : route('driver.dashboard');
                            $isDashboardActive = request()->routeIs('admin.dashboard', 'driver.dashboard', 'dashboard');
                            @endphp
                            <a href="{{ $dashboardRoute }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $isDashboardActive ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="material-symbols:dashboard-rounded" class="w-5 h-5 mr-3 {{ $isDashboardActive ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Dashboard</span>
                            </a>
                        </li>

                        {{-- Organisations --}}
                        @can('view organizations')
                        <li class="flex">
                            <a href="{{ route('admin.organizations.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.organizations.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:office-building" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.organizations.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Organisations</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Véhicules avec sous-menu --}}
                        @canany(['view vehicles', 'view assignments'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:car-multiple" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Véhicules</span>
                                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            $vehicleBarHeight = request()->routeIs('admin.vehicles.index') ? '50%' : (request()->routeIs('admin.assignments.*') ? '50%' : '0%');
                                            $vehicleBarTop = request()->routeIs('admin.assignments.*') ? '50%' : '0%';
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: {{ $vehicleBarHeight }}; top: {{ $vehicleBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        @can('view vehicles')
                                        <a href="{{ route('admin.vehicles.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicles.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:format-list-bulleted" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicles.index') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Gestion Véhicules
                                        </a>
                                        @endcan
                                        @can('view assignments')
                                        <a href="{{ route('admin.assignments.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.assignments.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:clipboard-text" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.assignments.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Affectations
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcanany

                        {{-- Chauffeurs avec sous-menu --}}
                        @canany(['view drivers', 'view all driver sanctions', 'view team driver sanctions', 'view own driver sanctions'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:account-group" class="w-5 h-5 mr-3 {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Chauffeurs</span>
                                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-96"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-96"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            $driverBarHeight = request()->routeIs('admin.drivers.index') ? '50%' : (request()->routeIs('admin.drivers.sanctions.*') ? '50%' : '0%');
                                            $driverBarTop = request()->routeIs('admin.drivers.sanctions.*') ? '50%' : '0%';
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: {{ $driverBarHeight }}; top: {{ $driverBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        @can('view drivers')
                                        <a href="{{ route('admin.drivers.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.drivers.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:view-list" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.drivers.index') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Liste
                                        </a>
                                        @endcan
                                        @canany(['view all driver sanctions', 'view team driver sanctions', 'view own driver sanctions'])
                                        <a href="{{ route('admin.drivers.sanctions.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.drivers.sanctions.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:gavel" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.drivers.sanctions.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Sanctions
                                        </a>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcanany

                        {{-- Dépôts - ENTERPRISE GRADE --}}
                        @can('view depots')
                        <li class="flex">
                            <a href="{{ route('admin.depots.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.depots.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:office-building" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.depots.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Dépôts</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Demandes de Réparation - Chauffeurs uniquement (menu séparé) --}}
                        @hasrole('Chauffeur')
                        @can('view own repair requests')
                        <li class="flex">
                            <a href="{{ route('driver.repair-requests.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('driver.repair-requests.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:tools" class="w-5 h-5 mr-3 {{ request()->routeIs('driver.repair-requests.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Mes Demandes</span>
                            </a>
                        </li>
                        @endcan
                        @endhasrole

                        {{-- Kilométrage avec sous-menus - Accessible à tous les rôles avec permission --}}
                        @canany(['view own mileage readings', 'view team mileage readings', 'view all mileage readings'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.mileage-readings.*', 'driver.mileage.*', 'admin.vehicles.*.mileage-history') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.mileage-readings.*', 'driver.mileage.*', 'admin.vehicles.*.mileage-history') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:speedometer" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.mileage-readings.*', 'driver.mileage.*', 'admin.vehicles.*.mileage-history') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Kilométrage</span>
                                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            $mileageBarTop = request()->routeIs('admin.mileage-readings.update', 'driver.mileage.update') ? '50%' : '0%';
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300 h-1/2"
                                                x-bind:style="`top: {{ $mileageBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <ul class="flex-1 space-y-1 pb-2">
                                        {{-- Historique --}}
                                        <li>
                                            @php
                                            $mileageIndexRoute = auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Supervisor'])
                                            ? route('admin.mileage-readings.index')
                                            : route('admin.mileage-readings.index');
                                            $isMileageIndexActive = request()->routeIs('admin.mileage-readings.index');
                                            @endphp
                                            <a href="{{ $mileageIndexRoute }}"
                                                class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $isMileageIndexActive ? 'bg-blue-100/70 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                                <x-iconify icon="mdi:history" class="w-5 h-5 mr-2 {{ $isMileageIndexActive ? 'text-blue-600' : 'text-gray-600' }}" />
                                                Historique
                                            </a>
                                        </li>
                                        {{-- Mettre à jour --}}
                                        @can('create mileage readings')
                                        <li>
                                            @php
                                            $mileageUpdateRoute = auth()->user()->hasRole('Chauffeur')
                                            ? route('driver.mileage.update')
                                            : route('admin.mileage-readings.update');
                                            $isMileageUpdateActive = request()->routeIs('admin.mileage-readings.update', 'driver.mileage.update');
                                            @endphp
                                            <a href="{{ $mileageUpdateRoute }}"
                                                class="flex items-center h-9 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 {{ $isMileageUpdateActive ? 'bg-blue-100/70 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                                <x-iconify icon="mdi:pencil" class="w-5 h-5 mr-2 {{ $isMileageUpdateActive ? 'text-blue-600' : 'text-gray-600' }}" />
                                                Mettre à jour
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </li>
                        @endcanany

                        {{-- ====================================================================
 🔧 MAINTENANCE - MENU ULTRA-PRO ENTERPRISE GRADE
 ==================================================================== 
 Architecture nouvelle génération qui surpasse Fleetio, Samsara, Geotab
 - Structure hiérarchique claire
 - Icônes Iconify premium cohérentes
 - Barre de progression dynamique
 - États actifs intelligents
 @version 2.0 Ultra-Professional
 @since 2025-10-23
 ==================================================================== --}}
                        @canany(['view maintenance', 'view team repair requests', 'view all repair requests', 'view own repair requests'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="lucide:wrench" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Maintenance</span>
                                <x-iconify icon="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-[500px]"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-[500px]"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            // Calcul intelligent de la position de la barre bleue selon la route active
                                            $maintenanceBarHeight = '0%';
                                            $maintenanceBarTop = '0%';
                                            $itemHeight = 16.67; // 100% / 6 items

                                            if (request()->routeIs('admin.maintenance.dashboard*')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = '0%';
                                            } elseif (request()->routeIs('admin.maintenance.operations.index')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = $itemHeight.'%';
                                            } elseif (request()->routeIs('admin.maintenance.operations.kanban')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = ($itemHeight * 2).'%';
                                            } elseif (request()->routeIs('admin.maintenance.operations.calendar')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = ($itemHeight * 3).'%';
                                            } elseif (request()->routeIs('admin.maintenance.schedules.*')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = ($itemHeight * 4).'%';
                                            } elseif (request()->routeIs('admin.repair-requests.*')) {
                                            $maintenanceBarHeight = $itemHeight.'%';
                                            $maintenanceBarTop = ($itemHeight * 5).'%';
                                            }
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: {{ $maintenanceBarHeight }}; top: {{ $maintenanceBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        {{-- Vue d'ensemble / Dashboard --}}
                                        <a href="{{ route('admin.maintenance.dashboard') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.maintenance.dashboard*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:layout-dashboard" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.dashboard*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Vue d'ensemble
                                        </a>

                                        {{-- Opérations - Liste --}}
                                        <a href="{{ route('admin.maintenance.operations.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:list" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.operations.index') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Opérations
                                        </a>

                                        {{-- Vue Kanban --}}
                                        <a href="{{ route('admin.maintenance.operations.kanban') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.kanban') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:columns-3" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.operations.kanban') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Kanban
                                        </a>

                                        {{-- Vue Calendrier --}}
                                        <a href="{{ route('admin.maintenance.operations.calendar') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.calendar') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:calendar-days" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.operations.calendar') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Calendrier
                                        </a>

                                        {{-- Planifications Préventives --}}
                                        <a href="{{ route('admin.maintenance.schedules.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:repeat" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Planifications
                                        </a>

                                        {{-- Demandes de Réparation --}}
                                        @canany(['view team repair requests', 'view all repair requests'])
                                        <a href="{{ route('admin.repair-requests.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.repair-requests.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:hammer" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.repair-requests.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Demandes Réparation
                                        </a>
                                        @endcanany
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcanany

                        {{-- Alertes --}}
                        @can('view alerts')
                        <li class="flex">
                            <a href="{{ route('admin.alerts.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.alerts.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:bell-ring" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.alerts.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Alertes</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Documents --}}
                        @can('view documents')
                        <li class="flex">
                            <a href="{{ route('admin.documents.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:file-document" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.documents.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Documents</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Fournisseurs --}}
                        @can('view suppliers')
                        <li class="flex">
                            <a href="{{ route('admin.suppliers.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:store" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.suppliers.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Fournisseurs</span>
                            </a>
                        </li>
                        @endcan

                        {{-- ====================================================================
 💰 GESTION DES DÉPENSES - MENU ULTRA-PRO ENTERPRISE GRADE
 ====================================================================
 Module complet de gestion financière surpassant Fleetio, Samsara, Geotab
 - Workflow d'approbation multi-niveaux
 - Analytics temps réel avec ML predictions
 - TCO et budgets intelligents
 - Export multi-format
 @version 1.0 Enterprise Ultra-Pro
 @since 2025-10-27
 ==================================================================== --}}
                        @canany(['view expenses', 'create expenses', 'approve expenses'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.vehicle-expenses.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="solar:wallet-money-bold" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.vehicle-expenses.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Dépenses</span>
                                @php
                                $pendingExpenses = \App\Models\VehicleExpense::where('organization_id', auth()->user()->organization_id)
                                ->whereIn('approval_status', ['pending_level1', 'pending_level2'])
                                ->count();
                                @endphp
                                @if($pendingExpenses > 0)
                                <span class="bg-yellow-500 text-white text-xs rounded-full px-2 py-0.5 mr-2">{{ $pendingExpenses }}</span>
                                @endif
                                <x-iconify icon="lucide:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-[400px]"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-[400px]"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            // Calcul de la position de la barre bleue selon la route active
                                            $expenseBarHeight = '0%';
                                            $expenseBarTop = '0%';
                                            $expenseItemHeight = 14.29; // 100% / 7 items

                                            if (request()->routeIs('admin.vehicle-expenses.index')) {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = '0%';
                                            } elseif (request()->routeIs('admin.vehicle-expenses.create')) {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = $expenseItemHeight.'%';
                                            } elseif (request()->routeIs('admin.vehicle-expenses.dashboard')) {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = ($expenseItemHeight * 2).'%';
                                            } elseif (request()->url() == route('admin.vehicle-expenses.index').'?filter=pending_approval') {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = ($expenseItemHeight * 3).'%';
                                            } elseif (request()->url() == route('admin.vehicle-expenses.index').'?section=groups') {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = ($expenseItemHeight * 4).'%';
                                            } elseif (request()->routeIs('admin.vehicle-expenses.export')) {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = ($expenseItemHeight * 5).'%';
                                            } elseif (request()->routeIs('admin.vehicle-expenses.analytics.cost-trends')) {
                                            $expenseBarHeight = $expenseItemHeight.'%';
                                            $expenseBarTop = ($expenseItemHeight * 6).'%';
                                            }
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: {{ $expenseBarHeight }}; top: {{ $expenseBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        {{-- Vue d'ensemble --}}
                                        <a href="{{ route('admin.vehicle-expenses.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.index') && !request()->has('filter') && !request()->has('section') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:layout-dashboard" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.index') && !request()->has('filter') && !request()->has('section') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Tableau de bord
                                        </a>

                                        {{-- Nouvelle dépense --}}
                                        @can('create expenses')
                                        <a href="{{ route('admin.vehicle-expenses.create') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:plus-circle" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.create') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Nouvelle dépense
                                        </a>
                                        @endcan

                                        {{-- Analytics --}}
                                        @can('view expense analytics')
                                        <a href="{{ route('admin.vehicle-expenses.dashboard') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:chart-line" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.dashboard') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Analytics
                                        </a>
                                        @endcan

                                        {{-- En attente d'approbation --}}
                                        @can('approve expenses')
                                        <a href="{{ route('admin.vehicle-expenses.index') }}?filter=pending_approval"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->get('filter') == 'pending_approval' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:clock" class="w-4 h-4 mr-2.5 {{ request()->get('filter') == 'pending_approval' ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Approbations
                                            @if($pendingExpenses > 0)
                                            <span class="ml-auto bg-yellow-500 text-white text-xs rounded-full px-1.5">{{ $pendingExpenses }}</span>
                                            @endif
                                        </a>
                                        @endcan

                                        {{-- Budgets & Groupes --}}
                                        <a href="{{ route('admin.vehicle-expenses.index') }}?section=groups"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->get('section') == 'groups' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:wallet" class="w-4 h-4 mr-2.5 {{ request()->get('section') == 'groups' ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Budgets
                                        </a>

                                        {{-- Export --}}
                                        @can('export expenses')
                                        <a href="{{ route('admin.vehicle-expenses.export') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.export') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:download" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.export') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Export
                                        </a>
                                        @endcan

                                        {{-- TCO & Rapports --}}
                                        @can('view expense analytics')
                                        <a href="{{ route('admin.vehicle-expenses.analytics.cost-trends') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.analytics.cost-trends') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:trending-up" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.analytics.cost-trends') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            TCO & Tendances
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcanany

                        {{-- Rapports --}}
                        @can('view analytics')
                        <li class="flex">
                            <a href="{{ route('admin.reports.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:chart-bar" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Rapports</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Administration avec sous-menu --}}
                        @canany(['view users', 'view roles', 'view audit logs'])
                        <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:cog" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1 text-left">Administration</span>
                                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                                <div class="flex w-full mt-2 pl-3">
                                    <div class="mr-1">
                                        <div class="px-1 py-2 h-full relative">
                                            <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
                                            @php
                                            $adminBarHeight = '0%';
                                            $adminBarTop = '0%';
                                            if (request()->routeIs('admin.users.*')) {
                                            $adminBarHeight = '33.33%'; $adminBarTop = '0%';
                                            } elseif (request()->routeIs('admin.roles.*')) {
                                            $adminBarHeight = '33.33%'; $adminBarTop = '33.33%';
                                            } elseif (request()->routeIs('admin.audit.*')) {
                                            $adminBarHeight = '33.33%'; $adminBarTop = '66.66%';
                                            }
                                            @endphp
                                            <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
                                                x-bind:style="`height: {{ $adminBarHeight }}; top: {{ $adminBarTop }};`"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        @can('view users')
                                        <a href="{{ route('admin.users.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:account-multiple" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Utilisateurs
                                        </a>
                                        @endcan
                                        @can('view roles')
                                        <a href="{{ route('admin.roles.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:shield-check" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.roles.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Rôles & Permissions
                                        </a>
                                        @endcan
                                        @can('view audit logs')
                                        @hasrole('Super Admin')
                                        <a href="{{ route('admin.audit.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.audit.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:security" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.audit.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Audit & Sécurité
                                        </a>
                                        @endhasrole
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endcanany
                    </ul>

                    {{-- Footer du menu supprimé --}}
                </div>
            </div>
        </div>

        {{-- Sidebar mobile --}}
        <div class="lg:hidden" x-data="{ open: false }">
            {{-- Backdrop --}}
            <div x-show="open"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="relative z-50 lg:hidden">
                <div class="fixed inset-0 bg-gray-900/80" @click="open = false"></div>

                <div class="fixed inset-0 flex">
                    <div x-show="open"
                        x-transition:enter="transition ease-in-out duration-300 transform"
                        x-transition:enter-start="-translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-300 transform"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="relative mr-16 flex w-full max-w-xs flex-1">
                        {{-- Même contenu que la sidebar desktop --}}
                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-zinc-50 px-6 pb-4">
                            {{-- Logo --}}
                            <div class="flex h-16 shrink-0 items-center">
                                <div class="flex items-center">
                                    <x-iconify icon="heroicons:truck" class="w-6 h-6 text-zinc-900 mr-3" />
                                    <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
                                </div>
                            </div>

                            {{-- Navigation mobile (copie de la navigation desktop) --}}
                            <nav class="flex flex-1 flex-col">
                                <ul role="list" class="flex flex-1 flex-col gap-y-2">
                                    <li>
                                        <ul role="list" class="-mx-2 space-y-1">
                                            {{-- Dashboard --}}
                                            <li>
                                                @php
                                                $dashboardRouteMobile = auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Supervisor'])
                                                ? route('admin.dashboard')
                                                : route('driver.dashboard');
                                                $isDashboardActiveMobile = request()->routeIs('admin.dashboard', 'driver.dashboard', 'dashboard');
                                                @endphp
                                                <a href="{{ $dashboardRouteMobile }}"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $isDashboardActiveMobile ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="heroicons:home" class="h-5 w-5 shrink-0" />
                                                    Dashboard
                                                </a>
                                            </li>

                                            {{-- Organisations (Super Admin uniquement) --}}
                                            @hasrole('Super Admin')
                                            <li>
                                                <a href="{{ route('admin.organizations.index') }}"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.organizations.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="heroicons:building-office" class="h-5 w-5 shrink-0" />
                                                    Organisations
                                                </a>
                                            </li>
                                            @endhasrole

                                            {{-- Véhicules avec sous-menu --}}
                                            @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                            <li x-data="{ open: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'true' : 'false' }} }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="heroicons:truck" class="h-5 w-5 shrink-0" />
                                                    <span class="flex-1 text-left">Véhicules</span>
                                                    <x-iconify icon="heroicons:chevron-right" class="h-4 w-4 transition-transform" ::class="{ 'rotate-90': open }" />
                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.vehicles.index') }}"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.vehicles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <x-iconify icon="heroicons:truck" class="h-4 w-4 shrink-0" />
                                                                Gestion Véhicules
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.assignments.index') }}"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.assignments.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <x-iconify icon="heroicons:clipboard-document-list" class="h-4 w-4 shrink-0" />
                                                                Affectations
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            @endhasanyrole

                                            {{-- Autres éléments du menu... --}}
                                            @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                            <li>
                                                <a href="{{ route('admin.drivers.index') }}"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.drivers.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="heroicons:user" class="h-5 w-5 shrink-0" />
                                                    Chauffeurs
                                                </a>
                                            </li>
                                            @endhasanyrole

                                            {{-- Dépôts - ENTERPRISE GRADE (Mobile) --}}
                                            @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                            <li>
                                                <a href="{{ route('admin.depots.index') }}"
                                                    class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.depots.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="mdi:office-building" class="h-5 w-5 shrink-0" />
                                                    Dépôts
                                                </a>
                                            </li>
                                            @endhasanyrole

                                            {{-- Administration avec sous-menu --}}
                                            @hasanyrole('Super Admin|Admin')
                                            <li x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'true' : 'false' }} }">
                                                <button @click="open = !open"
                                                    class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <x-iconify icon="mdi:cog" class="h-5 w-5 shrink-0" />
                                                    <span class="flex-1 text-left">Administration</span>
                                                    <x-iconify icon="heroicons:chevron-right" class="h-4 w-4 transition-transform" ::class="{ 'rotate-90': open }" />
                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.users.index') }}"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <x-iconify icon="mdi:account-multiple" class="h-4 w-4 shrink-0" />
                                                                Utilisateurs
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.roles.index') }}"
                                                                class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <x-iconify icon="mdi:shield-check" class="h-4 w-4 shrink-0" />
                                                                Rôles & Permissions
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                            @endhasanyrole
                                        </ul>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header mobile --}}
            <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
                <button type="button" @click="open = true" class="-m-2.5 p-2.5 text-zinc-500 lg:hidden">
                    <span class="sr-only">Ouvrir la sidebar</span>
                    <x-iconify icon="heroicons:bars-3" class="h-6 w-6" />
                </button>
                <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
                <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:user" class="h-4 w-4 text-zinc-500" />
                </div>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:pl-64">
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-zinc-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <div class="h-6 w-px bg-zinc-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="relative flex flex-1">
                        {{-- Zone de recherche (optionnelle) --}}
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        {{-- Recherche rapide --}}
                        <div class="relative hidden lg:block">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-iconify icon="heroicons:magnifying-glass" class="h-4 w-4 text-zinc-400" />
                            </div>
                            <input type="search"
                                placeholder="Rechercher..."
                                class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
                        </div>

                        {{-- Notifications avec badge --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Voir les notifications</span>
                                <x-iconify icon="mdi:bell-ring" class="h-6 w-6" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>

                        {{-- Messages --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Messages</span>
                                <x-iconify icon="heroicons:envelope" class="h-6 w-6" />
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
                            </button>
                        </div>

                        {{-- Mode sombre --}}
                        <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
                            <span class="sr-only">Basculer le mode sombre</span>
                            <x-iconify icon="heroicons:moon" class="h-6 w-6" />
                        </button>

                        {{-- Séparateur --}}
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

                        {{-- Profile dropdown amélioré --}}
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                    <x-iconify icon="heroicons:user" class="text-white w-4 h-4" />
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <div class="ml-3 text-left">
                                        <div class="text-sm font-semibold leading-5 text-zinc-900">{{ auth()->user()->name }}</div>
                                        <div class="text-xs leading-4 text-zinc-500">{{ auth()->user()->getRoleNames()->first() }}</div>
                                    </div>
                                    <x-iconify icon="heroicons:chevron-down" class="ml-2 h-4 w-4 text-zinc-500 transition-transform" ::class="{ 'rotate-180': open }" />
                                </span>
                            </button>

                            <div x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-10 mt-2.5 w-56 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-zinc-900/5">

                                {{-- En-tête du profil --}}
                                <div class="px-4 py-3 border-b border-zinc-100">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                            <x-iconify icon="heroicons:user" class="text-white w-5 h-5" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-zinc-900">{{ auth()->user()->name }}</div>
                                            <div class="text-xs text-zinc-500">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Menu items --}}
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="heroicons:user-circle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Mon Profil
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="mdi:cog" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Paramètres
                                    </a>
                                    <a href="#"
                                        class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <x-iconify icon="heroicons:question-mark-circle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                        Aide & Support
                                    </a>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                            <x-iconify icon="heroicons:arrow-right-on-rectangle" class="mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600" />
                                            Se déconnecter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- ====================================================================
 📦 SCRIPTS JAVASCRIPT ENTERPRISE-GRADE
 ==================================================================== 
 Tom Select, Flatpickr - Chargés globalement avant Alpine.js
 @version 1.0 Production-Ready
 ==================================================================== --}}

    {{-- Tom Select JS --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    {{-- SlimSelect JS - REMOVED (Bundled locally via Vite) --}}

    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    {{-- Initialisation Globale Tom Select & Flatpickr --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ====================================================================
            // TOM SELECT - Initialisation Globale
            // ====================================================================
            document.querySelectorAll('.tomselect').forEach(function(el) {
                if (el.tomselect) return; // Déjà initialisé

                new TomSelect(el, {
                    plugins: ['clear_button', 'remove_button'],
                    maxOptions: 100,
                    placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
                    allowEmptyOption: true,
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    render: {
                        no_results: function(data, escape) {
                            return '<div class="no-results p-2 text-sm text-gray-500">Aucun résultat trouvé</div>';
                        }
                    }
                });
            });

            // ====================================================================
            // FLATPICKR DATEPICKER - Initialisation Globale
            // ====================================================================
            document.querySelectorAll('.datepicker').forEach(function(el) {
                if (el._flatpickr) return; // Déjà initialisé

                const minDate = el.getAttribute('data-min-date');
                const maxDate = el.getAttribute('data-max-date');
                const dateFormat = el.getAttribute('data-date-format') || 'd/m/Y';

                flatpickr(el, {
                    locale: 'fr',
                    dateFormat: dateFormat,
                    minDate: minDate,
                    maxDate: maxDate,
                    allowInput: true,
                    disableMobile: true,
                    nextArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>',
                    prevArrow: '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                });
            });

            // ====================================================================
            // FLATPICKR TIMEPICKER - Initialisation Globale avec Masque
            // ====================================================================

            // Fonction de masque de saisie pour le format HH:MM
            function applyTimeMask(input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, ''); // Garder seulement les chiffres

                    if (value.length >= 2) {
                        // Limiter les heures à 23
                        let hours = parseInt(value.substring(0, 2));
                        if (hours > 23) hours = 23;

                        let formattedValue = String(hours).padStart(2, '0');

                        if (value.length >= 3) {
                            // Limiter les minutes à 59
                            let minutes = parseInt(value.substring(2, 4));
                            if (minutes > 59) minutes = 59;
                            formattedValue += ':' + String(minutes).padStart(2, '0');
                        } else if (value.length === 2) {
                            formattedValue += ':';
                        }

                        e.target.value = formattedValue;
                    }
                });

                // Empêcher la suppression du ':'
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        const cursorPos = e.target.selectionStart;
                        if (cursorPos === 3 && e.target.value.charAt(2) === ':') {
                            e.preventDefault();
                            e.target.value = e.target.value.substring(0, 2);
                        }
                    }
                });
            }

            document.querySelectorAll('.timepicker').forEach(function(el) {
                if (el._flatpickr) return; // Déjà initialisé

                const enableSeconds = el.getAttribute('data-enable-seconds') === 'true';

                // Appliquer le masque de saisie
                applyTimeMask(el);

                flatpickr(el, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: enableSeconds ? "H:i:S" : "H:i",
                    time_24hr: true,
                    allowInput: true,
                    disableMobile: true,
                    defaultHour: 0,
                    defaultMinute: 0,
                });
            });
        });

        // ====================================================================
        // LIVEWIRE - Réinitialisation après mises à jour
        // ====================================================================
        document.addEventListener('livewire:navigated', function() {
            // Réinitialiser Tom Select
            document.querySelectorAll('.tomselect').forEach(function(el) {
                if (!el.tomselect) {
                    new TomSelect(el, {
                        plugins: ['clear_button', 'remove_button'],
                        maxOptions: 100,
                        placeholder: el.getAttribute('data-placeholder') || 'Rechercher...',
                        allowEmptyOption: true,
                        create: false,
                    });
                }
            });

            // Réinitialiser Flatpickr
            document.querySelectorAll('.datepicker, .timepicker').forEach(function(el) {
                if (!el._flatpickr) {
                    flatpickr(el, {
                        locale: 'fr',
                        allowInput: true,
                        disableMobile: true,
                    });
                }
            });
        });
    </script>

    @stack('scripts')
    {{--
    ⚠️ ATTENTION: Alpine.js est déjà chargé via Livewire 3 dans resources/js/admin/app.js
    NE PAS AJOUTER de CDN Alpine.js ici - cela cause des conflits de double initialisation
    avec les composants Livewire (@entangle, wire:click, etc.)
 --}}

    {{-- ====================================================================
 🔔 TOAST NOTIFICATION SYSTEM - Enterprise Grade
 ====================================================================
 Système de notifications toast pour feedback utilisateur temps réel
 Compatible avec Livewire events
 ==================================================================== --}}
    <div x-data="toastManager()"
        @toast.window="showToast($event.detail)"
        class="fixed top-4 right-4 z-50 space-y-2"
        style="pointer-events: none;">
        <template x-for="(toast, index) in toasts" :key="toast.id">
            <div x-show="toast.show"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-full"
                class="max-w-md w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
                :class="{
                  'bg-green-50 border border-green-200': toast.type === 'success',
                  'bg-red-50 border border-red-200': toast.type === 'error',
                  'bg-blue-50 border border-blue-200': toast.type === 'info',
                  'bg-yellow-50 border border-yellow-200': toast.type === 'warning'
              }">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <template x-if="toast.type === 'success'">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'error'">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'info'">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </template>
                            <template x-if="toast.type === 'warning'">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </template>
                        </div>
                        <div class="ml-3 flex-1">
                            <template x-if="toast.title">
                                <p class="text-sm font-semibold mb-1"
                                    :class="{
                                    'text-green-900': toast.type === 'success',
                                    'text-red-900': toast.type === 'error',
                                    'text-blue-900': toast.type === 'info',
                                    'text-yellow-900': toast.type === 'warning'
                                }"
                                    x-text="toast.title"></p>
                            </template>
                            <p class="text-sm"
                                :class="{
                                'text-green-800': toast.type === 'success',
                                'text-red-800': toast.type === 'error',
                                'text-blue-800': toast.type === 'info',
                                'text-yellow-800': toast.type === 'warning'
                            }"
                                x-text="toast.message || 'Notification'"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="removeToast(toast.id)"
                                class="inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="{
                                     'text-green-500 hover:text-green-600 focus:ring-green-500': toast.type === 'success',
                                     'text-red-500 hover:text-red-600 focus:ring-red-500': toast.type === 'error',
                                     'text-blue-500 hover:text-blue-600 focus:ring-blue-500': toast.type === 'info',
                                     'text-yellow-500 hover:text-yellow-600 focus:ring-yellow-500': toast.type === 'warning'
                                 }">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </template>
    </div>

    <script>
        function toastManager() {
            return {
                toasts: [],
                counter: 0,

                showToast(detail) {
                    const id = ++this.counter;
                    const toast = {
                        id: id,
                        type: detail.type || 'info',
                        title: detail.title || '',
                        message: detail.message || 'Notification',
                        show: true
                    };

                    this.toasts.push(toast);

                    // Auto-remove after 5 seconds
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 5000);
                },

                removeToast(id) {
                    const index = this.toasts.findIndex(t => t.id === id);
                    if (index !== -1) {
                        this.toasts[index].show = false;
                        setTimeout(() => {
                            this.toasts.splice(index, 1);
                        }, 300);
                    }
                }
            }
        }
    </script>
    @livewireScriptConfig
</body>

</html>