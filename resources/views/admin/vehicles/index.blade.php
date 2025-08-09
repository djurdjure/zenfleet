<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion de la Flotte - Véhicules') }}
        </h2>
    </x-slot>

    {{-- Initialisation d'Alpine.js pour gérer la modale de suppression/archivage --}}
    <div x-data="{
            showConfirmModal: false,
            modalAction: '', // 'archive' ou 'delete'
            modalTitle: '',
            modalDescription: '',
            modalButtonText: '',
            modalButtonClass: '',
            modalIconClass: '',
            vehicleToProcess: {},
            formUrl: '',
            isForceDelete: false,

            openModal(event, action) {
                const button = event.currentTarget;
                this.vehicleToProcess = JSON.parse(button.dataset.vehicle);
                this.formUrl = button.dataset.url;
                this.modalAction = action;

                if (action === 'archive') {
                    this.modalTitle = 'Archiver le Véhicule';
                    this.modalDescription = `Êtes-vous sûr de vouloir archiver le véhicule ${this.vehicleToProcess.brand} ${this.vehicleToProcess.model} (${this.vehicleToProcess.registration_plate}) ? Il pourra être restauré plus tard.`;
                    this.modalButtonText = 'Confirmer l\'Archivage';
                    this.modalButtonClass = 'bg-yellow-600 hover:bg-yellow-700';
                    this.modalIconClass = 'text-yellow-600 bg-yellow-100';
                    this.isForceDelete = false;
                } else if (action === 'delete') {
                    this.modalTitle = 'Suppression Définitive';
                    this.modalDescription = `Cette action est irréversible. Toutes les données associées à ce véhicule (maintenances, affectations...) seront définitivement perdues. Confirmez-vous la suppression du véhicule ${this.vehicleToProcess.brand} ${this.vehicleToProcess.model} (${this.vehicleToProcess.registration_plate}) ?`;
                    this.modalButtonText = 'Supprimer Définitivement';
                    this.modalButtonClass = 'bg-red-600 hover:bg-red-700';
                    this.modalIconClass = 'text-red-600 bg-red-100';
                    this.isForceDelete = true;
                }
                this.showConfirmModal = true;
            }
        }" class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres et de la Recherche --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.vehicles.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Immat, marque, modèle..." class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                @foreach($vehicleStatuses as $status)
                                    <option value="{{ $status->id }}" @selected(($filters['status_id'] ?? '') == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                @foreach(['15', '30', '50', '100'] as $value)
                                    <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="view_deleted" class="block text-sm font-medium text-gray-700">Affichage</label>
                            <select name="view_deleted" id="view_deleted" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Actifs</option>
                                <option value="true" @selected(request('view_deleted'))>Archivés</option>
                            </select>
                        </div>
                        <div class="flex-shrink-0 flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Filtrer</button>
                            <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Véhicules') }}</h3>
                        <div class="flex space-x-2">
                            @can('create vehicles')
                                <a href="{{ route('admin.vehicles.import.show') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                    <x-lucide-upload class="w-4 h-4 mr-2"/>
                                    Importer
                                </a>
                                <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                    <x-lucide-plus-circle class="w-4 h-4 mr-2"/>
                                    Ajouter
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Immatriculation</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Marque & Modèle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kilométrage</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vehicles as $vehicle)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 whitespace-nowrap text-sm font-mono text-gray-900">{{ $vehicle->registration_plate }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">{{ number_format($vehicle->current_mileage, 0, ',', ' ') }} km</td>
                                        <td class="px-6 py-3 whitespace-nowrap text-sm">
                                            @php
                                                $statusName = $vehicle->vehicleStatus?->name ?? 'Indéfini';
                                                $statusClass = 'bg-gray-100 text-gray-800'; // Default
                                                if ($vehicle->trashed()) {
                                                    $statusName = 'Archivé';
                                                    $statusClass = 'bg-gray-200 text-gray-600';
                                                } else {
                                                    switch ($statusName) {
                                                        case 'Parking': $statusClass = 'bg-blue-100 text-blue-800'; break;
                                                        case 'En mission': $statusClass = 'bg-green-100 text-green-800'; break;
                                                        case 'En maintenance': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'Hors service': $statusClass = 'bg-red-100 text-red-800'; break;
                                                    }
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusName }}</span>
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if ($vehicle->trashed())
                                                    @can('restore vehicles')
                                                        <form method="POST" action="{{ route('admin.vehicles.restore', $vehicle->id) }}">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" title="Restaurer" class="p-2 rounded-full text-gray-400 hover:bg-green-100 hover:text-green-600">
                                                                <x-lucide-rotate-ccw class="h-5 w-5"/>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('force delete vehicles')
                                                        <button type="button" @click="openModal($event, 'delete')" data-vehicle='@json($vehicle)' data-url="{{ route('admin.vehicles.force-delete', $vehicle->id) }}" title="Supprimer Définitivement" class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                            <x-lucide-trash-2 class="h-5 w-5"/>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @can('edit vehicles')
                                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                            <x-lucide-file-pen-line class="h-5 w-5"/>
                                                        </a>
                                                    @endcan
                                                    @can('delete vehicles')
                                                        <button type="button" @click="openModal($event, 'archive')" data-vehicle='@json($vehicle)' data-url="{{ route('admin.vehicles.destroy', $vehicle->id) }}" title="Archiver" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                            <x-lucide-archive class="h-5 w-5"/>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucun véhicule ne correspond à vos critères.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $vehicles->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Fenêtre Modale de Confirmation --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIconClass">
                        <x-lucide-alert-triangle x-show="modalAction === 'archive' || modalAction === 'delete'" class="h-6 w-6"/>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" x-text="modalTitle"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600" x-html="modalDescription"></p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="formUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto" :class="modalButtonClass" x-text="modalButtonText">
                        </button>
                    </form>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>