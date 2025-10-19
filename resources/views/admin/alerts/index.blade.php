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
<div class="space-y-8" x-data="alertsManager()" x-init="init()">
 {{-- Header Enterprise avec statistiques en temps réel --}}
 <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl shadow-2xl p-8 text-white">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-6">
 <div class="p-4 bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl shadow-lg">
 <x-iconify icon="heroicons:exclamation-triangle" class="h-12 w-12 text-white" stroke-width="2" / />
 </div>
 <div>
 <h1 class="text-4xl font-bold text-white mb-2">Centre d'Alertes Enterprise</h1>
 <p class="text-slate-300 text-lg">Surveillance et gestion proactive de votre flotte</p>
 <div class="flex items-center mt-3 space-x-4">
 <div class="flex items-center space-x-2">
 <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
 <span class="text-sm text-slate-300">Système actif</span>
 </div>
 <div class="text-sm text-slate-400">Dernière mise à jour: <span x-text="lastUpdate"></span></div>
 </div>
 </div>
 </div>
 <div class="text-right">
 <div class="grid grid-cols-2 gap-4">
 <div class="text-center">
 <div class="text-3xl font-bold text-red-400">{{ $stats['critical_count'] }}</div>
 <div class="text-sm text-slate-300">Critiques</div>
 </div>
 <div class="text-center">
 <div class="text-3xl font-bold text-orange-400">{{ $stats['total_alerts'] }}</div>
 <div class="text-sm text-slate-300">Total</div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Statistiques détaillées --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-red-100 rounded-full">
 <x-iconify icon="heroicons:exclamation-triangle" class="h-6 w-6 text-red-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">Critiques</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['critical_count'] }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-indigo-100 rounded-full">
 <x-iconify icon="heroicons:wrench" class="h-6 w-6 text-indigo-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">Maintenance</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['maintenance_count'] }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-emerald-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-emerald-100 rounded-full">
 <x-iconify icon="lucide:wallet" class="h-6 w-6 text-emerald-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">Budget</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['budget_overruns'] }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-orange-100 rounded-full">
 <x-iconify icon="lucide:wrench" class="h-6 w-6 text-orange-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">Réparations</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_repairs'] }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-yellow-100 rounded-full">
 <x-iconify icon="heroicons:clock" class="h-6 w-6 text-yellow-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">En retard</p>
 <p class="text-2xl font-bold text-gray-900">{{ $stats['overdue_maintenance'] }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
 <div class="flex items-center">
 <div class="p-3 bg-blue-100 rounded-full">
 <x-iconify icon="heroicons:arrow-trending-up" class="h-6 w-6 text-blue-600" / />
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-500">Tendance</p>
 <p class="text-2xl font-bold text-gray-900">+12%</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Filtres et actions --}}
 <div class="bg-white rounded-xl shadow-lg p-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <div class="flex items-center space-x-2">
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
 <div class="flex items-center space-x-2">
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
 <div class="flex items-center space-x-3">
 <button @click="refreshAlerts()"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
 <x-iconify icon="heroicons:arrow-path" class="h-4 w-4 mr-2" ::class="refreshing ? 'animate-spin' : ''" / />
 Actualiser
 </button>
 <button onclick="exportAlerts()"
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
 <x-iconify icon="heroicons:arrow-down-tray" class="h-4 w-4 mr-2" / />
 Exporter
 </button>
 </div>
 </div>
 </div>

 {{-- Alertes critiques --}}
 @if($criticalAlerts->count() > 0)
 <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-xl p-6">
 <div class="flex items-center mb-4">
 <div class="p-2 bg-red-500 rounded-full mr-3">
 <x-iconify icon="heroicons:exclamation-triangle" class="h-5 w-5 text-white" / />
 </div>
 <h3 class="text-xl font-bold text-red-900">Alertes Critiques - Action Immédiate Requise</h3>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 @foreach($criticalAlerts as $alert)
 <div class="bg-white border border-red-200 rounded-lg p-4 shadow-sm">
 <div class="flex items-start justify-between">
 <div class="flex-1">
 <h4 class="font-semibold text-red-900">{{ $alert->title }}</h4>
 <p class="text-red-700 text-sm mt-1">{{ $alert->message }}</p>
 <div class="flex items-center mt-2">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
 Action requise
 </span>
 </div>
 </div>
 <button class="ml-4 text-red-600 hover:text-red-800 transition-colors">
 <x-iconify icon="heroicons:arrow-right" class="h-5 w-5" / />
 </button>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
 {{-- Alertes de maintenance --}}
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <x-iconify icon="heroicons:wrench" class="h-6 w-6" / />
 <h3 class="text-xl font-bold">Alertes Maintenance</h3>
 </div>
 <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
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
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
 {{ $alert->alert_priority === 'overdue' ? 'bg-red-100 text-red-800' :
 ($alert->alert_priority === 'urgent' ? 'bg-orange-100 text-orange-800' :
 'bg-yellow-100 text-yellow-800') }}">
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
 <x-iconify icon="heroicons:arrow-top-right-on-square" class="h-4 w-4" / />
 </button>
 </div>
 </div>
 </div>
 @empty
 <div class="p-8 text-center text-gray-500">
 <x-iconify icon="heroicons:check-circle" class="h-12 w-12 mx-auto mb-3 text-green-500" / />
 <p>Aucune alerte de maintenance</p>
 </div>
 @endforelse
 </div>
 </div>

 {{-- Alertes budgétaires --}}
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-white">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <x-iconify icon="lucide:wallet" class="h-6 w-6" / />
 <h3 class="text-xl font-bold">Alertes Budget</h3>
 </div>
 <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
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
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
 {{ $alert->type === 'budget_overrun' ? 'bg-red-100 text-red-800' :
 ($alert->type === 'budget_critical' ? 'bg-orange-100 text-orange-800' :
 'bg-yellow-100 text-yellow-800') }}">
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
 <x-iconify icon="heroicons:check-circle" class="h-12 w-12 mx-auto mb-3 text-green-500" / />
 <p>Tous les budgets sont sous contrôle</p>
 </div>
 @endforelse
 </div>
 </div>
 </div>

 {{-- Alertes de réparation --}}
 <div class="bg-white rounded-xl shadow-lg overflow-hidden">
 <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6 text-white">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <x-iconify icon="lucide:wrench" class="h-6 w-6" / />
 <h3 class="text-xl font-bold">Demandes de Réparation en Attente</h3>
 </div>
 <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium">
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
 {{ $repair->priority === 'urgent' ? 'bg-red-100 text-red-800' :
 ($repair->priority === 'high' ? 'bg-orange-100 text-orange-800' :
 'bg-yellow-100 text-yellow-800') }}">
 {{ ucfirst($repair->priority) }}
 </span>
 </td>
 <td class="px-6 py-4">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
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
 <x-iconify icon="heroicons:check-circle" class="h-16 w-16 mx-auto mb-4 text-green-500" / />
 <p class="text-lg">Aucune demande de réparation en attente</p>
 <p class="text-sm">Toutes les demandes sont traitées</p>
 </div>
 @endif
 </div>
 </div>
</div>

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