<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Document : ') }} {{ $document->original_filename }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 border-b border-gray-200">

                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <p class="block text-sm font-medium text-gray-700">Fichier Actuel</p>
                        <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                            <x-lucide-file class="w-5 h-5 text-gray-400" stroke-width="1.5"/>
                            <a href="#" class="text-primary-600 hover:underline" onclick="alert('TODO: Implement secure file download link.'); return false;">
                                {{ $document->original_filename }}
                            </a>
                            <span class="text-gray-400">({{ $document->formatted_size }})</span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Note: Le remplacement de fichier n'est pas supporté. Pour changer le fichier, veuillez supprimer ce document et en importer un nouveau.</p>
                    </div>

                    @include('admin.documents._form', ['document' => $document])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
