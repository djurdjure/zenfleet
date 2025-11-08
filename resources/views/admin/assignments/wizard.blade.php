@extends('layouts.admin.catalyst')

@section('title', 'Nouveau - Affectation Véhicule')

@section('content')
{{-- ====================================================================
 🚀 ASSIGNMENT WIZARD - Page Unique Ultra-Professionnelle
 ====================================================================
 Interface révolutionnaire surpassant Fleetio et Samsara:
 - Page unique sans steps multiples
 - Filtrage intelligent (PARKING + DISPONIBLES uniquement)
 - Validation temps réel avec détection conflits
 - Changement automatique statuts
 - UX optimale Enterprise-Grade

 @version 2.0-Revolution
 ==================================================================== --}}

<div class="min-h-screen bg-gray-50">
    {{-- Header avec breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700">
                            <i class="fas fa-home"></i>
                        </a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <a href="{{ route('admin.assignments.index') }}" class="hover:text-gray-700">Affectations</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-gray-900 font-medium">Nouvelle Affectation</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-magic text-blue-600"></i>
                        Wizard d'Affectation
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Interface ultra-professionnelle pour affecter un véhicule à un chauffeur
                    </p>
                </div>

                <a href="{{ route('admin.assignments.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour à la liste</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Composant Livewire AssignmentWizard --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @livewire('admin.assignment-wizard')
    </div>
</div>
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
