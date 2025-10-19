@props([
 'organization' => null,
 'isEdit' => false,
 'wilayas' => [],
 'organizationTypes' => []
])

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* Ultra Professional Form Styling */
.enterprise-form {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.form-section {
 background: white;
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.form-section:hover {
 border-color: #cbd5e1;
 box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.form-field {
 position: relative;
 margin-bottom: 1.5rem;
}

.form-label {
 display: block;
 font-size: 0.875rem;
 font-weight: 600;
 color: #374151;
 margin-bottom: 0.5rem;
 transition: color 0.2s ease;
}

.form-input {
 width: 100%;
 padding: 0.75rem 1rem;
 border: 2px solid #e5e7eb;
 border-radius: 0.5rem;
 font-size: 0.875rem;
 background: white;
 transition: all 0.2s ease;
 box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.form-input:focus {
 outline: none;
 border-color: #3b82f6;
 box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
 background: #ffffff;
}

.form-input.error {
 border-color: #ef4444;
 background: #fef2f2;
}

.form-input.error:focus {
 border-color: #ef4444;
 box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-input.success {
 border-color: #10b981;
 background: #f0fdf4;
}

.error-message {
 color: #ef4444;
 font-size: 0.75rem;
 margin-top: 0.25rem;
 display: flex;
 align-items: center;
}

.success-message {
 color: #10b981;
 font-size: 0.75rem;
 margin-top: 0.25rem;
 display: flex;
 align-items: center;
}

.help-text {
 color: #6b7280;
 font-size: 0.75rem;
 margin-top: 0.25rem;
}

.required-indicator {
 color: #ef4444;
 margin-left: 0.25rem;
}

.field-icon {
 position: absolute;
 left: 0.75rem;
 top: 50%;
 transform: translateY(-50%);
 color: #9ca3af;
 pointer-events: none;
 z-index: 10;
}

.form-input.with-icon {
 padding-left: 2.5rem;
}

.section-header {
 display: flex;
 align-items: center;
 gap: 0.75rem;
 margin-bottom: 1.5rem;
 padding-bottom: 1rem;
 border-bottom: 2px solid #f1f5f9;
}

.section-icon {
 padding: 0.5rem;
 border-radius: 0.5rem;
 color: white;
}

.progress-bar {
 height: 4px;
 background: #e5e7eb;
 border-radius: 2px;
 overflow: hidden;
 margin-bottom: 2rem;
}

.progress-fill {
 height: 100%;
 background: linear-gradient(90deg, #3b82f6, #1d4ed8);
 transition: width 0.3s ease;
}

/* TomSelect Customization */
.ts-control {
 border: 2px solid #e5e7eb !important;
 border-radius: 0.5rem !important;
 padding: 0.5rem 0.75rem !important;
 min-height: 2.75rem !important;
 box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
 background: white !important;
}

.ts-control:focus-within {
 border-color: #3b82f6 !important;
 box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

.ts-control.error {
 border-color: #ef4444 !important;
 background: #fef2f2 !important;
}

.ts-control.error:focus-within {
 border-color: #ef4444 !important;
 box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.ts-dropdown {
 border: 2px solid #e5e7eb !important;
 border-radius: 0.5rem !important;
 box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
}

.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

.file-upload-area {
 border: 2px dashed #d1d5db;
 border-radius: 0.5rem;
 padding: 1.5rem;
 text-align: center;
 transition: all 0.3s ease;
 background: #fafafa;
}

.file-upload-area:hover {
 border-color: #3b82f6;
 background: #f8faff;
}

.file-upload-area.dragover {
 border-color: #3b82f6;
 background: #eff6ff;
}

.btn-primary {
 background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
 color: white;
 padding: 0.75rem 2rem;
 border-radius: 0.5rem;
 font-weight: 600;
 border: none;
 transition: all 0.2s ease;
 box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.btn-primary:hover {
 background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
 transform: translateY(-1px);
 box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-secondary {
 background: white;
 color: #374151;
 padding: 0.75rem 2rem;
 border: 2px solid #e5e7eb;
 border-radius: 0.5rem;
 font-weight: 600;
 transition: all 0.2s ease;
}

.btn-secondary:hover {
 background: #f9fafb;
 border-color: #d1d5db;
 transform: translateY(-1px);
}
</style>
@endpush

<div class="max-w-6xl mx-auto px-6 py-8">
 {{-- Header --}}
 <div class="enterprise-form rounded-xl p-6 mb-8 fade-in">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-4">
 <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
 <i class="fas fa-building text-white text-xl"></i>
 </div>
 <div>
 <h1 class="text-2xl font-bold text-gray-900">
 {{ $isEdit ? 'Modifier l\'Organisation' : 'Nouvelle Organisation' }}
 </h1>
 <p class="text-sm text-gray-600 mt-1">
 <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
 Informations pour l'enregistrement en Algérie
 </p>
 </div>
 </div>
 <div class="text-right">
 <p class="text-sm text-gray-500">
 <i class="fas fa-clock mr-1"></i>
 {{ now()->format('d/m/Y à H:i') }}
 </p>
 <p class="text-xs text-gray-400">
 Utilisateur: {{ auth()->user()->name }}
 </p>
 </div>
 </div>

 {{-- Progress Bar --}}
 <div class="progress-bar mt-6">
 <div class="progress-fill" style="width: {{ $isEdit ? '100' : '0' }}%" id="form-progress"></div>
 </div>
 </div>

 {{-- Form --}}
 <form method="POST"
 action="{{ $isEdit && $organization ? route('admin.organizations.update', $organization) : route('admin.organizations.store') }}"
 enctype="multipart/form-data"
 class="space-y-8"
 id="organization-form">
 @csrf
 @if($isEdit)
 @method('PUT')
 @endif

 {{-- Section 1: Informations Générales --}}
 <div class="form-section rounded-xl p-6 fade-in">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-blue-500 to-blue-600">
 <i class="fas fa-info-circle text-lg"></i>
 </div>
 <h2 class="text-lg font-bold text-gray-900">Informations Générales</h2>
 <div class="ml-auto text-sm text-gray-500">Étape 1/5</div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Nom de l'organisation --}}
 <div class="md:col-span-2">
 <div class="form-field">
 <label class="form-label">
 Nom de l'Organisation
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-building"></i>
 <input type="text"
 name="name"
 class="form-input with-icon @error('name') error @enderror"
 value="{{ old('name', $organization?->name ?? '') }}"
 placeholder="Entrez le nom de l'organisation"
 required>
 </div>
 @error('name')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>

 {{-- Raison Sociale --}}
 <div class="form-field">
 <label class="form-label">Raison Sociale</label>
 <div class="relative">
 <i class="field-icon fas fa-certificate"></i>
 <input type="text"
 name="legal_name"
 class="form-input with-icon @error('legal_name') error @enderror"
 value="{{ old('legal_name', $organization?->legal_name ?? '') }}"
 placeholder="Raison sociale officielle">
 </div>
 @error('legal_name')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Type d'Organisation --}}
 <div class="form-field">
 <label class="form-label">
 Type d'Organisation
 <span class="required-indicator">*</span>
 </label>
 <select name="organization_type"
 class="form-input @error('organization_type') error @enderror"
 id="organization-type-select"
 required>
 <option value="">Sélectionner le type...</option>
 @foreach($organizationTypes as $key => $value)
 <option value="{{ $key }}" {{ old('organization_type', $organization?->organization_type) == $key ? 'selected' : '' }}>
 {{ $value }}
 </option>
 @endforeach
 </select>
 @error('organization_type')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Secteur d'Activité --}}
 <div class="form-field">
 <label class="form-label">Secteur d'Activité</label>
 <div class="relative">
 <i class="field-icon fas fa-industry"></i>
 <input type="text"
 name="industry"
 class="form-input with-icon @error('industry') error @enderror"
 value="{{ old('industry', $organization?->industry ?? '') }}"
 placeholder="Ex: Transport, Logistique...">
 </div>
 @error('industry')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Email Principal --}}
 <div class="form-field">
 <label class="form-label">
 Email Principal
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-envelope"></i>
 <input type="email"
 name="email"
 class="form-input with-icon @error('email') error @enderror"
 value="{{ old('email', $organization?->email ?? '') }}"
 placeholder="contact@entreprise.dz"
 required>
 </div>
 @error('email')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Téléphone Principal --}}
 <div class="form-field">
 <label class="form-label">
 Téléphone Principal
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-phone"></i>
 <input type="tel"
 name="phone_number"
 class="form-input with-icon @error('phone_number') error @enderror"
 value="{{ old('phone_number', $organization?->phone_number ?? '') }}"
 placeholder="+213 XX XX XX XX XX"
 required>
 </div>
 <div class="help-text">Format: +213 suivi de 8 ou 9 chiffres</div>
 @error('phone_number')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Site Web --}}
 <div class="form-field">
 <label class="form-label">Site Web</label>
 <div class="relative">
 <i class="field-icon fas fa-globe"></i>
 <input type="url"
 name="website"
 class="form-input with-icon @error('website') error @enderror"
 value="{{ old('website', $organization?->website ?? '') }}"
 placeholder="https://www.entreprise.dz">
 </div>
 @error('website')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Statut --}}
 <div class="form-field">
 <label class="form-label">Statut</label>
 <select name="status"
 class="form-input @error('status') error @enderror"
 id="status-select">
 <option value="active" {{ old('status', $organization?->status ?? 'active') == 'active' ? 'selected' : '' }}>Actif</option>
 <option value="inactive" {{ old('status', $organization?->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
 <option value="suspended" {{ old('status', $organization?->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
 </select>
 @error('status')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Description --}}
 <div class="md:col-span-2">
 <div class="form-field">
 <label class="form-label">Description de l'Activité</label>
 <textarea name="description"
 rows="4"
 class="form-input @error('description') error @enderror"
 placeholder="Décrivez brièvement l'activité de l'organisation...">{{ old('description', $organization?->description ?? '') }}</textarea>
 @error('description')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Section 2: Informations Légales --}}
 <div class="form-section rounded-xl p-6 fade-in">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-green-500 to-green-600">
 <i class="fas fa-gavel text-lg"></i>
 </div>
 <h2 class="text-lg font-bold text-gray-900">Informations Légales Algérie</h2>
 <div class="ml-auto text-sm text-gray-500">Étape 2/5</div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Registre de Commerce --}}
 <div class="form-field">
 <label class="form-label">
 Registre de Commerce
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-file-contract"></i>
 <input type="text"
 name="trade_register"
 class="form-input with-icon @error('trade_register') error @enderror"
 value="{{ old('trade_register', $organization?->trade_register ?? '') }}"
 placeholder="Ex: 16/00-1234567B23"
 required>
 </div>
 <div class="help-text">Numéro du registre de commerce</div>
 @error('trade_register')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- NIF --}}
 <div class="form-field">
 <label class="form-label">
 NIF (Numéro d'Identification Fiscale)
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-hashtag"></i>
 <input type="text"
 name="nif"
 class="form-input with-icon @error('nif') error @enderror"
 value="{{ old('nif', $organization?->nif ?? '') }}"
 placeholder="123456789012345"
 maxlength="15"
 required>
 </div>
 <div class="help-text">15 chiffres - Identifiant fiscal unique</div>
 @error('nif')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- AI --}}
 <div class="form-field">
 <label class="form-label">AI (Article d'Imposition)</label>
 <div class="relative">
 <i class="field-icon fas fa-receipt"></i>
 <input type="text"
 name="ai"
 class="form-input with-icon @error('ai') error @enderror"
 value="{{ old('ai', $organization?->ai ?? '') }}"
 placeholder="Ex: 16001234">
 </div>
 <div class="help-text">Numéro d'article d'imposition</div>
 @error('ai')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- NIS --}}
 <div class="form-field">
 <label class="form-label">NIS (Numéro d'Identification Statistique)</label>
 <div class="relative">
 <i class="field-icon fas fa-chart-bar"></i>
 <input type="text"
 name="nis"
 class="form-input with-icon @error('nis') error @enderror"
 value="{{ old('nis', $organization?->nis ?? '') }}"
 placeholder="Ex: 12345678901234">
 </div>
 <div class="help-text">Identifiant statistique ONS</div>
 @error('nis')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>
 </div>

 {{-- Section 3: Adresse --}}
 <div class="form-section rounded-xl p-6 fade-in">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-purple-500 to-purple-600">
 <i class="fas fa-map-marker-alt text-lg"></i>
 </div>
 <h2 class="text-lg font-bold text-gray-900">Adresse du Siège Social</h2>
 <div class="ml-auto text-sm text-gray-500">Étape 3/5</div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Adresse Complète --}}
 <div class="md:col-span-2">
 <div class="form-field">
 <label class="form-label">
 Adresse Complète
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-home"></i>
 <input type="text"
 name="address"
 class="form-input with-icon @error('address') error @enderror"
 value="{{ old('address', $organization?->address ?? '') }}"
 placeholder="Ex: Rue Mohamed Khemisti, Bt A, Appt 12"
 required>
 </div>
 <div class="help-text">Adresse complète du siège social</div>
 @error('address')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>

 {{-- Wilaya --}}
 <div class="form-field">
 <label class="form-label">
 Wilaya
 <span class="required-indicator">*</span>
 </label>
 <select name="wilaya"
 class="form-input @error('wilaya') error @enderror"
 id="wilaya-select"
 required>
 <option value="">Sélectionner une wilaya...</option>
 @foreach($wilayas as $code => $name)
 <option value="{{ $code }}" {{ old('wilaya', $organization?->wilaya) == $code ? 'selected' : '' }}>
 {{ $code }} - {{ $name }}
 </option>
 @endforeach
 </select>
 <div class="help-text">Division administrative d'Algérie</div>
 @error('wilaya')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Commune --}}
 <div class="form-field">
 <label class="form-label">
 Commune
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-building"></i>
 <input type="text"
 name="city"
 class="form-input with-icon @error('city') error @enderror"
 value="{{ old('city', $organization?->city ?? '') }}"
 placeholder="Ex: Alger Centre"
 required>
 </div>
 <div class="help-text">Commune de la wilaya</div>
 @error('city')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Précision Commune --}}
 <div class="form-field">
 <label class="form-label">Précision Commune</label>
 <div class="relative">
 <i class="field-icon fas fa-map-pin"></i>
 <input type="text"
 name="commune"
 class="form-input with-icon @error('commune') error @enderror"
 value="{{ old('commune', $organization?->commune ?? '') }}"
 placeholder="Ex: Quartier des Affaires">
 </div>
 <div class="help-text">Localisation précise dans la commune</div>
 @error('commune')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Code Postal --}}
 <div class="form-field">
 <label class="form-label">Code Postal</label>
 <div class="relative">
 <i class="field-icon fas fa-mailbox"></i>
 <input type="text"
 name="zip_code"
 class="form-input with-icon @error('zip_code') error @enderror"
 value="{{ old('zip_code', $organization?->zip_code ?? '') }}"
 placeholder="16000"
 maxlength="5">
 </div>
 <div class="help-text">Code postal algérien (5 chiffres)</div>
 @error('zip_code')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>
 </div>

 {{-- Section 4: Représentant Légal --}}
 <div class="form-section rounded-xl p-6 fade-in">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-amber-500 to-amber-600">
 <i class="fas fa-user-tie text-lg"></i>
 </div>
 <h2 class="text-lg font-bold text-gray-900">Représentant Légal</h2>
 <div class="ml-auto text-sm text-gray-500">Étape 4/5</div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Prénom du Représentant --}}
 <div class="form-field">
 <label class="form-label">
 Prénom du Représentant
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-user"></i>
 <input type="text"
 name="manager_first_name"
 class="form-input with-icon @error('manager_first_name') error @enderror"
 value="{{ old('manager_first_name', $organization?->manager_first_name ?? '') }}"
 placeholder="Ex: Mohamed"
 required>
 </div>
 @error('manager_first_name')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Nom du Représentant --}}
 <div class="form-field">
 <label class="form-label">
 Nom du Représentant
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-user"></i>
 <input type="text"
 name="manager_last_name"
 class="form-input with-icon @error('manager_last_name') error @enderror"
 value="{{ old('manager_last_name', $organization?->manager_last_name ?? '') }}"
 placeholder="Ex: Benali"
 required>
 </div>
 @error('manager_last_name')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- NIN --}}
 <div class="form-field">
 <label class="form-label">
 NIN (Numéro d'Identification Nationale)
 <span class="required-indicator">*</span>
 </label>
 <div class="relative">
 <i class="field-icon fas fa-id-card"></i>
 <input type="text"
 name="manager_nin"
 class="form-input with-icon @error('manager_nin') error @enderror"
 value="{{ old('manager_nin', $organization?->manager_nin ?? '') }}"
 placeholder="123456789012345678"
 maxlength="18"
 required>
 </div>
 <div class="help-text">18 chiffres - Carte d'identité nationale</div>
 @error('manager_nin')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Téléphone du Représentant --}}
 <div class="form-field">
 <label class="form-label">Téléphone du Représentant</label>
 <div class="relative">
 <i class="field-icon fas fa-phone"></i>
 <input type="tel"
 name="manager_phone_number"
 class="form-input with-icon @error('manager_phone_number') error @enderror"
 value="{{ old('manager_phone_number', $organization?->manager_phone_number ?? '') }}"
 placeholder="+213 XX XX XX XX XX">
 </div>
 @error('manager_phone_number')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Date de Naissance --}}
 <div class="form-field">
 <label class="form-label">Date de Naissance</label>
 <div class="relative">
 <i class="field-icon fas fa-calendar"></i>
 <input type="date"
 name="manager_dob"
 class="form-input with-icon @error('manager_dob') error @enderror"
 value="{{ old('manager_dob', $organization?->manager_dob?->format('Y-m-d') ?? '') }}">
 </div>
 @error('manager_dob')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Lieu de Naissance --}}
 <div class="form-field">
 <label class="form-label">Lieu de Naissance</label>
 <div class="relative">
 <i class="field-icon fas fa-map-marker"></i>
 <input type="text"
 name="manager_pob"
 class="form-input with-icon @error('manager_pob') error @enderror"
 value="{{ old('manager_pob', $organization?->manager_pob ?? '') }}"
 placeholder="Ex: Alger">
 </div>
 @error('manager_pob')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Adresse du Représentant --}}
 <div class="md:col-span-2">
 <div class="form-field">
 <label class="form-label">Adresse du Représentant Légal</label>
 <div class="relative">
 <i class="field-icon fas fa-home"></i>
 <input type="text"
 name="manager_address"
 class="form-input with-icon @error('manager_address') error @enderror"
 value="{{ old('manager_address', $organization?->manager_address ?? '') }}"
 placeholder="Adresse personnelle du représentant légal">
 </div>
 @error('manager_address')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Section 5: Documents --}}
 <div class="form-section rounded-xl p-6 fade-in">
 <div class="section-header">
 <div class="section-icon bg-gradient-to-br from-indigo-500 to-indigo-600">
 <i class="fas fa-file-upload text-lg"></i>
 </div>
 <h2 class="text-lg font-bold text-gray-900">Documents Justificatifs</h2>
 <div class="ml-auto text-sm text-gray-500">Étape 5/5</div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Scan du NIF --}}
 <div class="form-field">
 <label class="form-label">Scan du NIF</label>
 <div class="file-upload-area">
 <input type="file"
 name="scan_nif"
 class="hidden"
 id="scan_nif"
 accept=".pdf,.jpg,.jpeg,.png">
 <label for="scan_nif" class="cursor-pointer">
 <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
 <p class="text-sm text-gray-600">Cliquez pour sélectionner</p>
 <p class="text-xs text-gray-400">PDF, JPG, PNG jusqu'à 5MB</p>
 </label>
 </div>
 <div class="help-text">Document NIF au format PDF ou image</div>
 @error('scan_nif')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Scan de l'AI --}}
 <div class="form-field">
 <label class="form-label">Scan de l'AI</label>
 <div class="file-upload-area">
 <input type="file"
 name="scan_ai"
 class="hidden"
 id="scan_ai"
 accept=".pdf,.jpg,.jpeg,.png">
 <label for="scan_ai" class="cursor-pointer">
 <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
 <p class="text-sm text-gray-600">Cliquez pour sélectionner</p>
 <p class="text-xs text-gray-400">PDF, JPG, PNG jusqu'à 5MB</p>
 </label>
 </div>
 <div class="help-text">Document AI au format PDF ou image</div>
 @error('scan_ai')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Scan du NIS --}}
 <div class="form-field">
 <label class="form-label">Scan du NIS</label>
 <div class="file-upload-area">
 <input type="file"
 name="scan_nis"
 class="hidden"
 id="scan_nis"
 accept=".pdf,.jpg,.jpeg,.png">
 <label for="scan_nis" class="cursor-pointer">
 <i class="fas fa-file-pdf text-3xl text-gray-400 mb-2"></i>
 <p class="text-sm text-gray-600">Cliquez pour sélectionner</p>
 <p class="text-xs text-gray-400">PDF, JPG, PNG jusqu'à 5MB</p>
 </label>
 </div>
 <div class="help-text">Document NIS au format PDF ou image</div>
 @error('scan_nis')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Pièce d'Identité du Représentant --}}
 <div class="form-field">
 <label class="form-label">Pièce d'Identité du Représentant</label>
 <div class="file-upload-area">
 <input type="file"
 name="manager_id_scan"
 class="hidden"
 id="manager_id_scan"
 accept=".pdf,.jpg,.jpeg,.png">
 <label for="manager_id_scan" class="cursor-pointer">
 <i class="fas fa-id-card text-3xl text-gray-400 mb-2"></i>
 <p class="text-sm text-gray-600">Cliquez pour sélectionner</p>
 <p class="text-xs text-gray-400">PDF, JPG, PNG jusqu'à 5MB</p>
 </label>
 </div>
 <div class="help-text">Carte d'identité ou passeport</div>
 @error('manager_id_scan')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>

 {{-- Logo de l'Organisation --}}
 <div class="md:col-span-2">
 <div class="form-field">
 <label class="form-label">Logo de l'Organisation</label>
 <div class="file-upload-area">
 <input type="file"
 name="logo"
 class="hidden"
 id="logo"
 accept=".jpg,.jpeg,.png,.svg">
 <label for="logo" class="cursor-pointer">
 <i class="fas fa-image text-3xl text-gray-400 mb-2"></i>
 <p class="text-sm text-gray-600">Cliquez pour sélectionner</p>
 <p class="text-xs text-gray-400">JPG, PNG, SVG jusqu'à 2MB</p>
 </label>
 </div>
 <div class="help-text">Logo officiel de l'organisation (optionnel)</div>
 @error('logo')
 <div class="error-message">
 <i class="fas fa-exclamation-circle mr-1"></i>
 {{ $message }}
 </div>
 @enderror
 </div>
 </div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="enterprise-form rounded-xl p-6 flex items-center justify-between fade-in">
 <div class="flex items-center gap-3 text-sm text-gray-600">
 <i class="fas fa-info-circle text-blue-500"></i>
 <span>Tous les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires</span>
 </div>
 <div class="flex items-center gap-4">
 <a href="{{ route('admin.organizations.index') }}" class="btn-secondary">
 <i class="fas fa-arrow-left mr-2"></i>
 Annuler
 </a>
 <button type="submit" class="btn-primary" id="submit-btn">
 <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus' }} mr-2"></i>
 {{ $isEdit ? 'Mettre à jour' : 'Créer l\'organisation' }}
 </button>
 </div>
 </div>
 </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
 // Initialize TomSelect for dropdowns
 const organizationTypeSelect = new TomSelect('#organization-type-select', {
 placeholder: 'Sélectionner le type...',
 searchField: ['text', 'value'],
 allowEmptyOption: false,
 create: false
 });

 const statusSelect = new TomSelect('#status-select', {
 placeholder: 'Sélectionner le statut...',
 searchField: ['text', 'value'],
 allowEmptyOption: false,
 create: false
 });

 const wilayaSelect = new TomSelect('#wilaya-select', {
 placeholder: 'Sélectionner une wilaya...',
 searchField: ['text'],
 allowEmptyOption: false,
 create: false,
 maxOptions: 100
 });

 // Form validation and progress tracking
 const form = document.getElementById('organization-form');
 const progressBar = document.getElementById('form-progress');
 let currentProgress = {{ $isEdit ? '100' : '0' }};

 // Real-time validation
 const inputs = form.querySelectorAll('input, select, textarea');
 inputs.forEach(input => {
 input.addEventListener('blur', validateField);
 input.addEventListener('input', updateProgress);
 });

 function validateField(event) {
 const field = event.target;
 const value = field.value.trim();
 const isRequired = field.hasAttribute('required');

 // Remove existing validation classes
 field.classList.remove('error', 'success');

 // Remove existing messages
 const existingMessage = field.parentNode.querySelector('.error-message, .success-message');
 if (existingMessage && !existingMessage.classList.contains('permanent')) {
 existingMessage.remove();
 }

 if (isRequired && !value) {
 field.classList.add('error');
 showFieldMessage(field, 'Ce champ est obligatoire', 'error');
 return false;
 }

 // Specific validations
 if (field.type === 'email' && value) {
 const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
 if (!emailRegex.test(value)) {
 field.classList.add('error');
 showFieldMessage(field, 'Format d\'email invalide', 'error');
 return false;
 }
 }

 if (field.name === 'nif' && value) {
 if (value.length !== 15 || !/^\d+$/.test(value)) {
 field.classList.add('error');
 showFieldMessage(field, 'Le NIF doit contenir exactement 15 chiffres', 'error');
 return false;
 }
 }

 if (field.name === 'manager_nin' && value) {
 if (value.length !== 18 || !/^\d+$/.test(value)) {
 field.classList.add('error');
 showFieldMessage(field, 'Le NIN doit contenir exactement 18 chiffres', 'error');
 return false;
 }
 }

 if (field.name === 'zip_code' && value) {
 if (value.length !== 5 || !/^\d+$/.test(value)) {
 field.classList.add('error');
 showFieldMessage(field, 'Le code postal doit contenir exactement 5 chiffres', 'error');
 return false;
 }
 }

 if (field.type === 'tel' && value) {
 const phoneRegex = /^\+213\s?\d{8,9}$/;
 if (!phoneRegex.test(value.replace(/\s/g, ''))) {
 field.classList.add('error');
 showFieldMessage(field, 'Format: +213 suivi de 8 ou 9 chiffres', 'error');
 return false;
 }
 }

 // If validation passes
 if (value) {
 field.classList.add('success');
 showFieldMessage(field, 'Valide', 'success');
 }

 return true;
 }

 function showFieldMessage(field, message, type) {
 const messageDiv = document.createElement('div');
 messageDiv.className = `${type}-message`;
 messageDiv.innerHTML = `
 <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} mr-1"></i>
 ${message}
 `;

 // Insert after the input field's parent
 const parent = field.closest('.form-field');
 if (parent) {
 parent.appendChild(messageDiv);
 }
 }

 function updateProgress() {
 const totalFields = form.querySelectorAll('input[required], select[required], textarea[required]').length;
 const filledFields = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'))
 .filter(field => field.value.trim() !== '').length;

 const progress = Math.round((filledFields / totalFields) * 100);
 progressBar.style.width = `${progress}%`;
 }

 // File upload handling
 const fileInputs = form.querySelectorAll('input[type="file"]');
 fileInputs.forEach(input => {
 input.addEventListener('change', function() {
 const file = this.files[0];
 const uploadArea = this.closest('.file-upload-area');
 const label = uploadArea.querySelector('label');

 if (file) {
 // Validate file size
 const maxSize = this.name === 'logo' ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for logo, 5MB for others

 if (file.size > maxSize) {
 alert(`Le fichier est trop volumineux. Taille maximale: ${maxSize / (1024 * 1024)}MB`);
 this.value = '';
 return;
 }

 // Update UI
 label.innerHTML = `
 <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
 <p class="text-sm text-green-600">${file.name}</p>
 <p class="text-xs text-gray-400">Fichier sélectionné</p>
 `;
 uploadArea.style.borderColor = '#10b981';
 uploadArea.style.background = '#f0fdf4';
 }
 });
 });

 // Form submission handling
 form.addEventListener('submit', function(e) {
 e.preventDefault();

 // Validate all required fields
 const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
 let isValid = true;

 requiredFields.forEach(field => {
 if (!validateField({ target: field })) {
 isValid = false;
 }
 });

 if (!isValid) {
 alert('Veuillez corriger les erreurs avant de soumettre le formulaire.');
 return;
 }

 // Show loading state
 const submitBtn = document.getElementById('submit-btn');
 const originalText = submitBtn.innerHTML;
 submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
 submitBtn.disabled = true;

 // Submit the form
 this.submit();
 });

 // Auto-formatting for phone numbers
 const phoneInputs = form.querySelectorAll('input[type="tel"]');
 phoneInputs.forEach(input => {
 input.addEventListener('input', function() {
 let value = this.value.replace(/\D/g, '');
 if (value.startsWith('213')) {
 value = '+' + value;
 } else if (value.startsWith('0')) {
 value = '+213' + value.substring(1);
 } else if (value && !value.startsWith('+213')) {
 value = '+213' + value;
 }
 this.value = value;
 });
 });

 // Auto-formatting for NIF and NIN
 const nifInput = form.querySelector('input[name="nif"]');
 const ninInput = form.querySelector('input[name="manager_nin"]');

 if (nifInput) {
 nifInput.addEventListener('input', function() {
 this.value = this.value.replace(/\D/g, '').substring(0, 15);
 });
 }

 if (ninInput) {
 ninInput.addEventListener('input', function() {
 this.value = this.value.replace(/\D/g, '').substring(0, 18);
 });
 }

 // Initialize progress
 updateProgress();
});
</script>
@endpush