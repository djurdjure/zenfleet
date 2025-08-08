<nav class="flex flex-col h-full">
    <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0 px-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto" />
        </a>
    </div>

    <div class="flex-1 px-2 py-4 overflow-y-auto space-y-1">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-heroicon-o-home class="mr-2 h-6 w-6 shrink-0"/>
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

        @can('view vehicles')
            <x-responsive-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.*')">
                <x-heroicon-o-truck class="mr-2 h-6 w-6 shrink-0"/>
                {{ __('Véhicules') }}
            </x-responsive-nav-link>
        @endcan

        @can('view drivers')
            <x-responsive-nav-link :href="route('admin.drivers.index')" :active="request()->routeIs('admin.drivers.*')">
                <x-heroicon-o-users class="mr-2 h-6 w-6 shrink-0"/>
                {{ __('Chauffeurs') }}
            </x-responsive-nav-link>
        @endcan

        @can('view assignments')
            <x-responsive-nav-link :href="route('admin.assignments.index')" :active="request()->routeIs('admin.assignments.*')">
                <x-heroicon-o-clipboard-document-list class="mr-2 h-6 w-6 shrink-0"/>
                {{ __('Affectations') }}
            </x-responsive-nav-link>
        @endcan

        @canany(['view maintenance', 'manage maintenance plans'])
            @php($isMaintenanceActive = request()->routeIs('admin.maintenance.*'))
            <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center p-2 text-base font-normal text-gray-900 rounded-lg hover:bg-gray-100">
                    <x-heroicon-o-wrench-screwdriver class="mr-2 h-6 w-6 shrink-0 text-gray-500"/>
                    <span class="flex-1 ml-1 text-left whitespace-nowrap">{{ __('Maintenance') }}</span>
                    <x-heroicon-o-chevron-down class="h-4 w-4 transform transition-transform" ::class="{'rotate-180': open}"/>
                </button>
                {{-- Utilisation de pl-8 au lieu de pl-4 car pl-4 n'existe pas dans le CSS compilé --}}
                <div x-show="open" x-transition class="mt-1 space-y-1" style="padding-left: 1rem;">
                    <a href="{{ route('admin.maintenance.dashboard') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.dashboard') ? 'font-semibold text-violet-700' : 'text-gray-600' }}">
                        <x-heroicon-o-chart-bar class="mr-2 h-5 w-5 shrink-0"/>
                        Tableau de Bord
                    </a>
                    <a href="{{ route('admin.maintenance.plans.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.maintenance.plans.*') ? 'font-semibold text-violet-700' : 'text-gray-600' }}">
                        <x-heroicon-o-clipboard-document-check class="mr-2 h-5 w-5 shrink-0"/>
                        Plans de Maintenance
                    </a>
                </div>
            </div>
        @endcanany
    </div>

    <div class="mt-auto shrink-0 space-y-4 p-4 border-t">
        @role('Super Admin')
            <div>
                <h6 class="px-3 text-xs text-gray-500 uppercase font-semibold tracking-wider">Administration</h6>
                <div class="mt-2 space-y-1">
                    <x-responsive-nav-link :href="route('admin.organizations.index')" :active="request()->routeIs('admin.organizations.*')">
                        <x-heroicon-o-building-office-2 class="mr-2 h-6 w-6 shrink-0"/>
                        {{ __('Organisations') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <x-heroicon-o-user-group class="mr-2 h-6 w-6 shrink-0"/>
                        {{ __('Utilisateurs') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                        <x-heroicon-o-key class="mr-2 h-6 w-6 shrink-0"/>
                        {{ __('Rôles') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endrole

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

