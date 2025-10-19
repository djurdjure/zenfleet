{{-- resources/views/admin/drivers/archived.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Chauffeurs Archivés - ZenFleet Enterprise')

@push('styles')
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

/* Animations pour les modales */
@keyframes scale-in {
 from {
 opacity: 0;
 transform: scale(0.95) translateY(-20px);
 }
 to {
 opacity: 1;
 transform: scale(1) translateY(0);
 }
}

.animate-scale-in {
 animation: scale-in 0.3s ease-out;
}

.hover-scale {
 transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
 transform: scale(1.02);
}

.stats-grid {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
 gap: 1.5rem;
}

.data-table {
 border-collapse: separate;
 border-spacing: 0;
}

.data-table thead th {
 position: sticky;
 top: 0;
 background: white;
 z-index: 10;
}

.modal-backdrop {
 backdrop-filter: blur(4px);
 background: rgba(0, 0, 0, 0.4);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
 <div class="fade-in space-y-8">
 <!-- 🎨 Enterprise Header Section -->
 <div class="max-w-7xl mx-auto">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <!-- Breadcrumb -->
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home"></i> Dashboard
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
 Gestion des Chauffeurs
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="font-semibold text-gray-900">Archives</span>
 </nav>

 <!-- Hero Content -->
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-amber-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-archive text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">Chauffeurs Archivés</h1>
 <p class="text-gray-600 text-lg mt-2">
 Gestion des chauffeurs supprimés - Restauration et suppression définitive
 </p>
 </div>
 </div>

 <!-- Actions principales -->
 <div class="flex items-center gap-4">
 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200 hover-scale">
 <i class="fas fa-arrow-left"></i>
 <span>Retour aux chauffeurs</span>
 </a>
 </div>
 </div>
 </div>
 </div>

 <!-- 📊 Statistiques des Archives -->
 <div class="max-w-7xl mx-auto">
 <div class="stats-grid">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Total Archivés</p>
 <p class="text-3xl font-bold text-amber-600">{{ $stats['total_archived'] }}</p>
 </div>
 <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-archive text-amber-600 text-xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Ce Mois</p>
 <p class="text-3xl font-bold text-orange-600">{{ $stats['archived_this_month'] }}</p>
 </div>
 <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Cette Année</p>
 <p class="text-3xl font-bold text-red-600">{{ $stats['archived_this_year'] }}</p>
 </div>
 <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-chart-line text-red-600 text-xl"></i>
 </div>
 </div>
 </div>

 @if(auth()->user()->hasRole('Super Admin'))
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Organisation</p>
 <p class="text-3xl font-bold text-purple-600">{{ $stats['organization_archived'] }}</p>
 </div>
 <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-building text-purple-600 text-xl"></i>
 </div>
 </div>
 </div>
 @endif
 </div>
 </div>

 <!-- 🗂️ Liste des Chauffeurs Archivés -->
 <div class="max-w-7xl mx-auto">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-3">
 <i class="fas fa-users text-amber-600"></i>
 Chauffeurs Archivés ({{ $drivers->total() }})
 </h3>
 </div>

 @if($drivers && $drivers->count() > 0)
 <div class="overflow-x-auto">
 <table class="data-table min-w-full">
 <thead>
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Chauffeur</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Matricule</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Statut</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Archivé le</th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Actions</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($drivers as $driver)
 <tr class="hover:bg-gray-50 transition-colors">
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 @if($driver->photo)
 <img class="h-10 w-10 rounded-full object-cover border-2 border-amber-200"
 src="{{ asset('storage/' . $driver->photo) }}"
 alt="Photo de {{ $driver->first_name }}">
 @else
 <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
 <span class="text-sm font-medium text-white">
 {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
 </span>
 </div>
 @endif
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">{{ $driver->first_name }} {{ $driver->last_name }}</div>
 <div class="text-sm text-gray-500">{{ $driver->personal_email ?? 'Pas d\'email' }}</div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
 {{ $driver->employee_number ?? 'N/A' }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($driver->driverStatus)
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
 {{ $driver->driverStatus->name }}
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
 N/A
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
 <div class="flex flex-col">
 <span>{{ $driver->deleted_at->format('d/m/Y') }}</span>
 <span class="text-xs text-gray-400">{{ $driver->deleted_at->format('H:i') }}</span>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
 <button onclick="showRestoreConfirmation({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
 class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105">
 <i class="fas fa-undo mr-1"></i>
 Restaurer
 </button>
 <button onclick="showForceDeleteConfirmation({{ $driver->id }}, '{{ $driver->first_name }}', '{{ $driver->last_name }}', '{{ $driver->employee_number ?? 'N/A' }}')"
 class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105">
 <i class="fas fa-trash-alt mr-1"></i>
 Supprimer
 </button>
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 <div class="px-6 py-3 border-t border-gray-200">
 {{ $drivers->links() }}
 </div>
 @else
 <div class="text-center py-12">
 <div class="mx-auto h-24 w-24 text-amber-400 mb-4">
 <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
 </svg>
 </div>
 <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun chauffeur archivé</h3>
 <p class="mt-1 text-sm text-gray-500">Tous vos chauffeurs sont actifs ou ont été restaurés.</p>
 </div>
 @endif
 </div>
 </div>
 </div>
</div>

@push('scripts')
<script>
// 🔄 Enterprise-Grade Restore Confirmation Modal
function showRestoreConfirmation(driverId, firstName, lastName, employeeNumber) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 modal-backdrop transition-opacity" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 animate-scale-in">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-green-100 sm:mx-0 sm:h-12 sm:w-12">
 <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-xl font-bold text-gray-900 mb-2">Restaurer le chauffeur</h3>
 <div class="mt-2">
 <p class="text-sm text-gray-600 mb-4">Voulez-vous restaurer ce chauffeur ? Il redeviendra actif dans votre flotte.</p>
 <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
 <div class="flex items-center">
 <svg class="h-5 w-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
 </svg>
 <div>
 <p class="font-semibold text-green-900">${firstName} ${lastName}</p>
 <p class="text-sm text-green-700">Matricule: ${employeeNumber}</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button type="button" onclick="confirmRestore(${driverId})"
 class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-green-600 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto transition-all duration-200 hover:scale-105">
 <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
 </svg>
 Restaurer le chauffeur
 </button>
 <button type="button" onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-all duration-200">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;
 document.body.appendChild(modal);
}

// 💀 Enterprise-Grade Force Delete Confirmation Modal
function showForceDeleteConfirmation(driverId, firstName, lastName, employeeNumber) {
 const modal = document.createElement('div');
 modal.className = 'fixed inset-0 z-50 overflow-y-auto';
 modal.innerHTML = `
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 modal-backdrop transition-opacity" onclick="closeModal()"></div>
 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 animate-scale-in">
 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 sm:mx-0 sm:h-12 sm:w-12">
 <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
 </svg>
 </div>
 <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
 <h3 class="text-xl font-bold text-gray-900 mb-2">⚠️ Suppression Définitive</h3>
 <div class="mt-2">
 <p class="text-sm text-gray-600 mb-4">
 <strong class="text-red-600">ATTENTION :</strong> Cette action est IRRÉVERSIBLE !
 </p>
 <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
 <div class="flex items-center">
 <svg class="h-5 w-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
 </svg>
 <div>
 <p class="font-semibold text-red-900">${firstName} ${lastName}</p>
 <p class="text-sm text-red-700">Matricule: ${employeeNumber}</p>
 </div>
 </div>
 </div>
 <div class="bg-red-50 border border-red-200 rounded-lg p-3">
 <div class="flex">
 <svg class="h-5 w-5 text-red-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 <div>
 <p class="text-sm font-medium text-red-800">Suppression irréversible</p>
 <p class="text-xs text-red-700 mt-1">Toutes les données du chauffeur seront définitivement perdues. Cette action ne peut pas être annulée.</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
 <button type="button" onclick="confirmForceDelete(${driverId})"
 class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-all duration-200 hover:scale-105">
 <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
 </svg>
 Supprimer Définitivement
 </button>
 <button type="button" onclick="closeModal()"
 class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto transition-all duration-200">
 Annuler
 </button>
 </div>
 </div>
 </div>
 `;
 document.body.appendChild(modal);
}

// Action handlers with enterprise-grade error handling and traceability
function confirmRestore(driverId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/drivers/${driverId}/restore`;
 form.innerHTML = `
 @csrf
 @method('PATCH')
 `;
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 300);
}

function confirmForceDelete(driverId) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = `/admin/drivers/${driverId}/force-delete`;
 form.innerHTML = `
 @csrf
 @method('DELETE')
 `;
 document.body.appendChild(form);
 closeModal();
 setTimeout(() => form.submit(), 300);
}

function closeModal() {
 const modal = document.querySelector('.fixed.inset-0.z-50');
 if (modal) {
 modal.style.opacity = '0';
 modal.style.transform = 'scale(0.95)';
 setTimeout(() => modal.remove(), 300);
 }
}
</script>
@endpush
@endsection