{{-- resources/views/admin/maintenance/types/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Types de Maintenance - ZenFleet')

@section('content')
<div class="fade-in">
 {{-- En-tête compact --}}
 <div class="mb-8">
 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-xl font-semibold leading-6 text-gray-900">Types de Maintenance</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
 </svg>
 Configuration des types de maintenance ultra-professionnelle
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 <a href="{{ route('admin.maintenance.types.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
 </svg>
 Nouveau type
 </a>
 </div>
 </div>
 </div>

 {{-- Message pour utilisateur s'il n'y a pas de données --}}
 <div class="bg-white shadow rounded-lg overflow-hidden">
 <div class="text-center py-12">
 <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
 </svg>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Module en cours de développement</h3>
 <p class="text-gray-500 mb-4">Les types de maintenance sont en cours de création.</p>
 <a href="{{ route('admin.maintenance.types.create') }}"
 class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
 </svg>
 Créer un type
 </a>
 </div>
 </div>
</div>
@endsection