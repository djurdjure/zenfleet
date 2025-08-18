<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un Fournisseur</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <form method="POST" action="{{ route('admin.suppliers.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2"><label for="name">Nom du Fournisseur <span class="text-red-500">*</span></label><x-text-input id="name" name="name" :value="old('name')" required class="mt-1 w-full"/></div>
                            <div><label for="contact_name">Nom du Contact</label><x-text-input id="contact_name" name="contact_name" :value="old('contact_name')" class="mt-1 w-full"/></div>
                            <div><label for="phone">Téléphone</label><x-text-input id="phone" name="phone" :value="old('phone')" class="mt-1 w-full"/></div>
                            <div class="md:col-span-2"><label for="email">Email</label><x-text-input id="email" name="email" type="email" :value="old('email')" class="mt-1 w-full"/></div>
                            <div class="md:col-span-2"><label for="address">Adresse</label><textarea id="address" name="address" class="mt-1 w-full ...">{{ old('address') }}</textarea></div>
                            <div><label for="supplier_category_id">Catégorie</label><select id="supplier_category_id" name="supplier_category_id" class="mt-1 w-full ..."><option value="">Aucune</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
                        </div>
                        <div class="mt-8 flex justify-end gap-4">
                            <a href="{{ route('admin.suppliers.index') }}" class="text-sm ...">Annuler</a>
                            <button type="submit" class="... bg-violet-600 ...">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
