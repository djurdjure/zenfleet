<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                Gestion des Organisations
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $analytics['total'] ?? 0 }})</span>
            </h1>

            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,status,wilaya,type,perPage">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total organisations</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actives</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['active'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 border border-green-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg border border-purple-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Utilisateurs</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $analytics['users'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 border border-purple-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:users" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Véhicules</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['vehicles'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 border border-orange-200 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:car" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        @php
            $activeFilters = collect([
                $search,
                $status,
                $wilaya,
                $type,
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
                        placeholder="Rechercher par nom, NIF, ville..."
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
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
            </x-slot:filters>

            <x-slot:actions>
                <a href="{{ route('admin.organizations.export') }}"
                    title="Exporter"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                </a>
                <a href="{{ route('admin.organizations.create') }}"
                    title="Nouvelle Organisation"
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
                            @foreach($filterOptions['statuses'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Wilaya</label>
                        <select wire:model.live="wilaya" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les wilayas</option>
                            @foreach($filterOptions['wilayas'] as $code => $name)
                                <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <select wire:model.live="type" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les types</option>
                            @foreach($filterOptions['types'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-slot:reset>
                        @if($activeCount > 0)
                        <button wire:click="$set('search', ''); $set('status', ''); $set('wilaya', ''); $set('type', '')"
                            class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden relative">
            @if(count($selectedOrganizations) > 0)
                <div class="absolute top-0 left-0 right-0 z-10 bg-blue-50 p-2 flex items-center justify-between border-b border-blue-100 animate-fade-in-down">
                    <div class="flex items-center gap-3 px-4">
                        <span class="font-medium text-blue-900">{{ count($selectedOrganizations) }} sélectionnée(s)</span>
                        <button wire:click="$set('selectedOrganizations', [])" class="text-sm text-blue-600 hover:text-blue-800 underline">Annuler</button>
                    </div>
                    <div class="flex items-center gap-2 px-4">
                        @can('organizations.delete')
                            <button wire:click="bulkDelete"
                                onclick="confirm('Êtes-vous sûr de vouloir supprimer les organisations sélectionnées ? Cette action est irréversible.') || event.stopImmediatePropagation()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                <x-iconify icon="lucide:trash-2" class="w-4 h-4" /> Supprimer
                            </button>
                        @endcan
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('name')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Organisation
                                    @if($sortField === 'name')
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
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('users_count')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Utilisateurs
                                    @if($sortField === 'users_count')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('vehicles_count')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Véhicules
                                    @if($sortField === 'vehicles_count')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('drivers_count')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Chauffeurs
                                    @if($sortField === 'drivers_count')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left">
                                <button wire:click="sortBy('created_at')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase tracking-wider hover:text-gray-700">
                                    Création
                                    @if($sortField === 'created_at')
                                        <x-iconify icon="lucide:{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3 h-3" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($organizations as $org)
                        <tr class="hover:bg-gray-50 transition-colors" wire:key="org-{{ $org->id }}">
                            <td class="px-6 py-4">
                                <input type="checkbox" wire:model.live="selectedOrganizations" value="{{ $org->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($org->logo_path)
                                        <img src="{{ Storage::url($org->logo_path) }}" alt="{{ $org->name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($org->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.organizations.show', $org) }}" class="text-sm font-semibold text-gray-900 hover:text-blue-700">
                                            {{ $org->name }}
                                        </a>
                                        <div class="text-xs text-gray-500">
                                            {{ $org->city }}@if($org->wilaya), W{{ $org->wilaya }}@endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @can('organizations.update')
                                    <button wire:click="toggleStatus({{ $org->id }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ match($org->status) {
                                            'active' => 'bg-green-50 text-green-700 border border-green-200',
                                            'pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                            'inactive' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                            'suspended' => 'bg-red-50 text-red-700 border border-red-200',
                                            default => 'bg-gray-50 text-gray-700 border border-gray-200'
                                        } }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ match($org->status) {
                                            'active' => 'bg-green-500',
                                            'pending' => 'bg-yellow-500',
                                            'inactive' => 'bg-gray-400',
                                            'suspended' => 'bg-red-500',
                                            default => 'bg-gray-400'
                                        } }}"></span>
                                        @switch($org->status)
                                            @case('active') Actif @break
                                            @case('pending') Attente @break
                                            @case('inactive') Inactif @break
                                            @case('suspended') Suspendu @break
                                            @default {{ ucfirst($org->status) }}
                                        @endswitch
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ match($org->status) {
                                        'active' => 'bg-green-50 text-green-700 border border-green-200',
                                        'pending' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                        'inactive' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                        'suspended' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-50 text-gray-700 border border-gray-200'
                                    } }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ match($org->status) {
                                            'active' => 'bg-green-500',
                                            'pending' => 'bg-yellow-500',
                                            'inactive' => 'bg-gray-400',
                                            'suspended' => 'bg-red-500',
                                            default => 'bg-gray-400'
                                        } }}"></span>
                                        @switch($org->status)
                                            @case('active') Actif @break
                                            @case('pending') Attente @break
                                            @case('inactive') Inactif @break
                                            @case('suspended') Suspendu @break
                                            @default {{ ucfirst($org->status) }}
                                        @endswitch
                                    </span>
                                @endcan
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($org->users_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($org->vehicles_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($org->drivers_count) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ $org->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $org->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.organizations.show', $org) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @can('organizations.update')
                                        <a href="{{ route('admin.organizations.edit', $org) }}"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                            title="Modifier">
                                            <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </a>
                                    @endcan
                                    @can('organizations.delete')
                                        <button type="button"
                                            wire:click="confirmDelete({{ $org->id }})"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group"
                                            title="Supprimer">
                                            <x-iconify icon="lucide:trash-2" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="lucide:building-2" class="w-12 h-12 text-gray-400 mb-3" />
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune organisation trouvée</h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        @if($search || $status || $wilaya || $type)
                                            Essayez de modifier vos critères de recherche.
                                        @else
                                            Commencez par créer votre première organisation.
                                        @endif
                                    </p>
                                    @if($search || $status || $wilaya || $type)
                                        <button wire:click="$set('search', ''); $set('status', ''); $set('wilaya', ''); $set('type', '')"
                                            class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                                            Réinitialiser
                                        </button>
                                    @else
                                        <a href="{{ route('admin.organizations.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                                            <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                            Créer une organisation
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
                <div class="flex items-center gap-2 text-blue-600">
                    <x-iconify icon="lucide:loader" class="w-5 h-5 animate-spin" />
                    <span class="text-sm font-medium">Chargement...</span>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$organizations" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>

        @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="relative z-50 inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="lucide:trash-2" class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Suppression définitive</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer définitivement l'organisation
                                    <span class="font-bold text-gray-900">{{ $this->deletingOrganization?->name }}</span> ?
                                </p>
                                <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <x-iconify icon="lucide:alert-triangle" class="h-5 w-5 text-red-400" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Attention : Action irréversible</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul role="list" class="list-disc pl-5 space-y-1">
                                                    <li>Tous les utilisateurs liés seront désactivés.</li>
                                                    <li>Les données de flotte associées seront supprimées.</li>
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
                        <button wire:click="deleteOrganization" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Supprimer
                        </button>
                        <button wire:click="cancelDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
