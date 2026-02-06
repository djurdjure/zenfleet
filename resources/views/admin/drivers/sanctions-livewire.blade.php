@extends('layouts.admin.catalyst')

@section('title', 'Sanctions des Chauffeurs')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                <x-iconify icon="heroicons:home" class="w-4 h-4" />
            </a>
            <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
            <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">Chauffeurs</a>
            <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
            <span class="font-semibold text-gray-900">Sanctions</span>
        </nav>

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
                Sanctions des Chauffeurs
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Gérez l'historique des sanctions de votre équipe
            </p>
        </div>

        {{-- Composant Livewire --}}
        @livewire('admin.drivers.driver-sanctions')
        
    </div>
</section>
@endsection
