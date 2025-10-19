{{--
 📊 Mise à jour du Kilométrage - Enterprise Grade

 Cette page charge le composant Livewire UpdateVehicleMileage qui gère:
 - Sélection du véhicule (selon rôle et permissions)
 - Saisie du nouveau kilométrage
 - Validation avancée (kilométrage croissant uniquement)
 - Enregistrement dans l'historique

 Architecture: Route → Controller → View → @livewire
 Compatible: Livewire 3 + Laravel 12
--}}
@extends('layouts.admin.catalyst')

@section('title', 'Mettre à jour le kilométrage')

@section('content')
 @if(isset($vehicleId))
 @livewire('admin.update-vehicle-mileage', ['vehicleId' => $vehicleId])
 @else
 @livewire('admin.update-vehicle-mileage')
 @endif
@endsection
