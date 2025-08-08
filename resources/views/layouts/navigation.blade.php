<nav class="flex flex-col h-full bg-white shadow-md">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0 px-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto" />
        </a>
    </div>

    {{-- Liens de navigation principaux --}}
    <div class="flex-1 px-4 py-4 overflow-y-auto space-y-2">

        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-heroicon-o-home class="mr-3 h-6 w-6 shrink-0"/>
            {{ __('Tableau de bord') }}
        </x-responsive-nav-link>

        {{-- Section Gestion de la Flotte --}}
        @canany(['view vehicles', 'view drivers', 'view assignments'])
            @php($isFleetActive = request()->routeIs('admin.vehicles.*') || request()->routeIs('admin.drivers.*') || request()->routeIs('admin.assignments.*'))
            <div x-data="{ open: {{ $isFleetActive ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-truck class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Flotte') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-8 border-l-2 border-dotted border-gray-300 ml-4">
                    @can('view vehicles')
                        <a href="{{ route('admin.vehicles.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.vehicles.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <x-heroicon-o-truck class="mr-3 h-5 w-5 shrink-0"/> {{ __('Véhicules') }}
                        </a>
                    @endcan
                    @can('view drivers')
                        <a href="{{ route('admin.drivers.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.drivers.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <x-heroicon-o-users class="mr-3 h-5 w-5 shrink-0"/> {{ __('Chauffeurs') }}
                        </a>
                    @endcan
                    @can('view assignments')
                        <a href="{{ route('admin.assignments.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.assignments.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <x-heroicon-o-clipboard-document-list class="mr-3 h-5 w-5 shrink-0"/> {{ __('Affectations') }}
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany

        {{-- Section Maintenance --}}
        @canany(['view maintenance', 'manage maintenance plans'])
            @php($isMaintenanceActive = request()->routeIs('admin.maintenance.*'))
            <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-wrench-screwdriver class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Maintenance') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-8 border-l-2 border-dotted border-gray-300 ml-4">
                    <a href="{{ route('admin.maintenance.dashboard') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.maintenance.dashboard') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar class="mr-3 h-5 w-5 shrink-0"/> Tableau de Bord
                    </a>
                    <a href="{{ route('admin.maintenance.plans.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.maintenance.plans.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-clipboard-document-check class="mr-3 h-5 w-5 shrink-0"/> Plans de Maintenance
                    </a>
                </div>
            </div>
        @endcanany

        {{-- Section Administration --}}
        @role('Super Admin')
            @php($isAdminActive = request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*'))
            <div x-data="{ open: {{ $isAdminActive ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-cog-6-tooth class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Administration') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-8 border-l-2 border-dotted border-gray-300 ml-4">
                    <a href="{{ route('admin.organizations.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.organizations.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-building-office-2 class="mr-3 h-5 w-5 shrink-0"/> {{ __('Organisations') }}
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-user-group class="mr-3 h-5 w-5 shrink-0"/> {{ __('Utilisateurs') }}
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-key class="mr-3 h-5 w-5 shrink-0"/> {{ __('Rôles') }}
                    </a>
                </div>
            </div>
        @endrole
    </div>

    {{-- Section Utilisateur / Déconnexion --}}
    <div class="mt-auto shrink-0 p-4 border-t border-gray-200">
        <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.away="open = false" class="relative">
            {{-- Bouton de déclenchement --}}
            <button @click="open = !open" class="w-full flex-1 flex items-center space-x-3 group p-2 rounded-lg hover:bg-gray-100">
                <span class="inline-block h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                     <x-heroicon-o-user-circle class="h-8 w-8 text-gray-500"/>
                </span>
                <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">Options</p>
                </div>
                <x-heroicon-o-chevron-up class="h-5 w-5 text-gray-400 shrink-0"/>
            </button>

            {{-- Menu déroulant --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-full left-0 right-0 mb-2 w-full origin-bottom z-10"
                 style="display: none;">
                <div class="bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                        <x-heroicon-o-user class="mr-3 h-5 w-5"/>
                        Mon Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                            <x-heroicon-o-arrow-left-on-rectangle class="mr-3 h-5 w-5"/>
                            Déconnexion
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
