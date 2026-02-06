<div>
    {{-- ====================================================================
 ⚖️ DRIVER SANCTIONS - ULTRA PRO ENTERPRISE GRADE
 ====================================================================
 Aligné sur le style du module véhicules pour une cohérence parfaite
 @version 2.0-Ultra-Pro
 @since 2025-01-20
 ==================================================================== --}}

    {{-- ===============================================
 STATISTIQUES - Style Véhicules
 =============================================== --}}
    <x-page-analytics-grid columns="4">

        {{-- Total Sanctions --}}
        <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Sanctions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
        </div>

        {{-- Actives --}}
        <div class="bg-amber-50 rounded-lg border border-amber-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 border border-amber-300 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:shield-exclamation" class="w-6 h-6 text-amber-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Actives</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $statistics['active'] }}</p>
                </div>
            </div>
        </div>

        {{-- Ce Mois --}}
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:calendar" class="w-6 h-6 text-blue-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Ce Mois</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $statistics['this_month'] }}</p>
                </div>
            </div>
        </div>

        {{-- Critiques --}}
        <div class="bg-purple-50 rounded-lg border border-purple-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                    <x-iconify icon="heroicons:signal" class="w-6 h-6 text-purple-600" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Critiques</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['by_severity']['critical'] ?? 0 }}</p>
                </div>
            </div>
        </div>

    </x-page-analytics-grid>

    {{-- ===============================================
 RECHERCHE ET FILTRES - Style Véhicules
 =============================================== --}}
    {{-- ===============================================
 RECHERCHE ET FILTRES - Style Véhicules
 =============================================== --}}
    <div class="mb-6" x-data="{ showFilters: false }">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
            {{-- Recherche --}}
            <div class="flex-1 w-full lg:w-auto relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Rechercher par matricule, nom..."
                    wire:loading.attr="aria-busy"
                    wire:target="search"
                    class="pl-10 pr-4 py-2.5 block w-full bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <div wire:loading.delay wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <x-iconify icon="lucide:loader-2" class="w-4 h-4 text-blue-500 animate-spin" />
                </div>
            </div>

            {{-- Bouton Filtres --}}
            <button
                @click="showFilters = !showFilters"
                type="button"
                title="Filtres"
                class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md relative">
                <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="showFilters ? 'rotate-180' : ''" />

                @if($sanctionTypeFilter || $severityFilter || $dateFrom || $dateTo)
                <span class="absolute top-0 right-0 flex h-3 w-3 -mt-1 -mr-1">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                </span>
                @endif
            </button>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- Toggle Archives --}}
                <button
                    wire:click="$toggle('showArchived')"
                    class="{{ $showArchived 
                        ? 'inline-flex items-center gap-2 p-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md' 
                        : 'inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md' }}"
                    title="{{ $showArchived ? 'Voir les sanctions actives' : 'Voir les archives' }}">
                    @if($showArchived)
                    <x-iconify icon="lucide:list" class="w-5 h-5" />
                    @else
                    <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                    @endif
                </button>

                {{-- Nouvelle Sanction --}}
                @can('create', \App\Models\DriverSanction::class)
                    <button
                        onclick="openCreateSanctionModal()"
                        class="inline-flex items-center gap-2 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                        title="Nouvelle Sanction">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                    </button>
                @endcan
            </div>
        </div>

        {{-- Panel Filtres (Collapsible) --}}
        <div
            x-show="showFilters"
            x-transition
            class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
            style="display: none;">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Filtre Type --}}
                <div class="text-xs">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                    <x-slim-select
                        wire:model.live="sanctionTypeFilter"
                        name="sanctionTypeFilter"
                        placeholder="Tous"
                        :options="[
 'avertissement_verbal' => 'Avertissement Verbal',
 'avertissement_ecrit' => 'Avertissement Écrit',
 'mise_a_pied' => 'Mise à Pied',
 'mise_en_demeure' => 'Mise en Demeure',
 'suspension_permis' => 'Suspension Permis',
 'amende' => 'Amende',
 'blame' => 'Blâme',
 'licenciement' => 'Licenciement'
 ]" />
                </div>

                {{-- Filtre Gravité --}}
                <div class="text-xs">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Gravité</label>
                    <x-slim-select
                        wire:model.live="severityFilter"
                        name="severityFilter"
                        placeholder="Toutes"
                        :options="[
 'low' => 'Faible',
 'medium' => 'Moyenne',
 'high' => 'Élevée',
 'critical' => 'Critique'
 ]" />
                </div>

                {{-- Date Début --}}
                <div>
                    <x-datepicker
                        name="dateFrom"
                        label="Du"
                        :value="$dateFrom"
                        :maxDate="date('Y-m-d')"
                        placeholder="JJ/MM/AAAA"
                        x-on:input="$wire.set('dateFrom', $event.detail); $wire.applyFilters()" />
                </div>

                {{-- Date Fin --}}
                <div>
                    <x-datepicker
                        name="dateTo"
                        label="Au"
                        :value="$dateTo"
                        :maxDate="date('Y-m-d')"
                        placeholder="JJ/MM/AAAA"
                        x-on:input="$wire.set('dateTo', $event.detail); $wire.applyFilters()" />
                </div>

            </div>

            <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-200">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model.live="showArchived" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Inclure archivées</span>
                </label>

                <button
                    wire:click="resetFilters"
                    class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    x Réinitialiser
                </button>
            </div>

        </div>
    </div>

    {{-- ===============================================
 TABEAU SANCTIONS - Style Véhicules
 =============================================== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="relative">
            {{-- Loading Overlay --}}
            <div wire:loading.delay wire:target="search,sanctionTypeFilter,severityFilter,dateFrom,dateTo,showArchived,sortBy" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
                <div class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg shadow-lg border border-gray-200">
                    <x-iconify icon="heroicons:arrow-path" class="w-5 h-5 text-blue-600 animate-spin" />
                    <span class="text-sm font-medium text-gray-700">Chargement...</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">
                                <button wire:click="sortBy('driver_id')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
                                    Chauffeur
                                    @if($sortField === 'driver_id')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-3 py-2 text-left">
                                <button wire:click="sortBy('sanction_type')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
                                    Type
                                    @if($sortField === 'sanction_type')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gravité</th>
                            <th class="px-3 py-2 text-left">
                                <button wire:click="sortBy('sanction_date')" class="flex items-center gap-1 text-xs font-medium text-gray-500 uppercase hover:text-gray-700">
                                    Date
                                    @if($sortField === 'sanction_date')
                                    <x-iconify icon="heroicons:chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sanctions as $sanction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-3 py-2">
                                @if($sanction->driver)
                                <div class="flex items-center gap-3">
                                    @if($sanction->driver->photo)
                                    <img src="{{ asset('storage/' . $sanction->driver->photo) }}"
                                        alt="{{ $sanction->driver->first_name }} {{ $sanction->driver->last_name }}"
                                        class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                                    @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                        {{ substr($sanction->driver->first_name, 0, 1) }}{{ substr($sanction->driver->last_name, 0, 1) }}
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $sanction->driver->first_name }} {{ $sanction->driver->last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $sanction->driver->employee_number }}</p>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-300 border border-gray-400 rounded-full flex items-center justify-center text-gray-600 font-semibold text-sm">
                                        <x-iconify icon="heroicons:user" class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-500 italic">
                                            Chauffeur supprimé
                                        </p>
                                        <p class="text-xs text-gray-400">ID: {{ $sanction->driver_id }}</p>
                                    </div>
                                </div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-{{ $sanction->getSanctionTypeColor() }}-100 text-{{ $sanction->getSanctionTypeColor() }}-800 text-xs font-medium rounded-full">
                                    <x-iconify icon="{{ $sanction->getSanctionTypeIcon() }}" class="w-3.5 h-3.5" />
                                    {{ $sanction->getSanctionTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @php
                                $severityColors = [
                                'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                'medium' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                'high' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                'critical' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                ];
                                $colors = $severityColors[$sanction->severity] ?? $severityColors['medium'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 {{ $colors['bg'] }} {{ $colors['text'] }} text-xs font-medium rounded-full">
                                    {{ match($sanction->severity) {
 'low' => 'Faible',
 'medium' => 'Moyenne',
 'high' => 'Élevée',
 'critical' => 'Critique',
 default => $sanction->severity,
 } }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($sanction->sanction_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2">
                                <p class="text-sm text-gray-900 line-clamp-2" title="{{ $sanction->reason }}">{{ $sanction->reason }}</p>
                                @if($sanction->attachment_path)
                                <a href="{{ $sanction->getAttachmentUrl() }}" target="_blank" class="inline-flex items-center mt-1 text-xs text-blue-600 hover:text-blue-800">
                                    <x-iconify icon="heroicons:paper-clip" class="w-3 h-3 mr-1" />
                                    Pièce jointe
                                </a>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @php
                                $statusColors = [
                                'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                'appealed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                'archived' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500'],
                                ];
                                $statusColor = $statusColors[$sanction->status] ?? $statusColors['active'];
                                @endphp
                                <span class="inline-flex px-2.5 py-1 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} text-xs font-medium rounded-full">
                                    {{ match($sanction->status) {
 'active' => 'Active',
 'appealed' => 'Contestée',
 'cancelled' => 'Annulée',
 'archived' => 'Archivée',
 default => $sanction->status,
 } }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Voir --}}
                                    <a href="{{ route('admin.drivers.sanctions.show', $sanction->id) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir détails">
                                        <x-iconify icon="heroicons:eye" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" />
                                    </a>

                                    {{-- Dropdown Menu (3 points) --}}
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
                                                const width = 192; // w-48
                                                const padding = 12;
                                                const menuHeight = this.$refs.menu.offsetHeight || 220;
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
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all duration-200 group"
                                            title="Plus d'actions">
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
                                                class="fixed z-[80] rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                <div class="py-1">
                                                {{-- Modifier --}}
                                                @can('update', $sanction)
                                                    <button @click="$wire.call('openEditModal', {{ $sanction->id }}); open = false"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="heroicons:pencil-square" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500" />
                                                        Modifier
                                                    </button>
                                                @endcan

                                                @if($sanction->trashed())
                                                <div class="border-t border-gray-100 my-1"></div>
                                                {{-- Restaurer --}}
                                                @can('restore', $sanction)
                                                    <button wire:click="confirmRestore({{ $sanction->id }}); close()"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="heroicons:arrow-path" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-green-500" />
                                                        Restaurer
                                                    </button>
                                                @endcan

                                                {{-- Supprimer définitivement --}}
                                                @can('forceDelete', $sanction)
                                                    <button wire:click="confirmForceDelete({{ $sanction->id }}); close()"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="heroicons:trash" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-red-500" />
                                                        Supprimer définitivement
                                                    </button>
                                                @endcan
                                                @else
                                                <div class="border-t border-gray-100 my-1"></div>
                                                {{-- Archiver --}}
                                                @can('delete', $sanction)
                                                    <button wire:click="confirmSoftDelete({{ $sanction->id }}); close()"
                                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                        <x-iconify icon="heroicons:archive-box-arrow-down" class="mr-3 h-4 w-4 text-gray-400 group-hover:text-amber-500" />
                                                        Archiver
                                                    </button>
                                                @endcan
                                                @endif
                                            </div>
                                        </div>
                                        </template>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-iconify icon="heroicons:shield-check" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune sanction trouvée</h3>
                                    <p class="text-sm text-gray-500">Aucune sanction ne correspond à vos critères de recherche.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        <x-pagination :paginator="$sanctions" :records-per-page="$perPage" wire:model.live="perPage" />
    </div>

    {{-- ===============================================
 MODAL CRÉER/MODIFIER SANCTION - LIVEWIRE
 =============================================== --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-start justify-center min-h-screen pt-10 px-4 pb-20 text-center sm:block sm:p-0">
            <div wire:click="closeModal" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-top bg-white rounded-2xl text-left shadow-xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full max-h-[90vh] overflow-y-auto relative z-50">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="heroicons:shield-exclamation" class="w-5 h-5 text-orange-600" />
                        {{ $editMode ? 'Modifier la Sanction' : 'Nouvelle Sanction' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                        <x-iconify icon="heroicons:x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Formulaire --}}
                <form wire:submit.prevent="save" class="space-y-6 px-6 py-4">

                    {{-- Chauffeur - SlimSelect avec recherche --}}
                    <div wire:ignore x-data="{
                        instance: null,
                        value: @entangle('driver_id'),
                        initSelect() {
                            if (this.instance) return;
                            this.instance = new SlimSelect({
                                select: this.$refs.select,
                                settings: {
                                    showSearch: true,
                                    searchPlaceholder: 'Rechercher un chauffeur...',
                                    searchText: 'Aucun résultat',
                                    placeholderText: 'Sélectionner un chauffeur',
                                    allowDeselect: true,
                                },
                                events: {
                                    afterChange: (newVal) => {
                                        this.value = newVal && newVal.length > 0 ? newVal[0].value : null;
                                    }
                                }
                            });
                            
                            // Watch for changes from Livewire
                            this.$watch('value', (val) => {
                                if (this.instance && val != this.instance.getSelected()[0]) {
                                    this.instance.setSelected(val);
                                }
                            });

                            // Set initial value
                            if (this.value) {
                                this.instance.setSelected(this.value);
                            }
                        }
                    }"
                        x-init="initSelect()">
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Chauffeur <span class="text-red-500">*</span>
                        </label>
                        <select x-ref="select" class="slimselect-field w-full">
                            <option value="" data-placeholder="true">Sélectionner un chauffeur</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }} - {{ $driver->employee_number }}</option>
                            @endforeach
                        </select>
                        @error('driver_id')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Type et Gravité --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Type de Sanction <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="sanction_type"
                                class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all @error('sanction_type') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror">
                                <option value="">Sélectionner</option>
                                <option value="avertissement_verbal">Avertissement Verbal</option>
                                <option value="avertissement_ecrit">Avertissement Écrit</option>
                                <option value="mise_a_pied">Mise à Pied</option>
                                <option value="mise_en_demeure">Mise en Demeure</option>
                                <option value="suspension_permis">Suspension Permis</option>
                                <option value="amende">Amende</option>
                                <option value="blame">Blâme</option>
                                <option value="licenciement">Licenciement</option>
                            </select>
                            @error('sanction_type')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Gravité <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="severity"
                                class="w-full px-4 py-2.5 bg-gray-50 border text-sm text-gray-900 rounded-lg shadow-sm transition-all @error('severity') border-red-500 bg-red-50 ring-1 ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 hover:border-gray-400 @enderror">
                                <option value="low">Faible</option>
                                <option value="medium">Moyenne</option>
                                <option value="high">Élevée</option>
                                <option value="critical">Critique</option>
                            </select>
                            @error('severity')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4 mr-1.5 flex-shrink-0" />
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Date et Durée --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-datepicker
                                name="sanction_date"
                                label="Date de Sanction"
                                :value="$sanction_date"
                                required
                                :maxDate="date('Y-m-d')"
                                placeholder="JJ/MM/AAAA"
                                :error="$errors->first('sanction_date')"
                                x-on:input="$wire.set('sanction_date', $event.detail)" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Durée (jours)
                            </label>
                            <input
                                type="number"
                                wire:model="duration_days"
                                min="1"
                                max="365"
                                placeholder="Nombre de jours"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('duration_days') border-red-500 @enderror">
                            @error('duration_days') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Motif --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Motif <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            wire:model="reason"
                            rows="3"
                            placeholder="Décrivez le motif de la sanction (minimum 10 caractères)..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('reason') border-red-500 @enderror"></textarea>
                        @error('reason') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Statut et Notes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div wire:ignore x-data="{
                            instance: null,
                            value: @entangle('status'),
                            init() {
                                this.$nextTick(() => {
                                    this.initSlimSelect();
                                });
                                this.$watch('value', (val) => {
                                    if (this.instance) {
                                        this.instance.setSelected(val);
                                    }
                                });
                            },
                            initSlimSelect() {
                                if (this.instance) {
                                    this.instance.destroy();
                                }
                                this.instance = new SlimSelect({
                                    select: this.$refs.statusSelect,
                                    settings: {
                                        showSearch: false,
                                        placeholderText: 'Sélectionner un statut',
                                        allowDeselect: false,
                                    },
                                    events: {
                                        afterChange: (newVal) => {
                                            if (newVal && newVal[0]) {
                                                this.value = newVal[0].value;
                                            }
                                        }
                                    }
                                });
                                if (this.value) {
                                    this.instance.setSelected(this.value);
                                }
                            }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select x-ref="statusSelect" class="slimselect-field w-full">
                                <option value="active">Active</option>
                                <option value="appealed">Contestée</option>
                                <option value="cancelled">Annulée</option>
                                <option value="archived">Archivée</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <input
                                type="text"
                                wire:model="notes"
                                placeholder="Notes additionnelles"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    {{-- Pièce jointe --}}
                    {{-- Pièce jointe (Drag & Drop) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pièce jointe
                        </label>

                        <div x-data="{ isDropping: false, progress: 0 }"
                            x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                            :class="{ 'border-blue-500 bg-blue-50': isDropping, 'border-gray-300 hover:bg-gray-50': !isDropping }"
                            class="relative border-2 border-dashed rounded-xl p-6 transition-all text-center">

                            <input
                                type="file"
                                wire:model="attachment"
                                x-ref="fileInput"
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                            <div class="flex flex-col items-center justify-center pointer-events-none">
                                <x-iconify icon="heroicons:cloud-arrow-up" class="w-8 h-8 text-gray-400 mb-2" />
                                <p class="text-sm text-gray-600 font-medium">
                                    <span class="text-blue-600">Cliquez pour upload</span> ou glissez-déposez
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG, DOC (max 10 MB)</p>
                            </div>

                            {{-- Fichier sélectionné ou existant --}}
                            @if($attachment)
                            <div class="mt-3 flex items-center justify-center gap-2 text-sm text-blue-600 font-medium bg-blue-50 py-1 px-3 rounded-full inline-flex">
                                <x-iconify icon="heroicons:document-check" class="w-4 h-4" />
                                <span>Fichier sélectionné</span>
                            </div>
                            @elseif($existingAttachment)
                            <div class="mt-3 flex items-center justify-center gap-2 text-sm text-gray-600 bg-gray-100 py-1 px-3 rounded-full inline-flex relative z-20">
                                <x-iconify icon="heroicons:paper-clip" class="w-4 h-4" />
                                <span class="truncate max-w-xs">{{ basename($existingAttachment) }}</span>
                            </div>
                            @endif
                        </div>

                        @error('attachment') <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="flex items-center justify-end gap-3 pt-4 pb-4 px-6 border-t border-gray-200 bg-gray-50 sticky bottom-0">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
                            Annuler
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md text-sm disabled:opacity-50">
                            <span wire:loading.remove>{{ $editMode ? 'Enregistrer' : 'Créer' }}</span>
                            <span wire:loading>
                                <x-iconify icon="heroicons:arrow-path" class="w-4 h-4 animate-spin inline" />
                                Traitement...
                            </span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    @endif

    {{-- ===============================================
    MODAL CONFIRMATION ARCHIVAGE
    =============================================== --}}
    @if($showArchiveModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelArchive"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="heroicons:archive-box-arrow-down" class="h-6 w-6 text-amber-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Archiver la sanction
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            @if($modalSanction)
                            <p class="mb-2">
                                Voulez-vous archiver la sanction <strong>{{ $modalSanction->getSanctionTypeLabel() }}</strong> du chauffeur <strong>{{ $modalSanction->driver->first_name }} {{ $modalSanction->driver->last_name }}</strong> ?
                            </p>
                            <p>
                                Elle sera déplacée dans les archives et ne sera plus visible dans la liste principale.
                            </p>
                            @else
                            <p>Chargement des détails...</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="executeSoftDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Archiver
                    </button>
                    <button type="button" wire:click="cancelSoftDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===============================================
    MODAL CONFIRMATION RESTAURATION
    =============================================== --}}
    @if($showRestoreModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelRestore"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="heroicons:arrow-path" class="h-6 w-6 text-green-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Restaurer la sanction
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir restaurer cette sanction ? Elle apparaîtra de nouveau dans la liste des sanctions actives.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="executeRestore" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Restaurer
                    </button>
                    <button type="button" wire:click="cancelRestore" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ===============================================
    MODAL CONFIRMATION SUPPRESSION DÉFINITIVE
    =============================================== --}}
    @if($showForceDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" wire:click="cancelForceDelete"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <x-iconify icon="heroicons:exclamation-triangle" class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Suppression définitive
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            @if($modalSanction)
                            <p class="mb-4">
                                Êtes-vous certain de vouloir supprimer <strong>DÉFINITIVEMENT</strong> la sanction <strong>{{ $modalSanction->getSanctionTypeLabel() }}</strong> du chauffeur <strong>{{ $modalSanction->driver->first_name }} {{ $modalSanction->driver->last_name }}</strong> ?
                            </p>
                            @endif
                            <div class="bg-red-50 border-l-4 border-red-500 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="heroicons:exclamation-circle" class="h-5 w-5 text-red-400" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-red-700 font-bold uppercase tracking-wide">
                                            Action Irréversible
                                        </p>
                                        <p class="text-sm text-red-600 mt-1">
                                            Cette action supprimera définitivement la sanction, l'historique associé et les pièces jointes. Cette opération ne peut pas être annulée.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="forceDelete" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Je confirme la suppression définitive
                    </button>
                    <button type="button" wire:click="cancelForceDelete" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // ═══════════════════════════════════════════════════════════════════════════
    // FONCTIONS JAVASCRIPT POUR LES MODALES - STYLE VÉHICULES
    // ═══════════════════════════════════════════════════════════════════════════

    // Toggle filtres
    function toggleFilters() {
        const panel = document.getElementById('filtersPanel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
        } else {
            panel.style.display = 'none';
        }
    }

    // Fermer modal
    function closeModal() {
        const modal = document.querySelector('.fixed.inset-0.z-50');
        if (modal) {
            modal.style.opacity = '0';
            modal.style.transform = 'scale(0.95)';
            setTimeout(() => modal.remove(), 200);
        }
    }

    // NOTE: TomSelect removed as we are now using SlimSelect component

    // Ouvrir modal création sanction
    function openCreateSanctionModal() {
        @this.call('openCreateModal');
    }

    // Ouvrir modal édition sanction
    function openEditSanctionModal(sanctionId) {
        @this.call('openEditModal', sanctionId);
    }

    // Modal de confirmation de suppression - Style véhicules
    function deleteSanctionModal(sanctionId, driverName) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');

        modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Supprimer la sanction
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Êtes-vous sûr de vouloir supprimer cette sanction pour <strong class="font-semibold text-gray-900">${driverName}</strong> ?
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Cette action est <strong class="font-semibold text-red-600">irréversible</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        onclick="confirmDeleteSanction(${sanctionId})"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Supprimer
                    </button>
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    // Confirmer suppression
    function confirmDeleteSanction(sanctionId) {
        @this.call('deleteSanction', sanctionId);
        closeModal();
    }

    // Écouter les notifications Livewire
    window.addEventListener('notification', event => {
        const data = event.detail[0] || event.detail;

        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-md transform transition-all duration-300 ${
        data.type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
    } border rounded-lg shadow-lg p-4`;

        toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                ${data.type === 'success' 
                    ? '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                    : '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
                }
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium ${data.type === 'success' ? 'text-green-800' : 'text-red-800'}">
                    ${data.message}
                </p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="${data.type === 'success' ? 'text-green-600 hover:text-green-800' : 'text-red-600 hover:text-red-800'}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;

        document.body.appendChild(toast);

        // Auto-fermer après 5 secondes
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
</script>
@endpush
