<nav class="flex flex-col h-full bg-white shadow-md">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0 px-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto" />
        </a>
    </div>

    {{-- Liens de navigation principaux --}}
    <div class="flex-1 px-4 py-4 overflow-y-auto space-y-2">

        {{-- Lien Tableau de bord --}}
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-heroicon-o-home class="mr-3 h-6 w-6 shrink-0"/>
            {{ __('Tableau de bord') }}
        </x-responsive-nav-link>

        {{-- Section Gestion de la Flotte --}}
        @canany(['view vehicles', 'view drivers', 'view assignments'])
            @php($isFleetActive = request()->routeIs('admin.vehicles.*') || request()->routeIs('admin.drivers.*') || request()->routeIs('admin.assignments.*'))
            <div x-data="{ open: {{ $isFleetActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-truck class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Flotte') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-6">
                    @can('view vehicles')
                        <a href="{{ route('admin.vehicles.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.vehicles.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Véhicules') }}
                        </a>
                    @endcan
                    @can('view drivers')
                        <a href="{{ route('admin.drivers.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.drivers.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Chauffeurs') }}
                        </a>
                    @endcan
                    @can('view assignments')
                        <a href="{{ route('admin.assignments.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.assignments.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Affectations') }}
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany

        {{-- Section Maintenance --}}
        @canany(['view maintenance', 'manage maintenance plans'])
            @php($isMaintenanceActive = request()->routeIs('admin.maintenance.*'))
            <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-wrench-screwdriver class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Maintenance') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-6">
                    <a href="{{ route('admin.maintenance.dashboard') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.maintenance.dashboard') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> Tableau de Bord
                    </a>
                    <a href="{{ route('admin.maintenance.plans.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.maintenance.plans.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> Plans de Maintenance
                    </a>
                </div>
            </div>
        @endcanany

        {{-- Section Administration (visible uniquement par Super Admin) --}}
        @role('Super Admin')
            @php($isAdminActive = request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*'))
            <div x-data="{ open: {{ $isAdminActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <x-heroicon-o-cog-6-tooth class="mr-3 h-6 w-6 shrink-0 text-gray-500 group-hover:text-gray-700"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Administration') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                <div x-show="open" x-transition class="mt-1 space-y-1 pl-6">
                    <a href="{{ route('admin.organizations.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.organizations.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Organisations') }}
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Utilisateurs') }}
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center w-full px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'font-semibold text-primary-600 bg-primary-50' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <span class="mr-3 h-1 w-1 rounded-full bg-current"></span> {{ __('Rôles') }}
                    </a>
                </div>
            </div>
        @endrole
    </div>

    {{-- Section Utilisateur / Déconnexion --}}
    <div class="mt-auto shrink-0 space-y-4 p-4 border-t border-gray-200">
        <div class="flex items-center">
            <a href="{{ route('profile.edit') }}" class="flex-1 flex items-center space-x-3 group p-2 rounded-lg hover:bg-gray-100">
                <span class="inline-block h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                     <x-heroicon-o-user-circle class="h-8 w-8 text-gray-500"/>
                </span>
                <div>
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">Voir Profil</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" title="Déconnexion" class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600">
                    <x-heroicon-o-arrow-left-on-rectangle class="h-6 w-6"/>
                </a>
            </form>
        </div>
    </div>
</nav>
