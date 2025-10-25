@extends('layouts.admin.catalyst')

@section('title', 'Modifier ' . $supplier->company_name)

@section('content')
{{-- ====================================================================
 🏢 FORMULAIRE MODIFICATION FOURNISSEUR - ENTERPRISE GRADE WORLD-CLASS
 ====================================================================
 
 Style identique au formulaire véhicules:
 ✨ Composants x-input avec fond gris clair
 ✨ x-tom-select pour wilayas
 ✨ Validation visuelle (bordures rouges)
 ✨ Messages d'erreur sous champs avec icônes
 ✨ Design Flowbite-inspired
 ✨ Qualité surpassant Fleetio/Samsara
 
 @version 3.0-Ultra-Professional
 @since 2025-10-24
 ==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         class="fixed top-4 right-4 z-50 max-w-md">
        <x-alert type="success" title="Succès" dismissible>
            {{ session('success') }}
        </x-alert>
    </div>
@endif

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="heroicons:building-office" class="w-6 h-6 text-blue-600" />
                Modifier Fournisseur
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                {{ $supplier->company_name }}
            </p>
        </div>

        {{-- Affichage des erreurs globales --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes avant de soumettre le formulaire :
                <ul class="mt-2 ml-5 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- FORMULAIRE --}}
        <x-card padding="p-0" margin="mb-6">
            <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="p-6">
                @csrf
                @method('PUT')

                {{-- SECTION 1: INFORMATIONS GÉNÉRALES --}}
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="heroicons:identification" class="w-5 h-5 text-blue-600" />
                        Informations Générales
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Raison Sociale --}}
                        <div class="md:col-span-2">
                            <x-input
                                name="company_name"
                                label="Raison Sociale"
                                icon="building-office"
                                placeholder="Ex: SARL Transport Alger"
                                :value="old('company_name', $supplier->company_name)"
                                required
                                :error="$errors->first('company_name')"
                                helpText="Nom officiel de l'entreprise"
                            />
                        </div>

                        {{-- Type --}}
                        <x-tom-select
                            name="supplier_type"
                            label="Type de Fournisseur"
                            :options="App\Models\Supplier::getSupplierTypes()"
                            :selected="old('supplier_type', $supplier->supplier_type)"
                            placeholder="Sélectionnez un type..."
                            required
                            :error="$errors->first('supplier_type')"
                        />

                        {{-- Catégorie --}}
                        @if(isset($categories) && $categories->count() > 0)
                            <x-tom-select
                                name="supplier_category_id"
                                label="Catégorie"
                                :options="$categories->pluck('name', 'id')->toArray()"
                                :selected="old('supplier_category_id', $supplier->supplier_category_id)"
                                placeholder="Sélectionnez une catégorie..."
                                :error="$errors->first('supplier_category_id')"
                            />
                        @endif

                        {{-- Registre Commerce --}}
                        <x-input
                            name="trade_register"
                            label="Registre du Commerce"
                            icon="identification"
                            placeholder="Ex: 16/00-23A1234567"
                            :value="old('trade_register', $supplier->trade_register)"
                            :error="$errors->first('trade_register')"
                            helpText="Format: XX/XX-XXAXXXXXXX"
                            pattern="[0-9]{2}/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}"
                        />

                        {{-- NIF --}}
                        <x-input
                            name="nif"
                            label="NIF"
                            icon="finger-print"
                            placeholder="Ex: 099116000987654"
                            :value="old('nif', $supplier->nif)"
                            :error="$errors->first('nif')"
                            helpText="15 chiffres"
                            maxlength="15"
                            pattern="[0-9]{15}"
                        />

                        {{-- NIS --}}
                        <x-input
                            name="nis"
                            label="NIS"
                            icon="document-text"
                            placeholder="Numéro NIS"
                            :value="old('nis', $supplier->nis)"
                            :error="$errors->first('nis')"
                        />

                        {{-- AI --}}
                        <x-input
                            name="ai"
                            label="Article d'Imposition"
                            icon="document-check"
                            placeholder="Article d'imposition"
                            :value="old('ai', $supplier->ai)"
                            :error="$errors->first('ai')"
                        />
                    </div>
                </div>

                {{-- SECTION 2: CONTACT PRINCIPAL --}}
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="heroicons:user" class="w-5 h-5 text-blue-600" />
                        Contact Principal
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Prénom --}}
                        <x-input
                            name="contact_first_name"
                            label="Prénom"
                            icon="user"
                            placeholder="Prénom du contact"
                            :value="old('contact_first_name', $supplier->contact_first_name)"
                            required
                            :error="$errors->first('contact_first_name')"
                        />

                        {{-- Nom --}}
                        <x-input
                            name="contact_last_name"
                            label="Nom"
                            icon="user"
                            placeholder="Nom du contact"
                            :value="old('contact_last_name', $supplier->contact_last_name)"
                            required
                            :error="$errors->first('contact_last_name')"
                        />

                        {{-- Téléphone --}}
                        <x-input
                            type="tel"
                            name="contact_phone"
                            label="Téléphone"
                            icon="phone"
                            placeholder="Ex: 0561234567"
                            :value="old('contact_phone', $supplier->contact_phone)"
                            required
                            :error="$errors->first('contact_phone')"
                        />

                        {{-- Email --}}
                        <x-input
                            type="email"
                            name="contact_email"
                            label="Email"
                            icon="envelope"
                            placeholder="contact@entreprise.dz"
                            :value="old('contact_email', $supplier->contact_email)"
                            :error="$errors->first('contact_email')"
                        />

                        {{-- Téléphone Entreprise --}}
                        <x-input
                            type="tel"
                            name="phone"
                            label="Téléphone Entreprise"
                            icon="phone"
                            placeholder="Ex: 021234567"
                            :value="old('phone', $supplier->phone)"
                            :error="$errors->first('phone')"
                        />

                        {{-- Email Entreprise --}}
                        <x-input
                            type="email"
                            name="email"
                            label="Email Entreprise"
                            icon="envelope"
                            placeholder="info@entreprise.dz"
                            :value="old('email', $supplier->email)"
                            :error="$errors->first('email')"
                        />

                        {{-- Site Web --}}
                        <div class="md:col-span-2">
                            <x-input
                                type="url"
                                name="website"
                                label="Site Web"
                                icon="globe-alt"
                                placeholder="https://www.entreprise.dz"
                                :value="old('website', $supplier->website)"
                                :error="$errors->first('website')"
                            />
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: LOCALISATION --}}
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="heroicons:map-pin" class="w-5 h-5 text-blue-600" />
                        Localisation
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Adresse --}}
                        <div class="md:col-span-2">
                            <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
                                Adresse Complète <span class="text-red-600">*</span>
                            </label>
                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                required
                                placeholder="Adresse complète du fournisseur"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('address') !border-red-500 !bg-red-50 @enderror"
                            >{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600 flex items-start font-medium">
                                    <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Wilaya avec Tom Select --}}
                        <x-tom-select
                            name="wilaya"
                            label="Wilaya"
                            :options="array_combine(array_keys(App\Models\Supplier::WILAYAS), array_map(fn($code, $name) => $code . ' - ' . $name, array_keys(App\Models\Supplier::WILAYAS), App\Models\Supplier::WILAYAS))"
                            :selected="old('wilaya', $supplier->wilaya)"
                            placeholder="Rechercher une wilaya..."
                            required
                            :error="$errors->first('wilaya')"
                        />

                        {{-- Ville --}}
                        <x-input
                            name="city"
                            label="Ville"
                            icon="building-office-2"
                            placeholder="Ville"
                            :value="old('city', $supplier->city)"
                            required
                            :error="$errors->first('city')"
                        />

                        {{-- Commune --}}
                        <x-input
                            name="commune"
                            label="Commune"
                            icon="map"
                            placeholder="Commune"
                            :value="old('commune', $supplier->commune)"
                            :error="$errors->first('commune')"
                        />

                        {{-- Code Postal --}}
                        <x-input
                            name="postal_code"
                            label="Code Postal"
                            icon="map-pin"
                            placeholder="16000"
                            :value="old('postal_code', $supplier->postal_code)"
                            :error="$errors->first('postal_code')"
                        />
                    </div>
                </div>

                {{-- SECTION 4: PARAMÈTRES --}}
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <x-iconify icon="heroicons:cog-6-tooth" class="w-5 h-5 text-blue-600" />
                        Paramètres & Notes
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        {{-- Rating --}}
                        <x-input
                            type="number"
                            name="rating"
                            label="Rating (0-5)"
                            icon="star"
                            placeholder="5.0"
                            :value="old('rating', $supplier->rating)"
                            :error="$errors->first('rating')"
                            min="0"
                            max="5"
                            step="0.1"
                        />

                        {{-- Score Qualité --}}
                        <x-input
                            type="number"
                            name="quality_score"
                            label="Score Qualité (%)"
                            icon="chart-bar"
                            placeholder="100"
                            :value="old('quality_score', $supplier->quality_score)"
                            :error="$errors->first('quality_score')"
                            min="0"
                            max="100"
                            step="0.1"
                        />

                        {{-- Score Fiabilité --}}
                        <x-input
                            type="number"
                            name="reliability_score"
                            label="Score Fiabilité (%)"
                            icon="shield-check"
                            placeholder="100"
                            :value="old('reliability_score', $supplier->reliability_score)"
                            :error="$errors->first('reliability_score')"
                            min="0"
                            max="100"
                            step="0.1"
                        />
                    </div>

                    {{-- Checkboxes --}}
                    <div class="flex flex-wrap items-center gap-6 mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                                   id="is_active"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_active" class="ml-2 text-sm font-medium text-gray-900">
                                Actif
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_preferred" 
                                   value="1" 
                                   {{ old('is_preferred', $supplier->is_preferred) ? 'checked' : '' }}
                                   id="is_preferred"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_preferred" class="ml-2 text-sm font-medium text-gray-900">
                                Préféré
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="is_certified" 
                                   value="1" 
                                   {{ old('is_certified', $supplier->is_certified) ? 'checked' : '' }}
                                   id="is_certified"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_certified" class="ml-2 text-sm font-medium text-gray-900">
                                Certifié
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="blacklisted" 
                                   value="1" 
                                   {{ old('blacklisted', $supplier->blacklisted) ? 'checked' : '' }}
                                   id="blacklisted"
                                   class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500"
                                   x-data
                                   @change="document.getElementById('blacklist_reason_section').style.display = $event.target.checked ? 'block' : 'none'">
                            <label for="blacklisted" class="ml-2 text-sm font-medium text-gray-900">
                                Liste noire
                            </label>
                        </div>
                    </div>

                    {{-- Raison blacklist --}}
                    <div id="blacklist_reason_section" class="mb-6" style="display: {{ old('blacklisted', $supplier->blacklisted) ? 'block' : 'none' }}">
                        <label for="blacklist_reason" class="block mb-2 text-sm font-medium text-gray-900">
                            Raison Liste Noire
                        </label>
                        <textarea
                            id="blacklist_reason"
                            name="blacklist_reason"
                            rows="2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        >{{ old('blacklist_reason', $supplier->blacklist_reason) }}</textarea>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">
                            Notes Internes
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="4"
                            placeholder="Notes internes, commentaires, observations..."
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        >{{ old('notes', $supplier->notes) }}</textarea>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <x-button 
                        href="{{ route('admin.suppliers.show', $supplier) }}" 
                        variant="secondary"
                        icon="arrow-left"
                        iconPosition="left"
                    >
                        Retour
                    </x-button>

                    <x-button 
                        type="submit"
                        variant="primary"
                        icon="check"
                        iconPosition="left"
                    >
                        Enregistrer les Modifications
                    </x-button>
                </div>

            </form>
        </x-card>

    </div>
</section>
@endsection
