{{-- resources/views/livewire/admin/organization-table.blade.php --}}
<div class="bg-white rounded-2xl shadow-xl overflow-hidden" wire:loading.class.delay="opacity-50 transition-opacity">
    <!-- Barre de filtres ultra-pro -->
    <div class="bg-gray-50/70 border-b border-gray-200/80 p-4">
        <div class="flex flex-col lg:flex-row gap-3">
            <!-- Recherche -->
            <div class="flex-1" wire:loading.class="opacity-50">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                    <input 
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher une organisation..."
                        class="pl-11 w-full zenfleet-input bg-white/80 focus:bg-white transition-all"
                    />
                </div>
            </div>

            <!-- Filtres -->
            <div class="flex flex-wrap items-center gap-3" wire:loading.class="opacity-50">
                <select wire:model.live="status" class="zenfleet-input bg-white/80 focus:bg-white min-w-[150px] transition-all">
                    <option value="">Tous les statuts</option>
                    @foreach($filterOptions['statuses'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="country" class="zenfleet-input bg-white/80 focus:bg-white min-w-[150px] transition-all">
                    <option value="">Tous les pays</option>
                    @foreach($filterOptions['countries'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="type" class="zenfleet-input bg-white/80 focus:bg-white min-w-[150px] transition-all">
                    <option value="">Tous les types</option>
                    @foreach($filterOptions['types'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                @if($search || $status || $country || $type)
                    <button 
                        wire:click="$set('search', ''); $set('status', ''); $set('country', ''); $set('type', '')"
                        class="zenfleet-btn bg-gray-200/80 hover:bg-gray-300/80 text-gray-700 !px-4 !py-2.5 transition-all"
                        title="Réinitialiser les filtres"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- Actions bulk si sélections -->
        <div x-data="{ show: @entangle('selectedOrganizations').live.length > 0 }" x-show="show" x-transition.opacity.duration.300ms>
            <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-200/80 flex items-center justify-between">
                <span class="text-sm text-blue-800 font-semibold">
                    <span x-text="@this.selectedOrganizations.length"></span> organisation(s) sélectionnée(s)
                </span>
                <div class="flex gap-3">
                    <button 
                        wire:click="bulkDelete"
                        onclick="confirm('Êtes-vous sûr de vouloir supprimer les organisations sélectionnées ? Cette action est irréversible.') || event.stopImmediatePropagation()"
                        class="zenfleet-btn !px-4 !py-2 bg-red-600 hover:bg-red-700 text-white shadow-sm"
                    >
                        <i class="fas fa-trash-alt"></i> 
                        <span>Supprimer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 🗃️ Enterprise Data Table -->
    <div class="overflow-hidden rounded-2xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0 z-10">
                    <tr>
                        <th class="w-12 p-6 text-left">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAll"
                                    class="w-4 h-4 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition-colors"
                                />
                            </div>
                        </th>
                    
                        @foreach([
                            'name' => ['label' => 'Organisation', 'icon' => 'fa-building'],
                            'status' => ['label' => 'Statut', 'icon' => 'fa-circle'],
                            'users_count' => ['label' => 'Utilisateurs', 'icon' => 'fa-users'],
                            'vehicles_count' => ['label' => 'Véhicules', 'icon' => 'fa-car'],
                            'created_at' => ['label' => 'Création', 'icon' => 'fa-calendar']
                        ] as $field => $config)
                            <th
                                wire:click="sortBy('{{ $field }}')"
                                class="p-6 text-left cursor-pointer hover:bg-gray-100/60 transition-all duration-200 group"
                            >
                                <div class="flex items-center gap-3"
                                     wire:loading.class="opacity-50"
                                     wire:target="sortBy('{{ $field }}')">
                                    <i class="fas {{ $config['icon'] }} text-gray-400 text-sm group-hover:text-gray-600"></i>
                                    <span class="text-sm font-bold text-gray-700 uppercase tracking-wider group-hover:text-gray-900">
                                        {{ $config['label'] }}
                                    </span>
                                    <div class="flex items-center">
                                        @if($sortField === $field)
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-blue-600 text-xs"></i>
                                        @else
                                            <i class="fas fa-sort text-gray-300 text-xs group-hover:text-gray-400 transition-colors"></i>
                                        @endif
                                    </div>
                                </div>
                            </th>
                        @endforeach
                        <th class="p-6 text-right">
                            <span class="text-sm font-bold text-gray-700 uppercase tracking-wider">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($organizations as $org)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50/30 hover:to-indigo-50/30 transition-all duration-300 group">
                            <td class="p-6">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedOrganizations"
                                    value="{{ $org->id }}"
                                    class="w-4 h-4 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 transition-colors"
                                />
                            </td>
                        
                            <!-- Organization Info -->
                            <td class="p-6">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar/Logo -->
                                    <div class="relative">
                                        @if($org->logo_path)
                                            <img src="{{ Storage::url($org->logo_path) }}"
                                                 alt="{{ $org->name }}"
                                                 class="w-12 h-12 rounded-xl object-cover shadow-lg border-2 border-white">
                                        @else
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg border-2 border-white">
                                                {{ strtoupper(substr($org->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <!-- Status indicator -->
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white shadow-sm {{ match($org->status) {
                                            'active' => 'bg-green-500',
                                            'pending' => 'bg-yellow-500',
                                            'inactive' => 'bg-gray-400',
                                            default => 'bg-red-500'
                                        } }}"></div>
                                    </div>

                                    <!-- Organization Details -->
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.organizations.show', $org) }}"
                                               class="font-bold text-gray-900 hover:text-blue-700 transition-colors text-base leading-tight truncate">
                                                {{ $org->name }}
                                            </a>
                                            @if($org->organization_type === 'enterprise')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    <i class="fas fa-crown mr-1"></i> Enterprise
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-500">
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-map-marker-alt text-xs"></i>
                                                <span>{{ $org->city }}, {{ $org->country }}</span>
                                            </div>
                                            @if($org->industry)
                                                <div class="flex items-center gap-1">
                                                    <i class="fas fa-industry text-xs"></i>
                                                    <span class="truncate">{{ $org->industry }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="p-6">
                                <button
                                    wire:click="toggleStatus({{ $org->id }})"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-105 {{ match($org->status) {
                                        'active' => 'bg-green-50 text-green-700 ring-1 ring-green-200 hover:bg-green-100 hover:ring-green-300',
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-200 hover:bg-yellow-100 hover:ring-yellow-300',
                                        'inactive' => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200 hover:bg-gray-100 hover:ring-gray-300',
                                        'suspended' => 'bg-red-50 text-red-700 ring-1 ring-red-200 hover:bg-red-100 hover:ring-red-300',
                                        default => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200'
                                    } }}"
                                >
                                    <span class="w-2 h-2 rounded-full {{ match($org->status) {
                                        'active' => 'bg-green-500 animate-pulse',
                                        'pending' => 'bg-yellow-500',
                                        'inactive' => 'bg-gray-400',
                                        'suspended' => 'bg-red-500',
                                        default => 'bg-gray-400'
                                    } }}"></span>
                                    <span>
                                        @switch($org->status)
                                            @case('active') Actif @break
                                            @case('pending') En attente @break
                                            @case('inactive') Inactif @break
                                            @case('suspended') Suspendu @break
                                            @default {{ ucfirst($org->status) }}
                                        @endswitch
                                    </span>
                                </button>
                            </td>

                            <!-- Users Count -->
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ number_format($org->users_count) }}</div>
                                        <div class="text-xs text-gray-500">utilisateurs</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Vehicles Count -->
                            <td class="p-6">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ number_format($org->vehicles_count) }}</div>
                                        <div class="text-xs text-gray-500">véhicules</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Created Date -->
                            <td class="p-6">
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ $org->created_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Il y a {{ $org->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="p-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <!-- Quick Stats Tooltip -->
                                    <div class="group/tooltip relative">
                                        <button class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-all duration-200">
                                            <i class="fas fa-chart-bar text-gray-600 text-sm"></i>
                                        </button>
                                        <div class="absolute bottom-full right-0 mb-2 w-48 p-3 bg-gray-900 text-white rounded-lg shadow-xl opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 pointer-events-none">
                                            <div class="text-xs space-y-1">
                                                <div class="flex justify-between">
                                                    <span>Utilisateurs:</span>
                                                    <span class="font-semibold">{{ $org->users_count }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Véhicules:</span>
                                                    <span class="font-semibold">{{ $org->vehicles_count }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span>Plan:</span>
                                                    <span class="font-semibold">{{ ucfirst($org->subscription_plan ?? 'basic') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- View Button -->
                                    <a href="{{ route('admin.organizations.show', $org) }}"
                                       class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-700 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
                                       title="Voir les détails">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.organizations.edit', $org) }}"
                                       class="w-8 h-8 bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110"
                                       title="Modifier">
                                        <i class="fas fa-pencil-alt text-sm"></i>
                                    </a>

                                    <!-- More Actions Dropdown -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                                class="w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center transition-all duration-200"
                                                title="Plus d'actions">
                                            <i class="fas fa-ellipsis-v text-sm"></i>
                                        </button>
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             @click.away="open = false"
                                             class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 z-50">
                                            <div class="py-2">
                                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-download text-blue-500 w-4"></i>
                                                    Exporter
                                                </a>
                                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-copy text-green-500 w-4"></i>
                                                    Dupliquer
                                                </a>
                                                <hr class="my-2 border-gray-100">
                                                <button class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-trash text-red-500 w-4"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Empty State -->
                        <tr>
                            <td colspan="7" class="p-16 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Empty State Illustration -->
                                    <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center">
                                        <i class="fas fa-building text-4xl text-gray-400"></i>
                                    </div>

                                    <div class="text-center space-y-2">
                                        <h3 class="text-2xl font-bold text-gray-800">
                                            @if($search || $status || $country || $type)
                                                Aucun résultat pour cette recherche
                                            @else
                                                Aucune organisation enregistrée
                                            @endif
                                        </h3>
                                        <p class="text-gray-500 text-lg max-w-md mx-auto">
                                            @if($search || $status || $country || $type)
                                                Essayez d'ajuster vos critères de filtrage pour élargir les résultats.
                                            @else
                                                Commencez par créer votre première organisation pour démarrer la gestion multi-tenant.
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-4">
                                        @if($search || $status || $country || $type)
                                            <button
                                                wire:click="$set('search', ''); $set('status', ''); $set('country', ''); $set('type', '')"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all duration-200"
                                            >
                                                <i class="fas fa-filter"></i>
                                                Effacer les filtres
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.organizations.create') }}"
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                                            <i class="fas fa-plus"></i>
                                            Créer une organisation
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 📄 Advanced Pagination & Results Info -->
    @if($organizations->hasPages())
        <div class="border-t border-gray-100/80 bg-gradient-to-r from-gray-50/50 to-white/50 px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Results Info -->
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list text-gray-400"></i>
                        <span>
                            Affichage de
                            <span class="font-bold text-gray-900">{{ number_format($organizations->firstItem()) }}</span>
                            à
                            <span class="font-bold text-gray-900">{{ number_format($organizations->lastItem()) }}</span>
                            sur
                            <span class="font-bold text-blue-600">{{ number_format($organizations->total()) }}</span>
                            résultat{{ $organizations->total() > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <!-- Per Page Selector -->
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Par page:</span>
                        <select wire:model.live="perPage" class="px-2 py-1 border border-gray-200 rounded-lg text-sm focus:border-blue-400 focus:ring-1 focus:ring-blue-200 bg-white">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="flex items-center">
                    {{ $organizations->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    @else
        <!-- Results counter when no pagination -->
        <div class="border-t border-gray-100/80 bg-gradient-to-r from-gray-50/50 to-white/50 px-6 py-3">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="fas fa-list text-gray-400"></i>
                <span>
                    <span class="font-bold text-blue-600">{{ number_format($organizations->count()) }}</span>
                    résultat{{ $organizations->count() > 1 ? 's' : '' }} au total
                </span>
            </div>
        </div>
    @endif
</div>

