@extends('layouts.admin.catalyst')

@section('title', 'Mes Demandes de Réparation')

@section('content')
 {{-- 🚗 Composant Livewire des Demandes de Réparation - Chauffeur --}}
 @livewire('admin.repair-request-manager')
@endsection
