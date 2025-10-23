{{-- resources/views/admin/documents/index-livewire.blade.php --}}
@extends('layouts.admin.catalyst')

@section('title', 'Gestion des Documents')

@section('content')
    @livewire('admin.document-manager-index')
@endsection
