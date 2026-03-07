@extends('layouts.admin.catalyst')

@section('title', 'Nouveau Fournisseur')

@section('content')
{{-- ====================================================================
 🏢 FORMULAIRE CRÉATION FOURNISSEUR - ENTERPRISE GRADE WORLD-CLASS
 ====================================================================
 
 Style identique au formulaire véhicules:
 ✨ Composants x-input avec fond gris clair
 ✨ x-slim-select pour wilayas
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

<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-600 mb-1">
                Nouveau Fournisseur
            </h1>
            <p class="text-xs text-gray-600">
                Remplissez les informations pour enregistrer un nouveau fournisseur
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

        {{-- FORMULAIRE AVEC ALPINE.JS VALIDATION --}}
        <div x-data="supplierFormValidation()" x-init="init()">
            <form method="POST" action="{{ route('admin.suppliers.store') }}" @submit="onSubmit" class="space-y-8">
                @csrf

                <x-form-section
                    title="Informations Générales"
                    icon="heroicons:identification"
                    subtitle="Données d'identité et légales du fournisseur">
                    <x-field-group :columns="2">
                        <x-input
                            name="company_name"
                            label="Raison Sociale"
                            icon="building-office"
                            placeholder="Ex: SARL Transport Alger"
                            :value="old('company_name')"
                            required
                            :error="$errors->first('company_name')"
                            helpText="Nom officiel de l'entreprise"
                            @blur="validateField('company_name', $event.target.value)" />

                        <x-slim-select
                            name="supplier_type"
                            label="Type de Fournisseur"
                            :options="App\Models\Supplier::getSupplierTypes()"
                            :selected="old('supplier_type')"
                            placeholder="Sélectionnez un type..."
                            required
                            :error="$errors->first('supplier_type')"
                            @change="validateField('supplier_type', $event.target.value)" />
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">
                        @if(isset($categories) && $categories->count() > 0)
                        <x-slim-select
                            name="supplier_category_id"
                            label="Catégorie"
                            :options="$categories->pluck('name', 'id')->toArray()"
                            :selected="old('supplier_category_id')"
                            placeholder="Sélectionnez une catégorie..."
                            :error="$errors->first('supplier_category_id')" />
                        @endif

                        <x-input
                            name="trade_register"
                            label="Registre du Commerce"
                            icon="identification"
                            placeholder="Ex: 16/00-23A1234567"
                            :value="old('trade_register')"
                            :error="$errors->first('trade_register')"
                            helpText="Format: XX/XX-XXAXXXXXXX"
                            pattern="[0-9]{2}/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}" />

                        <x-input
                            name="nif"
                            label="NIF"
                            icon="finger-print"
                            placeholder="Ex: 099116000987654"
                            :value="old('nif')"
                            :error="$errors->first('nif')"
                            helpText="15 chiffres"
                            maxlength="15"
                            pattern="[0-9]{15}" />

                        <x-input
                            name="nis"
                            label="NIS"
                            icon="document-text"
                            placeholder="Numéro NIS"
                            :value="old('nis')"
                            :error="$errors->first('nis')" />

                        <x-input
                            name="ai"
                            label="Article d'Imposition"
                            icon="document-check"
                            placeholder="Article d'imposition"
                            :value="old('ai')"
                            :error="$errors->first('ai')" />
                    </x-field-group>
                </x-form-section>

                <x-form-section
                    title="Contact Principal"
                    icon="heroicons:user"
                    subtitle="Coordonnées et informations de contact">
                    <x-field-group :columns="2">
                        <x-input
                            name="contact_first_name"
                            label="Prénom"
                            icon="user"
                            placeholder="Prénom du contact"
                            :value="old('contact_first_name')"
                            required
                            :error="$errors->first('contact_first_name')"
                            @blur="validateField('contact_first_name', $event.target.value)" />

                        <x-input
                            name="contact_last_name"
                            label="Nom"
                            icon="user"
                            placeholder="Nom du contact"
                            :value="old('contact_last_name')"
                            required
                            :error="$errors->first('contact_last_name')"
                            @blur="validateField('contact_last_name', $event.target.value)" />
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">
                        <x-input
                            name="contact_phone"
                            type="tel"
                            label="Téléphone"
                            icon="phone"
                            placeholder="Ex: 0561234567"
                            :value="old('contact_phone')"
                            required
                            :error="$errors->first('contact_phone')"
                            @blur="validateField('contact_phone', $event.target.value)" />

                        <x-input
                            type="email"
                            name="contact_email"
                            label="Email"
                            icon="envelope"
                            placeholder="contact@entreprise.dz"
                            :value="old('contact_email')"
                            :error="$errors->first('contact_email')" />

                        <x-input
                            type="tel"
                            name="phone"
                            label="Téléphone Entreprise"
                            icon="phone"
                            placeholder="Ex: 021234567"
                            :value="old('phone')"
                            :error="$errors->first('phone')" />

                        <x-input
                            type="email"
                            name="email"
                            label="Email Entreprise"
                            icon="envelope"
                            placeholder="info@entreprise.dz"
                            :value="old('email')"
                            :error="$errors->first('email')" />

                        <x-input
                            type="url"
                            name="website"
                            label="Site Web"
                            icon="globe-alt"
                            placeholder="https://www.entreprise.dz"
                            :value="old('website')"
                            :error="$errors->first('website')"
                            class="md:col-span-2" />
                    </x-field-group>
                </x-form-section>

                <x-form-section
                    title="Localisation"
                    icon="heroicons:map-pin"
                    subtitle="Adresse et informations géographiques">
                    <x-field-group :columns="1">
                        <x-textarea
                            name="address"
                            label="Adresse complète"
                            rows="3"
                            placeholder="Adresse complète du fournisseur"
                            :value="old('address')"
                            required
                            :error="$errors->first('address')"
                            x-bind:class="(fieldErrors && fieldErrors['address'] && touchedFields && touchedFields['address']) ? '!border-red-500 !bg-red-50' : ''"
                            @blur="validateField('address', $event.target.value)" />
                        <p x-show="fieldErrors && fieldErrors['address'] && touchedFields && touchedFields['address']"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-1"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="mt-2 text-sm text-red-600 flex items-start font-medium"
                            style="display: none;">
                            <x-iconify icon="lucide:circle-alert" class="w-4 h-4 mr-1.5 mt-0.5 flex-shrink-0" />
                            <span>Ce champ est obligatoire</span>
                        </p>
                    </x-field-group>

                    <x-field-group :columns="2" class="mt-6">
                        <x-slim-select
                            name="wilaya"
                            label="Wilaya"
                            :options="App\Models\Supplier::WILAYAS"
                            :selected="old('wilaya')"
                            placeholder="Rechercher une wilaya..."
                            required
                            :error="$errors->first('wilaya')"
                            @change="validateField('wilaya', $event.target.value)" />

                        <x-input
                            name="city"
                            label="Ville"
                            icon="building-office-2"
                            placeholder="Ville"
                            :value="old('city')"
                            required
                            :error="$errors->first('city')"
                            @blur="validateField('city', $event.target.value)" />

                        <x-input
                            name="commune"
                            label="Commune"
                            icon="map"
                            placeholder="Commune"
                            :value="old('commune')"
                            :error="$errors->first('commune')" />

                        <x-input
                            name="postal_code"
                            label="Code Postal"
                            icon="map-pin"
                            placeholder="16000"
                            :value="old('postal_code')"
                            :error="$errors->first('postal_code')" />
                    </x-field-group>
                </x-form-section>

                <x-form-section
                    title="Paramètres & Notes"
                    icon="heroicons:cog-6-tooth"
                    subtitle="Critères qualité et informations complémentaires">
                    <x-field-group :columns="3">
                        <x-input
                            type="number"
                            name="rating"
                            label="Rating (0-5)"
                            icon="star"
                            placeholder="5.0"
                            :value="old('rating', 5)"
                            :error="$errors->first('rating')"
                            min="0"
                            max="5"
                            step="0.1" />

                        <x-input
                            type="number"
                            name="quality_score"
                            label="Score Qualité (%)"
                            icon="chart-bar"
                            placeholder="100"
                            :value="old('quality_score')"
                            :error="$errors->first('quality_score')"
                            min="0"
                            max="100"
                            step="0.1" />

                        <x-input
                            type="number"
                            name="reliability_score"
                            label="Score Fiabilité (%)"
                            icon="shield-check"
                            placeholder="100"
                            :value="old('reliability_score')"
                            :error="$errors->first('reliability_score')"
                            min="0"
                            max="100"
                            step="0.1" />
                    </x-field-group>

                    <div class="mt-6 flex flex-wrap items-center gap-6 rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox"
                                name="is_active"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-[#0c90ee] bg-gray-100 border-gray-300 rounded focus:ring-[#0c90ee]/30 focus:ring-2">
                            Actif
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox"
                                name="is_preferred"
                                value="1"
                                {{ old('is_preferred') ? 'checked' : '' }}
                                class="w-4 h-4 text-[#0c90ee] bg-gray-100 border-gray-300 rounded focus:ring-[#0c90ee]/30 focus:ring-2">
                            Préféré
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox"
                                name="is_certified"
                                value="1"
                                {{ old('is_certified') ? 'checked' : '' }}
                                class="w-4 h-4 text-[#0c90ee] bg-gray-100 border-gray-300 rounded focus:ring-[#0c90ee]/30 focus:ring-2">
                            Certifié
                        </label>
                    </div>

                    <div class="mt-6">
                        <x-textarea
                            name="notes"
                            label="Notes Internes"
                            rows="4"
                            placeholder="Notes internes, commentaires, observations..."
                            :value="old('notes')" />
                    </div>
                </x-form-section>

                <div class="relative pl-14">
                    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between gap-3 px-6 py-4">
                            <a href="{{ route('admin.suppliers.index') }}"
                                class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all">
                                <x-iconify icon="heroicons:arrow-left" class="w-4 h-4 mr-2" />
                                Annuler
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium text-white bg-[#0c90ee] border border-transparent rounded-lg hover:bg-[#0b82d6] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/25 transition-all">
                                <x-iconify icon="heroicons:check" class="w-4 h-4 mr-2" />
                                Créer le Fournisseur
                            </button>
                        </div>
                    </section>
                </div>
            </form>
        </div>

    </div>
</section>

{{-- ================================================================
    ALPINE.JS VALIDATION SYSTEM - ENTERPRISE GRADE
    ================================================================
    Système de validation en temps réel identique au formulaire véhicules
    - Validation par champ avec état persistant
    - Indicateurs visuels (bordures rouges, messages d'erreur)
    - Messages d'erreur contextuels sous les champs
    - Validation côté client synchronisée avec serveur
================================================================ --}}
<script>
    function supplierFormValidation() {
        return {
            fieldErrors: {},
            touchedFields: {},

            init() {
                // Initialiser avec les erreurs serveur si présentes
                @if($errors -> any())
                @foreach($errors -> keys() as $field)
                this.fieldErrors['{{ $field }}'] = true;
                this.touchedFields['{{ $field }}'] = true;
                @endforeach
                @endif
            },

            validateField(fieldName, value) {
                // Marquer le champ comme touché
                this.touchedFields[fieldName] = true;

                // Règles de validation
                const rules = {
                    'company_name': (v) => v && v.trim().length > 0,
                    'supplier_type': (v) => v && v.length > 0,
                    'contact_first_name': (v) => v && v.trim().length > 0,
                    'contact_last_name': (v) => v && v.trim().length > 0,
                    'contact_phone': (v) => v && v.trim().length > 0,
                    'address': (v) => v && v.trim().length > 0,
                    'wilaya': (v) => v && v.length > 0,
                    'city': (v) => v && v.trim().length > 0,
                    'trade_register': (v) => !v || /^[0-9]{2}\/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}$/.test(v),
                    'nif': (v) => !v || /^[0-9]{15}$/.test(v),
                };

                const isValid = rules[fieldName] ? rules[fieldName](value) : true;

                if (!isValid) {
                    this.fieldErrors[fieldName] = true;

                    // SlimSelect validation logic (handled by component or livewire usually)
                } else {
                    this.clearFieldError(fieldName);
                }

                return isValid;
            },

            clearFieldError(fieldName) {
                delete this.fieldErrors[fieldName];

                const input = document.querySelector(`[name="${fieldName}"]`);
                if (input) {

                }
            },

            onSubmit(e) {
                // Valider tous les champs requis
                const requiredFields = ['company_name', 'supplier_type', 'contact_first_name', 'contact_last_name', 'contact_phone', 'address', 'wilaya', 'city'];
                let allValid = true;

                requiredFields.forEach(fieldName => {
                    const input = document.querySelector(`[name="${fieldName}"]`);
                    if (input) {
                        const value = input.value;
                        const isValid = this.validateField(fieldName, value);
                        if (!isValid) {
                            allValid = false;
                        }
                    }
                });

                if (!allValid) {
                    e.preventDefault();
                    alert('Veuillez corriger les erreurs avant de soumettre le formulaire');
                    return false;
                }

                return true;
            }
        };
    }
</script>

<style>
    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-4px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(4px);
        }
    }

    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }

</style>

@endsection
