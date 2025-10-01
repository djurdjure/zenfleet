@extends('layouts.admin')

@section('title', 'Fournisseurs Enterprise')

@section('content')
<div class="space-y-6">
    {{-- Header avec actions --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fournisseurs Enterprise</h1>
            <p class="mt-2 text-sm text-gray-700">Gestion complète des fournisseurs avec conformité réglementaire algérienne</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
            <a href="{{ route('admin.suppliers-enterprise.export') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-lucide-download class="h-4 w-4 mr-2" />
                Exporter
            </a>
            <a href="{{ route('admin.suppliers-enterprise.import') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-lucide-upload class="h-4 w-4 mr-2" />
                Importer
            </a>
            <a href="{{ route('admin.suppliers-enterprise.create') }}"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <x-lucide-plus class="h-4 w-4 mr-2" />
                Nouveau fournisseur
            </a>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-lucide-building-2 class="h-8 w-8 text-blue-500" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total fournisseurs</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-lucide-check-circle class="h-8 w-8 text-green-500" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Actifs</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['active'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-lucide-star class="h-8 w-8 text-yellow-500" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Note moyenne</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['average_rating'] ?? 0, 1) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-lucide-shield-check class="h-8 w-8 text-indigo-500" />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Conformes DZ</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['compliant'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Filtres</h3>
        </div>
        <div class="px-6 py-4">
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                {{-- Recherche --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Nom, NIF, RC..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Statut --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>

                {{-- Wilaya --}}
                <div>
                    <label for="wilaya" class="block text-sm font-medium text-gray-700">Wilaya</label>
                    <select name="wilaya" id="wilaya" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Toutes les wilayas</option>
                        @foreach($wilayas as $wilaya)
                            <option value="{{ $wilaya }}" {{ request('wilaya') === $wilaya ? 'selected' : '' }}>{{ $wilaya }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Spécialité --}}
                <div>
                    <label for="specialty" class="block text-sm font-medium text-gray-700">Spécialité</label>
                    <select name="specialty" id="specialty" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Toutes les spécialités</option>
                        <option value="engine" {{ request('specialty') === 'engine' ? 'selected' : '' }}>Moteur</option>
                        <option value="brake" {{ request('specialty') === 'brake' ? 'selected' : '' }}>Freinage</option>
                        <option value="electrical" {{ request('specialty') === 'electrical' ? 'selected' : '' }}>Électrique</option>
                        <option value="bodywork" {{ request('specialty') === 'bodywork' ? 'selected' : '' }}>Carrosserie</option>
                        <option value="tires" {{ request('specialty') === 'tires' ? 'selected' : '' }}>Pneumatiques</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <x-lucide-search class="h-4 w-4 mr-2" />
                        Filtrer
                    </button>
                    <a href="{{ route('admin.suppliers-enterprise.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Composant Livewire --}}
    @livewire('admin.supplier-manager', [
        'filters' => [
            'search' => request('search'),
            'status' => request('status'),
            'wilaya' => request('wilaya'),
            'specialty' => request('specialty')
        ]
    ])
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const selects = document.querySelectorAll('#status, #wilaya, #specialty');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
@endsection