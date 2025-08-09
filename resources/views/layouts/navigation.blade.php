<nav x-data="{ open: true }" class="flex flex-col h-full bg-white shadow-md font-sans">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0 px-6">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-10 w-auto" />
        </a>
    </div>

    {{-- Liens de navigation --}}
    <div class="flex-1 px-4 py-4 overflow-y-auto space-y-1 text-sm">

        <x-sidebar.sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <x-lucide-layout-grid class="h-5 w-5" stroke-width="1.5" />
            </x-slot>
            {{ __('Tableau de bord') }}
        </x-sidebar.sidebar-link>

        {{-- Section Gestion de la Flotte --}}
        @canany(['view vehicles', 'view assignments'])
            <x-sidebar.sidebar-group title="Flotte" :active="request()->routeIs('admin.vehicles.*') || request()->routeIs('admin.assignments.*')">
                <x-slot name="icon">
                    <x-lucide-truck stroke-width="1.5" />
                </x-slot>
                @can('view vehicles')
                    <x-sidebar.sidebar-sub-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.*')">
                        <x-slot name="icon"><x-lucide-truck stroke-width="1.5" /></x-slot>
                        {{ __('Véhicules') }}
                    </x-sidebar.sidebar-sub-link>
                @endcan
                @can('view assignments')
                    <x-sidebar.sidebar-sub-link :href="route('admin.assignments.index')" :active="request()->routeIs('admin.assignments.*')">
                        <x-slot name="icon"><x-lucide-clipboard-list stroke-width="1.5" /></x-slot>
                        {{ __('Affectations') }}
                    </x-sidebar.sidebar-sub-link>
                @endcan
            </x-sidebar.sidebar-group>
        @endcanany

        {{-- Section Chauffeurs --}}
        @can('view drivers')
             <x-sidebar.sidebar-group title="Chauffeurs" :active="request()->routeIs('admin.drivers.*')">
                <x-slot name="icon">
                    <x-lucide-users stroke-width="1.5" />
                </x-slot>
                <x-sidebar.sidebar-sub-link :href="route('admin.drivers.index')" :active="request()->routeIs('admin.drivers.index')">
                    <x-slot name="icon"><x-lucide-list stroke-width="1.5" /></x-slot>
                    {{ __('Liste des chauffeurs') }}
                </x-sidebar.sidebar-sub-link>
                 <x-sidebar.sidebar-sub-link :href="route('admin.drivers.import.show')" :active="request()->routeIs('admin.drivers.import.*')">
                    <x-slot name="icon"><x-lucide-file-up stroke-width="1.5" /></x-slot>
                    {{ __('Importer des chauffeurs') }}
                </x-sidebar.sidebar-sub-link>
            </x-sidebar.sidebar-group>
        @endcan

        {{-- Section Maintenance --}}
        @canany(['view maintenance', 'manage maintenance plans'])
            <x-sidebar.sidebar-group title="Maintenance" :active="request()->routeIs('admin.maintenance.*')">
                <x-slot name="icon">
                    <x-lucide-wrench stroke-width="1.5" />
                </x-slot>
                <x-sidebar.sidebar-sub-link :href="route('admin.maintenance.dashboard')" :active="request()->routeIs('admin.maintenance.dashboard')">
                    <x-slot name="icon"><x-lucide-bar-chart-2 stroke-width="1.5" /></x-slot>
                    Tableau de Bord
                </x-sidebar.sidebar-sub-link>
                <x-sidebar.sidebar-sub-link :href="route('admin.maintenance.plans.index')" :active="request()->routeIs('admin.maintenance.plans.*')">
                    <x-slot name="icon"><x-lucide-calendar-check stroke-width="1.5" /></x-slot>
                    Plans de Maintenance
                </x-sidebar.sidebar-sub-link>
            </x-sidebar.sidebar-group>
        @endcanany

        {{-- Section Administration --}}
        @role('Super Admin')
            <x-sidebar.sidebar-group title="Administration" :active="request()->routeIs('admin.organizations.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*')">
                <x-slot name="icon">
                    <x-lucide-settings stroke-width="1.5" />
                </x-slot>
                <x-sidebar.sidebar-sub-link :href="route('admin.organizations.index')" :active="request()->routeIs('admin.organizations.*')">
                    <x-slot name="icon"><x-lucide-building-2 stroke-width="1.5" /></x-slot>
                    {{ __('Organisations') }}
                </x-sidebar.sidebar-sub-link>
                <x-sidebar.sidebar-sub-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <x-slot name="icon"><x-lucide-user-cog stroke-width="1.5" /></x-slot>
                    {{ __('Utilisateurs') }}
                </x-sidebar.sidebar-sub-link>
                <x-sidebar.sidebar-sub-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                    <x-slot name="icon"><x-lucide-key-round stroke-width="1.5" /></x-slot>
                    {{ __('Rôles') }}
                </x-sidebar.sidebar-sub-link>
            </x-sidebar.sidebar-group>
        @endrole
    </div>

    {{-- Section Utilisateur / Déconnexion --}}
    <div class="mt-auto shrink-0 p-4 border-t border-gray-200">
        <div x-data="{ open: false }" @keydown.escape.window="open = false" @click.away="open = false" class="relative">
            <button @click="open = !open" class="w-full flex-1 flex items-center space-x-3 group p-2 rounded-lg hover:bg-gray-100">
                <span class="inline-block h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                     <x-lucide-user-round class="h-8 w-8 text-gray-500" stroke-width="1.5"/>
                </span>
                <div class="flex-1 text-left">
                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">Options</p>
                </div>
                <x-lucide-chevron-up class="h-5 w-5 text-gray-400 shrink-0" stroke-width="1.5"/>
            </button>

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
                        <x-lucide-user class="mr-3 h-5 w-5" stroke-width="1.5"/>
                        Mon Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md">
                            <x-lucide-log-out class="mr-3 h-5 w-5" stroke-width="1.5"/>
                            Déconnexion
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
