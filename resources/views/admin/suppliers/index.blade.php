@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Fournisseurs')

@section('content')
{{-- ====================================================================
 🏢 GESTION DES FOURNISSEURS V2.0 - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 Design cohérent avec modules Véhicules et Chauffeurs:
 ✨ Fond gris clair premium (bg-gray-50)
 ✨ Header compact moderne
 ✨ 8 Cards métriques riches
 ✨ Filtres avancés (7 critères)
 ✨ Table ultra-lisible
 ✨ Icônes Iconify cohérentes

 @version 2.0-World-Class-Enterprise
 @since 2025-10-23
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- HEADER ULTRA-COMPACT --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                Gestion des Fournisseurs
                <span class="ml-2 text-sm font-normal text-gray-500">
                    ({{ $suppliers->total() }})
                </span>
            </h1>
        </div>

        {{-- CARDS MÉTRIQUES ULTRA-PRO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Fournisseurs --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Total fournisseurs</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">{{ $analytics['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:building-2" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Actifs --}}
            <div class="bg-green-50 rounded-lg border border-green-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Actifs</p>
                        <p class="text-xl font-bold text-green-600 mt-1">{{ $analytics['active'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600" />
                    </div>
                </div>
            </div>

            {{-- Préférés --}}
            <div class="bg-red-50 rounded-lg border border-red-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Préférés</p>
                        <p class="text-xl font-bold text-red-600 mt-1">{{ $analytics['preferred'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:heart" class="w-5 h-5 text-red-600" />
                    </div>
                </div>
            </div>

            {{-- Certifiés --}}
            <div class="bg-purple-50 rounded-lg border border-purple-200 p-4 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600">Certifiés</p>
                        <p class="text-xl font-bold text-purple-600 mt-1">{{ $analytics['certified'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:badge-check" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS SUPPLÉMENTAIRES (Enterprise-Grade) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Top 5 par Rating --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-blue-200 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:star" class="w-4 h-4 text-blue-700" />
                    </div>
                    <h3 class="text-sm font-semibold text-blue-900">Top 5 - Rating</h3>
                </div>
                <div class="space-y-2">
                    @forelse($analytics['top_rated'] as $supplier)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-blue-900 truncate flex-1">{{ $supplier->company_name }}</span>
                        <span class="text-blue-700 font-bold ml-2">{{ number_format($supplier->rating, 1) }} ⭐</span>
                    </div>
                    @empty
                    <p class="text-xs text-blue-700">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            {{-- Top 5 par Qualité --}}
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg border border-emerald-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-emerald-200 border border-emerald-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:award" class="w-4 h-4 text-emerald-700" />
                    </div>
                    <h3 class="text-sm font-semibold text-emerald-900">Top 5 - Qualité</h3>
                </div>
                <div class="space-y-2">
                    @forelse($analytics['top_quality'] as $supplier)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-900 truncate flex-1">{{ $supplier->company_name }}</span>
                        <span class="text-emerald-700 font-bold ml-2">{{ number_format($supplier->quality_score, 1) }}%</span>
                    </div>
                    @empty
                    <p class="text-xs text-emerald-700">Aucune donnée</p>
                    @endforelse
                </div>
            </div>

            {{-- Distribution Géographique --}}
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 bg-purple-200 border border-purple-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:map-pin" class="w-4 h-4 text-purple-700" />
                    </div>
                    <h3 class="text-sm font-semibold text-purple-900">Top 5 - Wilayas</h3>
                </div>
                <div class="space-y-2">
                    @forelse($analytics['by_wilaya'] as $wilaya)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-purple-900">{{ \App\Models\Supplier::WILAYAS[$wilaya->wilaya] ?? $wilaya->wilaya }}</span>
                        <span class="text-purple-700 font-bold">{{ $wilaya->count }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-purple-700">Aucune donnée</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- BARRE RECHERCHE + FILTRES + ACTIONS --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mb-4">
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="space-y-4">
                {{-- Ligne 1: Recherche + Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Recherche --}}
                    <div class="flex-1 relative">
                        <x-iconify icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input type="text"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Rechercher par nom, contact, téléphone, email..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Bouton Filtres --}}
                    <button type="button"
                        onclick="document.getElementById('advancedFilters').classList.toggle('hidden')"
                        class="px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                        <x-iconify icon="lucide:filter" class="w-5 h-5" />
                        Filtres
                        @if(count(array_filter($filters)) > 1)
                        <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                            {{ count(array_filter($filters)) - 1 }}
                        </span>
                        @endif
                    </button>

                    {{-- Bouton Export --}}
                    <a href="{{ route('admin.suppliers.export', $filters) }}"
                        class="px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                        <x-iconify icon="lucide:download" class="w-5 h-5" />
                        Export
                    </a>

                    {{-- Bouton Créer --}}
                    @can('suppliers.create')
                    <a href="{{ route('admin.suppliers.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2 font-semibold shadow-sm">
                        <x-iconify icon="lucide:plus" class="w-5 h-5" />
                        Nouveau Fournisseur
                    </a>
                    @endcan
                </div>

                {{-- Filtres Avancés Collapsibles --}}
                <div id="advancedFilters" class="hidden space-y-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        {{-- Type --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="supplier_type" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Tous les types</option>
                                @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['supplier_type'] ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Wilaya --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wilaya</label>
                            <select name="wilaya" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Toutes les wilayas</option>
                                @foreach($wilayas as $code => $name)
                                <option value="{{ $code }}" {{ ($filters['wilaya'] ?? '') == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rating Minimum --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Min</label>
                            <select name="min_rating" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Tous</option>
                                <option value="4" {{ ($filters['min_rating'] ?? '') == 4 ? 'selected' : '' }}>4⭐ et +</option>
                                <option value="3" {{ ($filters['min_rating'] ?? '') == 3 ? 'selected' : '' }}>3⭐ et +</option>
                                <option value="2" {{ ($filters['min_rating'] ?? '') == 2 ? 'selected' : '' }}>2⭐ et +</option>
                            </select>
                        </div>

                        {{-- Actif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="is_active" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Tous</option>
                                <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Actifs</option>
                                <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactifs</option>
                            </select>
                        </div>

                        {{-- Préféré --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Préféré</label>
                            <select name="is_preferred" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Tous</option>
                                <option value="1" {{ ($filters['is_preferred'] ?? '') === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ ($filters['is_preferred'] ?? '') === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                        </div>

                        {{-- Certifié --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Certifié</label>
                            <select name="is_certified" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="">Tous</option>
                                <option value="1" {{ ($filters['is_certified'] ?? '') === '1' ? 'selected' : '' }}>Oui</option>
                                <option value="0" {{ ($filters['is_certified'] ?? '') === '0' ? 'selected' : '' }}>Non</option>
                            </select>
                        </div>

                        {{-- Par Page --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Par page</label>
                            <select name="per_page" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="15" {{ ($filters['per_page'] ?? 15) == 15 ? 'selected' : '' }}>15</option>
                                <option value="30" {{ ($filters['per_page'] ?? 15) == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ ($filters['per_page'] ?? 15) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($filters['per_page'] ?? 15) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>

                    {{-- Boutons Action Filtres --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2 text-sm font-semibold">
                            <x-iconify icon="lucide:filter" class="w-4 h-4" />
                            Appliquer
                        </button>
                        <a href="{{ route('admin.suppliers.index') }}"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2 text-sm font-medium">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABLE ULTRA-PRO --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fournisseur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Localisation
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rating
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- Fournisseur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                                        <x-iconify icon="lucide:building-2" class="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $supplier->company_name }}
                                        </div>
                                        @if($supplier->trade_register)
                                        <div class="text-xs text-gray-500">RC: {{ $supplier->trade_register }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ \App\Models\Supplier::TYPES[$supplier->supplier_type] ?? $supplier->supplier_type }}
                                </span>
                            </td>

                            {{-- Contact --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $supplier->contact_first_name }} {{ $supplier->contact_last_name }}
                                </div>
                                @if($supplier->contact_phone)
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <x-iconify icon="lucide:phone" class="w-3 h-3" />
                                    {{ $supplier->contact_phone }}
                                </div>
                                @endif
                            </td>

                            {{-- Localisation --}}
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

                            {{-- Rating --}}
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

                            {{-- Statut --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($supplier->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        Actif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                        Inactif
                                    </span>
                                    @endif
                                    @if($supplier->is_preferred)
                                    <x-iconify icon="lucide:heart" class="w-4 h-4 text-red-500 fill-current" title="Préféré" />
                                    @endif
                                    @if($supplier->is_certified)
                                    <x-iconify icon="lucide:badge-check" class="w-4 h-4 text-purple-500" title="Certifié" />
                                    @endif
                                </div>
                            </td>

                            {{-- Actions --}}
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
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        Aucun fournisseur trouvé
                                    </h3>
                                    <p class="text-gray-600 mb-4">
                                        @if(count(array_filter($filters)) > 0)
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

            {{-- Pagination --}}
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                <x-pagination-standard :paginator="$suppliers" :records-per-page="request('per_page', 15)" />
            </div>
        </div>

    </div>
</section>
@endsection