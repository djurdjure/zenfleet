{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Utilisateurs - ZenFleet')

@push('styles')
<style>
.stats-card {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 transition: transform 0.2s ease;
}
.stats-card:hover {
 transform: translateY(-2px);
}
.user-avatar {
 width: 40px;
 height: 40px;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-weight: 600;
 font-size: 14px;
 color: white;
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
<div class="fade-in" x-data="{
 showConfirmModal: false,
 userToDelete: {},
 deleteFormUrl: '',
 searchQuery: '',
 selectedRole: ''
}">
 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

 {{-- En-tête avec fil d'Ariane --}}
 <div class="mb-6">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home mr-1"></i> Tableau de bord
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-blue-600 font-semibold">Utilisateurs</span>
 </nav>

 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
 <p class="text-sm text-gray-600 mt-1">Gérez les comptes utilisateurs et leurs permissions</p>
 </div>
 @can('create users')
 <a href="{{ route('admin.users.create') }}"
 class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-lg hover:shadow-xl">
 <i class="fas fa-user-plus mr-2"></i>
 Nouvel Utilisateur
 </a>
 @endcan
 </div>
 </div>

 {{-- Messages de succès/erreur --}}
 @if (session('success'))
 <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm" role="alert">
 <div class="flex items-center">
 <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
 <p class="font-semibold text-green-800">{{ session('success') }}</p>
 </div>
 </div>
 @endif

 @if (session('error'))
 <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm" role="alert">
 <div class="flex items-center">
 <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
 <p class="font-semibold text-red-800">{{ session('error') }}</p>
 </div>
 </div>
 @endif

 {{-- Statistiques en haut --}}
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
 <div class="stats-card rounded-xl p-6 text-white shadow-lg">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-white/80">Total Utilisateurs</p>
 <p class="text-3xl font-bold mt-2">{{ $users->total() }}</p>
 </div>
 <div class="bg-white/20 rounded-full p-3">
 <i class="fas fa-users text-2xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-white/80">Administrateurs</p>
 <p class="text-3xl font-bold mt-2">{{ $users->filter(fn($u) => $u->hasRole('Admin') || $u->hasRole('Super Admin'))->count() }}</p>
 </div>
 <div class="bg-white/20 rounded-full p-3">
 <i class="fas fa-user-shield text-2xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-white/80">Superviseurs</p>
 <p class="text-3xl font-bold mt-2">{{ $users->filter(fn($u) => $u->hasRole('Superviseur'))->count() }}</p>
 </div>
 <div class="bg-white/20 rounded-full p-3">
 <i class="fas fa-user-tie text-2xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-white/80">Chauffeurs</p>
 <p class="text-3xl font-bold mt-2">{{ $users->filter(fn($u) => $u->hasRole('Chauffeur'))->count() }}</p>
 </div>
 <div class="bg-white/20 rounded-full p-3">
 <i class="fas fa-id-card text-2xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Filtres de recherche --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-search mr-2 text-gray-400"></i>Rechercher
 </label>
 <input type="text"
 x-model="searchQuery"
 placeholder="Nom, email..."
 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
 </div>
 <div>
 <label class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user-tag mr-2 text-gray-400"></i>Filtrer par rôle
 </label>
 <select x-model="selectedRole"
 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
 <option value="">Tous les rôles</option>
 <option value="Super Admin">Super Admin</option>
 <option value="Admin">Admin</option>
 <option value="Superviseur">Superviseur</option>
 <option value="Gestionnaire Flotte">Gestionnaire Flotte</option>
 <option value="Chauffeur">Chauffeur</option>
 </select>
 </div>
 <div class="flex items-end">
 <button @click="searchQuery = ''; selectedRole = ''"
 class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
 <i class="fas fa-redo mr-2"></i>Réinitialiser
 </button>
 </div>
 </div>
 </div>

 {{-- Table des utilisateurs --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
 <div class="overflow-x-auto">
 <table class="w-full divide-y divide-gray-200">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Utilisateur</th>
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact</th>
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Organisation</th>
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rôles</th>
 <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Inscrit le</th>
 <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse ($users as $user)
 <tr class="hover:bg-blue-50/50 transition-colors"
 x-show="(searchQuery === '' || '{{ strtolower($user->name . ' ' . $user->email) }}'.includes(searchQuery.toLowerCase())) &&
 (selectedRole === '' || '{{ $user->roles->pluck('name')->implode(',') }}'.includes(selectedRole))">
 <td class="px-6 py-4">
 <div class="flex items-center">
 <div class="user-avatar bg-gradient-to-br from-blue-500 to-indigo-600 mr-3">
 {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
 </div>
 <div>
 <p class="text-sm font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
 <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <p class="text-sm text-gray-900"><i class="fas fa-envelope text-gray-400 mr-2"></i>{{ $user->email }}</p>
 @if($user->phone)
 <p class="text-xs text-gray-500 mt-1"><i class="fas fa-phone text-gray-400 mr-2"></i>{{ $user->phone }}</p>
 @endif
 </td>
 <td class="px-6 py-4 text-sm text-gray-700">
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
 <i class="fas fa-building mr-2 text-gray-500"></i>
 {{ $user->organization->name ?? 'N/A' }}
 </span>
 @if($user->vehicles_count > 0)
 <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
 <i class="fas fa-car mr-1"></i> {{ $user->vehicles_count }}
 </span>
 @endif
 </td>
 <td class="px-6 py-4">
 <div class="flex flex-wrap gap-1">
 @forelse($user->roles as $role)
 @php
 $roleColors = [
 'Super Admin' => 'bg-red-100 text-red-800 border-red-200',
 'Admin' => 'bg-purple-100 text-purple-800 border-purple-200',
 'Superviseur' => 'bg-orange-100 text-orange-800 border-orange-200',
 'Gestionnaire Flotte' => 'bg-blue-100 text-blue-800 border-blue-200',
 'Chauffeur' => 'bg-green-100 text-green-800 border-green-200',
 ];
 $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
 @endphp
 <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colorClass }}">
 {{ $role->name }}
 </span>
 @empty
 <span class="text-xs italic text-gray-400">Aucun rôle</span>
 @endforelse
 </div>
 </td>
 <td class="px-6 py-4 text-sm text-gray-600">
 <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
 {{ $user->created_at->format('d/m/Y') }}
 </td>
 <td class="px-6 py-4 text-right">
 <div class="flex items-center justify-end space-x-2">
 {{-- ✨ BOUTON PERMISSIONS --}}
 @can('edit users')
 <a href="{{ route('admin.users.permissions', $user) }}"
 title="Gérer les Permissions"
 class="p-2 rounded-lg text-purple-600 hover:bg-purple-100 transition-colors">
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
 </svg>
 </a>
 @endcan

 {{-- ✨ BOUTON ACCÈS VÉHICULES --}}
 @can('edit users')
 <a href="{{ route('admin.users.vehicles.manage', $user) }}"
 title="Gérer l'accès aux véhicules"
 class="p-2 rounded-lg text-green-600 hover:bg-green-100 transition-colors">
 <i class="fas fa-car text-lg"></i>
 </a>
 @endcan

 @can('edit users')
 <a href="{{ route('admin.users.edit', $user) }}"
 title="Modifier"
 class="p-2 rounded-lg text-blue-600 hover:bg-blue-100 transition-colors">
 <i class="fas fa-edit text-lg"></i>
 </a>
 @endcan

 @can('delete users')
 @if(auth()->id() !== $user->id)
 <button type="button"
 @click="showConfirmModal = true; userToDelete = {{ json_encode($user->only(['id', 'name', 'email'])) }}; deleteFormUrl = '{{ route('admin.users.destroy', $user->id) }}'"
 title="Supprimer"
 class="p-2 rounded-lg text-red-600 hover:bg-red-100 transition-colors">
 <i class="fas fa-trash-alt text-lg"></i>
 </button>
 @endif
 @endcan
 </div>
 </td>
 </tr>
 @empty
 <tr><td colspan="6" class="px-6 py-12 text-center">
 <i class="fas fa-users text-5xl text-gray-300 mb-4"></i>
 <p class="text-gray-500 font-medium">Aucun utilisateur trouvé</p>
 </td></tr>
 @endforelse
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
 {{ $users->links() }}
 </div>
 </div>
 </div>

 {{-- Fenêtre Modale de Confirmation de Suppression --}}
 <div x-show="showConfirmModal"
 x-cloak
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="fixed inset-0 z-50 overflow-y-auto"
 style="display: none;">
 <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
 {{-- Background overlay --}}
 <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" @click="showConfirmModal = false"></div>

 {{-- Modal panel --}}
 <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
 @click.away="showConfirmModal = false">
 <div class="bg-white px-6 pt-6 pb-4">
 <div class="sm:flex sm:items-start">
 <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 mx-auto bg-red-100 rounded-full sm:mx-0">
 <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
 <h3 class="text-xl font-bold leading-6 text-gray-900 mb-3">
 Confirmer la Suppression
 </h3>
 <div class="mt-2 space-y-3">
 <div class="bg-red-50 border border-red-200 rounded-lg p-4">
 <p class="text-sm text-gray-700 mb-2">
 Vous êtes sur le point de supprimer l'utilisateur :
 </p>
 <div class="flex items-center space-x-3 bg-white rounded-lg p-3 border border-red-300">
 <div class="user-avatar bg-gradient-to-br from-red-500 to-red-600 flex-shrink-0">
 <i class="fas fa-user"></i>
 </div>
 <div>
 <p class="font-bold text-gray-900" x-text="userToDelete.name || userToDelete.email"></p>
 <p class="text-xs text-gray-600" x-text="userToDelete.email"></p>
 </div>
 </div>
 </div>
 <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
 <div class="flex">
 <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
 <p class="text-sm text-yellow-800">
 Cette action est <strong>définitive et irréversible</strong>. Toutes les données associées seront perdues.
 </p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
 <form :action="deleteFormUrl" method="POST" class="sm:ml-0">
 @csrf
 @method('DELETE')
 <button type="submit"
 class="inline-flex w-full justify-center items-center rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 text-sm font-bold text-white shadow-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto transition-all">
 <i class="fas fa-trash-alt mr-2"></i>
 Supprimer Définitivement
 </button>
 </form>
 <button type="button"
 @click="showConfirmModal = false"
 class="mt-3 inline-flex w-full justify-center items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-all">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </button>
 </div>
 </div>
 </div>
 </div>

</div>
@endsection