<div x-data="{}"
    @depot-toggled.window="$wire.$refresh()"
    class="min-h-screen bg-gray-50/50">

    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
            HEADER
        =============================================== --}}
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:warehouse" class="w-6 h-6 text-blue-600" />
                Gestion des Dépôts
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $stats['total_depots'] ?? 0 }})
                </span>
            </h1>

            <div wire:loading class="flex items-center gap-2 text-blue-600">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        {{-- ===============================================
            STATS GRID
        =============================================== --}}
        <x-page-analytics-grid columns="4">
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total dépôts</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_depots'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:warehouse" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actifs</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_depots'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Capacité Totale</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_capacity'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:maximize" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Places Dispo.</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['total_available'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:parking-circle" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        {{-- ===============================================
            SEARCH & ACTIONS
        =============================================== --}}
        <x-page-search-bar x-data="{ showFilters: false }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm"
                        placeholder="Rechercher par nom, code, ville...">
                </div>
            </x-slot:search>

            <x-slot:filters>
                <button @click="showFilters = !showFilters" type="button"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                </button>
            </x-slot:filters>

            <x-slot:actions>
                <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    <span class="hidden sm:inline">Nouveau</span>
                </button>
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">Tous les statuts</option>
                            <option value="active">Actifs</option>
                            <option value="inactive">Inactifs</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Capacité</label>
                        <select wire:model.live="capacityFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">Toutes capacités</option>
                            <option value="available">Disponible</option>
                            <option value="full">Complet</option>
                        </select>
                    </div>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        {{-- ===============================================
            TABLE
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                                Nom du Dépôt
                                @if($sortBy === 'name') <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" /> @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localisation</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('capacity')">
                                Capacité
                                @if($sortBy === 'capacity') <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" /> @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($depots as $depot)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-sm">
                                        {{ substr($depot->name, 0, 2) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $depot->name }}</div>
                                        <div class="text-xs text-gray-500">ID: #{{ $depot->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 flex items-center gap-1.5">
                                    <x-iconify icon="lucide:map-pin" class="w-3.5 h-3.5 text-gray-400" /> {{ $depot->city }}
                                </div>
                                <div class="text-xs text-gray-500 ml-5">{{ $depot->wilaya }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $depot->capacity > 100 ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $depot->capacity }} places
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="toggleActive({{ $depot->id }})"
                                    class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $depot->is_active ? 'bg-green-500' : 'bg-gray-200' }}">
                                    <span aria-hidden="true" class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $depot->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openViewModal({{ $depot->id }})" class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50"><x-iconify icon="lucide:eye" class="w-4 h-4" /></button>
                                    <button wire:click="openEditModal({{ $depot->id }})" class="p-2 rounded-full text-gray-400 hover:text-amber-600 hover:bg-amber-50"><x-iconify icon="lucide:edit-3" class="w-4 h-4" /></button>
                                    <button wire:click="delete({{ $depot->id }})" wire:confirm="Êtes-vous sûr ?" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50"><x-iconify icon="lucide:trash-2" class="w-4 h-4" /></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Aucun dépôt trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $depots->links() }}
            </div>
        </div>
    </div>

    {{-- ===============================================
        INLINE MODAL (No Component)
    =============================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                {{-- Modal Header --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $modalMode === 'create' ? 'Nouveau Dépôt' : ($modalMode === 'edit' ? 'Modifier le Dépôt' : 'Détails du Dépôt') }}
                        </h3>
                        <button wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <x-iconify icon="lucide:x" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="px-4 pt-5 pb-4 sm:p-6">

                    @if (session()->has('error'))
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0"><x-iconify icon="lucide:alert-circle" class="h-5 w-5 text-red-400" /></div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Nom --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Nom</label>
                                <input type="text" wire:model="name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Code --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Code</label>
                                <input type="text" wire:model="code" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                                @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                                <input type="text" wire:model="address" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Ville --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ville</label>
                                <input type="text" wire:model="city" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Wilaya --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Wilaya</label>
                                <input type="text" wire:model="wilaya" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="text" wire:model="phone" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" wire:model="email" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Manager --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Responsable</label>
                                <input type="text" wire:model="manager_name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Manager Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tél. Responsable</label>
                                <input type="text" wire:model="manager_phone" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Capacité --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Capacité</label>
                                <input type="number" wire:model="capacity" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}>
                            </div>

                            {{-- Description --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea wire:model="description" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" {{ $modalMode === 'view' ? 'disabled' : '' }}></textarea>
                            </div>

                            {{-- Active --}}
                            @if($modalMode !== 'view')
                            <div class="col-span-2 flex items-center">
                                <input type="checkbox" wire:model="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-900">Dépôt actif</label>
                            </div>
                            @endif

                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if($modalMode !== 'view')
                    <button wire:click="save" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Enregistrer
                    </button>
                    @endif
                    <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Fermer
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

</div>