@extends('layouts.admin.catalyst')
@section('title', 'Matrice des Permissions - ZenFleet')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6">
 @livewire('admin.permission-matrix')
</div>
@endsection
