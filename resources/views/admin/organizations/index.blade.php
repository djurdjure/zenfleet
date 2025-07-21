<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Organisations') }}
        </h2>
    </x-slot>

    <div x-data="{ showConfirmModal: false, deleteFormUrl: '' }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><p class="font-bold">{{ session('success') }}</p></div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">Toutes les Organisations</h3>
                        @can('create organizations')
                            <a href="{{ route('admin.organizations.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 ...">Ajouter</a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left ...">Nom de l'Organisation</th>
                                    <th class="px-6 py-3 text-left ...">Statut</th>
                                    <th class="px-6 py-3 text-left ...">Utilisateurs</th>
                                    <th class="px-6 py-3 text-right ...">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($organizations as $organization)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 ...">{{ $organization->name }}</td>
                                        <td class="px-6 py-4 ...">{{ $organization->status }}</td>
                                        <td class="px-6 py-4 ...">{{ $organization->users_count }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.organizations.edit', $organization) }}" title="Modifier" class="p-2 ..."><svg></svg></a>
                                                <button @click="showConfirmModal = true; deleteFormUrl = '{{ route('admin.organizations.destroy', $organization) }}'" title="Supprimer" class="p-2 ..."><svg></svg></button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-4 text-center ...">Aucune organisation trouvée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modale de confirmation de suppression --}}
    </div>
</x-app-layout>
