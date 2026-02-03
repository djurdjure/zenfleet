@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Chauffeurs - ZenFleet Enterprise')

@push('styles')
<style>
 [x-cloak] { display: none !important; }
 
 .driver-card {
 transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
 }
 
 .driver-card:hover {
 transform: translateY(-2px);
 }
 
 .status-badge {
 animation: fadeIn 0.3s ease-in-out;
 }
 
 @keyframes fadeIn {
 from { opacity: 0; transform: scale(0.9); }
 to { opacity: 1; transform: scale(1); }
 }
 
 .glass-effect {
 background: rgba(255, 255, 255, 0.95);
 backdrop-filter: blur(10px);
 -webkit-backdrop-filter: blur(10px);
 }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
 
 {{-- 🎯 Enterprise Header avec Statistiques en Temps Réel --}}
 <div class="max-w-8xl mx-auto mb-8">
 <div class="glass-effect rounded-2xl shadow-xl border border-white/50 p-8">
 
 {{-- Navigation Breadcrumb --}}
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home"></i> Dashboard
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="font-semibold text-gray-900">Gestion des Chauffeurs</span>
 </nav>
 
 {{-- Hero Section avec Stats --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 
 {{-- Titre et Actions --}}
 <div class="lg:col-span-2">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-2xl">
 <i class="fas fa-users text-white text-3xl"></i>
 </div>
 <div>
 <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
 Gestion des Chauffeurs
 </h1>
 <p class="text-gray-600 text-lg mt-2">
 Gérez votre équipe de conducteurs professionnels
 </p>
 </div>
 </div>
 </div>
 
 {{-- Actions Rapides --}}
 <div class="flex flex-wrap gap-3 mt-6">
 @can('drivers.create')
 <a href="{{ route('admin.drivers.create') }}" 
 class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
 <i class="fas fa-user-plus mr-2"></i>
 Nouveau Chauffeur
 </a>
 @endcan
 
 @can('drivers.export')
 <button onclick="exportDrivers()" 
 class="inline-flex items-center px-5 py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all shadow-md hover:shadow-lg border border-gray-200">
 <i class="fas fa-file-export mr-2"></i>
 Exporter
 </button>
 @endcan
 
 @can('drivers.import')
 <button onclick="importDrivers()" 
 class="inline-flex items-center px-5 py-3 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all shadow-md hover:shadow-lg border border-gray-200">
 <i class="fas fa-file-import mr-2"></i>
 Importer
 </button>
 @endcan
 </div>
 </div>
 
 {{-- Statistiques en Temps Réel --}}
 <div class="grid grid-cols-2 gap-4">
 <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
 <div class="text-3xl font-bold">{{ $drivers->total() ?? 0 }}</div>
 <div class="text-sm opacity-90 mt-1">Total Chauffeurs</div>
 <i class="fas fa-users text-white/20 text-4xl absolute bottom-2 right-2"></i>
 </div>
 
 <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-4 text-white">
 <div class="text-3xl font-bold">
 {{ $drivers->filter(function($d) { return $d->status && $d->status->can_drive ?? false; })->count() ?? 0 }}
 </div>
 <div class="text-sm opacity-90 mt-1">Disponibles</div>
 <i class="fas fa-check-circle text-white/20 text-4xl absolute bottom-2 right-2"></i>
 </div>
 
 <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl p-4 text-white">
 <div class="text-3xl font-bold">
 {{ $drivers->filter(function($d) { return $d->status && !($d->status->can_drive ?? true); })->count() ?? 0 }}
 </div>
 <div class="text-sm opacity-90 mt-1">Indisponibles</div>
 <i class="fas fa-times-circle text-white/20 text-4xl absolute bottom-2 right-2"></i>
 </div>
 
 <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 text-white">
 <div class="text-3xl font-bold">
 {{ $drivers->filter(function($d) { return $d->user_id; })->count() ?? 0 }}
 </div>
 <div class="text-sm opacity-90 mt-1">Avec Compte</div>
 <i class="fas fa-user-check text-white/20 text-4xl absolute bottom-2 right-2"></i>
 </div>
 </div>
 </div>
 </div>
 </div>
 
 {{-- 🔍 Barre de Filtres Avancés --}}
 <div class="max-w-8xl mx-auto mb-6">
 <div class="glass-effect rounded-xl shadow-lg border border-white/50 p-6">
 <form method="GET" action="{{ route('admin.drivers.index') }}" x-data="driverFilters()" class="space-y-4">
 
 {{-- Titre des Filtres --}}
 <div class="flex items-center justify-between mb-4">
 <h3 class="text-lg font-semibold text-gray-900 flex items-center">
 <i class="fas fa-filter mr-2 text-blue-500"></i>
 Filtres Avancés
 </h3>
 <button type="button" @click="resetFilters()" 
 class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
 <i class="fas fa-undo mr-1"></i>
 Réinitialiser
 </button>
 </div>
 
 {{-- Grille de Filtres --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
 
 {{-- Recherche Globale --}}
 <div class="lg:col-span-2">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-search mr-1"></i>Recherche
 </label>
 <div class="relative">
 <input type="text" 
 name="search" 
 value="{{ request('search') }}"
 placeholder="Nom, prénom, matricule, téléphone..."
 class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
 </div>
 </div>
 
 {{-- Filtre par Statut --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-user-tag mr-1"></i>Statut
 </label>
 <select name="status_id" 
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 <option value="">Tous les statuts</option>
 @foreach($driverStatuses as $status)
 @php
 // Gestion sécurisée array/object
 $statusId = is_array($status) ? $status['id'] : $status->id;
 $statusName = is_array($status) ? $status['name'] : $status->name;
 $statusColor = is_array($status) ? ($status['color'] ?? '#6B7280') : ($status->color ?? '#6B7280');
 @endphp
 <option value="{{ $statusId }}" 
 {{ request('status_id') == $statusId ? 'selected' : '' }}>
 {{ $statusName }}
 </option>
 @endforeach
 </select>
 </div>
 
 {{-- Organisation (pour Super Admin) --}}
 @if(auth()->user()->hasRole('Super Admin'))
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-building mr-1"></i>Organisation
 </label>
 <select name="organization_id" 
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 <option value="">Toutes</option>
 @foreach(\App\Models\Organization::all() as $org)
 <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
 {{ $org->name }}
 </option>
 @endforeach
 </select>
 </div>
 @endif
 
 {{-- Éléments par page --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-list-ol mr-1"></i>Par page
 </label>
 <select name="per_page" 
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
 <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
 <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
 <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
 <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
 </select>
 </div>
 </div>
 
 {{-- Filtres Avancés Collapsibles --}}
 <div x-data="{ showAdvanced: false }" class="mt-4">
 <button type="button" @click="showAdvanced = !showAdvanced" 
 class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
 <span x-show="!showAdvanced"><i class="fas fa-plus mr-1"></i>Plus de filtres</span>
 <span x-show="showAdvanced"><i class="fas fa-minus mr-1"></i>Moins de filtres</span>
 </button>
 
 <div x-show="showAdvanced" x-collapse class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
 {{-- Avec/Sans compte utilisateur --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-user-shield mr-1"></i>Compte Utilisateur
 </label>
 <select name="has_user" 
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50">
 <option value="">Tous</option>
 <option value="1" {{ request('has_user') == '1' ? 'selected' : '' }}>Avec compte</option>
 <option value="0" {{ request('has_user') == '0' ? 'selected' : '' }}>Sans compte</option>
 </select>
 </div>
 
 {{-- Date de recrutement --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-calendar-plus mr-1"></i>Recruté après
 </label>
 <input type="date" name="recruited_after" value="{{ request('recruited_after') }}"
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50">
 </div>
 
 {{-- Permis de conduire --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 <i class="fas fa-id-card mr-1"></i>Catégorie Permis
 </label>
 <input type="text" name="license_category" value="{{ request('license_category') }}"
 placeholder="Ex: B, C, D..."
 class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50">
 </div>
 </div>
 </div>
 
 {{-- Boutons d'action --}}
 <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
 <button type="submit" 
 class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl">
 <i class="fas fa-search mr-2"></i>
 Appliquer les filtres
 </button>
 </div>
 </form>
 </div>
 </div>
 
 {{-- 📊 Liste des Chauffeurs --}}
 <div class="max-w-8xl mx-auto">
 <div class="glass-effect rounded-xl shadow-lg border border-white/50 overflow-hidden">
 
 @if($drivers->count() > 0)
 {{-- Vue Grille/Liste Toggle --}}
 <div class="p-4 border-b border-gray-200 flex justify-between items-center">
 <div class="text-sm text-gray-600">
 <span class="font-semibold">{{ $drivers->total() }}</span> chauffeur(s) trouvé(s)
 </div>
 <div x-data="{ view: 'grid' }" class="flex gap-2">
 <button @click="view = 'grid'" 
 :class="view === 'grid' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
 class="p-2 rounded-lg transition-all">
 <i class="fas fa-th"></i>
 </button>
 <button @click="view = 'list'" 
 :class="view === 'list' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
 class="p-2 rounded-lg transition-all">
 <i class="fas fa-list"></i>
 </button>
 </div>
 </div>
 
 {{-- Tableau Responsive Enterprise --}}
 <div class="overflow-x-auto">
 <table class="w-full">
 <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
 <tr>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Photo
 </th>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Chauffeur
 </th>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Contact
 </th>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Permis
 </th>
 <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Organisation
 </th>
 <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($drivers as $driver)
 <tr class="driver-card hover:bg-blue-50/50 transition-all duration-200">
 {{-- Photo --}}
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex-shrink-0 h-12 w-12">
 @if($driver->photo)
 <img src="{{ Storage::url($driver->photo) }}" 
 alt="{{ $driver->getFullNameAttribute() }}"
 class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
 @else
 <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white font-semibold">
 {{ strtoupper(substr($driver->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($driver->last_name ?? '', 0, 1)) }}
 </div>
 @endif
 </div>
 </td>
 
 {{-- Infos Chauffeur --}}
 <td class="px-6 py-4">
 <div class="text-sm">
 <div class="font-semibold text-gray-900">
 {{ $driver->getFullNameAttribute() }}
 </div>
 <div class="text-gray-500">
 <i class="fas fa-id-badge mr-1"></i>{{ $driver->employee_number ?? 'N/A' }}
 </div>
 @if($driver->user)
 <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
 <i class="fas fa-user-check mr-1"></i>Compte actif
 </span>
 @endif
 </div>
 </td>
 
 {{-- Contact --}}
 <td class="px-6 py-4 text-sm">
 <div class="space-y-1">
 @if($driver->personal_phone)
 <div class="flex items-center text-gray-600">
 <i class="fas fa-phone mr-2 text-gray-400"></i>
 {{ $driver->personal_phone }}
 </div>
 @endif
 @if($driver->personal_email)
 <div class="flex items-center text-gray-600">
 <i class="fas fa-envelope mr-2 text-gray-400"></i>
 {{ $driver->personal_email }}
 </div>
 @endif
 </div>
 </td>
 
 {{-- Statut --}}
 <td class="px-6 py-4 whitespace-nowrap">
 @if($driver->status)
 @php
 $status = $driver->status;
 $statusColor = $status->color ?? '#6B7280';
 $statusIcon = $status->icon ?? 'fa-circle';
 $statusName = $status->name ?? 'Inconnu';
 @endphp
 <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
 style="background-color: {{ $statusColor }}20; color: {{ $statusColor }};">
 <i class="fas {{ $statusIcon }} mr-1"></i>
 {{ $statusName }}
 </span>
 @else
 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
 <i class="fas fa-question-circle mr-1"></i>
 Non défini
 </span>
 @endif
 </td>
 
 {{-- Permis --}}
 <td class="px-6 py-4 text-sm">
 @if($driver->license_number)
 <div class="space-y-1">
 <div class="font-medium text-gray-900">
 {{ $driver->license_category ?? 'N/A' }}
 </div>
 <div class="text-gray-500 text-xs">
 {{ $driver->license_number }}
 </div>
 @if($driver->license_expiry_date)
 @php
 $daysUntilExpiry = now()->diffInDays($driver->license_expiry_date, false);
 @endphp
 @if($daysUntilExpiry < 30)
 <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
 <i class="fas fa-exclamation-triangle mr-1"></i>
 Expire {{ $daysUntilExpiry > 0 ? "dans $daysUntilExpiry jours" : 'Expiré' }}
 </span>
 @endif
 @endif
 </div>
 @else
 <span class="text-gray-400">Non renseigné</span>
 @endif
 </td>
 
 {{-- Organisation --}}
 <td class="px-6 py-4 text-sm">
 @if($driver->organization)
 <div class="font-medium text-gray-900">
 {{ $driver->organization->name }}
 </div>
 @else
 <span class="text-gray-400">N/A</span>
 @endif
 </td>
 
 {{-- Actions --}}
 <td class="px-6 py-4 text-center">
 <div class="flex items-center justify-center gap-2">
 @can('drivers.view')
 <a href="{{ route('admin.drivers.show', $driver) }}" 
 class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors"
 title="Voir les détails">
 <i class="fas fa-eye"></i>
 </a>
 @endcan
 
 @can('drivers.update')
 <a href="{{ route('admin.drivers.edit', $driver) }}" 
 class="p-2 bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-colors"
 title="Modifier">
 <i class="fas fa-edit"></i>
 </a>
 @endcan
 
 @can('drivers.delete')
 <form action="{{ route('admin.drivers.destroy', $driver) }}" 
 method="POST" 
 class="inline"
 onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce chauffeur ?');">
 @csrf
 @method('DELETE')
 <button type="submit" 
 class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors"
 title="Supprimer">
 <i class="fas fa-trash"></i>
 </button>
 </form>
 @endcan
 </div>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 
 {{-- Pagination --}}
 <div class="px-6 py-4 border-t border-gray-200">
 {{ $drivers->withQueryString()->links() }}
 </div>
 
 @else
 {{-- Message si aucun chauffeur --}}
 <div class="p-16 text-center">
 <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
 <i class="fas fa-users-slash text-gray-400 text-3xl"></i>
 </div>
 <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun chauffeur trouvé</h3>
 <p class="text-gray-500 mb-6">
 @if(request()->anyFilled(['search', 'status_id', 'organization_id']))
 Aucun résultat ne correspond à vos critères de recherche.
 @else
 Commencez par ajouter votre premier chauffeur.
 @endif
 </p>
 @if(request()->anyFilled(['search', 'status_id', 'organization_id']))
 <a href="{{ route('admin.drivers.index') }}" 
 class="inline-flex items-center px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
 <i class="fas fa-undo mr-2"></i>
 Réinitialiser les filtres
 </a>
 @else
 @can('drivers.create')
 <a href="{{ route('admin.drivers.create') }}" 
 class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
 <i class="fas fa-user-plus mr-2"></i>
 Ajouter un chauffeur
 </a>
 @endcan
 @endif
 </div>
 @endif
 </div>
 </div>
</div>

{{-- 🎯 Modals et Scripts Enterprise --}}
@push('scripts')
<script>
 // Component Alpine pour les filtres
 function driverFilters() {
 return {
 resetFilters() {
 window.location.href = '{{ route("admin.drivers.index") }}';
 }
 }
 }
 
 // Export des chauffeurs
 function exportDrivers() {
 Swal.fire({
 title: 'Exporter les chauffeurs',
 text: 'Choisissez le format d\'export',
 icon: 'question',
 showCancelButton: true,
 confirmButtonText: 'CSV',
 cancelButtonText: 'Excel',
 showDenyButton: true,
 denyButtonText: 'PDF'
 }).then((result) => {
 if (result.isConfirmed || result.isDenied || result.dismiss === Swal.DismissReason.cancel) {
 let format = result.isConfirmed ? 'csv' : (result.isDenied ? 'pdf' : 'excel');
 window.location.href = '{{ route("admin.drivers.export") }}?format=' + format;
 }
 });
 }
 
 // Import des chauffeurs
 function importDrivers() {
 Swal.fire({
 title: 'Importer des chauffeurs',
 html: `
 <form id="importForm" enctype="multipart/form-data">
 <div class="mb-4">
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Fichier CSV ou Excel
 </label>
 <input type="file" name="file" accept=".csv,.xlsx,.xls" required
 class="w-full px-3 py-2 border border-gray-300 rounded-md">
 </div>
 <div class="text-xs text-gray-500">
 <a href="{{ route('admin.drivers.import.template') }}" class="text-blue-600 hover:underline">
 Télécharger le modèle
 </a>
 </div>
 </form>
 `,
 showCancelButton: true,
 confirmButtonText: 'Importer',
 cancelButtonText: 'Annuler',
 preConfirm: () => {
 const form = document.getElementById('importForm');
 const formData = new FormData(form);
 
 return fetch('{{ route("admin.drivers.import") }}', {
 method: 'POST',
 headers: {
 'X-CSRF-TOKEN': '{{ csrf_token() }}'
 },
 body: formData
 }).then(response => {
 if (!response.ok) {
 throw new Error(response.statusText);
 }
 return response.json();
 }).catch(error => {
 Swal.showValidationMessage(`Erreur: ${error}`);
 });
 }
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire('Succès!', 'Les chauffeurs ont été importés.', 'success')
 .then(() => window.location.reload());
 }
 });
 }
</script>
@endpush
@endsection
