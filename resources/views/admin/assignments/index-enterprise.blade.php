{{-- resources/views/admin/assignments/index-enterprise.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Gestion des Affectations - ZenFleet')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
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
 box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tab-button {
 position: relative;
 padding: 0.75rem 1.5rem;
 font-weight: 500;
 font-size: 0.875rem;
 border: none;
 background: none;
 cursor: pointer;
 transition: all 0.3s ease;
}

.tab-button.active {
 color: #6366f1;
 border-bottom: 2px solid #6366f1;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.tab-button:not(.active):hover {
 color: #4b5563;
 background: #f9fafb;
}

.tom-select .ts-control {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 2px solid #e2e8f0;
 border-radius: 8px;
 transition: all 0.3s ease;
}

.tom-select.focus .ts-control {
 border-color: #6366f1;
 box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
</style>
@endpush

@section('content')
<div class="fade-in" x-data="assignmentsPage()" x-init="init()">
 {{-- En-tête ultra-professionnel --}}
 <div class="mb-8">
 <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
 {{-- En-tête avec titre --}}
 <div class="flex items-center gap-4 mb-6">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-exchange-alt text-white text-xl"></i>
 </div>
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Affectations Véhicule ↔ Chauffeur</h1>
 <p class="text-gray-600 mt-1">Gestion des affectations ultra-moderne et professionnelle</p>
 </div>
 </div>

 {{-- Barre d'actions sur une seule ligne --}}
 <div class="flex items-center gap-3 flex-wrap">
 {{-- Champ de recherche réduit --}}
 <div class="flex-none w-64">
 <div class="relative">
 <input type="text" 
 placeholder="Rechercher..." 
 class="search-input w-full pl-10 pr-4 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
 x-model="searchQuery"
 @input="performSearch()">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <i class="fas fa-search text-gray-400"></i>
 </div>
 </div>
 </div>

 {{-- Boutons d'action groupés --}}
 <div class="flex-1 flex items-center justify-end gap-2">
 <button type="button" class="action-button inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
 <i class="fas fa-filter -ml-0.5 mr-2 h-4 w-4"></i>
 Filtres
 </button>
 
 <a href="{{ route('admin.assignments.create') }}" class="action-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
 <i class="fas fa-plus -ml-1 mr-2 h-4 w-4"></i>
 Nouvelle affectation
 </a>

 {{-- Menu options supplémentaires --}}
 <div class="relative"
 x-data="{
 open: false,
 styles: '',
 direction: 'down',
 align: 'right',
 toggle() {
 if (this.open) { this.close(); return; }
 this.open = true;
 this.$nextTick(() => {
 this.updatePosition();
 requestAnimationFrame(() => this.updatePosition());
 });
 },
 close() { this.open = false; },
 updatePosition() {
 if (!this.$refs.trigger || !this.$refs.menu) return;
 const rect = this.$refs.trigger.getBoundingClientRect();
 const menuHeight = this.$refs.menu.offsetHeight || 200;
 const menuWidth = this.$refs.menu.offsetWidth || 192;
 const padding = 12;
 const spaceBelow = window.innerHeight - rect.bottom;
 const spaceAbove = rect.top;
 const shouldOpenUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
 this.direction = shouldOpenUp ? 'up' : 'down';
 let top = shouldOpenUp ? (rect.top - menuHeight - 8) : (rect.bottom + 8);
 if (top + menuHeight > window.innerHeight - padding) {
 top = window.innerHeight - padding - menuHeight;
 }
 if (top < padding) top = padding;
 let left = this.align === 'right' ? (rect.right - menuWidth) : rect.left;
 if (left + menuWidth > window.innerWidth - padding) {
 left = window.innerWidth - padding - menuWidth;
 }
 if (left < padding) left = padding;
 this.styles = `position: fixed; top: ${top}px; left: ${left}px; width: ${menuWidth}px; z-index: 9999;`;
 }
 }"
 x-init="$watch('open', value => {
 if (value) {
 $nextTick(() => {
 this.updatePosition();
 requestAnimationFrame(() => this.updatePosition());
 });
 }
 })"
 @keydown.escape.window="close()"
 @scroll.window="open && updatePosition()"
 @resize.window="open && updatePosition()">
 <button @click="toggle()" x-ref="trigger" type="button" class="action-button inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
 <i class="fas fa-ellipsis-v h-4 w-4"></i>
 </button>
 
 <template x-teleport="body">
 <div x-show="open" 
 x-ref="menu"
 @click.outside="close()"
 x-transition:enter="transition ease-out duration-100"
 x-transition:enter-start="transform opacity-0 scale-95"
 x-transition:enter-end="transform opacity-100 scale-100"
 :style="styles"
 class="rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-[9999]"
 x-cloak>
 <div class="py-1">
 <a href="{{ route('admin.assignments.gantt') }}" @click="close()" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
 <i class="fas fa-chart-gantt mr-3 text-amber-500"></i>
 Vue Gantt
 </a>
 <button type="button" @click="close()" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
 <i class="fas fa-download mr-3 text-gray-500"></i>
 Exporter
 </button>
 </div>
 </div>
 </template>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Statistiques ultra-professionnelles --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-play-circle text-white text-lg"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Affectations actives</p>
 <p class="text-2xl font-bold text-gray-900" x-text="stats.active">-</p>
 </div>
 </div>
 </div>

 <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-calendar-alt text-white text-lg"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Programmées</p>
 <p class="text-2xl font-bold text-green-600" x-text="stats.scheduled">-</p>
 </div>
 </div>
 </div>

 <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-check-circle text-white text-lg"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Terminées (ce mois)</p>
 <p class="text-2xl font-bold text-gray-600" x-text="stats.completed">-</p>
 </div>
 </div>
 </div>

 <div class="metric-card hover-scale rounded-2xl p-6 border border-gray-100 shadow-sm">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-tachometer-alt text-white text-lg"></i>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Taux d'utilisation</p>
 <p class="text-2xl font-bold text-orange-600" x-text="stats.utilization + '%'">-</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Interface principale avec onglets --}}
 <div class="bg-white shadow-sm overflow-hidden rounded-2xl border border-gray-100">
 {{-- Onglets de navigation --}}
 <div class="border-b border-gray-200">
 <nav class="-mb-px flex space-x-8 px-8" aria-label="Tabs">
 <button @click="currentView = 'table'"
 :class="currentView === 'table' ? 'tab-button active' : 'tab-button'"
 class="tab-button">
 <i class="fas fa-table mr-2 text-sm"></i>
 Vue Table
 </button>
 <button @click="currentView = 'gantt'"
 :class="currentView === 'gantt' ? 'tab-button active' : 'tab-button'"
 class="tab-button">
 <i class="fas fa-chart-gantt mr-2 text-sm"></i>
 Vue Gantt
 </button>
 </nav>
 </div>

 {{-- En-tête avec titre --}}
 <div class="px-8 py-6 border-b border-gray-200">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-list text-white"></i>
 </div>
 <h3 class="text-xl font-bold text-gray-900" x-text="currentView === 'table' ? 'Liste des affectations' : 'Planning Gantt des affectations'">
 Liste des affectations
 </h3>
 </div>
 </div>

 {{-- Contenu des vues --}}
 <div class="min-h-96">
 {{-- Vue Table avec composant Livewire --}}
 <div x-show="currentView === 'table'"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100">
 <livewire:assignment-table />
 </div>

 {{-- Vue Gantt avec composant Livewire --}}
 <div x-show="currentView === 'gantt'"
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100">
 <livewire:assignment-gantt />
 </div>

 {{-- État vide élégant --}}
 <template x-if="!hasAssignments && !isLoading">
 <div class="text-center py-12 px-8">
 <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
 <i class="fas fa-clipboard-list h-8 w-8 text-gray-400"></i>
 </div>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune affectation</h3>
 <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
 Commencez par créer votre première affectation véhicule ↔ chauffeur pour optimiser la gestion de votre flotte.
 </p>
 <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
 <button @click="openCreateModal()"
 class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:scale-105 shadow-lg">
 <i class="fas fa-plus mr-2"></i>
 Créer ma première affectation
 </button>
 <div class="flex items-center space-x-4 text-sm text-gray-500">
 <span class="flex items-center">
 <i class="fas fa-car mr-1 text-blue-500"></i>
 <span x-text="availableVehicles.length">0</span> véhicules
 </span>
 <span class="flex items-center">
 <i class="fas fa-user-tie mr-1 text-green-500"></i>
 <span x-text="availableDrivers.length">0</span> chauffeurs
 </span>
 </div>
 </div>
 </div>
 </template>

 {{-- Indicateur de chargement --}}
 <div x-show="isLoading" class="text-center py-12">
 <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
 <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Chargement des données...
 </div>
 </div>
 </div>
 </div>

 {{-- Modal de création d'affectation --}}
 <div x-show="showCreateModal"
 x-transition:enter="ease-out duration-300"
 x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 x-transition:leave="ease-in duration-200"
 x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0"
 class="fixed inset-0 z-50 overflow-y-auto"
 style="display: none;">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" @click="closeCreateModal()"></div>

 <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

 <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6 animate-scale-in relative z-50"
 @click.away="closeCreateModal()">
 <div class="flex items-start justify-between mb-4">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
 <i class="fas fa-plus text-white"></i>
 </div>
 <h3 class="text-xl font-bold text-gray-900">Nouvelle Affectation Enterprise</h3>
 </div>
 <button @click="closeCreateModal()"
 class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 p-1 transition-colors">
 <span class="sr-only">Fermer</span>
 <i class="fas fa-times h-5 w-5"></i>
 </button>
 </div>

 <div class="mt-4">
 <livewire:assignment-form />
 </div>
 </div>
 </div>
 </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
// Fonction Alpine.js pour la page d'affectations
function assignmentsPage() {
 return {
 currentView: 'table',
 showCreateModal: false,
 hasAssignments: true, // Sera mis à jour par l'API
 isLoading: true,
 availableVehicles: [],
 availableDrivers: [],
 stats: {
 active: 0,
 scheduled: 0,
 completed: 0,
 utilization: 0
 },

 async init() {
 console.log('🚀 Initialisation de la page d\'affectations');
 await this.loadStats();
 await this.loadAvailableResources();
 this.isLoading = false;
 console.log('✅ Page d\'affectations initialisée avec succès');
 },

 async loadStats() {
 try {
 const response = await fetch('/admin/assignments/stats', {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
 }
 });

 if (response.ok) {
 const data = await response.json();
 this.stats = {
 active: data.active_assignments || 0,
 scheduled: data.scheduled_assignments || 0,
 completed: data.completed_assignments || 0,
 utilization: Math.round(data.average_utilization || 0)
 };

 this.hasAssignments = this.stats.active + this.stats.scheduled + this.stats.completed > 0;
 console.log('📊 Statistiques chargées:', this.stats);
 } else {
 console.warn('⚠️ Erreur lors du chargement des statistiques:', response.status);
 }
 } catch (error) {
 console.warn('⚠️ Erreur lors du chargement des statistiques:', error);
 if (window.zenfleetAdmin) {
 window.zenfleetAdmin.notify('Impossible de charger les statistiques', 'warning');
 }
 }
 },

 async loadAvailableResources() {
 try {
 // Chargement des véhicules disponibles
 const vehiclesResponse = await fetch('/admin/vehicles/available', {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
 }
 });

 if (vehiclesResponse.ok) {
 this.availableVehicles = await vehiclesResponse.json();
 }

 // Chargement des chauffeurs disponibles
 const driversResponse = await fetch('/admin/drivers/available', {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 'Accept': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
 }
 });

 if (driversResponse.ok) {
 this.availableDrivers = await driversResponse.json();
 }

 console.log(`🚗 ${this.availableVehicles.length} véhicules disponibles, 👤 ${this.availableDrivers.length} chauffeurs disponibles`);
 } catch (error) {
 console.warn('⚠️ Erreur lors du chargement des ressources:', error);
 }
 },

 openCreateModal() {
 this.showCreateModal = true;
 document.body.style.overflow = 'hidden';
 console.log('📝 Modal de création ouverte');
 },

 closeCreateModal() {
 this.showCreateModal = false;
 document.body.style.overflow = '';
 console.log('✖️ Modal de création fermée');
 }
 }
}

// Écouter les événements Livewire
document.addEventListener('DOMContentLoaded', function() {
 console.log('🎉 Page d\'affectations chargée avec succès !');

 // Animation des cartes statistiques
 const cards = document.querySelectorAll('.metric-card');
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

// Écouter les événements Livewire
document.addEventListener('livewire:init', () => {
 Livewire.on('assignment-created', (event) => {
 console.log('✅ Affectation créée');
 // Recharger les statistiques
 const assignmentPage = Alpine.$data(document.querySelector('[x-data*="assignmentsPage"]'));
 if (assignmentPage) {
 assignmentPage.loadStats();
 assignmentPage.closeCreateModal();
 }

 // Afficher notification
 if (window.zenfleetAdmin) {
 window.zenfleetAdmin.notify('Affectation créée avec succès', 'success');
 }
 });

 Livewire.on('assignment-updated', (event) => {
 console.log('📝 Affectation mise à jour');
 const assignmentPage = Alpine.$data(document.querySelector('[x-data*="assignmentsPage"]'));
 if (assignmentPage) {
 assignmentPage.loadStats();
 }

 if (window.zenfleetAdmin) {
 window.zenfleetAdmin.notify('Affectation mise à jour', 'success');
 }
 });

 Livewire.on('assignment-terminated', (event) => {
 console.log('🏁 Affectation terminée');
 const assignmentPage = Alpine.$data(document.querySelector('[x-data*="assignmentsPage"]'));
 if (assignmentPage) {
 assignmentPage.loadStats();
 }

 if (window.zenfleetAdmin) {
 window.zenfleetAdmin.notify('Affectation terminée', 'success');
 }
 });
});
</script>
@endpush
