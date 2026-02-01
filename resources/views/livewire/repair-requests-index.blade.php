<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:wrench" class="w-6 h-6 text-blue-600" />
                Demandes de Réparation
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $statistics['total'] ?? 0 }})</span>
            </h1>

            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,statusFilter,urgencyFilter,categoryFilter,vehicleFilter,dateFrom,dateTo,perPage">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clipboard-list" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-amber-600">En attente</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['pending'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Approuvées</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['approved'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Rejetées</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['rejected'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:x-circle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Critiques</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['critical'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:alert-triangle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-600">Urgentes</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['high'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:zap" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Aujourd'hui</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['today'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">Cette semaine</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['week'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:bar-chart-2" class="w-6 h-6 text-indigo-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        @php
            $activeFilters = collect([
                $search,
                $statusFilter,
                $urgencyFilter,
                $categoryFilter,
                $vehicleFilter,
                $dateFrom,
                $dateTo,
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
                @can('create', App\Models\RepairRequest::class)
                <a href="{{ route('admin.repair-requests.create') }}"
                    title="Nouvelle Demande"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
                @endcan

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
                            const menuHeight = this.$refs.menu.offsetHeight || 180;
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
                    <button
                        x-ref="trigger"
                        @click="toggle"
                        @click.outside="close"
                        title="Exporter"
                        class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                        <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                    </button>
                    <template x-teleport="body">
                        <div
                            x-show="open"
                            x-transition
                            :style="styles"
                            @click.outside="close"
                            x-ref="menu"
                            :class="direction === 'up' ? 'origin-bottom-right' : 'origin-top-right'"
                            class="fixed z-[80] rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="display: none;">
                            <div class="py-1">
                                <button wire:click="exportData('csv')" class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">Export CSV</button>
                                <button wire:click="exportData('excel')" class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">Export Excel</button>
                                <button wire:click="exportData('pdf')" class="block w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">Export PDF</button>
                            </div>
                        </div>
                    </template>
                </div>
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="statusFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $value => $config)
                                <option value="{{ $value }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Urgence</label>
                        <select wire:model.live="urgencyFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les urgences</option>
                            @foreach($urgencyLevels as $value => $config)
                                <option value="{{ $value }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model.live="categoryFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Véhicule</label>
                        <select wire:model.live="vehicleFilter" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date début</label>
                        <input type="date" wire:model.live="dateFrom" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date fin</label>
                        <input type="date" wire:model.live="dateTo" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
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
            @if(count($selectedRequests) > 0)
                <div class="absolute top-0 left-0 right-0 z-10 bg-blue-50 p-2 flex items-center justify-between border-b border-blue-100 animate-fade-in-down">
                    <div class="flex items-center gap-3 px-4">
                        <span class="font-medium text-blue-900">{{ count($selectedRequests) }} sélectionné(s)</span>
                        <button wire:click="$set('selectedRequests', [])" class="text-sm text-blue-600 hover:text-blue-800 underline">Annuler</button>
                    </div>
                    <div class="flex items-center gap-2 px-4">
                        @can('approve', App\Models\RepairRequest::class)
                        <button wire:click="applyBulkAction('approve')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                            <x-iconify icon="lucide:check" class="w-4 h-4" /> Approuver
                        </button>
                        @endcan
                        @can('reject', App\Models\RepairRequest::class)
                        <button wire:click="applyBulkAction('reject')" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                            <x-iconify icon="lucide:x" class="w-4 h-4" /> Rejeter
                        </button>
                        @endcan
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('uuid')">
                                <div class="flex items-center space-x-1">
                                    <span>ID</span>
                                    @if($sortField === 'uuid')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('created_at')">
                                <div class="flex items-center space-x-1">
                                    <span>Date</span>
                                    @if($sortField === 'created_at')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('urgency')">
                                <div class="flex items-center space-x-1">
                                    <span>Urgence</span>
                                    @if($sortField === 'urgency')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('status')">
                                <div class="flex items-center space-x-1">
                                    <span>Statut</span>
                                    @if($sortField === 'status')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="{{ $sortDirection === 'asc' ? 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' : 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' }}"></path>
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            <td class="px-6 py-4">
                                <input type="checkbox" value="{{ $request->id }}" wire:model.live="selectedRequests" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">#{{ substr($request->uuid, 0, 8) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $request->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $request->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-600">{{ substr($request->driver->user->name ?? 'NA', 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->driver->user->name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->driver->license_number ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->vehicle->registration_plate ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $request->vehicle->brand ?? '' }} {{ $request->vehicle->model ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($request->title, 30) }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($request->description, 40) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($request->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $request->category->name }}</span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColorClass }}">{{ $statusConfig['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.repair-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900" title="Voir les détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    @can('approve', $request)
                                    <button wire:click="$dispatch('approve-request', { requestId: {{ $request->id }} })" class="text-green-600 hover:text-green-900" title="Approuver">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('reject', $request)
                                    <button wire:click="$dispatch('reject-request', { requestId: {{ $request->id }} })" class="text-red-600 hover:text-red-900" title="Rejeter">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    @endcan

                                    @can('edit', $request)
                                    <a href="{{ route('admin.repair-requests.edit', $request) }}" class="text-gray-600 hover:text-gray-900" title="Modifier">
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

            <div class="mt-4">
                <x-pagination :paginator="$repairRequests" :records-per-page="$perPage" wire:model.live="perPage" />
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
