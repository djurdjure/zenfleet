<div>
    {{-- ====================================================================
     👨‍💼 GESTION DES CHAUFFEURS V7.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
     ==================================================================== --}}

    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

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
            
            {{-- Loading Indicator --}}
            <div wire:loading class="flex items-center gap-2 text-blue-600">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        {{-- ===============================================
            CARDS MÉTRIQUES ULTRA-PRO
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Total Chauffeurs --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total chauffeurs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:users" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Disponibles --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disponibles</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['available_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:user-check" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- En Mission --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En mission</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $analytics['active_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:briefcase" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            {{-- En Repos --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En repos</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $analytics['resting_drivers'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="lucide:pause-circle" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
            BARRE DE RECHERCHE ET ACTIONS (Enterprise-Grade)
        =============================================== --}}
        <div class="mb-6" x-data="{ showFilters: false }">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                {{-- Recherche rapide --}}
                <div class="flex-1 w-full lg:w-auto relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-iconify icon="lucide:search" class="w-5 h-5 text-gray-400" />
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Rechercher par nom, prénom, matricule..."
                        class="pl-10 pr-4 py-2.5 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm">
                </div>

                {{-- Bouton Filtres Avancés --}}
                <button
                    @click="showFilters = !showFilters"
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:filter" class="w-5 h-5 text-gray-500" />
                    <span class="font-medium text-gray-700">Filtres</span>
                    <x-iconify
                        icon="heroicons:chevron-down"
                        class="w-4 h-4 text-gray-400 transition-transform duration-200"
                        x-bind:class="showFilters ? 'rotate-180' : ''" />
                </button>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Toggle Archives --}}
                    @if($visibility === 'archived')
                        <button wire:click="$set('visibility', 'active')"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:list" class="w-5 h-5" />
                            <span class="hidden lg:inline font-medium">Voir Actifs</span>
                        </button>
                    @else
                        <button wire:click="$set('visibility', 'archived')"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:archive" class="w-5 h-5 text-amber-600" />
                            <span class="hidden lg:inline font-medium text-gray-700">Voir Archives</span>
                        </button>
                    @endif

                    {{-- Export Dropdown --}}
                    <div class="relative" x-data="{ exportOpen: false }">
                        <button
                            @click="exportOpen = !exportOpen"
                            @click.away="exportOpen = false"
                            type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                            <span class="hidden lg:inline font-medium text-gray-700">Export</span>
                            <x-iconify icon="heroicons:chevron-down" class="w-4 h-4 text-gray-400" />
                        </button>

                        <div
                            x-show="exportOpen"
                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="display: none;">
                            <div class="py-1">
                                <a href="{{ route('admin.drivers.export.pdf', request()->all()) }}" class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-text" class="w-4 h-4 text-red-600" />
                                    <span>Export PDF</span>
                                </a>
                                <a href="{{ route('admin.drivers.export.csv', request()->all()) }}" class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-spreadsheet" class="w-4 h-4 text-green-600" />
                                    <span>Export CSV</span>
                                </a>
                                <a href="{{ route('admin.drivers.export.excel', request()->all()) }}" class="group flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100">
                                    <x-iconify icon="lucide:file-bar-chart" class="w-4 h-4 text-blue-600" />
                                    <span>Export Excel</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Import --}}
                    <a href="{{ route('admin.drivers.import.show') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:upload" class="w-5 h-5" />
                        <span class="font-medium">Importer</span>
                    </a>

                    {{-- Nouveau Chauffeur --}}
                    <a href="{{ route('admin.drivers.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                        <span class="font-medium">Nouveau Chauffeur</span>
                    </a>
                </div>
            </div>

            {{-- Panel Filtres Avancés --}}
            <div
                x-show="showFilters"
                x-transition
                class="mt-4 bg-white rounded-lg border border-gray-200 p-4 shadow-sm"
                style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Statut --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="status_id" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            @foreach($driverStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Catégorie Permis --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie permis</label>
                        <select wire:model.live="license_category" class="block w-full border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les catégories</option>
                            @foreach(['A1', 'A', 'B', 'BE', 'C1', 'C1E', 'C', 'CE', 'D', 'DE', 'F'] as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-gray-200">
                    <button
                        wire:click="resetFilters"
                        class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        {{-- ===============================================
            TABLE DES CHAUFFEURS (Enterprise-Grade)
        =============================================== --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden relative">
            
            {{-- Bulk Actions Floating Menu --}}
            @if(count($selectedDrivers) > 0)
                <div class="absolute top-0 left-0 right-0 z-10 bg-blue-50 p-2 flex items-center justify-between border-b border-blue-100 animate-fade-in-down">
                    <div class="flex items-center gap-3 px-4">
                        <span class="font-medium text-blue-900">{{ count($selectedDrivers) }} sélectionné(s)</span>
                        <button wire:click="$set('selectedDrivers', [])" class="text-sm text-blue-600 hover:text-blue-800 underline">
                            Annuler
                        </button>
                    </div>
                    <div class="flex items-center gap-2 px-4">
                        @if($visibility === 'archived')
                            <button wire:click="bulkRestore" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700">
                                <x-iconify icon="lucide:rotate-ccw" class="w-4 h-4" /> Restaurer
                            </button>
                            <button wire:click="bulkForceDelete" 
                                onclick="confirm('Êtes-vous sûr de vouloir supprimer définitivement ces chauffeurs ?') || event.stopImmediatePropagation()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                <x-iconify icon="lucide:trash-2" class="w-4 h-4" /> Supprimer
                            </button>
                        @else
                            <button wire:click="bulkArchive" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded hover:bg-orange-700">
                                <x-iconify icon="lucide:archive" class="w-4 h-4" /> Archiver
                            </button>
                        @endif
                    </div>
                </div>
            @endif

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
                                                    <span class="text-sm font-semibold text-blue-700">
                                                        {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $driver->first_name }} {{ $driver->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">#{{ $driver->employee_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 flex items-center gap-1.5">
                                        <x-iconify icon="lucide:phone" class="w-4 h-4 text-gray-400" /> {{ $driver->personal_phone ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 flex items-center gap-1.5">
                                        <x-iconify icon="lucide:mail" class="w-4 h-4 text-gray-400" /> {{ $driver->personal_email ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $driver->license_number }}</div>
                                    @if($driver->license_category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            Catégorie {{ $driver->license_category }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusName = $driver->driverStatus->name ?? 'N/A';
                                        $colors = [
                                            'Disponible' => 'bg-green-100 text-green-800',
                                            'En mission' => 'bg-orange-100 text-orange-800',
                                            'En repos' => 'bg-amber-100 text-amber-800',
                                            'En congé' => 'bg-purple-100 text-purple-800',
                                            'Maladie' => 'bg-red-100 text-red-800',
                                            'Indisponible' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $colorClass = $colors[$statusName] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ $statusName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($driver->activeAssignment && $driver->activeAssignment->vehicle)
                                        <div class="flex items-center gap-1.5">
                                            <x-iconify icon="lucide:car" class="w-4 h-4 text-blue-600" />
                                            <span class="font-medium text-gray-900">{{ $driver->activeAssignment->vehicle->registration_plate }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Aucun véhicule</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($driver->deleted_at)
                                            <button wire:click="confirmRestore({{ $driver->id }})" class="text-green-600 hover:text-green-900" title="Restaurer">
                                                <x-iconify icon="lucide:rotate-ccw" class="w-5 h-5" />
                                            </button>
                                            <button wire:click="confirmForceDelete({{ $driver->id }})" class="text-red-600 hover:text-red-900" title="Supprimer définitivement">
                                                <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                                            </button>
                                        @else
                                            <a href="{{ route('admin.drivers.show', $driver) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                                <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                            </a>
                                            <a href="{{ route('admin.drivers.edit', $driver) }}" class="text-gray-600 hover:text-gray-900" title="Modifier">
                                                <x-iconify icon="lucide:edit" class="w-5 h-5" />
                                            </a>
                                            <button wire:click="confirmArchive({{ $driver->id }})" class="text-orange-600 hover:text-orange-900" title="Archiver">
                                                <x-iconify icon="lucide:archive" class="w-5 h-5" />
                                            </button>
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
                                        <a href="{{ route('admin.drivers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <x-iconify icon="lucide:plus" class="w-5 h-5" />
                                            Ajouter un chauffeur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>

    {{-- ===============================================
        MODALS (Archive, Restore, Force Delete)
    =============================================== --}}

    {{-- Archive Modal --}}
    @if($showArchiveModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelArchive"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="lucide:archive" class="w-6 h-6 text-orange-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Archiver le chauffeur</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir archiver ce chauffeur ? Il ne sera plus visible dans la liste active.</p>
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

    {{-- Restore Modal --}}
    @if($showRestoreModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelRestore"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="lucide:rotate-ccw" class="w-6 h-6 text-green-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Restaurer le chauffeur</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir restaurer ce chauffeur ? Il réapparaîtra dans la liste active.</p>
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

    {{-- Force Delete Modal --}}
    @if($showForceDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelForceDelete"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="lucide:trash-2" class="w-6 h-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Suppression définitive</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer définitivement ce chauffeur ? Cette action est irréversible et supprimera toutes les données associées.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button wire:click="forceDeleteDriver" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
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

</div>
