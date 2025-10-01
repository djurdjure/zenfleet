{{-- resources/views/admin/maintenance/providers/create.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Nouveau Fournisseur - ZenFleet')

@section('content')
<div class="fade-in">
    {{-- En-tête --}}
    <div class="mb-8">
        <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
            <a href="{{ route('admin.maintenance.dashboard') }}" class="hover:text-indigo-600 transition-colors">
                <i class="fas fa-wrench mr-1"></i> Maintenance
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="{{ route('admin.maintenance.providers.index') }}" class="hover:text-indigo-600 transition-colors">
                Fournisseurs
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="text-indigo-600 font-semibold">Nouveau fournisseur</span>
        </nav>

        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold leading-6 text-gray-900">Nouveau Fournisseur de Maintenance</h1>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <i class="fas fa-building mr-2"></i>
                        Ajout d'un nouveau prestataire
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenu --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="text-center py-12">
            <i class="fas fa-building text-4xl text-green-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nouveau Fournisseur</h3>
            <p class="text-gray-500 mb-4">Module en cours de développement - Formulaire de création à venir</p>
        </div>
    </div>
</div>
@endsection