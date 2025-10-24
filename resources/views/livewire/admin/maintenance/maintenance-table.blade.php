<div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
    
    {{-- Header Table avec Tri --}}
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <x-iconify icon="lucide:list" class="w-4 h-4" />
                Liste des Opérations
                <span class="text-xs font-normal text-gray-500">({{ $operations->total() }} résultats)</span>
            </h3>
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-600">Par page:</label>
                <select 
                    wire:model.live="perPage"
                    class="text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
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
                        {{-- Véhicule --}}
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

                        {{-- Type --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $operation->maintenanceType->getCategoryColor() }}"></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $operation->maintenanceType->name }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($operation->maintenanceType->category ?? '') }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $operation->scheduled_date?->format('d/m/Y') ?? 'Non définie' }}</div>
                            <div class="text-xs text-gray-500">
                                @if($operation->scheduled_date)
                                    {{ $operation->scheduled_date->diffForHumans() }}
                                @endif
                            </div>
                        </td>

                        {{-- Statut --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            {!! $operation->getStatusBadge() !!}
                        </td>

                        {{-- Fournisseur --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($operation->provider)
                                <div class="text-sm text-gray-900">{{ $operation->provider->name }}</div>
                                <div class="text-xs text-gray-500">{{ $operation->provider->contact_phone ?? '' }}</div>
                            @else
                                <span class="text-sm text-gray-400">Non défini</span>
                            @endif
                        </td>

                        {{-- Coût --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($operation->total_cost)
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($operation->total_cost, 0, ',', ' ') }} DA</div>
                            @else
                                <span class="text-sm text-gray-400">Non défini</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Voir --}}
                                <a href="{{ route('admin.maintenance.operations.show', $operation) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Voir détails">
                                    <x-iconify icon="lucide:eye" class="w-5 h-5" />
                                </a>

                                {{-- Éditer --}}
                                @can('update', $operation)
                                    <a href="{{ route('admin.maintenance.operations.edit', $operation) }}" 
                                       class="text-gray-600 hover:text-gray-900" title="Modifier">
                                        <x-iconify icon="lucide:pencil" class="w-5 h-5" />
                                    </a>
                                @endcan

                                {{-- Actions rapides selon statut --}}
                                @if($operation->status === 'planned')
                                    <button 
                                        wire:click="$dispatch('start-operation', { id: {{ $operation->id }} })"
                                        class="text-green-600 hover:text-green-900" 
                                        title="Démarrer">
                                        <x-iconify icon="lucide:play" class="w-5 h-5" />
                                    </button>
                                @endif

                                {{-- Supprimer --}}
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
                                    <button 
                                        wire:click="resetFilters"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700">
                                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                                        Réinitialiser les filtres
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($operations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $operations->links() }}
        </div>
    @endif

    {{-- Loading Indicator --}}
    <div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
        <div class="flex items-center gap-2 text-blue-600">
            <x-iconify icon="lucide:loader" class="w-5 h-5 animate-spin" />
            <span class="text-sm font-medium">Chargement...</span>
        </div>
    </div>
</div>
