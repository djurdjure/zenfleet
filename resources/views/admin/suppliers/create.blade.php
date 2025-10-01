@extends('layouts.admin.catalyst')
@section('title', 'Nouveau Fournisseur - ZenFleet Enterprise')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    <div x-data="{
            currentStep: {{ old('current_step', 1) }},
            formData: {},

            nextStep() {
                if (this.currentStep < 4) {
                    this.currentStep++;
                    this.updateProgressBar();
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    this.updateProgressBar();
                }
            },

            updateProgressBar() {
                const progress = (this.currentStep / 4) * 100;
                const progressBar = this.$refs.progressBar;
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
            },

            init() {
                this.updateProgressBar();
                @if ($errors->any())
                    const fieldToStepMap = {
                        'company_name': 1, 'supplier_type': 1, 'trade_register': 1, 'nif': 1,
                        'contact_first_name': 2, 'contact_last_name': 2, 'contact_phone': 2, 'contact_email': 2,
                        'address': 3, 'wilaya': 3, 'city': 3, 'commune': 3,
                        'rating': 4, 'notes': 4
                    };

                    const errors = @json($errors->keys());
                    let firstErrorStep = null;

                    for (const field of errors) {
                        if (fieldToStepMap[field]) {
                            firstErrorStep = fieldToStepMap[field];
                            break;
                        }
                    }

                    if (firstErrorStep) {
                        this.currentStep = firstErrorStep;
                        this.updateProgressBar();
                    }
                @endif
            }
        }" x-init="init()" class="space-y-8">

        <!-- 🎨 Enterprise Header Section -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <a href="{{ route('admin.suppliers.index') }}" class="hover:text-blue-600 transition-colors">
                        Gestion des Fournisseurs
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-900">Nouveau Fournisseur</span>
                </nav>

                <!-- Hero Content -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Nouveau Fournisseur</h1>
                            <p class="text-gray-600 text-lg mt-2">
                                Ajout d'un nouveau fournisseur à votre réseau
                            </p>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="text-right">
                        <div class="text-sm text-gray-500 mb-2">Étape <span x-text="currentStep"></span> sur 4</div>
                        <div class="text-xs text-blue-600 mb-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Utilisez les boutons "Suivant/Précédent" ou le bouton vert flottant pour créer
                        </div>
                        <div class="w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div x-ref="progressBar" class="h-full bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-500 ease-out"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📋 Form Section -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Step Indicator -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <!-- Step 1 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 1 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 1 ? 'text-blue-600' : 'text-gray-400'">
                                        Informations Générales
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 1 ? 'bg-blue-500' : 'bg-gray-300'"></div>

                            <!-- Step 2 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-400'">
                                        Contact Principal
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 2 ? 'bg-blue-500' : 'bg-gray-300'"></div>

                            <!-- Step 3 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 3 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-400'">
                                        Localisation
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 3 ? 'bg-blue-500' : 'bg-gray-300'"></div>

                            <!-- Step 4 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 4 ? 'text-blue-600' : 'text-gray-400'">
                                        Paramètres & Notes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                    <form id="supplierCreateForm" method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-8">
                        @csrf
                        <input type="hidden" name="current_step" x-model="currentStep">

                        <!-- 🏢 STEP 1: Informations Générales -->
                        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Générales</h3>
                                <p class="text-gray-600">Renseignez les informations de base du fournisseur</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-building text-gray-400 mr-2"></i>Nom de l'entreprise *
                                    </label>
                                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('company_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="supplier_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-tags text-gray-400 mr-2"></i>Type de fournisseur *
                                    </label>
                                    <select id="supplier_type" name="supplier_type" required
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                                        <option value="">Sélectionner un type</option>
                                        @foreach(App\Models\Supplier::getSupplierTypes() as $key => $label)
                                            <option value="{{ $key }}" {{ old('supplier_type') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="trade_register" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-id-card text-gray-400 mr-2"></i>Registre du commerce
                                    </label>
                                    <input type="text" id="trade_register" name="trade_register" value="{{ old('trade_register') }}"
                                           pattern="[0-9]{2}/[0-9]{2}-[0-9]{7}" placeholder="Ex: 16/00-9876543"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('trade_register')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nif" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-hashtag text-gray-400 mr-2"></i>NIF
                                    </label>
                                    <input type="text" id="nif" name="nif" value="{{ old('nif') }}"
                                           pattern="[0-9]{15}" placeholder="15 chiffres"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('nif')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 👤 STEP 2: Contact Principal -->
                        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Contact Principal</h3>
                                <p class="text-gray-600">Personne de contact chez le fournisseur</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user text-gray-400 mr-2"></i>Prénom *
                                    </label>
                                    <input type="text" id="contact_first_name" name="contact_first_name" value="{{ old('contact_first_name') }}" required
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('contact_first_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user text-gray-400 mr-2"></i>Nom *
                                    </label>
                                    <input type="text" id="contact_last_name" name="contact_last_name" value="{{ old('contact_last_name') }}" required
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('contact_last_name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone *
                                    </label>
                                    <input type="tel" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('contact_phone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contact_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>Email
                                    </label>
                                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                    @error('contact_email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 📍 STEP 3: Localisation -->
                        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Localisation</h3>
                                <p class="text-gray-600">Adresse et localisation du fournisseur</p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Adresse complète *
                                    </label>
                                    <textarea id="address" name="address" rows="3" required
                                              class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="wilaya" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-map text-gray-400 mr-2"></i>Wilaya *
                                        </label>
                                        <select id="wilaya" name="wilaya" required
                                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all appearance-none">
                                            <option value="">Sélectionner une wilaya</option>
                                            @foreach(App\Models\Supplier::WILAYAS as $code => $name)
                                                <option value="{{ $code }}" {{ old('wilaya') === $code ? 'selected' : '' }}>
                                                    {{ $code }} - {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('wilaya')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-city text-gray-400 mr-2"></i>Ville *
                                        </label>
                                        <input type="text" id="city" name="city" value="{{ old('city') }}" required
                                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                        @error('city')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="commune" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-building text-gray-400 mr-2"></i>Commune
                                        </label>
                                        <input type="text" id="commune" name="commune" value="{{ old('commune') }}"
                                               class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                        @error('commune')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ⚙️ STEP 4: Paramètres & Notes -->
                        <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Paramètres & Notes</h3>
                                <p class="text-gray-600">Configuration et informations complémentaires</p>
                            </div>

                            <div class="space-y-8">
                                <!-- Parameters Section -->
                                <div class="bg-blue-50 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4">
                                        <i class="fas fa-cog mr-2"></i>Paramètres
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label for="rating" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-star text-gray-400 mr-2"></i>Note (0-10)
                                            </label>
                                            <input type="number" id="rating" name="rating" value="{{ old('rating', 5) }}"
                                                   min="0" max="10" step="0.1"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">
                                            @error('rating')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <label class="flex items-center space-x-3">
                                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-medium text-gray-700">Actif</span>
                                                </label>

                                                <label class="flex items-center space-x-3">
                                                    <input type="checkbox" name="is_preferred" value="1" {{ old('is_preferred') ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-medium text-gray-700">Privilégié</span>
                                                </label>

                                                <label class="flex items-center space-x-3">
                                                    <input type="checkbox" name="is_certified" value="1" {{ old('is_certified') ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-medium text-gray-700">Certifié</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="bg-yellow-50 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-yellow-900 mb-4">
                                        <i class="fas fa-sticky-note mr-2"></i>Notes internes
                                    </h4>
                                    <div>
                                        <textarea id="notes" name="notes" rows="4"
                                                  placeholder="Notes internes, commentaires, observations..."
                                                  class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-50 transition-all">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <button type="button" @click="prevStep()" x-show="currentStep > 1"
                                    class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200">
                                <i class="fas fa-arrow-left"></i>
                                <span>Précédent</span>
                            </button>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.suppliers.index') }}"
                                   class="text-gray-600 hover:text-gray-900 font-semibold transition-colors">
                                    Annuler
                                </a>

                                <button type="button" @click="nextStep()" x-show="currentStep < 4"
                                        class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                    <span>Suivant</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>

                                <button type="submit" x-show="currentStep === 4"
                                        class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-building"></i>
                                    <span>Créer le Fournisseur</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton de création flottant pour une meilleure UX -->
    <div class="fixed bottom-6 right-6 z-50">
        <button type="submit" form="supplierCreateForm"
                class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white rounded-2xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-plus text-lg"></i>
            <span>Créer</span>
        </button>
    </div>
</div>
@endsection