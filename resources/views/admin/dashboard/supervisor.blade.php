@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Superviseur')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-indigo-100 -m-6 p-6">
 {{-- 🎨 En-tête Superviseur --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-user-shield text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 Dashboard Superviseur
 </h1>
 <p class="text-gray-600 text-lg mt-2">
 Supervision opérationnelle et contrôle qualité
 </p>
 </div>
 </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Connecté en tant que</div>
 <div class="font-semibold text-gray-900">{{ $user->name }}</div>
 <div class="text-sm text-purple-600">Superviseur</div>
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

 {{-- 📊 Métriques de supervision --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 {{-- Véhicules supervisés --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['supervisedVehicles'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Véhicules</div>
 <div class="text-xs text-blue-600">Sous supervision</div>
 </div>
 </div>
 </div>

 {{-- Chauffeurs supervisés --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-user-tie text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['supervisedDrivers'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Chauffeurs</div>
 <div class="text-xs text-emerald-600">Équipe supervisée</div>
 </div>
 </div>
 </div>

 {{-- Affectations du jour --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-day text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['todayAssignments'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Affectations</div>
 <div class="text-xs text-orange-600">Aujourd'hui</div>
 </div>
 </div>
 </div>

 {{-- Inspections en attente --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-clipboard-check text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['pendingInspections'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Inspections</div>
 <div class="text-xs text-red-600">En attente</div>
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
 <div class="bg-white rounded-xl shadow-sm border border-gray-200">
 <div class="p-6 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-tasks text-blue-600 mr-2"></i>
 Tâches du Jour
 </h3>
 </div>
 <div class="p-6">
 <div class="space-y-4">
 <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
 <div class="flex items-center">
 <i class="fas fa-check-circle text-green-500 mr-3"></i>
 <span>Inspection véhicule ABC-123</span>
 </div>
 <span class="text-sm text-gray-500">09:00</span>
 </div>
 <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
 <div class="flex items-center">
 <i class="fas fa-clock text-yellow-500 mr-3"></i>
 <span>Contrôle chauffeur Martin</span>
 </div>
 <span class="text-sm text-gray-500">14:30</span>
 </div>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-sm border border-gray-200">
 <div class="p-6 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
 Alertes
 </h3>
 </div>
 <div class="p-6">
 <div class="space-y-4">
 <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
 <p class="text-sm text-yellow-800">
 <i class="fas fa-fuel-pump mr-2"></i>
 Véhicule DEF-456 - Niveau carburant faible
 </p>
 </div>
 <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
 <p class="text-sm text-red-800">
 <i class="fas fa-wrench mr-2"></i>
 Véhicule GHI-789 - Maintenance requise
 </p>
 </div>
 </div>
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
 const increment = target / 20;
 const timer = setInterval(() => {
 current += increment;
 if (current >= target) {
 counter.textContent = target;
 clearInterval(timer);
 } else {
 counter.textContent = Math.floor(current);
 }
 }, 75);
 }
 });

 // Actualisation automatique des alertes
 setInterval(() => {
 console.log('Vérification des nouvelles alertes...');
 }, 30000);
});
</script>
@endpush

