<div class="min-h-screen bg-[#f8fafc]">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-start">
            <div>
                <h1 class="text-xl font-bold text-gray-600">
                    Gestion de la Maintenance
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $operations->total() }})</span>
                </h1>
                <p class="mt-1 text-xs text-gray-600">Pilotage opérationnel des interventions maintenance</p>
            </div>

            <div
                class="flex items-center gap-2 text-[#0c90ee] opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,status,maintenanceTypeId,providerId,vehicleId,category,dateFrom,dateTo,overdue,perPage">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total opérations</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Planifiées</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $analytics['planned_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-clock" class="w-6 h-6 text-indigo-600" />
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En cours</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['in_progress_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:loader" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En retard</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $analytics['overdue_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:alert-circle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        <x-page-analytics-grid columns="4" class="mt-4">
            <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Complétées</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['completed_operations'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Taux: {{ number_format((($analytics['completed_operations'] ?? 0) / max(($analytics['total_operations'] ?? 1), 1)) * 100, 1) }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle-2" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Coût total</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($analytics['total_cost'] ?? 0, 0, ',', ' ') }} DA</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Moyen: {{ number_format($analytics['avg_cost'] ?? 0, 0, ',', ' ') }} DA
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:banknote" class="w-6 h-6 text-indigo-600" />
                    </div>
                </div>
            </div>

            <div class="bg-cyan-50 rounded-lg border border-cyan-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Durée moyenne</p>
                        <p class="text-2xl font-bold text-cyan-700 mt-1">{{ number_format($analytics['avg_duration_minutes'] ?? 0, 0) }} min</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Total: {{ number_format($analytics['total_duration_hours'] ?? 0, 1) }} h
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 border border-cyan-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:clock-3" class="w-6 h-6 text-cyan-700" />
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Annulées</p>
                        <p class="text-2xl font-bold text-gray-600 mt-1">{{ $analytics['cancelled_operations'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Taux: {{ number_format((($analytics['cancelled_operations'] ?? 0) / max(($analytics['total_operations'] ?? 1), 1)) * 100, 1) }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:x-circle" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 mb-6">
            <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-lg border border-red-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Véhicules à surveiller</p>
                        <p class="text-sm text-red-700 mt-0.5">Plus de maintenances</p>
                    </div>
                    <div class="w-10 h-10 bg-red-200 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:trending-up" class="w-5 h-5 text-red-700" />
                    </div>
                </div>
                @if(isset($analytics['top_vehicles']) && $analytics['top_vehicles']->count() > 0)
                    <ul class="space-y-2">
                        @foreach($analytics['top_vehicles']->take(5) as $item)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-red-900 font-medium truncate">{{ $item->vehicle?->registration_plate ?? 'Véhicule indisponible' }}</span>
                                <span class="text-red-700 font-bold">{{ $item->count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-red-700">Aucune donnée disponible</p>
                @endif
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Types fréquents</p>
                        <p class="text-sm text-blue-700 mt-0.5">Maintenances courantes</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-200 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:bar-chart-3" class="w-5 h-5 text-blue-700" />
                    </div>
                </div>
                @if(isset($analytics['top_types']) && $analytics['top_types']->count() > 0)
                    <ul class="space-y-2">
                        @foreach($analytics['top_types']->take(5) as $item)
                            <li class="flex items-center justify-between text-sm">
                                <span class="text-blue-900 font-medium truncate">{{ $item->maintenanceType?->name ?? 'Type indisponible' }}</span>
                                <span class="text-blue-700 font-bold">{{ $item->count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-blue-700">Aucune donnée disponible</p>
                @endif
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg border border-yellow-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-semibold text-yellow-700 uppercase tracking-wide">Alertes & prévisions</p>
                        <p class="text-sm text-yellow-800 mt-0.5">Fenêtre 7 jours</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-200 border border-yellow-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:bell" class="w-5 h-5 text-yellow-700" />
                    </div>
                </div>
                <ul class="space-y-2">
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">Prochains 7 jours</span>
                        <span class="text-yellow-700 font-bold">{{ $analytics['upcoming_count'] ?? 0 }}</span>
                    </li>
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">En retard</span>
                        <span class="text-red-700 font-bold">{{ $analytics['overdue_operations'] ?? 0 }}</span>
                    </li>
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-yellow-900 font-medium">Planifié</span>
                        <span class="text-yellow-700 font-bold">{{ number_format($analytics['cost_planned'] ?? 0, 0, ',', ' ') }} DA</span>
                    </li>
                </ul>
            </div>
        </div>

        @php
            $activeFilters = collect([
                $search,
                $status,
                $maintenanceTypeId,
                $providerId,
                $vehicleId,
                $category,
                $dateFrom,
                $dateTo,
                $overdue ? '1' : '',
            ])->filter(fn($value) => $value !== '' && $value !== null);
            $activeCount = $activeFilters->count();
        @endphp

        <x-page-search-bar x-data="{ showFilters: false }" data-maintenance-filters-root>
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Rechercher par véhicule, type, fournisseur..."
                        wire:loading.attr="aria-busy"
                        wire:target="search"
                        class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] text-sm">
                    <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-[#0c90ee] animate-spin" />
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
                    @if($activeCount > 0)
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
            </x-slot:filters>

            <x-slot:actions>
                <div class="flex items-center gap-2">
                    <div class="inline-flex rounded-lg bg-gray-100 p-1 border border-gray-200">
                        <span class="px-3 py-1.5 text-xs font-semibold text-white bg-[#0c90ee] rounded-md inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:list" class="w-3.5 h-3.5" />
                            Liste
                        </span>
                        <a href="{{ route('admin.maintenance.operations.kanban') }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-600 rounded-md hover:bg-white transition-colors duration-200 inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:columns-3" class="w-3.5 h-3.5" />
                            Kanban
                        </a>
                        <a href="{{ route('admin.maintenance.operations.calendar') }}"
                           class="px-3 py-1.5 text-xs font-medium text-gray-600 rounded-md hover:bg-white transition-colors duration-200 inline-flex items-center gap-1.5">
                            <x-iconify icon="lucide:calendar-days" class="w-3.5 h-3.5" />
                            Calendrier
                        </a>
                    </div>

                    <div class="relative" x-data="{ exportOpen: false }">
                        @php
                            $exportParams = [
                                'search' => $search ?: null,
                                'status' => $status ?: null,
                                'maintenance_type_id' => $maintenanceTypeId ?: null,
                                'provider_id' => $providerId ?: null,
                                'vehicle_id' => $vehicleId ?: null,
                                'category' => $category ?: null,
                                'date_from' => $dateFrom ?: null,
                                'date_to' => $dateTo ?: null,
                                'overdue' => $overdue ? 1 : null,
                                'sort' => $sortField ?: null,
                                'direction' => $sortDirection ?: null,
                            ];
                        @endphp
                        <button
                            @click="exportOpen = !exportOpen"
                            @click.away="exportOpen = false"
                            type="button"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:download" class="w-4 h-4 text-gray-500" />
                            <span class="text-sm font-medium text-gray-700">Exporter</span>
                            <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                        </button>

                        <div
                            x-show="exportOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 z-20 mt-2 w-44 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                            style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('admin.maintenance.operations.export', $exportParams) }}"
                                   class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-spreadsheet" class="w-4 h-4 text-green-600" />
                                    Export CSV
                                </a>
                                <a href="{{ route('admin.maintenance.operations.export.pdf', $exportParams) }}"
                                   class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-text" class="w-4 h-4 text-red-600" />
                                    Export PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    @can('create', App\Models\MaintenanceOperation::class)
                    <a href="{{ route('admin.maintenance.operations.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-[#0c90ee] bg-[#0c90ee] text-white text-sm font-semibold rounded-lg hover:bg-[#0a7fd1] hover:border-[#0a7fd1] transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                        Nouvelle opération
                    </a>
                    @endcan
                </div>
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Statut</label>
                        <x-slim-select wire:model.live="status" name="status" placeholder="Tous les statuts">
                            <option value="" data-placeholder="true">Tous les statuts</option>
                            @foreach(\App\Models\MaintenanceOperation::STATUSES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Type de maintenance</label>
                        <x-slim-select wire:model.live="maintenanceTypeId" name="maintenance_type_id" placeholder="Tous les types">
                            <option value="" data-placeholder="true">Tous les types</option>
                            @foreach($maintenanceTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Véhicule</label>
                        <x-slim-select wire:model.live="vehicleId" name="vehicle_id" placeholder="Tous les véhicules">
                            <option value="" data-placeholder="true">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Fournisseur</label>
                        <x-slim-select wire:model.live="providerId" name="provider_id" placeholder="Tous les fournisseurs">
                            <option value="" data-placeholder="true">Tous les fournisseurs</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->company_name }}</option>
                            @endforeach
                        </x-slim-select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Catégorie</label>
                        <x-slim-select wire:model.live="category" name="category" placeholder="Toutes catégories">
                            <option value="" data-placeholder="true">Toutes catégories</option>
                            <option value="preventive">Préventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="inspection">Inspection</option>
                            <option value="revision">Révision</option>
                            <option value="emergency">Urgence</option>
                        </x-slim-select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Période planifiée</label>
                        <div date-rangepicker data-maintenance-date-range class="flex items-center gap-2">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-400" />
                                </div>
                                <input
                                    wire:model.live="dateFrom"
                                    data-range-start
                                    type="text"
                                    placeholder="Date début"
                                    autocomplete="off"
                                    class="block w-full bg-gray-50 border border-gray-300 rounded-lg pl-10 p-2.5 text-sm shadow-sm transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                            </div>

                            <span class="text-gray-500 text-sm font-medium">à</span>

                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-400" />
                                </div>
                                <input
                                    wire:model.live="dateTo"
                                    data-range-end
                                    type="text"
                                    placeholder="Date fin"
                                    autocomplete="off"
                                    class="block w-full bg-gray-50 border border-gray-300 rounded-lg pl-10 p-2.5 text-sm shadow-sm transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Retard</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="overdue" class="rounded border-gray-300 text-[#0c90ee] focus:ring-[#0c90ee]/20">
                            <span class="text-sm text-gray-700">Seulement en retard</span>
                        </label>
                    </div>

                    <x-slot:reset>
                        @if($activeCount > 0)
                        <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:list" class="w-4 h-4" />
                        Liste des Opérations
                        <span class="text-xs font-normal text-gray-500">({{ $operations->total() }} résultats)</span>
                    </h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('vehicle_id')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Véhicule
                                    @if($sortField === 'vehicle_id')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('maintenance_type_id')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Type
                                    @if($sortField === 'maintenance_type_id')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('scheduled_date')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Date planifiée
                                    @if($sortField === 'scheduled_date')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('status')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Statut
                                    @if($sortField === 'status')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fournisseur
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('total_cost')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Coût
                                    @if($sortField === 'total_cost')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($operations as $operation)
                            <tr class="hover:bg-gray-50 transition-colors duration-150" wire:key="operation-{{ $operation->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                                            <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $operation->vehicle?->registration_plate ?? 'Véhicule indisponible' }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($operation->vehicle)
                                                {{ $operation->vehicle->brand }} {{ $operation->vehicle->model }}
                                                @else
                                                Relation véhicule indisponible
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $operation->maintenanceType->getCategoryColor() }}"></div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $operation->maintenanceType->name }}</div>
                                            <div class="text-xs text-gray-500">{{ ucfirst($operation->maintenanceType->category ?? '') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $operation->scheduled_date?->format('d/m/Y') ?? 'Non définie' }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($operation->scheduled_date)
                                            {{ $operation->scheduled_date->diffForHumans() }}
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $operation->getStatusBadge() !!}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($operation->provider)
                                        <div class="text-sm text-gray-900">{{ $operation->provider->company_name ?? $operation->provider->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $operation->provider->contact_phone ?? '' }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">Non défini</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($operation->total_cost)
                                        <div class="text-sm font-semibold text-gray-900">{{ number_format($operation->total_cost, 0, ',', ' ') }} DA</div>
                                    @else
                                        <span class="text-sm text-gray-400">Non défini</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.maintenance.operations.show', $operation) }}"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                            title="Voir détails">
                                            <x-iconify icon="lucide:eye" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </a>

                                        @can('update', $operation)
                                            <a
                                                href="{{ route('admin.maintenance.operations.edit', $operation) }}"
                                                class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                                title="Modifier">
                                                <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                            </a>
                                        @endcan

                                        @if($operation->status === 'planned')
                                            @can('update', $operation)
                                                <form action="{{ route('admin.maintenance.operations.start', $operation) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-200 group"
                                                        title="Démarrer">
                                                        <x-iconify icon="lucide:play" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.maintenance.operations.cancel', $operation) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Confirmer l\\'annulation de cette opération ?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-all duration-200 group"
                                                        title="Annuler">
                                                        <x-iconify icon="lucide:x-circle" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif

                                        @if($operation->status === 'in_progress')
                                            @can('update', $operation)
                                                <form action="{{ route('admin.maintenance.operations.complete', $operation) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Confirmer la clôture de cette opération ?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="completed_date" value="{{ now()->toDateString() }}">
                                                    <button
                                                        type="submit"
                                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all duration-200 group"
                                                        title="Terminer">
                                                        <x-iconify icon="lucide:check-circle-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.maintenance.operations.cancel', $operation) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Confirmer l\\'annulation de cette opération ?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-all duration-200 group"
                                                        title="Annuler">
                                                        <x-iconify icon="lucide:x-circle" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif

                                        @can('delete', $operation)
                                            <form action="{{ route('admin.maintenance.operations.destroy', $operation) }}" method="POST" class="inline"
                                                onsubmit="return confirm('Confirmer la suppression ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group"
                                                    title="Supprimer">
                                                    <x-iconify icon="lucide:trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-iconify icon="lucide:inbox" class="w-12 h-12 text-gray-400 mb-3" />
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune opération trouvée</h3>
                                        <p class="text-sm text-gray-500 mb-4">
                                            @if($search || $status || $maintenanceTypeId)
                                                Essayez de modifier vos critères de recherche.
                                            @else
                                                Commencez par créer une nouvelle opération de maintenance.
                                            @endif
                                        </p>
                                        @if(!$search && !$status && !$maintenanceTypeId)
                                            @can('create', App\Models\MaintenanceOperation::class)
                                                <a href="{{ route('admin.maintenance.operations.create') }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 border border-[#0c90ee] bg-[#0c90ee] text-white text-sm font-semibold rounded-lg hover:bg-[#0a7fd1] hover:border-[#0a7fd1]">
                                                    <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                                    Nouvelle Maintenance
                                                </a>
                                            @endcan
                                        @else
                                            <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                                                <x-iconify icon="lucide:x" class="w-4 h-4" />
                                                Réinitialiser
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                <div class="flex items-center gap-2 text-[#0c90ee]">
                    <x-iconify icon="lucide:loader" class="w-5 h-5 animate-spin" />
                    <span class="text-sm font-medium">Chargement...</span>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$operations" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>
    </div>
</div>

@script
<script>
    (() => {
        const RANGE_SELECTOR = '[data-maintenance-date-range]';
        let rangePickerInstance = null;
        let rangePickerElement = null;

        const ROOT_SELECTOR = '[data-maintenance-filters-root]';
        const getRoot = () => document.querySelector(ROOT_SELECTOR) || document;
        const q = (selector) => getRoot().querySelector(selector);

        const syncLivewireInput = (el) => {
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const initFlowbiteRangePicker = () => {
            const rangeEl = q(RANGE_SELECTOR);
            if (!rangeEl) return;

            const startInput = rangeEl.querySelector('[data-range-start]');
            const endInput = rangeEl.querySelector('[data-range-end]');
            if (!startInput || !endInput) return;

            if (rangeEl._maintenanceRangePicker) {
                return;
            }

            const boot = () => {
                if (typeof window.DateRangePicker === 'undefined') {
                    setTimeout(boot, 120);
                    return;
                }

                if (rangePickerInstance && rangePickerElement && rangePickerElement !== rangeEl) {
                    try {
                        rangePickerInstance.destroy();
                    } catch (e) {
                        // no-op
                    }
                }

                const picker = new window.DateRangePicker(rangeEl, {
                    autohide: true,
                    format: 'dd/mm/yyyy',
                    language: 'fr',
                    todayBtn: true,
                    clearBtn: true,
                    todayBtnMode: 1
                });

                rangeEl._maintenanceRangePicker = picker;
                rangePickerInstance = picker;
                rangePickerElement = rangeEl;

                const syncToLivewire = () => {
                    syncLivewireInput(startInput);
                    syncLivewireInput(endInput);
                };

                startInput.addEventListener('changeDate', syncToLivewire);
                endInput.addEventListener('changeDate', syncToLivewire);
            };

            boot();
        };

        const initAll = () => {
            initFlowbiteRangePicker();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initAll();
            }, { once: true });
        } else {
            initAll();
        }

        if (!window.__maintenanceFiltersLivewireHooked) {
            window.__maintenanceFiltersLivewireHooked = true;
            document.addEventListener('livewire:initialized', () => {
                if (window.Livewire && typeof window.Livewire.hook === 'function') {
                    window.Livewire.hook('morph.updated', () => {
                        requestAnimationFrame(initAll);
                    });
                }
            });
        }
    })();
</script>
@endscript
