<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Fournisseurs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">Tous les Fournisseurs</h3>
                        @can('create suppliers')
                            <a href="{{ route('admin.suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 ...">Ajouter</a>
                        @endcan
                    </div>

                    {{-- Tableau des fournisseurs --}}
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left ...">Nom</th>
                                    <th class="px-6 py-3 text-left ...">Catégorie</th>
                                    <th class="px-6 py-3 text-left ...">Contact</th>
                                    <th class="px-6 py-3 text-right ...">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($suppliers as $supplier)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 ...">{{ $supplier->name }}</td>
                                        <td class="px-6 py-4 ...">{{ $supplier->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 ...">{{ $supplier->contact_name }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-4 text-center ...">Aucun fournisseur trouvé.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
