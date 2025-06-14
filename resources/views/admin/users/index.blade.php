{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administration - Gestion des Utilisateurs') }}
        </h2>
    </x-slot>

    {{-- Initialisation d'Alpine.js pour la modale de confirmation --}}
    <div x-data="{ showConfirmModal: false, userToDelete: {}, deleteFormUrl: '' }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">{{ __('Liste des Utilisateurs') }}</h3>
                        @can('create users')
                            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('Ajouter un Utilisateur') }}
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nom Complet</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Rôles</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Inscrit le</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-800">{{ $user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @forelse($user->roles as $role)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-800">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs italic text-gray-400">Aucun rôle</span>
                                            @endforelse
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>

                                        {{-- ##### SECTION DES BOUTONS D'ACTION CORRIGÉE ##### --}}
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @can('edit users')
                                                    <a href="{{ route('admin.users.edit', $user) }}" title="Modifier" class="p-2 rounded-full text-gray-400 hover:bg-violet-100 hover:text-violet-600 transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                                    </a>
                                                @endcan
                                                @can('delete users')
                                                    <button type="button" 
                                                            @click="showConfirmModal = true; userToDelete = {{ json_encode($user->only(['id', 'name'])) }}; deleteFormUrl = '{{ route('admin.users.destroy', $user->id) }}'" 
                                                            title="Supprimer" 
                                                            class="p-2 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600 transition-colors duration-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                        {{-- ##### FIN DE LA SECTION CORRIGÉE ##### --}}

                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucun utilisateur trouvé.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $users->links() }}</div>
                </div>
            </div>
        </div>

        {{-- Fenêtre Modale de Confirmation de Suppression --}}
        <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showConfirmModal = false" class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirmer la Suppression</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong class="font-bold" x-text="userToDelete.name"></strong> ?</p>
                            <p class="mt-1 text-sm text-gray-500">Cette action est définitive et irréversible.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <form :action="deleteFormUrl" method="POST" x-ref="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
                            Confirmer la Suppression
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
