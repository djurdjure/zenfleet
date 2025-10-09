<div>
    {{-- HEADER SECTION --}}
    <div class="bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Demandes de Réparation
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Gérez les demandes de réparation de votre flotte
                    </p>
                </div>

                {{-- BOUTON NOUVELLE DEMANDE --}}
                @can('create', App\Models\RepairRequest::class)
                <button
                    onclick="window.location.href='{{ route('admin.repair-requests.create') }}'"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nouvelle Demande
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 pb-8 space-y-6">
        {{-- STATISTIQUES --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4">
            {{-- Total --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Total</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
                    </div>
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- En attente --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-yellow-600">En attente</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['pending'] }}</p>
                    </div>
                    <div class="p-2 bg-yellow-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Approuvées --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-green-600">Approuvées</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['approved'] }}</p>
                    </div>
                    <div class="p-2 bg-green-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Rejetées --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-600">Rejetées</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['rejected'] }}</p>
                    </div>
                    <div class="p-2 bg-red-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Critiques --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-red-600">Critiques</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['critical'] }}</p>
                    </div>
                    <div class="p-2 bg-red-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Urgentes --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-orange-600">Urgentes</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['high'] }}</p>
                    </div>
                    <div class="p-2 bg-orange-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Aujourd'hui --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-blue-600">Aujourd'hui</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['today'] }}</p>
                    </div>
                    <div class="p-2 bg-blue-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Cette semaine --}}
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-indigo-600">Cette semaine</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $statistics['week'] }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100/20 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION FILTRES --}}
        <div class="bg-white rounded-xl shadow-sm" x-data="{ showFilters: @entangle('showFilters') }">
            {{-- Header des filtres --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        {{-- Barre de recherche --}}
                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Rechercher..."
                                class="w-96 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        {{-- Bouton filtres avancés --}}
                        <button
                            @click="showFilters = !showFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtres
                            <span x-show="showFilters" class="ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </span>
                            <span x-show="!showFilters" class="ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>

                        {{-- Badge nombre de filtres actifs --}}
                        @if($search || $statusFilter || $urgencyFilter || $categoryFilter || $vehicleFilter || $driverFilter || $dateFrom || $dateTo)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ collect([$search, $statusFilter, $urgencyFilter, $categoryFilter, $vehicleFilter, $driverFilter, $dateFrom, $dateTo])->filter()->count() }} filtre(s) actif(s)
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        {{-- Actions groupées --}}
                        @if(count($selectedRequests) > 0)
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">
                                {{ count($selectedRequests) }} sélectionné(s)
                            </span>
                            
                            @can('approve', App\Models\RepairRequest::class)
                            <button
                                wire:click="applyBulkAction('approve')"
                                class="inline-flex items-center px-3 py-1.5 border border-green-300 rounded-md text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                            >
                                Approuver
                            </button>
                            @endcan
                            
                            @can('reject', App\Models\RepairRequest::class)
                            <button
                                wire:click="applyBulkAction('reject')"
                                class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                Rejeter
                            </button>
                            @endcan
                        </div>
                        @endif

                        {{-- Export --}}
                        <div class="relative" x-data="{ open: false }">
                            <button
                                @click="open = !open"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Export
                            </button>
                            
                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-transition
                                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                            >
                                <div class="py-1">
                                    <button wire:click="exportData('csv')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Export CSV
                                    </button>
                                    <button wire:click="exportData('excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Export Excel
                                    </button>
                                    <button wire:click="exportData('pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Reset filtres --}}
                        @if($search || $statusFilter || $urgencyFilter || $categoryFilter || $vehicleFilter || $driverFilter || $dateFrom || $dateTo)
                        <button
                            wire:click="resetFilters"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Panneau de filtres avancés --}}
            <div
                x-show="showFilters"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="px-6 py-4 bg-gray-50 border-b border-gray-200"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Statut --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select
                            wire:model.live="statusFilter"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $value => $config)
                                <option value="{{ $value }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Urgence --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urgence</label>
                        <select
                            wire:model.live="urgencyFilter"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Toutes les urgences</option>
                            @foreach($urgencyLevels as $value => $config)
                                <option value="{{ $value }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Catégorie --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select
                            wire:model.live="categoryFilter"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Véhicule --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Véhicule</label>
                        <select
                            wire:model.live="vehicleFilter"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date début --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                        <input
                            type="date"
                            wire:model.live="dateFrom"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Date fin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                        <input
                            type="date"
                            wire:model.live="dateTo"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    {{-- Nombre par page --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Afficher</label>
                        <select
                            wire:model.live="perPage"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="10">10 par page</option>
                            <option value="20">20 par page</option>
                            <option value="50">50 par page</option>
                            <option value="100">100 par page</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES DEMANDES --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Checkbox --}}
                            <th scope="col" class="px-6 py-3 text-left">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAll"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                            </th>
                            
                            {{-- ID --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                wire:click="sortBy('uuid')">
                                <div class="flex items-center space-x-1">
                                    <span>ID</span>
                                    @if($sortField === 'uuid')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            {{-- Date --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                wire:click="sortBy('created_at')">
                                <div class="flex items-center space-x-1">
                                    <span>Date</span>
                                    @if($sortField === 'created_at')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            {{-- Demandeur --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Demandeur
                            </th>

                            {{-- Véhicule --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Véhicule
                            </th>

                            {{-- Description --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>

                            {{-- Type/Catégorie --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>

                            {{-- Urgence --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                wire:click="sortBy('urgency')">
                                <div class="flex items-center space-x-1">
                                    <span>Urgence</span>
                                    @if($sortField === 'urgency')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            {{-- Statut --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>Statut</span>
                                    @if($sortField === 'status')
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                        </svg>
                                    @endif
                                </div>
                            </th>

                            {{-- Actions --}}
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($repairRequests as $request)
                            @php
                                $rowColor = match($request->status) {
                                    'pending_supervisor' => 'bg-yellow-50/10',
                                    'approved_supervisor' => 'bg-blue-50/10',
                                    'pending_fleet_manager' => 'bg-orange-50/10',
                                    'approved_final' => 'bg-green-50/10',
                                    'rejected_supervisor', 'rejected_final' => 'bg-red-50/10',
                                    default => ''
                                };
                            @endphp
                            <tr class="{{ $rowColor }} hover:bg-gray-50/50 transition-colors">
                                {{-- Checkbox --}}
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        value="{{ $request->id }}"
                                        wire:model.live="selectedRequests"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                </td>

                                {{-- ID --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono text-gray-900">
                                        #{{ substr($request->uuid, 0, 8) }}
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $request->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $request->created_at->format('H:i') }}
                                    </div>
                                </td>

                                {{-- Demandeur --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600">
                                                    {{ substr($request->driver->user->name ?? 'NA', 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $request->driver->user->name ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $request->driver->license_number ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Véhicule --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $request->vehicle->registration_plate ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $request->vehicle->brand ?? '' }} {{ $request->vehicle->model ?? '' }}
                                    </div>
                                </td>

                                {{-- Description --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($request->title, 30) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ Str::limit($request->description, 40) }}
                                    </div>
                                </td>

                                {{-- Type/Catégorie --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $request->category->name }}
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-500">-</span>
                                    @endif
                                </td>

                                {{-- Urgence --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $urgencyConfig = $urgencyLevels[$request->urgency] ?? $urgencyLevels['normal'];
                                        $colorClass = match($urgencyConfig['color']) {
                                            'green' => 'bg-green-100 text-green-800',
                                            'blue' => 'bg-blue-100 text-blue-800',
                                            'orange' => 'bg-orange-100 text-orange-800',
                                            'red' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            @if($urgencyConfig['icon'] === 'arrow-down')
                                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L10 15.586l5.293-5.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            @elseif($urgencyConfig['icon'] === 'arrow-up')
                                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L10 4.414l-5.293 5.293a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            @elseif($urgencyConfig['icon'] === 'alert-triangle')
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            @else
                                                <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                            @endif
                                        </svg>
                                        {{ $urgencyConfig['label'] }}
                                    </span>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = $statuses[$request->status] ?? ['label' => 'Inconnu', 'color' => 'gray'];
                                        $statusColorClass = match($statusConfig['color']) {
                                            'yellow' => 'bg-yellow-100 text-yellow-800',
                                            'blue' => 'bg-blue-100 text-blue-800',
                                            'orange' => 'bg-orange-100 text-orange-800',
                                            'green' => 'bg-green-100 text-green-800',
                                            'red' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColorClass }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        {{-- Voir --}}
                                        <a
                                            href="{{ route('admin.repair-requests.show', $request) }}"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Voir les détails"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        {{-- Actions conditionnelles selon permissions --}}
                                        @can('approve', $request)
                                        <button
                                            wire:click="$dispatch('approve-request', { requestId: {{ $request->id }} })"
                                            class="text-green-600 hover:text-green-900"
                                            title="Approuver"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        @endcan

                                        @can('reject', $request)
                                        <button
                                            wire:click="$dispatch('reject-request', { requestId: {{ $request->id }} })"
                                            class="text-red-600 hover:text-red-900"
                                            title="Rejeter"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        @endcan

                                        @can('edit', $request)
                                        <a
                                            href="{{ route('admin.repair-requests.edit', $request) }}"
                                            class="text-gray-600 hover:text-gray-900"
                                            title="Modifier"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-gray-500 text-sm">Aucune demande de réparation trouvée</p>
                                        @if($search || $statusFilter || $urgencyFilter || $categoryFilter)
                                        <p class="text-gray-400 text-xs mt-2">Essayez de modifier vos filtres</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($repairRequests->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $repairRequests->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- LOADING OVERLAY --}}
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700">Chargement...</span>
        </div>
    </div>
</div>
