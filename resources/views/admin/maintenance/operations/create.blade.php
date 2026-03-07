@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Opération de Maintenance')

@section('content')
{{-- ====================================================================
🔧 PAGE CRÉATION OPÉRATION DE MAINTENANCE - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Design surpassant Fleetio, Samsara et Verizon Connect:
✨ Design épuré inspiré de la page assignments/wizard
✨ SlimSelect pour sélecteurs professionnels
✨ Flatpickr pour dates intuitives
✨ Auto-complétion intelligente depuis maintenance_types
✨ Kilométrage auto-chargé depuis véhicule
✨ Validation temps réel avec feedback visuel
✨ Layout responsive et moderne
✨ Architecture Livewire enterprise-grade

@version 1.0-Enterprise-Grade
@since 2025-11-23
@author ZenFleet Architecture Team - Expert Système Senior
==================================================================== --}}

<section class="min-h-screen bg-[#f8fafc]">
    {{-- ===============================================
    HEADER AVEC BREADCRUMB
    =============================================== --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                {{-- Breadcrumb --}}
                <nav class="flex items-center space-x-2 text-xs text-gray-500 mb-3" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700 transition-colors">
                        <x-iconify icon="lucide:home" class="w-4 h-4" />
                    </a>
                    <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                    <a href="{{ route('admin.maintenance.operations.index') }}" class="hover:text-gray-700 transition-colors">
                        Opérations de Maintenance
                    </a>
                    <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                    <span class="text-gray-900 font-medium">Nouvelle Opération</span>
                </nav>

                {{-- Header --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-600">
                            Nouvelle Opération de Maintenance
                        </h1>
                        <p class="text-xs text-gray-600 mt-1">
                            Créez une nouvelle opération de maintenance avec validation temps réel
                        </p>
                    </div>

                    {{-- Bouton retour --}}
                    <a href="{{ route('admin.maintenance.operations.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-[#0c90ee] hover:border-[#0c90ee]/40 hover:shadow-md transition-all duration-200 text-sm font-medium text-gray-600">
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
        @livewire('maintenance.maintenance-operation-create')
    </div>
</section>
@endsection

@push('styles')
<style>
/* Styles optimisés pour la page de création */
.maintenance-operation-form-card {
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

/* SlimSelect custom styling */
.ss-main .ss-single-selected {
    border-radius: 0.5rem;
    border-color: #D1D5DB;
    transition: all 0.2s;
}

.ss-main .ss-single-selected:hover {
    border-color: #3B82F6;
}

.ss-main .ss-single-selected.ss-open-below,
.ss-main .ss-single-selected.ss-open-above {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Flatpickr custom styling */
.flatpickr-input {
    border-radius: 0.5rem;
}

.flatpickr-calendar {
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
</style>
@endpush

@push('scripts')
<script>
// Listener pour les événements Livewire
document.addEventListener('livewire:init', () => {
    console.log('[MaintenanceOperationCreate] Livewire initialisé');

    // Redirection après création réussie
    Livewire.on('operation-created', (event) => {
        console.log('[MaintenanceOperationCreate] Opération créée avec succès', event);

        // Toast de succès
        if (window.showToast) {
            window.showToast('Opération créée avec succès !', 'success');
        }

        // Redirection après 2 secondes
        setTimeout(() => {
            window.location.href = '{{ route("admin.maintenance.operations.index") }}';
        }, 2000);
    });

    // Fermeture du formulaire (si modal)
    Livewire.on('close-form', () => {
        window.location.href = '{{ route("admin.maintenance.operations.index") }}';
    });
});

// Fonction toast globale (si non définie)
if (typeof window.showToast === 'undefined') {
    window.showToast = function(message, type = 'info') {
        const bgColors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
        toast.textContent = message;
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);

        // Animate out and remove
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    };
}
</script>
@endpush
