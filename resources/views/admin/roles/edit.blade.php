{{-- resources/views/admin/roles/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Rôle :') }} <span class="text-violet-700">{{ $role->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h3 class="text-xl font-semibold text-gray-700 mb-6">{{ __('Assigner les Permissions') }}</h3>

                    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse ($permissions as $permission)
                                <div class="flex items-center p-2 border rounded-md">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           id="permission_{{ $permission->id }}"
                                           value="{{ $permission->id }}"
                                           class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                                           @if($role->hasPermissionTo($permission)) checked @endif>
                                    <label for="permission_{{ $permission->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-gray-500">Aucune permission trouvée. Veuillez exécuter le seeder.</p>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                                {{ __('Mettre à jour les Permissions') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
