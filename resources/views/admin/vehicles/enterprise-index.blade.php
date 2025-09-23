{{-- resources/views/admin/vehicles/enterprise-index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Véhicules - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Animations pour les modales */
@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-scale-in {
    animation: scale-in 0.3s ease-out;
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
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tom-select .ts-control {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.tom-select.focus .ts-control {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- En-tête ultra-professionnel --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-car text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Gestion des Véhicules</h1>
                            <p class="text-gray-600 mt-1">Gestion de flotte ultra-moderne et professionnelle</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 md:mt-0 flex flex-wrap gap-3">
                <button type="button" class="action-button inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-download -ml-0.5 mr-2 h-4 w-4"></i>
                    Exporter
                </button>
                <a href="{{ route('admin.vehicles.import.show') }}" class="action-button inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm leading-4 font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <i class="fas fa-upload -ml-0.5 mr-2 h-4 w-4"></i>
                    Importer
                </a>
                <a href="{{ route('admin.vehicles.archived') }}" class="action-button inline-flex items-center px-3 py-2 border border-amber-300 shadow-sm text-sm leading-4 font-medium rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-200">
                    <i class="fas fa-archive -ml-0.5 mr-2 h-4 w-4"></i>
                    Archives
                </a>
                <button type="button" class="action-button inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-filter -ml-0.5 mr-2 h-4 w-4"></i>
                    Filtres avancés
                </button>
                <a href="{{ route('admin.vehicles.create') }}" class="action-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus -ml-1 mr-2 h-4 w-4"></i>
                    Nouveau véhicule
                </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques ultra-professionnelles --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-car text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total véhicules</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Disponibles</p>
                    <p class="text-2xl font-bold text-green-600">{{ $analytics['available_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-check text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Affectés</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $analytics['assigned_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-tools text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">En maintenance</p>
                    <p class="text-2xl font-bold text-red-600">{{ $analytics['maintenance_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres ultra-professionnels --}}
    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 mb-8">
        <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Filtres de recherche</h3>
            </div>
        </div>
        <div class="px-8 py-6">
            <form method="GET" action="{{ route('admin.vehicles.index') }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Plaque, VIN, marque..."
                               class="search-input block w-full rounded-md text-sm px-3 py-2 focus:outline-none">
                    </div>
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status_id" id="status_id" class="tom-select-status w-full">
                            <option value="">Tous les statuts</option>
                            @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
                                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="vehicle_type_id" id="vehicle_type_id" class="tom-select-type w-full">
                            <option value="">Tous les types</option>
                            @foreach($referenceData['vehicle_types'] ?? [] as $type)
                                <option value="{{ $type->id }}" {{ request('vehicle_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fuel_type_id" class="block text-sm font-medium text-gray-700 mb-1">Carburant</label>
                        <select name="fuel_type_id" id="fuel_type_id" class="tom-select-fuel w-full">
                            <option value="">Tous les carburants</option>
                            @foreach($referenceData['fuel_types'] ?? [] as $fuel)
                                <option value="{{ $fuel->id }}" {{ request('fuel_type_id') == $fuel->id ? 'selected' : '' }}>
                                    {{ $fuel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <a href="{{ route('admin.vehicles.index') }}" class="action-button inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </a>
                    <button type="submit" class="action-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table ultra-professionnelle --}}
    <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-gray-100">
        <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-white"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">
                    Liste des véhicules ({{ $vehicles->total() ?? 0 }})
                </h3>
            </div>
        </div>
        @if($vehicles && $vehicles->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Véhicule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Kilométrage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Année</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-car h-4 w-4 text-gray-500"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $vehicle->registration_plate }}</div>
                                            <div class="text-sm text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'Disponible' => 'bg-green-100 text-green-800',
                                            'Affecté' => 'bg-yellow-100 text-yellow-800',
                                            'Maintenance' => 'bg-red-100 text-red-800',
                                            'Hors service' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
                                        $colorClass = $statusColors[$statusName] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="status-indicator inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                        {{ $statusName }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($vehicle->current_mileage) }} km
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $vehicle->manufacturing_year }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium space-x-2">
                                    @can('view_vehicles')
                                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye h-4 w-4 inline"></i>
                                        </a>
                                    @endcan
                                    @can('edit_vehicles')
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit h-4 w-4 inline"></i>
                                        </a>
                                    @endcan
                                    @can('delete_vehicles')
                                        <button onclick="deleteVehicle({{ $vehicle->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash h-4 w-4 inline"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $vehicles->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-car mx-auto h-12 w-12 text-gray-400"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun véhicule</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un véhicule à votre flotte.</p>
                @can('create_vehicles')
                    <div class="mt-6">
                        <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus -ml-1 mr-2 h-5 w-5"></i>
                            Nouveau véhicule
                        </a>
                    </div>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TomSelect for all select elements
    ['tom-select-status', 'tom-select-type', 'tom-select-fuel'].forEach(className => {
        const element = document.querySelector('.' + className);
        if (element) {
            new TomSelect(element, {
                plugins: ['clear_button'],
                placeholder: 'Sélectionner...',
                allowEmptyOption: true,
                searchField: ['text'],
                render: {
                    no_results: function() {
                        return '<div class="no-results">Aucun résultat trouvé</div>';
                    }
                }
            });
        }
    });
});

function deleteVehicle(vehicleId) {
    // Trouver les informations du véhicule depuis le DOM
    const vehicleRow = document.querySelector(`button[onclick*="${vehicleId}"]`).closest('tr');
    const vehiclePlate = vehicleRow.querySelector('td:first-child .text-sm.font-medium').textContent;
    const vehicleBrand = vehicleRow.querySelector('td:first-child .text-sm.text-gray-500').textContent;

    showDeleteConfirmation(vehicleId, vehiclePlate, vehicleBrand);
}

function showDeleteConfirmation(vehicleId, plate, brand) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.innerHTML = `
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>

            <!-- Center the modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 animate-scale-in">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                        <i class="fas fa-exclamation-triangle h-8 w-8 text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            Archiver le véhicule
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 mb-4">
                                Voulez-vous archiver ce véhicule ? Cette action peut être annulée.
                            </p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-car h-5 w-5 text-blue-600 mr-2"></i>
                                    <div>
                                        <p class="font-semibold text-blue-900">${plate}</p>
                                        <p class="text-sm text-blue-700">${brand}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <div class="flex">
                                    <i class="fas fa-exclamation-triangle h-5 w-5 text-amber-600 mr-2 mt-0.5"></i>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Information importante</p>
                                        <p class="text-xs text-amber-700 mt-1">Le véhicule sera archivé et pourra être restauré depuis la section "Véhicules archivés".</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" onclick="confirmDelete(${vehicleId})"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-200 hover:scale-105">
                        <i class="fas fa-archive h-5 w-5 mr-2"></i>
                        Archiver le véhicule
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-all duration-200">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Animation d'entrée
    setTimeout(() => {
        modal.classList.add('opacity-100');
    }, 10);
}

function confirmDelete(vehicleId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/vehicles/${vehicleId}`;
    form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    document.body.appendChild(form);

    // Animation de sortie
    closeModal();

    // Petit délai pour l'animation avant de soumettre
    setTimeout(() => {
        form.submit();
    }, 300);
}

function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.z-50');
    if (modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}
</script>
@endpush