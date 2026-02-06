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
                        @can('manage-organizations')
                        <li class="flex">
                            <a href="{{ route('admin.organizations.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.organizations.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:office-building" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.organizations.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Organisations</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Véhicules avec sous-menu --}}
                        @canany(['vehicles.view', 'assignments.view'])
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
                                        @can('vehicles.view')
                                        <a href="{{ route('admin.vehicles.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicles.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:format-list-bulleted" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicles.index') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Gestion Véhicules
                                        </a>
                                        @endcan
                                        @can('assignments.view')
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
                        @canany(['drivers.view', 'driver-sanctions.view.all', 'driver-sanctions.view.team', 'driver-sanctions.view.own'])
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
                                        @can('drivers.view')
                                        <a href="{{ route('admin.drivers.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.drivers.index') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:view-list" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.drivers.index') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Liste
                                        </a>
                                        @endcan
                                        @canany(['driver-sanctions.view.all', 'driver-sanctions.view.team', 'driver-sanctions.view.own'])
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
                        @can('depots.view')
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
                        @can('repair-requests.view.own')
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
                        @canany(['mileage-readings.view.own', 'mileage-readings.view.team', 'mileage-readings.view.all'])
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
                                        @can('mileage-readings.create')
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
                        @canany(['maintenance.view', 'repair-requests.view.team', 'repair-requests.view.all', 'repair-requests.view.own'])
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
                                        @canany(['repair-requests.view.team', 'repair-requests.view.all'])
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
                        @can('alerts.view')
                        <li class="flex">
                            <a href="{{ route('admin.alerts.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.alerts.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:bell-ring" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.alerts.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Alertes</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Documents --}}
                        @can('documents.view')
                        <li class="flex">
                            <a href="{{ route('admin.documents.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:file-document" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.documents.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Documents</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Fournisseurs --}}
                        @can('suppliers.view')
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
                        @canany(['expenses.view', 'expenses.create', 'expenses.approve'])
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
                                        @can('expenses.create')
                                        <a href="{{ route('admin.vehicle-expenses.create') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:plus-circle" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.create') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Nouvelle dépense
                                        </a>
                                        @endcan

                                        {{-- Analytics --}}
                                        @can('expenses.analytics.view')
                                        <a href="{{ route('admin.vehicle-expenses.dashboard') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:chart-line" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.dashboard') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Analytics
                                        </a>
                                        @endcan

                                        {{-- En attente d'approbation --}}
                                        @can('expenses.approve')
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
                                        @can('expenses.export')
                                        <a href="{{ route('admin.vehicle-expenses.export') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vehicle-expenses.export') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="lucide:download" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.vehicle-expenses.export') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Export
                                        </a>
                                        @endcan

                                        {{-- TCO & Rapports --}}
                                        @can('expenses.analytics.view')
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
                        @can('analytics.view')
                        <li class="flex">
                            <a href="{{ route('admin.reports.index') }}"
                                class="flex items-center w-full h-11 px-3.5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-white hover:text-gray-900 hover:shadow-sm' }}">
                                <x-iconify icon="mdi:chart-bar" class="w-5 h-5 mr-3 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-gray-600' }}" />
                                <span class="flex-1">Rapports</span>
                            </a>
                        </li>
                        @endcan

                        {{-- Administration avec sous-menu --}}
                        @canany(['users.view', 'roles.view', 'audit-logs.view'])
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
                                        @can('users.view')
                                        <a href="{{ route('admin.users.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:account-multiple" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Utilisateurs
                                        </a>
                                        @endcan
                                        @can('roles.view')
                                        <a href="{{ route('admin.roles.index') }}"
                                            class="flex items-center w-full h-9 px-2.5 py-1.5 rounded-md text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-white/70 hover:text-gray-900' }}">
                                            <x-iconify icon="mdi:shield-check" class="w-4 h-4 mr-2.5 {{ request()->routeIs('admin.roles.*') ? 'text-blue-600' : 'text-gray-600' }}" />
                                            Rôles & Permissions
                                        </a>
                                        @endcan
                                        @can('audit-logs.view')
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
