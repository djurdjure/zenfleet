@extends('layouts.admin.catalyst')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-admin">
 
 {{-- Header Dashboard --}}
 <div class="mb-8">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">
 <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
 Dashboard Administrateur
 </h1>
 <p class="text-gray-600 mt-2">
 Vue d'ensemble de votre système ZenFleet
 </p>
 </div>
 <div class="text-right">
 <div class="text-sm text-gray-500">Dernière mise à jour</div>
 <div class="text-lg font-semibold">{{ now()->format('d/m/Y H:i') }}</div>
 </div>
 </div>
 </div>

 {{-- Statistiques Cards --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 {{-- Total Organisations --}}
 <div class="bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-xl p-6 shadow-lg">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">
 Organisations
 </p>
 <p class="text-3xl font-bold mt-2">
 {{ $stats['total_organizations'] ?? '12' }}
 </p>
 <p class="text-blue-100 text-sm mt-1">
 Toutes organisations
 </p>
 </div>
 <div class="p-4 bg-blue-400 bg-opacity-30 rounded-full">
 <i class="fas fa-building text-2xl"></i>
 </div>
 </div>
 </div>

 {{-- Utilisateurs Actifs --}}
 <div class="bg-gradient-to-br from-green-500 to-green-700 text-white rounded-xl p-6 shadow-lg">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-green-100 text-sm font-medium uppercase tracking-wider">
 Utilisateurs
 </p>
 <p class="text-3xl font-bold mt-2">
 {{ $stats['active_users'] ?? '245' }}
 </p>
 <p class="text-green-100 text-sm mt-1">
 Utilisateurs actifs
 </p>
 </div>
 <div class="p-4 bg-green-400 bg-opacity-30 rounded-full">
 <i class="fas fa-users text-2xl"></i>
 </div>
 </div>
 </div>

 {{-- Véhicules --}}
 <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 text-white rounded-xl p-6 shadow-lg">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-yellow-100 text-sm font-medium uppercase tracking-wider">
 Véhicules
 </p>
 <p class="text-3xl font-bold mt-2">
 {{ $stats['total_vehicles'] ?? '89' }}
 </p>
 <p class="text-yellow-100 text-sm mt-1">
 Flotte totale
 </p>
 </div>
 <div class="p-4 bg-yellow-400 bg-opacity-30 rounded-full">
 <i class="fas fa-car text-2xl"></i>
 </div>
 </div>
 </div>

 {{-- Alertes --}}
 <div class="bg-gradient-to-br from-red-500 to-red-700 text-white rounded-xl p-6 shadow-lg">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-red-100 text-sm font-medium uppercase tracking-wider">
 Alertes
 </p>
 <p class="text-3xl font-bold mt-2">5</p>
 <p class="text-red-100 text-sm mt-1">
 Nécessitent attention
 </p>
 </div>
 <div class="p-4 bg-red-400 bg-opacity-30 rounded-full">
 <i class="fas fa-exclamation-triangle text-2xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Contenu Principal --}}
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
 
 {{-- Activités Récentes --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200">
 <div class="p-6 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-history text-blue-600 mr-2"></i>
 Activité Récente
 </h3>
 </div>
 <div class="p-6">
 <div class="space-y-4">
 <div class="flex items-center p-4 bg-gray-50 rounded-lg">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
 <i class="fas fa-plus text-blue-600"></i>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-900">
 Nouvelle organisation créée
 </p>
 <p class="text-sm text-gray-500">Il y a 2 heures</p>
 </div>
 </div>
 
 <div class="flex items-center p-4 bg-gray-50 rounded-lg">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
 <i class="fas fa-user text-green-600"></i>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-900">
 Nouvel utilisateur inscrit
 </p>
 <p class="text-sm text-gray-500">Il y a 4 heures</p>
 </div>
 </div>

 <div class="flex items-center p-4 bg-gray-50 rounded-lg">
 <div class="flex-shrink-0">
 <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
 <i class="fas fa-car text-yellow-600"></i>
 </div>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-900">
 Véhicule ajouté à la flotte
 </p>
 <p class="text-sm text-gray-500">Il y a 6 heures</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Actions Rapides --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200">
 <div class="p-6 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900">
 <i class="fas fa-bolt text-yellow-600 mr-2"></i>
 Actions Rapides
 </h3>
 </div>
 <div class="p-6">
 <div class="grid grid-cols-1 gap-4">
 @can('Super Admin')
 <a href="{{ route('admin.organizations.create') }}" 
 class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
 <i class="fas fa-plus mr-2"></i>
 Nouvelle Organisation
 </a>
 @endcan
 
 @can('Admin')
 <a href="{{ route('admin.users.index') }}" 
 class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
 <i class="fas fa-users mr-2"></i>
 Gérer Utilisateurs
 </a>
 @endcan
 
 @can('Gestionnaire Flotte')
 <a href="{{ route('admin.vehicles.create') }}" 
 class="flex items-center justify-center px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
 <i class="fas fa-car mr-2"></i>
 Nouveau Véhicule
 </a>
 
 <a href="{{ route('admin.drivers.create') }}" 
 class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
 <i class="fas fa-id-card mr-2"></i>
 Nouveau Chauffeur
 </a>
 @endcan
 </div>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
 console.log('🎉 Dashboard Admin chargé avec succès !');
 
 // Animation des cartes statistiques
 const cards = document.querySelectorAll('.bg-gradient-to-br');
 cards.forEach((card, index) => {
 card.style.opacity = '0';
 card.style.transform = 'translateY(20px)';
 
 setTimeout(() => {
 card.style.transition = 'all 0.6s ease';
 card.style.opacity = '1';
 card.style.transform = 'translateY(0)';
 }, index * 150);
 });
});
</script>
@endpush

