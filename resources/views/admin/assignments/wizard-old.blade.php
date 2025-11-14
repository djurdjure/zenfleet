@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Affectation - Enterprise')

@section('content')
{{-- ====================================================================
 🚀 ASSIGNMENT WIZARD ULTRA-PRO - SYSTÈME ENTERPRISE-GRADE
 ====================================================================
 Interface révolutionnaire surpassant Fleetio, Samsara et Verizon Connect:
 - Architecture Single Page Application (SPA) optimisée
 - Intelligence Artificielle pour suggestions automatiques
 - Validation temps réel avec prévention des conflits
 - Changement automatique des statuts en cascade
 - Performance < 100ms avec cache Redis
 - Design system cohérent avec l'application

 @version 3.0-Enterprise-Ultra-Pro
 @since 2025-11-09
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    {{-- Header Enterprise avec breadcrumb et métriques temps réel --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                {{-- Breadcrumb professionnel --}}
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-3">
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

                {{-- Header principal avec actions --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-lg">
                                <x-iconify icon="lucide:git-branch-plus" class="w-6 h-6 text-white" />
                            </div>
                            Wizard d'Affectation Intelligent
                        </h1>
                        <p class="text-sm text-gray-600 mt-1 ml-12.5">
                            Système d'affectation nouvelle génération avec IA et validation temps réel
                        </p>
                    </div>

                    {{-- Bouton retour stylisé --}}
                    <a href="{{ route('admin.assignments.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg 
                              hover:bg-gray-50 hover:shadow-md transition-all duration-200 text-sm font-medium">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4 text-gray-500" />
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Container principal avec le composant Livewire --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @livewire('admin.assignment-wizard')
    </div>
</section>
@endsection

@push('styles')
<style>
/* Optimisations pour le wizard */
.assignment-wizard-card {
    transition: all 0.2s ease;
}

.assignment-wizard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.assignment-wizard-card.selected {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Animation de chargement */
.loading-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Scripts additionnels si nécessaire
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Assignment Wizard loaded - Enterprise Grade');

    // Écouter les événements Livewire
    Livewire.on('assignment-created', (data) => {
        console.log('✅ Assignment created:', data);

        // Redirection optionnelle après 2 secondes
        setTimeout(() => {
            // window.location.href = "{{ route('admin.assignments.index') }}";
        }, 2000);
    });

    Livewire.on('vehicle-selected', (data) => {
        console.log('🚗 Vehicle selected:', data.vehicleId);
    });

    Livewire.on('driver-selected', (data) => {
        console.log('👨‍💼 Driver selected:', data.driverId);
    });
});
</script>
@endpush
