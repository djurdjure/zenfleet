<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Documents') }}
        </h2>
    </x-slot>

    <div x-data="{
 showConfirmModal: false,
 modalTitle: 'Supprimer le Document',
 modalDescription: '',
 modalButtonText: 'Supprimer Définitivement',
 modalButtonClass: 'bg-red-600 hover:bg-red-700',
 modalIconClass: 'text-red-600 bg-red-100',
 documentToProcess: {},
 formUrl: '',

 openModal(event) {
 const button = event.currentTarget;
 this.documentToProcess = JSON.parse(button.dataset.document);
 this.formUrl = button.dataset.url;
 this.modalDescription = `Êtes-vous sûr de vouloir supprimer le document <strong>${this.documentToProcess.original_filename}</strong> ? Cette action est irréversible.`;
 this.showConfirmModal = true;
 }
 }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Documents') }}</h3>
                        <div class="flex space-x-2">
                            @can('documents.create')
                            <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                <x-iconify icon="heroicons:arrow-up-tray" class="w-4 h-4 mr-2" stroke-width="1.5" / />
                                Importer
                            </a>
                            @endcan
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nom du fichier</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Catégorie</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Taille</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date d'expiration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Importé par</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date d'import</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($documents as $document)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center gap-2">
                                        <x-iconify icon="heroicons:document" class="w-5 h-5 text-gray-400" stroke-width="1.5" / />
                                        <span>{{ $document->original_filename }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-700 border border-gray-200">
                                            {{ $document->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->formatted_size }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($document->expiry_date)
                                        <span class="{{ $document->expiry_date->isPast() ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                            {{ $document->expiry_date->format('d/m/Y') }}
                                        </span>
                                        @else
                                        <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->uploader->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @can('documents.download')
                                            <a href="#" title="Télécharger" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                <x-iconify icon="heroicons:arrow-down-tray" class="h-5 w-5" stroke-width="1.5" / />
                                            </a>
                                            @endcan
                                            @can('documents.update')
                                            <a href="{{ route('admin.documents.edit', $document) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                <x-iconify icon="heroicons:document" -pen-line class="h-5 w-5" stroke-width="1.5" / />
                                            </a>
                                            @endcan
                                            @can('documents.delete')
                                            <button type="button" @click="openModal($event)"
                                                data-document='@json($document->only([' id', 'original_filename' ]))'
                                                data-url="{{ route('admin.documents.destroy', $document) }}"
                                                title="Supprimer"
                                                class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                <x-iconify icon="heroicons:trash" class="h-5 w-5" stroke-width="1.5" / />
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <x-iconify icon="heroicons:document" -x class="w-12 h-12 text-gray-400 mb-4" / />
                                            <h3 class="text-lg font-semibold text-gray-800">Aucun document trouvé</h3>
                                            <p class="mt-1 text-sm">Commencez par importer un nouveau document.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($documents->hasPages())
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Fenêtre Modale de Confirmation --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIconClass">
                        <x-iconify icon="heroicons:exclamation-triangle" class="h-6 w-6" stroke-width="1.5" / />
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
