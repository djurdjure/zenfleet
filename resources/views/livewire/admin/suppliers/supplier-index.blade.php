<div>
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                Gestion des Fournisseurs
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $suppliers->total() }})
                </span>
            </h1>

            <div
                class="flex items-center gap-2 text-blue-600 opacity-0 transition-opacity duration-150"
                wire:loading.delay.class="opacity-100"
                wire:loading.delay.class.remove="opacity-0"
                wire:target="search,supplier_type,category_id,wilaya,is_active,is_preferred,is_certified,min_rating,sort_by,sort_direction,perPage">
                <x-iconify icon="lucide:loader-2" class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Chargement...</span>
            </div>
        </div>

        <x-page-analytics-grid columns="4">
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total fournisseurs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analytics['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Actifs</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $analytics['active'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Préférés</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $analytics['preferred'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:heart" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg border border-purple-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Certifiés</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $analytics['certified'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:badge-check" class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        @php
            $activeFilters = collect([
                $search,
                $supplier_type,
                $category_id,
                $wilaya,
                $is_active,
                $is_preferred,
                $is_certified,
                $min_rating,
            ])->filter(fn($value) => $value !== '' && $value !== null);
            $activeCount = $activeFilters->count();
            $exportParams = array_filter([
                'search' => $search,
                'supplier_type' => $supplier_type,
                'category_id' => $category_id,
                'wilaya' => $wilaya,
                'is_active' => $is_active,
                'is_preferred' => $is_preferred,
                'is_certified' => $is_certified,
                'min_rating' => $min_rating,
            ], fn($value) => $value !== '' && $value !== null);
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
                        placeholder="Rechercher par nom, contact, téléphone, email..."
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
                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
            </x-slot:filters>

            <x-slot:actions>
                <a href="{{ route('admin.suppliers.export', $exportParams) }}"
                    title="Exporter"
                    class="inline-flex items-center gap-2 p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:download" class="w-5 h-5 text-gray-500" />
                </a>
                @can('suppliers.create')
                <a href="{{ route('admin.suppliers.create') }}"
                    title="Nouveau Fournisseur"
                    class="inline-flex items-center gap-2 p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:plus" class="w-5 h-5" />
                </a>
                @endcan
            </x-slot:actions>

            <x-slot:filtersPanel>
                <x-page-filters-panel columns="4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <select wire:model.live="supplier_type" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous les types</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catégorie</label>
                        <select wire:model.live="category_id" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Wilaya</label>
                        <select wire:model.live="wilaya" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Toutes les wilayas</option>
                            @foreach($wilayas as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select wire:model.live="is_active" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            <option value="1">Actifs</option>
                            <option value="0">Inactifs</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Préféré</label>
                        <select wire:model.live="is_preferred" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Certifié</label>
                        <select wire:model.live="is_certified" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">Tous</option>
                            <option value="1">Oui</option>
                            <option value="0">Non</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Note minimale</label>
                        <input wire:model.live="min_rating" type="number" min="0" max="5" step="0.1" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="0-5">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tri</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select wire:model.live="sort_by" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="company_name">Nom</option>
                                <option value="rating">Rating</option>
                                <option value="quality_score">Qualité</option>
                                <option value="reliability_score">Fiabilité</option>
                            </select>
                            <select wire:model.live="sort_direction" class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="asc">Asc</option>
                                <option value="desc">Desc</option>
                            </select>
                        </div>
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

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localisation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-iconify icon="lucide:building-2" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $supplier->company_name }}</div>
                                        @if($supplier->trade_register)
                                        <div class="text-xs text-gray-500">RC: {{ $supplier->trade_register }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ \App\Models\Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $supplier->contact_first_name }} {{ $supplier->contact_last_name }}</div>
                                @if($supplier->contact_phone)
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <x-iconify icon="lucide:phone" class="w-3 h-3" />
                                    {{ $supplier->contact_phone }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplier->wilaya)
                                <div class="flex items-center gap-1 text-sm text-gray-900">
                                    <x-iconify icon="lucide:map-pin" class="w-4 h-4 text-gray-400" />
                                    {{ \App\Models\Supplier::WILAYAS[$supplier->wilaya] ?? $supplier->wilaya }}
                                </div>
                                @endif
                                @if($supplier->city)
                                <div class="text-xs text-gray-500">{{ $supplier->city }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplier->rating)
                                <div class="flex items-center gap-1">
                                    <x-iconify icon="lucide:star" class="w-4 h-4 text-yellow-400 fill-current" />
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($supplier->rating, 1) }}</span>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">Non noté</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($supplier->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Actif</span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">Inactif</span>
                                    @endif
                                    @if($supplier->is_preferred)
                                    <x-iconify icon="lucide:heart" class="w-4 h-4 text-red-500 fill-current" title="Préféré" />
                                    @endif
                                    @if($supplier->is_certified)
                                    <x-iconify icon="lucide:badge-check" class="w-4 h-4 text-purple-500" title="Certifié" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('suppliers.view')
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 group"
                                        title="Voir">
                                        <x-iconify icon="lucide:eye" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan
                                    @can('suppliers.update')
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                        class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-all duration-200 group"
                                        title="Modifier">
                                        <x-iconify icon="lucide:edit-3" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                    </a>
                                    @endcan
                                    @can('suppliers.delete')
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce fournisseur ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 rounded-full bg-gray-50 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200 group"
                                            title="Archiver">
                                            <x-iconify icon="lucide:archive" class="w-4 h-4 group-hover:scale-110 transition-transform" />
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-iconify icon="lucide:building-2" class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun fournisseur trouvé</h3>
                                    <p class="text-gray-600 mb-4">
                                        @if($activeCount > 0)
                                        Aucun résultat ne correspond à vos critères de recherche.
                                        @else
                                        Commencez par ajouter un fournisseur.
                                        @endif
                                    </p>
                                    @can('suppliers.create')
                                    <a href="{{ route('admin.suppliers.create') }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                                        <x-iconify icon="lucide:plus" class="w-4 h-4" />
                                        Créer un fournisseur
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$suppliers" :records-per-page="$perPage" wire:model.live="perPage" />
        </div>
    </div>
</div>
