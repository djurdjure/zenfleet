@extends('layouts.admin.catalyst')
@section('title', $title ?? 'Planning Gantt des Affectations')
@section('content')

{{-- 📈 Vue Gantt des Affectations - Interface Admin Enterprise --}}
 <div class="flex justify-between items-center">
 <div>
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ $title ?? 'Planning Gantt des Affectations' }}
 </h2>
 <p class="text-gray-600 text-sm mt-1">
 Visualisation temporelle des affectations véhicule ↔ chauffeur
 </p>
 </div>

 <div class="flex items-center space-x-3">
 {{-- Lien retour vers la vue table --}}
 <a href="{{ route('admin.assignments.index') }}"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"></path>
 </svg>
 Vue Table
 </a>

 {{-- Bouton nouvelle affectation --}}
 @can('assignments.create')
 <a href="{{ route('admin.assignments.create') }}"
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
 </svg>
 Nouvelle Affectation
 </a>
 @endcan
 </div>
 </div>

 {{-- Breadcrumbs --}}
 @if(isset($breadcrumbs))
 <div class="mb-6">
 <nav class="flex" aria-label="Breadcrumb">
 <ol class="inline-flex items-center space-x-1 md:space-x-3">
 @foreach($breadcrumbs as $name => $url)
 <li class="inline-flex items-center">
 @if($url)
 <a href="{{ $url }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
 {{ $name }}
 </a>
 <svg class="w-6 h-6 text-gray-400 ml-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
 </svg>
 @else
 <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $name }}</span>
 @endif
 </li>
 @endforeach
 </ol>
 </nav>
 </div>
 @endif

 {{-- Instructions et aide --}}
 <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <p class="text-sm text-blue-700">
 <strong>Guide d'utilisation:</strong>
 • Utilisez les contrôles temporels pour naviguer dans le planning
 • Cliquez sur une cellule libre pour créer rapidement une affectation
 • Survolez les barres d'affectation pour voir les détails
 • Basculez entre vue par véhicule et vue par chauffeur selon vos besoins
 </p>
 </div>
 </div>
 </div>

 {{-- Composant Gantt Livewire --}}
 <div class="space-y-6">
 @livewire('assignments.assignment-gantt')
 </div>

 {{-- Modal pour formulaire d'affectation (intégration avec le Gantt) --}}
 <div id="assignment-form-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
 <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40" onclick="closeAssignmentModal()"></div>

 <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 relative z-50">
 <div class="absolute top-0 right-0 pt-4 pr-4">
 <button type="button" onclick="closeAssignmentModal()" class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
 <span class="sr-only">Fermer</span>
 <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
 </svg>
 </button>
 </div>

 <div id="assignment-form-content">
 {{-- Le contenu du formulaire sera chargé dynamiquement --}}
 </div>
 </div>
 </div>
 </div>

 {{-- Scripts pour intégration Gantt --}}
 @push('scripts')
 <script>
 // Gestion globale des événements Gantt
 document.addEventListener('DOMContentLoaded', function() {
 // Écouter les événements Livewire pour ouverture de formulaire
 window.addEventListener('open-assignment-form', event => {
 openAssignmentModal(event.detail);
 });

 window.addEventListener('assignment-saved', event => {
 closeAssignmentModal();
 // Afficher notification de succès
 showNotification(event.detail.message || 'Affectation sauvegardée avec succès', 'success');
 });

 window.addEventListener('assignment-form-cancelled', event => {
 closeAssignmentModal();
 });
 });

 function openAssignmentModal(params = {}) {
 const modal = document.getElementById('assignment-form-modal');
 const content = document.getElementById('assignment-form-content');

 // Construire l'URL avec les paramètres
 let url = '{{ route("admin.assignments.create") }}';
 if (params && Object.keys(params).length > 0) {
 const urlParams = new URLSearchParams(params);
 url += '?' + urlParams.toString();
 }

 // Charger le formulaire via AJAX
 fetch(url, {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 'Accept': 'text/html'
 }
 })
 .then(response => response.text())
 .then(html => {
 content.innerHTML = html;
 modal.classList.remove('hidden');
 document.body.style.overflow = 'hidden';
 })
 .catch(error => {
 console.error('Erreur lors du chargement du formulaire:', error);
 showNotification('Erreur lors du chargement du formulaire', 'error');
 });
 }

 function closeAssignmentModal() {
 const modal = document.getElementById('assignment-form-modal');
 const content = document.getElementById('assignment-form-content');

 modal.classList.add('hidden');
 content.innerHTML = '';
 document.body.style.overflow = 'auto';
 }

 function showNotification(message, type = 'info') {
 // Implémentation simple de notification
 const colors = {
 success: 'bg-green-500',
 error: 'bg-red-500',
 warning: 'bg-yellow-500',
 info: 'bg-blue-500'
 };

 const notification = document.createElement('div');
 notification.className = `fixed top-4 right-4 ${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
 notification.textContent = message;

 document.body.appendChild(notification);

 // Animation d'entrée
 setTimeout(() => {
 notification.classList.remove('translate-x-full');
 }, 10);

 // Suppression automatique
 setTimeout(() => {
 notification.classList.add('translate-x-full');
 setTimeout(() => {
 document.body.removeChild(notification);
 }, 300);
 }, 5000);
 }

 // Gestion des raccourcis clavier
 document.addEventListener('keydown', function(e) {
 // Échap pour fermer la modal
 if (e.key === 'Escape') {
 closeAssignmentModal();
 }

 // Ctrl+N pour nouvelle affectation
 if (e.ctrlKey && e.key === 'n') {
 e.preventDefault();
 openAssignmentModal();
 }
 });
 </script>
 @endpush

 {{-- Styles CSS spécifiques à la vue Gantt --}}
 @push('styles')
 <style>
 /* Animation pour les barres Gantt */
 .gantt-assignment {
 transition: all 0.2s ease-in-out;
 }

 .gantt-assignment:hover {
 z-index: 20;
 transform: translateY(-2px);
 box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
 }

 /* Indicateurs de statut animés */
 @keyframes pulse-slow {
 0%, 100% {
 opacity: 1;
 }
 50% {
 opacity: 0.5;
 }
 }

 .status-indicator {
 animation: pulse-slow 2s infinite;
 }

 /* Responsive pour petits écrans */
 @media (max-width: 768px) {
 .gantt-container {
 font-size: 12px;
 }

 .gantt-resource-cell {
 min-width: 150px;
 }
 }

 /* Mode impression */
 @media print {
 .no-print {
 display: none !important;
 }

 .gantt-container {
 break-inside: avoid;
 }
 }
 </style>
 @endpush
@endsection
