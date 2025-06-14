<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion de la Flotte - Véhicules') }}
        </h2>
    </x-slot>

    {{-- Le composant Alpine.js est initialisé ici, avec la logique pour la modale --}}
    <div x-data="{
            showConfirmModal: false,
            vehicleToDelete: {},
            deleteFormUrl: '',
            openDeleteModal(event) {
                const button = event.currentTarget;
                this.vehicleToDelete = JSON.parse(button.dataset.vehicle);
                this.deleteFormUrl = button.dataset.url;
                this.showConfirmModal = true;
            }
        }"
         class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres et de la Recherche --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.vehicles.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Immat, marque, modèle..." class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                @foreach($vehicleStatuses as $status)
                                    <option value="{{ $status->id }}" @selected(($filters['status_id'] ?? '') == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                @foreach(['10', '20', '50', '100'] as $value)
                                    <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--///////////___________________affichage des vehicules archivés   --}}
                        <div>
                            <label for="view_deleted" class="block text-sm font-medium text-gray-700">Affichage</label>
                            <select name="view_deleted" id="view_deleted" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                <option value="">Actifs</option>
                                <option value="true" @selected(request('view_deleted'))>Archivés</option>
                            </select>
                        </div>
                       {{--//////////____________________--}}
                        <div class="flex-shrink-0 flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">Filtrer</button>
                            <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Véhicules') }}</h3>
                        <div class="flex space-x-2">
                            @can('create vehicles')
                                <a href="{{ route('admin.vehicles.import.show') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Importer
                                </a>
                                <a href="{{ route('admin.vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
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
                                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-800">{{ $vehicle->brand }}  {{ $vehicle->model }}</td>
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
                                                        case 'En service': $statusClass = 'bg-green-100 text-green-800'; break;
                                                        case 'En mission': $statusClass = 'bg-blue-100 text-blue-800'; break; // <-- AJOUT
                                                        case 'En maintenance': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'Hors service': $statusClass = 'bg-red-100 text-red-800'; break;
                                                    }
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusName }}</span>
                                        </td>

                                        {{--___________BOUTONS ACTION_________--}}
                                        <td>
                                         {{--///////////////////DEBUT BOUTONS EDIT SUPP ///////--}}
                                            <div class="flex items-center justify-end space-x-2">
                                                 @if ($vehicle->trashed())
                                                    @can('restore vehicles')
                                                        <form method="POST" action="{{ route('admin.vehicles.restore', $vehicle->id) }}">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" title="Restaurer" class="p-2 rounded-full text-gray-400 hover:bg-green-100 hover:text-green-600">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M7 9a8.25 8.25 0 0110.61 2.61M20 20v-5h-5M17 15a8.25 8.25 0 01-10.61-2.61" /></svg>
                                                            </button>
                                                        </form>
                                                     @endcan
                                                     @can('force delete vehicles')
                                                        <button type="button" @click="openDeleteModal($event, true)" data-vehicle='@json($vehicle->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.vehicles.force-delete', $vehicle->id) }}" title="Supprimer Définitivement" class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @can('edit vehicles')
                                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-violet-100 hover:text-violet-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete vehicles')
                                                        <button type="button" @click="openDeleteModal($event)" data-vehicle='@json($vehicle->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.vehicles.destroy', $vehicle->id) }}" title="Archiver" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4H5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 8v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8h14z" /></svg>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        {{--///////////////////FIN BOUTONS EDIT SUPP ///////--}}
                                    </td>
                                        {{--___________FIN BOUTON ACTION__________--}}
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

        {{-- Fenêtre Modale de Confirmation de Suppression --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Confirmer la Suppression</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir supprimer le véhicule <strong class="font-bold" x-text="vehicleToDelete.brand + ' ' + vehicleToDelete.model + ' (' + vehicleToDelete.registration_plate + ')'"></strong> ?
                            </p>
                            <p class="mt-1 text-sm text-gray-500">Cette action est définitive et irréversible.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="deleteFormUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
                            Confirmer
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
