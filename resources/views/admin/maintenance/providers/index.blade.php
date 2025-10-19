{{-- resources/views/admin/maintenance/providers/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Fournisseurs de Maintenance - ZenFleet')

@section('content')
<div class="fade-in">
 {{-- En-tête --}}
 <div class="mb-8">
 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-xl font-semibold leading-6 text-gray-900">Fournisseurs de Maintenance</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <i class="fas fa-building mr-2"></i>
 Gestion des prestataires et fournisseurs
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 <a href="{{ route('admin.maintenance.providers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
 <i class="fas fa-plus mr-2"></i>
 Nouveau fournisseur
 </a>
 </div>
 </div>
 </div>

 {{-- Contenu --}}
 <div class="bg-white shadow rounded-lg overflow-hidden">
 <div class="text-center py-12">
 <i class="fas fa-building text-4xl text-green-400 mb-4"></i>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Fournisseurs de Maintenance</h3>
 <p class="text-gray-500 mb-4">Module en cours de développement - Gestion des fournisseurs à venir</p>
 </div>
 </div>
</div>
@endsection