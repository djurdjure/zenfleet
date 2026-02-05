<div>
    {{-- ====================================================================
     👨‍💼 GESTION DES CHAUFFEURS V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
     ==================================================================== --}}

    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        @php
            $driverSuccess = session('driver_success');
        @endphp

        @if($driverSuccess)
            <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

                    <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 z-50">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:user-plus" class="w-5 h-5 text-blue-600" />
                                Compte chauffeur créé
                            </h3>
                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-sm text-gray-600">Chauffeur</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $driverSuccess['driver_name'] ?? '—' }}</p>
                                @if(!empty($driverSuccess['driver_employee_number']))
                                    <p class="text-xs text-gray-500">Matricule: {{ $driverSuccess['driver_employee_number'] }}</p>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-200 p-4">
                                <p class="text-sm text-gray-600">Email de connexion</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $driverSuccess['user_email'] ?? '—' }}</p>
                            </div>

                            @if(!empty($driverSuccess['user_was_created']) && !empty($driverSuccess['user_password']))
                                <div class="rounded-lg border border-orange-200 bg-orange-50 p-4">
                                    <p class="text-sm text-orange-700 font-medium">Mot de passe généré</p>
                                    <div class="mt-2 flex items-center justify-between gap-3">
                                        <span class="font-mono text-sm text-gray-900 bg-white border border-orange-200 rounded-md px-3 py-1.5">
                                            {{ $driverSuccess['user_password'] }}
                                        </span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $driverSuccess['user_password'] }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-orange-700 bg-white border border-orange-200 rounded-md hover:bg-orange-100 transition">
                                            <x-iconify icon="lucide:copy" class="w-4 h-4" />
                                            Copier
                                        </button>
                                    </div>
                                    <p class="mt-2 text-xs text-orange-700">
                                        Conseillé: demander au chauffeur de modifier ce mot de passe lors de sa première connexion.
                                    </p>
                                </div>
                            @else
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                                    Compte utilisateur existant associé. Aucun nouveau mot de passe généré.
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" @click="open = false"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ===============================================
            HEADER ULTRA-COMPACT
        =============================================== --}}
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                Gestion des Chauffeurs
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $drivers->total() }})
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

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO
        =============================================== --}}
        {{-- CARDS MÉTRIQUES ULTRA-PRO --}}
        <x-page-analytics-grid columns="4">
            {{-- Total Chauffeurs --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total chauffeurs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Disponibles --}}
            <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disponibles</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['available_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:user-check" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- En Mission --}}
            <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En mission</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['active_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:briefcase" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- En Repos --}}
            <div class="bg-amber-50 rounded-lg border border-amber-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En repos</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $analytics['resting_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 border border-amber-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:pause-circle" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        {{-- ===============================================
            BARRE DE RECHERCHE ET ACTIONS (Enterprise-Grade)
        =============================================== --}}
        {{-- ===============================================
            BARRE DE RECHERCHE ET ACTIONS (Enterprise-Grade)
        =============================================== --}}
        <x-page-search-bar x-data="{ showFilters: false }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Rechercher par nom, prénom, matricule..."
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
                {{-- Toggle Archives --}}
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

                @can('drivers.export')
                {{-- Export Dropdown --}}
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
                            <a href="{{ route('admin.drivers.export.pdf', request()->all()) }}" class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                <x-iconify icon="lucide:file-text" class="w-4 h-4 text-red-600" />
                                <span>Export PDF</span>
                            </a>
                            {{-- Add CSV/Excel if needed --}}
                        </div>
                    </div>
                </div>
                @endcan

                @can('drivers.import')
                {{-- Import --}}
                <a href="{{ route('admin.drivers.import.show') }}"
                    title="Importer"
                    class="inline-flex items-center gap-2 p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:upload" class="w-5 h-5" />
                </a>
                @endcan

                @can('drivers.create')
                {{-- Nouveau Chauffeur --}}
                <a href="{{ route('admin.drivers.create') }}"
                    title="Nouveau Chauffeur"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="2">
                    {{-- Statut --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <x-slim-select wire:model.live="status_id" name="status_id" placeholder="Tous les statuts">
                            <option value="" data-placeholder="true">Tous les statuts</option>
                            @foreach($driverStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    {{-- Catégorie Permis --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie permis</label>
                        <x-slim-select wire:model.live="license_category" name="license_category" placeholder="Toutes les catégories">
                            <option value="" data-placeholder="true">Toutes les catégories</option>
                            @foreach(['A1', 'A', 'B', 'BE', 'C1', 'C1E', 'C', 'CE', 'D', 'DE', 'F'] as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    <x-slot:reset>
                        @if($search || $status_id || $license_category)
                        <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        {{-- ===============================================
            TABLE DES CHAUFFEURS (Enterprise-Grade)
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden relative">

            {{-- Bulk Actions Floating Menu --}}
            <div x-data="{ show: @entangle('selectedDrivers').live }"
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
                        @can('drivers.restore')
                        <button
                            wire:click="confirmBulkRestore"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4" />
                            <span class="hidden sm:inline">Restaurer</span>
                        </button>
                        @endcan

                        @can('drivers.force-delete')
                        <button
                            wire:click="confirmBulkForceDelete"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:trash-2" class="w-4 h-4" />
                            <span class="hidden sm:inline">Supprimer Définitivement</span>
                        </button>
                        @endcan
                        @else
                        @canany(['drivers.status.update', 'drivers.update'])
                        <button
                            wire:click="confirmBulkStatusChange"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:refresh-cw" class="w-4 h-4" />
                            <span class="hidden sm:inline">Changer Statut</span>
                        </button>
                        @endcanany

                        @can('drivers.delete')
                        <button
                            wire:click="confirmBulkArchive"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <x-iconify icon="lucide:archive" class="w-4 h-4" />
                            <span class="hidden sm:inline">Archiver</span>
                        </button>
                        @endcan
                        @endif

                        {{-- Clear Selection --}}
                        <button
                            wire:click="$set('selectedDrivers', [])"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors border border-gray-300 shadow-sm">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            <span class="hidden sm:inline">Annuler</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox" wire:click="toggleAll" @if($selectAll) checked @endif class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('last_name')">
                                Chauffeur
                                @if($sortField === 'last_name')
                                <x-iconify icon="{{ $sortDirection === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="w-3 h-3 inline ml-1" />
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permis</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule Actuel</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($drivers as $driver)
                        <tr wire:key="driver-{{ $driver->id }}" class="hover:bg-gray-50 transition-colors duration-150 {{ in_array($driver->id, $selectedDrivers) ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" wire:click="toggleSelection({{ $driver->id }})" @if(in_array($driver->id, $selectedDrivers)) checked @endif class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        @if($driver->photo)
                                        <img src="{{ asset('storage/' . $driver->photo) }}" class="h-full w-full object-cover">
                                        @else
                                        <div class="h-10 w-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-blue-700">
                                                {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $driver->first_name }} {{ $driver->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">#{{ $driver->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-900 flex items-center gap-1.5">
                                    <x-iconify icon="lucide:phone" class="w-3.5 h-3.5 text-gray-400" /> {{ $driver->personal_phone ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-1">
                                    <x-iconify icon="lucide:mail" class="w-3.5 h-3.5 text-gray-400" /> {{ $driver->personal_email ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-900">{{ $driver->license_number }}</div>
                                @if(!empty($driver->license_categories))
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($driver->license_categories as $cat)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $cat }}
                                    </span>
                                    @endforeach
                                </div>
                                @elseif($driver->license_category)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $driver->license_category }}
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @livewire('admin.driver-status-badge-ultra-pro', ['driver' => $driver], key('status-'.$driver->id))
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                @if($driver->activeAssignment && $driver->activeAssignment->vehicle)
                                <div class="flex items-center gap-1.5">
                                    <x-iconify icon="lucide:car" class="w-3.5 h-3.5 text-blue-600" />
                                    <span class="font-medium text-gray-900">{{ $driver->activeAssignment->vehicle->registration_plate }}</span>
                                </div>
                                @else
                                <span class="text-gray-400 italic">Aucun véhicule</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if($driver->deleted_at)
                                    @can('drivers.restore')
                                    <button wire:click="confirmRestore({{ $driver->id }})"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-200 group"
                                        title="Restaurer">
                                        <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </button>
                                    @endcan
                                    @can('drivers.force-delete')
                                    <button wire:click="confirmForceDelete({{ $driver->id }})"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group"
                                        title="Supprimer définitivement">
                                        <x-iconify icon="lucide:trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </button>
                                    @endcan
                                    @else
                                    {{-- Actions directes --}}
                                    @can('drivers.view')
                                    <a href="{{ route('admin.drivers.show', $driver) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan
                                    @can('drivers.update')
                                    <a href="{{ route('admin.drivers.edit', $driver) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan

                                    {{-- Dropdown Menu (3 points) --}}
                                    <div class="relative"
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
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                            <x-iconify icon="lucide:more-vertical" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </button>

                                        <template x-teleport="body">
                                            <div x-show="open"
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
                                                class="fixed z-[80] rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                style="display: none;">
                                                <div class="py-1">
                                                    @can('drivers.export')
                                                    <button wire:click="exportPdf({{ $driver->id }}); close()" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <x-iconify icon="lucide:file-text" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500" />
                                                        Exporter PDF
                                                    </button>
                                                    @endcan
                                                    @can('drivers.delete')
                                                    <div class="border-t border-gray-100 my-1"></div>
                                                    <button type="button" @click="close(); $wire.confirmArchive({{ $driver->id }})" class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                        <x-iconify icon="lucide:archive" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500" />
                                                        Archiver
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="lucide:users" class="w-16 h-16 text-gray-300 mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
                                    <p class="text-sm text-gray-500 mb-4">Essayez de modifier vos filtres ou ajoutez un nouveau chauffeur.</p>
                                    @can('drivers.create')
                                    <a href="{{ route('admin.drivers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                                        Ajouter un chauffeur
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination (Gray Area) --}}
        <div class="mt-4">
            <x-pagination :paginator="$drivers" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>
    </div>

    {{-- ===============================================
        MODALS (Archive, Restore, Force Delete)
    =============================================== --}}

    {{-- Archive Modal --}}
    @if($showArchiveModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelArchive"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:archive" class="w-6 h-6 text-orange-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Archiver le chauffeur</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir archiver le chauffeur
                                <span class="font-bold text-gray-900">{{ $this->confirmingDriver?->full_name }}</span>
                                (<span class="font-medium">#{{ $this->confirmingDriver?->employee_number }}</span>) ?
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Il ne sera plus visible dans la liste active, mais pourra être restauré ultérieurement.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="archiveDriver" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Archiver
                    </button>
                    <button wire:click="cancelArchive" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Archive Modal --}}
    @if($showBulkArchiveModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkArchive"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:archive" class="w-6 h-6 text-orange-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Archiver des chauffeurs</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir archiver <span class="font-bold text-gray-900">{{ count($selectedDrivers) }}</span> chauffeur(s) ?
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Les chauffeurs archivés ne seront plus visibles dans la liste active, mais pourront être restaurés.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="bulkArchive" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Archiver
                    </button>
                    <button wire:click="cancelBulkArchive" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Restore Modal --}}
    @if($showRestoreModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelRestore"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:rotate-ccw" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Restaurer le chauffeur</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir restaurer le chauffeur
                                <span class="font-bold text-gray-900">{{ $this->confirmingDriver?->full_name }}</span>
                                (<span class="font-medium">#{{ $this->confirmingDriver?->employee_number }}</span>) ?
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Il réapparaîtra dans la liste active et pourra être affecté à nouveau.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="restoreDriver" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Restaurer
                    </button>
                    <button wire:click="cancelRestore" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Restore Modal --}}
    @if($showBulkRestoreModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkRestore"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:rotate-ccw" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Restaurer des chauffeurs</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir restaurer <span class="font-bold text-gray-900">{{ count($selectedDrivers) }}</span> chauffeur(s) ?
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Les chauffeurs restaurés réapparaîtront dans la liste active.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="bulkRestore" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Restaurer
                    </button>
                    <button wire:click="cancelBulkRestore" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Force Delete Modal --}}
    @if($showForceDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelForceDelete"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:trash-2" class="w-6 h-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Suppression définitive</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer définitivement le chauffeur
                                <span class="font-bold text-gray-900">{{ $this->confirmingDriver?->full_name }}</span>
                                (<span class="font-medium">#{{ $this->confirmingDriver?->employee_number }}</span>) ?
                            </p>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Tapez <span class="font-semibold text-red-600">SUPPRIMER</span> pour confirmer</label>
                                <input
                                    type="text"
                                    wire:model.live="forceDeleteConfirm"
                                    placeholder="SUPPRIMER"
                                    class="mt-2 w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm" />
                            </div>
                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="lucide:alert-triangle" class="h-5 w-5 text-red-400" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Attention : Action irréversible</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul role="list" class="list-disc pl-5 space-y-1">
                                                <li>Toutes les données du chauffeur seront effacées.</li>
                                                <li>L'historique des affectations sera détaché.</li>
                                                <li>Cette action ne peut pas être annulée.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($this->confirmingDriverNonDriverRoles->count() > 0)
                            <div class="mt-3 bg-amber-50 border border-amber-200 rounded-md p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="lucide:alert-circle" class="h-5 w-5 text-amber-500" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-amber-800">Compte utilisateur conservé</h3>
                                        <p class="mt-1 text-sm text-amber-700">
                                            Le compte utilisateur ne sera pas supprimé car il possède d'autres rôles :
                                            <span class="font-semibold">{{ $this->confirmingDriverNonDriverRoles->implode(', ') }}</span>.
                                            Si nécessaire, supprimez le compte depuis la gestion des utilisateurs.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button wire:click="forceDeleteDriver" type="button"
                        {{ strtoupper(trim($forceDeleteConfirm)) !== 'SUPPRIMER' ? 'disabled' : '' }}
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm {{ strtoupper(trim($forceDeleteConfirm)) !== 'SUPPRIMER' ? 'bg-red-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700' }}">
                        Supprimer
                    </button>
                    <button wire:click="cancelForceDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Force Delete Modal --}}
    @if($showBulkForceDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkForceDelete"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:trash-2" class="w-6 h-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Suppression définitive</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer définitivement <span class="font-bold text-gray-900">{{ count($selectedDrivers) }}</span> chauffeur(s) ?
                            </p>
                            @if($this->selectedDriversPreview->count() > 0)
                            <div class="mt-3 rounded-md border border-gray-200 bg-gray-50 p-3">
                                <p class="text-xs font-semibold uppercase text-gray-600 mb-2">Résumé des chauffeurs</p>
                                <ul class="space-y-1 text-sm text-gray-700">
                                    @foreach($this->selectedDriversPreview as $previewDriver)
                                    <li class="flex items-center justify-between gap-3">
                                        <span class="font-medium">{{ $previewDriver->full_name }}</span>
                                        <span class="text-xs text-gray-500">#{{ $previewDriver->employee_number ?? $previewDriver->id }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                                @if(count($selectedDrivers) > $this->selectedDriversPreview->count())
                                <p class="mt-2 text-xs text-gray-500">
                                    + {{ count($selectedDrivers) - $this->selectedDriversPreview->count() }} autres chauffeurs
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
                            <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="lucide:alert-triangle" class="h-5 w-5 text-red-400" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Attention : Action irréversible</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul role="list" class="list-disc pl-5 space-y-1">
                                                <li>Toutes les données du chauffeur seront effacées.</li>
                                                <li>L'historique des affectations sera détaché.</li>
                                                <li>Cette action ne peut pas être annulée.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button
                        wire:click="bulkForceDelete"
                        type="button"
                        {{ strtoupper(trim($bulkForceDeleteConfirm)) !== 'SUPPRIMER' ? 'disabled' : '' }}
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm {{ strtoupper(trim($bulkForceDeleteConfirm)) !== 'SUPPRIMER' ? 'bg-red-300 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700' }}">
                        Supprimer
                    </button>
                    <button wire:click="cancelBulkForceDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Status Change Modal --}}
    @if($showBulkStatusModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="bulk-status-modal">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelBulkStatusChange"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6 z-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:refresh-cw" class="w-5 h-5 text-amber-600" />
                        Changer le statut des chauffeurs
                    </h3>
                    <button wire:click="cancelBulkStatusChange" class="text-gray-400 hover:text-gray-500">
                        <x-iconify icon="lucide:x" class="w-5 h-5" />
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionner un statut</label>
                    <select wire:model="bulkStatusId" class="block w-full border-gray-300 rounded-lg text-sm">
                        <option value="">-- Choisir un statut --</option>
                        @foreach($driverStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('bulkStatusId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 justify-end">
                    <button wire:click="cancelBulkStatusChange"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Annuler
                    </button>
                    <button wire:click="bulkChangeStatus"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 inline-flex items-center gap-2">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 animate-spin" wire:loading wire:target="bulkChangeStatus" />
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
