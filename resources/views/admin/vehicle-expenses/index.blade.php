@extends('layouts.admin')

@section('title', 'Dépenses Véhicules')

@section('content')
<div class="space-y-6">
    {{-- Header avec actions --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dépenses Véhicules</h1>
            <p class="mt-2 text-sm text-gray-700">Gestion complète des dépenses avec traçabilité maximale et conformité fiscale</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
            <a href="{{ route('admin.vehicle-expenses.export') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-icon icon="heroicons:arrow-down-tray" class="h-4 w-4 mr-2" / />
                Exporter
            </a>
            <a href="{{ route('admin.vehicle-expenses.reports') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-icon icon="heroicons:chart-bar"-3 class="h-4 w-4 mr-2" / />
                Rapports
            </a>
            <a href="{{ route('admin.vehicle-expenses.create') }}"
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                <x-icon icon="heroicons:plus" class="h-4 w-4 mr-2" / />
                Nouvelle dépense
            </a>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:receipt-percent" class="h-8 w-8 text-blue-500" / />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total dépenses</dt>
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
                        <x-icon icon="heroicons:credit-card" class="h-8 w-8 text-green-500" / />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Montant total</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_amount'] ?? 0, 0) }} DA</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:clock" class="h-8 w-8 text-yellow-500" / />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['pending'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:exclamation-circle" class="h-8 w-8 text-red-500" / />
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Paiements dus</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['overdue'] ?? 0 }}</dd>
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
            <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                {{-- Recherche --}}
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Description, référence..."
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Véhicule --}}
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Véhicule</label>
                    <select name="vehicle_id" id="vehicle_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Catégorie --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Toutes les catégories</option>
                        <option value="fuel" {{ request('category') === 'fuel' ? 'selected' : '' }}>Carburant</option>
                        <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="repair" {{ request('category') === 'repair' ? 'selected' : '' }}>Réparation</option>
                        <option value="insurance" {{ request('category') === 'insurance' ? 'selected' : '' }}>Assurance</option>
                        <option value="tolls" {{ request('category') === 'tolls' ? 'selected' : '' }}>Péages</option>
                        <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>

                {{-- Statut approbation --}}
                <div>
                    <label for="approval_status" class="block text-sm font-medium text-gray-700">Approbation</label>
                    <select name="approval_status" id="approval_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('approval_status') === 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Approuvée</option>
                        <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Rejetée</option>
                    </select>
                </div>

                {{-- Période --}}
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700">Du</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Actions --}}
                <div class="flex items-end space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <x-icon icon="heroicons:magnifying-glass" class="h-4 w-4 mr-2" / />
                        Filtrer
                    </button>
                    <a href="{{ route('admin.vehicle-expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Actions rapides --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Actions rapides</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.vehicle-expenses.index', ['approval_status' => 'pending']) }}"
                   class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:clock" class="h-8 w-8 text-yellow-500" / />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Dépenses en attente</p>
                        <p class="text-sm text-gray-500">{{ $stats['pending'] ?? 0 }} dépenses</p>
                    </div>
                </a>

                <a href="{{ route('admin.vehicle-expenses.payments-due') }}"
                   class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:exclamation-circle" class="h-8 w-8 text-red-500" / />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Paiements dus</p>
                        <p class="text-sm text-gray-500">{{ $stats['overdue'] ?? 0 }} paiements</p>
                    </div>
                </a>

                <a href="{{ route('admin.expense-budgets.index') }}"
                   class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400">
                    <div class="flex-shrink-0">
                        <x-icon icon="lucide:wallet" class="h-8 w-8 text-indigo-500" / />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Gérer les budgets</p>
                        <p class="text-sm text-gray-500">Suivi et alertes</p>
                    </div>
                </a>

                <a href="{{ route('admin.vehicle-expenses.recurring') }}"
                   class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400">
                    <div class="flex-shrink-0">
                        <x-icon icon="heroicons:arrow-path" class="h-8 w-8 text-green-500" / />
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="absolute inset-0" aria-hidden="true"></span>
                        <p class="text-sm font-medium text-gray-900">Dépenses récurrentes</p>
                        <p class="text-sm text-gray-500">Gestion automatique</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Composant Livewire --}}
    @livewire('admin.expense-tracker', [
        'filters' => [
            'search' => request('search'),
            'vehicle_id' => request('vehicle_id'),
            'category' => request('category'),
            'approval_status' => request('approval_status'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to')
        ]
    ])
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const selects = document.querySelectorAll('#vehicle_id, #category, #approval_status');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Auto-submit on date change
    const dateInputs = document.querySelectorAll('#date_from, #date_to');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endpush
@endsection