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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/js/admin/app.js'])
    @stack('styles')
</head>
<body class="h-full">
    <div class="min-h-full">
        {{-- Sidebar pour desktop --}}
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-zinc-50 px-6 pb-4 border-r border-zinc-200">
                {{-- Logo --}}
                <div class="flex h-16 shrink-0 items-center">
                    <div class="flex items-center">
                        <i class="fas fa-truck text-zinc-900 text-2xl mr-3"></i>
                        <span class="text-zinc-900 text-xl font-bold">ZenFleet</span>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-2">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                {{-- Dashboard --}}
                                <li>
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-home h-5 w-5 shrink-0"></i>
                                        Dashboard
                                    </a>
                                </li>

                                {{-- Organisations (Super Admin uniquement) --}}
                                @hasrole('Super Admin')
                                <li>
                                    <a href="{{ route('admin.organizations.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.organizations.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-building h-5 w-5 shrink-0"></i>
                                        Organisations
                                    </a>
                                </li>
                                @endhasrole

                                {{-- Véhicules avec sous-menu --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                <li x-data="{ open: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'true' : 'false' }} }">
                                    <button @click="open = !open"
                                            class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-car h-5 w-5 shrink-0"></i>
                                        <span class="flex-1 text-left">Véhicules</span>
                                        <i class="fas fa-chevron-right h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"></i>
                                    </button>
                                    <div x-show="open" x-transition class="mt-1">
                                        <ul class="ml-6 space-y-1">
                                            <li class="relative">
                                                <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                <a href="{{ route('admin.vehicles.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.vehicles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                    <i class="fas fa-car h-4 w-4 shrink-0"></i>
                                                    Gestion Véhicules
                                                </a>
                                            </li>
                                            <li class="relative">
                                                <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                <a href="{{ route('admin.assignments.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.assignments.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                    <i class="fas fa-clipboard-list h-4 w-4 shrink-0"></i>
                                                    Affectations
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                @endhasanyrole

                                {{-- Chauffeurs --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                <li>
                                    <a href="{{ route('admin.drivers.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.drivers.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-user-tie h-5 w-5 shrink-0"></i>
                                        Chauffeurs
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Maintenance --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte|Supervisor')
                                <li>
                                    <a href="{{ route('admin.maintenance.dashboard') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.maintenance.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-wrench h-5 w-5 shrink-0"></i>
                                        Maintenance
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Planning --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte|Supervisor')
                                <li>
                                    <a href="{{ route('admin.planning.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.planning.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-calendar-alt h-5 w-5 shrink-0"></i>
                                        Planning
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Documents --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                <li>
                                    <a href="{{ route('admin.documents.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.documents.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-file-alt h-5 w-5 shrink-0"></i>
                                        Documents
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Fournisseurs --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                <li>
                                    <a href="{{ route('admin.suppliers.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.suppliers.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-handshake h-5 w-5 shrink-0"></i>
                                        Fournisseurs
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Rapports --}}
                                @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                <li>
                                    <a href="{{ route('admin.reports.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.reports.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-chart-bar h-5 w-5 shrink-0"></i>
                                        Rapports
                                    </a>
                                </li>
                                @endhasanyrole

                                {{-- Administration avec sous-menu --}}
                                @hasanyrole('Super Admin|Admin')
                                <li x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'true' : 'false' }} }">
                                    <button @click="open = !open"
                                            class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                        <i class="fas fa-cogs h-5 w-5 shrink-0"></i>
                                        <span class="flex-1 text-left">Administration</span>
                                        <i class="fas fa-chevron-right h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"></i>
                                    </button>
                                    <div x-show="open" x-transition class="mt-1">
                                        <ul class="ml-6 space-y-1">
                                            <li class="relative">
                                                <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                <a href="{{ route('admin.users.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                    <i class="fas fa-users h-4 w-4 shrink-0"></i>
                                                    Utilisateurs
                                                </a>
                                            </li>
                                            <li class="relative">
                                                <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                <a href="{{ route('admin.roles.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                    <i class="fas fa-user-shield h-4 w-4 shrink-0"></i>
                                                    Rôles & Permissions
                                                </a>
                                            </li>
                                            @hasrole('Super Admin')
                                            <li class="relative">
                                                <div class="absolute left-0 top-0 w-px bg-zinc-300" style="height: calc(100% - 8px);"></div>
                                                <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                <a href="{{ route('admin.audit.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.audit.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                    <i class="fas fa-shield-alt h-4 w-4 shrink-0"></i>
                                                    Audit & Sécurité
                                                </a>
                                            </li>
                                            @endhasrole
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
                                    <i class="fas fa-truck text-zinc-900 text-2xl mr-3"></i>
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
                                                <a href="{{ route('admin.dashboard') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <i class="fas fa-home h-5 w-5 shrink-0"></i>
                                                    Dashboard
                                                </a>
                                            </li>

                                            {{-- Organisations (Super Admin uniquement) --}}
                                            @hasrole('Super Admin')
                                            <li>
                                                <a href="{{ route('admin.organizations.index') }}"
                                                   class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.organizations.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <i class="fas fa-building h-5 w-5 shrink-0"></i>
                                                    Organisations
                                                </a>
                                            </li>
                                            @endhasrole

                                            {{-- Véhicules avec sous-menu --}}
                                            @hasanyrole('Super Admin|Admin|Gestionnaire Flotte')
                                            <li x-data="{ open: {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'true' : 'false' }} }">
                                                <button @click="open = !open"
                                                        class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.vehicles.*', 'admin.assignments.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <i class="fas fa-car h-5 w-5 shrink-0"></i>
                                                    <span class="flex-1 text-left">Véhicules</span>
                                                    <i class="fas fa-chevron-right h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"></i>
                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.vehicles.index') }}"
                                                               class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.vehicles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <i class="fas fa-car h-4 w-4 shrink-0"></i>
                                                                Gestion Véhicules
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.assignments.index') }}"
                                                               class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.assignments.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <i class="fas fa-clipboard-list h-4 w-4 shrink-0"></i>
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
                                                    <i class="fas fa-user-tie h-5 w-5 shrink-0"></i>
                                                    Chauffeurs
                                                </a>
                                            </li>
                                            @endhasanyrole

                                            {{-- Administration avec sous-menu --}}
                                            @hasanyrole('Super Admin|Admin')
                                            <li x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'true' : 'false' }} }">
                                                <button @click="open = !open"
                                                        class="group flex w-full gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.audit.*') ? 'bg-zinc-950 text-white' : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100' }}">
                                                    <i class="fas fa-cogs h-5 w-5 shrink-0"></i>
                                                    <span class="flex-1 text-left">Administration</span>
                                                    <i class="fas fa-chevron-right h-4 w-4 transition-transform" :class="{ 'rotate-90': open }"></i>
                                                </button>
                                                <div x-show="open" x-transition class="mt-1">
                                                    <ul class="ml-6 space-y-1">
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.users.index') }}"
                                                               class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.users.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <i class="fas fa-users h-4 w-4 shrink-0"></i>
                                                                Utilisateurs
                                                            </a>
                                                        </li>
                                                        <li class="relative">
                                                            <div class="absolute left-0 top-0 bottom-0 w-px bg-zinc-300"></div>
                                                            <div class="absolute left-0 top-3 w-3 h-px bg-zinc-300"></div>
                                                            <a href="{{ route('admin.roles.index') }}"
                                                               class="group flex gap-x-3 rounded-md p-2 pl-4 text-sm leading-6 font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-50' }}">
                                                                <i class="fas fa-user-shield h-4 w-4 shrink-0"></i>
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
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>
                <div class="flex-1 text-sm font-semibold leading-6 text-zinc-900">ZenFleet</div>
                <div class="h-8 w-8 bg-zinc-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-zinc-500 text-sm"></i>
                </div>
            </div>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:pl-72">
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
                                <i class="fas fa-search h-4 w-4 text-zinc-400"></i>
                            </div>
                            <input type="search"
                                   placeholder="Rechercher..."
                                   class="block w-64 rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-zinc-900 ring-1 ring-inset ring-zinc-300 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-zinc-600 sm:text-sm sm:leading-6">
                        </div>

                        {{-- Notifications avec badge --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Voir les notifications</span>
                                <i class="fas fa-bell h-6 w-6"></i>
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>

                        {{-- Messages --}}
                        <div class="relative">
                            <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600 relative">
                                <span class="sr-only">Messages</span>
                                <i class="fas fa-envelope h-6 w-6"></i>
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center">2</span>
                            </button>
                        </div>

                        {{-- Mode sombre --}}
                        <button type="button" class="-m-2.5 p-2.5 text-zinc-500 hover:text-zinc-600">
                            <span class="sr-only">Basculer le mode sombre</span>
                            <i class="fas fa-moon h-6 w-6"></i>
                        </button>

                        {{-- Séparateur --}}
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-zinc-200" aria-hidden="true"></div>

                        {{-- Profile dropdown amélioré --}}
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="-m-1.5 flex items-center p-1.5 hover:bg-zinc-50 rounded-lg transition-colors">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="h-8 w-8 bg-gradient-to-br from-zinc-600 to-zinc-800 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden lg:flex lg:items-center">
                                    <div class="ml-3 text-left">
                                        <div class="text-sm font-semibold leading-5 text-zinc-900">{{ auth()->user()->name }}</div>
                                        <div class="text-xs leading-4 text-zinc-500">{{ auth()->user()->getRoleNames()->first() }}</div>
                                    </div>
                                    <i class="ml-2 fas fa-chevron-down h-4 w-4 text-zinc-500 transition-transform" :class="{ 'rotate-180': open }"></i>
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
                                            <i class="fas fa-user text-white"></i>
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
                                        <i class="fas fa-user-circle mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"></i>
                                        Mon Profil
                                    </a>
                                    <a href="#"
                                       class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <i class="fas fa-cog mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"></i>
                                        Paramètres
                                    </a>
                                    <a href="#"
                                       class="group flex items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                        <i class="fas fa-question-circle mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"></i>
                                        Aide & Support
                                    </a>
                                    <div class="border-t border-zinc-100 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="group flex w-full items-center px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-50">
                                            <i class="fas fa-sign-out-alt mr-3 h-4 w-4 text-zinc-400 group-hover:text-zinc-600"></i>
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