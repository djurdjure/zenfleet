{{-- resources/views/livewire/admin/organization-table.blade.php --}}
<div class="zenfleet-card overflow-hidden">
    <!-- Barre de filtres avancés -->
    <div class="bg-gray-50/50 border-b border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Recherche -->
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher par nom, email, ville..."
                        class="pl-11 w-full zenfleet-input"
                    />
                </div>
            </div>

            <!-- Filtres -->
            <div class="flex flex-wrap gap-3">
                <select wire:model.live="status" class="zenfleet-input min-w-[140px]">
                    <option value="">Tous les statuts</option>
                    @foreach($filterOptions['statuses'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="country" class="zenfleet-input min-w-[140px]">
                    <option value="">Tous les pays</option>
                    @foreach($filterOptions['countries'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="type" class="zenfleet-input min-w-[140px]">
                    <option value="">Tous les types</option>
                    @foreach($filterOptions['types'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                @if($search || $status || $country || $type)
                    <button 
                        wire:click="$set('search', ''); $set('status', ''); $set('country', ''); $set('type', '')"
                        class="zenfleet-btn bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs"
                    >
                        <i class="fas fa-times"></i>
                        Réinitialiser
                    </button>
                @endif
            </div>
        </div>

        <!-- Actions bulk si sélections -->
        @if(count($selectedOrganizations) > 0)
            <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-blue-700 font-medium">
                        {{ count($selectedOrganizations) }} organisation(s) sélectionnée(s)
                    </span>
                    <div class="flex gap-2">
                        <button 
                            wire:click="bulkDelete"
                            onclick="confirm('Confirmer la suppression ?') || event.stopImmediatePropagation()"
                            class="text-xs px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg"
                        >
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Table avec design moderne -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="zenfleet-table-header">
                <tr class="text-left">
                    <th class="w-8 p-4">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectAll"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                    </th>
                    
                    @foreach([
                        'name' => 'Organisation',
                        'status' => 'Statut', 
                        'users_count' => 'Utilisateurs',
                        'vehicles_count' => 'Véhicules',
                        'created_at' => 'Créée le'
                    ] as $field => $label)
                        <th 
                            wire:click="sortBy('{{ $field }}')"
                            class="p-4 text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:text-gray-900 transition-colors"
                        >
                            <div class="flex items-center gap-1">
                                {{ $label }}
                                @if($sortField === $field)
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-500"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    <th class="p-4 w-px">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($organizations as $org)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="p-4">
                            <input 
                                type="checkbox" 
                                wire:model.live="selectedOrganizations"
                                value="{{ $org->id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                        </td>
                        
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($org->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        <a href="{{ route('admin.organizations.show', $org) }}" 
                                           class="hover:text-blue-600 transition-colors">
                                            {{ $org->name }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $org->city }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            <button 
                                wire:click="toggleStatus({{ $org->id }})"
                                class="zenfleet-badge {{ match($org->status) {
                                    'active' => 'bg-green-100 text-green-700 hover:bg-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200', 
                                    'inactive' => 'bg-red-100 text-red-700 hover:bg-red-200',
                                    default => 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                } }} transition-colors"
                            >
                                <i class="fas fa-{{ match($org->status) {
                                    'active' => 'check-circle',
                                    'pending' => 'clock',
                                    'inactive' => 'times-circle',
                                    default => 'question-circle'
                                } }}"></i>
                                {{ ucfirst($org->status) }}
                            </button>
                        </td>

                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ $org->users_count }}</span>
                                <i class="fas fa-users text-gray-400 text-sm"></i>
                            </div>
                        </td>

                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ $org->vehicles_count }}</span>
                                <i class="fas fa-car text-gray-400 text-sm"></i>
                            </div>
                        </td>

                        <td class="p-4 text-sm text-gray-600">
                            {{ $org->created_at->format('d/m/Y') }}
                        </td>

                        <td class="p-4">
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a 
                                    href="{{ route('admin.organizations.show', $org) }}"
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    title="Voir"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a 
                                    href="{{ route('admin.organizations.edit', $org) }}"
                                    class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                    title="Modifier"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center">
                            <div class="flex flex-col items-center gap-4 text-gray-500">
                                <i class="fas fa-search text-4xl text-gray-300"></i>
                                <div>
                                    <p class="font-medium">Aucune organisation trouvée</p>
                                    <p class="text-sm">Essayez de modifier vos critères de recherche</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination moderne -->
    @if($organizations->hasPages())
        <div class="border-t border-gray-100 p-6">
            {{ $organizations->links() }}
        </div>
    @endif
</div>

