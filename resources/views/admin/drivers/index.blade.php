<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Chauffeurs') }}
        </h2>
    </x-slot>

    {{-- Initialisation d'Alpine.js pour gérer la modale dynamique --}}
    <div x-data="{
            showConfirmModal: false,
            modalAction: '', // 'archive' ou 'delete'
            modalTitle: '',
            modalDescription: '',
            modalButtonText: '',
            modalButtonClass: '',
            modalIconClass: '',
            driverToProcess: {},
            formUrl: '',

            openModal(event, action) {
                const button = event.currentTarget;
                this.driverToProcess = JSON.parse(button.dataset.driver);
                this.formUrl = button.dataset.url;
                this.modalAction = action;

                if (action === 'archive') {
                    this.modalTitle = 'Archiver le Chauffeur';
                    this.modalDescription = `Êtes-vous sûr de vouloir archiver le chauffeur <strong>${this.driverToProcess.first_name} ${this.driverToProcess.last_name}</strong> ? Il pourra être restauré plus tard.`;
                    this.modalButtonText = 'Confirmer l\'Archivage';
                    this.modalButtonClass = 'bg-yellow-600 hover:bg-yellow-700';
                    this.modalIconClass = 'text-yellow-600 bg-yellow-100';
                } else if (action === 'delete') {
                    this.modalTitle = 'Suppression Définitive';
                    this.modalDescription = `Cette action est irréversible et supprimera définitivement le chauffeur <strong>${this.driverToProcess.first_name} ${this.driverToProcess.last_name}</strong>. Confirmez-vous cette action ?`;
                    this.modalButtonText = 'Supprimer Définitivement';
                    this.modalButtonClass = 'bg-red-600 hover:bg-red-700';
                    this.modalIconClass = 'text-red-600 bg-red-100';
                }
                this.showConfirmModal = true;
            }
        }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres et de la Recherche --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.drivers.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nom, matricule, N° permis..." class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status_id" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                @foreach($driverStatuses as $status)
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
                            <a href="{{ route('admin.drivers.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Chauffeurs') }}</h3>
                        <div class="flex space-x-2">
                            @can('create drivers')
                                <a href="{{ route('admin.drivers.import.show') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition">
                                    <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2"/>
                                    Importer
                                </a>
                                <a href="{{ route('admin.drivers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                    <x-heroicon-o-plus-circle class="w-4 h-4 mr-2"/>
                                    Ajouter
                                </a>
                            @endcan
                        </div>
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
                                                            <x-heroicon-s-user class="h-6 w-6 text-gray-400"/>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $driver->last_name }} {{ $driver->first_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $driver->personal_phone ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $driver->employee_number ?? '-' }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            @php
                                                $statusName = $driver->driverStatus?->name ?? 'Indéfini';
                                                $statusClass = 'bg-gray-100 text-gray-800'; // Default
                                                if ($driver->trashed()) {
                                                    $statusName = 'Archivé';
                                                    $statusClass = 'bg-gray-200 text-gray-600';
                                                } else {
                                                    switch ($statusName) {
                                                        case 'Disponible': $statusClass = 'bg-green-100 text-green-800'; break;
                                                        case 'En mission': $statusClass = 'bg-blue-100 text-blue-800'; break;
                                                        case 'En congé': $statusClass = 'bg-indigo-100 text-indigo-800'; break;
                                                        case 'Suspendu': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'Inactif':
                                                        case 'Ex-employé':
                                                            $statusClass = 'bg-red-100 text-red-800'; break;
                                                    }
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">{{ $statusName }}</span>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $driver->user?->email ?? 'Non lié' }}</td>
                                        <td class="px-6 py-2 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if ($driver->trashed())
                                                    @can('restore drivers')
                                                        <form method="POST" action="{{ route('admin.drivers.restore', $driver->id) }}">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" title="Restaurer" class="p-2 rounded-full text-gray-400 hover:bg-green-100 hover:text-green-600">
                                                                <x-heroicon-o-arrow-uturn-left class="h-5 w-5"/>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @can('force delete drivers')
                                                        <button type="button" @click="openModal($event, 'delete')" data-driver='@json($driver->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.drivers.force-delete', $driver->id) }}" title="Supprimer Définitivement" class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                            <x-heroicon-o-trash class="h-5 w-5"/>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @can('edit drivers')
                                                        <a href="{{ route('admin.drivers.edit', $driver) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                            <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                                        </a>
                                                    @endcan
                                                    @can('delete drivers')
                                                        <button type="button" @click="openModal($event, 'archive')" data-driver='@json($driver->only(['id', 'first_name', 'last_name']))' data-url="{{ route('admin.drivers.destroy', $driver->id) }}" title="Archiver" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                            <x-heroicon-o-archive-box-arrow-down class="h-5 w-5"/>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
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

        {{-- Fenêtre Modale de Confirmation --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIconClass">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6"/>
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