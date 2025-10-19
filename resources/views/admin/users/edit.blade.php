@extends('layouts.admin.catalyst')
@section('title', 'Modifier Utilisateur - ZenFleet')

@push('styles')
<style>
.role-checkbox-card {
 transition: all 0.2s ease;
 cursor: pointer;
}
.role-checkbox-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.role-checkbox-card input:checked + label {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 border-color: #667eea;
}
.user-info-badge {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
 <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

 {{-- En-tête avec fil d'Ariane --}}
 <div class="mb-6">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home mr-1"></i> Tableau de bord
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600 transition-colors">
 Utilisateurs
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-blue-600 font-semibold">Modifier</span>
 </nav>

 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Modifier l'Utilisateur</h1>
 <p class="text-sm text-gray-600 mt-1">Mettez à jour les informations du compte</p>
 </div>
 </div>
 </div>

 {{-- Carte d'information utilisateur --}}
 <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden mb-6">
 <div class="user-info-badge px-8 py-6">
 <div class="flex items-center justify-between space-x-4">
 <div class="flex items-center space-x-4 flex-1">
 <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold">
 {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
 </div>
 <div class="flex-1">
 <h2 class="text-2xl font-bold text-white">{{ $user->first_name }} {{ $user->last_name }}</h2>
 <p class="text-blue-100 text-sm">{{ $user->email }}</p>
 </div>
 <div class="flex flex-wrap gap-2">
 @foreach($user->roles as $role)
 <span class="px-3 py-1 bg-white/20 border border-white/30 rounded-full text-xs font-semibold text-white">
 {{ $role->name }}
 </span>
 @endforeach
 </div>
 </div>

 {{-- ✨ BOUTON GESTION DES PERMISSIONS --}}
 <a href="{{ route('admin.users.permissions', $user->id) }}"
 class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-lg font-bold text-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all shadow-lg hover:shadow-xl">
 <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
 </svg>
 Gérer les Permissions
 </a>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
 <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-8 py-6">
 <h2 class="text-xl font-bold text-white flex items-center">
 <i class="fas fa-user-edit mr-3 text-2xl"></i>
 Informations de l'Utilisateur
 </h2>
 <p class="text-indigo-100 text-sm mt-1">Modifiez les détails du compte utilisateur</p>
 </div>

 <div class="p-8 text-gray-900">

 @if ($errors->any())
 <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-5" role="alert">
 <div class="flex items-start">
 <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3 mt-0.5"></i>
 <div class="flex-1">
 <p class="font-bold text-red-800 mb-2">Veuillez corriger les erreurs ci-dessous</p>
 <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
 @foreach ($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 </div>
 </div>
 @endif

 <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-8">
 @csrf
 @method('PUT')

 {{-- Section : Informations Personnelles --}}
 <div>
 <div class="flex items-center mb-4">
 <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-user text-blue-600"></i>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Informations Personnelles</h3>
 <p class="text-sm text-gray-600">Nom, prénom et coordonnées</p>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-13">
 <div>
 <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user-circle text-gray-400 mr-2"></i>Prénom <span class="text-red-500">*</span>
 </label>
 <input type="text"
 id="first_name"
 name="first_name"
 value="{{ old('first_name', $user->first_name) }}"
 required
 autofocus
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
 @error('first_name')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-user-circle text-gray-400 mr-2"></i>Nom de famille <span class="text-red-500">*</span>
 </label>
 <input type="text"
 id="last_name"
 name="last_name"
 value="{{ old('last_name', $user->last_name) }}"
 required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
 @error('last_name')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div class="md:col-span-2">
 <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-envelope text-gray-400 mr-2"></i>Adresse Email <span class="text-red-500">*</span>
 </label>
 <input type="email"
 id="email"
 name="email"
 value="{{ old('email', $user->email) }}"
 required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
 @error('email')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div class="md:col-span-2">
 <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone <span class="text-gray-400 text-xs">(optionnel)</span>
 </label>
 <input type="text"
 id="phone"
 name="phone"
 value="{{ old('phone', $user->phone) }}"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
 placeholder="+213 XX XX XX XX">
 @error('phone')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>

 {{-- Section : Organisation --}}
 <div class="border-t border-gray-200 pt-8">
 <div class="flex items-center mb-4">
 <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-building text-purple-600"></i>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Organisation</h3>
 <p class="text-sm text-gray-600">Organisation de rattachement</p>
 </div>
 </div>

 <div class="ml-13">
 <label for="organization_id" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-sitemap text-gray-400 mr-2"></i>Organisation <span class="text-red-500">*</span>
 </label>
 <select name="organization_id"
 id="organization_id"
 required
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
 @foreach($organizations as $org)
 <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>
 {{ $org->name }}
 </option>
 @endforeach
 </select>
 @error('organization_id')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Section : Changer le Mot de passe --}}
 <div class="border-t border-gray-200 pt-8">
 <div class="flex items-center mb-4">
 <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-lock text-green-600"></i>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Changer le Mot de passe</h3>
 <p class="text-sm text-gray-600">Optionnel - Laissez vide pour conserver l'actuel</p>
 </div>
 </div>

 <div class="ml-13">
 <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
 <div class="flex">
 <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
 <p class="text-sm text-yellow-800">
 <strong>Remarque :</strong> Laissez les champs vides si vous ne souhaitez pas modifier le mot de passe.
 </p>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-key text-gray-400 mr-2"></i>Nouveau Mot de passe
 </label>
 <input type="password"
 id="password"
 name="password"
 autocomplete="new-password"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
 placeholder="••••••••">
 <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
 @error('password')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
 <i class="fas fa-key text-gray-400 mr-2"></i>Confirmer le mot de passe
 </label>
 <input type="password"
 id="password_confirmation"
 name="password_confirmation"
 class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
 placeholder="••••••••">
 @error('password_confirmation')
 <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Section : Rôles & Permissions --}}
 <div class="border-t border-gray-200 pt-8">
 <div class="flex items-center mb-4">
 <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
 <i class="fas fa-user-shield text-orange-600"></i>
 </div>
 <div>
 <h3 class="text-lg font-bold text-gray-900">Rôles & Permissions</h3>
 <p class="text-sm text-gray-600">Modifier les accès et permissions</p>
 </div>
 </div>

 <div class="ml-13">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
 @foreach ($roles as $role)
 @php
 $roleIcons = [
 'Super Admin' => 'fa-crown',
 'Admin' => 'fa-user-shield',
 'Superviseur' => 'fa-user-tie',
 'Gestionnaire Flotte' => 'fa-car',
 'Chauffeur' => 'fa-id-card',
 ];
 $roleColors = [
 'Super Admin' => 'from-red-500 to-red-600',
 'Admin' => 'from-purple-500 to-purple-600',
 'Superviseur' => 'from-orange-500 to-orange-600',
 'Gestionnaire Flotte' => 'from-blue-500 to-blue-600',
 'Chauffeur' => 'from-green-500 to-green-600',
 ];
 $icon = $roleIcons[$role->name] ?? 'fa-user';
 $color = $roleColors[$role->name] ?? 'from-gray-500 to-gray-600';
 $isChecked = in_array($role->id, old('roles', $user->roles->pluck('id')->toArray()));
 @endphp
 <div class="role-checkbox-card">
 <input type="checkbox"
 name="roles[]"
 id="role_{{ $role->id }}"
 value="{{ $role->id }}"
 class="peer hidden"
 {{ $isChecked ? 'checked' : '' }}>
 <label for="role_{{ $role->id }}"
 class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition-all peer-checked:bg-gradient-to-r peer-checked:{{ $color }} peer-checked:text-white peer-checked:border-transparent">
 <i class="fas {{ $icon }} text-xl mr-3"></i>
 <span class="font-semibold text-sm">{{ $role->name }}</span>
 </label>
 </div>
 @endforeach
 </div>
 @error('roles')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Boutons d'action --}}
 <div class="border-t border-gray-200 pt-6 flex items-center justify-between">
 <a href="{{ route('admin.users.index') }}"
 class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </a>
 <button type="submit"
 class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-700 border border-transparent rounded-lg font-bold text-sm text-white hover:from-indigo-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg hover:shadow-xl">
 <i class="fas fa-save mr-2"></i>
 Mettre à Jour l'Utilisateur
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>
@endsection