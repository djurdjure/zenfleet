@extends('layouts.admin.catalyst')

@section('title', 'Modifier ' . $supplier->company_name)

@section('content')
{{-- ====================================================================
 🏢 FORMULAIRE MODIFICATION FOURNISSEUR - ENTERPRISE GRADE V2.0
 ====================================================================

 Design moderne cohérent:
 ✨ Même structure que create.blade.php
 ✨ Pré-remplissage des données
 ✨ Icônes Iconify Lucide

 @version 2.0-Enterprise
 @since 2025-10-23
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-5xl lg:py-6">

        {{-- HEADER AVEC BREADCRUMB --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                    <x-iconify icon="lucide:home" class="w-4 h-4" />
                    Dashboard
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <a href="{{ route('admin.suppliers.index') }}" class="hover:text-blue-600 transition-colors">
                    Fournisseurs
                </a>
                <x-iconify icon="lucide:chevron-right" class="w-3 h-3 text-gray-400" />
                <span class="font-semibold text-gray-900">{{ $supplier->company_name }}</span>
            </nav>

            {{-- Titre --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <x-iconify icon="lucide:building-2" class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Modifier Fournisseur</h1>
                    <p class="text-gray-600 text-sm">{{ $supplier->company_name }}</p>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- SECTION 1: INFORMATIONS GÉNÉRALES --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                        Informations Générales
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Raison Sociale --}}
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Raison Sociale <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name', $supplier->company_name) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label for="supplier_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Type Fournisseur <span class="text-red-500">*</span>
                            </label>
                            <select id="supplier_type" 
                                    name="supplier_type" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supplier_type') border-red-500 @enderror">
                                <option value="">Sélectionner un type</option>
                                @foreach(App\Models\Supplier::getSupplierTypes() as $key => $label)
                                    <option value="{{ $key }}" {{ old('supplier_type', $supplier->supplier_type) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catégorie --}}
                        @if(isset($categories) && $categories->count() > 0)
                            <div>
                                <label for="supplier_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catégorie
                                </label>
                                <select id="supplier_category_id" 
                                        name="supplier_category_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Aucune catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('supplier_category_id', $supplier->supplier_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Registre Commerce --}}
                        <div>
                            <label for="trade_register" class="block text-sm font-medium text-gray-700 mb-2">
                                Registre du Commerce
                            </label>
                            <input type="text" 
                                   id="trade_register" 
                                   name="trade_register" 
                                   value="{{ old('trade_register', $supplier->trade_register) }}"
                                   placeholder="Ex: 16/00-23A1234567"
                                   pattern="[0-9]{2}/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}"
                                   title="Format algérien: XX/XX-XXAXXXXXXX (ex: 16/00-23A1234567)"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('trade_register') border-red-500 @enderror">
                            @error('trade_register')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <x-iconify icon="lucide:info" class="w-3 h-3 inline" />
                                Format: XX/XX-XXAXXXXXXX (ex: 16/00-23A1234567)
                            </p>
                        </div>

                        {{-- NIF --}}
                        <div>
                            <label for="nif" class="block text-sm font-medium text-gray-700 mb-2">
                                NIF
                            </label>
                            <input type="text" 
                                   id="nif" 
                                   name="nif" 
                                   value="{{ old('nif', $supplier->nif) }}"
                                   placeholder="Ex: 099116000987654"
                                   pattern="[0-9]{15}"
                                   maxlength="15"
                                   title="Le NIF doit contenir exactement 15 chiffres"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nif') border-red-500 @enderror">
                            @error('nif')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <x-iconify icon="lucide:info" class="w-3 h-3 inline" />
                                15 chiffres exactement
                            </p>
                        </div>

                        {{-- NIS --}}
                        <div>
                            <label for="nis" class="block text-sm font-medium text-gray-700 mb-2">
                                NIS
                            </label>
                            <input type="text" 
                                   id="nis" 
                                   name="nis" 
                                   value="{{ old('nis', $supplier->nis) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- AI --}}
                        <div>
                            <label for="ai" class="block text-sm font-medium text-gray-700 mb-2">
                                Article d'Imposition
                            </label>
                            <input type="text" 
                                   id="ai" 
                                   name="ai" 
                                   value="{{ old('ai', $supplier->ai) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: CONTACT PRINCIPAL --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:user" class="w-5 h-5 text-blue-600" />
                        Contact Principal
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Prénom --}}
                        <div>
                            <label for="contact_first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_first_name" 
                                   name="contact_first_name" 
                                   value="{{ old('contact_first_name', $supplier->contact_first_name) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_first_name') border-red-500 @enderror">
                            @error('contact_first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nom --}}
                        <div>
                            <label for="contact_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_last_name" 
                                   name="contact_last_name" 
                                   value="{{ old('contact_last_name', $supplier->contact_last_name) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_last_name') border-red-500 @enderror">
                            @error('contact_last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   value="{{ old('contact_phone', $supplier->contact_phone) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('contact_phone') border-red-500 @enderror">
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   value="{{ old('contact_email', $supplier->contact_email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Téléphone Entreprise --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone Entreprise
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $supplier->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Email Entreprise --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Entreprise
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $supplier->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Site Web --}}
                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Site Web
                            </label>
                            <input type="url" 
                                   id="website" 
                                   name="website" 
                                   value="{{ old('website', $supplier->website) }}"
                                   placeholder="https://"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: LOCALISATION --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:map-pin" class="w-5 h-5 text-blue-600" />
                        Localisation
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Adresse --}}
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse Complète <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3" 
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Wilaya avec Tom Select --}}
                        <x-tom-select
                            name="wilaya"
                            label="Wilaya"
                            :options="array_map(fn($code, $name) => $code . ' - ' . $name, array_keys(App\Models\Supplier::WILAYAS), App\Models\Supplier::WILAYAS)"
                            :selected="old('wilaya', $supplier->wilaya)"
                            placeholder="Rechercher une wilaya..."
                            required
                            :error="$errors->first('wilaya')"
                            helpText="Sélectionnez la wilaya du fournisseur"
                        />

                        {{-- Ville --}}
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ville <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $supplier->city) }}" 
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Commune --}}
                        <div>
                            <label for="commune" class="block text-sm font-medium text-gray-700 mb-2">
                                Commune
                            </label>
                            <input type="text" 
                                   id="commune" 
                                   name="commune" 
                                   value="{{ old('commune', $supplier->commune) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        {{-- Code Postal --}}
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Code Postal
                            </label>
                            <input type="text" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $supplier->postal_code) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4: PARAMÈTRES --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:settings" class="w-5 h-5 text-blue-600" />
                        Paramètres & Notes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        {{-- Checkboxes --}}
                        <div class="flex flex-wrap items-center gap-6">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Actif</span>
                            </label>

                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="is_preferred" 
                                       value="1" 
                                       {{ old('is_preferred', $supplier->is_preferred) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Préféré</span>
                            </label>

                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="is_certified" 
                                       value="1" 
                                       {{ old('is_certified', $supplier->is_certified) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Certifié</span>
                            </label>

                            <label class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       name="blacklisted" 
                                       value="1" 
                                       {{ old('blacklisted', $supplier->blacklisted) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-sm font-medium text-gray-700">Liste noire</span>
                            </label>
                        </div>

                        {{-- Raison blacklist --}}
                        <div x-data="{ blacklisted: {{ old('blacklisted', $supplier->blacklisted) ? 'true' : 'false' }} }">
                            <div x-show="blacklisted">
                                <label for="blacklist_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Raison Liste Noire
                                </label>
                                <textarea id="blacklist_reason" 
                                          name="blacklist_reason" 
                                          rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('blacklist_reason', $supplier->blacklist_reason) }}</textarea>
                            </div>
                        </div>

                        {{-- Scores --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rating (0-5)
                                </label>
                                <input type="number" 
                                       id="rating" 
                                       name="rating" 
                                       value="{{ old('rating', $supplier->rating) }}"
                                       min="0" 
                                       max="5" 
                                       step="0.1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="quality_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    Score Qualité (0-100)
                                </label>
                                <input type="number" 
                                       id="quality_score" 
                                       name="quality_score" 
                                       value="{{ old('quality_score', $supplier->quality_score) }}"
                                       min="0" 
                                       max="100" 
                                       step="0.1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="reliability_score" class="block text-sm font-medium text-gray-700 mb-2">
                                    Score Fiabilité (0-100)
                                </label>
                                <input type="number" 
                                       id="reliability_score" 
                                       name="reliability_score" 
                                       value="{{ old('reliability_score', $supplier->reliability_score) }}"
                                       min="0" 
                                       max="100" 
                                       step="0.1"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes Internes
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="4"
                                      placeholder="Notes internes, commentaires, observations..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $supplier->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <a href="{{ route('admin.suppliers.show', $supplier) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium">
                    <x-iconify icon="lucide:arrow-left" class="w-5 h-5" />
                    Retour
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-semibold shadow-sm">
                    <x-iconify icon="lucide:save" class="w-5 h-5" />
                    Enregistrer les Modifications
                </button>
            </div>

        </form>

    </div>
</section>

<script>
    // Toggle blacklist reason visibility
    document.querySelector('input[name="blacklisted"]')?.addEventListener('change', function(e) {
        const reasonDiv = document.querySelector('[x-show="blacklisted"]');
        if (reasonDiv) {
            if (e.target.checked) {
                reasonDiv.style.display = 'block';
            } else {
                reasonDiv.style.display = 'none';
            }
        }
    });
</script>
@endsection
