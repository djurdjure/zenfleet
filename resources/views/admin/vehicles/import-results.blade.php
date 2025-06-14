<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Résultats de l\'Importation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Résumé --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Succès --}}
                <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-lg font-bold text-green-800">{{ $successCount }} {{ Str::plural('véhicule', $successCount) }} importé(s)</p>
                            <p class="text-sm text-green-700">avec succès.</p>
                        </div>
                    </div>
                </div>
                {{-- Échecs --}}
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg shadow-sm">
                     <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-full p-2">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-lg font-bold text-red-800">{{ count($errorRows) }} {{ Str::plural('ligne', count($errorRows)) }} en erreur</p>
                            <p class="text-sm text-red-700">voir les détails ci-dessous.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Détail des Erreurs --}}
            @if(count($errorRows) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Détail des Lignes en Erreur</h3>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Ligne CSV</th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Données Fournies</th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 uppercase">Problèmes de Validation</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($errorRows as $errorRow)
                                        <tr class="bg-white">
                                            <td class="px-4 py-3 text-sm font-medium text-center text-gray-900">{{ $errorRow['row_number'] }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto"><code>{{ json_encode($errorRow['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-red-600">
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach($errorRow['errors'] as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-8 text-center">
                <a href="{{ route('admin.vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                    Retour à la liste des véhicules
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
