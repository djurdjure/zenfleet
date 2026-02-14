{{--
 🏢 ZenFleet Enterprise Organization Form Component
 Dynamic component for both CREATE and EDIT operations
 Features: Multi-step wizard, real-time validation, modern glass morphism UI
--}}

@props([
 'organization' => null,
 'isEdit' => false,
 'countries' => [],
 'organizationTypes' => [],
 'currencies' => [],
 'timezones' => [],
 'subscriptionPlans' => []
])

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
 <!-- 🎨 Modern Hero Header -->
 <div class="max-w-6xl mx-auto mb-8">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <!-- Breadcrumb -->
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-home"></i> Dashboard
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.organizations.index') }}" class="hover:text-blue-600 transition-colors">
 Organisations
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="font-semibold text-gray-900">
 {{ $isEdit ? 'Modifier' : 'Nouvelle Organisation' }}
 </span>
 </nav>

 <!-- Hero Content -->
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plus' }} text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">
 {{ $isEdit ? 'Modifier l\'Organisation' : 'Créer une Organisation' }}
 </h1>
 <p class="text-gray-600 text-lg mt-2">
 {{ $isEdit ? 'Mettez à jour les informations de l\'organisation' : 'Configurez une nouvelle organisation sur la plateforme ZenFleet' }}
 </p>
 </div>
 </div>
 </div>
 </div>

 <!-- 🧙‍♂️ Multi-Step Wizard Form -->
 <div class="max-w-6xl mx-auto" x-data="organizationWizard(@js($organization), @js($isEdit))">
 <!-- Progress Stepper -->
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
 <div class="flex items-center justify-between">
 <template x-for="(step, index) in steps" :key="index">
 <div class="flex items-center" :class="{ 'flex-1': index < steps.length - 1 }">
 <!-- Step Circle -->
 <div class="flex flex-col items-center">
 <button
 @click="goToStep(index)"
 :disabled="index > highestStep"
 class="w-14 h-14 rounded-full flex items-center justify-center transition-all duration-300 relative group"
 :class="{
 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg scale-110': currentStep === index,
 'bg-white text-blue-600 border-2 border-blue-200 shadow-md': currentStep > index && index <= highestStep,
 'bg-gray-200 text-gray-500': currentStep < index || index > highestStep
 }"
 >
 <i class="fas" :class="step.icon"></i>

 <!-- Completion checkmark -->
 <div x-show="currentStep > index && index <= highestStep"
 class="absolute -top-1 -right-1 w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center">
 <i class="fas fa-check text-xs"></i>
 </div>
 </button>

 <!-- Step Label -->
 <div class="mt-3 text-center">
 <p class="text-sm font-semibold"
 :class="currentStep >= index ? 'text-gray-900' : 'text-gray-500'"
 x-text="step.title"></p>
 <p class="text-xs text-gray-500 mt-1" x-text="step.description"></p>
 </div>
 </div>

 <!-- Progress Line -->
 <div x-show="index < steps.length - 1"
 class="flex-1 h-1 mx-6 transition-all duration-500 rounded-full"
 :class="currentStep > index ? 'bg-gradient-to-r from-blue-500 to-indigo-500' : 'bg-gray-200'"></div>
 </div>
 </template>
 </div>
 </div>

 <!-- Form Container -->
 <form id="organization-form"
 :action="isEdit ? '{{ $isEdit ? route('admin.organizations.update', $organization) : '' }}' : '{{ route('admin.organizations.store') }}'"
 method="POST"
 enctype="multipart/form-data"
 class="space-y-8">
 @csrf
 @if($isEdit)
 @method('PATCH')
 @endif

 <!-- Step 1: Informations Générales -->
 <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-6"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-info-circle text-white"></i>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">Informations Générales</h2>
 <p class="text-gray-600">Définissez l'identité de base de votre organisation</p>
 </div>
 </div>

 <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
 <!-- Organization Name -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Nom de l'organisation <span class="text-red-500">*</span>
 </label>
 <div class="relative">
 <input
 type="text"
 name="name"
 value="{{ old('name', $organization->name ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all text-lg"
 placeholder="ex: Acme Corporation"
 required
 >
 <div class="absolute inset-y-0 right-0 flex items-center pr-4">
 <i class="fas fa-building text-gray-400"></i>
 </div>
 </div>
 @error('name')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Nom public affiché sur la plateforme</p>
 </div>

 <!-- Legal Name -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Raison sociale <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="legal_name"
 value="{{ old('legal_name', $organization->legal_name ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="ex: Acme Corporation SAS"
 required
 >
 @error('legal_name')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Organization Type with Tom Select -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Type d'organisation <span class="text-red-500">*</span>
 </label>
 <div class="relative">
 <select
 name="organization_type"
 id="organization_type"
 class="tom-select w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 required
 data-placeholder="Sélectionnez un type d'organisation"
 >
 <option value="">Sélectionnez un type</option>
 @foreach($organizationTypes as $value => $label)
 <option value="{{ $value }}" {{ old('organization_type', $organization->organization_type ?? '') === $value ? 'selected' : '' }}>
 {{ $label }}
 </option>
 @endforeach
 </select>
 </div>
 @error('organization_type')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Industry with Tom Select -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Secteur d'activité
 </label>
 <select
 name="industry"
 id="industry"
 class="tom-select-taggable w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 data-placeholder="Sélectionnez ou saisissez un secteur d'activité"
 >
 @php
 $industries = [
 'Transport et Logistique',
 'BTP et Construction',
 'Agriculture et Agroalimentaire',
 'Industrie Manufacturière',
 'Technologies de l\'Information',
 'Santé et Pharmaceutique',
 'Éducation et Formation',
 'Commerce et Distribution',
 'Tourisme et Hôtellerie',
 'Énergie et Hydrocarbures',
 'Mines et Métallurgie',
 'Textile et Cuir',
 'Services Financiers',
 'Télécommunications',
 'Chimie et Pétrochimie'
 ];
 $currentIndustry = old('industry', $organization->industry ?? '');
 @endphp
 <option value="">Sélectionnez un secteur</option>
 @foreach($industries as $industry)
 <option value="{{ $industry }}" {{ $currentIndustry === $industry ? 'selected' : '' }}>
 {{ $industry }}
 </option>
 @endforeach
 @if($currentIndustry && !in_array($currentIndustry, $industries))
 <option value="{{ $currentIndustry }}" selected>{{ $currentIndustry }}</option>
 @endif
 </select>
 @error('industry')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 <!-- Description -->
 <div class="mt-8 space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Description de l'organisation
 </label>
 <textarea
 name="description"
 rows="4"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all resize-none"
 placeholder="Décrivez l'activité principale de votre organisation, ses spécialités et sa mission..."
 >{{ old('description', $organization->description ?? '') }}</textarea>
 @error('description')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Cette description sera visible dans le profil public de l'organisation</p>
 </div>

 <!-- Logo Upload Section -->
 <div class="mt-8 space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Logo de l'organisation
 </label>
 <div x-data="fileUploader()"
 class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 transition-colors"
 @dragover.prevent
 @drop.prevent="handleDrop($event)">

 <div x-show="!preview && !existingLogo" class="space-y-4">
 <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
 <i class="fas fa-cloud-upload-alt text-2xl text-gray-400"></i>
 </div>
 <div>
 <p class="text-gray-600 text-lg">
 Glissez votre logo ici ou
 <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:text-blue-700 font-semibold">
 parcourez vos fichiers
 </button>
 </p>
 <p class="text-sm text-gray-500 mt-2">PNG, JPG, SVG jusqu'à 2MB • Recommandé: 200x200px</p>
 </div>
 </div>

 <!-- Existing Logo (Edit mode) -->
 @if($organization && $organization->logo_path)
 <div x-show="!preview" x-init="existingLogo = true" class="space-y-4">
 <img src="{{ Storage::url($organization->logo_path) }}"
 alt="Logo actuel"
 class="max-w-32 mx-auto rounded-xl shadow-lg">
 <p class="text-sm text-gray-600">Logo actuel</p>
 <button type="button" @click="$refs.fileInput.click()"
 class="text-blue-600 hover:text-blue-700 font-semibold">
 Remplacer le logo
 </button>
 </div>
 @endif

 <!-- Preview -->
 <div x-show="preview" class="space-y-4">
 <img :src="preview" class="max-w-32 mx-auto rounded-xl shadow-lg">
 <div class="flex items-center justify-center gap-4">
 <button type="button" @click="removeFile()"
 class="text-red-600 hover:text-red-700 font-semibold">
 <i class="fas fa-trash mr-1"></i> Supprimer
 </button>
 <button type="button" @click="$refs.fileInput.click()"
 class="text-blue-600 hover:text-blue-700 font-semibold">
 <i class="fas fa-edit mr-1"></i> Changer
 </button>
 </div>
 </div>

 <input type="file" name="logo" x-ref="fileInput" @change="handleFile($event)"
 accept="image/*" class="hidden">
 </div>
 @error('logo')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>

 <!-- Step 2: Informations Légales -->
 <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-6"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-file-contract text-white"></i>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">Informations Légales Algériennes</h2>
 <p class="text-gray-600">Identifiants fiscaux et registres officiels</p>
 </div>
 </div>

 <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
 <!-- NIF (Numéro d'Identification Fiscale) -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 NIF <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="nif"
 value="{{ old('nif', $organization->nif ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="000000000000000"
 pattern="[0-9]{15}"
 maxlength="15"
 required
 >
 @error('nif')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Numéro d'Identification Fiscale (15 chiffres)</p>
 </div>

 <!-- AI (Article d'Imposition) -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 AI
 </label>
 <input
 type="text"
 name="ai"
 value="{{ old('ai', $organization->ai ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="00000000000000"
 >
 @error('ai')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Article d'Imposition</p>
 </div>

 <!-- NIS (Numéro d'Identification Statistique) -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 NIS
 </label>
 <input
 type="text"
 name="nis"
 value="{{ old('nis', $organization->nis ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="000000000000000"
 >
 @error('nis')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Numéro d'Identification Statistique</p>
 </div>

 <!-- Registre de Commerce -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Registre de Commerce <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="trade_register"
 value="{{ old('trade_register', $organization->trade_register ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="00/00-0000000 B 00"
 required
 >
 @error('trade_register')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Numéro d'inscription au registre de commerce</p>
 </div>

 <!-- Forme Juridique -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Forme Juridique
 </label>
 <select
 name="legal_form"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none"
 >
 <option value="">Sélectionnez une forme</option>
 @foreach([
 'SARL' => 'SARL - Société à Responsabilité Limitée',
 'SPA' => 'SPA - Société Par Actions',
 'SNC' => 'SNC - Société en Nom Collectif',
 'SCS' => 'SCS - Société en Commandite Simple',
 'EURL' => 'EURL - Entreprise Unipersonnelle à Responsabilité Limitée',
 'EI' => 'EI - Entreprise Individuelle',
 'Association' => 'Association',
 'Cooperative' => 'Coopérative'
 ] as $value => $label)
 <option value="{{ $value }}" {{ old('legal_form', $organization->legal_form ?? '') === $value ? 'selected' : '' }}>
 {{ $label }}
 </option>
 @endforeach
 </select>
 @error('legal_form')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Date d'Immatriculation -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Date d'Immatriculation
 </label>
 <input
 type="date"
 name="registration_date"
 value="{{ old('registration_date', $organization->registration_date ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 >
 @error('registration_date')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 </div>

 <!-- Registration Date -->
 <!-- Document Scans Section -->
 <div class="mt-8 space-y-6">
 <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
 Documents Légaux (Scans)
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <!-- Scan NIF -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Scan du NIF
 </label>
 <div x-data="documentUploader('nif')"
 class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors"
 @dragover.prevent
 @drop.prevent="handleDrop($event)">
 <div x-show="!preview" class="space-y-2">
 <i class="fas fa-file-upload text-2xl text-gray-400"></i>
 <p class="text-sm text-gray-600">
 <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:text-blue-700 font-semibold">
 Choisir le fichier
 </button>
 </p>
 <p class="text-xs text-gray-500">PDF, JPG, PNG (max 5MB)</p>
 </div>
 <div x-show="preview" class="space-y-2">
 <i class="fas fa-file-alt text-2xl text-green-600"></i>
 <p class="text-sm text-green-600" x-text="fileName"></p>
 <button type="button" @click="removeFile()" class="text-red-600 hover:text-red-700 text-xs">
 Supprimer
 </button>
 </div>
 <input type="file" name="scan_nif" x-ref="fileInput" @change="handleFile($event)"
 accept=".pdf,.jpg,.jpeg,.png" class="hidden">
 </div>
 @error('scan_nif')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Scan AI -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Scan de l'AI
 </label>
 <div x-data="documentUploader('ai')"
 class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors"
 @dragover.prevent
 @drop.prevent="handleDrop($event)">
 <div x-show="!preview" class="space-y-2">
 <i class="fas fa-file-upload text-2xl text-gray-400"></i>
 <p class="text-sm text-gray-600">
 <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:text-blue-700 font-semibold">
 Choisir le fichier
 </button>
 </p>
 <p class="text-xs text-gray-500">PDF, JPG, PNG (max 5MB)</p>
 </div>
 <div x-show="preview" class="space-y-2">
 <i class="fas fa-file-alt text-2xl text-green-600"></i>
 <p class="text-sm text-green-600" x-text="fileName"></p>
 <button type="button" @click="removeFile()" class="text-red-600 hover:text-red-700 text-xs">
 Supprimer
 </button>
 </div>
 <input type="file" name="scan_ai" x-ref="fileInput" @change="handleFile($event)"
 accept=".pdf,.jpg,.jpeg,.png" class="hidden">
 </div>
 @error('scan_ai')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Scan NIS -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Scan du NIS
 </label>
 <div x-data="documentUploader('nis')"
 class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors"
 @dragover.prevent
 @drop.prevent="handleDrop($event)">
 <div x-show="!preview" class="space-y-2">
 <i class="fas fa-file-upload text-2xl text-gray-400"></i>
 <p class="text-sm text-gray-600">
 <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:text-blue-700 font-semibold">
 Choisir le fichier
 </button>
 </p>
 <p class="text-xs text-gray-500">PDF, JPG, PNG (max 5MB)</p>
 </div>
 <div x-show="preview" class="space-y-2">
 <i class="fas fa-file-alt text-2xl text-green-600"></i>
 <p class="text-sm text-green-600" x-text="fileName"></p>
 <button type="button" @click="removeFile()" class="text-red-600 hover:text-red-700 text-xs">
 Supprimer
 </button>
 </div>
 <input type="file" name="scan_nis" x-ref="fileInput" @change="handleFile($event)"
 accept=".pdf,.jpg,.jpeg,.png" class="hidden">
 </div>
 @error('scan_nis')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Step 3: Contact & Adresse -->
 <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-6"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-map-marker-alt text-white"></i>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">Contact & Adresse</h2>
 <p class="text-gray-600">Coordonnées et localisation de l'organisation</p>
 </div>
 </div>

 <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
 <!-- Email -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Email <span class="text-red-500">*</span>
 </label>
 <input
 type="email"
 name="email"
 value="{{ old('email', $organization->email ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="contact@entreprise.dz"
 required
 >
 @error('email')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Téléphone -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Téléphone <span class="text-red-500">*</span>
 </label>
 <input
 type="tel"
 name="phone_number"
 value="{{ old('phone_number', $organization->phone_number ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="+213 XX XX XX XX XX"
 required
 >
 @error('phone_number')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Site Web -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Site Web
 </label>
 <input
 type="url"
 name="website"
 value="{{ old('website', $organization->website ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="https://www.entreprise.dz"
 >
 @error('website')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Wilaya with Tom Select -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Wilaya <span class="text-red-500">*</span>
 </label>
 <select
 name="wilaya"
 id="wilaya"
 class="tom-select w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 required
 data-placeholder="Recherchez et sélectionnez une wilaya"
 >
 <option value="">Sélectionnez une wilaya</option>
 @foreach([
 '01' => '01 - Adrar',
 '02' => '02 - Chlef',
 '03' => '03 - Laghouat',
 '04' => '04 - Oum El Bouaghi',
 '05' => '05 - Batna',
 '06' => '06 - Béjaïa',
 '07' => '07 - Biskra',
 '08' => '08 - Béchar',
 '09' => '09 - Blida',
 '10' => '10 - Bouira',
 '11' => '11 - Tamanrasset',
 '12' => '12 - Tébessa',
 '13' => '13 - Tlemcen',
 '14' => '14 - Tiaret',
 '15' => '15 - Tizi Ouzou',
 '16' => '16 - Alger',
 '17' => '17 - Djelfa',
 '18' => '18 - Jijel',
 '19' => '19 - Sétif',
 '20' => '20 - Saïda',
 '21' => '21 - Skikda',
 '22' => '22 - Sidi Bel Abbès',
 '23' => '23 - Annaba',
 '24' => '24 - Guelma',
 '25' => '25 - Constantine',
 '26' => '26 - Médéa',
 '27' => '27 - Mostaganem',
 '28' => '28 - M\'Sila',
 '29' => '29 - Mascara',
 '30' => '30 - Ouargla',
 '31' => '31 - Oran',
 '32' => '32 - El Bayadh',
 '33' => '33 - Illizi',
 '34' => '34 - Bordj Bou Arréridj',
 '35' => '35 - Boumerdès',
 '36' => '36 - El Tarf',
 '37' => '37 - Tindouf',
 '38' => '38 - Tissemsilt',
 '39' => '39 - El Oued',
 '40' => '40 - Khenchela',
 '41' => '41 - Souk Ahras',
 '42' => '42 - Tipaza',
 '43' => '43 - Mila',
 '44' => '44 - Aïn Defla',
 '45' => '45 - Naâma',
 '46' => '46 - Aïn Témouchent',
 '47' => '47 - Ghardaïa',
 '48' => '48 - Relizane',
 '49' => '49 - Timimoun',
 '50' => '50 - Bordj Badji Mokhtar',
 '51' => '51 - Ouled Djellal',
 '52' => '52 - Béni Abbès',
 '53' => '53 - In Salah',
 '54' => '54 - In Guezzam',
 '55' => '55 - Touggourt',
 '56' => '56 - Djanet',
 '57' => '57 - El M\'Ghair',
 '58' => '58 - El Menia'
 ] as $code => $name)
 <option value="{{ $code }}" {{ old('wilaya', $organization->wilaya ?? '') === $code ? 'selected' : '' }}>
 {{ $name }}
 </option>
 @endforeach
 </select>
 @error('wilaya')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 <!-- Adresse complète -->
 <div class="mt-8 space-y-6">
 <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
 Adresse de l'Organisation
 </h3>

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <!-- Adresse -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Adresse <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="address"
 value="{{ old('address', $organization->address ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Rue, avenue, boulevard..."
 required
 >
 @error('address')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Complément d'adresse -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Complément d'adresse
 </label>
 <input
 type="text"
 name="address_line_2"
 value="{{ old('address_line_2', $organization->address_line_2 ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Bâtiment, étage, appartement..."
 >
 @error('address_line_2')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Ville -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Ville <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="city"
 value="{{ old('city', $organization->city ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Nom de la ville"
 required
 >
 @error('city')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Code postal -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Code postal
 </label>
 <input
 type="text"
 name="zip_code"
 value="{{ old('zip_code', $organization->zip_code ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="XXXXX"
 pattern="[0-9]{5}"
 maxlength="5"
 >
 @error('zip_code')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Step 4: Responsable Légal -->
 <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 transform translate-x-6"
 x-transition:enter-end="opacity-100 transform translate-x-0">
 <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <div class="flex items-center gap-4 pb-6 border-b border-gray-100">
 <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
 <i class="fas fa-user-tie text-white"></i>
 </div>
 <div>
 <h2 class="text-2xl font-bold text-gray-900">Responsable Légal</h2>
 <p class="text-gray-600">Gérant ou représentant légal de l'organisation</p>
 </div>
 </div>

 <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
 <!-- Prénom du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Prénom du Gérant <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="manager_first_name"
 value="{{ old('manager_first_name', $organization->manager_first_name ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Prénom du gérant"
 required
 >
 @error('manager_first_name')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Nom du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Nom du Gérant <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="manager_last_name"
 value="{{ old('manager_last_name', $organization->manager_last_name ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Nom de famille du gérant"
 required
 >
 @error('manager_last_name')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- NIN du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 NIN du Gérant <span class="text-red-500">*</span>
 </label>
 <input
 type="text"
 name="manager_nin"
 value="{{ old('manager_nin', $organization->manager_nin ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="000000000000000000"
 pattern="[0-9]{18}"
 maxlength="18"
 required
 >
 @error('manager_nin')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 <p class="text-sm text-gray-500">Numéro d'Identification National (18 chiffres)</p>
 </div>

 <!-- Adresse du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Adresse du Gérant
 </label>
 <input
 type="text"
 name="manager_address"
 value="{{ old('manager_address', $organization->manager_address ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Adresse de domicile du gérant"
 >
 @error('manager_address')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Date de naissance du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Date de naissance du Gérant
 </label>
 <input
 type="date"
 name="manager_dob"
 value="{{ old('manager_dob', $organization->manager_dob ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 >
 @error('manager_dob')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Lieu de naissance du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Lieu de naissance du Gérant
 </label>
 <input
 type="text"
 name="manager_pob"
 value="{{ old('manager_pob', $organization->manager_pob ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="Lieu de naissance"
 >
 @error('manager_pob')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>

 <!-- Téléphone du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Téléphone du Gérant
 </label>
 <input
 type="tel"
 name="manager_phone_number"
 value="{{ old('manager_phone_number', $organization->manager_phone_number ?? '') }}"
 class="w-full px-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all"
 placeholder="+213 XX XX XX XX XX"
 >
 @error('manager_phone_number')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>

 <!-- Documents du Responsable -->
 <div class="mt-8 space-y-6">
 <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">
 Documents du Responsable Légal
 </h3>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <!-- Scan Pièce d'Identité du Responsable -->
 <div class="space-y-3">
 <label class="block mb-2 text-sm font-medium text-gray-600">
 Scan Pièce d'Identité du Gérant
 </label>
 <div x-data="documentUploader('manager_id')"
 class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors"
 @dragover.prevent
 @drop.prevent="handleDrop($event)">
 <div x-show="!preview" class="space-y-2">
 <i class="fas fa-id-card text-2xl text-gray-400"></i>
 <p class="text-sm text-gray-600">
 <button type="button" @click="$refs.fileInput.click()" class="text-blue-600 hover:text-blue-700 font-semibold">
 Choisir le fichier
 </button>
 </p>
 <p class="text-xs text-gray-500">PDF, JPG, PNG (max 5MB)</p>
 </div>
 <div x-show="preview" class="space-y-2">
 <i class="fas fa-file-alt text-2xl text-green-600"></i>
 <p class="text-sm text-green-600" x-text="fileName"></p>
 <button type="button" @click="removeFile()" class="text-red-600 hover:text-red-700 text-xs">
 Supprimer
 </button>
 </div>
 <input type="file" name="manager_id_scan_path" x-ref="fileInput" @change="handleFile($event)"
 accept=".pdf,.jpg,.jpeg,.png" class="hidden">
 </div>
 @error('manager_id_scan_path')
 <p class="text-sm text-red-600 flex items-center gap-2">
 <i class="fas fa-exclamation-triangle"></i> {{ $message }}
 </p>
 @enderror
 </div>
 </div>
 </div>
 </div>
 </div>

 <!-- Navigation Buttons -->
 <div class="flex items-center justify-between p-6 bg-white/95 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100">
 <!-- Back Button -->
 <button type="button" @click="prevStep()" x-show="currentStep > 0"
 class="inline-flex items-center gap-3 px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-sm">
 <i class="fas fa-arrow-left"></i>
 <span>Précédent</span>
 </button>
 <div x-show="currentStep === 0"></div>

 <!-- Next/Submit Button -->
 <div class="flex items-center gap-4">
 <a href="{{ route('admin.organizations.index') }}"
 class="inline-flex items-center gap-3 px-6 py-3 text-gray-600 hover:text-gray-800 font-semibold transition-colors">
 <i class="fas fa-times"></i>
 <span>Annuler</span>
 </a>

 <button type="button" @click="nextStep()" x-show="currentStep < steps.length - 1"
 class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
 <span>Continuer</span>
 <i class="fas fa-arrow-right"></i>
 </button>

 <button type="submit" x-show="currentStep === steps.length - 1"
 class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md"
 :disabled="submitting"
 x-bind:class="{ 'opacity-50 cursor-not-allowed': submitting }">
 <i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
 <span x-text="submitting ? 'Traitement...' : '{{ $isEdit ? 'Mettre à jour' : 'Créer l\'organisation' }}'"></span>
 </button>
 </div>
 </div>
 </form>
 </div>
</div>

<!-- Scripts -->
@push('scripts')
<script>
function organizationWizard(existingOrg = null, isEdit = false) {
 return {
 currentStep: 0,
 highestStep: 0,
 submitting: false,
 existingOrg: existingOrg,
 isEdit: isEdit,

 steps: [
 {
 title: 'Informations',
 description: 'Identité de base',
 icon: 'fa-info-circle'
 },
 {
 title: 'Légal',
 description: 'Données officielles',
 icon: 'fa-file-contract'
 },
 {
 title: 'Contact',
 description: 'Coordonnées',
 icon: 'fa-map-marker-alt'
 },
 {
 title: 'Responsable',
 description: 'Gérant légal',
 icon: 'fa-user-tie'
 }
 ],

 validateStep(stepIndex) {
 const form = document.getElementById('organization-form');
 const currentStepElement = form.querySelector(`[x-show="currentStep === ${stepIndex}"]`);
 if (!currentStepElement) return true;

 const requiredInputs = currentStepElement.querySelectorAll('[required]');
 let isValid = true;

 requiredInputs.forEach(input => {
 if (!input.value.trim()) {
 isValid = false;
 input.focus();

 // Show visual feedback
 input.classList.add('border-red-300', 'bg-red-50');
 setTimeout(() => {
 input.classList.remove('border-red-300', 'bg-red-50');
 }, 3000);
 }
 });

 if (!isValid) {
 // Show toast notification
 this.showToast('Veuillez remplir tous les champs obligatoires', 'error');
 }

 return isValid;
 },

 nextStep() {
 if (!this.validateStep(this.currentStep)) return;

 if (this.currentStep < this.steps.length - 1) {
 this.currentStep++;
 if (this.currentStep > this.highestStep) {
 this.highestStep = this.currentStep;
 }
 this.scrollToTop();
 }
 },

 prevStep() {
 if (this.currentStep > 0) {
 this.currentStep--;
 this.scrollToTop();
 }
 },

 goToStep(index) {
 if (index <= this.highestStep) {
 this.currentStep = index;
 this.scrollToTop();
 }
 },

 scrollToTop() {
 window.scrollTo({ top: 0, behavior: 'smooth' });
 },

 showToast(message, type = 'info') {
 // Simple toast implementation
 const toast = document.createElement('div');
 toast.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 transition-all duration-300 ${
 type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
 }`;
 toast.textContent = message;

 document.body.appendChild(toast);

 setTimeout(() => {
 toast.style.opacity = '0';
 toast.style.transform = 'translateX(100%)';
 setTimeout(() => toast.remove(), 300);
 }, 3000);
 },

 submitForm() {
 this.submitting = true;
 document.getElementById('organization-form').submit();
 }
 };
}

function fileUploader() {
 return {
 preview: null,
 existingLogo: false,

 handleFile(event) {
 const file = event.target.files[0];
 if (file) {
 if (file.size > 2 * 1024 * 1024) { // 2MB limit
 alert('Le fichier est trop volumineux. Taille maximum: 2MB');
 return;
 }
 this.preview = URL.createObjectURL(file);
 }
 },

 handleDrop(event) {
 const file = event.dataTransfer.files[0];
 if (file && file.type.startsWith('image/')) {
 if (file.size > 2 * 1024 * 1024) {
 alert('Le fichier est trop volumineux. Taille maximum: 2MB');
 return;
 }
 this.$refs.fileInput.files = event.dataTransfer.files;
 this.preview = URL.createObjectURL(file);
 }
 },

 removeFile() {
 this.preview = null;
 this.$refs.fileInput.value = '';
 }
 };
}

function documentUploader(documentType) {
 return {
 preview: false,
 fileName: '',
 documentType: documentType,

 handleFile(event) {
 const file = event.target.files[0];
 if (file) {
 if (file.size > 5 * 1024 * 1024) { // 5MB limit for documents
 alert('Le fichier est trop volumineux. Taille maximum: 5MB');
 return;
 }
 this.preview = true;
 this.fileName = file.name;
 }
 },

 handleDrop(event) {
 const file = event.dataTransfer.files[0];
 if (file) {
 if (file.size > 5 * 1024 * 1024) {
 alert('Le fichier est trop volumineux. Taille maximum: 5MB');
 return;
 }
 this.$refs.fileInput.files = event.dataTransfer.files;
 this.preview = true;
 this.fileName = file.name;
 }
 },

 removeFile() {
 this.preview = false;
 this.fileName = '';
 this.$refs.fileInput.value = '';
 }
 };
}

// Initialize Tom Select components
document.addEventListener('DOMContentLoaded', function() {
 // Auto-format NIF/AI/NIS inputs
 const nifInput = document.querySelector('input[name="nif"]');
 if (nifInput) {
 nifInput.addEventListener('input', function() {
 this.value = this.value.replace(/\D/g, '').slice(0, 15);
 });
 }

 // Initialize Tom Select for Organization Type
 if (document.getElementById('organization_type')) {
 new TomSelect('#organization_type', {
 placeholder: 'Sélectionnez un type d\'organisation',
 allowEmptyOption: true,
 searchField: ['text'],
 render: {
 option: function(data, escape) {
 return '<div class="flex items-center py-2 px-3 hover:bg-blue-50 transition-colors">' +
 '<div class="flex-1">' +
 '<div class="font-medium text-gray-900">' + escape(data.text) + '</div>' +
 '</div>' +
 '</div>';
 }
 }
 });
 }

 // Initialize Tom Select for Industry (with create option)
 if (document.getElementById('industry')) {
 new TomSelect('#industry', {
 placeholder: 'Sélectionnez ou saisissez un secteur d\'activité',
 allowEmptyOption: true,
 create: true,
 searchField: ['text'],
 render: {
 option: function(data, escape) {
 return '<div class="flex items-center py-2 px-3 hover:bg-blue-50 transition-colors">' +
 '<div class="w-8 h-8 bg-blue-100 border border-blue-200 rounded-full flex items-center justify-center mr-3">' +
 '<i class="fas fa-industry text-blue-600 text-sm"></i>' +
 '</div>' +
 '<div class="flex-1">' +
 '<div class="font-medium text-gray-900">' + escape(data.text) + '</div>' +
 '</div>' +
 '</div>';
 },
 item: function(data, escape) {
 return '<div class="flex items-center">' +
 '<i class="fas fa-industry text-blue-600 text-sm mr-2"></i>' +
 '<span>' + escape(data.text) + '</span>' +
 '</div>';
 }
 }
 });
 }

 // Initialize Tom Select for Wilaya (with search)
 if (document.getElementById('wilaya')) {
 new TomSelect('#wilaya', {
 placeholder: 'Recherchez et sélectionnez une wilaya',
 allowEmptyOption: true,
 searchField: ['text'],
 render: {
 option: function(data, escape) {
 return '<div class="flex items-center py-2 px-3 hover:bg-blue-50 transition-colors">' +
 '<div class="w-8 h-8 bg-green-100 border border-green-200 rounded-full flex items-center justify-center mr-3">' +
 '<i class="fas fa-map-marker-alt text-green-600 text-sm"></i>' +
 '</div>' +
 '<div class="flex-1">' +
 '<div class="font-medium text-gray-900">' + escape(data.text) + '</div>' +
 '</div>' +
 '</div>';
 },
 item: function(data, escape) {
 return '<div class="flex items-center">' +
 '<i class="fas fa-map-marker-alt text-green-600 text-sm mr-2"></i>' +
 '<span>' + escape(data.text) + '</span>' +
 '</div>';
 }
 }
 });
 }
});
</script>
@endpush