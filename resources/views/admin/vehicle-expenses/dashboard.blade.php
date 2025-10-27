@extends('layouts.app')

@section('title', 'Analytics des Dépenses')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- En-tête avec stats --}}
    <div class="bg-gradient-to-br from-emerald-900/90 to-green-800/90 backdrop-blur-sm rounded-2xl p-8 mb-8 text-white shadow-2xl">
        <h1 class="text-4xl font-bold mb-4 flex items-center gap-3">
            <x-iconify icon="heroicons:chart-pie" class="h-10 w-10" />
            Analytics des Dépenses
        </h1>
        <p class="text-emerald-100/90">Analyse approfondie et insights intelligents de vos dépenses</p>
    </div>

    {{-- Composant Livewire Analytics --}}
    @livewire('admin.vehicle-expenses.expense-analytics')
</div>
@endsection

@push('styles')
<style>
    /* Animations personnalisées pour les graphiques */
    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .chart-animation {
        animation: slideInUp 0.6s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialisation des tooltips si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des composants interactifs
    });
</script>
@endpush
