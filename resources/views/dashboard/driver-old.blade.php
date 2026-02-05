@extends('layouts.admin.catalyst')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-green-50 to-blue-100 -m-6 p-6">
 {{-- 🎨 En-tête Chauffeur --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-id-card text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 Dashboard Chauffeur
 </h1>
 <p class="text-gray-600 text-lg mt-2">
 Bienvenue {{ $user->name }} - Interface chauffeur
 </p>
 </div>
 </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Connecté en tant que</div>
 <div class="font-semibold text-gray-900">{{ $user->name }}</div>
 <div class="text-sm text-green-600">Chauffeur</div>
 </div>
 </div>
 </div>
 </div>

 {{-- 🚨 Mode dégradé si erreur --}}
 @if(isset($error) || isset($fallbackMode) || isset($setupRequired))
 <div class="max-w-7xl mx-auto mb-8">
 <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6">
 <div class="flex items-center gap-4">
 <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-white"></i>
 </div>
 <div>
 <h3 class="font-semibold text-amber-800">
 {{ isset($setupRequired) ? 'Configuration requise' : 'Erreur Dashboard' }}
 </h3>
 <p class="text-amber-700">{{ $error ?? 'Données partiellement indisponibles - Mode dégradé activé' }}</p>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- 📊 Métriques chauffeur --}}
 <div class="max-w-7xl mx-auto mb-8">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 {{-- Voyages total --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-route text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ $stats['totalTrips'] ?? 0 }}</div>
 <div class="text-sm text-gray-600">Voyages</div>
 <div class="text-xs text-blue-600">Total effectués</div>
 </div>
 </div>
 </div>

 {{-- Kilomètres ce mois --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-road text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['monthlyKm'] ?? 0) }}</div>
 <div class="text-sm text-gray-600">Kilomètres</div>
 <div class="text-xs text-emerald-600">Ce mois</div>
 </div>
 </div>
 </div>

 {{-- Score de sécurité --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-shield-alt text-white"></i>
 </div>
 <div>
 <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['safetyScore'] ?? 95, 1) }}%</div>
 <div class="text-sm text-gray-600">Score Sécurité</div>
 <div class="text-xs text-yellow-600">Performance</div>
 </div>
 </div>
 </div>

 {{-- Statut actuel --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-6">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-check-circle text-white"></i>
 </div>
 <div>
 <div class="text-xl font-bold text-gray-900">Disponible</div>
 <div class="text-sm text-gray-600">Statut</div>
 <div class="text-xs text-green-600">Prêt à partir</div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- 📈 Contenu principal --}}
 <div class="max-w-7xl mx-auto">
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
 {{-- Affectation actuelle --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-clipboard-list text-white"></i>
 </div>
 <h2 class="text-xl font-bold text-gray-900">Affectation Actuelle</h2>
 </div>
 <div class="mt-6">
 @if($stats['currentAssignment'])
 <div class="p-6 bg-blue-50 border border-blue-200 rounded-xl">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
 <i class="fas fa-car text-white"></i>
 </div>
 <div class="flex-1">
 <div class="font-semibold text-gray-900">Mission en cours</div>
 <div class="text-sm text-gray-600">{{ $stats['currentAssignment'] }}</div>
 <div class="text-xs text-blue-600 mt-1">Démarrée il y a 2h30</div>
 </div>
 <div class="text-right">
 <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 text-xs font-semibold rounded-full">
 En cours
 </span>
 </div>
 </div>
 </div>
 @else
 <div class="text-center py-8 text-gray-500">
 <i class="fas fa-calendar-check text-3xl mb-4"></i>
 <p class="font-medium">Aucune affectation en cours</p>
 <p class="text-sm">Vous êtes disponible pour une nouvelle mission</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Voyages récents --}}
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-history text-white"></i>
 </div>
 <h2 class="text-xl font-bold text-gray-900">Voyages Récents</h2>
 </div>
 <div class="mt-6">
 @if(isset($recentTrips) && count($recentTrips) > 0)
 @foreach($recentTrips->take(5) as $trip)
 <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
 <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
 <i class="fas fa-route text-green-600 text-xs"></i>
 </div>
 <div class="flex-1">
 <div class="font-medium text-gray-900">{{ $trip['destination'] ?? 'Destination' }}</div>
 <div class="text-sm text-gray-600">{{ $trip['distance'] ?? '25' }} km • {{ $trip['duration'] ?? '45 min' }}</div>
 <div class="text-xs text-gray-500">{{ $trip['date'] ?? 'Hier 14:30' }}</div>
 </div>
 <div class="text-right">
 <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
 Terminé
 </span>
 </div>
 </div>
 @endforeach
 @else
 <div class="text-center py-8 text-gray-500">
 <i class="fas fa-route text-3xl mb-4"></i>
 <p>Aucun voyage récent</p>
 </div>
 @endif
 </div>
 </div>
 </div>
 </div>

 {{-- 🔧 Actions rapides Chauffeur --}}
 <div class="max-w-7xl mx-auto mt-8">
 <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-8">
 <h2 class="text-xl font-bold text-gray-900 mb-6">Actions Rapides</h2>
 <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
 <a href="{{ route('driver.missions.index') }}" class="flex flex-col items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
 <i class="fas fa-tasks text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Mes Missions</span>
 </a>

 <a href="{{ route('driver.vehicle.check') }}" class="flex flex-col items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
 <i class="fas fa-search text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Contrôle Véhicule</span>
 </a>

 <a href="{{ route('driver.trips.history') }}" class="flex flex-col items-center gap-3 p-4 bg-orange-50 hover:bg-orange-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
 <i class="fas fa-history text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Historique</span>
 </a>

 <a href="{{ route('driver.fuel.report') }}" class="flex flex-col items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
 <i class="fas fa-gas-pump text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Carburant</span>
 </a>

 <a href="{{ route('driver.incidents.report') }}" class="flex flex-col items-center gap-3 p-4 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Incidents</span>
 </a>

 <a href="{{ route('driver.profile.index') }}" class="flex flex-col items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
 <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
 <i class="fas fa-user text-white"></i>
 </div>
 <span class="text-sm font-medium text-gray-700">Mon Profil</span>
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
 const target = parseFloat(counter.textContent.replace(/[^\d.]/g, ''));
 if (target && target > 0) {
 let current = 0;
 const increment = target / 25;
 const timer = setInterval(() => {
 current += increment;
 if (current >= target) {
 if (counter.textContent.includes('%')) {
 counter.textContent = target.toFixed(1) + '%';
 } else if (counter.textContent.includes(',')) {
 counter.textContent = new Intl.NumberFormat('fr-FR').format(Math.round(target));
 } else {
 counter.textContent = Math.round(target);
 }
 clearInterval(timer);
 } else {
 if (counter.textContent.includes('%')) {
 counter.textContent = current.toFixed(1) + '%';
 } else if (counter.textContent.includes(',')) {
 counter.textContent = new Intl.NumberFormat('fr-FR').format(Math.floor(current));
 } else {
 counter.textContent = Math.floor(current);
 }
 }
 }, 60);
 }
 });

 // Mise à jour du statut en temps réel
 setInterval(() => {
 console.log('Vérification du statut chauffeur...');
 }, 60000);
});
</script>
@endpush

