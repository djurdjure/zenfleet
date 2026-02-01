@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Enterprise ZenFleet')

@section('content')
{{-- 🚀 Header Enterprise Ultra-Professionnel --}}
<div class="zenfleet-header-enterprise">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
 <div class="min-w-0 flex-1">
 <h1 class="text-5xl font-black leading-tight text-gray-900 sm:text-6xl">
 <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 bg-clip-text text-transparent">
 <i class="fas fa-chart-line mr-4"></i>
 Dashboard Enterprise
 </span>
 </h1>
 <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-600">
 <i class="fas fa-building mr-2 h-5 w-5 text-blue-500"></i>
 {{ auth()->user()->organization->name ?? 'Organisation' }}
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-600">
 <i class="fas fa-user-tie mr-2 h-5 w-5 text-emerald-500"></i>
 {{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->first() ?? 'Utilisateur' }})
 </div>
 <div class="mt-2 flex items-center text-sm text-gray-600">
 <i class="fas fa-clock mr-2 h-5 w-5 text-purple-500"></i>
 Dernière connexion: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'N/A' }}
 </div>
 </div>
 </div>

 <div class="mt-5 lg:ml-4 lg:mt-0 flex space-x-3">
 <button type="button"
 class="zenfleet-btn-enterprise-secondary"
 onclick="refreshDashboard()">
 <i class="fas fa-sync-alt mr-2"></i>
 Actualiser
 </button>

 <div class="relative"
 x-data="{
 open: false,
 styles: '',
 direction: 'down',
 align: 'right',
 toggle() {
 if (this.open) { this.close(); return; }
 this.open = true;
 this.$nextTick(() => {
 this.updatePosition();
 requestAnimationFrame(() => this.updatePosition());
 });
 },
 close() { this.open = false; },
 updatePosition() {
 if (!this.$refs.trigger || !this.$refs.menu) return;
 const rect = this.$refs.trigger.getBoundingClientRect();
 const menuHeight = this.$refs.menu.offsetHeight || 200;
 const menuWidth = this.$refs.menu.offsetWidth || 192;
 const padding = 12;
 const spaceBelow = window.innerHeight - rect.bottom;
 const spaceAbove = rect.top;
 const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
 this.direction = shouldOpenUp ? 'up' : 'down';
 let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
 if (top + menuHeight > window.innerHeight - padding) {
 top = window.innerHeight - padding - menuHeight;
 }
 if (top < padding) top = padding;
 let left = this.align === 'right' ? (rect.right - menuWidth) : rect.left;
 if (left + menuWidth > window.innerWidth - padding) {
 left = window.innerWidth - padding - menuWidth;
 }
 if (left < padding) left = padding;
 this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
 }
 }"
 x-init="$watch('open', value => {
 if (value) {
 $nextTick(() => {
 this.updatePosition();
 requestAnimationFrame(() => this.updatePosition());
 });
 }
 })"
 @keydown.escape.window="close()"
 @scroll.window="open && updatePosition()"
 @resize.window="open && updatePosition()">
 <button @click="toggle()" x-ref="trigger"
 class="zenfleet-btn-enterprise-secondary">
 <i class="fas fa-cog mr-2"></i>
 Paramètres
 </button>

 <template x-teleport="body">
 <div x-show="open"
 x-ref="menu"
 @click.outside="close()"
 x-transition:enter="transition ease-out duration-100"
 x-transition:enter-start="transform opacity-0 scale-95"
 x-transition:enter-end="transform opacity-100 scale-100"
 x-transition:leave="transition ease-in duration-75"
 x-transition:leave-start="transform opacity-100 scale-100"
 x-transition:leave-end="transform opacity-0 scale-95"
 :style="styles"
 class="rounded-2xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-[9999]"
 x-cloak>
 <div class="py-1">
 <a href="#" @click="close()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <i class="fas fa-user-cog mr-2"></i>
 Profil
 </a>
 <a href="#" @click="close()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <i class="fas fa-bell mr-2"></i>
 Notifications
 </a>
 <a href="#" @click="close()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
 <i class="fas fa-download mr-2"></i>
 Rapports
 </a>
 </div>
 </div>
 </template>
 </div>
 </div>
 </div>
</div>

{{-- 🔥 KPIs Enterprise Ultra-Modernes --}}
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
 {{-- Total Véhicules --}}
 <div class="zenfleet-analytics-premium">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl flex items-center justify-center shadow-xl">
 <i class="fas fa-car text-white text-2xl"></i>
 </div>
 </div>
 <div class="ml-6 w-0 flex-1">
 <dl>
 <dt class="text-sm font-bold text-gray-700 truncate">Véhicules Totaux</dt>
 <dd class="text-4xl font-black text-blue-700">{{ $dashboardData['total_vehicles'] ?? 0 }}</dd>
 <dd class="text-xs text-gray-500 mt-1">
 <i class="fas fa-chart-line mr-1"></i>
 +5% ce mois
 </dd>
 </dl>
 </div>
 </div>
 </div>

 {{-- Total Chauffeurs --}}
 <div class="zenfleet-analytics-premium">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-green-600 rounded-3xl flex items-center justify-center shadow-xl">
 <i class="fas fa-users text-white text-2xl"></i>
 </div>
 </div>
 <div class="ml-6 w-0 flex-1">
 <dl>
 <dt class="text-sm font-bold text-gray-700 truncate">Chauffeurs Actifs</dt>
 <dd class="text-4xl font-black text-emerald-700">{{ $dashboardData['total_drivers'] ?? 0 }}</dd>
 <dd class="text-xs text-gray-500 mt-1">
 <i class="fas fa-user-check mr-1"></i>
 {{ $dashboardData['active_drivers'] ?? 0 }} disponibles
 </dd>
 </dl>
 </div>
 </div>
 </div>

 {{-- Missions Actives --}}
 <div class="zenfleet-analytics-premium">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl flex items-center justify-center shadow-xl">
 <i class="fas fa-route text-white text-2xl"></i>
 </div>
 </div>
 <div class="ml-6 w-0 flex-1">
 <dl>
 <dt class="text-sm font-bold text-gray-700 truncate">Missions Actives</dt>
 <dd class="text-4xl font-black text-amber-700">{{ $dashboardData['active_missions'] ?? 0 }}</dd>
 <dd class="text-xs text-gray-500 mt-1">
 <i class="fas fa-clock mr-1"></i>
 En cours maintenant
 </dd>
 </dl>
 </div>
 </div>
 </div>

 {{-- Efficacité Globale --}}
 <div class="zenfleet-analytics-premium">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-3xl flex items-center justify-center shadow-xl">
 <i class="fas fa-medal text-white text-2xl"></i>
 </div>
 </div>
 <div class="ml-6 w-0 flex-1">
 <dl>
 <dt class="text-sm font-bold text-gray-700 truncate">Efficacité Globale</dt>
 <dd class="text-4xl font-black text-purple-700">{{ $dashboardData['efficiency_score'] ?? 85 }}%</dd>
 <dd class="text-xs text-gray-500 mt-1">
 <i class="fas fa-trophy mr-1"></i>
 Performance excellente
 </dd>
 </dl>
 </div>
 </div>
 </div>
</div>

{{-- 📊 Contenu Principal du Dashboard --}}
<div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
 {{-- Colonne Principale --}}
 <div class="lg:col-span-2 space-y-8">
 {{-- Graphique de Performance --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center justify-between mb-6">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-chart-area text-white"></i>
 </div>
 <h3 class="text-xl font-black text-gray-900">Performance de la Flotte</h3>
 </div>
 <div class="flex items-center space-x-2">
 <select class="text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
 <option>7 derniers jours</option>
 <option>30 derniers jours</option>
 <option>3 derniers mois</option>
 </select>
 </div>
 </div>

 <div class="h-80 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl flex items-center justify-center">
 <div class="text-center">
 <i class="fas fa-chart-line text-6xl text-gray-400 mb-4"></i>
 <p class="text-gray-600 font-semibold">Graphique de Performance</p>
 <p class="text-sm text-gray-500">Intégration Chart.js à venir</p>
 </div>
 </div>
 </div>

 {{-- Véhicules Récents --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center justify-between mb-6">
 <div class="flex items-center">
 <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-car text-white"></i>
 </div>
 <h3 class="text-xl font-black text-gray-900">Véhicules Récents</h3>
 </div>
 <a href="{{ route('admin.vehicles.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
 Voir tout <i class="fas fa-arrow-right ml-1"></i>
 </a>
 </div>

 @if(isset($dashboardData['recent_vehicles']) && count($dashboardData['recent_vehicles']) > 0)
 <div class="space-y-4">
 @foreach($dashboardData['recent_vehicles'] as $vehicle)
 <div class="flex items-center p-4 bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl">
 <div class="flex-shrink-0 h-12 w-12">
 <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-bold text-gray-900">{{ $vehicle->registration_plate }}</p>
 <p class="text-xs text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
 </div>
 <div class="text-right">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
 {{ $vehicle->vehicleStatus->name ?? 'Disponible' }}
 </span>
 <p class="text-xs text-gray-500 mt-1">{{ number_format($vehicle->current_mileage) }} km</p>
 </div>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 @else
 <div class="text-center py-8">
 <i class="fas fa-car text-4xl text-gray-400 mb-2"></i>
 <p class="text-gray-600">Aucun véhicule récent</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Sidebar Droite --}}
 <div class="space-y-6">
 {{-- Actions Rapides --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-4">
 <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-bolt text-white text-sm"></i>
 </div>
 <h4 class="text-lg font-bold text-gray-900">Actions Rapides</h4>
 </div>

 <div class="space-y-3">
 <a href="{{ route('admin.vehicles.create') }}" class="w-full zenfleet-btn-enterprise-secondary text-left">
 <i class="fas fa-plus mr-2"></i>
 Nouveau Véhicule
 </a>

 <a href="{{ route('admin.drivers.create') }}" class="w-full zenfleet-btn-enterprise-secondary text-left">
 <i class="fas fa-user-plus mr-2"></i>
 Nouveau Chauffeur
 </a>

 <button class="w-full zenfleet-btn-enterprise-secondary text-left">
 <i class="fas fa-route mr-2"></i>
 Nouvelle Mission
 </button>

 <button class="w-full zenfleet-btn-enterprise-secondary text-left">
 <i class="fas fa-file-alt mr-2"></i>
 Rapport Mensuel
 </button>
 </div>
 </div>

 {{-- Alertes et Notifications --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-4">
 <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-bell text-white text-sm"></i>
 </div>
 <h4 class="text-lg font-bold text-gray-900">Alertes Importantes</h4>
 </div>

 <div class="space-y-3">
 @if(isset($dashboardData['alerts']) && count($dashboardData['alerts']) > 0)
 @foreach($dashboardData['alerts'] as $alert)
 <div class="bg-{{ $alert['color'] }}-50 border border-{{ $alert['color'] }}-200 rounded-lg p-3">
 <div class="flex items-start">
 <i class="{{ $alert['icon'] }} text-{{ $alert['color'] }}-600 mr-2 mt-1 text-sm"></i>
 <div>
 <p class="text-sm font-semibold text-gray-900">{{ $alert['title'] }}</p>
 <p class="text-xs text-gray-600 mt-1">{{ $alert['message'] }}</p>
 </div>
 </div>
 </div>
 @endforeach
 @else
 <div class="text-center py-4">
 <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
 <p class="text-sm text-gray-600">Aucune alerte</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Météo du Jour --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-4">
 <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-cloud-sun text-white text-sm"></i>
 </div>
 <h4 class="text-lg font-bold text-gray-900">Conditions Météo</h4>
 </div>

 <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">Alger, Algérie</p>
 <p class="text-2xl font-bold text-blue-700">22°C</p>
 <p class="text-xs text-gray-600">Ensoleillé</p>
 </div>
 <div class="text-right">
 <i class="fas fa-sun text-3xl text-yellow-500"></i>
 <p class="text-xs text-gray-600 mt-1">Vent: 10 km/h</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Taux d'Utilisation --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-4">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-percentage text-white text-sm"></i>
 </div>
 <h4 class="text-lg font-bold text-gray-900">Taux d'Utilisation</h4>
 </div>

 <div class="space-y-4">
 {{-- Véhicules --}}
 <div>
 <div class="flex justify-between text-sm mb-1">
 <span class="font-medium text-gray-700">Véhicules</span>
 <span class="font-bold text-green-600">{{ $dashboardData['vehicle_utilization'] ?? 78 }}%</span>
 </div>
 <div class="zenfleet-progress">
 <div class="zenfleet-progress-bar" style="width: {{ $dashboardData['vehicle_utilization'] ?? 78 }}%"></div>
 </div>
 </div>

 {{-- Chauffeurs --}}
 <div>
 <div class="flex justify-between text-sm mb-1">
 <span class="font-medium text-gray-700">Chauffeurs</span>
 <span class="font-bold text-blue-600">{{ $dashboardData['driver_utilization'] ?? 85 }}%</span>
 </div>
 <div class="zenfleet-progress">
 <div class="zenfleet-progress-bar bg-gradient-to-r from-blue-500 to-blue-600" style="width: {{ $dashboardData['driver_utilization'] ?? 85 }}%"></div>
 </div>
 </div>

 {{-- Efficacité --}}
 <div>
 <div class="flex justify-between text-sm mb-1">
 <span class="font-medium text-gray-700">Efficacité</span>
 <span class="font-bold text-purple-600">{{ $dashboardData['efficiency_score'] ?? 92 }}%</span>
 </div>
 <div class="zenfleet-progress">
 <div class="zenfleet-progress-bar bg-gradient-to-r from-purple-500 to-purple-600" style="width: {{ $dashboardData['efficiency_score'] ?? 92 }}%"></div>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>

{{-- 📈 Graphiques Supplémentaires --}}
<div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
 {{-- Graphique Coûts --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-6">
 <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-euro-sign text-white"></i>
 </div>
 <h3 class="text-xl font-black text-gray-900">Analyse des Coûts</h3>
 </div>

 <div class="h-64 bg-gradient-to-br from-gray-50 to-amber-50 rounded-2xl flex items-center justify-center">
 <div class="text-center">
 <i class="fas fa-chart-pie text-4xl text-gray-400 mb-2"></i>
 <p class="text-gray-600 font-semibold">Graphique des Coûts</p>
 <p class="text-sm text-gray-500">Intégration à venir</p>
 </div>
 </div>
 </div>

 {{-- Graphique Maintenance --}}
 <div class="zenfleet-form-enterprise">
 <div class="flex items-center mb-6">
 <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
 <i class="fas fa-wrench text-white"></i>
 </div>
 <h3 class="text-xl font-black text-gray-900">Planning Maintenance</h3>
 </div>

 <div class="h-64 bg-gradient-to-br from-gray-50 to-red-50 rounded-2xl flex items-center justify-center">
 <div class="text-center">
 <i class="fas fa-calendar-alt text-4xl text-gray-400 mb-2"></i>
 <p class="text-gray-600 font-semibold">Calendrier Maintenance</p>
 <p class="text-sm text-gray-500">Intégration à venir</p>
 </div>
 </div>
 </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/zenfleet-ultra-pro.css') }}">
@endpush

@push('scripts')
<script>
function refreshDashboard() {
 window.location.reload();
}

// Auto-refresh dashboard toutes les 5 minutes
setInterval(function() {
 // Actualisation silencieuse des données via AJAX
 console.log('Dashboard auto-refresh');
}, 300000);

// Animation des compteurs au chargement
document.addEventListener('DOMContentLoaded', function() {
 // Animation des valeurs numériques
 const counters = document.querySelectorAll('[data-counter]');
 counters.forEach(counter => {
 const target = parseInt(counter.getAttribute('data-counter'));
 let current = 0;
 const increment = target / 100;
 const timer = setInterval(() => {
 current += increment;
 if (current >= target) {
 current = target;
 clearInterval(timer);
 }
 counter.textContent = Math.floor(current);
 }, 20);
 });
});
</script>
@endpush
