{{-- resources/views/admin/roles/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Rôles - ZenFleet')

@push('styles')
<style>
.role-card {
 transition: all 0.3s ease;
 cursor: pointer;
}
.role-card:hover {
 transform: translateY(-4px);
 box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}
.fade-in {
 animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')
<div class="fade-in">
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

 {{-- En-tête avec fil d'Ariane --}}
 <div class="mb-6">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home mr-1"></i> Tableau de bord
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-blue-600 font-semibold">Rôles & Permissions</span>
 </nav>

 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Gestion des Rôles</h1>
 <p class="text-sm text-gray-600 mt-1">Gérez les rôles et leurs permissions associées</p>
 </div>

 @can('manage', \Spatie\Permission\Models\Role::class)
 <a href="{{ route('admin.permissions.index') }}"
 class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-600 transition-all transform hover:scale-105">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
 </svg>
 Matrice des Permissions
 </a>
 @endcan
 </div>
 </div>

 @if($isSuperAdmin)
 <form method="GET" class="mb-6 bg-white border border-gray-200 rounded-xl shadow-sm p-5">
 <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
 <div>
 <label class="block text-xs font-semibold text-gray-600 mb-2">Contexte</label>
 <select name="context" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
 onchange="this.form.submit()">
 <option value="organization" {{ $context === 'organization' ? 'selected' : '' }}>Organisation</option>
 <option value="global" {{ $context === 'global' ? 'selected' : '' }}>Rôles globaux</option>
 <option value="all" {{ $context === 'all' ? 'selected' : '' }}>Toutes les organisations</option>
 </select>
 </div>

 @if($context === 'organization')
 <div>
 <label class="block text-xs font-semibold text-gray-600 mb-2">Organisation</label>
 <select name="organization_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
 onchange="this.form.submit()">
 @foreach($organizations as $org)
 <option value="{{ $org->id }}" {{ (int) $selectedOrgId === (int) $org->id ? 'selected' : '' }}>
 {{ $org->name }}{{ $org->legal_name ? ' · ' . $org->legal_name : '' }}
 </option>
 @endforeach
 </select>
 </div>

 <div class="flex items-center space-x-3">
 <label class="inline-flex items-center text-sm text-gray-700">
 <input type="checkbox" name="include_global" value="1"
 class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
 onchange="this.form.submit()" {{ $includeGlobal ? 'checked' : '' }}>
 <span class="ml-2">Inclure rôles globaux</span>
 </label>
 </div>
 @endif

 <div class="text-xs text-gray-500">
 <p>Astuce : sélectionnez “Organisation” pour éviter les doublons par nom.</p>
 </div>
 </div>
 </form>
 @endif

 {{-- Messages de succès --}}
 @if (session('success'))
 <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm" role="alert">
 <div class="flex items-center">
 <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
 <p class="font-semibold text-green-800">{{ session('success') }}</p>
 </div>
 </div>
 @endif

 {{-- Cartes des rôles --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach ($roles as $role)
 @php
 $roleConfig = [
 'Super Admin' => [
 'gradient' => 'from-red-500 to-red-600',
 'icon' => 'fa-crown',
 'iconBg' => 'bg-red-100',
 'iconColor' => 'text-red-600',
 'description' => 'Accès total et illimité à toutes les fonctionnalités'
 ],
 'Admin' => [
 'gradient' => 'from-purple-500 to-purple-600',
 'icon' => 'fa-user-shield',
 'iconBg' => 'bg-purple-100',
 'iconColor' => 'text-purple-600',
 'description' => 'Gestion complète de son organisation'
 ],
 'Superviseur' => [
 'gradient' => 'from-orange-500 to-orange-600',
 'icon' => 'fa-user-tie',
 'iconBg' => 'bg-orange-100',
 'iconColor' => 'text-orange-600',
 'description' => 'Supervision des opérations et du personnel'
 ],
 'Gestionnaire Flotte' => [
 'gradient' => 'from-blue-500 to-blue-600',
 'icon' => 'fa-car',
 'iconBg' => 'bg-blue-100',
 'iconColor' => 'text-blue-600',
 'description' => 'Gestion des véhicules et affectations'
 ],
 'Chauffeur' => [
 'gradient' => 'from-green-500 to-green-600',
 'icon' => 'fa-id-card',
 'iconBg' => 'bg-green-100',
 'iconColor' => 'text-green-600',
 'description' => 'Accès limité aux missions assignées'
 ],
 ];
 $config = $roleConfig[$role->name] ?? [
 'gradient' => 'from-gray-500 to-gray-600',
 'icon' => 'fa-user',
 'iconBg' => 'bg-gray-100',
 'iconColor' => 'text-gray-600',
 'description' => 'Rôle personnalisé'
 ];
 @endphp

 <div class="role-card bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
 {{-- Header avec gradient --}}
 <div class="bg-gradient-to-r {{ $config['gradient'] }} px-6 py-5">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
 <i class="fas {{ $config['icon'] }} text-2xl text-white"></i>
 </div>
 <div>
 <h3 class="text-xl font-bold text-white">{{ $role->name }}</h3>
 <p class="text-white/80 text-xs">
 ID: {{ $role->id }} •
 {{ $role->organization_id ? 'Org #' . $role->organization_id : 'Global' }}
 </p>
 </div>
 </div>
 </div>
 </div>

 {{-- Body --}}
 <div class="p-6">
 <p class="text-sm text-gray-600 mb-4">{{ $config['description'] }}</p>
 <div class="mb-4">
 <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $role->organization_id ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
 {{ $role->organization_id ? 'Organisation #' . $role->organization_id : 'Rôle global' }}
 </span>
 </div>

 {{-- Statistiques --}}
 <div class="bg-gray-50 rounded-lg p-4 mb-4">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Permissions</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ $role->permissions->count() }}</p>
 </div>
 <div class="w-12 h-12 {{ $config['iconBg'] }} rounded-lg flex items-center justify-center">
 <i class="fas fa-key text-xl {{ $config['iconColor'] }}"></i>
 </div>
 </div>
 </div>

 {{-- Liste aperçu permissions (max 3) --}}
 @if($role->permissions->count() > 0)
 <div class="mb-4">
 <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Permissions clés :</p>
 <div class="space-y-1">
 @foreach($role->permissions->take(3) as $permission)
 <div class="flex items-center text-xs text-gray-600">
 <i class="fas fa-check-circle text-green-500 mr-2"></i>
 {{ $permission->name }}
 </div>
 @endforeach
 @if($role->permissions->count() > 3)
 <div class="text-xs text-gray-500 italic">
 + {{ $role->permissions->count() - 3 }} autres...
 </div>
 @endif
 </div>
 </div>
 @else
 <div class="mb-4 text-center py-3">
 <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
 <p class="text-xs text-gray-500">Aucune permission assignée</p>
 </div>
 @endif

 {{-- Bouton action --}}
 <a href="{{ route('admin.roles.edit', $role) }}"
 class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r {{ $config['gradient'] }} border border-transparent rounded-lg font-semibold text-sm text-white hover:opacity-90 transition-all shadow-md hover:shadow-lg">
 <i class="fas fa-cog mr-2"></i>
 Gérer les Permissions
 </a>
 </div>
 </div>
 @endforeach
 </div>

 {{-- Info box --}}
 <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
 <div class="flex items-start">
 <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
 <div>
 <h4 class="text-lg font-bold text-blue-900 mb-2">À propos des Rôles</h4>
 <p class="text-sm text-blue-800 mb-3">
 Les rôles définissent les permissions accordées aux utilisateurs dans le système. Chaque utilisateur peut avoir un ou plusieurs rôles.
 </p>
 <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-blue-800">
 <div class="flex items-start">
 <i class="fas fa-shield-alt text-blue-600 mr-2 mt-0.5"></i>
 <span><strong>Hiérarchie :</strong> Super Admin > Admin > Superviseur > Gestionnaire > Chauffeur</span>
 </div>
 <div class="flex items-start">
 <i class="fas fa-lock text-blue-600 mr-2 mt-0.5"></i>
 <span><strong>Sécurité :</strong> Les permissions sont héritées par les utilisateurs</span>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
