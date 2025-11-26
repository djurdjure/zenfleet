@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="max-w-7xl mx-auto">
    @livewire('admin.user-permission-manager', ['userId' => $userId])
</div>
@endsection
