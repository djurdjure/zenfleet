@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Affectation')

@section('content')
{{-- ====================================================================
🎯 PAGE CRÉATION AFFECTATION V2 - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Design surpassant Fleetio, Samsara et Verizon Connect:
✨ Design épuré inspiré de la page show
✨ SlimSelect pour sélecteurs professionnels
✨ Kilométrage initial auto-chargé
✨ Toasts optimisés sans texte inutile
✨ Validation temps réel avec feedback visuel
✨ Layout responsive et moderne

@version 2.0-Enterprise-Grade
@since 2025-11-14
==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    {{-- ===============================================
    HEADER AVEC BREADCRUMB
    =============================================== --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                {{-- Breadcrumb --}}
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-3" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">
                        <x-iconify icon="lucide:home" class="w-4 h-4" />
                    </a>
                    <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                    <a href="{{ route('admin.assignments.index') }}" class="hover:text-gray-700 transition-colors">
                        Affectations
                    </a>
                    <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                    <span class="text-gray-900 font-medium">Nouvelle Affectation</span>
                </nav>

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                                <x-iconify icon="lucide:clipboard-check" class="w-6 h-6 text-white" />
                            </div>
                            Nouvelle Affectation
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 ml-12.5">
                            Créez une nouvelle affectation véhicule ↔ chauffeur avec validation temps réel
                        </p>
                    </div>

                    {{-- Bouton retour --}}
                    <a href="{{ route('admin.assignments.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:shadow-md transition-all duration-200 text-sm font-medium">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4 text-gray-500" />
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===============================================
    COMPOSANT LIVEWIRE FORMULAIRE
    =============================================== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @livewire('assignment-form')
    </div>
</section>
@endsection

@push('styles')
<style>
/* Styles optimisés pour la page de création */
.assignment-form-card {
    transition: all 0.2s ease;
}

/* Animation de loading */
.loading-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .max-w-7xl {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Listener pour les événements Livewire
document.addEventListener('livewire:init', () => {
    // Redirection après création réussie
    Livewire.on('assignment-created', (event) => {
        setTimeout(() => {
            window.location.href = '{{ route("admin.assignments.index") }}';
        }, 2000);
    });

    // Fermeture du formulaire (si modal)
    Livewire.on('close-form', () => {
        window.location.href = '{{ route("admin.assignments.index") }}';
    });
});
</script>
@endpush
