{{-- resources/views/admin/documents/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Documents') }}
            </h2>
            @can('create documents')
                <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('Importer un Document') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nom du fichier</th>
                                    <th scope="col" class="px-6 py-3">Catégorie</th>
                                    <th scope="col" class="px-6 py-3">Taille</th>
                                    <th scope="col" class="px-6 py-3">Date d'expiration</th>
                                    <th scope="col" class="px-6 py-3">Importé par</th>
                                    <th scope="col" class="px-6 py-3">Date d'import</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $document->original_filename }}</th>
                                        <td class="px-6 py-4">{{ $document->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $document->formatted_size }}</td>
                                        <td class="px-6 py-4">{{ $document->expiry_date ? $document->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $document->uploader->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            {{-- Download link to be implemented later with secure URLs --}}
                                            @can('download documents')
                                            <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Télécharger</a>
                                            @endcan
                                            @can('delete documents')
                                            <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="inline-block ml-4" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">Supprimer</button>
                                            </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center">Aucun document trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
