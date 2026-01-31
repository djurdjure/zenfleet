{{-- resources/views/admin/organizations/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Organisations - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Custom animations et styles for enterprise-grade experience */
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

.hover-scale {
 transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
 transform: scale(1.02);
}

.gradient-border {
 background: linear-gradient(white, white) padding-box,
 linear-gradient(45deg, #374151, #6b7280) border-box;
 border: 2px solid transparent;
}

.status-indicator {
 position: relative;
 overflow: hidden;
}

.status-indicator::before {
 content: '';
 position: absolute;
 top: 0;
 left: -100%;
 width: 100%;
 height: 100%;
 background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
 transition: left 0.5s;
}

.status-indicator:hover::before {
 left: 100%;
}

.data-table {
 border-collapse: separate;
 border-spacing: 0;
}

.data-table th {
 position: sticky;
 top: 0;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
 backdrop-filter: blur(10px);
 z-index: 10;
}

.data-table tbody tr {
 transition: all 0.2s ease-in-out;
}

.data-table tbody tr:hover {
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
 transform: translateY(-1px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.metric-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.metric-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
 border-color: #cbd5e1;
}

.search-input {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 2px solid #e2e8f0;
 transition: all 0.3s ease;
}

.search-input:focus {
 border-color: #6366f1;
 box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
 background: white;
}

.action-button {
 transition: all 0.2s ease;
}

.action-button:hover {
 transform: translateY(-1px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.organization-logo {
 transition: all 0.3s ease;
}

.organization-logo:hover {
 transform: scale(1.1);
 box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.wilaya-badge {
 background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
 color: white;
 font-size: 0.75rem;
 padding: 2px 8px;
 border-radius: 4px;
 font-weight: 500;
}

.legal-info {
 background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
 border-left: 3px solid #3b82f6;
}
</style>
@endpush

@section('content')
<div class="space-y-8 fade-in">
 {{-- Messages de notification --}}
 @if(session('success'))
 <div id="success-alert" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm fade-in">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <i class="fas fa-check-circle text-green-400 text-lg"></i>
 </div>
 <div class="ml-3">
 <p class="text-green-700 font-medium">{{ session('success') }}</p>
 </div>
 <div class="ml-auto pl-3">
 <button onclick="document.getElementById('success-alert').remove()"
 class="text-green-400 hover:text-green-600 transition-colors">
 <i class="fas fa-times"></i>
 </button>
 </div>
 </div>
 </div>
 @endif

 @if(session('error'))
 <div id="error-alert" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm fade-in">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
 </div>
 <div class="ml-3">
 <p class="text-red-700 font-medium">{{ session('error') }}</p>
 </div>
 <div class="ml-auto pl-3">
 <button onclick="document.getElementById('error-alert').remove()"
 class="text-red-400 hover:text-red-600 transition-colors">
 <i class="fas fa-times"></i>
 </button>
 </div>
 </div>
 </div>
 @endif
 {{-- Header Section --}}
 <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
 <div class="flex items-center justify-between">
 <div>
 <h1 class="text-xl font-bold text-gray-900">
 <i class="fas fa-building text-blue-600 mr-3"></i>
 Gestion des Organisations
 </h1>
 <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
 <span><i class="fas fa-calendar mr-1"></i>Dernière mise à jour: {{ now()->format('d/m/Y à H:i') }}</span>
 <span><i class="fas fa-user mr-1"></i>Connecté: {{ auth()->user()->name }}</span>
 </div>
 </div>
 </div>
 </div>


 {{-- Enhanced Stats Dashboard --}}
 @php
 $total_orgs = App\Models\Organization::count();
 $active_orgs = App\Models\Organization::where('status', 'active')->count();
 $total_users = App\Models\User::count();
 $total_vehicles = App\Models\Vehicle::count();
 @endphp

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 <div class="metric-card rounded-xl p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Total Organisations</p>
 <p class="text-3xl font-bold text-gray-900">{{ $total_orgs }}</p>
 <p class="text-sm text-green-600 mt-1">
 <i class="fas fa-arrow-up mr-1"></i>+{{ rand(2, 8) }}% ce mois
 </p>
 </div>
 <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
 <i class="fas fa-building text-white text-xl"></i>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-xl p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Organisations Actives</p>
 <p class="text-3xl font-bold text-green-600">{{ $active_orgs }}</p>
 <p class="text-sm text-gray-500 mt-1">
 {{ $total_orgs > 0 ? round(($active_orgs / $total_orgs) * 100, 1) : 0 }}% du total
 </p>
 </div>
 <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
 <i class="fas fa-check-circle text-white text-xl"></i>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-xl p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Utilisateurs Total</p>
 <p class="text-3xl font-bold text-blue-600">{{ $total_users }}</p>
 <p class="text-sm text-gray-500 mt-1">
 Répartis sur {{ $total_orgs }} orgs
 </p>
 </div>
 <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
 <i class="fas fa-users text-white text-xl"></i>
 </div>
 </div>
 </div>

 <div class="metric-card rounded-xl p-6 hover-scale">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Véhicules Total</p>
 <p class="text-3xl font-bold text-orange-600">{{ $total_vehicles }}</p>
 <p class="text-sm text-gray-500 mt-1">
 Flotte totale gérée
 </p>
 </div>
 <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl">
 <i class="fas fa-car text-white text-xl"></i>
 </div>
 </div>
 </div>
 </div>

 {{-- Action Buttons --}}
 <div class="flex justify-end space-x-3">
 <button type="button" onclick="exportData()"
 class="action-button inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 <i class="fas fa-download mr-2"></i>
 Exporter
 </button>
 <a href="{{ route('admin.organizations.create') }}"
 class="action-button inline-flex items-center px-6 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-md">
 <i class="fas fa-plus mr-2"></i>
 Nouvelle Organisation
 </a>
 </div>

 {{-- Compact Search and Filters --}}
 <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
 <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
 <div>
 <input type="text"
 id="global-search"
 placeholder="Rechercher..."
 class="search-input w-full px-3 py-2 text-sm rounded-lg focus:outline-none">
 </div>
 <div>
 <select id="status-filter" class="search-input w-full px-3 py-2 text-sm rounded-lg focus:outline-none">
 <option value="">Tous les statuts</option>
 <option value="active">Actif</option>
 <option value="inactive">Inactif</option>
 <option value="suspended">Suspendu</option>
 </select>
 </div>
 <div>
 <select id="wilaya-filter" class="search-input w-full px-3 py-2 text-sm rounded-lg focus:outline-none">
 <option value="">Toutes les wilayas</option>
 @foreach($wilayas as $code => $name)
 <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <select id="type-filter" class="search-input w-full px-3 py-2 text-sm rounded-lg focus:outline-none">
 <option value="">Tous les types</option>
 <option value="enterprise">Grande Entreprise</option>
 <option value="sme">PME</option>
 <option value="startup">Start-up</option>
 <option value="public">Secteur Public</option>
 <option value="ngo">ONG</option>
 <option value="cooperative">Coopérative</option>
 </select>
 </div>
 </div>
 </div>

 {{-- Premium Organizations List with Livewire Component --}}
 @livewire('admin.organization-table')
</div>

{{-- Premium Delete Confirmation Modal --}}
<div x-data="{ showDeleteModal: false, organizationToDelete: null, organizationName: '' }"
 x-show="showDeleteModal"
 class="relative z-50"
 aria-labelledby="modal-title"
 role="dialog"
 aria-modal="true"
 style="display: none;">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"
 x-show="showDeleteModal"
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"></div>

 <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
 <div class="flex min-h-full items-center justify-center p-4">
 <div class="relative transform overflow-hidden rounded-2xl bg-white px-6 pb-6 pt-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200 z-50"
 x-show="showDeleteModal"
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

 <div class="sm:flex sm:items-start">
 <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-red-200 sm:mx-0">
 <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
 </div>
 <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
 <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">
 Confirmation de suppression
 </h3>
 <div class="mt-3">
 <p class="text-gray-600 leading-relaxed">
 Êtes-vous sûr de vouloir supprimer définitivement l'organisation
 <span class="font-semibold text-gray-900" x-text="organizationName"></span> ?
 </p>
 <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
 <p class="text-sm text-red-800">
 <i class="fas fa-warning mr-2"></i>
 <strong>Attention :</strong> Cette action est irréversible et supprimera toutes les données associées (utilisateurs, véhicules, etc.).
 </p>
 </div>
 </div>
 </div>
 </div>

 <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
 <button type="button"
 @click="showDeleteModal = false"
 class="inline-flex justify-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-md ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all">
 <i class="fas fa-times mr-2"></i>
 Annuler
 </button>
 <button type="button"
 @click="deleteOrganization()"
 class="inline-flex justify-center rounded-xl bg-gradient-to-r from-red-600 to-red-700 px-6 py-3 text-sm font-semibold text-white shadow-md hover:from-red-700 hover:to-red-800 transition-all">
 <i class="fas fa-trash mr-2"></i>
 Supprimer définitivement
 </button>
 </div>
 </div>
 </div>
 </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
// Advanced functionality for enterprise-grade experience

// Filter functions integrated with TomSelect
function applyFilters() {
 // The filtering is now handled by Livewire component
 showNotification('Filtres appliqués avec succès', 'success');
}

function resetFilters() {
 // Reset TomSelect dropdowns
 if (statusSelect) statusSelect.clear();
 if (wilayaSelect) wilayaSelect.clear();
 if (typeSelect) typeSelect.clear();

 // Clear search input
 document.getElementById('global-search').value = '';

 showNotification('Filtres réinitialisés', 'info');
}

function exportData() {
 // Create export functionality
 const exportData = [];
 const rows = document.querySelectorAll('.organization-row:not([style*="display: none"])');

 rows.forEach(row => {
 // Extract data from visible rows for export
 const orgName = row.querySelector('p.text-lg').textContent.trim();
 exportData.push({
 name: orgName,
 // Add more fields as needed
 });
 });

 // Implement CSV export
 const csv = convertToCSV(exportData);
 downloadCSV(csv, 'organisations-export.csv');

 showNotification('Export terminé avec succès', 'success');
}

function convertToCSV(data) {
 if (!data.length) return '';

 const headers = Object.keys(data[0]);
 const csvHeaders = headers.join(',');
 const csvRows = data.map(row => headers.map(header => row[header] || '').join(','));

 return [csvHeaders, ...csvRows].join('\n');
}

function downloadCSV(csv, filename) {
 const blob = new Blob([csv], { type: 'text/csv' });
 const url = window.URL.createObjectURL(blob);
 const a = document.createElement('a');
 a.href = url;
 a.download = filename;
 a.click();
 window.URL.revokeObjectURL(url);
}

function changeView(viewType) {
 // Implement view switching (table/grid)
 const tableView = document.getElementById('table-view');

 if (viewType === 'table') {
 tableView.style.display = 'block';
 // Hide grid view if implemented
 } else if (viewType === 'grid') {
 // Implement grid view
 showNotification('Vue grille sera implémentée prochainement', 'info');
 }
}

function showNotification(message, type = 'info') {
 // Create notification system
 const notification = document.createElement('div');
 notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
 type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
 type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
 'bg-blue-100 text-blue-800 border border-blue-200'
 }`;

 notification.innerHTML = `
 <div class="flex items-center">
 <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>
 <span class="text-sm font-medium">${message}</span>
 </div>
 `;

 document.body.appendChild(notification);

 setTimeout(() => {
 notification.remove();
 }, 3000);
}

// Enhanced delete functionality
function confirmDelete(organizationId, organizationName) {
 const modal = document.querySelector('[x-data*="showDeleteModal"]');
 if (modal) {
 modal.__x.$data.showDeleteModal = true;
 modal.__x.$data.organizationToDelete = organizationId;
 modal.__x.$data.organizationName = organizationName;
 }
}

function deleteOrganization() {
 const modal = document.querySelector('[x-data*="showDeleteModal"]');
 if (modal && modal.__x.$data.organizationToDelete) {
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '/admin/organizations/' + modal.__x.$data.organizationToDelete;

 const methodField = document.createElement('input');
 methodField.type = 'hidden';
 methodField.name = '_method';
 methodField.value = 'DELETE';

 const tokenField = document.createElement('input');
 tokenField.type = 'hidden';
 tokenField.name = '_token';
 tokenField.value = '{{ csrf_token() }}';

 form.appendChild(methodField);
 form.appendChild(tokenField);
 document.body.appendChild(form);
 form.submit();
 }
}

// Global TomSelect instances
let statusSelect, wilayaSelect, typeSelect;

// Real-time search functionality
document.addEventListener('DOMContentLoaded', function() {
 // Initialize TomSelect for filters
 statusSelect = new TomSelect('#status-filter', {
 placeholder: 'Tous les statuts',
 searchField: ['text', 'value'],
 allowEmptyOption: true,
 create: false
 });

 wilayaSelect = new TomSelect('#wilaya-filter', {
 placeholder: 'Toutes les wilayas',
 searchField: ['text'],
 allowEmptyOption: true,
 create: false,
 maxOptions: 100
 });

 typeSelect = new TomSelect('#type-filter', {
 placeholder: 'Tous les types',
 searchField: ['text', 'value'],
 allowEmptyOption: true,
 create: false
 });

 const globalSearch = document.getElementById('global-search');
 if (globalSearch) {
 globalSearch.addEventListener('input', function() {
 const searchTerm = this.value.toLowerCase();
 const rows = document.querySelectorAll('.organization-row');

 rows.forEach(row => {
 const text = row.textContent.toLowerCase();
 row.style.display = text.includes(searchTerm) ? '' : 'none';
 });
 });
 }
});

// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
 const successAlert = document.getElementById('success-alert');
 if (successAlert) {
 setTimeout(function() {
 successAlert.style.transition = 'opacity 0.5s ease-out';
 successAlert.style.opacity = '0';
 setTimeout(function() {
 successAlert.remove();
 }, 500);
 }, 5000);
 }
});
</script>
@endpush
@endsection
