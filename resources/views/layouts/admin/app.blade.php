<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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

    <!-- ✅ CORRECTION CRITIQUE: Utiliser l'entrée admin JS qui importe le CSS admin -->
    @vite(['resources/js/admin/app.js'])

    @stack('styles')
</head>
<body class="admin-layout h-full bg-gray-50">
    <div class="admin-container">
        {{-- Sidebar --}}
        <nav class="admin-sidebar">
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <div class="flex items-center">
                    <i class="fas fa-truck text-white text-2xl mr-3"></i>
                    <span class="text-white text-xl font-bold">ZenFleet</span>
                </div>
            </div>
            
            <div class="mt-8">
                <div class="px-4">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Menu Principal
                    </h3>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    
                    @can('Super Admin')
                    <a href="{{ route('admin.organizations.index') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.organizations.*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-building mr-3"></i>
                        Organisations
                    </a>
                    @endcan
                    
                    @can('Admin')
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Utilisateurs
                    </a>
                    @endcan
                    
                    @can('Gestionnaire Flotte')
                    <a href="{{ route('admin.vehicles.index') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.vehicles.*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-car mr-3"></i>
                        Véhicules
                    </a>
                    
                    <a href="{{ route('admin.drivers.index') }}" 
                       class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.drivers.*') ? 'bg-gray-800 text-white' : '' }}">
                        <i class="fas fa-id-card mr-3"></i>
                        Chauffeurs
                    </a>
                    @endcan
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <div class="admin-main">
            {{-- Header --}}
            <header class="admin-header flex items-center justify-between">
                <div class="flex items-center">
                    <button type="button" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <h1 class="text-2xl font-semibold text-gray-900 ml-4">
                        @yield('title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center">
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img class="h-8 w-8 rounded-full" 
                                 src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=7F9CF5&background=EBF4FF" 
                                 alt="{{ auth()->user()->name }}">
                            <span class="ml-2 text-gray-700">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
                        </button>

                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-48 origin-top-right bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                            
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Mon Profil
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Notifications --}}
            @if (session('success'))
                <div class="mx-6 mt-4 p-4 mb-4 text-sm text-green-800 bg-green-50 rounded-lg border border-green-200" role="alert">
                    <div class="flex">
                        <i class="fas fa-check-circle mr-3 mt-0.5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-6 mt-4 p-4 mb-4 text-sm text-red-800 bg-red-50 rounded-lg border border-red-200" role="alert">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle mr-3 mt-0.5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            {{-- Main Content --}}
            <main class="admin-content">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        © {{ date('Y') }} ZenFleet. Tous droits réservés.
                    </div>
                    <div>
                        Version 2.1
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

