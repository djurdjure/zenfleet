{{--
    Vue principale pour la gestion des sanctions des chauffeurs
    
    Cette vue sert de pont entre le layout principal de l'application et le composant Livewire.
    Elle garantit que le composant DriverSanctionIndex est correctement encapsulé
    dans le layout 'layouts.admin.catalyst-enterprise', résolvant ainsi les problèmes
    d'affichage du menu latéral et maintenant la cohérence visuelle de l'application.
    
    @package ZenFleet Enterprise
    @version 1.0.0
--}}

@extends('layouts.admin.catalyst')

@section('title', $pageTitle ?? 'Gestion des Sanctions Chauffeurs')

{{-- Breadcrumbs optionnels --}}
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
@section('breadcrumbs')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($breadcrumbs as $index => $breadcrumb)
            @if($breadcrumb['url'])
                <li class="inline-flex items-center">
                    @if($index > 0)
                        <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    <a href="{{ $breadcrumb['url'] }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        {{ $breadcrumb['title'] }}
                    </a>
                </li>
            @else
                <li aria-current="page">
                    @if($index > 0)
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $breadcrumb['title'] }}</span>
                        </div>
                    @else
                        <span class="text-sm font-medium text-gray-500">{{ $breadcrumb['title'] }}</span>
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endsection
@endif

@section('content')
    {{-- 
        Container principal pour le composant Livewire
        Le composant gère son propre état et ses interactions
    --}}
    <div class="sanctions-container">
        {{-- 
            Intégration du composant Livewire DriverSanctionIndex
            Ce composant contient toute la logique métier pour :
            - L'affichage de la liste des sanctions
            - La création de nouvelles sanctions
            - La modification des sanctions existantes
            - La suppression et l'archivage
            - Les filtres et la recherche
            - L'upload de pièces jointes
            
            Le composant est autonome mais s'intègre parfaitement
            dans le layout principal grâce à cette vue container
        --}}
        <livewire:admin.driver-sanction-index />
    </div>
@endsection

@push('styles')
<style>
    /* Styles spécifiques pour la page des sanctions si nécessaire */
    .sanctions-container {
        /* Assure que le container prend toute la largeur disponible */
        width: 100%;
        min-height: calc(100vh - 200px); /* Ajuste selon la hauteur du header/footer */
    }
    
    /* Animation pour les modals du composant Livewire */
    [x-cloak] { 
        display: none !important; 
    }
    
    /* Amélioration de l'affichage des badges de sévérité */
    .sanction-severity-badge {
        transition: all 0.2s ease-in-out;
    }
    
    .sanction-severity-badge:hover {
        transform: scale(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Script pour améliorer l'UX si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        // Listener pour les événements Livewire si nécessaire
        Livewire.on('sanctionCreated', () => {
            // Notification ou animation après création
            console.log('Sanction créée avec succès');
        });
        
        Livewire.on('sanctionUpdated', () => {
            // Notification ou animation après modification
            console.log('Sanction modifiée avec succès');
        });
        
        Livewire.on('sanctionDeleted', () => {
            // Notification ou animation après suppression
            console.log('Sanction supprimée avec succès');
        });
    });
    
    // Fonction helper pour les confirmations si nécessaire
    window.confirmAction = function(message) {
        return confirm(message || 'Êtes-vous sûr de vouloir effectuer cette action ?');
    }
</script>
@endpush
