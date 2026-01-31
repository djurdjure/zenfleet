<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:wrench" class="w-6 h-6 text-blue-600" />
                Gestion de la Maintenance
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $operations->total() }})</span>
            </h1>

            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,status,maintenanceTypeId,providerId,vehicleId,category,dateFrom,dateTo,overdue,perPage">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total opérations</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Planifiées</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $analytics['planned_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-clock" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En cours</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['in_progress_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:loader" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En retard</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $analytics['overdue_operations'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:alert-circle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

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

        <x-page-search-bar x-data="{ showFilters: false }">
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
                    @if($activeCount > 0)
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
            </x-slot:filters>

            <x-slot:actions>
                <a href="{{ route('admin.maintenance.operations.create') }}"
                    title="Nouvelle Maintenance"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="status" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les statuts</option>
                            @foreach(\App\Models\MaintenanceOperation::STATUSES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type de maintenance</label>
                        <select wire:model.live="maintenanceTypeId" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les types</option>
                            @foreach($maintenanceTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Véhicule</label>
                        <select wire:model.live="vehicleId" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fournisseur</label>
                        <select wire:model.live="providerId" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}">{{ $provider->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model.live="category" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes catégories</option>
                            <option value="preventive">Préventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="inspection">Inspection</option>
                            <option value="revision">Révision</option>
                            <option value="emergency">Urgence</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de début</label>
                        <input type="date" wire:model.live="dateFrom" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date de fin</label>
                        <input type="date" wire:model.live="dateTo" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Retard</label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model.live="overdue" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-iconify icon="lucide:car" class="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $operation->vehicle->registration_plate }}</div>
                                            <div class="text-xs text-gray-500">{{ $operation->vehicle->brand }} {{ $operation->vehicle->model }}</div>
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

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.maintenance.operations.show', $operation) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                            <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                        </a>

                                        @can('update', $operation)
                                            <a href="{{ route('admin.maintenance.operations.edit', $operation) }}" 
                                               class="text-gray-600 hover:text-gray-900" title="Modifier">
                                                <x-iconify icon="lucide:pencil" class="w-5 h-5" />
                                            </a>
                                        @endcan

                                        @if($operation->status === 'planned')
                                            <button 
                                                wire:click="$dispatch('start-operation', { id: {{ $operation->id }} })"
                                                class="text-green-600 hover:text-green-900" 
                                                title="Démarrer">
                                                <x-iconify icon="lucide:play" class="w-5 h-5" />
                                            </button>
                                        @endif

                                        @can('delete', $operation)
                                            <button 
                                                wire:click="$dispatch('confirm-delete', { id: {{ $operation->id }} })"
                                                class="text-red-600 hover:text-red-900" 
                                                title="Supprimer">
                                                <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                                            </button>
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
                                            <a href="{{ route('admin.maintenance.operations.create') }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                                <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                                Nouvelle Maintenance
                                            </a>
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

            <div class="mt-4">
                <x-pagination :paginator="$operations" :records-per-page="$perPage" wire:model.live="perPage" />
            </div>

            <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                <div class="flex items-center gap-2 text-blue-600">
                    <x-iconify icon="lucide:loader" class="w-5 h-5 animate-spin" />
                    <span class="text-sm font-medium">Chargement...</span>
                </div>
            </div>
        </div>
    </div>
</div>
