@section('title', 'Gestion des Affectations')

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- ====================================================================
             HEADER - ULTRA-PRO DESIGN
             ===================================================================== --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="lucide:calendar-clock" class="w-6 h-6 text-blue-600" />
                Affectations
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $assignments->total() }} total)
                </span>
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Gérez les affectations de véhicules à vos chauffeurs
            </p>
        </div>

        {{-- METRIC CARDS - KEY STATISTICS --}}
        <x-page-analytics-grid columns="4">
            {{-- Total Affectations --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Affectations</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $assignments->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar-check" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Actives --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Affectations Actives</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeAssignments ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- En Cours --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En Cours</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $inProgressAssignments ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:clock" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- Planifiées --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Planifiées</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $scheduledAssignments ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        {{-- ====================================================================
             SEARCH + FILTERS - PROFESSIONAL DESIGN
             ===================================================================== --}}
        <x-page-search-bar x-data="{ showFilters: false }">
            <x-slot:search>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
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
                </button>
            </x-slot:filters>

            <x-slot:actions>
                @can('create', \App\Models\Assignment::class)
                <a href="{{ route('admin.assignments.create') }}"
                    title="Nouvelle affectation"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="2">
                    {{-- Status Filter --}}
                    <div>
                        <x-slim-select
                            name="status"
                            wire:model.live="status"
                            placeholder="Tous les statuts">
                            <option value="">Tous les statuts</option>
                            <option value="scheduled">Planifiée</option>
                            <option value="active">Active</option>
                            <option value="completed">Complétée</option>
                            <option value="cancelled">Annulée</option>
                        </x-slim-select>
                    </div>

                    {{-- Période (Date Range) --}}
                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Période</label>
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Date Début --}}
                            <x-datepicker
                                name="date_from"
                                label="Début"
                                :value="$date_from"
                                placeholder="JJ/MM/AAAA"
                                x-on:input="$wire.set('date_from', $event.detail)" />

                            {{-- Date Fin --}}
                            <x-datepicker
                                name="date_to"
                                label="Fin"
                                :value="$date_to"
                                placeholder="JJ/MM/AAAA"
                                x-on:input="$wire.set('date_to', $event.detail)" />
                        </div>
                    </div>

                    <x-slot:reset>
                        @if($search || $status || $date_from || $date_to)
                        <button wire:click="resetFilters" class="text-sm text-red-600 hover:text-red-800 flex items-center gap-1 font-medium bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </button>
                        @endif
                    </x-slot:reset>
                </x-page-filters-panel>
            </x-slot:filtersPanel>
        </x-page-search-bar>
        {{-- ====================================================================
             ASSIGNMENTS TABLE - ULTRA-PRO DESIGN
             ===================================================================== --}}
        <div class="relative">
            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="search, status, date_from, date_to, resetFilters, sortBy, page"
                class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 items-center justify-center rounded-lg">
                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full shadow-lg border border-gray-100">
                    <x-iconify icon="lucide:loader-2" class="w-5 h-5 text-blue-600 animate-spin" />
                    <span class="text-sm font-medium text-gray-600">Chargement...</span>
                </div>
            </div>

            @if($assignments->count() > 0)
            <x-card padding="p-0" margin="mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        {{-- Table Header --}}
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réf</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>

                                {{-- Sortable Columns --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('start_datetime')">
                                    <div class="flex items-center gap-1">
                                        Période
                                        @if($sort_by === 'start_datetime')
                                        <x-iconify icon="{{ $sort_order === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="h-3 w-3 text-blue-600" />
                                        @else
                                        <x-iconify icon="lucide:arrow-up-down" class="h-3 w-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        @endif
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('created_at')">
                                    <div class="flex items-center gap-1">
                                        Créé le
                                        @if($sort_by === 'created_at')
                                        <x-iconify icon="{{ $sort_order === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="h-3 w-3 text-blue-600" />
                                        @else
                                        <x-iconify icon="lucide:arrow-up-down" class="h-3 w-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        @endif
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer group" wire:click="sortBy('status')">
                                    <div class="flex items-center gap-1">
                                        Statut
                                        @if($sort_by === 'status')
                                        <x-iconify icon="{{ $sort_order === 'asc' ? 'lucide:arrow-up' : 'lucide:arrow-down' }}" class="h-3 w-3 text-blue-600" />
                                        @else
                                        <x-iconify icon="lucide:arrow-up-down" class="h-3 w-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity" />
                                        @endif
                                    </div>
                                </th>

                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assignments as $assignment)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                {{-- Référence --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">#{{ $assignment->id }}</div>
                                </td>

                                {{-- Véhicule --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <x-iconify icon="lucide:car" class="h-5 w-5 text-gray-500" />
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $assignment->vehicle->registration_plate ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $assignment->vehicle->brand ?? '' }} {{ $assignment->vehicle->model ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Chauffeur --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($assignment->driver)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($assignment->driver->photo)
                                            <img src="{{ Storage::url($assignment->driver->photo) }}"
                                                alt="{{ $assignment->driver->full_name }}"
                                                class="h-10 w-10 rounded-full object-cover ring-2 ring-blue-100 shadow-sm">
                                            @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-blue-100 shadow-sm">
                                                <span class="text-sm font-bold text-white">
                                                    {{ strtoupper(substr($assignment->driver->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->driver->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $assignment->driver->full_name }}</div>
                                            <div class="flex items-center gap-1 text-xs text-gray-500">
                                                <x-iconify icon="lucide:phone" class="w-3.5 h-3.5" />
                                                <span>{{ $assignment->driver->personal_phone ?? $assignment->driver->professional_phone ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="flex items-center gap-2 text-sm text-gray-400">
                                        <x-iconify icon="lucide:user-x" class="w-5 h-5" />
                                        <span class="italic">Non assigné</span>
                                    </div>
                                    @endif
                                </td>

                                {{-- Période --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <x-iconify icon="lucide:calendar-check" class="w-4 h-4 inline mr-1 text-green-600" />
                                        {{ $assignment->start_datetime?->format('d/m/Y H:i') ?? '-' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <x-iconify icon="lucide:calendar-x" class="w-4 h-4 inline mr-1 text-orange-600" />
                                        {{ $assignment->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé' }}
                                    </div>
                                </td>

                                {{-- Créé le --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $assignment->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $assignment->created_at->format('H:i') }}</div>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                    $statusConfig = [
                                    'scheduled' => ['badge' => 'bg-purple-100 text-purple-800', 'icon' => 'lucide:clock', 'label' => 'Planifiée'],
                                    'active' => ['badge' => 'bg-green-100 text-green-800', 'icon' => 'lucide:play-circle', 'label' => 'Active'],
                                    'completed' => ['badge' => 'bg-blue-100 text-blue-800', 'icon' => 'lucide:check-circle', 'label' => 'Terminée'],
                                    'cancelled' => ['badge' => 'bg-red-100 text-red-800', 'icon' => 'lucide:x-circle', 'label' => 'Annulée'],
                                    ];
                                    $status = $statusConfig[$assignment->status] ?? ['badge' => 'bg-gray-100 text-gray-800', 'icon' => 'lucide:help-circle', 'label' => $assignment->status];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['badge'] }}">
                                        <x-iconify :icon="$status['icon']" class="w-3.5 h-3.5" />
                                        {{ $status['label'] }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Terminer Button --}}
                                        @can('end', $assignment)
                                        @if($assignment->canBeEnded())
                                        <button wire:click="confirmEndAssignment({{ $assignment->id }})"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-all duration-200 group"
                                            title="Terminer l'affectation">
                                            <x-iconify icon="lucide:flag-triangle-right" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                        </button>
                                        @endif
                                        @endcan

                                        {{-- View Button --}}
                                        @can('view', $assignment)
                                        <a href="{{ route('admin.assignments.show', $assignment) }}"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                            title="Voir détails">
                                            <x-iconify icon="lucide:eye" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                        </a>
                                        @endcan

                                        {{-- Three-Dot Menu --}}
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
                                                    const width = 224; // w-56
                                                    const padding = 12;
                                                    const menuHeight = this.$refs.menu.offsetHeight || 240;
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
                                                class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group">
                                                <x-iconify icon="lucide:more-vertical" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                            </button>

                                            <template x-teleport="body">
                                                <div x-show="open" x-cloak
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
                                                    @can('update', $assignment)
                                                    @if($assignment->canBeEdited())
                                                    <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:edit" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500" />
                                                        Modifier
                                                    </a>
                                                    @endif
                                                    @endcan

                                                    @if($assignment->status === 'active')
                                                    @if($assignment->handoverForm)
                                                    <a href="{{ route('admin.handovers.vehicles.show', $assignment->handoverForm) }}"
                                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:eye" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-indigo-500" />
                                                        Voir Fiche de Remise
                                                    </a>
                                                    <a href="{{ route('admin.handovers.vehicles.download-pdf', $assignment->handoverForm) }}"
                                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:file-down" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500" />
                                                        Télécharger PDF
                                                    </a>
                                                    @else
                                                    <a href="{{ route('admin.handovers.vehicles.create', $assignment) }}"
                                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:clipboard-check" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-blue-500" />
                                                        Créer Fiche de Remise
                                                    </a>
                                                    @endif
                                                    @endif

                                                    <button wire:click="exportHandoverPdf({{ $assignment->id }})"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:file-text" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-blue-500" />
                                                        Fiche de remise
                                                    </button>

                                                    @can('delete', $assignment)
                                                    @if($assignment->canBeDeleted())
                                                    <div class="border-t border-gray-100 my-1"></div>
                                                    <button type="button"
                                                        @click="close(); $wire.confirmDeleteAssignment({{ $assignment->id }})"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="lucide:trash-2" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500" />
                                                        Supprimer
                                                    </button>
                                                    @endif
                                                    @endcan
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- Pagination & Per Page --}}
            <div class="mt-6">
                <x-pagination :paginator="$assignments" :records-per-page="$perPage" wire:model.live="perPage" />
            </div>
            @else
            {{-- Empty State --}}
            <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                <x-iconify icon="lucide:calendar-clock" class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune affectation trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">Essayez de modifier vos filtres ou créez une nouvelle affectation.</p>
                <div class="mt-6">
                    <button wire:click="resetFilters" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Réinitialiser les filtres
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ====================================================================
         MODALS - ENTERPRISE GRADE
         ===================================================================== --}}

    {{-- End Assignment Modal --}}
    <div x-data="{ show: @entangle('showEndModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show"
                @click="show = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="show"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-visible shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">

                {{-- Close Button (Top Right) --}}
                <button type="button" @click="show = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded-full p-1">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:flag-triangle-right" class="h-6 w-6 text-orange-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Terminer l'affectation
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Voulez-vous terminer cette affectation ?
                            </p>

                            {{-- Assignment Details --}}
                            <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <x-iconify icon="lucide:car" class="h-6 w-6 text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">{{ $endingAssignmentVehicle }}</p>
                                        <p class="text-sm text-blue-700">
                                            <x-iconify icon="lucide:user" class="w-4 h-4 inline mr-1" />
                                            {{ $endingAssignmentDriver }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 space-y-4">
                                {{-- Date et Heure sur la même ligne --}}
                                <div class="grid grid-cols-3 gap-3">
                                    {{-- Date --}}
                                    <div class="col-span-2">
                                        <x-datepicker
                                            name="endDate"
                                            label="Date de fin"
                                            :value="$endDate"
                                            required
                                            :maxDate="date('Y-m-d')"
                                            placeholder="JJ/MM/AAAA"
                                            x-on:input="$wire.set('endDate', $event.detail)" />
                                        @error('endDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Heure --}}
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Heure <span class="text-red-600">*</span>
                                        </label>
                                        <div wire:ignore>
                                            <select id="modal_end_time" class="w-full border-gray-300 rounded-lg text-sm">
                                                <option value="">--:--</option>
                                                @php
                                                for ($h = 0; $h < 24; $h++) {
                                                    for ($m=0; $m < 60; $m +=30) {
                                                    $time=sprintf('%02d:%02d', $h, $m);
                                                    $selected=($endTime===$time) ? 'selected' : '' ;
                                                    echo "<option value=\"{$time}\" {$selected}>{$time}</option>";
                                                    }
                                                    }
                                                    @endphp
                                            </select>
                                        </div>
                                        @error('endTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Kilométrage actuel --}}
                                @if($endingAssignmentCurrentMileage)
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <x-iconify icon="lucide:gauge" class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-xs font-medium text-blue-700 uppercase tracking-wider">Kilométrage actuel</p>
                                            <p class="mt-0.5 text-base font-bold text-blue-900 font-mono">
                                                {{ number_format($endingAssignmentCurrentMileage, 0, ',', ' ') }} km
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Kilométrage de fin --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kilométrage de fin (optionnel)
                                    </label>
                                    <input type="number" wire:model="endMileage" placeholder="Ex: 125000"
                                        class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    @error('endMileage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                {{-- Notes --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Observations (optionnel)
                                    </label>
                                    <textarea wire:model="endNotes" rows="2" maxlength="1000"
                                        class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" wire:click="endAssignment" wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 hover:bg-orange-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="endAssignment" />
                        Confirmer la fin
                    </button>
                    <button type="button" @click="show = false"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" wire:key="assignment-delete-modal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelDelete"></div>

            <div class="relative bg-white rounded-2xl px-6 py-6 text-left shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="lucide:alert-triangle" class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer l'affectation
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer cette affectation ?
                            </p>
                            <p class="text-sm font-medium text-gray-900 mt-2">
                                {{ $deletingAssignmentDescription }}
                            </p>
                            <p class="text-sm text-red-500 mt-2">
                                Cette action est irréversible.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" wire:click="deleteAssignment" wire:loading.attr="disabled"
                        class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50">
                        <x-iconify icon="lucide:loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="deleteAssignment" />
                        Supprimer
                    </button>
                    <button type="button" wire:click="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</section>

@push('scripts')
<script>
    /**
     * ✅ SLIMSELECT INITIALIZATION - MODAL END TIME PICKER
     * Initializes SlimSelect for the assignment termination modal time selector
     * Syncs with Livewire endTime property
     */
    document.addEventListener('livewire:initialized', () => {
        let modalEndTimeSlimSelect = null;

        // Initialize SlimSelect when modal opens
        Livewire.hook('morph.updated', ({
            el,
            component
        }) => {
            const modalTimeSelect = document.getElementById('modal_end_time');

            if (modalTimeSelect && !modalEndTimeSlimSelect) {
                try {
                    modalEndTimeSlimSelect = new SlimSelect({
                        select: modalTimeSelect,
                        settings: {
                            showSearch: true,
                            searchHighlight: true,
                            closeOnSelect: true,
                            allowDeselect: false,
                            placeholderText: 'Sélectionner l\'heure',
                        },
                        events: {
                            afterChange: (newVal) => {
                                if (newVal && newVal[0]) {
                                    const value = (newVal[0].value || '').trim();
                                    // Sync with Livewire
                                    @this.set('endTime', value, false);
                                }
                            }
                        }
                    });
                    console.log('✅ Modal End Time SlimSelect initialized');
                } catch (error) {
                    console.error('❌ Error initializing modal end time SlimSelect:', error);
                }
            }
        });

        // Cleanup SlimSelect when modal closes
        Livewire.on('closeEndModal', () => {
            if (modalEndTimeSlimSelect) {
                modalEndTimeSlimSelect.destroy();
                modalEndTimeSlimSelect = null;
            }
        });
    });
</script>
@endpush
