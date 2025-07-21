<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'Utilisateur :') }} <span class="text-violet-700">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs ci-dessous.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Prénom --}}
                            <div>
                                <label for="first_name" class="block font-medium text-sm text-gray-700">Prénom <span class="text-red-500">*</span></label>
                                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $user->first_name)" required autofocus />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>

                            {{-- Nom --}}
                            <div>
                                <label for="last_name" class="block font-medium text-sm text-gray-700">Nom <span class="text-red-500">*</span></label>
                                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $user->last_name)" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label for="email" class="block font-medium text-sm text-gray-700">Email <span class="text-red-500">*</span></label>
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                             {{-- Organisation --}}
                            <div class="md:col-span-2">
                                <label for="organization_id" class="block font-medium text-sm text-gray-700">Organisation <span class="text-red-500">*</span></label>
                                <select name="organization_id" id="organization_id" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
                            </div>

                            {{-- Mot de passe --}}
                            <div class="border-t pt-6 md:col-span-2">
                                <p class="text-sm text-gray-500">Laissez les champs de mot de passe vides si vous ne souhaitez pas le modifier.</p>
                            </div>
                            <div>
                                <label for="password" class="block font-medium text-sm text-gray-700">Nouveau Mot de passe</label>
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmation du Mot de passe</label>
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        {{-- Rôles --}}
                        <div class="mt-6 border-t pt-6">
                            <label class="block font-medium text-sm text-gray-700">Rôles</label>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}" class="h-4 w-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                                            @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())))>
                                        <label for="role_{{ $role->id }}" class="ml-3 block text-sm font-medium text-gray-700">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                Mettre à Jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>