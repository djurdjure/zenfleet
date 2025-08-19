<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modifier : ') . $supplier->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{
                    initTomSelect() {
                        if (this.$refs.supplier_category_id) {
                            new TomSelect(this.$refs.supplier_category_id, { create: false, placeholder: 'Sélectionnez une catégorie...' });
                        }
                    }
                }" x-init="initTomSelect()">

                    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
                        @csrf
                        @method('PUT')
                        <fieldset class="border border-gray-200 p-6 rounded-lg">
                            <legend class="text-lg font-semibold text-gray-800 px-2">Informations sur le Fournisseur</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="name" value="Nom du Fournisseur" required />
                                    <x-text-input id="name" name="name" :value="old('name', $supplier->name)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="contact_name" value="Nom du Contact" />
                                    <x-text-input id="contact_name" name="contact_name" :value="old('contact_name', $supplier->contact_name)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="phone" value="Téléphone" />
                                    <x-text-input id="phone" name="phone" :value="old('phone', $supplier->phone)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input id="email" name="email" type="email" :value="old('email', $supplier->email)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="address" value="Adresse" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('address', $supplier->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="supplier_category_id" value="Catégorie" />
                                    <select x-ref="supplier_category_id" name="supplier_category_id" id="supplier_category_id">
                                        <option value="">Aucune</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('supplier_category_id', $supplier->supplier_category_id) == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('supplier_category_id')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                            <x-primary-button type="submit">
                                Enregistrer les Modifications
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
