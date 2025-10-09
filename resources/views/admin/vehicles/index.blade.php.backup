@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Véhicules')

@push('styles')
<style>
/* Animations ultra-modernes */
.fade-in {
    animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.98);
        filter: blur(2px);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }
}

/* Cartes métriques ultra-riches */
.metric-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
    border: 1px solid rgba(226, 232, 240, 0.8);
    backdrop-filter: blur(10px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.6s ease;
}

.metric-card:hover::before {
    left: 100%;
}

.metric-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 8px 16px rgba(0,0,0,0.1);
    border-color: rgba(99, 102, 241, 0.3);
}

/* Gradients d'icônes ultra-modernes */
.icon-gradient-blue {
    background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 50%, #1E3A8A 100%);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.icon-gradient-green {
    background: linear-gradient(135deg, #10B981 0%, #059669 50%, #047857 100%);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.icon-gradient-orange {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 50%, #B45309 100%);
    box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.icon-gradient-red {
    background: linear-gradient(135deg, #EF4444 0%, #DC2626 50%, #B91C1C 100%);
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
}

/* Table ultra-moderne */
.data-table {
    border-collapse: separate;
    border-spacing: 0;
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
}

.data-table thead th {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    backdrop-filter: blur(10px);
    position: sticky;
    top: 0;
    z-index: 20;
    border-bottom: 2px solid rgba(99, 102, 241, 0.1);
}

.data-table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.data-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    transform: translateY(-2px) scale(1.005);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.05);
    z-index: 10;
}

/* Animations des statuts */
.status-badge {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s ease;
}

.status-badge:hover::before {
    left: 100%;
}

/* Filtres ultra-modernes */
.filter-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(226, 232, 240, 0.6);
    box-shadow: 0 8px 32px rgba(0,0,0,0.06);
}

/* Boutons ultra-riches */
.btn-ultra {
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
}

.btn-ultra::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-ultra:hover::before {
    left: 100%;
}

.btn-ultra:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* Pagination ultra-moderne */
.pagination-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
    backdrop-filter: blur(15px);
    border-top: 1px solid rgba(99, 102, 241, 0.1);
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #94a3b8, #64748b);
    border-radius: 10px;
    transition: background 0.3s ease;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #64748b, #475569);
}

/* Effets de focus ultra-modernes */
input:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15), 0 8px 20px rgba(0,0,0,0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endpush

@section('content')
<div class="fade-in">
    {{-- Statistiques enterprise-grade --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 icon-gradient-blue rounded-xl flex items-center justify-center">
                    <x-lucide-car class="w-6 h-6 text-white" stroke-width="1.5"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total véhicules</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 icon-gradient-green rounded-xl flex items-center justify-center">
                    <x-lucide-check-circle class="w-6 h-6 text-white" stroke-width="1.5"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Disponibles</p>
                    <p class="text-2xl font-bold text-green-600">{{ $analytics['available_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 icon-gradient-orange rounded-xl flex items-center justify-center">
                    <x-lucide-user-check class="w-6 h-6 text-white" stroke-width="1.5"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Affectés</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $analytics['assigned_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="metric-card rounded-xl p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 icon-gradient-red rounded-xl flex items-center justify-center">
                    <x-lucide-wrench class="w-6 h-6 text-white" stroke-width="1.5"/>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">En maintenance</p>
                    <p class="text-2xl font-bold text-red-600">{{ $analytics['maintenance_vehicles'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Initialisation d'Alpine.js pour gérer la modale de suppression/archivage --}}
    <div x-data="{
            showConfirmModal: false,
            modalAction: '', // 'archive' ou 'delete'
            modalTitle: '',
            modalDescription: '',
            modalButtonText: '',
            modalButtonClass: '',
            modalIconClass: '',
            vehicleToProcess: {},
            formUrl: '',
            isForceDelete: false,

            openModal(event, action) {
                const button = event.currentTarget;
                this.vehicleToProcess = JSON.parse(button.dataset.vehicle);
                this.formUrl = button.dataset.url;
                this.modalAction = action;

                if (action === 'archive') {
                    this.modalTitle = 'Archiver le Véhicule';
                    this.modalDescription = `Êtes-vous sûr de vouloir archiver le véhicule ${this.vehicleToProcess.brand} ${this.vehicleToProcess.model} (${this.vehicleToProcess.registration_plate}) ? Il pourra être restauré plus tard.`;
                    this.modalButtonText = 'Confirmer l\'Archivage';
                    this.modalButtonClass = 'bg-yellow-600 hover:bg-yellow-700';
                    this.modalIconClass = 'text-yellow-600 bg-yellow-100';
                    this.isForceDelete = false;
                } else if (action === 'delete') {
                    this.modalTitle = 'Suppression Définitive';
                    this.modalDescription = `Cette action est irréversible. Toutes les données associées à ce véhicule (maintenances, affectations...) seront définitivement perdues. Confirmez-vous la suppression du véhicule ${this.vehicleToProcess.brand} ${this.vehicleToProcess.model} (${this.vehicleToProcess.registration_plate}) ?`;
                    this.modalButtonText = 'Supprimer Définitivement';
                    this.modalButtonClass = 'bg-red-600 hover:bg-red-700';
                    this.modalIconClass = 'text-red-600 bg-red-100';
                    this.isForceDelete = true;
                }
                this.showConfirmModal = true;
            }
        }" class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres et de la Recherche Enterprise --}}
            <div class="mb-6 filter-section p-6 rounded-2xl">
                <form action="{{ route('admin.vehicles.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Immat, marque, modèle..." class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                @foreach($referenceData['vehicle_statuses'] ?? [] as $status)
                                    <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                @foreach(['20', '50', '100'] as $value)
                                    <option value="{{ $value }}" @selected(request('per_page', '20') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="vehicle_type_id" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="vehicle_type_id" id="vehicle_type_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous les types</option>
                                @foreach($referenceData['vehicle_types'] ?? [] as $type)
                                    <option value="{{ $type->id }}" @selected(request('vehicle_type_id') == $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="fuel_type_id" class="block text-sm font-medium text-gray-700">Carburant</label>
                            <select name="fuel_type_id" id="fuel_type_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous les carburants</option>
                                @foreach($referenceData['fuel_types'] ?? [] as $fuel)
                                    <option value="{{ $fuel->id }}" @selected(request('fuel_type_id') == $fuel->id)>{{ $fuel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-3">
                        <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Réinitialiser
                        </a>
                        <button type="submit" class="btn-ultra inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                            <x-lucide-search class="w-4 h-4 mr-2" stroke-width="1.5"/>
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            

            {{-- Table Enterprise-Grade --}}
            <div class="bg-white shadow-sm overflow-hidden rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                                <x-lucide-list class="w-5 h-5 text-white" stroke-width="1.5"/>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">
                                Liste des véhicules ({{ $vehicles->total() ?? 0 }})
                            </h3>
                        </div>
                        <div class="flex space-x-2">
                            @can('create vehicles')
                                <a href="{{ route('admin.vehicles.import.show') }}" class="btn-ultra inline-flex items-center px-4 py-3 border border-transparent text-sm font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700">
                                    <x-lucide-upload class="w-4 h-4 mr-2" stroke-width="1.5"/>
                                    Importer
                                </a>
                                <a href="{{ route('admin.vehicles.create') }}" class="btn-ultra inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-primary-600 to-indigo-700 hover:from-primary-700 hover:to-indigo-800">
                                    <x-lucide-plus-circle class="w-4 h-4 mr-2" stroke-width="1.5"/>
                                    Nouveau véhicule
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                @if($vehicles && $vehicles->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="data-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilométrage</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($vehicles as $vehicle)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <x-lucide-car class="h-4 w-4 text-gray-500" stroke-width="1.5"/>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $vehicle->registration_plate }}</div>
                                                    <div class="text-sm text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $vehicle->vehicleType->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'Disponible' => 'bg-green-100 text-green-800',
                                                    'Affecté' => 'bg-orange-100 text-orange-800',
                                                    'Maintenance' => 'bg-red-100 text-red-800',
                                                    'Hors service' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
                                                $colorClass = $statusColors[$statusName] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                                {{ $statusName }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($vehicle->current_mileage) }} km
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            @can('view vehicles')
                                                <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                                    <x-lucide-eye class="h-4 w-4 inline" stroke-width="1.5"/>
                                                </a>
                                            @endcan
                                            @can('edit vehicles')
                                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                                    <x-lucide-edit class="h-4 w-4 inline" stroke-width="1.5"/>
                                                </a>
                                            @endcan
                                            @can('delete vehicles')
                                                <button onclick="deleteVehicle({{ $vehicle->id }})" class="text-red-600 hover:text-red-900 transition-colors">
                                                    <x-lucide-archive class="h-4 w-4 inline" stroke-width="1.5"/>
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination Enterprise Ultra-Moderne --}}
                    <div class="pagination-section px-6 py-4">
                        <div class="flex items-center justify-between">
                            {{-- Options de pagination à gauche --}}
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700">Affichage :</span>
                                    <form method="GET" action="{{ route('admin.vehicles.index') }}" class="inline">
                                        @foreach(request()->except('per_page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach
                                        <select name="per_page" onchange="showLoadingAndSubmit(this.form)" class="text-sm border-gray-300 rounded-lg focus:border-primary-500 focus:ring-primary-500 bg-white shadow-sm transition-all duration-300">
                                            @foreach([20, 50, 100] as $size)
                                                <option value="{{ $size }}" @selected(request('per_page', 20) == $size)>
                                                    {{ $size }} par page
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div class="text-sm text-gray-600">
                                    Affichage de {{ $vehicles->firstItem() ?? 0 }} à {{ $vehicles->lastItem() ?? 0 }} sur {{ $vehicles->total() ?? 0 }} véhicules
                                </div>
                            </div>

                            {{-- Navigation de pagination à droite --}}
                            <div class="flex-1 flex justify-end">
                                {{ $vehicles->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-lucide-car class="mx-auto h-12 w-12 text-gray-400" stroke-width="1.5"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun véhicule</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter un véhicule à votre flotte.</p>
                        @can('create vehicles')
                            <div class="mt-6">
                                <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                    <x-lucide-plus class="-ml-1 mr-2 h-5 w-5" stroke-width="1.5"/>
                                    Nouveau véhicule
                                </a>
                            </div>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Scripts Enterprise Ultra-Modernes --}}
@push('scripts')
<script>
// Effet de chargement pour pagination
function showLoadingAndSubmit(form) {
    const select = form.querySelector('select[name="per_page"]');
    select.style.opacity = '0.6';
    select.style.transform = 'scale(0.98)';

    // Animation de chargement
    const loadingSpinner = document.createElement('div');
    loadingSpinner.innerHTML = '<div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-primary-600 ml-2"></div>';
    select.parentNode.appendChild(loadingSpinner);

    setTimeout(() => form.submit(), 150);
}

// Animations au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Animation séquentielle des cartes métriques
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Animation des lignes de tableau
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease-out';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 300 + index * 50);
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
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 animate-scale-in">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
                        <svg class="h-8 w-8 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Archiver le véhicule</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600 mb-4">Voulez-vous archiver ce véhicule ? Cette action peut être annulée.</p>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-blue-900">${plate}</p>
                                        <p class="text-sm text-blue-700">${brand}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" onclick="confirmDelete(${vehicleId})"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-200">
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
    closeModal();
    setTimeout(() => form.submit(), 300);
}

function closeModal() {
    const modal = document.querySelector('.fixed.inset-0.z-50');
    if (modal) {
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.95)';
        setTimeout(() => modal.remove(), 300);
    }
}
</script>
@endpush

{{-- Ancienne modale Alpine.js (garder pour compatibilité) --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIconClass">
                        <x-lucide-alert-triangle x-show="modalAction === 'archive' || modalAction === 'delete'" class="h-6 w-6" stroke-width="1.5"/>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" x-text="modalTitle"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600" x-html="modalDescription"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="formUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto" :class="modalButtonClass" x-text="modalButtonText">
                        </button>
                    </form>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection