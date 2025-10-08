@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Demandes de Réparation')

@section('content')
    {{-- 🔧 Composant Livewire Kanban des Demandes de Réparation --}}
    @livewire('admin.repair-request-manager')
@endsection
