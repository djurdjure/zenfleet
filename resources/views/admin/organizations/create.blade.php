<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer une Nouvelle Organisation</h2></x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('admin.organizations.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="name">Nom de l'Organisation <span class="text-red-500">*</span></label>
                                <x-text-input id="name" name="name" :value="old('name')" required class="mt-1 block w-full" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            {{-- (Ajoutez ici d'autres champs comme address, contact_email, status si nécessaire) --}}
                        </div>
                        <div class="mt-8 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.organizations.index') }}" class="text-sm ...">Annuler</a>
                            <button type="submit" class="... bg-violet-600 ...">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
