@extends('layouts.admin.catalyst')
@section('title', 'Nouveau Chauffeur - ZenFleet Enterprise')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    <div x-data="{
            currentStep: {{ old('current_step', 1) }},
            photoPreview: null,
            formData: {},

            updatePhotoPreview(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.photoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

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
                        'first_name': 1, 'last_name': 1, 'birth_date': 1, 'personal_phone': 1, 'address': 1,
                        'blood_type': 1, 'personal_email': 1, 'photo': 1,
                        'employee_number': 2, 'recruitment_date': 2, 'contract_end_date': 2, 'status_id': 2,
                        'license_number': 3, 'license_category': 3, 'license_issue_date': 3, 'license_authority': 3,
                        'user_id': 4, 'emergency_contact_name': 4, 'emergency_contact_phone': 4
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
                    <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
                        Gestion des Chauffeurs
                    </a>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-900">Nouveau Chauffeur</span>
                </nav>

                <!-- Hero Content -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-plus text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-gray-900">Nouveau Chauffeur</h1>
                            <p class="text-gray-600 text-lg mt-2">
                                Ajout d'un nouveau chauffeur à votre flotte
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
                            <div x-ref="progressBar" class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500 ease-out"></div>
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
                                     :class="currentStep >= 1 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 1 ? 'text-emerald-600' : 'text-gray-400'">
                                        Informations Personnelles
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 1 ? 'bg-emerald-500' : 'bg-gray-300'"></div>

                            <!-- Step 2 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 2 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 2 ? 'text-emerald-600' : 'text-gray-400'">
                                        Informations Professionnelles
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 2 ? 'bg-emerald-500' : 'bg-gray-300'"></div>

                            <!-- Step 3 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 3 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 3 ? 'text-emerald-600' : 'text-gray-400'">
                                        Permis de Conduire
                                    </div>
                                </div>
                            </div>

                            <div class="w-8 h-0.5 bg-gray-300" :class="currentStep > 3 ? 'bg-emerald-500' : 'bg-gray-300'"></div>

                            <!-- Step 4 -->
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                     :class="currentStep >= 4 ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-200 text-gray-400'">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm font-semibold" :class="currentStep >= 4 ? 'text-emerald-600' : 'text-gray-400'">
                                        Compte & Contact d'Urgence
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                    <form id="driverCreateForm" method="POST" action="{{ route('admin.drivers.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        <input type="hidden" name="current_step" x-model="currentStep">

                        <!-- 👤 STEP 1: Informations Personnelles -->
                        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Personnelles</h3>
                                <p class="text-gray-600">Renseignez les informations personnelles du chauffeur</p>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                <!-- Photo Section -->
                                <div class="lg:col-span-1">
                                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                                        <label for="photo" class="block text-sm font-semibold text-gray-700 mb-4">
                                            <i class="fas fa-camera text-gray-400 mr-2"></i>Photo de Profil
                                        </label>

                                        <div class="mb-4">
                                            <div x-show="!photoPreview" class="w-32 h-32 mx-auto bg-gray-200 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-400 text-4xl"></i>
                                            </div>
                                            <img x-show="photoPreview" :src="photoPreview" class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-white shadow-lg">
                                        </div>

                                        <input id="photo" name="photo" type="file" @change="updatePhotoPreview($event)"
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all">

                                        @error('photo')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Personal Information -->
                                <div class="lg:col-span-2 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-user text-gray-400 mr-2"></i>Prénom *
                                            </label>
                                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('first_name')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-user text-gray-400 mr-2"></i>Nom *
                                            </label>
                                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('last_name')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-calendar text-gray-400 mr-2"></i>Date de Naissance
                                            </label>
                                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('birth_date')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="blood_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-tint text-gray-400 mr-2"></i>Groupe Sanguin
                                            </label>
                                            <input type="text" id="blood_type" name="blood_type" value="{{ old('blood_type') }}"
                                                   placeholder="Ex: O+"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('blood_type')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="personal_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone Personnel
                                            </label>
                                            <input type="tel" id="personal_phone" name="personal_phone" value="{{ old('personal_phone') }}"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('personal_phone')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="personal_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Personnel
                                            </label>
                                            <input type="email" id="personal_email" name="personal_email" value="{{ old('personal_email') }}"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('personal_email')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Adresse Complète
                                        </label>
                                        <textarea id="address" name="address" rows="3"
                                                  class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">{{ old('address') }}</textarea>
                                        @error('address')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 💼 STEP 2: Informations Professionnelles -->
                        <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Informations Professionnelles</h3>
                                <p class="text-gray-600">Définissez le statut et les informations d'emploi</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="employee_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-id-badge text-gray-400 mr-2"></i>Matricule Employé
                                    </label>
                                    <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('employee_number')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @php
                                    $statusesData = [];
                                    if (isset($driverStatuses) && $driverStatuses && $driverStatuses->isNotEmpty()) {
                                        $statusesData = $driverStatuses->map(function($status) {
                                            return [
                                                'id' => (int) $status->id,
                                                'name' => (string) $status->name,
                                                'description' => (string) ($status->description ?? ''),
                                                'color' => (string) ($status->color ?? '#6B7280'),
                                                'icon' => (string) ($status->icon ?? 'fa-circle'),
                                                'can_drive' => (bool) ($status->can_drive ?? true),
                                                'can_assign' => (bool) ($status->can_assign ?? true)
                                            ];
                                        })->values()->toArray();
                                    }
                                @endphp

                                <!-- 🎯 Statut Chauffeur - Design Enterprise Ultra Moderne -->
                                <div x-data="{
                                    open: false,
                                    selectedStatus: null,
                                    selectedId: '{{ old('status_id') }}',
                                    statuses: @js($statusesData)
                                }" x-init="
                                    console.log('Statuses available:', statuses.length, statuses);
                                    if (selectedId && statuses.length > 0) {
                                        selectedStatus = statuses.find(s => s.id == selectedId);
                                        console.log('Pre-selected status:', selectedStatus);
                                    }
                                " class="relative">

                                    <label for="status_id" class="block text-sm font-semibold text-gray-700 mb-3">
                                        <i class="fas fa-user-check text-emerald-500 mr-2"></i>
                                        Statut du Chauffeur <span class="text-red-500">*</span>
                                    </label>

                                    <!-- Hidden Select for Form Submission -->
                                    <input type="hidden" name="status_id" :value="selectedId" required>

                                    <!-- Custom Dropdown Button -->
                                    <button type="button" @click="open = !open" @click.away="open = false"
                                            class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all duration-200 flex items-center justify-between hover:border-emerald-300 group">

                                        <div class="flex items-center" x-show="selectedStatus">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium shadow-sm"
                                                     :style="`background-color: ${selectedStatus?.color || '#6B7280'}`">
                                                    <i :class="selectedStatus?.icon || 'fas fa-circle'" class="text-xs"></i>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-semibold text-gray-900" x-text="selectedStatus?.name"></div>
                                                    <div class="text-xs text-gray-500" x-text="selectedStatus?.description" x-show="selectedStatus?.description"></div>
                                                </div>
                                                <div class="flex gap-1 ml-auto">
                                                    <span x-show="selectedStatus?.can_drive" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-car text-green-600 mr-1"></i> Conduite
                                                    </span>
                                                    <span x-show="selectedStatus?.can_assign" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-tasks text-blue-600 mr-1"></i> Missions
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="!selectedStatus" class="text-gray-500">
                                            <i class="fas fa-user-plus mr-2"></i> Sélectionnez un statut
                                        </div>

                                        <i class="fas fa-chevron-down transition-transform duration-200 text-gray-400 group-hover:text-emerald-500"
                                           :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute z-50 w-full mt-2 bg-white border-2 border-gray-100 rounded-xl shadow-xl overflow-hidden">

                                        @if($driverStatuses && $driverStatuses->isNotEmpty())
                                            <div class="max-h-80 overflow-y-auto">
                                                <template x-for="status in statuses" :key="status.id">
                                                    <button type="button" @click="selectedStatus = status; selectedId = status.id; open = false"
                                                            class="w-full px-4 py-4 text-left hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 transition-all duration-200 border-b border-gray-50 last:border-0 group"
                                                            :class="{ 'bg-emerald-50 border-l-4 border-l-emerald-500': selectedId == status.id }">

                                                        <div class="flex items-center gap-4">
                                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium shadow-md group-hover:shadow-lg transition-shadow"
                                                                 :style="`background-color: ${status.color}`">
                                                                <i :class="status.icon" class="text-sm"></i>
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="font-semibold text-gray-900" x-text="status.name"></div>
                                                                <div class="text-sm text-gray-600" x-text="status.description" x-show="status.description"></div>
                                                                <div class="flex gap-2 mt-2">
                                                                    <span x-show="status.can_drive" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <i class="fas fa-car text-green-600 mr-1"></i> Conduite
                                                                    </span>
                                                                    <span x-show="status.can_assign" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                        <i class="fas fa-tasks text-blue-600 mr-1"></i> Missions
                                                                    </span>
                                                                    <span x-show="!status.can_drive || !status.can_assign" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-1"></i> Limité
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <i class="fas fa-check text-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                                               :class="{ 'opacity-100': selectedId == status.id }"></i>
                                                        </div>
                                                    </button>
                                                </template>
                                            </div>
                                        @else
                                            <div class="px-4 py-8 text-center">
                                                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-exclamation-circle text-gray-400 text-2xl"></i>
                                                </div>
                                                <p class="text-gray-500 font-medium">Aucun statut disponible</p>
                                                <p class="text-gray-400 text-sm mt-1">Contactez votre administrateur</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Validation Error -->
                                    @error('status_id')
                                        <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <p class="text-sm text-red-600 flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                {{ $message }}
                                            </p>
                                        </div>
                                    @enderror

                                    <!-- Info Helper -->
                                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-xs text-blue-700 flex items-center">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Le statut détermine les permissions et capacités du chauffeur dans le système.
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <label for="recruitment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>Date de Recrutement
                                    </label>
                                    <input type="date" id="recruitment_date" name="recruitment_date" value="{{ old('recruitment_date') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('recruitment_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contract_end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-times text-gray-400 mr-2"></i>Date de Fin de Contrat
                                    </label>
                                    <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('contract_end_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 🆔 STEP 3: Permis de Conduire -->
                        <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Permis de Conduire</h3>
                                <p class="text-gray-600">Informations sur le permis de conduire du chauffeur</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="license_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-id-card text-gray-400 mr-2"></i>Numéro de Permis
                                    </label>
                                    <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('license_number')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="license_category" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-certificate text-gray-400 mr-2"></i>Catégorie(s)
                                    </label>
                                    <input type="text" id="license_category" name="license_category" value="{{ old('license_category') }}"
                                           placeholder="Ex: B, C1E"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('license_category')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="license_issue_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>Date de Délivrance
                                    </label>
                                    <input type="date" id="license_issue_date" name="license_issue_date" value="{{ old('license_issue_date') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('license_issue_date')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="license_authority" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-building text-gray-400 mr-2"></i>Autorité de Délivrance
                                    </label>
                                    <input type="text" id="license_authority" name="license_authority" value="{{ old('license_authority') }}"
                                           class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                    @error('license_authority')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- 🔗 STEP 4: Compte Utilisateur & Contact d'Urgence -->
                        <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Compte Utilisateur & Contact d'Urgence</h3>
                                <p class="text-gray-600">Liez le chauffeur à un compte utilisateur et définissez un contact d'urgence</p>
                            </div>

                            <div class="space-y-8">
                                <!-- User Account Section -->
                                <div class="bg-blue-50 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4">
                                        <i class="fas fa-user-circle mr-2"></i>Compte Utilisateur (Optionnel)
                                    </h4>
                                    <div>
                                        <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Lier à un compte utilisateur existant
                                        </label>
                                        <select id="user_id" name="user_id"
                                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all appearance-none">
                                            <option value="">Ne pas lier de compte</option>
                                            @foreach($linkableUsers as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-2 text-sm text-blue-600">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Si aucun compte n'est lié, un compte sera automatiquement créé avec l'email personnel.
                                        </p>
                                    </div>
                                </div>

                                <!-- Emergency Contact Section -->
                                <div class="bg-red-50 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-red-900 mb-4">
                                        <i class="fas fa-phone-square-alt mr-2"></i>Contact d'Urgence
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="emergency_contact_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-user text-gray-400 mr-2"></i>Nom du Contact
                                            </label>
                                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('emergency_contact_name')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="emergency_contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <i class="fas fa-phone text-gray-400 mr-2"></i>Téléphone d'Urgence
                                            </label>
                                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                                   class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-50 transition-all">
                                            @error('emergency_contact_phone')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
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
                                <a href="{{ route('admin.drivers.index') }}"
                                   class="text-gray-600 hover:text-gray-900 font-semibold transition-colors">
                                    Annuler
                                </a>

                                <button type="button" @click="nextStep()" x-show="currentStep < 4"
                                        class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                    <span>Suivant</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>

                                <button type="submit" x-show="currentStep === 4"
                                        class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Créer le Chauffeur</span>
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
        <button type="submit" form="driverCreateForm"
                class="inline-flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-2xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-plus text-lg"></i>
            <span>Créer</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TomSelect for enhanced dropdowns
    if (typeof TomSelect !== 'undefined') {
        // Status dropdown
        const statusSelect = document.getElementById('status_id');
        if (statusSelect) {
            new TomSelect(statusSelect, {
                create: false,
                placeholder: 'Sélectionnez un statut...',
                searchField: ['text', 'value'],
                render: {
                    option: function(data, escape) {
                        return '<div class="flex items-center gap-2 py-2">' +
                               '<i class="fas fa-circle text-xs"></i>' +
                               '<span>' + escape(data.text) + '</span>' +
                               '</div>';
                    }
                }
            });
        }

        // User dropdown
        const userSelect = document.getElementById('user_id');
        if (userSelect) {
            new TomSelect(userSelect, {
                create: false,
                placeholder: 'Rechercher un utilisateur...',
                searchField: ['text', 'value'],
                render: {
                    option: function(data, escape) {
                        return '<div class="py-2">' +
                               '<div class="font-semibold">' + escape(data.text.split(' (')[0]) + '</div>' +
                               '<div class="text-sm text-gray-500">' + escape(data.text.split(' (')[1]?.replace(')', '') || '') + '</div>' +
                               '</div>';
                    }
                }
            });
        }
    }
});
</script>
@endsection