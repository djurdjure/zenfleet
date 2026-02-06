<div>
    {{-- Header avec titre et bouton d'ajout --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                {{-- Titre --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <x-iconify icon="heroicons:exclamation-triangle" class="w-8 h-8 text-red-600" />
                        Sanctions Chauffeurs
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Gestion des sanctions disciplinaires appliquées aux chauffeurs
                    </p>
                </div>

                {{-- Bouton Ajouter --}}
                @can('create', App\Models\DriverSanction::class)
                <button
                    wire:click="create"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <x-iconify icon="heroicons:plus-circle" class="w-5 h-5 mr-2" />
                    Ajouter une sanction
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        {{-- Messages flash --}}
        @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex">
                <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-400 mt-0.5" />
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex">
                <x-iconify icon="heroicons:exclamation-circle" class="w-5 h-5 text-red-400 mt-0.5" />
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- ===============================================
            ANALYTICS CARDS (Unified)
        =============================================== --}}
        <x-page-analytics-grid class="mb-6" columns="4">
            {{-- Total Sanctions --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Sanctions</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->sanctions->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:scale" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>

            {{-- Actives --}}
            <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Sanctions Actives</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ \App\Models\DriverSanction::whereNull('archived_at')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:exclamation-circle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Archivées --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Archivées</p>
                        <p class="text-2xl font-bold text-gray-500 mt-1">
                            {{ \App\Models\DriverSanction::whereNotNull('archived_at')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 border border-gray-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:archive-box" class="w-6 h-6 text-gray-500" />
                    </div>
                </div>
            </div>

            {{-- Cette semaine --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Cette semaine</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">
                            {{ \App\Models\DriverSanction::whereBetween('sanction_date', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:calendar" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        {{-- ===============================================
            SEARCH & FILTERS (Unified)
        =============================================== --}}
        <x-page-search-bar x-data="{ showFilters: @entangle('showFilters') }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        wire:model.live.debounce.500ms="search"
                        type="text"
                        placeholder="Rechercher..."
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
                    wire:click="toggleFilters"
                    type="button"
                    title="Filtres"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md relative">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />
                    @if($filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo || $filterArchived !== 'active')
                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-600 text-white text-[10px] font-bold items-center justify-center">
                            !
                        </span>
                    </span>
                    @endif
                </button>
            </x-slot:filters>

            <x-slot:actions>
                @can('create', \App\Models\DriverSanction::class)
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm hover:shadow transition-all"
                    title="Nouvelle Sanction">
                    <x-iconify icon="heroicons:plus" class="h-5 w-5" />
                </button>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="5">
                    {{-- Type --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select wire:model.live="filterSanctionType" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="">Tous</option>
                            @foreach($this->sanctionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chauffeur --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Chauffeur</label>
                        {{-- Note: Converting Simple Select to something consistent --}}
                        <select wire:model.live="filterDriverId" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="">Tous</option>
                            @foreach($this->drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Début --}}
                    <div>
                        <x-datepicker
                            name="filterDateFrom"
                            label="Du"
                            :value="$filterDateFrom"
                            placeholder="JJ/MM/AAAA"
                            x-on:input="$wire.set('filterDateFrom', $event.detail)" />
                    </div>

                    {{-- Date Fin --}}
                    <div>
                        <x-datepicker
                            name="filterDateTo"
                            label="Au"
                            :value="$filterDateTo"
                            placeholder="JJ/MM/AAAA"
                            x-on:input="$wire.set('filterDateTo', $event.detail)" />
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                        <select wire:model.live="filterArchived" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-xs sm:leading-6">
                            <option value="active">Actifs</option>
                            <option value="archived">Archivés</option>
                            <option value="all">Tous</option>
                        </select>
                    </div>

                    <x-slot:reset>
                        <div class="flex items-center justify-between w-full">
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold">{{ $sanctions->total() }}</span> résultat(s)
                            </div>

                            @if($search || $filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo || $filterArchived !== 'active')
                            <button
                                wire:click="resetFilters"
                                class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                                <x-iconify icon="lucide:x" class="w-4 h-4" />
                                Réinitialiser
                            </button>
                            @endif
                        </div>
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>

        {{-- Content --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('id')">
                                Réf
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('driver_id')">
                                Chauffeur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('sanction_type')">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('sanction_date')">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700" wire:click="sortBy('supervisor_id')">
                                Superviseur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($this->sanctions as $sanction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sanction->reference ?? '#' . $sanction->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs">
                                        {{ substr($sanction->driver->first_name, 0, 1) }}{{ substr($sanction->driver->last_name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $sanction->driver->full_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $sanction->driver->employee_number }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sanction->getSanctionTypeColor() }}">
                                    <x-iconify icon="{{ $sanction->getSanctionTypeIcon() }}" class="w-3 h-3 mr-1" />
                                    {{ $sanction->getSanctionTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $sanction->reason }}">
                                    {{ $sanction->reason }}
                                </div>
                                @if($sanction->attachment_path)
                                <a href="{{ $sanction->getAttachmentUrl() }}" target="_blank" class="inline-flex items-center mt-1 text-xs text-blue-600 hover:text-blue-800">
                                    <x-iconify icon="heroicons:paper-clip" class="w-3 h-3 mr-1" />
                                    Pièce jointe
                                </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $sanction->sanction_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Il y a {{ $sanction->getDaysSinceSanction() }} jours
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $sanction->supervisor->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sanction->isArchived())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    <x-iconify icon="heroicons:archive-box" class="w-3 h-3 mr-1" />
                                    Archivée
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <x-iconify icon="heroicons:check-circle" class="w-3 h-3 mr-1" />
                                    Active
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Voir --}}
                                    @can('view', $sanction)
                                    <a href="{{ route('admin.sanctions.show', $sanction->id) }}"
                                        class="text-gray-600 hover:text-gray-900 transition-colors"
                                        title="Voir détails">
                                        <x-iconify icon="heroicons:eye" class="w-4 h-4" />
                                    </a>
                                    @endcan

                                    {{-- Modifier --}}
                                    @can('update', $sanction)
                                    <button
                                        wire:click="edit({{ $sanction->id }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                        title="Modifier">
                                        <x-iconify icon="heroicons:pencil-square" class="w-4 h-4" />
                                    </button>
                                    @endcan

                                    {{-- Archiver/Désarchiver --}}
                                    @if($sanction->isArchived())
                                    @can('unarchive', $sanction)
                                    <button
                                        wire:click="unarchive({{ $sanction->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                        title="Désarchiver">
                                        <x-iconify icon="heroicons:archive-box-x-mark" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @else
                                    @can('archive', $sanction)
                                    <button
                                        wire:click="archive({{ $sanction->id }})"
                                        class="text-gray-600 hover:text-gray-900 transition-colors"
                                        title="Archiver">
                                        <x-iconify icon="heroicons:archive-box-arrow-down" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                    @endif

                                    {{-- Supprimer --}}
                                    @can('delete', $sanction)
                                    <button
                                        wire:click="confirmDelete({{ $sanction->id }})"
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        title="Supprimer">
                                        <x-iconify icon="heroicons:trash" class="w-4 h-4" />
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <x-iconify icon="heroicons:inbox" class="w-12 h-12 text-gray-400 mb-4" />
                                    <p class="text-gray-500 text-lg font-medium">Aucune sanction trouvée</p>
                                    <p class="text-gray-400 text-sm mt-2">
                                        @if($search || $filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo)
                                        Essayez de modifier vos filtres de recherche
                                        @else
                                        Commencez par ajouter une nouvelle sanction
                                        @endif
                                    </p>
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
            <x-pagination :paginator="$sanctions" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>
    </div>

    {{-- Modal de création/édition --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-start justify-center min-h-screen pt-10 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="closeModal"></div>

            {{-- Centrage du modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenu du modal --}}
            <div class="inline-block align-top bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full max-h-[90vh] overflow-y-auto relative z-50">
                <form wire:submit.prevent="save">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <x-iconify icon="heroicons:scale" class="w-6 h-6 mr-3" />
                                {{ $editMode ? 'Modifier la sanction' : 'Nouvelle sanction' }}
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                <x-iconify icon="heroicons:x-mark" class="w-6 h-6" />
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-4 space-y-4">
                        {{-- Chauffeur - SlimSelect avec recherche --}}
                        <div wire:ignore x-data="{
                            instance: null,
                            value: @entangle('driver_id'),
                            init() {
                                this.$nextTick(() => {
                                    this.initSlimSelect();
                                });
                            },
                            initSlimSelect() {
                                if (this.instance) {
                                    this.instance.destroy();
                                }
                                this.instance = new SlimSelect({
                                    select: this.$refs.driverSelect,
                                    settings: {
                                        showSearch: true,
                                        searchPlaceholder: 'Rechercher un chauffeur...',
                                        searchText: 'Aucun résultat',
                                        placeholderText: 'Sélectionner un chauffeur',
                                        allowDeselect: true,
                                    },
                                    events: {
                                        afterChange: (newVal) => {
                                            if (newVal && newVal[0]) {
                                                this.value = newVal[0].value;
                                            } else {
                                                this.value = '';
                                            }
                                        }
                                    }
                                });
                                // Set initial value if exists
                                if (this.value) {
                                    this.instance.setSelected(this.value);
                                }
                            }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Chauffeur <span class="text-red-500">*</span>
                            </label>
                            <select x-ref="driverSelect" class="slimselect-field w-full">
                                <option value="" data-placeholder="true">Sélectionner un chauffeur</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                @endforeach
                            </select>
                            @error('driver_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Type de sanction --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Type de sanction <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="sanction_type"
                                class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all @error('sanction_type') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror">
                                <option value="">Sélectionner un type</option>
                                @foreach($sanctionTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type['label'] }} (Sévérité: {{ $type['severity'] }})</option>
                                @endforeach
                            </select>
                            @error('sanction_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Date de sanction --}}
                        <x-date-picker
                            label="Date de sanction"
                            wire:model="sanction_date"
                            required="true"
                            error="{{ $errors->first('sanction_date') }}"
                            placeholder="JJ/MM/AAAA" />

                        {{-- Raison --}}
                        <div>
                            <label for="reason" class="block mb-2 text-sm font-medium text-gray-900">
                                Raison détaillée <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="reason"
                                id="reason"
                                rows="4"
                                placeholder="Décrivez en détail les motifs de la sanction..."
                                class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all resize-none @error('reason') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror"></textarea>
                            @error('reason')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                {{ $message }}
                            </p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Minimum 10 caractères, maximum 5000 caractères</p>
                        </div>

                        {{-- Pièce jointe existante --}}
                        @if($editMode && $existingAttachmentPath)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <x-iconify icon="heroicons:paper-clip" class="w-4 h-4 text-blue-600 mr-3" />
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Pièce jointe existante</p>
                                        <a href="{{ Storage::url($existingAttachmentPath) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                            Voir le fichier
                                        </a>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeExistingAttachment"
                                    class="text-red-600 hover:text-red-800 transition-colors"
                                    title="Supprimer">
                                    <x-iconify icon="heroicons:trash" class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                        @endif

                        {{-- Upload de pièce jointe --}}
                        <div>
                            <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <x-iconify icon="heroicons:paper-clip" class="w-4 h-4 text-purple-500 mr-2" />
                                    Pièce jointe (optionnelle)
                                </span>
                            </label>
                            <input
                                wire:model="attachment"
                                type="file"
                                id="attachment"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-3 bg-white border @error('attachment') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                            @error('attachment')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (max 5 MB)</p>

                            {{-- Indicateur de chargement --}}
                            <div wire:loading wire:target="attachment" class="mt-2 text-sm text-blue-600 flex items-center">
                                <x-iconify icon="heroicons:arrow-path" class="w-4 h-4 animate-spin mr-2" />
                                Chargement du fichier...
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                        <button
                            type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire:target="save" class="flex items-center">
                                <x-iconify icon="heroicons:check" class="w-5 h-5 mr-2" />
                                {{ $editMode ? 'Mettre à jour' : 'Créer la sanction' }}
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <x-iconify icon="heroicons:arrow-path" class="w-5 h-5 animate-spin mr-2" />
                                Enregistrement...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <x-iconify icon="heroicons:x-mark" class="w-5 h-5 mr-2" />
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de confirmation de suppression --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelDelete"></div>

            {{-- Centrage du modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenu du modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Confirmer la suppression
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer cette sanction ? Cette action est irréversible et supprimera également la pièce jointe associée.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex flex-col sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        wire:click="delete"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <x-iconify icon="heroicons:trash" class="w-4 h-4 mr-2" />
                        Supprimer
                    </button>
                    <button
                        type="button"
                        wire:click="cancelDelete"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
