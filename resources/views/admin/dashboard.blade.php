@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Enterprise')

@push('styles')
<style>
 /* Animations pour les widgets */
 @keyframes slideUp {
 from { transform: translateY(20px); opacity: 0; }
 to { transform: translateY(0); opacity: 1; }
 }

 @keyframes countUp {
 from { opacity: 0; transform: scale(0.8); }
 to { opacity: 1; transform: scale(1); }
 }

 .widget-animate {
 animation: slideUp 0.6s ease-out forwards;
 }

 .widget-animate:nth-child(1) { animation-delay: 0.1s; }
 .widget-animate:nth-child(2) { animation-delay: 0.2s; }
 .widget-animate:nth-child(3) { animation-delay: 0.3s; }
 .widget-animate:nth-child(4) { animation-delay: 0.4s; }

 .stat-number {
 animation: countUp 0.8s ease-out forwards;
 }

 /* Graphiques animés */
 .chart-container {
 position: relative;
 height: 300px;
 }

 /* Progress rings */
 .progress-ring {
 transform: rotate(-90deg);
 }

 .progress-ring-circle {
 transition: stroke-dashoffset 1s ease-in-out;
 }

 /* Hover effects */
 .widget-card {
 transition: all 0.3s ease;
 position: relative;
 overflow: hidden;
 }

 .widget-card::before {
 content: '';
 position: absolute;
 top: -50%;
 left: -50%;
 width: 200%;
 height: 200%;
 background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
 transform: rotate(45deg);
 transition: all 0.5s;
 opacity: 0;
 }

 .widget-card:hover::before {
 animation: shine 0.5s ease-in-out;
 }

 @keyframes shine {
 0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); opacity: 0; }
 50% { opacity: 1; }
 100% { transform: translateX(100%) translateY(100%) rotate(45deg); opacity: 0; }
 }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
 <div class="space-y-6">
 {{-- Header avec salutation dynamique --}}
 <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">
 {{ $greeting ?? 'Bonjour' }}, {{ auth()->user()->name }} 👋
 </h1>
 <p class="mt-1 text-sm text-gray-500">
 Voici un aperçu de votre flotte aujourd'hui - {{ now()->format('l d F Y') }}
 </p>
 </div>
 <div class="mt-4 sm:mt-0 flex gap-3">
 <button class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 flex items-center gap-2">
 <i class="fas fa-download"></i>
 Exporter
 </button>
 <button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 flex items-center gap-2 shadow-lg">
 <i class="fas fa-plus"></i>
 Nouvelle entrée
 </button>
 </div>
 </div>

 {{-- Statistiques principales --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 {{-- Véhicules actifs --}}
 <div class="widget-card widget-animate bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-blue-100 text-sm font-medium">Véhicules Actifs</p>
 <p class="text-3xl font-bold mt-2 stat-number">{{ $stats['active_vehicles'] ?? 24 }}</p>
 <div class="flex items-center mt-4">
 <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
 <i class="fas fa-arrow-up mr-1"></i>
 12% ce mois
 </span>
 </div>
 </div>
 <div class="bg-white/20 p-4 rounded-xl">
 <i class="fas fa-car text-3xl"></i>
 </div>
 </div>
 <div class="mt-4 h-1 bg-white/20 rounded-full overflow-hidden">
 <div class="h-full bg-white rounded-full" style="width: 75%"></div>
 </div>
 </div>

 {{-- Chauffeurs disponibles --}}
 <div class="widget-card widget-animate bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-green-100 text-sm font-medium">Chauffeurs Disponibles</p>
 <p class="text-3xl font-bold mt-2 stat-number">{{ $stats['available_drivers'] ?? 18 }}</p>
 <div class="flex items-center mt-4">
 <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
 <i class="fas fa-check-circle mr-1"></i>
 85% disponibles
 </span>
 </div>
 </div>
 <div class="bg-white/20 p-4 rounded-xl">
 <i class="fas fa-users text-3xl"></i>
 </div>
 </div>
 <div class="mt-4 h-1 bg-white/20 rounded-full overflow-hidden">
 <div class="h-full bg-white rounded-full" style="width: 85%"></div>
 </div>
 </div>

 {{-- Maintenances en cours --}}
 <div class="widget-card widget-animate bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-xl">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-orange-100 text-sm font-medium">Maintenances</p>
 <p class="text-3xl font-bold mt-2 stat-number">{{ $stats['pending_maintenances'] ?? 5 }}</p>
 <div class="flex items-center mt-4">
 <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
 <i class="fas fa-clock mr-1"></i>
 3 urgentes
 </span>
 </div>
 </div>
 <div class="bg-white/20 p-4 rounded-xl">
 <i class="fas fa-tools text-3xl"></i>
 </div>
 </div>
 <div class="mt-4 h-1 bg-white/20 rounded-full overflow-hidden">
 <div class="h-full bg-white rounded-full" style="width: 30%"></div>
 </div>
 </div>

 {{-- Kilométrage total --}}
 <div class="widget-card widget-animate bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-purple-100 text-sm font-medium">Kilométrage Total</p>
 <p class="text-3xl font-bold mt-2 stat-number">{{ number_format($stats['total_mileage'] ?? 152847) }}</p>
 <div class="flex items-center mt-4">
 <span class="text-xs bg-white/20 px-2 py-1 rounded-full">
 <i class="fas fa-route mr-1"></i>
 +2,847 km/semaine
 </span>
 </div>
 </div>
 <div class="bg-white/20 p-4 rounded-xl">
 <i class="fas fa-tachometer-alt text-3xl"></i>
 </div>
 </div>
 <div class="mt-4 h-1 bg-white/20 rounded-full overflow-hidden">
 <div class="h-full bg-white rounded-full" style="width: 60%"></div>
 </div>
 </div>
 </div>

 {{-- Graphiques et tableaux --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 {{-- Graphique d'utilisation de la flotte --}}
 <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
 <div class="flex items-center justify-between mb-6">
 <div>
 <h3 class="text-lg font-semibold text-gray-900">Utilisation de la Flotte</h3>
 <p class="text-sm text-gray-500">Performance sur les 7 derniers jours</p>
 </div>
 <select class="px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
 <option>Cette semaine</option>
 <option>Ce mois</option>
 <option>Cette année</option>
 </select>
 </div>
 <div class="chart-container">
 <canvas id="fleetChart"></canvas>
 </div>
 </div>

 {{-- Statut des véhicules (cercle) --}}
 <div class="bg-white rounded-2xl shadow-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-6">Répartition des Véhicules</h3>
 <div class="flex items-center justify-center">
 <div class="relative">
 <svg class="progress-ring w-40 h-40">
 <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="12" fill="none" />
 <circle class="progress-ring-circle" cx="80" cy="80" r="70" stroke="url(#gradient)" stroke-width="12" fill="none"
 stroke-dasharray="440" stroke-dashoffset="110" stroke-linecap="round" />
 <defs>
 <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
 <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
 <stop offset="100%" style="stop-color:#6366f1;stop-opacity:1" />
 </linearGradient>
 </defs>
 </svg>
 <div class="absolute inset-0 flex flex-col items-center justify-center">
 <span class="text-3xl font-bold text-gray-900">75%</span>
 <span class="text-sm text-gray-500">Actifs</span>
 </div>
 </div>
 </div>
 <div class="mt-6 space-y-3">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-3 h-3 bg-green-500 rounded-full"></div>
 <span class="text-sm text-gray-600">En service</span>
 </div>
 <span class="text-sm font-semibold text-gray-900">18</span>
 </div>
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
 <span class="text-sm text-gray-600">En maintenance</span>
 </div>
 <span class="text-sm font-semibold text-gray-900">5</span>
 </div>
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-2">
 <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
 <span class="text-sm text-gray-600">Hors service</span>
 </div>
 <span class="text-sm font-semibold text-gray-900">1</span>
 </div>
 </div>
 </div>
 </div>

 {{-- Tableau des activités récentes --}}
 <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
 <div class="px-6 py-4 border-b border-gray-200">
 <div class="flex items-center justify-between">
 <h3 class="text-lg font-semibold text-gray-900">Activités Récentes</h3>
 <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Voir tout →</a>
 </div>
 </div>
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Événement</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Véhicule</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chauffeur</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($recentActivities ?? [] as $activity)
 <tr class="hover:bg-gray-50 transition-colors duration-200">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
 <i class="fas fa-{{ $activity->icon ?? 'circle' }} text-blue-600"></i>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">{{ $activity->title ?? 'Nouvelle affectation' }}</div>
 <div class="text-sm text-gray-500">{{ $activity->description ?? 'Description' }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">{{ $activity->vehicle ?? 'ABC-123' }}</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">{{ $activity->driver ?? 'John Doe' }}</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
 {{ $activity->status ?? 'Actif' }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 {{ $activity->created_at ?? now()->format('d/m/Y H:i') }}
 </td>
 </tr>
 @endforeach
 
 {{-- Données de démonstration --}}
 <tr class="hover:bg-gray-50 transition-colors duration-200">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
 <i class="fas fa-check-circle text-green-600"></i>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">Maintenance terminée</div>
 <div class="text-sm text-gray-500">Révision complète</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">ZF-001</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">Ahmed Benali</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
 Complété
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 {{ now()->subHours(2)->format('d/m/Y H:i') }}
 </td>
 </tr>
 
 <tr class="hover:bg-gray-50 transition-colors duration-200">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
 <i class="fas fa-exchange-alt text-blue-600"></i>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">Nouvelle affectation</div>
 <div class="text-sm text-gray-500">Véhicule assigné</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">ZF-015</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">Fatima Zohra</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
 En cours
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 {{ now()->subHours(5)->format('d/m/Y H:i') }}
 </td>
 </tr>
 
 <tr class="hover:bg-gray-50 transition-colors duration-200">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-orange-600"></i>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">Alerte kilométrage</div>
 <div class="text-sm text-gray-500">Maintenance requise</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">ZF-008</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="text-sm text-gray-900">Mohamed Karim</span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
 Attention
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 {{ now()->subDay()->format('d/m/Y H:i') }}
 </td>
 </tr>
 </tbody>
 </table>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Graphique d'utilisation de la flotte
 const ctx = document.getElementById('fleetChart').getContext('2d');
 const gradient = ctx.createLinearGradient(0, 0, 0, 300);
 gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
 gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

 new Chart(ctx, {
 type: 'line',
 data: {
 labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
 datasets: [{
 label: 'Véhicules actifs',
 data: [18, 22, 20, 24, 23, 19, 21],
 backgroundColor: gradient,
 borderColor: '#3b82f6',
 borderWidth: 3,
 fill: true,
 tension: 0.4,
 pointRadius: 5,
 pointBackgroundColor: '#fff',
 pointBorderColor: '#3b82f6',
 pointBorderWidth: 2,
 pointHoverRadius: 7,
 pointHoverBackgroundColor: '#3b82f6',
 pointHoverBorderColor: '#fff',
 pointHoverBorderWidth: 2
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: {
 display: false
 },
 tooltip: {
 backgroundColor: 'rgba(0, 0, 0, 0.8)',
 padding: 12,
 titleColor: '#fff',
 bodyColor: '#fff',
 borderColor: '#3b82f6',
 borderWidth: 1,
 displayColors: false,
 callbacks: {
 label: function(context) {
 return context.parsed.y + ' véhicules';
 }
 }
 }
 },
 scales: {
 y: {
 beginAtZero: true,
 grid: {
 color: 'rgba(0, 0, 0, 0.05)',
 drawBorder: false
 },
 ticks: {
 color: '#6b7280',
 font: {
 size: 12
 }
 }
 },
 x: {
 grid: {
 display: false
 },
 ticks: {
 color: '#6b7280',
 font: {
 size: 12
 }
 }
 }
 }
 }
 });

 // Animation des nombres
 const animateValue = (element, start, end, duration) => {
 let startTimestamp = null;
 const step = (timestamp) => {
 if (!startTimestamp) startTimestamp = timestamp;
 const progress = Math.min((timestamp - startTimestamp) / duration, 1);
 element.innerText = Math.floor(progress * (end - start) + start).toLocaleString();
 if (progress < 1) {
 window.requestAnimationFrame(step);
 }
 };
 window.requestAnimationFrame(step);
 };

 // Animer les statistiques au chargement
 document.querySelectorAll('.stat-number').forEach(el => {
 const value = parseInt(el.innerText.replace(/,/g, ''));
 animateValue(el, 0, value, 1000);
 });
});
</script>
@endpush
