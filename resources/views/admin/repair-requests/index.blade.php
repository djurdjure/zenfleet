@extends('layouts.admin.catalyst')

@section('title', 'Demandes de Réparation')

@section('content')
 @livewire('repair-requests-index')
@endsection

@push('scripts')
<script>
 // Notifications Toast
 window.addEventListener('notify', event => {
 const { type, message } = event.detail[0];
 
 // Vous pouvez utiliser votre système de notification préféré ici
 // Par exemple avec Alpine.js et un composant toast
 if (window.Alpine) {
 Alpine.store('notifications').add({
 type: type,
 message: message
 });
 }
 });

 // Export handler
 window.addEventListener('export-repair-requests', event => {
 const { format, filters } = event.detail[0];
 
 // Construction de l'URL avec les paramètres
 const params = new URLSearchParams({
 format: format,
 ...filters
 });
 
 window.location.href = `/admin/repair-requests/export?${params.toString()}`;
 });
</script>
@endpush

@push('styles')
<style>
 /* Animations personnalisées */
 [x-cloak] { display: none !important; }
 
 /* Transition douce pour les lignes du tableau */
 tbody tr {
 transition: all 0.2s ease;
 }
 
 /* Style pour les checkboxes */
 input[type="checkbox"] {
 transition: all 0.2s ease;
 }
 
 input[type="checkbox"]:checked {
 background-color: #3B82F6;
 border-color: #3B82F6;
 }
 
 /* Scrollbar personnalisée */
 .overflow-x-auto::-webkit-scrollbar {
 height: 8px;
 }
 
 .overflow-x-auto::-webkit-scrollbar-track {
 background: #f1f1f1;
 border-radius: 10px;
 }
 
 .overflow-x-auto::-webkit-scrollbar-thumb {
 background: #888;
 border-radius: 10px;
 }
 
 .overflow-x-auto::-webkit-scrollbar-thumb:hover {
 background: #555;
 }
 
 /* Dark mode scrollbar */
 .dark .overflow-x-auto::-webkit-scrollbar-track {
 background: #374151;
 }
 
 .dark .overflow-x-auto::-webkit-scrollbar-thumb {
 background: #6B7280;
 }
 
 .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
 background: #9CA3AF;
 }
</style>
@endpush
