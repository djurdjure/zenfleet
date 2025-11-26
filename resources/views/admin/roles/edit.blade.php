{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Modifier Rôle - ZenFleet')

@push('styles')
<style>
.permission-checkbox {
 transition: all 0.2s ease;
}
.permission-checkbox:hover {
 transform: scale(1.05);
}
.permission-checkbox input:checked + label {
 background: linear-gradient(135deg, #10b981 0%, #059669 100%);
 color: white;
 border-color: #10b981;
}
.fade-in {
 animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}
.category-badge {
 transition: all 0.2s ease;
}
.category-badge:hover {
 transform: translateX(4px);
}
</style>
@endpush

@section('content')
<div class="fade-in" x-data="{
 selectAll: false,
 selectedCount: {{ $role->permissions->count() }},
 totalCount: {{ $allPermissions->count() }},
 toggleAll() {
 const checkboxes = document.querySelectorAll('.permission-check');
 checkboxes.forEach(cb => cb.checked = this.selectAll);
 this.updateCount();
 },
 updateCount() {
 this.selectedCount = document.querySelectorAll('.permission-check:checked').length;
 }
}">
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

 {{-- En-tête avec fil d'Ariane --}}
 <div class="mb-6">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home mr-1"></i> Tableau de bord
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.roles.index') }}" class="hover:text-blue-600 transition-colors">
 Rôles & Permissions
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-blue-600 font-semibold">Modifier</span>
 </nav>

 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Modifier le Rôle</h1>
 <p class="text-sm text-gray-600 mt-1">Gérez les permissions pour ce rôle</p>
 </div>
 </div>
 </div>

 @php
 $roleConfig = [
 'Super Admin' => ['gradient' => 'from-red-500 to-red-600', 'icon' => 'fa-crown'],
 'Admin' => ['gradient' => 'from-purple-500 to-purple-600', 'icon' => 'fa-user-shield'],
 'Superviseur' => ['gradient' => 'from-orange-500 to-orange-600', 'icon' => 'fa-user-tie'],
 'Gestionnaire Flotte' => ['gradient' => 'from-blue-500 to-blue-600', 'icon' => 'fa-car'],
 'Chauffeur' => ['gradient' => 'from-green-500 to-green-600', 'icon' => 'fa-id-card'],
 ];
 $config = $roleConfig[$role->name] ?? ['gradient' => 'from-gray-500 to-gray-600', 'icon' => 'fa-user'];
 @endphp

 {{-- Carte d'information du rôle --}}
 <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-6">
 <div class="bg-gradient-to-r {{ $config['gradient'] }} px-8 py-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
 <i class="fas {{ $config['icon'] }} text-3xl text-white"></i>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-white">{{ $role->name }}</h2>
 <p class="text-white/80 text-sm">ID: {{ $role->id }}</p>
 </div>
 </div>
 <div class="text-right">
 <p class="text-white/80 text-sm">Permissions Actives</p>
 <p class="text-4xl font-bold text-white" x-text="selectedCount"></p>
 <p class="text-white/60 text-xs">sur <span x-text="totalCount"></span> disponibles</p>
 </div>
 </div>
 </div>
 </div>

 <form method="POST" action="{{ route('admin.roles.update', $role) }}">
 @csrf
 @method('PUT')

 {{-- Contrôles rapides --}}
 <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-4">
 <label class="flex items-center cursor-pointer">
 <input type="checkbox"
 x-model="selectAll"
 @change="toggleAll()"
 class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
 <span class="ml-3 font-semibold text-gray-700">
 <span x-show="!selectAll">Tout sélectionner</span>
 <span x-show="selectAll">Tout désélectionner</span>
 </span>
 </label>
 <div class="h-6 w-px bg-gray-300"></div>
 <span class="text-sm text-gray-600">
 <i class="fas fa-info-circle text-blue-500 mr-1"></i>
 <span x-text="selectedCount"></span> permission(s) sélectionnée(s)
 </span>
 </div>
 <div class="flex space-x-3">
 <a href="{{ route('admin.roles.index') }}"
 class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 transition-all">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </a>
 <button type="submit"
 class="inline-flex items-center px-6 py-2 bg-gradient-to-r {{ $config['gradient'] }} border border-transparent rounded-lg font-bold text-sm text-white hover:opacity-90 transition-all shadow-lg hover:shadow-xl">
 <i class="fas fa-save mr-2"></i>
 Enregistrer les Permissions
 </button>
 </div>
 </div>
 </div>

 {{-- Matrice de permissions par catégorie --}}
 <div class="space-y-6">
 @foreach ($orderedCategories as $category => $permissions)
 @php
  $categoryConfig = [
  'organizations' => ['icon' => 'fa-building', 'color' => 'indigo', 'label' => 'Organisations'],
  'users' => ['icon' => 'fa-users', 'color' => 'blue', 'label' => 'Utilisateurs'],
  'roles' => ['icon' => 'fa-user-shield', 'color' => 'purple', 'label' => 'Rôles & Permissions'],
  'vehicles' => ['icon' => 'fa-car', 'color' => 'blue', 'label' => 'Véhicules'],
  'drivers' => ['icon' => 'fa-id-card', 'color' => 'green', 'label' => 'Chauffeurs'],
  'assignments' => ['icon' => 'fa-key', 'color' => 'yellow', 'label' => 'Affectations'],
  'depots' => ['icon' => 'fa-warehouse', 'color' => 'gray', 'label' => 'Dépôts'],
  'maintenance' => ['icon' => 'fa-wrench', 'color' => 'red', 'label' => 'Maintenance'],
  'repairs' => ['icon' => 'fa-hammer', 'color' => 'orange', 'label' => 'Réparations'],
  'mileage' => ['icon' => 'fa-tachometer-alt', 'color' => 'cyan', 'label' => 'Kilométrage'],
  'suppliers' => ['icon' => 'fa-truck-loading', 'color' => 'teal', 'label' => 'Fournisseurs'],
  'expenses' => ['icon' => 'fa-file-invoice-dollar', 'color' => 'emerald', 'label' => 'Dépenses'],
  'documents' => ['icon' => 'fa-file-alt', 'color' => 'slate', 'label' => 'Documents'],
  'alerts' => ['icon' => 'fa-bell', 'color' => 'rose', 'label' => 'Alertes'],
  'sanctions' => ['icon' => 'fa-gavel', 'color' => 'red', 'label' => 'Sanctions'],
  'reports' => ['icon' => 'fa-chart-line', 'color' => 'violet', 'label' => 'Rapports & Analytics'],
  'audit' => ['icon' => 'fa-history', 'color' => 'gray', 'label' => 'Audit Logs'],
  'autres' => ['icon' => 'fa-cog', 'color' => 'gray', 'label' => 'Autres'],
  ];
 $catConfig = $categoryConfig[$category] ?? ['icon' => 'fa-cube', 'color' => 'gray', 'label' => ucfirst($category)];
 @endphp

 <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
 <div class="bg-gradient-to-r from-{{ $catConfig['color'] }}-50 to-{{ $catConfig['color'] }}-100 px-6 py-4 border-b border-{{ $catConfig['color'] }}-200">
 <div class="flex items-center justify-between">
 <div class="flex items-center space-x-3">
 <div class="w-10 h-10 bg-{{ $catConfig['color'] }}-500 rounded-lg flex items-center justify-center">
 <i class="fas {{ $catConfig['icon'] }} text-white"></i>
 </div>
 <div>
 <h3 class="text-lg font-bold text-{{ $catConfig['color'] }}-900">{{ $catConfig['label'] }}</h3>
 <p class="text-xs text-{{ $catConfig['color'] }}-600">{{ $permissions->count() }} permission(s)</p>
 </div>
 </div>
 <span class="px-3 py-1 bg-{{ $catConfig['color'] }}-500 text-white text-xs font-bold rounded-full">
 {{ $permissions->where(fn($p) => $role->hasPermissionTo($p))->count() }} / {{ $permissions->count() }}
 </span>
 </div>
 </div>

 <div class="p-6">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
 @foreach ($permissions as $permission)
 <div class="permission-checkbox">
 <input type="checkbox"
 name="permissions[]"
 id="permission_{{ $permission->id }}"
 value="{{ $permission->id }}"
 class="peer hidden permission-check"
 @change="updateCount()"
 @if($role->hasPermissionTo($permission)) checked @endif>
 <label for="permission_{{ $permission->id }}"
 class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-{{ $catConfig['color'] }}-400 transition-all peer-checked:bg-gradient-to-r peer-checked:from-green-500 peer-checked:to-green-600 peer-checked:text-white peer-checked:border-green-500 peer-checked:shadow-md">
 <i class="fas fa-check-circle text-lg mr-2 opacity-0 peer-checked:opacity-100"></i>
 <span class="font-medium text-sm">{{ $permission->name }}</span>
 </label>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @endforeach
 </div>

 {{-- Boutons d'action en bas --}}
 <div class="mt-8 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center justify-between">
 <div class="text-sm text-gray-600">
 <i class="fas fa-info-circle text-blue-500 mr-2"></i>
 Les modifications seront appliquées immédiatement à tous les utilisateurs ayant ce rôle.
 </div>
 <div class="flex space-x-3">
 <a href="{{ route('admin.roles.index') }}"
 class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 transition-all">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </a>
 <button type="submit"
 class="inline-flex items-center px-8 py-3 bg-gradient-to-r {{ $config['gradient'] }} border border-transparent rounded-lg font-bold text-sm text-white hover:opacity-90 transition-all shadow-lg hover:shadow-xl">
 <i class="fas fa-save mr-2"></i>
 Enregistrer les Permissions (<span x-text="selectedCount"></span>)
 </button>
 </div>
 </div>
 </div>
 </form>
 </div>
</div>
@endsection
