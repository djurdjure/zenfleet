@extends('layouts.admin.catalyst')

@section('title', 'Gestion Enterprise des Véhicules')

@section('content')
{{-- 🚗 Header Enterprise avec Analytics --}}
<div class="zenfleet-enterprise-header">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-3xl font-black leading-7 text-gray-900 sm:truncate sm:text-4xl sm:tracking-tight">
                <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 bg-clip-text text-transparent">
                    🚗 Gestion Enterprise des Véhicules
                </span>
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-chart-line mr-1.5 h-5 w-5 flex-shrink-0 text-blue-500"></i>
                    Système Ultra-Professionnel ZenFleet Enterprise
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-shield-check mr-1.5 h-5 w-5 flex-shrink-0 text-emerald-500"></i>
                    Sécurité RBAC Granulaire
                </div>
            </div>
        </div>

        <div class="mt-5 flex lg:ml-4 lg:mt-0">
            <span class="hidden sm:block">
                <a href="{{ route('admin.vehicles.create') }}"
                   class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Nouveau Véhicule
                </a>
            </span>

            <span class="ml-3 hidden sm:block">
                <button type="button"
                        class="zenfleet-btn-enterprise-secondary"
                        onclick="exportVehicles()">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </span>

            <div class="relative ml-3 sm:hidden">
                <button type="button" class="zenfleet-btn-enterprise-mobile" id="mobile-menu-button">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 🔥 Analytics Dashboard Enterprise --}}
<div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    {{-- Total Véhicules --}}
    <div class="zenfleet-analytics-card bg-gradient-to-br from-blue-50 via-blue-100 to-indigo-100 border-blue-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-car text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-bold text-gray-700 truncate">Total Véhicules</dt>
                        <dd class="text-3xl font-black text-blue-700">{{ $analytics['total_vehicles'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Véhicules Disponibles --}}
    <div class="zenfleet-analytics-card bg-gradient-to-br from-emerald-50 via-green-100 to-emerald-100 border-emerald-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-bold text-gray-700 truncate">Disponibles</dt>
                        <dd class="text-3xl font-black text-emerald-700">{{ $analytics['available_vehicles'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Véhicules Affectés --}}
    <div class="zenfleet-analytics-card bg-gradient-to-br from-orange-50 via-amber-100 to-orange-100 border-orange-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-tie text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-bold text-gray-700 truncate">Affectés</dt>
                        <dd class="text-3xl font-black text-orange-700">{{ $analytics['assigned_vehicles'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Véhicules en Maintenance --}}
    <div class="zenfleet-analytics-card bg-gradient-to-br from-red-50 via-pink-100 to-red-100 border-red-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-wrench text-white text-xl"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-bold text-gray-700 truncate">Maintenance</dt>
                        <dd class="text-3xl font-black text-red-700">{{ $analytics['maintenance_vehicles'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🔍 Filtres Enterprise Avancés --}}
<div class="mt-8 bg-white shadow-lg rounded-2xl border border-gray-200/50">
    <div class="px-6 py-4 border-b border-gray-200/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <i class="fas fa-filter mr-2 text-blue-600"></i>
            Filtres Enterprise Avancés
        </h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('admin.vehicles.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Recherche --}}
                <div>
                    <label for="search" class="zenfleet-label">Recherche</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Plaque, VIN, Marque, Modèle..."
                           class="zenfleet-input-enterprise">
                </div>

                {{-- Type de véhicule --}}
                <div>
                    <label for="vehicle_type_id" class="zenfleet-label">Type de Véhicule</label>
                    <select name="vehicle_type_id" id="vehicle_type_id" class="zenfleet-input-enterprise">
                        <option value="">Tous les types</option>
                        @foreach($referenceData['vehicle_types'] ?? [] as $type)
                            <option value="{{ $type->id }}"
                                    {{ request('vehicle_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut --}}
                <div>
                    <label for="status_id" class="zenfleet-label">Statut</label>
                    <select name="status_id" id="status_id" class="zenfleet-input-enterprise">
                        <option value="">Tous les statuts</option>
                        @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
                            <option value="{{ $status->id }}"
                                    {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type de carburant --}}
                <div>
                    <label for="fuel_type_id" class="zenfleet-label">Carburant</label>
                    <select name="fuel_type_id" id="fuel_type_id" class="zenfleet-input-enterprise">
                        <option value="">Tous les carburants</option>
                        @foreach($referenceData['fuel_types'] ?? [] as $fuel)
                            <option value="{{ $fuel->id }}"
                                    {{ request('fuel_type_id') == $fuel->id ? 'selected' : '' }}>
                                {{ $fuel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <button type="submit"
                        class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>

                <a href="{{ route('admin.vehicles.index') }}"
                   class="zenfleet-btn-enterprise-secondary">
                    <i class="fas fa-times mr-2"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

{{-- 📊 Table Enterprise des Véhicules --}}
<div class="mt-8 bg-white shadow-lg rounded-2xl border border-gray-200/50 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2 text-indigo-600"></i>
            Liste des Véhicules ({{ $vehicles->total() ?? 0 }})
        </h3>
    </div>

    @if($vehicles && $vehicles->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                    <tr>
                        <th class="zenfleet-table-header">Plaque</th>
                        <th class="zenfleet-table-header">Véhicule</th>
                        <th class="zenfleet-table-header">Type</th>
                        <th class="zenfleet-table-header">Statut</th>
                        <th class="zenfleet-table-header">Kilométrage</th>
                        <th class="zenfleet-table-header">Année</th>
                        <th class="zenfleet-table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($vehicles as $vehicle)
                        <tr class="zenfleet-table-row">
                            <td class="zenfleet-table-cell">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <i class="fas fa-car text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $vehicle->registration_plate }}</div>
                                        <div class="text-sm text-gray-500">{{ $vehicle->vin }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="zenfleet-table-cell">
                                <div class="text-sm font-semibold text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                <div class="text-sm text-gray-500">{{ $vehicle->color }}</div>
                            </td>
                            <td class="zenfleet-table-cell">
                                <span class="zenfleet-badge zenfleet-badge-blue">
                                    {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="zenfleet-table-cell">
                                @php
                                    $statusColors = [
                                        'Disponible' => 'green',
                                        'Affecté' => 'yellow',
                                        'Maintenance' => 'red',
                                        'Hors service' => 'gray'
                                    ];
                                    $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
                                    $color = $statusColors[$statusName] ?? 'gray';
                                @endphp
                                <span class="zenfleet-badge zenfleet-badge-{{ $color }}">
                                    {{ $statusName }}
                                </span>
                            </td>
                            <td class="zenfleet-table-cell">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($vehicle->current_mileage) }} km</div>
                                @if(isset($vehicle->utilization_rate))
                                    <div class="text-sm text-gray-500">{{ $vehicle->utilization_rate * 100 }}% utilisation</div>
                                @endif
                            </td>
                            <td class="zenfleet-table-cell">
                                <div class="text-sm font-semibold text-gray-900">{{ $vehicle->manufacturing_year }}</div>
                                @if(isset($vehicle->age_years))
                                    <div class="text-sm text-gray-500">{{ $vehicle->age_years }} ans</div>
                                @endif
                            </td>
                            <td class="zenfleet-table-cell">
                                <div class="flex items-center space-x-2">
                                    @can('view_vehicles')
                                        <a href="{{ route('admin.vehicles.show', $vehicle) }}"
                                           class="zenfleet-action-btn zenfleet-action-btn-view"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endcan

                                    @can('edit_vehicles')
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                           class="zenfleet-action-btn zenfleet-action-btn-edit"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('delete_vehicles')
                                        <button type="button"
                                                onclick="deleteVehicle({{ $vehicle->id }})"
                                                class="zenfleet-action-btn zenfleet-action-btn-delete"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Enterprise --}}
        <div class="px-6 py-4 border-t border-gray-200/50">
            {{ $vehicles->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @else
        {{-- État vide --}}
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <i class="fas fa-car text-6xl"></i>
            </div>
            <h3 class="mt-2 text-lg font-bold text-gray-900">Aucun véhicule trouvé</h3>
            <p class="mt-1 text-sm text-gray-500">
                Commencez par ajouter un véhicule à votre flotte.
            </p>
            @can('create_vehicles')
                <div class="mt-6">
                    <a href="{{ route('admin.vehicles.create') }}"
                       class="zenfleet-btn-enterprise-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter le premier véhicule
                    </a>
                </div>
            @endcan
        </div>
    @endif
</div>

{{-- 🎯 Success Message Enterprise --}}
@if(session('success'))
<div class="fixed top-4 right-4 z-50" id="success-notification">
    <div class="zenfleet-notification zenfleet-notification-success">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeNotification('success-notification')" class="zenfleet-notification-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
/* 🎨 Enterprise Styles Ultra-Professionnels */
.zenfleet-enterprise-header {
    @apply bg-gradient-to-r from-white via-blue-50 to-purple-50 rounded-2xl border border-blue-200/50 p-6 mb-6;
}

.zenfleet-analytics-card {
    @apply bg-white rounded-2xl shadow-lg border-2 transition-all duration-300 hover:scale-105 hover:shadow-xl;
}

.zenfleet-table-header {
    @apply px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider;
}

.zenfleet-table-cell {
    @apply px-6 py-4 whitespace-nowrap;
}

.zenfleet-table-row {
    @apply hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200;
}

.zenfleet-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold;
}

.zenfleet-badge-blue { @apply bg-blue-100 text-blue-800; }
.zenfleet-badge-green { @apply bg-green-100 text-green-800; }
.zenfleet-badge-yellow { @apply bg-yellow-100 text-yellow-800; }
.zenfleet-badge-red { @apply bg-red-100 text-red-800; }
.zenfleet-badge-gray { @apply bg-gray-100 text-gray-800; }

.zenfleet-action-btn {
    @apply inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium transition-all duration-200;
}

.zenfleet-action-btn-view { @apply bg-blue-100 text-blue-700 hover:bg-blue-200; }
.zenfleet-action-btn-edit { @apply bg-yellow-100 text-yellow-700 hover:bg-yellow-200; }
.zenfleet-action-btn-delete { @apply bg-red-100 text-red-700 hover:bg-red-200; }

.zenfleet-notification {
    @apply max-w-sm w-full rounded-lg shadow-lg pointer-events-auto ring-1 ring-black ring-opacity-5;
}

.zenfleet-notification-success {
    @apply bg-green-50 border border-green-200;
}

.zenfleet-notification-close {
    @apply inline-flex text-green-400 hover:text-green-600 focus:outline-none;
}
</style>
@endpush

@push('scripts')
<script>
// 🚀 JavaScript Enterprise Ultra-Professionnel

function exportVehicles() {
    // TODO: Implement export functionality
    alert('Fonctionnalité d\'export en cours de développement');
}

function deleteVehicle(vehicleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')) {
        // TODO: Implement delete functionality
        alert('Fonctionnalité de suppression en cours de développement');
    }
}

function closeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }
}

// Auto-hide success notifications
document.addEventListener('DOMContentLoaded', function() {
    const successNotification = document.getElementById('success-notification');
    if (successNotification) {
        setTimeout(() => {
            successNotification.style.opacity = '0';
            setTimeout(() => successNotification.remove(), 300);
        }, 5000);
    }
});
</script>
@endpush