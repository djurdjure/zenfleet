@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Dépôts')

@section('content')
    <div class="container mx-auto px-4 py-6">
        @livewire('depots.manage-depots')
    </div>
@endsection
