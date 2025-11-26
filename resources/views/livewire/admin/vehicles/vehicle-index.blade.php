<div class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- HEADER --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                Gestion des Véhicules
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $vehicles->total() }})
                </span>
            </h1>
        </div>

        {{-- ANALYTICS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Total --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total véhicules</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $analytics['total_vehicles'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>
            
            {{-- Disponibles --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Disponibles</p>
                        <p class="text-xl font-bold text-green-600 mt-1">{{ $analytics['available_vehicles'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle-2" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Affectés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Affectés</p>
                        <p class="text-xl font-bold text-orange-600 mt-1">{{ $analytics['assigned_vehicles'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:user-check" class="w-5 h-5 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- Maintenance --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">En maintenance</p>
                        <p class="text-xl font-bold text-red-600 mt-1">{{ $analytics['maintenance_vehicles'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-5 h-5 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Archivés --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Archivés</p>
                        <p class="text-xl font-bold text-gray-500 mt-1">{{ $analytics['archived_vehicles'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:archive" class="w-5 h-5 text-gray-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS & ACTIONS --}}
        <div class="mb-6" x-data="{ showFilters: false }">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                {{-- Search --}}
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="Rechercher par immatriculation, marque, modèle..."
                            class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                    </div>
                </div>

                {{-- Toggle Filters --}}
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <span class="font-medium text-gray-700">Filtres</span>
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    @if($archived === 'true')
                        <button wire:click="$set('archived', 'false')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-sm">
                            <x-iconify icon="lucide:list" class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>
                        </button>
                    @else
                        <button wire:click="$set('archived', 'true')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                            <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                            <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>
                        </button>
                    @endif

                    @can('create vehicles')
                        <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:plus-circle" class="w-5 h-5" />
                            <span class="hidden sm:inline">Nouveau véhicule</span>
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Filters Panel --}}
            <div x-show="showFilters" x-collapse class="mt-4 bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Depot --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dépôt</label>
                        <select wire:model.live="depot_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les dépôts</option>
                            @foreach($depots as $depot)
                                <option value="{{ $depot->id }}">{{ $depot->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select wire:model.live="status_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les statuts</option>
                            @foreach($vehicleStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="vehicle_type_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les types</option>
                            @foreach($vehicleTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fuel --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carburant</label>
                        <select wire:model.live="fuel_type_id" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Tous les carburants</option>
                            @foreach($fuelTypes as $fuel)
                                <option value="{{ $fuel->id }}">{{ $fuel->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Par page</label>
                        <select wire:model.live="per_page" class="block w-full border-gray-300 rounded-lg text-sm">
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <x-card padding="p-0" margin="mb-6">
            <div class="overflow-x-auto">
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
                            <tr class="hover:bg-gray-50 transition-colors duration-150 {{ in_array($vehicle->id, $selectedVehicles) ? 'bg-blue-50' : '' }}">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="flex items-center text-sm">
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
                                            <span class="text-sm text-gray-900">{{ $vehicle->depot->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Non assigné</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @php
                                        $assignment = $vehicle->assignments->first();
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
                                                <span class="text-sm font-medium text-gray-900">{{ $driver->first_name }} {{ $driver->last_name }}</span>
                                                @if($driver->employee_number)
                                                    <span class="text-xs text-gray-500">{{ $driver->employee_number }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Non affecté</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- Quick Actions --}}
                                        @can('view vehicles')
                                            <a href="{{ route('admin.vehicles.show', $vehicle) }}" 
                                               class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                                               title="Voir">
                                                <x-iconify icon="lucide:eye" class="w-4 h-4" />
                                            </a>
                                        @endcan
                                        
                                        @can('edit vehicles')
                                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" 
                                               class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-all duration-200"
                                               title="Modifier">
                                                <x-iconify icon="lucide:edit" class="w-4 h-4" />
                                            </a>
                                        @endcan
                                        
                                        {{-- Dropdown Menu --}}
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    @click.away="open = false"
                                                    type="button"
                                                    class="inline-flex items-center p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200"
                                                    title="Plus d'actions">
                                                <x-iconify icon="lucide:more-vertical" class="w-4 h-4" />
                                            </button>

                                            <div x-show="open"
                                                 x-cloak
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100">
                                                <div class="py-1">
                                                    {{-- Duplicate --}}
                                                    @can('create vehicles')
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
                                                    <a href="{{ route('admin.vehicles.export.single.pdf', $vehicle) }}"
                                                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:file-text" class="w-4 h-4 mr-2 text-emerald-600" />
                                                        Exporter PDF
                                                    </a>
                                                    
                                                    {{-- Archive --}}
                                                    @can('delete vehicles')
                                                        <div class="border-t border-gray-100">
                                                            <button wire:click="archiveVehicle({{ $vehicle->id }})"
                                                                    @click="open = false"
                                                                    class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                                <x-iconify icon="lucide:archive" class="w-4 h-4 mr-2 text-orange-600" />
                                                                Archiver
                                                            </button>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </div>
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
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $vehicles->links() }}
            </div>
        </x-card>
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
                {{-- Assign to Depot --}}
                <button 
                    @click="$dispatch('open-depot-assignment-modal')" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    <x-iconify icon="lucide:building-2" class="w-4 h-4" />
                    <span class="hidden sm:inline">Affecter Dépôt</span>
                </button>

                {{-- Change Status --}}
                <button 
                    @click="$dispatch('open-status-change-modal')" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    <x-iconify icon="lucide:refresh-cw" class="w-4 h-4" />
                    <span class="hidden sm:inline">Changer Statut</span>
                </button>

                {{-- Archive --}}
                <button 
                    @click="$dispatch('open-archive-modal')" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                    <x-iconify icon="lucide:archive" class="w-4 h-4" />
                    <span class="hidden sm:inline">Archiver</span>
                </button>

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
    
    {{-- Depot Assignment Modal --}}
    <div x-data="{ open: false }" 
         @open-depot-assignment-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:building-2" class="w-5 h-5 text-purple-600" />
                        Affecter à un Dépôt
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
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
                    <button @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button 
                        wire:click="bulkAssignDepot($wire.bulkDepotId)" 
                        @click="open = false"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Affecter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Change Modal --}}
    <div x-data="{ open: false }" 
         @open-status-change-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:refresh-cw" class="w-5 h-5 text-amber-600" />
                        Changer le Statut
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
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
                    <button @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button 
                        wire:click="bulkChangeStatus($wire.bulkStatusId)" 
                        @click="open = false"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                        Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Archive Confirmation Modal --}}
    <div x-data="{ open: false }" 
         @open-archive-modal.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:archive" class="w-5 h-5 text-gray-600" />
                        Archiver les Véhicules
                    </h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-500">
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
                                    Vous êtes sur le point d'archiver <span class="font-bold" x-text="@js($this->selectedVehicles).length"></span> véhicule(s).
                                    Cette action peut être annulée ultérieurement.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button 
                        wire:click="bulkArchive" 
                        @click="open = false"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Archiver
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
