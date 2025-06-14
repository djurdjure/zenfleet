<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Chauffeurs') }}
        </h2>
    </x-slot>

    {{-- Initialisation d'Alpine.js pour gérer la modale de suppression --}}
    <div x-data="{
            showConfirmModal: false,
            driverToDelete: {},
            deleteFormUrl: '',
            openDeleteModal(event) {
                const button = event.currentTarget;
                this.driverToDelete = JSON.parse(button.dataset.driver);
                this.deleteFormUrl = button.dataset.url;
                this.showConfirmModal = true;
            }
        }"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres et de la Recherche --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.drivers.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nom, matricule, N° permis..." class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                @foreach($driverStatuses as $status)
                                    <option value="{{ $status->id }}" @selected(($filters['status_id'] ?? '') == $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                @foreach(['10', '20', '50', '100'] as $value)
                                    <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="view_deleted" class="block text-sm font-medium text-gray-700">Affichage</label>
                            <select name="view_deleted" id="view_deleted" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                <option value="">Actifs</option>
                                <option value="true" @selected(request('view_deleted'))>Archivés</option>
                            </select>
                        </div>
                        <div class="flex-shrink-0 flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">Filtrer</button>
                            <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><p class="font-bold">{{ session('success') }}</p></div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Chauffeurs') }}</h3>
                        @can('create drivers')
                            <a href="{{ route('admin.drivers.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Ajouter un Chauffeur
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chauffeur</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Matricule</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Utilisateur Lié</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($drivers as $driver)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($driver->photo_path)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $driver->photo_path) }}" alt="Photo de {{ $driver->first_name }}">
                                                    @else
                                                        <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $driver->last_name }} {{ $driver->first_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $driver->personal_phone ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $driver->employee_number ?? '-' }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            {{-- Logique de couleur pour les statuts --}}
                                            @php
                                                $statusName = $driver->driverStatus?->name ?? 'Indéfini';
                                                $statusClass = 'bg-gray-100 text-gray-800'; // Default
                                                if ($driver->trashed()) {
                                                    $statusName = 'Archivé';
                                                    $statusClass = 'bg-gray-200 text-gray-600';
                                                } else {
                                                    switch ($statusName) {
                                                        case 'Actif': $statusClass = 'bg-green-100 text-green-800'; break;
                                                        case 'En mission': $statusClass = 'bg-blue-100 text-blue-800'; break; // <-- AJOUT
                                                        case 'En congé': $statusClass = 'bg-indigo-100 text-indigo-800'; break;
                                                        case 'Suspendu': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'Inactif': $statusClass = 'bg-gray-300 text-gray-700'; break;
                                                    }
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusName }}</span>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $driver->user?->email ?? 'Non lié' }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                                          {{--///////////////////DEBUT BOUTONS EDIT SUPP ///////--}}
                                            <div class="flex items-center justify-end space-x-2">
                                                 @if ($driver->trashed())
                                                    @can('restore drivers')
                                                        <form method="POST" action="{{ route('admin.drivers.restore', $driver->id) }}">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" title="Restaurer" class="p-2 rounded-full text-gray-400 hover:bg-green-100 hover:text-green-600">
                                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M7 9a8.25 8.25 0 0110.61 2.61M20 20v-5h-5M17 15a8.25 8.25 0 01-10.61-2.61" /></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('force delete drivers')
                                                        <button type="button" @click="openDeleteModal($event, true)" data-driver='@json($driver->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.drivers.force-delete', $driver->id) }}" title="Supprimer Définitivement" class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @can('edit drivers')
                                                        <a href="{{ route('admin.drivers.edit', $driver) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-violet-100 hover:text-violet-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete drivers')
                                                        <button type="button" @click="openDeleteModal($event)" data-driver='@json($driver->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.drivers.destroy', $driver->id) }}" title="Archiver" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4H5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 8v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8h14z" /></svg>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        {{--///////////////////FIN BOUTONS EDIT SUPP ///////--}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucun chauffeur trouvé.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $drivers->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>

        {{-- Fenêtre Modale de Confirmation de Suppression pour les Chauffeurs --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Supprimer le Chauffeur</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">
                                Êtes-vous sûr de vouloir supprimer <strong class="font-bold" x-text="driverToDelete.first_name + ' ' + driverToDelete.last_name"></strong> ?
                            </p>
                            <p class="mt-1 text-sm text-gray-500">Cette action est irréversible.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="deleteFormUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">Confirmer</button>
                    </form>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Annuler</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
