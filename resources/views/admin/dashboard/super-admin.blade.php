@extends('layouts.admin.catalyst')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="dashboard-super-admin">
     
     {{-- Header --}}
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 <i class="fas fa-tachometer-alt text-blue-600 mr-4"></i>
 Dashboard Super Admin
 </h1>
 <p class="text-xl text-gray-600 mt-2">
 Vue d'ensemble globale du système ZenFleet
 </p>

 {{-- Message d'erreur si mode dégradé --}}
 @if(isset($fallbackMode) && $fallbackMode)
 <div class="mt-3 px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg">
 <div class="flex items-center">
 <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
 <span class="text-sm text-yellow-800">{{ $error ?? 'Mode dégradé activé - Données partielles' }}</span>
 </div>
 </div>
 @endif
 </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Dernière mise à jour</div>
 <div class="text-lg font-semibold">{{ now()->format('d/m/Y H:i') }}</div>
 </div>
 </div>
 </div>

 {{-- Statistiques Système --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 {{-- Total Organisations --}}
 <div class="admin-card bg-gradient-to-br from-blue-500 to-blue-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">
 Organisations
 </p>
 <p class="text-4xl font-bold mt-2">
 {{ number_format($stats['totalOrganizations'] ?? 0) }}
 </p>
 <p class="text-blue-100 text-sm mt-1">
 {{ number_format($stats['activeOrganizations'] ?? 0) }} actives
 </p>
 </div>
 <div class="p-4 bg-blue-400 bg-opacity-30 rounded-full">
 <i class="fas fa-building text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Total Utilisateurs --}}
 <div class="admin-card bg-gradient-to-br from-green-500 to-green-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-green-100 text-sm font-medium uppercase tracking-wider">
 Utilisateurs
 </p>
 <p class="text-4xl font-bold mt-2">
 {{ number_format($stats['totalUsers'] ?? 0) }}
 </p>
 <p class="text-green-100 text-sm mt-1">
 {{ number_format($stats['activeUsers'] ?? 0) }} actifs
 </p>
 </div>
 <div class="p-4 bg-green-400 bg-opacity-30 rounded-full">
 <i class="fas fa-users text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Total Véhicules --}}
 <div class="admin-card bg-gradient-to-br from-yellow-500 to-yellow-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-yellow-100 text-sm font-medium uppercase tracking-wider">
 Véhicules
 </p>
 <p class="text-4xl font-bold mt-2">
 {{ number_format($stats['totalVehicles'] ?? 0) }}
 </p>
 <p class="text-yellow-100 text-sm mt-1">
 Toutes organisations
 </p>
 </div>
 <div class="p-4 bg-yellow-400 bg-opacity-30 rounded-full">
 <i class="fas fa-car text-3xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Santé Système --}}
 <div class="admin-card bg-gradient-to-br from-purple-500 to-purple-700 text-white">
 <div class="admin-card-body">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-purple-100 text-sm font-medium uppercase tracking-wider">
 Santé Système
 </p>
 <p class="text-4xl font-bold mt-2">
 {{ $stats['systemUptime'] }}
 </p>
 <p class="text-purple-100 text-sm mt-1">
 Disponibilité
 </p>
 </div>
 <div class="p-4 bg-purple-400 bg-opacity-30 rounded-full">
 <i class="fas fa-heartbeat text-3xl"></i>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Contenu Principal --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 
 {{-- Activité Système Récente --}}
 <div class="lg:col-span-2">
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-xl font-semibold text-gray-900">
 <i class="fas fa-activity text-blue-600 mr-2"></i>
 Activité Système Récente
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-4 max-h-96 overflow-y-auto">
 @forelse($recentActivity as $activity)
 <div class="flex items-start p-4 bg-gray-50 rounded-lg">
 <div class="flex-shrink-0 mr-4">
 <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center">
 <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
 </div>
 </div>
 <div class="flex-1 min-w-0">
 <p class="text-sm font-medium text-gray-900">
 {{ $activity['title'] }}
 </p>
 <p class="text-sm text-gray-500">
 {{ $activity['description'] }}
 </p>
 <p class="text-xs text-gray-400 mt-1">
 {{ $activity['timestamp']->diffForHumans() }}
 </p>
 </div>
 </div>
 @empty
 <div class="text-center text-gray-500 py-8">
 <i class="fas fa-inbox text-4xl mb-4"></i>
 <p>Aucune activité récente</p>
 </div>
 @endforelse
 </div>
 </div>
 </div>
 </div>

 {{-- Panel de Contrôle --}}
 <div class="space-y-6">
 
 {{-- Santé Système --}}
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-server text-green-600 mr-2"></i>
 État des Services
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-3">
 @foreach($systemHealth as $service => $status)
 <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-gray-700 capitalize">
 {{ str_replace('_', ' ', $service) }}
 </span>
 <span class="px-2 py-1 text-xs font-semibold rounded-full
 {{ $status === 'healthy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
 {{ $status === 'healthy' ? 'Opérationnel' : 'Problème' }}
 </span>
 </div>
 @endforeach
 </div>
 </div>
 </div>

 {{-- Top Organisations --}}
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-trophy text-yellow-600 mr-2"></i>
 Top Organisations
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-3">
 @foreach(array_slice($topOrganizations, 0, 5) as $org)
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-900">{{ $org['name'] }}</p>
 <p class="text-xs text-gray-500">{{ $org['city'] }}</p>
 </div>
 <div class="text-right">
 <p class="text-sm font-semibold text-blue-600">
 {{ $org['users_count'] }} users
 </p>
 <p class="text-xs text-gray-500">
 {{ $org['vehicles_count'] }} véhicules
 </p>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 </div>

 {{-- Actions Rapides --}}
 <div class="admin-card">
 <div class="admin-card-header">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-bolt text-blue-600 mr-2"></i>
 Actions Rapides
 </h3>
 </div>
 <div class="admin-card-body">
 <div class="space-y-2">
 <a href="{{ route('admin.organizations.create') }}" 
 class="admin-btn admin-btn-primary w-full text-center">
 <i class="fas fa-plus mr-2"></i>
 Nouvelle Organisation
 </a>
 
 <a href="{{ route('admin.system.health') }}" 
 class="admin-btn admin-btn-secondary w-full text-center">
 <i class="fas fa-heartbeat mr-2"></i>
 Monitoring Système
 </a>
 
 <a href="{{ route('admin.audit.index') }}" 
 class="admin-btn admin-btn-secondary w-full text-center">
 <i class="fas fa-shield-alt mr-2"></i>
 Logs d'Audit
 </a>
 </div>
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
 console.log('🎉 Dashboard Super Admin chargé avec succès !');
 
 // Animation des cartes statistiques
 const cards = document.querySelectorAll('.admin-card');
 cards.forEach((card, index) => {
 card.style.opacity = '0';
 card.style.transform = 'translateY(20px)';
 
 setTimeout(() => {
 card.style.transition = 'all 0.5s ease';
 card.style.opacity = '1';
 card.style.transform = 'translateY(0)';
 }, index * 100);
 });

 // Actualisation automatique des données toutes les 5 minutes
 setInterval(() => {
 window.location.reload();
 }, 300000);
});
</script>
@endpush
