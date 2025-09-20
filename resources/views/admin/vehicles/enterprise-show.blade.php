@extends('layouts.admin.catalyst')

@section('title', 'Détails Véhicule Enterprise')

@section('content')
{{-- 🚗 Header Enterprise Ultra-Professionnel --}}
<div class="zenfleet-header-enterprise">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
                <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors">
                    <i class="fas fa-car mr-1"></i>
                    Véhicules
                </a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-blue-600 font-bold">{{ $vehicle->registration_plate }}</span>
            </nav>

            <h1 class="text-4xl font-black leading-tight text-gray-900 sm:text-5xl">
                <span class="bg-gradient-to-r from-green-600 via-blue-600 to-indigo-700 bg-clip-text text-transparent">
                    <i class="fas fa-car mr-3"></i>
                    {{ $vehicle->registration_plate }}
                </span>
            </h1>

            <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    <i class="fas fa-industry mr-2 h-5 w-5 text-blue-500"></i>
                    {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->manufacturing_year }})
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    @php
                        $statusColors = [
                            'Disponible' => 'text-green-600',
                            'Affecté' => 'text-amber-600',
                            'Maintenance' => 'text-red-600',
                            'Hors service' => 'text-gray-600'
                        ];
                        $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
                        $statusColor = $statusColors[$statusName] ?? 'text-gray-600';
                    @endphp
                    <i class="fas fa-circle mr-2 h-3 w-3 {{ $statusColor }}"></i>
                    {{ $statusName }}
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-600">
                    <i class="fas fa-road mr-2 h-5 w-5 text-purple-500"></i>
                    {{ number_format($vehicle->current_mileage) }} km
                </div>
            </div>
        </div>

        <div class="mt-5 lg:ml-4 lg:mt-0 flex space-x-3">
            @can('edit_vehicles')
                <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                   class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            @endcan

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="zenfleet-btn-enterprise-secondary">
                    <i class="fas fa-ellipsis-v mr-2"></i>
                    Actions
                </button>

                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-2xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>
                            Exporter PDF
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-history mr-2"></i>
                            Historique
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-clone mr-2"></i>
                            Dupliquer
                        </a>
                        @can('delete_vehicles')
                            <div class="border-t border-gray-100"></div>
                            <button onclick="deleteVehicle({{ $vehicle->id }})"
                                    class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer
                            </button>
                        @endcan
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.vehicles.index') }}"
               class="zenfleet-btn-enterprise-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>
</div>

{{-- 🔥 Analytics du Véhicule --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    {{-- Âge du Véhicule --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-calendar text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Âge du Véhicule</dt>
                    <dd class="text-3xl font-black text-blue-700">
                        {{ isset($analytics['age_years']) ? $analytics['age_years'] : (isset($vehicle->age_years) ? $vehicle->age_years : 'N/A') }}
                        <span class="text-sm font-medium text-gray-600">ans</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Taux d'Utilisation --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-percentage text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Utilisation</dt>
                    <dd class="text-3xl font-black text-green-700">
                        {{ isset($analytics['utilization_rate']) ? round($analytics['utilization_rate'] * 100) : (isset($vehicle->utilization_rate) ? round($vehicle->utilization_rate * 100) : 'N/A') }}
                        <span class="text-sm font-medium text-gray-600">%</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Dépréciation --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Dépréciation</dt>
                    <dd class="text-3xl font-black text-amber-700">
                        {{ isset($analytics['depreciation_rate']) ? round($analytics['depreciation_rate'] * 100) : (isset($vehicle->depreciation_rate) ? round($vehicle->depreciation_rate * 100) : 'N/A') }}
                        <span class="text-sm font-medium text-gray-600">%</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Coût de Maintenance --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-wrench text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Maintenance</dt>
                    <dd class="text-3xl font-black text-red-700">
                        {{ isset($analytics['maintenance_cost_total']) ? number_format($analytics['maintenance_cost_total']) : (isset($vehicle->maintenance_cost_total) ? number_format($vehicle->maintenance_cost_total) : 'N/A') }}
                        <span class="text-sm font-medium text-gray-600">€</span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- 📊 Informations Détaillées --}}
<div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
    {{-- Colonne Principale --}}
    <div class="lg:col-span-2 space-y-8">
        {{-- Informations Générales --}}
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Informations Générales</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Plaque d'Immatriculation</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $vehicle->registration_plate }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Numéro VIN</label>
                        <p class="mt-1 text-sm font-mono text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $vehicle->vin }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Marque et Modèle</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Couleur</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vehicle->color }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Année de Fabrication</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $vehicle->manufacturing_year }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Type de Véhicule</label>
                        <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $vehicle->vehicleType->name ?? 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Statut</label>
                        @php
                            $statusName = $vehicle->vehicleStatus->name ?? 'Inconnu';
                            $statusClass = match($statusName) {
                                'Disponible' => 'zenfleet-status-available',
                                'Affecté' => 'zenfleet-status-assigned',
                                'Maintenance' => 'zenfleet-status-maintenance',
                                default => 'zenfleet-status-inactive'
                            };
                        @endphp
                        <span class="mt-1 {{ $statusClass }}">
                            <i class="fas fa-circle mr-1"></i>
                            {{ $statusName }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">Nombre de Places</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $vehicle->seats }} places</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Spécifications Techniques --}}
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-cog text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Spécifications Techniques</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Carburant</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-gas-pump text-purple-600 mr-2"></i>
                        <span class="text-sm text-gray-900">{{ $vehicle->fuelType->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Transmission</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-cogs text-purple-600 mr-2"></i>
                        <span class="text-sm text-gray-900">{{ $vehicle->transmissionType->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Cylindrée</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-tachometer-alt text-purple-600 mr-2"></i>
                        <span class="text-sm text-gray-900">{{ number_format($vehicle->engine_displacement_cc) }} cc</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Puissance</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-bolt text-purple-600 mr-2"></i>
                        <span class="text-sm text-gray-900">{{ number_format($vehicle->power_hp) }} HP</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Kilométrage Initial</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-road text-purple-600 mr-2"></i>
                        <span class="text-sm text-gray-900">{{ number_format($vehicle->initial_mileage) }} km</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Kilométrage Actuel</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-tachometer-alt text-purple-600 mr-2"></i>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($vehicle->current_mileage) }} km</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations Financières --}}
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-euro-sign text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Informations Financières</h3>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Date d'Acquisition</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-calendar-plus text-emerald-600 mr-2"></i>
                        <span class="text-sm text-gray-900">
                            {{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d/m/Y') : 'N/A' }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Prix d'Achat</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-money-bill text-emerald-600 mr-2"></i>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($vehicle->purchase_price, 2) }} €</span>
                    </div>
                </div>

                @if($vehicle->current_value)
                <div>
                    <label class="block text-sm font-bold text-gray-700">Valeur Actuelle</label>
                    <div class="mt-1 flex items-center">
                        <i class="fas fa-chart-line text-emerald-600 mr-2"></i>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($vehicle->current_value, 2) }} €</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Notes --}}
        @if($vehicle->notes)
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-sticky-note text-white"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900">Notes et Observations</h3>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $vehicle->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Actions Rapides --}}
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-bolt text-white text-sm"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Actions Rapides</h4>
            </div>

            <div class="space-y-3">
                <button class="w-full zenfleet-btn-enterprise-secondary text-left">
                    <i class="fas fa-user-plus mr-2"></i>
                    Affecter Chauffeur
                </button>

                <button class="w-full zenfleet-btn-enterprise-secondary text-left">
                    <i class="fas fa-wrench mr-2"></i>
                    Programmer Maintenance
                </button>

                <button class="w-full zenfleet-btn-enterprise-secondary text-left">
                    <i class="fas fa-file-alt mr-2"></i>
                    Générer Rapport
                </button>

                <button class="w-full zenfleet-btn-enterprise-secondary text-left">
                    <i class="fas fa-camera mr-2"></i>
                    Ajouter Photos
                </button>
            </div>
        </div>

        {{-- Timeline d'Activité --}}
        @if(isset($timeline) && !empty($timeline))
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Timeline d'Activité</h4>
            </div>

            <div class="space-y-4">
                @foreach($timeline as $event)
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="{{ $event['icon'] }} text-gray-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $event['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $event['date'] }}</p>
                        @if(isset($event['description']))
                            <p class="text-xs text-gray-600 mt-1">{{ $event['description'] }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Recommandations --}}
        @if(isset($recommendations) && !empty($recommendations))
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-lightbulb text-white text-sm"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Recommandations</h4>
            </div>

            <div class="space-y-3">
                @foreach($recommendations as $recommendation)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start">
                        <i class="{{ $recommendation['icon'] }} text-yellow-600 mr-2 mt-1 text-sm"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $recommendation['title'] }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $recommendation['description'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Informations Système --}}
        <div class="zenfleet-form-enterprise">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-database text-white text-sm"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Informations Système</h4>
            </div>

            <div class="space-y-3 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>ID Véhicule:</span>
                    <span class="font-mono">{{ $vehicle->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Créé le:</span>
                    <span>{{ $vehicle->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Modifié le:</span>
                    <span>{{ $vehicle->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($vehicle->organization)
                <div class="flex justify-between">
                    <span>Organisation:</span>
                    <span>{{ $vehicle->organization->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
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

@push('scripts')
<script>
function deleteVehicle(vehicleId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')) {
        // TODO: Implement delete functionality with form submission
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