<nav class="bg-white border-r border-gray-200">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-center h-20 border-b border-gray-200 shrink-0">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-10 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <div class="mt-5 flex-grow">
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @can('view vehicles')
                    <x-responsive-nav-link :href="route('admin.vehicles.index')" :active="request()->routeIs('admin.vehicles.*')">
                        {{ __('Véhicules') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view drivers')
                    <x-responsive-nav-link :href="route('admin.drivers.index')" :active="request()->routeIs('admin.drivers.*')">
                        {{ __('Chauffeurs') }}
                    </x-responsive-nav-link>
                @endcan

                @can('view assignments')
                    <x-responsive-nav-link :href="route('admin.assignments.index')" :active="request()->routeIs('admin.assignments.*')">
                        {{ __('Affectations') }}
                    </x-responsive-nav-link>
                @endcan

                {{-- CORRECTION : On passe une variable PHP au lieu d'une fonction --}}
                @canany(['view maintenance', 'manage maintenance plans'])
                    @php($isMaintenanceActive = request()->routeIs('admin.maintenance.*'))
                    <div x-data="{ open: {{ $isMaintenanceActive ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = ! open" class="w-full flex justify-between items-center px-4 py-2 text-left text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:bg-gray-50 focus:text-gray-900 transition duration-150 ease-in-out">
                            <span class="flex items-center">
                                {{-- Vous pouvez ajouter une icône ici si vous le souhaitez --}}
                                <span class="ml-3">{{ __('Maintenance') }}</span>
                            </span>
                            <svg class="w-4 h-4 ml-auto transition-transform duration-200 transform" :class="{'rotate-180': open, 'rotate-0': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition class="pl-8 space-y-1">
                            @can('view maintenance')
                            <x-responsive-nav-link :href="route('admin.maintenance.dashboard')" :active="request()->routeIs('admin.maintenance.dashboard')">
                                {{ __('Tableau de Bord') }}
                            </x-responsive-nav-link>
                            @endcan
                            @can('manage maintenance plans')
                            <x-responsive-nav-link :href="route('admin.maintenance.plans.index')" :active="request()->routeIs('admin.maintenance.plans.*')">
                                {{ __('Plans de Maintenance') }}
                            </x-responsive-nav-link>
                            @endcan
                        </div>
                    </div>
                @endcanany

                @role('Admin')
                    <div class="pt-4 pb-2">
                        <h6 class="px-4 text-xs text-gray-500 uppercase font-semibold">Administration</h6>
                    </div>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('Utilisateurs') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">{{ __('Rôles & Permissions') }}</x-responsive-nav-link>
                @endrole
            </div>
        </div>
    </div>
</nav>
