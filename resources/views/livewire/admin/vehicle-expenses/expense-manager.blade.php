<div class="space-y-6">
    {{-- En-tête avec statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-card class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total ce mois</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($totalAmount, 2) }} DZD
                    </p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <x-iconify icon="solar:wallet-bold-duotone" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </x-card>

        <x-card class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En attente</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                        {{ $pendingCount }}
                    </p>
                </div>
                <div class="p-3 bg-warning-100 dark:bg-warning-900 rounded-lg">
                    <x-iconify icon="solar:clock-circle-bold-duotone" class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
            </div>
        </x-card>

        <x-card class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approuvées</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ $approvedCount }}
                    </p>
                </div>
                <div class="p-3 bg-success-100 dark:bg-success-900 rounded-lg">
                    <x-iconify icon="solar:check-circle-bold-duotone" class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
            </div>
        </x-card>

        <x-card class="p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Moyenne mensuelle</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($monthlyAverage, 2) }} DZD
                    </p>
                </div>
                <div class="p-3 bg-gray-100 dark:bg-gray-900 rounded-lg">
                    <x-iconify icon="solar:chart-bold-duotone" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Barre d'actions et filtres --}}
    <x-card>
        <div class="p-4 space-y-4">
            {{-- Actions principales --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.vehicle-expenses.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                        <x-iconify icon="solar:add-circle-bold" class="w-5 h-5" />
                        <span>Nouvelle dépense</span>
                    </a>
                    
                    @if(count($selectedExpenses) > 0)
                    <div class="flex items-center gap-2">
                        <button wire:click="deleteSelected" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <x-iconify icon="solar:trash-bin-2-bold" class="w-5 h-5" />
                        </button>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                <x-iconify icon="solar:export-bold" class="w-5 h-5" />
                            </button>
                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
                                <button wire:click="exportSelected('csv')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Export CSV
                                </button>
                                <button wire:click="exportSelected('excel')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Export Excel
                                </button>
                                <button wire:click="exportSelected('pdf')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Export PDF
                                </button>
                            </div>
                        </div>
                        
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ count($selectedExpenses) }} sélectionnée(s)
                        </span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-2">
                    <button wire:click="resetFilters" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <x-iconify icon="solar:refresh-bold" class="w-5 h-5" />
                    </button>
                    <a href="{{ route('admin.vehicle-expenses.analytics') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <x-iconify icon="solar:chart-2-bold" class="w-5 h-5" />
                        <span>Analytics</span>
                    </a>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher..."
                        class="w-full"
                        icon="solar:magnifer-linear"
                    />
                </div>
                
                <div>
                    <x-select wire:model.live="vehicle_id" class="w-full">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->license_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                        @endforeach
                    </x-select>
                </div>
                
                <div>
                    <x-select wire:model.live="category" class="w-full">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-select>
                </div>
                
                <div>
                    <x-select wire:model.live="approval_status" class="w-full">
                        <option value="">Tous les statuts</option>
                        <option value="draft">Brouillon</option>
                        <option value="pending_level1">En attente niveau 1</option>
                        <option value="pending_level2">En attente niveau 2</option>
                        <option value="approved">Approuvé</option>
                        <option value="rejected">Rejeté</option>
                    </x-select>
                </div>
            </div>

            {{-- Filtres avancés --}}
            <div x-data="{ showAdvanced: false }">
                <button @click="showAdvanced = !showAdvanced" class="text-sm text-primary-600 hover:text-primary-700">
                    <span x-show="!showAdvanced">Afficher plus de filtres</span>
                    <span x-show="showAdvanced">Masquer les filtres</span>
                </button>
                
                <div x-show="showAdvanced" x-collapse class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <x-select wire:model.live="supplier_id" class="w-full">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    
                    <div>
                        <x-select wire:model.live="expense_group_id" class="w-full">
                            <option value="">Tous les groupes</option>
                            @foreach($expenseGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    
                    <div>
                        <x-input type="date" wire:model.live="dateFrom" placeholder="Date début" class="w-full" />
                    </div>
                    
                    <div>
                        <x-input type="date" wire:model.live="dateTo" placeholder="Date fin" class="w-full" />
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Tableau des dépenses --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('expense_number')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                N° Dépense
                                @if($sortField === 'expense_number')
                                    <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <button wire:click="sortBy('expense_date')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Date
                                @if($sortField === 'expense_date')
                                    <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Véhicule
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Catégorie
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Description
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Fournisseur
                        </th>
                        <th class="px-4 py-3 text-right">
                            <button wire:click="sortBy('total_amount')" class="flex items-center gap-1 font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300 ml-auto">
                                Montant
                                @if($sortField === 'total_amount')
                                    <x-iconify icon="{{ $sortDirection === 'asc' ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-bold' }}" class="w-4 h-4" />
                                @endif
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Statut
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" 
                                   wire:model.live="selectedExpenses" 
                                   value="{{ $expense->id }}"
                                   class="rounded">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $expense->expense_number }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $expense->expense_date?->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <x-iconify icon="solar:car-bold" class="w-4 h-4 text-gray-400" />
                                <span class="text-sm text-gray-900 dark:text-white">
                                    {{ $expense->vehicle?->license_plate ?? 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                {{ $categories[$expense->category] ?? $expense->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-900 dark:text-white truncate max-w-xs">
                                {{ $expense->description }}
                            </p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $expense->supplier?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ number_format($expense->total_amount, 2) }} DZD
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @switch($expense->approval_status)
                                @case('draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100">
                                        Brouillon
                                    </span>
                                    @break
                                @case('pending_level1')
                                @case('pending_level2')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                        En attente
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                        Approuvé
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                        Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.vehicle-expenses.show', $expense) }}" 
                                   class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded-lg transition"
                                   title="Voir">
                                    <x-iconify icon="solar:eye-bold" class="w-4 h-4" />
                                </a>
                                
                                <a href="{{ route('admin.vehicle-expenses.edit', $expense) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900 rounded-lg transition"
                                   title="Éditer">
                                    <x-iconify icon="solar:pen-bold" class="w-4 h-4" />
                                </a>
                                
                                @if(in_array($expense->approval_status, ['pending_level1', 'pending_level2']))
                                <button wire:click="approveExpense({{ $expense->id }})"
                                        class="p-1.5 text-green-600 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-900 rounded-lg transition"
                                        title="Approuver">
                                    <x-iconify icon="solar:check-circle-bold" class="w-4 h-4" />
                                </button>
                                @endif
                                
                                <button wire:click="confirmDelete({{ $expense->id }})"
                                        class="p-1.5 text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900 rounded-lg transition"
                                        title="Supprimer">
                                    <x-iconify icon="solar:trash-bin-2-bold" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center">
                                <x-iconify icon="solar:box-minimalistic-broken" class="w-12 h-12 mb-2 text-gray-300 dark:text-gray-600" />
                                <p>Aucune dépense trouvée</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $expenses->links() }}
        </div>
        @endif
    </x-card>

    {{-- Modal de suppression --}}
    @if($showDeleteModal)
    <x-modal wire:model="showDeleteModal" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 dark:bg-red-900 rounded-full">
                <x-iconify icon="solar:trash-bin-2-bold" class="w-6 h-6 text-red-600 dark:text-red-400" />
            </div>
            
            <h3 class="text-lg font-medium text-center text-gray-900 dark:text-white mb-2">
                Confirmer la suppression
            </h3>
            
            <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-6">
                Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.
            </p>
            
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    Annuler
                </button>
                <button wire:click="deleteExpense" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Supprimer
                </button>
            </div>
        </div>
    </x-modal>
    @endif
</div>
