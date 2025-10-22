@extends('layouts.admin.catalyst')

@section('title', 'Importation de Chauffeurs')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                <x-iconify icon="heroicons:home" class="w-4 h-4" />
            </a>
            <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
            <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">Chauffeurs</a>
            <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
            <span class="font-semibold text-gray-900">Importation</span>
        </nav>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <x-iconify icon="heroicons:arrow-up-tray" class="w-8 h-8 text-white" />
                </div>
                Importation de Chauffeurs
            </h1>
            <p class="text-gray-600 mt-2 ml-20">
                Importez votre équipe en masse via fichier CSV ou Excel
            </p>
        </div>

        {{-- Composant Livewire --}}
        @livewire('admin.drivers.drivers-import')
        
    </div>
</section>
@endsection
