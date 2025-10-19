{{--
 📊 Page des Relevés Kilométriques - Enterprise Grade

 Cette page charge le composant Livewire MileageReadingsIndex qui gère:
 - Affichage des relevés avec pagination
 - Filtres avancés (véhicule, méthode, dates)
 - Statistiques en temps réel
 - Export de données

 Architecture: Route → Controller → View → @livewire
 Compatible: Livewire 3 + Laravel 12
--}}
@extends('layouts.admin.catalyst')

@section('title', 'Relevés Kilométriques')

@section('content')
 @livewire('admin.mileage-readings-index')
@endsection
