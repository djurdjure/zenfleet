@extends('layouts.admin.catalyst')

@section('title', 'Mise à Jour du Kilométrage')

@section('content')
    {{--
    ====================================================================
    🚀 MILEAGE UPDATE PAGE - ENTERPRISE SINGLE PAGE V2
    ====================================================================
    
    Vue d'entrée pour le module de mise à jour du kilométrage
    Charge le composant Livewire MileageUpdateComponent
    
    @package Resources\Views\Admin\MileageReadings
    @version 2.0-Enterprise
    @since 2025-11-02
    ====================================================================
    --}}
    
    {{-- Le composant Livewire est chargé ici avec le layout complet --}}
    @livewire('admin.mileage.mileage-update-component', ['vehicleId' => $vehicleId ?? null])
@endsection
