@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Gestionnaire Flotte')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-green-50 to-emerald-100 -m-6 p-6">
 {{-- 🎨 En-tête Gestionnaire Flotte --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-truck text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 Dashboard Gestionnaire Flotte
 </h1>
 <p class="text-gray-600 text-lg mt-2">
 Gestion opérationnelle de la flotte - Vue d'ensemble
 </p>
 </div>
 </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Connecté en tant que</div>
 <div class="font-semibold text-gray-900">{{ $user->name }}</div>
 <div class="text-sm text-green-600">Gestionnaire Flotte</div>
 </div>
 </div>
 </div>
 </div>

 {{-- 📊 Métriques de la flotte --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 {{-- Véhicules --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['vehiclesCount'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Véhicules Total</div>
 <div class="text-xs text-blue-600">Flotte complète</div>
 </div>
 </div>
 </div>

 {{-- Chauffeurs --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-user-tie text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['driversCount'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Chauffeurs</div>
 <div class="text-xs text-emerald-600">Équipe active</div>
 </div>
 </div>
 </div>

 {{-- Affectations actives --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-clipboard-check text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['activeAssignments'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Affectations</div>
 <div class="text-xs text-orange-600">En cours</div>
 </div>
 </div>
 </div>

 {{-- Alertes maintenance --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['maintenanceAlerts'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Alertes</div>
 <div class="text-xs text-red-600">Maintenance</div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- 🚨 Mode dégradé si erreur --}}
 @if(isset($error) || isset($fallbackMode))
 <div class="max-w-7xl mx-auto mb-8">
 <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6">
 <div class="flex items-center gap-4">
 <div class="w-10 h-10 bg-amber-500 border border-amber-600 rounded-full flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-white"></i>
 </div>
 <div>
 <h3 class="font-semibold text-amber-800">Erreur Dashboard</h3>
 <p class="text-amber-700">{{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}</p>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- 📈 Métriques de performance --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 {{-- Taux de disponibilité --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-semibold text-gray-900">Taux de Disponibilité</h3>
 <div class="text-2xl font-bold text-green-600">{{ number_format($stats['availabilityRate'] ?? 85, 1) }}%</div>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-3">
 <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full"
 style="width: {{ $stats['availabilityRate'] ?? 85 }}%"></div>
 </div>
 <p class="text-sm text-gray-600 mt-2">Véhicules disponibles pour affectation</p>
 </div>

 {{-- Taux d'utilisation --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-semibold text-gray-900">Taux d'Utilisation</h3>
 <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['utilizationRate'] ?? 75, 1) }}%</div>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-3">
 <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full"
 style="width: {{ $stats['utilizationRate'] ?? 75 }}%"></div>
 </div>
 <p class="text-sm text-gray-600 mt-2">Optimisation de l'usage de la flotte</p>
 </div>

 {{-- Performance globale --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-semibold text-gray-900">Performance Globale</h3>
 <div class="text-2xl font-bold text-purple-600">
 {{ number_format((($stats['availabilityRate'] ?? 85) + ($stats['utilizationRate'] ?? 75)) / 2, 1) }}%
 </div>
 </div>
 <div class="w-full bg-gray-200 rounded-full h-3">
 <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full"
 style="width: {{ (($stats['availabilityRate'] ?? 85) + ($stats['utilizationRate'] ?? 75)) / 2 }}%"></div>
 </div>
 <p class="text-sm text-gray-600 mt-2">Score de performance général</p>
 </div>
 </div>
 </div>

 {{-- 📊 Contenu principal --}}
 <div class="max-w-7xl mx-auto">
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
 {{-- État des véhicules --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-chart-pie text-white"></i>
 </div>
 <h2 class="text-xl font-bold text-gray-900">État des Véhicules</h2>
 </div>
 <div class="mt-6">
 @if(isset($vehicleStatus) && count($vehicleStatus) > 0)
 @foreach($vehicleStatus as $status => $count)
 <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
 <div class="flex items-center gap-3">
 <div class="w-4 h-4 rounded-full bg-{{ $status === 'available' ? 'green' : ($status === 'in_use' ? 'blue' : ($status === 'maintenance' ? 'yellow' : 'gray')) }}-500"></div>
 <span class="text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
 </div>
 <span class="font-semibold text-gray-900">{{ $count }}</span>
 </div>
 @endforeach
 @else
 <div class="text-center py-8 text-gray-500">
 <i class="fas fa-car text-3xl mb-4"></i>
 <p>Aucune donnée véhicule</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Maintenance à venir --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-wrench text-white"></i>
 </div>
 <h2 class="text-xl font-bold text-gray-900">Maintenance Prévue</h2>
 </div>
 <div class="mt-6">
 @if(isset($upcomingMaintenance) && $upcomingMaintenance->count() > 0)
 @foreach($upcomingMaintenance->take(5) as $maintenance)
 <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
 <div class="w-8 h-8 bg-yellow-100 border border-yellow-200 rounded-full flex items-center justify-center">
 <i class="fas fa-wrench text-yellow-600 text-xs"></i>
 </div>
 <div class="flex-1">
 <div class="font-medium text-gray-900">{{ $maintenance->vehicle->registration ?? 'Véhicule' }}</div>
 <div class="text-sm text-gray-600">{{ $maintenance->description ?? 'Maintenance programmée' }}</div>
 <div class="text-xs text-gray-500">{{ isset($maintenance->scheduled_date) ? $maintenance->scheduled_date->format('d/m/Y') : '' }}</div>
 </div>
 </div>
 @endforeach
 @else
 <div class="text-center py-8 text-gray-500">
 <i class="fas fa-check-circle text-3xl mb-4 text-green-500"></i>
 <p>Aucune maintenance prévue</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>

 {{-- 🔧 Actions rapides --}}
 <div class="max-w-7xl mx-auto mt-8">
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <h2 class="text-xl font-bold text-gray-900 mb-6">Actions Gestionnaire Flotte</h2>
 <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
 <a href="{{ route('admin.vehicles.index') }}" class="flex flex-col items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-blue-500 border border-blue-600 rounded-full flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Véhicules</span>
 </a>

 <a href="{{ route('admin.drivers.index') }}" class="flex flex-col items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-green-500 border border-green-600 rounded-full flex items-center justify-center">
 <i class="fas fa-user-tie text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Chauffeurs</span>
 </a>

 <a href="{{ route('admin.assignments.index') }}" class="flex flex-col items-center gap-3 p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-orange-500 border border-orange-600 rounded-full flex items-center justify-center">
 <i class="fas fa-clipboard-check text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Affectations</span>
 </a>

 <a href="{{ route('admin.maintenance.dashboard') }}" class="flex flex-col items-center gap-3 p-4 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-yellow-500 border border-yellow-600 rounded-full flex items-center justify-center">
 <i class="fas fa-wrench text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Maintenance</span>
 </a>

 <a href="{{ route('admin.planning.index') }}" class="flex flex-col items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-purple-500 border border-purple-600 rounded-full flex items-center justify-center">
 <i class="fas fa-calendar-alt text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Planning</span>
 </a>

 <a href="{{ route('admin.reports.index') }}" class="flex flex-col items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-indigo-500 border border-indigo-600 rounded-full flex items-center justify-center">
 <i class="fas fa-chart-bar text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Rapports</span>
 </a>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Animation des compteurs
 const counters = document.querySelectorAll('.text-2xl.font-bold');
 counters.forEach(counter => {
 const target = parseInt(counter.textContent);
 if (target && target > 0) {
 let current = 0;
 const increment = target / 30;
 const timer = setInterval(() => {
 current += increment;
 if (current >= target) {
 counter.textContent = target;
 clearInterval(timer);
 } else {
 counter.textContent = Math.floor(current);
 }
 }, 50);
 }
 });
});
</script>
@endpush