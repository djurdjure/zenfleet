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

 <!-- Iconify CDN -->
 <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

 @vite(['resources/js/admin/app.js'])
 @stack('styles')
</head>
<body class="h-full">
 <div class="min-h-full">
 {{-- Sidebar pour desktop - Style Ultra-Pro World-Class --}}
 <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
 <div class="flex grow flex-col overflow-hidden bg-[#e3e7ec] border-r border-gray-300/60 shadow-sm">
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

 {{-- Organisations (Super Admin uniquement) --}}
 @hasrole('Super Admin')
 <li class="flex">
 <a href="{{ route('admin.organizations.index') }}"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.organizations.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:office-building" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.organizations.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1">Organisations</span>
 </a>
 </li>
 @endhasrole

 {{-- Véhicules avec sous-menu --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
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
 <a href="{{ route('admin.vehicles.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicles.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:format-list-bulleted" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicles.index') ? 'text-blue-600' : 'text-gray-600' }}" />
 Gestion Véhicules
 </a>
 <a href="{{ route('admin.assignments.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.assignments.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:clipboard-text" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.assignments.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Affectations
 </a>
 </div>
 </div>
 </div>
 </li>
 @endhasanyrole

 {{-- Chauffeurs avec sous-menu --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
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
 $driverBarHeight = request()->routeIs('admin.drivers.index') ? '50%' : (request()->routeIs('admin.sanctions.*') ? '50%' : '0%');
 $driverBarTop = request()->routeIs('admin.sanctions.*') ? '50%' : '0%';
 @endphp
 <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: {{ $driverBarHeight }}; top: {{ $driverBarTop }};`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 <a href="{{ route('admin.drivers.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.drivers.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:view-list" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.drivers.index') ? 'text-blue-600' : 'text-gray-600' }}" />
 Liste
 </a>
 <a href="{{ route('admin.sanctions.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.sanctions.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:gavel" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.sanctions.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Sanctions
 </a>
 </div>
 </div>
 </div>
 </li>
 @endhasanyrole

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
 <x-iconify icon="mdi:history" class="w-3 h-3 mr-2 {{ $isMileageIndexActive ? 'text-blue-600' : 'text-slate-400' }}" />
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
 <x-iconify icon="mdi:pencil" class="w-3 h-3 mr-2 {{ $isMileageUpdateActive ? 'text-blue-600' : 'text-slate-400' }}" />
 Mettre à jour
 </a>
 </li>
 @endcan
 </ul>
 </div>
 </div>
 </li>
 @endcanany

 {{-- Maintenance avec sous-menus --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte|Supervisor')
 <li class="flex flex-col" x-data="{ open: {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'true' : 'false' }} }">
 <button @click="open = !open"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:wrench" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.maintenance.*', 'admin.repair-requests.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1 text-left">Maintenance</span>
 <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 transition-transform duration-200" ::class="{ 'rotate-180': !open }" />
 </button>
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
 <div class="flex w-full mt-2 pl-3">
 <div class="mr-1">
 <div class="px-1 py-2 h-full relative">
 <div class="bg-gray-400/30 w-0.5 h-full rounded-full"></div>
 @php
 $maintenanceBarHeight = '0%';
 $maintenanceBarTop = '0%';
 if (request()->routeIs('admin.maintenance.surveillance.*')) {
 $maintenanceBarHeight = '25%'; $maintenanceBarTop = '0%';
 } elseif (request()->routeIs('admin.maintenance.schedules.*')) {
 $maintenanceBarHeight = '25%'; $maintenanceBarTop = '25%';
 } elseif (request()->routeIs('admin.repair-requests.*')) {
 $maintenanceBarHeight = '25%'; $maintenanceBarTop = '50%';
 } elseif (request()->routeIs('admin.maintenance.operations.*')) {
 $maintenanceBarHeight = '25%'; $maintenanceBarTop = '75%';
 }
 @endphp
 <div class="absolute w-0.5 rounded-full bg-blue-600 transition-all duration-300"
 x-bind:style="`height: {{ $maintenanceBarHeight }}; top: {{ $maintenanceBarTop }};`"></div>
 </div>
 </div>
 <div class="flex-1 min-w-0 space-y-1">
 <a href="{{ route('admin.maintenance.surveillance.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.maintenance.surveillance.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:monitor-dashboard" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.surveillance.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Surveillance
 </a>
 <a href="{{ route('admin.maintenance.schedules.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:calendar-clock" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.schedules.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Planifications
 </a>
 @canany(['view team repair requests', 'view all repair requests'])
 <a href="{{ route('admin.repair-requests.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.repair-requests.*', 'driver.repair-requests.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:tools" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.repair-requests.*', 'driver.repair-requests.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Demandes réparation
 </a>
 @endcanany
 <a href="{{ route('admin.maintenance.operations.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.maintenance.operations.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:cog" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.maintenance.operations.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Opérations
 </a>
 </div>
 </div>
 </div>
 </li>
 @endhasanyrole

 {{-- Alertes --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte|Supervisor')
 <li class="flex">
 <a href="{{ route('admin.alerts.index') }}"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.alerts.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:bell-ring" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.alerts.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1">Alertes</span>
 </a>
 </li>
 @endhasanyrole

 {{-- Documents --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
 <li class="flex">
 <a href="{{ route('admin.documents.index') }}"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:file-document" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.documents.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1">Documents</span>
 </a>
 </li>
 @endhasanyrole

 {{-- Fournisseurs --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
 <li class="flex">
 <a href="{{ route('admin.suppliers.index') }}"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:store" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.suppliers.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1">Fournisseurs</span>
 </a>
 </li>
 @endhasanyrole

 {{-- Rapports --}}
 @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
 <li class="flex">
 <a href="{{ route('admin.reports.index') }}"
 class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
 <x-iconify icon="mdi:chart-bar" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-gray-600' }}" />
 <span class="flex-1">Rapports</span>
 </a>
 </li>
 @endhasanyrole

 {{-- Administration avec sous-menu --}}
 @hasanyrole('Super Admin|Admin')
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
 <a href="{{ route('admin.users.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:account-multiple" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Utilisateurs
 </a>
 <a href="{{ route('admin.roles.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:shield-check" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.roles.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Rôles & Permissions
 </a>
 @hasrole('Super Admin')
 <a href="{{ route('admin.audit.index') }}"
 class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.audit.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
 <x-iconify icon="mdi:security" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.audit.*') ? 'text-blue-600' : 'text-gray-600' }}" />
 Audit & Sécurité
 </a>
 @endhasrole
 </div>
 </div>
 </div>
 </li>
 @endhasanyrole
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

 @stack('scripts')
 <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>