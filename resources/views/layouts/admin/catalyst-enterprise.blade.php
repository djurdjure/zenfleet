<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(auth()->check())
        <meta name="user-data" content="{{ json_encode(['id' => auth()->id(), 'name' => auth()->user()->name, 'role' => auth()->user()->getRoleNames()->first()]) }}">
    @endif

    <title>@yield('title', 'ZenFleet Enterprise') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- ✅ VITE: Les CSS sont déjà importés dans admin/app.js --}}
    @vite(['resources/js/admin/app.js'])
    
    <style>
        /* Variables du thème entreprise */
        :root {
            --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --sidebar-active: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        }

        /* Animations globales */
        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Sidebar moderne */
        .sidebar-enterprise {
            background: var(--sidebar-bg);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            animation: slideInLeft 0.3s ease-out;
        }

        /* Custom scrollbar pour sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Content area animations */
        .content-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Notification badge pulse */
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .notification-badge {
            animation: pulse-badge 2s infinite;
        }

        /* Menu item hover effect */
        .menu-item {
            position: relative;
            overflow: hidden;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .menu-item:hover::before {
            width: 300px;
            height: 300px;
        }
    </style>
    
    @stack('styles')
    @livewireStyles
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full" x-data="{ sidebarOpen: false, userMenuOpen: false, notificationsOpen: false }">
        
        {{-- Sidebar Desktop - Ultra Moderne --}}
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
            <div class="sidebar-enterprise flex grow flex-col overflow-y-auto">
                
                {{-- Logo Section --}}
                <div class="flex h-16 shrink-0 items-center px-6 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                                <i class="fas fa-truck text-white text-lg"></i>
                            </div>
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-slate-900"></div>
                        </div>
                        <div>
                            <h1 class="text-white font-bold text-lg tracking-tight">ZenFleet</h1>
                            <p class="text-xs text-gray-400">Enterprise Edition</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex flex-1 flex-col px-4 py-4 sidebar-scrollbar">
                    <ul role="list" class="flex flex-1 flex-col gap-y-1">
                        
                        {{-- Dashboard --}}
                        <li>
                            @php
                                $dashboardRoute = auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Supervisor'])
                                    ? route('admin.dashboard')
                                    : route('driver.dashboard');
                                $isDashboardActive = request()->routeIs('admin.dashboard', 'driver.dashboard', 'dashboard');
                            @endphp
                            <a href="{{ $dashboardRoute }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ $isDashboardActive 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-home text-lg {{ $isDashboardActive ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Dashboard</span>
                                @if($isDashboardActive)
                                    <span class="ml-auto w-1 h-6 bg-white rounded-full"></span>
                                @endif
                            </a>
                        </li>

                        {{-- Organisations (Super Admin) --}}
                        @hasrole('Super Admin')
                        <li>
                            <a href="{{ route('admin.organizations.index') }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('admin.organizations.*') 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-building text-lg {{ request()->routeIs('admin.organizations.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Organisations</span>
                                @if(request()->routeIs('admin.organizations.*'))
                                    <span class="ml-auto w-1 h-6 bg-white rounded-full"></span>
                                @endif
                            </a>
                        </li>
                        @endhasrole

                        {{-- Section Gestion de Flotte --}}
                        @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                        <li class="mt-6">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">
                                Gestion de Flotte
                            </div>
                        </li>

                        {{-- Véhicules avec sous-menu --}}
                        <li x-data="{ open: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 w-full
                                           {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') 
                                              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                              : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-car text-lg {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span class="flex-1 text-left">Véhicules</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            
                            <ul x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="mt-1 ml-8 space-y-1">
                                <li>
                                    <a href="{{ route('admin.vehicles.index') }}"
                                       class="group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm transition-all duration-200
                                              {{ request()->routeIs('admin.vehicles.index') 
                                                 ? 'bg-white/10 text-white' 
                                                 : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.vehicles.index') ? 'bg-white' : 'bg-gray-500' }}"></div>
                                        <span>Liste des véhicules</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.assignments.index') }}"
                                       class="group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm transition-all duration-200
                                              {{ request()->routeIs('admin.assignments.*') 
                                                 ? 'bg-white/10 text-white' 
                                                 : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.assignments.*') ? 'bg-white' : 'bg-gray-500' }}"></div>
                                        <span>Affectations</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Chauffeurs avec sous-menu --}}
                        <li x-data="{ open: {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) ? 'true' : 'false' }} }">
                            <button @click="open = !open"
                                    class="menu-item group flex w-full items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                           {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) 
                                              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                              : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-user-tie text-lg {{ request()->routeIs(['admin.drivers.*', 'admin.sanctions.*']) ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Chauffeurs</span>
                                <svg class="ml-auto h-4 w-4 transition-transform duration-200"
                                     :class="{ 'rotate-90': open }"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <ul x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                class="mt-1 ml-8 space-y-1">
                                <li>
                                    <a href="{{ route('admin.drivers.index') }}"
                                       class="group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm transition-all duration-200
                                              {{ request()->routeIs('admin.drivers.index') 
                                                 ? 'bg-white/10 text-white' 
                                                 : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.drivers.index') ? 'bg-white' : 'bg-gray-500' }}"></div>
                                        <span>Liste des chauffeurs</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.sanctions.index') }}"
                                       class="group flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm transition-all duration-200
                                              {{ request()->routeIs('admin.sanctions.*') 
                                                 ? 'bg-white/10 text-white' 
                                                 : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                        <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.sanctions.*') ? 'bg-white' : 'bg-gray-500' }}"></div>
                                        <span>Sanctions</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Maintenance --}}
                        <li>
                            <a href="{{ route('admin.maintenance.dashboard') }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('admin.maintenance.*') 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-tools text-lg {{ request()->routeIs('admin.maintenance.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Maintenance</span>
                                @if(request()->routeIs('admin.maintenance.*'))
                                    <span class="ml-auto w-1 h-6 bg-white rounded-full"></span>
                                @endif
                            </a>
                        </li>

                        {{-- Demandes de Réparation --}}
                        <li>
                            <a href="{{ route('admin.repair-requests.index') }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('admin.repair-requests.*') 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-wrench text-lg {{ request()->routeIs('admin.repair-requests.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Réparations</span>
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full notification-badge">3</span>
                            </a>
                        </li>
                        @endhasanyrole

                        {{-- Section Administration --}}
                        @hasanyrole('Super Admin|Admin')
                        <li class="mt-6">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 mb-2">
                                Administration
                            </div>
                        </li>

                        {{-- Utilisateurs --}}
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('admin.users.*') 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-user-shield text-lg {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Utilisateurs</span>
                            </a>
                        </li>

                        {{-- Paramètres --}}
                        <li>
                            <a href="{{ route('admin.settings.index') }}"
                               class="menu-item group flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                                      {{ request()->routeIs('admin.settings.*') 
                                         ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/20' 
                                         : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                                <i class="fas fa-cog text-lg {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                        @endhasanyrole
                    </ul>

                    {{-- User Profile Section at Bottom --}}
                    <div class="mt-auto pt-4 border-t border-white/10">
                        <div class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-white/5 transition-all duration-200 cursor-pointer"
                             @click="userMenuOpen = !userMenuOpen">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-400">{{ auth()->user()->getRoleNames()->first() }}</p>
                            </div>
                            <i class="fas fa-chevron-up text-gray-400 text-xs transition-transform duration-200" :class="{ 'rotate-180': !userMenuOpen }"></i>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        {{-- Mobile Sidebar --}}
        <div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" 
                 @click="sidebarOpen = false"></div>

            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative flex w-full max-w-xs flex-1 sidebar-enterprise">
                    <!-- Mobile sidebar content (same as desktop) -->
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="lg:pl-64">
            {{-- Top Header Bar --}}
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/95 backdrop-blur-sm px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>

                {{-- Separator --}}
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                {{-- Search Bar --}}
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <form class="relative flex flex-1" action="#" method="GET">
                        <label for="search-field" class="sr-only">Search</label>
                        <i class="fas fa-search pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400 pl-3"></i>
                        <input id="search-field" 
                               class="block h-full w-full border-0 py-0 pl-10 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" 
                               placeholder="Rechercher..." 
                               type="search" 
                               name="search">
                    </form>

                    {{-- Right Icons --}}
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        {{-- Notifications --}}
                        <button type="button" 
                                @click="notificationsOpen = !notificationsOpen"
                                class="relative -m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                            <span class="sr-only">View notifications</span>
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-red-500"></span>
                        </button>

                        {{-- Separator --}}
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        {{-- Profile dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" 
                                    @click="open = !open"
                                    class="-m-1.5 flex items-center p-1.5">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-xs font-semibold">
                                        {{ substr(auth()->user()->name, 0, 2) }}
                                    </span>
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900" aria-hidden="true">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <i class="fas fa-chevron-down ml-2 h-5 w-5 text-gray-400"></i>
                                </span>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-xl bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                    <i class="fas fa-user-circle mr-2"></i> Mon profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <main class="py-8 content-fade-in">
                <div class="px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    @livewireScripts
    @stack('scripts')

    <script>
        // Système de notifications toast global
        window.Toast = {
            show(type, message, title = null, duration = 5000) {
                const container = document.getElementById('toast-container');
                const id = 'toast-' + Date.now();
                
                const icons = {
                    success: '<i class="fas fa-check-circle"></i>',
                    error: '<i class="fas fa-exclamation-circle"></i>',
                    warning: '<i class="fas fa-exclamation-triangle"></i>',
                    info: '<i class="fas fa-info-circle"></i>'
                };

                const colors = {
                    success: 'from-green-500 to-emerald-600',
                    error: 'from-red-500 to-pink-600',
                    warning: 'from-yellow-500 to-orange-600',
                    info: 'from-blue-500 to-indigo-600'
                };

                const toast = document.createElement('div');
                toast.id = id;
                toast.className = `flex items-center gap-3 px-4 py-3 bg-white rounded-xl shadow-lg border border-gray-200 min-w-[300px] max-w-md transform transition-all duration-300 translate-x-full`;
                
                toast.innerHTML = `
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br ${colors[type]} rounded-lg flex items-center justify-center text-white">
                            ${icons[type]}
                        </div>
                    </div>
                    <div class="flex-1">
                        ${title ? `<p class="font-semibold text-gray-900">${title}</p>` : ''}
                        <p class="text-sm text-gray-600">${message}</p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                    toast.classList.add('translate-x-0');
                }, 10);

                // Auto remove
                if (duration > 0) {
                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => toast.remove(), 300);
                    }, duration);
                }
            },

            success(message, title) {
                this.show('success', message, title || 'Succès');
            },

            error(message, title) {
                this.show('error', message, title || 'Erreur');
            },

            warning(message, title) {
                this.show('warning', message, title || 'Attention');
            },

            info(message, title) {
                this.show('info', message, title || 'Information');
            }
        };

        // Gestion des messages flash Laravel
        @if(session('success'))
            Toast.success("{{ session('success') }}");
        @endif

        @if(session('error'))
            Toast.error("{{ session('error') }}");
        @endif

        @if(session('warning'))
            Toast.warning("{{ session('warning') }}");
        @endif

        @if(session('info'))
            Toast.info("{{ session('info') }}");
        @endif
    </script>
</body>
</html>
