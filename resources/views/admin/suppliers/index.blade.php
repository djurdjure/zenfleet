<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Fournisseurs') }}
        </h2>
    </x-slot>

    <div x-data="{
        showConfirmModal: false,
        modalTitle: '',
        modalDescription: '',
        modalButtonText: '',
        modalButtonClass: '',
        modalIconClass: '',
        formUrl: '',

        openModal(event) {
            const button = event.currentTarget;
            const supplier = JSON.parse(button.dataset.supplier);
            this.formUrl = button.dataset.url;

            this.modalTitle = 'Supprimer le Fournisseur';
            this.modalDescription = `Êtes-vous sûr de vouloir supprimer le fournisseur <strong>${supplier.name}</strong> ? Cette action est irréversible.`;
            this.modalButtonText = 'Confirmer la Suppression';
            this.modalButtonClass = 'bg-red-600 hover:bg-red-700';
            this.modalIconClass = 'text-red-600 bg-red-100';

            this.showConfirmModal = true;
        }
    }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Fournisseurs') }}</h3>
                        @can('create suppliers')
                            <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                <x-lucide-plus-circle class="w-4 h-4 mr-2" stroke-width="1.5"/>
                                Ajouter
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Catégorie</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Téléphone</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($suppliers as $supplier)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supplier->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->contact_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supplier->phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @can('edit suppliers')
                                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                        <x-lucide-file-pen-line class="h-5 w-5" stroke-width="1.5"/>
                                                    </a>
                                                @endcan
                                                @can('delete suppliers')
                                                    <button type="button"
                                                            @click="openModal($event)"
                                                            data-supplier='@json($supplier)'
                                                            data-url="{{ route('admin.suppliers.destroy', $supplier) }}"
                                                            title="Supprimer"
                                                            class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600">
                                                        <x-lucide-trash-2 class="h-5 w-5" stroke-width="1.5"/>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Aucun fournisseur trouvé.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     @if ($suppliers->hasPages())
                        <div class="mt-6">
                            {{ $suppliers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Fenêtre Modale de Confirmation --}}
        <div x-show="showConfirmModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10" :class="modalIconClass">
                        <x-lucide-alert-triangle class="h-6 w-6" stroke-width="1.5"/>
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