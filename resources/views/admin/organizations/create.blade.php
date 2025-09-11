{{-- resources/views/admin/organizations/create.blade.php --}}
@extends('layouts.admin.app')
@section('title', 'Nouvelle Organisation')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">
<style>
    .form-section { @apply bg-white rounded-2xl p-8 border border-gray-100 shadow-sm; }
    .form-group { @apply space-y-2; }
    .form-label { @apply block text-sm font-semibold text-gray-700 mb-2; }
    .form-input { @apply w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all; }
    .form-select { @apply form-input appearance-none bg-white; }
    .form-textarea { @apply form-input resize-none; }
    .form-error { @apply text-sm text-red-600 mt-1; }
    .form-help { @apply text-sm text-gray-500 mt-1; }
    .step-header { @apply flex items-center gap-4 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl mb-8; }
    .step-number { @apply w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold; }
</style>
@endpush

@section('content')
<div class="zenfleet-gradient min-h-screen -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <x-breadcrumb :items="[
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Organisations', 'url' => route('admin.organizations.index')],
                ['title' => 'Nouvelle organisation', 'active' => true]
            ]" />
            
            <div class="step-header">
                <div class="step-number">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Créer une nouvelle organisation</h1>
                    <p class="text-gray-600">Configurez les informations de base et les paramètres avancés</p>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.organizations.store') }}" method="POST" enctype="multipart/form-data" 
              x-data="organizationForm()" @submit.prevent="submitForm">
            @csrf
            
            <!-- Section 1: Informations générales -->
            <div class="form-section mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <i class="fas fa-info-circle text-blue-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Informations générales</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="name">
                            Nom de l'organisation <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="form-input @error('name') border-red-300 @enderror"
                            placeholder="Acme Corporation"
                            required
                            x-model="form.name"
                            @input="generateSlug"
                        >
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-help">Nom affiché publiquement</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="legal_name">
                            Raison sociale <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="legal_name" 
                            name="legal_name" 
                            value="{{ old('legal_name') }}"
                            class="form-input @error('legal_name') border-red-300 @enderror"
                            placeholder="Acme Corporation SAS"
                            required
                        >
                        @error('legal_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="organization_type">
                            Type d'organisation <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="organization_type" 
                            name="organization_type" 
                            class="form-select @error('organization_type') border-red-300 @enderror"
                            required
                        >
                            <option value="">Sélectionnez un type</option>
                            @foreach([
                                'enterprise' => 'Grande Entreprise',
                                'sme' => 'PME',
                                'startup' => 'Startup',
                                'public' => 'Secteur Public',
                                'ngo' => 'ONG',
                                'cooperative' => 'Coopérative'
                            ] as $value => $label)
                                <option value="{{ $value }}" {{ old('organization_type') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_type')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="industry">
                            Secteur d'activité
                        </label>
                        <input 
                            type="text" 
                            id="industry" 
                            name="industry" 
                            value="{{ old('industry') }}"
                            class="form-input @error('industry') border-red-300 @enderror"
                            placeholder="Transport, Logistique..."
                        >
                        @error('industry')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group mt-6">
                    <label class="form-label" for="description">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="3"
                        class="form-textarea @error('description') border-red-300 @enderror"
                        placeholder="Description de l'organisation et de ses activités..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo Upload -->
                <div class="form-group mt-6">
                    <label class="form-label" for="logo">Logo</label>
                    <div 
                        class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors"
                        x-data="fileUpload()"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)"
                    >
                        <div x-show="!preview">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">Glissez votre logo ici ou <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:underline">parcourir</button></p>
                            <p class="text-sm text-gray-500">PNG, JPG, SVG jusqu'à 2MB</p>
                        </div>
                        <div x-show="preview" class="space-y-4">
                            <img :src="preview" class="max-w-32 mx-auto rounded-lg">
                            <button type="button" @click="removeFile()" class="text-red-600 hover:underline text-sm">
                                <i class="fas fa-times"></i> Supprimer
                            </button>
                        </div>
                        <input 
                            type="file" 
                            name="logo" 
                            x-ref="fileInput"
                            @change="handleFile($event)"
                            accept="image/*"
                            class="hidden"
                        >
                    </div>
                    @error('logo')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 2: Informations légales -->
            <div class="form-section mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <i class="fas fa-file-contract text-green-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Informations légales</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="siret">SIRET</label>
                        <input 
                            type="text" 
                            id="siret" 
                            name="siret" 
                            value="{{ old('siret') }}"
                            class="form-input @error('siret') border-red-300 @enderror"
                            placeholder="12345678901234"
                            pattern="[0-9]{14}"
                        >
                        @error('siret')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="vat_number">Numéro TVA</label>
                        <input 
                            type="text" 
                            id="vat_number" 
                            name="vat_number" 
                            value="{{ old('vat_number') }}"
                            class="form-input @error('vat_number') border-red-300 @enderror"
                            placeholder="FR12345678901"
                        >
                        @error('vat_number')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Contact & Adresse -->
            <div class="form-section mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <i class="fas fa-address-book text-purple-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Contact & Adresse</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="email">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="form-input @error('email') border-red-300 @enderror"
                            placeholder="contact@acme-corp.com"
                            required
                        >
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Téléphone</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone') }}"
                            class="form-input @error('phone') border-red-300 @enderror"
                            placeholder="+33 1 23 45 67 89"
                        >
                        @error('phone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="website">Site web</label>
                        <input 
                            type="url" 
                            id="website" 
                            name="website" 
                            value="{{ old('website') }}"
                            class="form-input @error('website') border-red-300 @enderror"
                            placeholder="https://www.acme-corp.com"
                        >
                        @error('website')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="country">
                            Pays <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="country" 
                            name="country" 
                            class="form-select @error('country') border-red-300 @enderror"
                            required
                        >
                            <option value="">Sélectionnez un pays</option>
                            @foreach([
                                'FR' => 'France',
                                'DZ' => 'Algérie',
                                'BE' => 'Belgique',
                                'CH' => 'Suisse',
                                'DE' => 'Allemagne',
                                'ES' => 'Espagne'
                            ] as $code => $country)
                                <option value="{{ $code }}" {{ old('country') === $code ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group mt-6">
                    <label class="form-label" for="address">
                        Adresse <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="address" 
                        name="address" 
                        value="{{ old('address') }}"
                        class="form-input @error('address') border-red-300 @enderror"
                        placeholder="123 Rue de la Paix"
                        required
                    >
                    @error('address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="form-group">
                        <label class="form-label" for="city">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            value="{{ old('city') }}"
                            class="form-input @error('city') border-red-300 @enderror"
                            placeholder="Paris"
                            required
                        >
                        @error('city')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="postal_code">
                            Code postal <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="postal_code" 
                            name="postal_code" 
                            value="{{ old('postal_code') }}"
                            class="form-input @error('postal_code') border-red-300 @enderror"
                            placeholder="75001"
                            required
                        >
                        @error('postal_code')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 4: Paramètres -->
            <div class="form-section mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <i class="fas fa-cogs text-orange-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Paramètres</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="timezone">
                            Fuseau horaire <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="timezone" 
                            name="timezone" 
                            class="form-select @error('timezone') border-red-300 @enderror"
                            required
                        >
                            @foreach([
                                'Europe/Paris' => 'France (CET)',
                                'Africa/Algiers' => 'Algérie (GMT+1)',
                                'Europe/London' => 'Royaume-Uni (GMT)',
                                'Europe/Berlin' => 'Allemagne (CET)'
                            ] as $value => $label)
                                <option value="{{ $value }}" {{ old('timezone') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('timezone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="currency">
                            Devise <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="currency" 
                            name="currency" 
                            class="form-select @error('currency') border-red-300 @enderror"
                            required
                        >
                            @foreach([
                                'EUR' => 'Euro (€)',
                                'DZD' => 'Dinar Algérien (DZD)',
                                'USD' => 'Dollar US ($)',
                                'GBP' => 'Livre Sterling (£)'
                            ] as $value => $label)
                                <option value="{{ $value }}" {{ old('currency') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="language">
                            Langue <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="language" 
                            name="language" 
                            class="form-select @error('language') border-red-300 @enderror"
                            required
                        >
                            @foreach([
                                'fr' => 'Français',
                                'en' => 'English',
                                'ar' => 'العربية',
                                'de' => 'Deutsch'
                            ] as $value => $label)
                                <option value="{{ $value }}" {{ old('language', 'fr') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('language')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Limites -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="form-group">
                        <label class="form-label" for="max_users">
                            Limite utilisateurs <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="max_users" 
                            name="max_users" 
                            value="{{ old('max_users', 50) }}"
                            class="form-input @error('max_users') border-red-300 @enderror"
                            min="1"
                            max="1000"
                            required
                        >
                        @error('max_users')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="max_vehicles">
                            Limite véhicules <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="max_vehicles" 
                            name="max_vehicles" 
                            value="{{ old('max_vehicles', 100) }}"
                            class="form-input @error('max_vehicles') border-red-300 @enderror"
                            min="1"
                            max="10000"
                            required
                        >
                        @error('max_vehicles')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="max_drivers">
                            Limite chauffeurs <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="max_drivers" 
                            name="max_drivers" 
                            value="{{ old('max_drivers', 200) }}"
                            class="form-input @error('max_drivers') border-red-300 @enderror"
                            min="1"
                            max="5000"
                            required
                        >
                        @error('max_drivers')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 5: Administrateur -->
            <div class="form-section mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <i class="fas fa-user-shield text-red-600"></i>
                    <h2 class="text-xl font-semibold text-gray-900">Administrateur principal</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label" for="admin_first_name">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="admin_first_name" 
                            name="admin_first_name" 
                            value="{{ old('admin_first_name') }}"
                            class="form-input @error('admin_first_name') border-red-300 @enderror"
                            placeholder="Jean"
                            required
                        >
                        @error('admin_first_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_last_name">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="admin_last_name" 
                            name="admin_last_name" 
                            value="{{ old('admin_last_name') }}"
                            class="form-input @error('admin_last_name') border-red-300 @enderror"
                            placeholder="Dupont"
                            required
                        >
                        @error('admin_last_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_email">
                            Email administrateur <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="admin_email" 
                            name="admin_email" 
                            value="{{ old('admin_email') }}"
                            class="form-input @error('admin_email') border-red-300 @enderror"
                            placeholder="admin@acme-corp.com"
                            required
                        >
                        @error('admin_email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-help">Un mot de passe temporaire sera envoyé par email</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_phone">Téléphone administrateur</label>
                        <input 
                            type="tel" 
                            id="admin_phone" 
                            name="admin_phone" 
                            value="{{ old('admin_phone') }}"
                            class="form-input @error('admin_phone') border-red-300 @enderror"
                            placeholder="+33 1 23 45 67 89"
                        >
                        @error('admin_phone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.organizations.index') }}" 
                   class="zenfleet-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>

                <button 
                    type="submit" 
                    class="zenfleet-btn-primary"
                    :disabled="submitting"
                    x-bind:class="{ 'opacity-50 cursor-not-allowed': submitting }"
                >
                    <i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                    <span x-text="submitting ? 'Création en cours...' : 'Créer l\'organisation'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
function organizationForm() {
    return {
        form: {
            name: '{{ old('name') }}'
        },
        submitting: false,

        generateSlug() {
            // Auto-generate slug logic if needed
        },

        submitForm() {
            this.submitting = true;
            this.$el.submit();
        }
    }
}

function fileUpload() {
    return {
        preview: null,

        handleFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.preview = URL.createObjectURL(file);
            }
        },

        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.preview = URL.createObjectURL(file);
            }
        },

        removeFile() {
            this.preview = null;
            this.$refs.fileInput.value = '';
        }
    }
}

// Initialize select components
document.addEventListener('DOMContentLoaded', function() {
    new TomSelect('#country', {
        searchField: ['text', 'value'],
        placeholder: 'Sélectionnez un pays'
    });
    
    new TomSelect('#organization_type', {
        placeholder: 'Sélectionnez un type'
    });
});
</script>
@endpush
@endsection
