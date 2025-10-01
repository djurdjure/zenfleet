@extends('layouts.admin')

@section('title', 'Nouveau Fournisseur Enterprise')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouveau Fournisseur Enterprise</h1>
            <p class="mt-2 text-sm text-gray-700">Créez un nouveau fournisseur avec conformité réglementaire algérienne</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.suppliers-enterprise.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-lucide-arrow-left class="h-4 w-4 mr-2" />
                Retour à la liste
            </a>
        </div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('admin.suppliers-enterprise.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Informations générales --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations générales</h3>
                <p class="mt-1 text-sm text-gray-500">Informations de base sur l'entreprise</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Nom de l'entreprise --}}
                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                        <input type="text" name="company_name" id="company_name" required
                               value="{{ old('company_name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('company_name') border-red-300 @enderror">
                        @error('company_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type d'entreprise --}}
                    <div>
                        <label for="company_type" class="block text-sm font-medium text-gray-700">Type d'entreprise *</label>
                        <select name="company_type" id="company_type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('company_type') border-red-300 @enderror">
                            <option value="">Sélectionner un type</option>
                            <option value="eurl" {{ old('company_type') === 'eurl' ? 'selected' : '' }}>EURL</option>
                            <option value="sarl" {{ old('company_type') === 'sarl' ? 'selected' : '' }}>SARL</option>
                            <option value="spa" {{ old('company_type') === 'spa' ? 'selected' : '' }}>SPA</option>
                            <option value="sts" {{ old('company_type') === 'sts' ? 'selected' : '' }}>STS</option>
                            <option value="snc" {{ old('company_type') === 'snc' ? 'selected' : '' }}>SNC</option>
                            <option value="entreprise_individuelle" {{ old('company_type') === 'entreprise_individuelle' ? 'selected' : '' }}>Entreprise Individuelle</option>
                        </select>
                        @error('company_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Représentant légal --}}
                    <div>
                        <label for="legal_representative" class="block text-sm font-medium text-gray-700">Représentant légal *</label>
                        <input type="text" name="legal_representative" id="legal_representative" required
                               value="{{ old('legal_representative') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('legal_representative') border-red-300 @enderror">
                        @error('legal_representative')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Conformité réglementaire algérienne --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Conformité Réglementaire Algérienne</h3>
                <p class="mt-1 text-sm text-gray-500">Informations obligatoires selon la réglementation algérienne</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- NIF --}}
                    <div>
                        <label for="nif" class="block text-sm font-medium text-gray-700">NIF (15 chiffres) *</label>
                        <input type="text" name="nif" id="nif" required maxlength="15" pattern="[0-9]{15}"
                               value="{{ old('nif') }}" placeholder="123456789012345"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('nif') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Numéro d'Identification Fiscale (15 chiffres)</p>
                        @error('nif')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIS --}}
                    <div>
                        <label for="nis" class="block text-sm font-medium text-gray-700">NIS (15 chiffres) *</label>
                        <input type="text" name="nis" id="nis" required maxlength="15" pattern="[0-9]{15}"
                               value="{{ old('nis') }}" placeholder="098765432109876"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('nis') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Numéro d'Identification Statistique (15 chiffres)</p>
                        @error('nis')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Registre de commerce --}}
                    <div>
                        <label for="trade_register" class="block text-sm font-medium text-gray-700">Registre de Commerce *</label>
                        <input type="text" name="trade_register" id="trade_register" required
                               value="{{ old('trade_register') }}" placeholder="16/24-1234567"
                               pattern="[0-9]{2}/[0-9]{2}-[0-9]{7}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('trade_register') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: XX/XX-XXXXXXX (wilaya/année-numéro)</p>
                        @error('trade_register')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lieu du RC --}}
                    <div>
                        <label for="trade_register_place" class="block text-sm font-medium text-gray-700">Lieu du Registre de Commerce</label>
                        <input type="text" name="trade_register_place" id="trade_register_place"
                               value="{{ old('trade_register_place') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Acte constitutif --}}
                    <div>
                        <label for="article_of_association" class="block text-sm font-medium text-gray-700">Acte constitutif</label>
                        <input type="text" name="article_of_association" id="article_of_association"
                               value="{{ old('article_of_association') }}"
                               placeholder="Acte notarié n° 2024/156"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- RIB --}}
                    <div>
                        <label for="rib" class="block text-sm font-medium text-gray-700">RIB (20 chiffres) *</label>
                        <input type="text" name="rib" id="rib" required maxlength="20" pattern="[0-9]{20}"
                               value="{{ old('rib') }}" placeholder="12345678901234567890"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('rib') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Relevé d'Identité Bancaire (20 chiffres)</p>
                        @error('rib')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Banque --}}
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700">Banque</label>
                        <input type="text" name="bank_name" id="bank_name"
                               value="{{ old('bank_name') }}" placeholder="BNA Alger Centre"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- Coordonnées --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Coordonnées</h3>
                <p class="mt-1 text-sm text-gray-500">Informations de contact et adresse</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" required
                               value="{{ old('email') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Site web --}}
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Site web</label>
                        <input type="url" name="website" id="website"
                               value="{{ old('website') }}"
                               placeholder="https://example.dz"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="phone" id="phone"
                               value="{{ old('phone') }}" placeholder="+213-21-123456"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Mobile --}}
                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700">Mobile</label>
                        <input type="tel" name="mobile" id="mobile"
                               value="{{ old('mobile') }}" placeholder="+213-555-123456"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                {{-- Adresse --}}
                <div>
                    <label for="address_line_1" class="block text-sm font-medium text-gray-700">Adresse ligne 1 *</label>
                    <input type="text" name="address_line_1" id="address_line_1" required
                           value="{{ old('address_line_1') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('address_line_1') border-red-300 @enderror">
                    @error('address_line_1')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address_line_2" class="block text-sm font-medium text-gray-700">Adresse ligne 2</label>
                    <input type="text" name="address_line_2" id="address_line_2"
                           value="{{ old('address_line_2') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Wilaya --}}
                    <div>
                        <label for="wilaya" class="block text-sm font-medium text-gray-700">Wilaya *</label>
                        <select name="wilaya" id="wilaya" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('wilaya') border-red-300 @enderror">
                            <option value="">Sélectionner une wilaya</option>
                            @foreach($wilayas as $wilaya)
                                <option value="{{ $wilaya }}" {{ old('wilaya') === $wilaya ? 'selected' : '' }}>{{ $wilaya }}</option>
                            @endforeach
                        </select>
                        @error('wilaya')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Code postal --}}
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">Code postal</label>
                        <input type="text" name="postal_code" id="postal_code"
                               value="{{ old('postal_code') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- Spécialités et certifications --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Spécialités et Certifications</h3>
                <p class="mt-1 text-sm text-gray-500">Domaines d'expertise et certifications</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                {{-- Spécialités --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Spécialités</label>
                    <div class="mt-2 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach($specialties as $key => $label)
                            <div class="flex items-center">
                                <input type="checkbox" name="specialties[]" id="specialty_{{ $key }}" value="{{ $key }}"
                                       {{ in_array($key, old('specialties', [])) ? 'checked' : '' }}
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="specialty_{{ $key }}" class="ml-3 text-sm text-gray-700">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Certifications --}}
                <div>
                    <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications</label>
                    <textarea name="certifications" id="certifications" rows="3"
                              placeholder="ISO 9001:2015, OHSAS 18001, etc."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('certifications') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Une certification par ligne</p>
                </div>
            </div>
        </div>

        {{-- Conditions commerciales --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Conditions Commerciales</h3>
                <p class="mt-1 text-sm text-gray-500">Termes et conditions de collaboration</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    {{-- Délai de paiement --}}
                    <div>
                        <label for="payment_terms_days" class="block text-sm font-medium text-gray-700">Délai de paiement (jours)</label>
                        <input type="number" name="payment_terms_days" id="payment_terms_days" min="0"
                               value="{{ old('payment_terms_days', 30) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Limite de crédit --}}
                    <div>
                        <label for="credit_limit" class="block text-sm font-medium text-gray-700">Limite de crédit (DA)</label>
                        <input type="number" name="credit_limit" id="credit_limit" step="0.01" min="0"
                               value="{{ old('credit_limit') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Taux de TVA --}}
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700">Taux de TVA (%)</label>
                        <select name="tax_rate" id="tax_rate"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="19" {{ old('tax_rate', '19') == '19' ? 'selected' : '' }}>19% (Standard)</option>
                            <option value="9" {{ old('tax_rate') == '9' ? 'selected' : '' }}>9% (Réduit)</option>
                            <option value="0" {{ old('tax_rate') == '0' ? 'selected' : '' }}>0% (Exonéré)</option>
                        </select>
                    </div>
                </div>

                {{-- Contact d'urgence --}}
                <div>
                    <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Contact d'urgence</label>
                    <input type="text" name="emergency_contact" id="emergency_contact"
                           value="{{ old('emergency_contact') }}"
                           placeholder="+213-555-987654"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Informations supplémentaires..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="window.history.back()"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                Annuler
            </button>
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <x-lucide-save class="h-4 w-4 mr-2" />
                Créer le fournisseur
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation NIF en temps réel
    const nifInput = document.getElementById('nif');
    nifInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, '');
        this.value = value;
        if (value.length === 15) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
        }
    });

    // Validation NIS en temps réel
    const nisInput = document.getElementById('nis');
    nisInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, '');
        this.value = value;
        if (value.length === 15) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
        }
    });

    // Validation RIB en temps réel
    const ribInput = document.getElementById('rib');
    ribInput.addEventListener('input', function() {
        const value = this.value.replace(/\D/g, '');
        this.value = value;
        if (value.length === 20) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
        }
    });

    // Validation RC en temps réel
    const rcInput = document.getElementById('trade_register');
    rcInput.addEventListener('input', function() {
        const value = this.value;
        const pattern = /^[0-9]{2}\/[0-9]{2}-[0-9]{7}$/;
        if (pattern.test(value)) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
        }
    });

    // Formatage automatique du RC
    rcInput.addEventListener('keyup', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2);
        }
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 12);
        }
        this.value = value;
    });
});
</script>
@endpush
@endsection