<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- HEADER ULTRA-COMPACT --}}
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                Gestion des Véhicules
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $vehicles->total() }})
                </span>
            </h1>

            {{-- Loading Indicator (no layout shift) --}}
            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search"
                aria-live="polite">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        {{-- ANALYTICS CARDS --}}
        <x-page-analytics-grid columns="5">
            {{-- Total --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total véhicules</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_vehicles'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

                {{-- Disponibles --}}
                <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Disponibles</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['available_vehicles'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:check-circle-2" class="w-6 h-6 text-green-600" />
                        </div>
                    </div>
                </div>

                {{-- Affectés --}}
                <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Affectés</p>
                            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['assigned_vehicles'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:user-check" class="w-6 h-6 text-orange-600" />
                        </div>
                    </div>
                </div>

                {{-- Maintenance --}}
                <div class="bg-amber-50 rounded-lg border border-amber-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">En maintenance</p>
                            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $analytics['maintenance_vehicles'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 border border-amber-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:wrench" class="w-6 h-6 text-amber-600" />
                        </div>
                    </div>
                </div>

                {{-- En Panne --}}
                <div class="bg-rose-50 rounded-lg border border-rose-200 p-6 hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">En panne</p>
                            <p class="text-2xl font-bold text-rose-600 mt-1">{{ $analytics['broken_vehicles'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-rose-100 border border-rose-200 rounded-lg flex items-center justify-center">
                            <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-rose-600" />
                        </div>
                    </div>
                </div>
            </x-page-analytics-grid>
        {{-- FILTERS & ACTIONS (Unified) --}}
        <x-page-search-bar x-data="{ showFilters: false }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Rechercher par immatriculation, marque, modèle..."
                        wire:loading.attr="aria-busy"
                        wire:target="search"
                        class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                    </div>
                </div>
            </x-slot:search>

            <x-slot:filters>
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    title="Filtres"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>
            </x-slot:filters>

            <x-slot:actions>
                @if($visibility === 'archived')
                <button wire:click="$set('visibility', 'active')"
                    title="Voir Actifs"
                    class="inline-flex items-center gap-2 p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:list" class="w-5 h-5" />
                </button>
                @else
                <button wire:click="$set('visibility', 'archived')"
                    title="Voir Archives"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                </button>
                @endif

                {{-- Export Dropdown --}}
                @can('vehicles.export')
                <div class="relative" x-data="{ exportOpen: false }">
                    <button
                        @click="exportOpen = !exportOpen"
                        @click.away="exportOpen = false"
                        type="button"
                        title="Exporter"
                        class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                        <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                    </button>
                    {{-- Dropdown --}}
                    <div
                        x-show="exportOpen"
                        x-transition
                        class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        style="display: none;">
                        <div class="py-1">
                            <button wire:click="exportPdf"
                                @click="exportOpen = false"
                                wire:loading.attr="disabled"
                                wire:target="exportPdf"
                                class="group flex w-full items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-wait">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-gray-400 animate-spin" wire:loading wire:target="exportPdf" />
                                <x-iconify icon="lucide:file-text" class="w-4 h-4 text-red-500" wire:loading.remove wire:target="exportPdf" />
                                <span>Export PDF</span>
                                <span class="ml-auto text-xs text-gray-400" wire:loading wire:target="exportPdf">Génération...</span>
                            </button>
                            <button wire:click="exportExcel"
                                @click="exportOpen = false"
                                wire:loading.attr="disabled"
                                wire:target="exportExcel"
                                class="group flex w-full items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-wait">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-gray-400 animate-spin" wire:loading wire:target="exportExcel" />
                                <x-iconify icon="lucide:file-spreadsheet" class="w-4 h-4 text-green-600" wire:loading.remove wire:target="exportExcel" />
                                <span>Export Excel</span>
                                <span class="ml-auto text-xs text-gray-400" wire:loading wire:target="exportExcel">Génération...</span>
                            </button>
                            <button wire:click="exportCsv"
                                @click="exportOpen = false"
                                wire:loading.attr="disabled"
                                wire:target="exportCsv"
                                class="group flex w-full items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-wait">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-gray-400 animate-spin" wire:loading wire:target="exportCsv" />
                                <x-iconify icon="lucide:file-code" class="w-4 h-4 text-blue-500" wire:loading.remove wire:target="exportCsv" />
                                <span>Export CSV</span>
                                <span class="ml-auto text-xs text-gray-400" wire:loading wire:target="exportCsv">Génération...</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endcan

                @can('vehicles.import')
                {{-- Import --}}
                <a href="{{ route('admin.vehicles.import.show') }}"
                    title="Importer"
                    class="inline-flex items-center gap-2 p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:upload" class="w-5 h-5" />
                </a>
                @endcan

                @can('vehicles.create')
                {{-- Nouveau Véhicule --}}
                <a href="{{ route('admin.vehicles.create') }}"
                    title="Nouveau Véhicule"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    {{-- Depot --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt</label>
                        <x-slim-select wire:model.live="depot_id" name="depot_id" placeholder="Tous les dépôts">
                            <option value="" data-placeholder="true">Tous les dépôts</option>
                            @foreach($depots as $depot)
                            <option value="{{ $depot->id }}">{{ $depot->name }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <x-slim-select wire:model.live="status_id" name="status_id" placeholder="Tous les statuts">
                            <option value="" data-placeholder="true">Tous les statuts</option>
                            @foreach($vehicleStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                        <x-slim-select wire:model.live="brand" name="brand" placeholder="Toutes les marques">
                            <option value="" data-placeholder="true">Toutes les marques</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    {{-- Fuel Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de carburant</label>
                        <x-slim-select wire:model.live="fuel_type_id" name="fuel_type_id" placeholder="Tous les carburants">
                            <option value="" data-placeholder="true">Tous les carburants</option>
                            @foreach($fuelTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    {{-- Acquisition Date Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition (de)</label>
                        <x-datepicker
                            wire:model.live="acquisition_date_from"
                            name="acquisition_date_from"
                            placeholder="JJ/MM/AAAA" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition (à)</label>
                        <x-datepicker
                            wire:model.live="acquisition_date_to"
                            name="acquisition_date_to"
                            placeholder="JJ/MM/AAAA" />
                    </div>

                    {{-- Mileage Range --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kilométrage min</label>
                        <x-input
                            wire:model.live.debounce.500ms="mileage_min"
                            type="number"
                            placeholder="0" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kilométrage max</label>
                        <x-input
                            wire:model.live.debounce.500ms="mileage_max"
                            type="number"
                            placeholder="999999" />
                    </div>

                    <x-slot:reset>
                        @if($search || $status_id || $depot_id || $fuel_type_id || $brand || $acquisition_date_from || $acquisition_date_to || $mileage_min || $mileage_max)
                        <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        {{-- TABLE --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-visible relative">
            <div class="overflow-x-auto overflow-y-visible">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="relative px-3 py-2 w-12">
                                        <input type="checkbox" wire:click="toggleAll" @checked($selectAll) class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </th>
                                    <th wire:click="sortBy('registration_plate')" scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700">
                                        Véhicule
                                        @if($sortField === 'registration_plate')
                                        <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="inline w-3 h-3 ml-1" />
                                        @endif
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th wire:click="sortBy('current_mileage')" scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700">
                                        Kilométrage
                                        @if($sortField === 'current_mileage')
                                        <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="inline w-3 h-3 ml-1" />
                                        @endif
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dépôt</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($vehicles as $vehicle)
                                <tr wire:key="vehicle-{{ $vehicle->id }}" class="hover:bg-gray-50 transition-colors duration-150 {{ in_array($vehicle->id, $selectedVehicles) ? 'bg-blue-50' : '' }}">
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <input type="checkbox" wire:click="toggleSelection({{ $vehicle->id }})" @checked(in_array($vehicle->id, $selectedVehicles)) class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9">
                                                <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center ring-1 ring-gray-200 shadow-sm">
                                                    <x-iconify icon="lucide:car" class="h-4 w-4 text-gray-600" />
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900">{{ $vehicle->registration_plate }}</div>
                                                <div class="text-xs text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 shadow-sm">
                                            {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        <div class="flex items-center text-xs">
                                            <x-iconify icon="lucide:gauge" class="h-3.5 w-3.5 text-gray-400 mr-1.5" />
                                            <span class="font-medium text-gray-900">{{ number_format($vehicle->current_mileage) }}</span>
                                            <span class="text-gray-500 ml-1">km</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @livewire('admin.vehicle-status-badge-ultra-pro', ['vehicle' => $vehicle], key('status-'.$vehicle->id))
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @if($vehicle->depot)
                                        <div class="flex items-center gap-1.5">
                                            <x-iconify icon="lucide:building-2" class="w-3.5 h-3.5 text-purple-600" />
                                            <span class="text-xs text-gray-900">{{ $vehicle->depot->name }}</span>
                                        </div>
                                        @else
                                        <span class="text-xs text-gray-400 italic">Non assigné</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap">
                                        @php
                                        $assignment = $vehicle->currentAssignment;
                                        $driver = $assignment ? $assignment->driver : null;
                                        @endphp
                                        @if($driver)
                                        <div class="flex items-center gap-2">
                                            @if($driver->photo)
                                            <img src="{{ asset('storage/' . $driver->photo) }}"
                                                alt="{{ $driver->first_name }}"
                                                class="h-8 w-8 rounded-full object-cover ring-2 ring-emerald-100 shadow-sm">
                                            @else
                                            <div class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-xs font-bold text-white ring-2 ring-emerald-100 shadow-sm">
                                                {{ substr($driver->first_name, 0, 1) }}{{ substr($driver->last_name, 0, 1) }}
                                            </div>
                                            @endif
                                            <div class="flex flex-col">
                                                <span class="text-xs font-medium text-gray-900">{{ $driver->first_name }} {{ $driver->last_name }}</span>
                                                @if($driver->employee_number)
                                                <span class="text-[10px] text-gray-500">{{ $driver->employee_number }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-xs text-gray-400 italic">Non affecté</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Quick Actions --}}
                                            @can('vehicles.view')
                                            <a href="{{ route('admin.vehicles.show', $vehicle) }}"
                                                class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                                title="Voir">
                                                <x-iconify icon="lucide:eye" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                            </a>
                                            @endcan

                                            @can('vehicles.update')
                                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                                class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                                title="Modifier">
                                                <x-iconify icon="lucide:edit-3" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                            </a>
                                            @endcan

                                            {{-- Dropdown Menu --}}
                                            <div class="relative inline-block text-left"
                                                x-data="{
                                                    open: false,
                                                    styles: '',
                                                    direction: 'down',
                                                    toggle() {
                                                        this.open = !this.open;
                                                        if (this.open) {
                                                            this.$nextTick(() => requestAnimationFrame(() => this.updatePosition()));
                                                        }
                                                    },
                                                    close() { this.open = false; },
                                                    updatePosition() {
                                                        if (!this.$refs.trigger || !this.$refs.menu) return;
                                                        const rect = this.$refs.trigger.getBoundingClientRect();
                                                        const width = 192; // w-48
                                                        const padding = 12;
                                                        const menuHeight = this.$refs.menu.offsetHeight || 200;
                                                        const spaceBelow = window.innerHeight - rect.bottom - padding;
                                                        const spaceAbove = rect.top - padding;
                                                        const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
                                                        this.direction = shouldOpenUp ? 'up' : 'down';

                                                        let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
                                                        if (top < padding) top = padding;
                                                        if (top + menuHeight > window.innerHeight - padding) {
                                                            top = window.innerHeight - padding - menuHeight;
                                                        }

                                                        let left = rect.right - width;
                                                        const maxLeft = window.innerWidth - width - padding;
                                                        if (left > maxLeft) left = maxLeft;
                                                        if (left < padding) left = padding;

                                                        this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${width}px; z-index: 80;`;
                                                    }
                                                }"
                                                x-init="
                                                    window.addEventListener('scroll', () => { if (open) updatePosition(); }, true);
                                                    window.addEventListener('resize', () => { if (open) updatePosition(); });
                                                ">
                                                <button x-ref="trigger"
                                                    @click="toggle"
                                                    @click.outside="close"
                                                    type="button"
                                                    class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group"
                                                    title="Plus d'actions">
                                                    <x-iconify icon="lucide:more-vertical" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                                </button>

                                                <template x-teleport="body">
                                                    <div x-show="open"
                                                        x-cloak
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        x-transition:leave="transition ease-in duration-75"
                                                        x-transition:leave-start="transform opacity-100 scale-100"
                                                        x-transition:leave-end="transform opacity-0 scale-95"
                                                        :style="styles"
                                                        @click.outside="close"
                                                        x-ref="menu"
                                                        :class="direction === 'up' ? 'origin-bottom-right' : 'origin-top-right'"
                                                        class="fixed z-[80] rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                        <div class="py-1">
                                                            @if($visibility === 'archived')
                                                            {{-- Restore --}}
                                                            @can('vehicles.restore')
                                                            <button wire:click="confirmRestore({{ $vehicle->id }})"
                                                                @click="close"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4 mr-2 text-green-600" />
                                                                Restaurer
                                                            </button>
                                                            @endcan

                                                            {{-- Force Delete --}}
                                                            @can('vehicles.force-delete')
                                                            <button wire:click="confirmForceDelete({{ $vehicle->id }})"
                                                                @click="close"
                                                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-red-50 transition-colors">
                                                                <x-iconify icon="lucide:trash-2" class="w-4 h-4 mr-2 text-red-600" />
                                                                Supprimer
                                                            </button>
                                                            @endcan
                                                            @else
                                                            {{-- Duplicate --}}
                                                            @can('vehicles.create')
                                                            <a href="{{ route('admin.vehicles.duplicate', $vehicle) }}"
                                                                class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                <x-iconify icon="lucide:copy" class="w-4 h-4 mr-2 text-purple-600" />
                                                                Dupliquer
                                                            </a>
                                                            @endcan

                                                            {{-- History --}}
                                                            <a href="{{ route('admin.vehicles.history', $vehicle) }}"
                                                                class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                <x-iconify icon="lucide:clock" class="w-4 h-4 mr-2 text-cyan-600" />
                                                                Historique
                                                            </a>



                                                            {{-- Export PDF --}}
                                                            @can('vehicles.export')
                                                            <a href="{{ route('admin.vehicles.export.single.pdf', $vehicle) }}"
                                                                class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-2 text-emerald-600" />
                                                                Exporter PDF
                                                            </a>
                                                            @endcan

                                                            {{-- Archive --}}
                                                            @can('vehicles.delete')
                                                            <div class="border-t border-gray-100">
                                                                <button type="button"
                                                                    @click="close(); $wire.confirmArchive({{ $vehicle->id }})"
                                                                    class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                    <x-iconify icon="lucide:archive" class="w-4 h-4 mr-2 text-orange-600" />
                                                                    Archiver
                                                                </button>
                                                            </div>
                                                            @endcan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                <x-iconify icon="lucide:search-x" class="w-6 h-6 text-gray-400" />
                                            </div>
                                            <h3 class="text-sm font-medium text-gray-900">Aucun véhicule trouvé</h3>
                                            <p class="text-sm text-gray-500 mt-1">Essayez de modifier vos filtres de recherche.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            <x-pagination :paginator="$vehicles" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>

            {{-- ================================================================
    🎯 BULK ACTIONS FLOATING MENU - Enterprise Grade
    ================================================================
    Menu flottant qui apparaît quand des véhicules sont sélectionnés
    Actions: Affectation Dépôt, Changement Statut, Archivage
    ================================================================ --}}
            <div x-data="{ show: @entangle('selectedVehicles').live }"
                x-show="show.length > 0"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50"
                style="display: none;">

                <div class="bg-white rounded-xl shadow-2xl border border-gray-200 px-6 py-4 flex items-center gap-6">
                    {{-- Selected Count --}}
                    <div class="flex items-center gap-2 border-r border-gray-300 pr-6">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 text-blue-600" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Sélectionnés</p>
                            <p class="text-lg font-bold text-gray-900" x-text="show.length"></p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3">
                        @if($visibility === 'archived')
                        {{-- Restore --}}
                        @can('vehicles.restore')
                        <button
                            wire:click="confirmBulkRestore"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4" wire:loading.remove wire:target="bulkRestore" />
                            <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkRestore" />
                            <span class="hidden sm:inline">Restaurer</span>
                        </button>
                        @endcan

                        {{-- Force Delete --}}
                        @can('vehicles.force-delete')
                        <button
                            wire:click="confirmBulkForceDelete"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:trash-2" class="w-4 h-4" wire:loading.remove wire:target="bulkForceDelete" />
                            <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkForceDelete" />
                            <span class="hidden sm:inline">Supprimer Définitivement</span>
                        </button>
                        @endcan
                        @else
                        {{-- Assign to Depot --}}
                        @canany(['vehicles.update', 'vehicles.manage'])
                        <button
                            wire:click="$set('showBulkDepotModal', true)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:building-2" class="w-4 h-4" />
                            <span class="hidden sm:inline">Affecter Dépôt</span>
                        </button>
                        @endcanany

                        {{-- Change Status --}}
                        @canany(['vehicles.status.update', 'vehicles.update', 'vehicles.manage'])
                        <button
                            wire:click="$set('showBulkStatusModal', true)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:refresh-cw" class="w-4 h-4" />
                            <span class="hidden sm:inline">Changer Statut</span>
                        </button>
                        @endcanany

                        {{-- Archive --}}
                        @can('vehicles.delete')
                        <button
                            wire:click="$set('showBulkArchiveModal', true)"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:archive" class="w-4 h-4" />
                            <span class="hidden sm:inline">Archiver</span>
                        </button>
                        @endcan
                        @endif

                        {{-- Clear Selection --}}
                        <button
                            wire:click="$set('selectedVehicles', [])"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors border border-gray-300 shadow-sm">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            <span class="hidden sm:inline">Annuler</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ================================================================
    📋 MODALS FOR BULK ACTIONS
    ================================================================ --}}

            {{-- Depot Assignment Modal - Pure Livewire --}}
            @if($showBulkDepotModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-depot-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="$set('showBulkDepotModal', false)"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:building-2" class="w-5 h-5 text-purple-600" />
                                Affecter à un Dépôt
                            </h3>
                            <button wire:click="$set('showBulkDepotModal', false)" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un dépôt</label>
                            <select wire:model="bulkDepotId" class="block w-full border-gray-300 rounded-lg text-sm">
                                <option value="">-- Choisir un dépôt --</option>
                                @foreach($depots as $depot)
                                <option value="{{ $depot->id }}">{{ $depot->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="$set('showBulkDepotModal', false)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="bulkAssignDepot"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkAssignDepot" />
                                Affecter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Status Change Modal - Pure Livewire --}}
            @if($showBulkStatusModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-status-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="$set('showBulkStatusModal', false)"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:refresh-cw" class="w-5 h-5 text-amber-600" />
                                Changer le Statut
                            </h3>
                            <button wire:click="$set('showBulkStatusModal', false)" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau statut</label>
                            <select wire:model="bulkStatusId" class="block w-full border-gray-300 rounded-lg text-sm">
                                <option value="">-- Choisir un statut --</option>
                                @foreach($vehicleStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="$set('showBulkStatusModal', false)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="bulkChangeStatus"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkChangeStatus" />
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Archive Confirmation Modal - Pure Livewire --}}
            @if($showBulkArchiveModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-archive-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="$set('showBulkArchiveModal', false)"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:archive" class="w-5 h-5 text-gray-600" />
                                Archiver les Véhicules
                            </h3>
                            <button wire:click="$set('showBulkArchiveModal', false)" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-amber-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm font-medium text-amber-900">Attention</p>
                                        <p class="text-sm text-amber-700 mt-1">
                                            Vous êtes sur le point d'archiver <span class="font-bold">{{ count($selectedVehicles) }}</span> véhicule(s).
                                            Cette action peut être annulée ultérieurement.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="$set('showBulkArchiveModal', false)"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="bulkArchive"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkArchive" />
                                Archiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bulk Restore Confirmation Modal --}}
            @if($showBulkRestoreModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-restore-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkRestore"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:rotate-ccw" class="w-5 h-5 text-green-600" />
                                Restaurer des Véhicules
                            </h3>
                            <button wire:click="cancelBulkRestore" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir restaurer <span class="font-bold text-gray-900">{{ count($selectedVehicles) }}</span> véhicule(s) ?
                            </p>

                            @if($this->selectedVehiclesPreview->count() > 0)
                            <div class="mt-3 rounded-md border border-gray-200 bg-gray-50 p-3">
                                <p class="text-xs font-semibold uppercase text-gray-600 mb-2">Résumé des véhicules</p>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    @foreach($this->selectedVehiclesPreview as $previewVehicle)
                                    <li class="flex items-center justify-between gap-3">
                                        <span class="font-medium">{{ $previewVehicle->registration_plate }}</span>
                                        <span class="text-xs text-gray-500">{{ $previewVehicle->brand }} {{ $previewVehicle->model }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @if(count($selectedVehicles) > $this->selectedVehiclesPreview->count())
                                <p class="mt-2 text-xs text-gray-500">
                                    + {{ count($selectedVehicles) - $this->selectedVehiclesPreview->count() }} autres véhicules
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelBulkRestore"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="bulkRestore"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkRestore" />
                                Restaurer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bulk Force Delete Confirmation Modal --}}
            @if($showBulkForceDeleteModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-force-delete-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkForceDelete"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:trash-2" class="w-5 h-5 text-red-600" />
                                Suppression Définitive
                            </h3>
                            <button wire:click="cancelBulkForceDelete" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir supprimer définitivement <span class="font-bold text-gray-900">{{ count($selectedVehicles) }}</span> véhicule(s) ?
                            </p>

                            @if($this->selectedVehiclesPreview->count() > 0)
                            <div class="mt-3 rounded-md border border-gray-200 bg-gray-50 p-3">
                                <p class="text-xs font-semibold uppercase text-gray-600 mb-2">Résumé des véhicules</p>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    @foreach($this->selectedVehiclesPreview as $previewVehicle)
                                    <li class="flex items-center justify-between gap-3">
                                        <span class="font-medium">{{ $previewVehicle->registration_plate }}</span>
                                        <span class="text-xs text-gray-500">{{ $previewVehicle->brand }} {{ $previewVehicle->model }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @if(count($selectedVehicles) > $this->selectedVehiclesPreview->count())
                                <p class="mt-2 text-xs text-gray-500">
                                    + {{ count($selectedVehicles) - $this->selectedVehiclesPreview->count() }} autres véhicules
                                </p>
                                @endif
                            </div>
                            @endif

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Tapez <span class="font-semibold text-red-600">SUPPRIMER</span> pour confirmer</label>
                                <input
                                    type="text"
                                    wire:model.live="bulkForceDeleteConfirm"
                                    placeholder="SUPPRIMER"
                                    class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" />
                            </div>

                            <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm font-medium text-red-900">Attention : Action Irréversible</p>
                                        <p class="text-sm text-red-700 mt-1">
                                            Toutes les données associées à ces véhicules seront supprimées définitivement.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelBulkForceDelete"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="bulkForceDelete"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                {{ strtoupper(trim($bulkForceDeleteConfirm)) !== 'SUPPRIMER' ? 'disabled' : '' }}
                                class="px-4 py-2 text-white rounded-lg inline-flex items-center gap-2 {{ strtoupper(trim($bulkForceDeleteConfirm)) !== 'SUPPRIMER' ? 'bg-red-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700' }}">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkForceDelete" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Restore Confirmation Modal - Pure Livewire --}}
            @if($showRestoreModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="restore-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelRestore"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:rotate-ccw" class="w-5 h-5 text-green-600" />
                                Restaurer le Véhicule
                            </h3>
                            <button wire:click="cancelRestore" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            @if($this->restoringVehicle)
                            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ $this->restoringVehicle->registration_plate }}</p>
                                <p class="text-xs text-gray-500">{{ $this->restoringVehicle->brand }} {{ $this->restoringVehicle->model }}</p>
                            </div>
                            @endif
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir restaurer ce véhicule ?
                                <br><br>
                                Il sera de nouveau visible dans la liste des véhicules actifs.
                            </p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelRestore"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="restoreVehicle"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="restoreVehicle" />
                                Restaurer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Force Delete Confirmation Modal - Pure Livewire --}}
            @if($showForceDeleteModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="force-delete-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelForceDelete"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:trash-2" class="w-5 h-5 text-red-600" />
                                Suppression Définitive
                            </h3>
                            <button wire:click="cancelForceDelete" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            @if($this->forceDeletingVehicle)
                            <div class="mb-4 bg-red-50 p-3 rounded-lg border border-red-100">
                                <p class="text-sm font-medium text-red-900">{{ $this->forceDeletingVehicle->registration_plate }}</p>
                                <p class="text-xs text-red-700">{{ $this->forceDeletingVehicle->brand }} {{ $this->forceDeletingVehicle->model }}</p>
                            </div>
                            @endif
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600 mt-0.5" />
                                    <div>
                                        <p class="text-sm font-medium text-red-900">Attention : Action Irréversible</p>
                                        <p class="text-sm text-red-700 mt-1">
                                            Vous êtes sur le point de supprimer définitivement ce véhicule.
                                            <br>
                                            Toutes les données associées seront perdues.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelForceDelete"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="forceDeleteVehicle"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="forceDeleteVehicle" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Archive Individual Confirmation Modal - Pure Livewire --}}
            @if($showArchiveModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="archive-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelArchive"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:archive" class="w-5 h-5 text-orange-600" />
                                Archiver le Véhicule
                            </h3>
                            <button wire:click="cancelArchive" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="mb-6">
                            @if($this->archivingVehicle)
                            <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ $this->archivingVehicle->registration_plate }}</p>
                                <p class="text-xs text-gray-500">{{ $this->archivingVehicle->brand }} {{ $this->archivingVehicle->model }}</p>
                            </div>
                            @endif
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir archiver ce véhicule ?
                                <br><br>
                                Il sera déplacé dans la liste des véhicules archivés et ne sera plus sélectionnable pour de nouvelles affectations.
                            </p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelArchive"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="archiveVehicle"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50 cursor-not-allowed"
                                class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="archiveVehicle" />
                                Archiver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Individual Status Change Modal --}}
            @if($showIndividualStatusModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="individual-status-modal">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelIndividualStatusChange"></div>

                    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:refresh-cw" class="w-5 h-5 text-amber-600" />
                                Changer le Statut
                            </h3>
                            <button wire:click="cancelIndividualStatusChange" class="text-gray-400 hover:text-gray-500">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        @if($this->individualStatusVehicle)
                        <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-900">{{ $this->individualStatusVehicle->registration_plate }}</p>
                            <p class="text-xs text-gray-500">{{ $this->individualStatusVehicle->brand }} {{ $this->individualStatusVehicle->model }}</p>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau statut</label>
                            <select wire:model="individualStatusId" class="block w-full border-gray-300 rounded-lg text-sm">
                                <option value="">-- Choisir un statut --</option>
                                @foreach($vehicleStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('individualStatusId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button wire:click="cancelIndividualStatusChange"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                Annuler
                            </button>
                            <button wire:click="updateIndividualStatus"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 inline-flex items-center gap-2">
                                <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="updateIndividualStatus" />
                                Modifier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
