<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Ajouter un Fournisseur') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="supplierForm()">
                    <!-- MODAL POUR AJOUTER UNE CATÉGORIE -->
                    <div x-show="showCategoryModal" 
                         class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50" 
                         @click.away="showCategoryModal = false"
                         style="display: none;">

                        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md" @click.stop>
                            {{-- Le reste du contenu de votre modale est déjà parfait --}}
                            <h4 class="text-lg font-semibold mb-4">Ajouter une Catégorie</h4>
                            <form @submit.prevent="submitCategory">
                                <div>
                                    <x-input-label for="new_category_name" value="Nom de la catégorie" required />
                                    <x-text-input id="new_category_name" x-model="newCategoryName" class="mt-1 block w-full" />
                                    <span x-show="categoryError" x-text="categoryError" class="text-sm text-red-600 mt-2"></span>
                                </div>
                                <div class="mt-6 flex justify-end gap-4">
                                    <x-secondary-button @click.prevent="showCategoryModal = false">Annuler</x-secondary-button>
                                    <x-primary-button type="submit">Enregistrer</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.suppliers.store') }}">
                        @csrf
                        <fieldset class="border border-gray-200 p-6 rounded-lg">
                            <legend class="text-lg font-semibold text-gray-800 px-2">Informations sur le Fournisseur</legend>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="name" value="Nom du Fournisseur" required />
                                    <x-text-input id="name" name="name" :value="old('name')" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="contact_name" value="Nom du Contact" />
                                    <x-text-input id="contact_name" name="contact_name" :value="old('contact_name')" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="phone" value="Téléphone" />
                                    <x-text-input id="phone" name="phone" :value="old('phone')" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="email" value="Email" />
                                    <x-text-input id="email" name="email" type="email" :value="old('email')" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="address" value="Adresse" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="supplier_category_id" value="Catégorie" />
                                    <div class="flex items-center gap-2 mt-1">
                                        <select x-ref="categorySelect" name="supplier_category_id" id="supplier_category_id" class="flex-grow">
                                            <option value="">Aucune</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('supplier_category_id')==$category->id)>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-primary-button type="button" @click.prevent="showCategoryModal = true" title="Ajouter une catégorie">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                        </x-primary-button>
                                    </div>
                                    <x-input-error :messages="$errors->get('supplier_category_id')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.suppliers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                            <x-primary-button type="submit">
                                Enregistrer le Fournisseur
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                @push('scripts')
                <script>
                    function supplierForm() {
                        return {
                            showCategoryModal: false,
                            newCategoryName: '',
                            categoryError: '',
                            tomSelect: null,

                            init() {
                                this.tomSelect = new TomSelect(this.$refs.categorySelect, {
                                    create: false,
                                    placeholder: 'Sélectionnez une catégorie...'
                                });

                                // Pour la synchronisation si la valeur old() est présente
                                const selectedValue = this.$refs.categorySelect.value;
                                if (selectedValue) {
                                    this.tomSelect.setValue(selectedValue);
                                }
                            },

                            async submitCategory() {
                                this.categoryError = '';
                                if (!this.newCategoryName.trim()) {
                                    this.categoryError = "Le nom de la catégorie ne peut pas être vide.";
                                    return;
                                }

                                try {
                                    const response = await fetch('{{ route('admin.supplier-categories.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            name: this.newCategoryName
                                        })
                                    });

                                    const result = await response.json();

                                    if (response.ok && result.success) {
                                        const newCategory = result.category;
                                        this.tomSelect.addOption({
                                            value: newCategory.id,
                                            text: newCategory.name
                                        });
                                        this.tomSelect.setValue(newCategory.id); // Sélectionne la nouvelle catégorie

                                        this.showCategoryModal = false;
                                        this.newCategoryName = '';

                                        // Afficher un toast/notification de succès
                                        window.dispatchEvent(new CustomEvent('toast-message', {
                                            detail: {
                                                message: 'Catégorie ajoutée avec succès !',
                                                type: 'success'
                                            }
                                        }));

                                    } else {
                                        this.categoryError = result.message || 'Une erreur est survenue.';
                                        if (result.errors && result.errors.name) {
                                            this.categoryError = result.errors.name[0];
                                        }
                                    }
                                } catch (error) {
                                    console.error('Erreur:', error);
                                    this.categoryError = 'Une erreur réseau est survenue.';
                                }
                            }
                        }
                    }
                </script>
                @endpush
            </div>
        </div>
    </div>
</x-app-layout>