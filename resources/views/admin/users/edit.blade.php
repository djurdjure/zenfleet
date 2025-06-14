{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'Utilisateur :') }} <span class="text-violet-700">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h3 class="text-xl font-semibold text-gray-700 mb-6">{{ __('Assigner les Rôles') }}</h3>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Erreurs de validation</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <p class="font-medium text-gray-800">Rôles disponibles :</p>
                            @forelse ($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="roles[]"
                                           id="role_{{ $role->id }}"
                                           value="{{ $role->id }}"
                                           class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                                           @if($user->hasRole($role->name)) checked @endif>
                                    <label for="role_{{ $role->id }}" class="ml-3 block text-sm font-medium text-gray-700">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="text-gray-500">Aucun rôle n'a été trouvé. Veuillez exécuter le seeder des rôles.</p>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>

                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition ease-in-out duration-150">
                                {{ __('Mettre à jour les Rôles') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
