@extends('layouts.admin')

@section('title', 'Centre d\'Alertes Enterprise')

@push('styles')
<style>
 .alert-card {
 transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
 }
 .alert-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
 }
 .pulse-ring {
 animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
 }
 @keyframes pulse-ring {
 0% {
 transform: scale(.33);
 opacity: 1;
 }
 80%, 100% {
 transform: scale(2.33);
 opacity: 0;
 }
 }
</style>
@endpush

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6" x-data="alertsManager()" x-init="init()">
        <div class="mb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                    <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                    Centre d'Alertes
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ $stats['total_alerts'] }})</span>
                </h1>
                <p class="text-sm text-gray-600 ml-8.5">
                    Surveillance proactive de la flotte
                    <span class="ml-2 text-xs text-gray-500">Dernière mise à jour: <span x-text="lastUpdate"></span></span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Système actif
                </span>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                    Critiques: {{ $stats['critical_count'] }}
                </span>
            </div>
        </div>

        <x-page-analytics-grid columns="6">
            <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Critiques</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['critical_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Maintenance</p>
                        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['maintenance_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 border border-indigo-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:wrench" class="w-6 h-6 text-indigo-600" />
                    </div>
                </div>
            </div>

            <div class="bg-emerald-50 rounded-lg border border-emerald-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Budget</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['budget_overruns'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 border border-emerald-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:wallet" class="w-6 h-6 text-emerald-600" />
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg border border-orange-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Réparations</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['pending_repairs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:wrench" class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">En retard</p>
                        <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['overdue_maintenance'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 border border-yellow-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:clock" class="w-6 h-6 text-yellow-700" />
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total alertes</p>
                        <p class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['total_alerts'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="heroicons:bell-alert" class="w-6 h-6 text-blue-700" />
                    </div>
                </div>
            </div>
        </x-page-analytics-grid>

        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm mb-6">
            <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label for="filter-type" class="text-sm font-medium text-gray-700">Type:</label>
                        <select id="filter-type" x-model="filters.type" @change="applyFilters()"
                            class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous</option>
                            <option value="critical">Critiques</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="budget">Budget</option>
                            <option value="repair">Réparations</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="filter-priority" class="text-sm font-medium text-gray-700">Priorité:</label>
                        <select id="filter-priority" x-model="filters.priority" @change="applyFilters()"
                            class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes</option>
                            <option value="urgent">Urgente</option>
                            <option value="high">Haute</option>
                            <option value="medium">Moyenne</option>
                            <option value="low">Basse</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2 ml-auto">
                    <button @click="refreshAlerts()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <x-iconify icon="heroicons:arrow-path" class="h-4 w-4 mr-2" ::class="refreshing ? 'animate-spin' : ''" />
                        Actualiser
                    </button>
                    <button onclick="exportAlerts()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <x-iconify icon="heroicons:arrow-down-tray" class="h-4 w-4 mr-2" />
                        Exporter
                    </button>
                </div>
            </div>
        </div>

        @php
            $alertCoverage = [
                ['icon' => 'lucide:file-badge', 'title' => 'Conformité & Documents', 'desc' => 'Assurances, cartes grises, contrôles techniques, permis.'],
                ['icon' => 'lucide:wrench', 'title' => 'Maintenance & Inspections', 'desc' => 'Préventif, overdue, campagnes techniques.'],
                ['icon' => 'lucide:git-branch', 'title' => 'Conflits d\'Affectation', 'desc' => 'Ressources double-assignées, indisponibles.'],
                ['icon' => 'lucide:shield-alert', 'title' => 'Sécurité de Conduite', 'desc' => 'Excès de vitesse, freinages brusques, fatigue.'],
                ['icon' => 'lucide:gas-pump', 'title' => 'Carburant & Coûts', 'desc' => 'Surconsommation, anomalies, dépenses hors seuil.'],
                ['icon' => 'lucide:map-pin', 'title' => 'Géolocalisation & Zones', 'desc' => 'Sorties de zone, immobilisation prolongée.'],
            ];
        @endphp

        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <x-iconify icon="lucide:radar" class="w-5 h-5 text-blue-600" />
                    <h2 class="text-lg font-semibold text-gray-900">Catalogue d'alertes (à activer)</h2>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                    Préconfiguration
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($alertCoverage as $item)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-white border border-gray-300 rounded-full flex items-center justify-center">
                                <x-iconify icon="{{ $item['icon'] }}" class="w-5 h-5 text-gray-700" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $item['title'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if($criticalAlerts->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-red-100 border border-red-200 rounded-full mr-3">
                    <x-iconify icon="heroicons:exclamation-triangle" class="h-5 w-5 text-red-600" />
                </div>
                <h3 class="text-lg font-bold text-red-900">Alertes Critiques - Action Immédiate Requise</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($criticalAlerts as $alert)
                <div class="bg-white border border-red-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-red-900">{{ $alert->title }}</h4>
                            <p class="text-red-700 text-sm mt-1">{{ $alert->message }}</p>
                            <div class="flex items-center mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    Action requise
                                </span>
                            </div>
                        </div>
                        <button class="ml-4 text-red-600 hover:text-red-800 transition-colors">
                            <x-iconify icon="heroicons:arrow-right" class="h-5 w-5" />
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-indigo-50 border-b border-indigo-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <x-iconify icon="heroicons:wrench" class="h-5 w-5 text-indigo-700" />
                            <h3 class="text-base font-bold text-indigo-900">Alertes Maintenance</h3>
                        </div>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold border border-indigo-200">
                            {{ $maintenanceAlerts->count() }} alertes
                        </span>
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse($maintenanceAlerts as $alert)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors alert-card">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium                                    {{ $alert->alert_priority === 'overdue' ? 'bg-red-50 text-red-700 border border-red-200' :
                                    ($alert->alert_priority === 'urgent' ? 'bg-orange-50 text-orange-700 border border-orange-200' :
                                    'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        {{ ucfirst($alert->alert_priority) }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $alert->maintenance_type }}</span>
                                </div>
                                <h4 class="font-semibold text-gray-900">{{ $alert->registration_plate }}</h4>
                                <p class="text-sm text-gray-600">{{ $alert->brand }} {{ $alert->model }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Échéance: {{ \Carbon\Carbon::parse($alert->next_maintenance_date)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                @if($alert->alert_priority === 'overdue')
                                <div class="w-3 h-3 bg-red-500 rounded-full pulse-ring"></div>
                                @endif
                                <button class="text-indigo-600 hover:text-indigo-800 transition-colors">
                                    <x-iconify icon="heroicons:arrow-top-right-on-square" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <x-iconify icon="heroicons:check-circle" class="h-12 w-12 mx-auto mb-3 text-green-500" />
                        <p>Aucune alerte de maintenance</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="bg-emerald-50 border-b border-emerald-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <x-iconify icon="lucide:wallet" class="h-5 w-5 text-emerald-700" />
                            <h3 class="text-base font-bold text-emerald-900">Alertes Budget</h3>
                        </div>
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-semibold border border-emerald-200">
                            {{ $budgetAlerts->count() }} alertes
                        </span>
                    </div>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @forelse($budgetAlerts as $alert)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors alert-card">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium                                    {{ $alert->type === 'budget_overrun' ? 'bg-red-50 text-red-700 border border-red-200' :
                                    ($alert->type === 'budget_critical' ? 'bg-orange-50 text-orange-700 border border-orange-200' :
                                    'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                        {{ $alert->type === 'budget_overrun' ? 'Dépassé' : ($alert->type === 'budget_critical' ? 'Critique' : 'Attention') }}
                                    </span>
                                    <span class="text-sm font-semibold {{ $alert->utilization_percentage > 100 ? 'text-red-600' : 'text-orange-600' }}">
                                        {{ number_format($alert->utilization_percentage, 1) }}%
                                    </span>
                                </div>
                                <h4 class="font-semibold text-gray-900">{{ $alert->scope_description }}</h4>
                                <div class="mt-2">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Dépensé: {{ number_format($alert->spent_amount, 0) }} DA</span>
                                        <span>Budget: {{ number_format($alert->budgeted_amount, 0) }} DA</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $alert->utilization_percentage > 100 ? 'bg-red-500' : ($alert->utilization_percentage > 90 ? 'bg-orange-500' : 'bg-yellow-500') }}"
                                        style="width: {{ min($alert->utilization_percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        <x-iconify icon="heroicons:check-circle" class="h-12 w-12 mx-auto mb-3 text-green-500" />
                        <p>Tous les budgets sont sous contrôle</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mt-6">
            <div class="bg-orange-50 border-b border-orange-200 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <x-iconify icon="lucide:wrench" class="h-5 w-5 text-orange-700" />
                        <h3 class="text-base font-bold text-orange-900">Demandes de Réparation en Attente</h3>
                    </div>
                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-semibold border border-orange-200">
                        {{ $repairAlerts->count() }} demandes
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if($repairAlerts->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demande</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Délai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($repairAlerts as $repair)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Demande #{{ $repair->id }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($repair->message, 50) }}</div>
                                    <div class="text-xs text-gray-400">par {{ $repair->requested_by }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $repair->vehicle }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $repair->priority === 'urgent' ? 'bg-red-50 text-red-700 border border-red-200' :
                                ($repair->priority === 'high' ? 'bg-orange-50 text-orange-700 border border-orange-200' :
                                'bg-yellow-50 text-yellow-700 border border-yellow-200') }}">
                                    {{ ucfirst($repair->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                    {{ $repair->status === 'en_attente' ? 'En attente' : 'Accord initial' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="{{ $repair->days_pending > 7 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $repair->days_pending }} jour(s)
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('admin.repair-requests.show', $repair->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    Voir détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-12 text-center text-gray-500">
                    <x-iconify icon="heroicons:check-circle" class="h-12 w-12 mx-auto mb-4 text-green-500" />
                    <p class="text-lg">Aucune demande de réparation en attente</p>
                    <p class="text-sm">Toutes les demandes sont traitées</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function alertsManager() {
 return {
 filters: {
 type: '',
 priority: ''
 },
 refreshing: false,
 lastUpdate: new Date().toLocaleTimeString('fr-FR'),

 init() {
 // Actualisation automatique toutes les 30 secondes
 setInterval(() => {
 this.refreshAlerts();
 }, 30000);
 },

 applyFilters() {
 // Implémentation des filtres
 console.log('Applying filters:', this.filters);
 },

 async refreshAlerts() {
 this.refreshing = true;
 try {
 const response = await fetch('{{ route("admin.alerts.api") }}');
 const data = await response.json();
 this.lastUpdate = new Date().toLocaleTimeString('fr-FR');
 // Mettre à jour l'interface avec les nouvelles données
 } catch (error) {
 console.error('Erreur lors de l\'actualisation des alertes:', error);
 } finally {
 this.refreshing = false;
 }
 }
 }
}

function exportAlerts() {
 window.location.href = '{{ route("admin.alerts.export") }}';
}

// Actualisation automatique de la page toutes les 5 minutes
setTimeout(() => {
 window.location.reload();
}, 300000);
</script>
@endpush
@endsection
