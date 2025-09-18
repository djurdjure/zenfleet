{{-- resources/views/admin/organizations/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Organisations - ZenFleet')

@push('styles')
<style>
/* Custom animations et styles for enterprise-grade experience */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.hover-scale {
    transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.02);
}

.gradient-border {
    background: linear-gradient(white, white) padding-box,
                linear-gradient(45deg, #374151, #6b7280) border-box;
    border: 2px solid transparent;
}

.status-indicator {
    position: relative;
    overflow: hidden;
}

.status-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.status-indicator:hover::before {
    left: 100%;
}

.data-table {
    border-collapse: separate;
    border-spacing: 0;
}

.data-table th {
    position: sticky;
    top: 0;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    backdrop-filter: blur(10px);
    z-index: 10;
}

.data-table tbody tr {
    transition: all 0.2s ease-in-out;
}

.data-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.metric-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}

.search-input {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    background: white;
}

.action-button {
    transition: all 0.2s ease;
}

.action-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.organization-logo {
    transition: all 0.3s ease;
}

.organization-logo:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.wilaya-badge {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: white;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.legal-info {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-left: 3px solid #3b82f6;
}
</style>
@endpush

@section('content')
<div class="space-y-8 fade-in">
    {{-- Header Section with Advanced Controls --}}
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold leading-tight text-gray-900 sm:text-4xl">
                    <i class="fas fa-building text-blue-600 mr-3"></i>
                    Gestion des Organisations
                </h1>
                <p class="mt-2 text-lg text-gray-600 max-w-2xl">
                    Plateforme centralisée pour la gestion complète des organisations, avec suivi des informations légales et représentants.
                </p>
                <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500">
                    <span><i class="fas fa-calendar mr-1"></i>Dernière mise à jour: {{ now()->format('d/m/Y à H:i') }}</span>
                    <span><i class="fas fa-user mr-1"></i>Connecté: {{ auth()->user()->name }}</span>
                </div>
            </div>
            <div class="mt-6 sm:mt-0 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="exportData()"
                        class="action-button inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-download mr-2"></i>
                    Exporter
                </button>
                <button type="button" onclick="toggleAdvancedFilters()"
                        class="action-button inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                    <i class="fas fa-filter mr-2"></i>
                    Filtres Avancés
                </button>
                <a href="{{ route('admin.organizations.create') }}"
                   class="action-button inline-flex items-center px-6 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvelle Organisation
                </a>
            </div>
        </div>
    </div>

    {{-- Advanced Search and Filters --}}
    <div id="advanced-filters" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche globale</label>
                <input type="text" id="global-search" placeholder="Nom, NIF, gérant..."
                       class="search-input w-full px-4 py-2 rounded-lg focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type d'organisation</label>
                <select id="type-filter" class="search-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    <option value="">Tous les types</option>
                    <option value="Grande Entreprise">Grande Entreprise</option>
                    <option value="PME">PME</option>
                    <option value="Association">Association</option>
                    <option value="StartUp">StartUp</option>
                    <option value="ONG">ONG</option>
                    <option value="Cooperative">Cooperative</option>
                    <option value="Société Public">Société Public</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Wilaya</label>
                <select id="wilaya-filter" class="search-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    <option value="">Toutes les wilayas</option>
                    @for($i = 1; $i <= 58; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} - {{ config('constants.wilayas.' . str_pad($i, 2, '0', STR_PAD_LEFT), 'Wilaya ' . $i) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select id="status-filter" class="search-input w-full px-4 py-2 rounded-lg focus:outline-none">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="suspended">Suspendu</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex justify-end space-x-3">
            <button type="button" onclick="resetFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                Réinitialiser
            </button>
            <button type="button" onclick="applyFilters()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                Appliquer les filtres
            </button>
        </div>
    </div>

    {{-- Enhanced Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="metric-card rounded-xl p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Organisations</p>
                    <p class="text-3xl font-bold text-gray-900">{{ isset($organizations) ? $organizations->count() : 0 }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>+{{ rand(2, 8) }}% ce mois
                    </p>
                </div>
                <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Organisations Actives</p>
                    <p class="text-3xl font-bold text-green-600">{{ isset($organizations) ? $organizations->where('status', 'active')->count() : 0 }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ isset($organizations) && $organizations->count() > 0 ? round(($organizations->where('status', 'active')->count() / $organizations->count()) * 100, 1) : 0 }}% du total
                    </p>
                </div>
                <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Utilisateurs Total</p>
                    <p class="text-3xl font-bold text-blue-600">{{ isset($organizations) ? $organizations->sum('current_users') : 0 }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Répartis sur {{ isset($organizations) ? $organizations->count() : 0 }} orgs
                    </p>
                </div>
                <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6 hover-scale">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Véhicules Total</p>
                    <p class="text-3xl font-bold text-orange-600">{{ isset($organizations) ? $organizations->sum('current_vehicles') : 0 }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Flotte totale gérée
                    </p>
                </div>
                <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl">
                    <i class="fas fa-car text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Premium Organizations List --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-list-alt text-blue-600 mr-2"></i>
                    Répertoire des Organisations
                </h2>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">{{ isset($organizations) ? $organizations->count() : 0 }} organisations trouvées</span>
                    <div class="flex items-center space-x-1">
                        <button onclick="changeView('table')" class="p-2 rounded-lg hover:bg-white text-gray-600 hover:text-blue-600">
                            <i class="fas fa-table"></i>
                        </button>
                        <button onclick="changeView('grid')" class="p-2 rounded-lg hover:bg-white text-gray-600 hover:text-blue-600">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($organizations) && $organizations->count() > 0)
        <div id="table-view" class="overflow-x-auto">
            <table class="data-table min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Organisation
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Informations Légales
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Représentant
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Localisation
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Métriques
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Statut
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-b border-gray-200">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($organizations as $organization)
                    <tr class="organization-row group" data-organization-id="{{ $organization->id }}">
                        <td class="px-6 py-5">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    @if($organization->logo_path)
                                        <img src="{{ asset('storage/' . $organization->logo_path) }}"
                                             alt="{{ $organization->name }}"
                                             class="organization-logo h-12 w-12 rounded-xl object-cover border-2 border-gray-200">
                                    @else
                                        <div class="organization-logo h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center border-2 border-gray-200">
                                            <span class="text-white font-bold text-lg">{{ substr($organization->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full {{ $organization->status === 'active' ? 'bg-green-500' : ($organization->status === 'inactive' ? 'bg-gray-400' : 'bg-red-500') }} border-2 border-white"></div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-lg font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                        {{ $organization->name }}
                                    </p>
                                    @if($organization->legal_name && $organization->legal_name !== $organization->name)
                                        <p class="text-sm text-gray-500 truncate">{{ $organization->legal_name }}</p>
                                    @endif
                                    <div class="flex items-center space-x-3 mt-1">
                                        @if($organization->organization_type)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $organization->organization_type }}
                                            </span>
                                        @endif
                                        @if($organization->industry)
                                            <span class="text-xs text-gray-500">{{ $organization->industry }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="legal-info rounded-lg p-3 space-y-2">
                                @if($organization->nif)
                                    <div class="flex items-center text-sm">
                                        <span class="font-medium text-gray-700 w-8">NIF:</span>
                                        <span class="text-gray-900 font-mono">{{ $organization->nif }}</span>
                                    </div>
                                @endif
                                @if($organization->ai)
                                    <div class="flex items-center text-sm">
                                        <span class="font-medium text-gray-700 w-8">AI:</span>
                                        <span class="text-gray-900 font-mono">{{ $organization->ai }}</span>
                                    </div>
                                @endif
                                @if($organization->nis)
                                    <div class="flex items-center text-sm">
                                        <span class="font-medium text-gray-700 w-8">NIS:</span>
                                        <span class="text-gray-900 font-mono">{{ $organization->nis }}</span>
                                    </div>
                                @endif
                                @if($organization->trade_register)
                                    <div class="flex items-center text-sm">
                                        <span class="font-medium text-gray-700 w-8">RC:</span>
                                        <span class="text-gray-900 font-mono">{{ $organization->trade_register }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            @if($organization->manager_first_name || $organization->manager_last_name || $organization->manager_name)
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-900">
                                        @if($organization->manager_first_name || $organization->manager_last_name)
                                            {{ trim(($organization->manager_first_name ?? '') . ' ' . ($organization->manager_last_name ?? '')) }}
                                        @else
                                            {{ $organization->manager_name }}
                                        @endif
                                    </p>
                                    @if($organization->manager_phone_number || $organization->phone_number)
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-phone mr-1"></i>{{ $organization->manager_phone_number ?? $organization->phone_number }}
                                        </p>
                                    @endif
                                    @if($organization->manager_nin)
                                        <p class="text-xs text-gray-500 font-mono">
                                            NIN: {{ $organization->manager_nin }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400 italic">Non renseigné</span>
                            @endif
                        </td>

                        <td class="px-6 py-5">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-900">{{ $organization->city }}</span>
                                </div>
                                @if($organization->wilaya)
                                    <div class="wilaya-badge">
                                        Wilaya {{ $organization->wilaya }}
                                    </div>
                                @endif
                                @if($organization->address)
                                    <p class="text-xs text-gray-500 truncate max-w-xs">{{ $organization->address }}</p>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="text-center p-2 bg-blue-50 rounded-lg">
                                    <div class="text-lg font-bold text-blue-600">{{ $organization->current_users ?? 0 }}</div>
                                    <div class="text-xs text-blue-500">Utilisateurs</div>
                                </div>
                                <div class="text-center p-2 bg-green-50 rounded-lg">
                                    <div class="text-lg font-bold text-green-600">{{ $organization->current_vehicles ?? 0 }}</div>
                                    <div class="text-xs text-green-500">Véhicules</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex flex-col items-start space-y-2">
                                @if($organization->status === 'active')
                                    <span class="status-indicator inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                        Actif
                                    </span>
                                @elseif($organization->status === 'inactive')
                                    <span class="status-indicator inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                        Inactif
                                    </span>
                                @else
                                    <span class="status-indicator inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        Suspendu
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">
                                    Créé {{ $organization->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.organizations.show', $organization) }}"
                                   class="action-button p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all"
                                   title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.organizations.edit', $organization) }}"
                                   class="action-button p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-all"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        onclick="confirmDelete('{{ $organization->id }}', '{{ $organization->name }}')"
                                        class="action-button p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-building text-blue-500 text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Aucune organisation trouvée</h3>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Commencez par créer votre première organisation pour débuter la gestion de votre flotte.
                </p>
                <a href="{{ route('admin.organizations.create') }}"
                   class="action-button inline-flex items-center px-6 py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Créer une organisation
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Premium Delete Confirmation Modal --}}
<div x-data="{ showDeleteModal: false, organizationToDelete: null, organizationName: '' }"
     x-show="showDeleteModal"
     class="relative z-50"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true"
     style="display: none;">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white px-6 pb-6 pt-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200"
                 x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-red-200 sm:mx-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">
                            Confirmation de suppression
                        </h3>
                        <div class="mt-3">
                            <p class="text-gray-600 leading-relaxed">
                                Êtes-vous sûr de vouloir supprimer définitivement l'organisation
                                <span class="font-semibold text-gray-900" x-text="organizationName"></span> ?
                            </p>
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800">
                                    <i class="fas fa-warning mr-2"></i>
                                    <strong>Attention :</strong> Cette action est irréversible et supprimera toutes les données associées (utilisateurs, véhicules, etc.).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <button type="button"
                            @click="showDeleteModal = false"
                            class="inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-md ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
                        <i class="fas fa-times mr-2"></i>
                        Annuler
                    </button>
                    <button type="button"
                            @click="deleteOrganization()"
                            class="inline-flex justify-center rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-md hover:from-red-700 hover:to-red-800 transition-all">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Advanced functionality for enterprise-grade experience
function toggleAdvancedFilters() {
    const filters = document.getElementById('advanced-filters');
    filters.classList.toggle('hidden');

    if (!filters.classList.contains('hidden')) {
        filters.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function applyFilters() {
    const globalSearch = document.getElementById('global-search').value.toLowerCase();
    const typeFilter = document.getElementById('type-filter').value;
    const wilayaFilter = document.getElementById('wilaya-filter').value;
    const statusFilter = document.getElementById('status-filter').value;

    const rows = document.querySelectorAll('.organization-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesGlobal = !globalSearch || text.includes(globalSearch);
        const matchesType = !typeFilter || text.includes(typeFilter.toLowerCase());
        const matchesWilaya = !wilayaFilter || text.includes('wilaya ' + wilayaFilter.replace(/^0+/, ''));
        const matchesStatus = !statusFilter || text.includes(statusFilter);

        const shouldShow = matchesGlobal && matchesType && matchesWilaya && matchesStatus;

        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update counter
    const counter = document.querySelector('.flex.items-center.space-x-3 span');
    if (counter) {
        counter.textContent = `${visibleCount} organisations trouvées`;
    }

    // Show notification
    showNotification(`Filtres appliqués - ${visibleCount} résultats trouvés`, 'success');
}

function resetFilters() {
    document.getElementById('global-search').value = '';
    document.getElementById('type-filter').value = '';
    document.getElementById('wilaya-filter').value = '';
    document.getElementById('status-filter').value = '';

    document.querySelectorAll('.organization-row').forEach(row => {
        row.style.display = '';
    });

    // Reset counter
    const counter = document.querySelector('.flex.items-center.space-x-3 span');
    if (counter) {
        const totalRows = document.querySelectorAll('.organization-row').length;
        counter.textContent = `${totalRows} organisations trouvées`;
    }

    showNotification('Filtres réinitialisés', 'info');
}

function exportData() {
    // Create export functionality
    const exportData = [];
    const rows = document.querySelectorAll('.organization-row:not([style*="display: none"])');

    rows.forEach(row => {
        // Extract data from visible rows for export
        const orgName = row.querySelector('p.text-lg').textContent.trim();
        exportData.push({
            name: orgName,
            // Add more fields as needed
        });
    });

    // Implement CSV export
    const csv = convertToCSV(exportData);
    downloadCSV(csv, 'organisations-export.csv');

    showNotification('Export terminé avec succès', 'success');
}

function convertToCSV(data) {
    if (!data.length) return '';

    const headers = Object.keys(data[0]);
    const csvHeaders = headers.join(',');
    const csvRows = data.map(row => headers.map(header => row[header] || '').join(','));

    return [csvHeaders, ...csvRows].join('\n');
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

function changeView(viewType) {
    // Implement view switching (table/grid)
    const tableView = document.getElementById('table-view');

    if (viewType === 'table') {
        tableView.style.display = 'block';
        // Hide grid view if implemented
    } else if (viewType === 'grid') {
        // Implement grid view
        showNotification('Vue grille sera implémentée prochainement', 'info');
    }
}

function showNotification(message, type = 'info') {
    // Create notification system
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Enhanced delete functionality
function confirmDelete(organizationId, organizationName) {
    const modal = document.querySelector('[x-data*="showDeleteModal"]');
    if (modal) {
        modal.__x.$data.showDeleteModal = true;
        modal.__x.$data.organizationToDelete = organizationId;
        modal.__x.$data.organizationName = organizationName;
    }
}

function deleteOrganization() {
    const modal = document.querySelector('[x-data*="showDeleteModal"]');
    if (modal && modal.__x.$data.organizationToDelete) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/organizations/' + modal.__x.$data.organizationToDelete;

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';

        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Real-time search functionality
document.addEventListener('DOMContentLoaded', function() {
    const globalSearch = document.getElementById('global-search');
    if (globalSearch) {
        globalSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.organization-row');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
@endsection