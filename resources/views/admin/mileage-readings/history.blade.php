{{--
    📊 Historique Kilométrique d'un Véhicule - Enterprise Grade

    Cette page charge le composant Livewire VehicleMileageHistory qui gère:
    - Affichage de l'historique des relevés d'un véhicule spécifique
    - Filtres avancés (date, méthode, auteur)
    - Statistiques du véhicule
    - Ajout de nouveaux relevés (modal)
    - Export de données

    Architecture: Route → Controller → View → @livewire
    Compatible: Livewire 3 + Laravel 12
--}}
@extends('layouts.admin.catalyst')

@section('title', 'Historique Kilométrique')

@section('content')
    @livewire('admin.vehicle-mileage-history', ['vehicleId' => $vehicleId])
@endsection
