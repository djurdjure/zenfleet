<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Documents') }}
            </h2>
            <div>
                @can('create documents')
                    <x-primary-button-link href="{{ route('admin.documents.create') }}">
                        <x-lucide-upload class="w-4 h-4 mr-2" />
                        {{ __('Importer un Document') }}
                    </x-primary-button-link>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3 rounded-l-lg">Nom du fichier</th>
                                    <th scope="col" class="px-6 py-3">Catégorie</th>
                                    <th scope="col" class="px-6 py-3">Taille</th>
                                    <th scope="col" class="px-6 py-3">Date d'expiration</th>
                                    <th scope="col" class="px-6 py-3">Importé par</th>
                                    <th scope="col" class="px-6 py-3">Date d'import</th>
                                    <th scope="col" class="px-6 py-3 rounded-r-lg"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap flex items-center gap-2">
                                            <x-lucide-file class="w-4 h-4 text-gray-500" />
                                            {{ $document->original_filename }}
                                        </th>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $document->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $document->formatted_size }}</td>
                                        <td class="px-6 py-4">
                                            @if($document->expiry_date)
                                                <span class="{{ $document->expiry_date->isPast() ? 'text-red-500 font-semibold' : '' }}">
                                                    {{ $document->expiry_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $document->uploader->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            @can('download documents')
                                                <a href="#" class="font-medium text-primary-600 dark:text-primary-500 hover:underline">Télécharger</a>
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
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <x-lucide-file-x class="w-12 h-12 text-gray-400 mb-4" />
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Aucun document trouvé</h3>
                                                <p class="mt-1 text-sm text-gray-500">Commencez par importer un nouveau document.</p>
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
    </div>
</x-app-layout>
