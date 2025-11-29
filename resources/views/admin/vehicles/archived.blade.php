{{-- resources/views/admin/vehicles/archived.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Véhicules Archivés - ZenFleet')

@section('content')
    {{-- ====================================================================
     🗂️ PAGE VÉHICULES ARCHIVÉS - ARCHITECTURE LIVEWIRE 3
     ====================================================================

     Cette page utilise maintenant un composant Livewire pour :
     ✨ Réactivité temps réel sans rafraîchissement page
     ✨ Actions CRUD instantanées (restaurer, supprimer)
     ✨ Gestion du cache automatique
     ✨ Notifications en temps réel
     ✨ Pas de problème de cache navigateur

     @version 2.0-Livewire-Enterprise-Ultra
     @since 2025-11-27
     ==================================================================== --}}

    @livewire('admin.vehicles.archived-vehicles')

@endsection
