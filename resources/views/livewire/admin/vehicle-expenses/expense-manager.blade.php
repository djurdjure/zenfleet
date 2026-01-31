<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:credit-card" class="w-6 h-6 text-blue-600" />
                Gestion des Dépenses Véhicules
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $expenses->total() }})</span>
            </h1>

            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,vehicle_id,supplier_id,expense_group_id,category,payment_status,approval_status,dateFrom,dateTo,perPage,filter">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total ce mois</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalAmount, 2) }} DZD</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="solar:wallet-bold-duotone" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En attente</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="solar:clock-circle-bold-duotone" class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Approuvées</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="solar:check-circle-bold-duotone" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Moyenne mensuelle</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($monthlyAverage, 2) }} DZD</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <x-iconify icon="solar:chart-bold-duotone" class="w-6 h-6 text-gray-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        @php
            $activeFilters = collect([
                $search,
                $filter,
                $vehicle_id,
                $supplier_id,
                $expense_group_id,
                $category,
                $payment_status,
                $approval_status,
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
                        placeholder="Rechercher par référence, description, véhicule..."
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
                <a href="{{ route('admin.vehicle-expenses.create') }}"
                    title="Nouvelle dépense"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="solar:add-circle-bold" class="w-5 h-5" />
                </a>
                <a href="{{ route('admin.vehicle-expenses.dashboard') }}"
                    title="Analytics"
                    class="inline-flex items-center gap-2 p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="solar:chart-2-bold" class="w-5 h-5" />
                </a>
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Véhicule</label>
                        <select wire:model.live="vehicle_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model.live="category" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut d'approbation</label>
                        <select wire:model.live="approval_status" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les statuts</option>
                            <option value="draft">Brouillon</option>
                            <option value="pending_level1">En attente niveau 1</option>
                            <option value="pending_level2">En attente niveau 2</option>
                            <option value="approved">Approuvé</option>
                            <option value="rejected">Rejeté</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut de paiement</label>
                        <select wire:model.live="payment_status" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            <option value="pending">En attente</option>
                            <option value="paid">Payé</option>
                            <option value="rejected">Rejeté</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fournisseur</label>
                        <select wire:model.live="supplier_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Groupe</label>
                        <select wire:model.live="expense_group_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les groupes</option>
                            @foreach($expenseGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
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
            @if(count($selectedExpenses) > 0)
                <div class="absolute top-0 left-0 right-0 z-10 bg-blue-50 p-2 flex items-center justify-between border-b border-blue-100 animate-fade-in-down">
                    <div class="flex items-center gap-3 px-4">
                        <span class="font-medium text-blue-900">{{ count($selectedExpenses) }} sélectionnée(s)</span>
                        <button wire:click="$set('selectedExpenses', [])" class="text-sm text-blue-600 hover:text-blue-800 underline">
                            Annuler
                        </button>
                    </div>
                    <div class="flex items-center gap-2 px-4">
                        <button wire:click="deleteSelected" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                            <x-iconify icon="solar:trash-bin-2-bold" class="w-4 h-4" /> Supprimer
                        </button>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded hover:bg-gray-700">
                                <x-iconify icon="solar:export-bold" class="w-4 h-4" /> Export
                            </button>
                            <div x-show="open" x-transition class="absolute right-0 z-10 mt-2 w-40 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5" style="display: none;">
                                <button wire:click="exportSelected('csv')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">CSV</button>
                                <button wire:click="exportSelected('excel')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Excel</button>
                                <button wire:click="exportSelected('pdf')" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" wire:model.live="selectAll" class="rounded">
                            </th>
                            <th class="px-4 py-3 text-left">
                                <button wire:click="sortBy('invoice_number')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700">
                                    N° Dépense
                                    @if($sortField === 'invoice_number')
                                        <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left">
                                <button wire:click="sortBy('expense_date')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700">
                                    Date
                                    @if($sortField === 'expense_date')
                                        <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Véhicule</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Catégorie</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Description</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Fournisseur</th>
                            <th class="px-4 py-3 text-right">
                                <button wire:click="sortBy('total_ttc')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700 ml-auto">
                                    Montant
                                    @if($sortField === 'total_ttc')
                                        <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                    @endif
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Statut</th>
                            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" wire:model.live="selectedExpenses" value="{{ $expense->id }}" class="rounded">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-900">{{ $expense->expense_number ?? $expense->invoice_number ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $expense->expense_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-iconify icon="solar:car-bold" class="w-4 h-4 text-gray-400" />
                                    <span class="text-sm text-gray-900">{{ $expense->vehicle?->registration_plate ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $categories[$expense->expense_category] ?? $expense->expense_category }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900 truncate max-w-xs">{{ $expense->description }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $expense->supplier?->name ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="font-semibold text-gray-900">{{ number_format($expense->total_ttc, 2) }} DZD</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @switch($expense->approval_status)
                                    @case('draft')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Brouillon</span>
                                        @break
                                    @case('pending_level1')
                                    @case('pending_level2')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">En attente</span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approuvé</span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.vehicle-expenses.show', $expense) }}" class="p-1.5 text-gray-600 hover:bg-gray-100 rounded-lg transition" title="Voir">
                                        <x-iconify icon="solar:eye-bold" class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('admin.vehicle-expenses.edit', $expense) }}" class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition" title="Éditer">
                                        <x-iconify icon="solar:pen-bold" class="w-4 h-4" />
                                    </a>
                                    @if(in_array($expense->approval_status, ['pending_level1', 'pending_level2']))
                                        <button wire:click="approveExpense({{ $expense->id }})" class="p-1.5 text-green-600 hover:bg-green-100 rounded-lg transition" title="Approuver">
                                            <x-iconify icon="solar:check-circle-bold" class="w-4 h-4" />
                                        </button>
                                    @endif
                                    <button wire:click="confirmDelete({{ $expense->id }})" class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition" title="Supprimer">
                                        <x-iconify icon="solar:trash-bin-2-bold" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <x-iconify icon="solar:box-minimalistic-broken" class="w-12 h-12 mb-2 text-gray-300" />
                                    <p>Aucune dépense trouvée</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$expenses" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>

        @if($showDeleteModal)
            <x-modal wire:model="showDeleteModal" maxWidth="md">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                        <x-iconify icon="solar:trash-bin-2-bold" class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-medium text-center text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-sm text-center text-gray-600 mb-6">
                        Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.
                    </p>
                    <div class="flex gap-3">
                        <button wire:click="$set('showDeleteModal', false)" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Annuler</button>
                        <button wire:click="deleteExpense" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Supprimer</button>
                    </div>
                </div>
            </x-modal>
        @endif
    </div>
</div>
