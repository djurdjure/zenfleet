@extends('layouts.admin.catalyst')
@section('title', 'Nouveau Chauffeur - ZenFleet Enterprise')

@push('styles')
<style>
 [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">

 {{-- Alpine.js Component --}}
 <div x-cloak
 x-data="driverCreateFormComponent()"
 x-init="init()"
 class="space-y-8">

 {{-- 🎨 Enterprise Header Section --}}
 <div class="max-w-5xl mx-auto">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 {{-- Breadcrumb --}}
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

 {{-- Hero Content --}}
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-user-plus text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">Nouveau Chauffeur</h1>
 <p class="text-gray-600 text-lg mt-2">
 Ajout d'un nouveau chauffeur à votre flotte
 </p>
 </div>
 </div>

 {{-- Progress Info --}}
 <div class="bg-blue-50 rounded-xl p-4 text-center min-w-[200px]">
 <div class="text-sm font-semibold text-gray-900 mb-2">
 Étape <span x-text="currentStep"></span> sur 4
 </div>
 <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
 <div x-ref="progressBar" class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-500 ease-out"></div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- 📋 Form Section --}}
 <div class="max-w-5xl mx-auto">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

 {{-- Step Indicator --}}
 <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 {{-- Step 1 --}}
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
 :class="currentStep >= 1 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
 <i class="fas fa-user"></i>
 </div>
 <div class="hidden md:block">
 <div class="text-sm font-semibold" :class="currentStep >= 1 ? 'text-blue-600' : 'text-gray-400'">
 Informations Personnelles
 </div>
 </div>
 </div>

 <div class="w-8 h-0.5" :class="currentStep > 1 ? 'bg-blue-500' : 'bg-gray-300'"></div>

 {{-- Step 2 --}}
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
 :class="currentStep >= 2 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
 <i class="fas fa-briefcase"></i>
 </div>
 <div class="hidden md:block">
 <div class="text-sm font-semibold" :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-400'">
 Informations Professionnelles
 </div>
 </div>
 </div>

 <div class="w-8 h-0.5" :class="currentStep > 2 ? 'bg-blue-500' : 'bg-gray-300'"></div>

 {{-- Step 3 --}}
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
 :class="currentStep >= 3 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
 <i class="fas fa-id-card"></i>
 </div>
 <div class="hidden md:block">
 <div class="text-sm font-semibold" :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-400'">
 Permis de Conduire
 </div>
 </div>
 </div>

 <div class="w-8 h-0.5" :class="currentStep > 3 ? 'bg-blue-500' : 'bg-gray-300'"></div>

 {{-- Step 4 --}}
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
 :class="currentStep >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'">
 <i class="fas fa-link"></i>
 </div>
 <div class="hidden md:block">
 <div class="text-sm font-semibold" :class="currentStep >= 4 ? 'text-blue-600' : 'text-gray-400'">
 Compte & Contact d'Urgence
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Form Content --}}
 <div class="p-8">
 <form id="driverCreateForm" method="POST" action="{{ route('admin.drivers.store') }}" enctype="multipart/form-data" class="space-y-8">
 @csrf
 <input type="hidden" name="current_step" x-model="currentStep">

 {{-- 👤 STEP 1: Informations Personnelles --}}
 <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0">
 @include('admin.drivers.partials.step1-personal', ['driver' => null])
 </div>

 {{-- 💼 STEP 2: Informations Professionnelles --}}
 <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
 @include('admin.drivers.partials.step2-professional', ['driver' => null])
 </div>

 {{-- 🆔 STEP 3: Permis de Conduire --}}
 <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
 @include('admin.drivers.partials.step3-license', ['driver' => null])
 </div>

 {{-- 🔗 STEP 4: Compte Utilisateur & Contact d'Urgence --}}
 <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-10" x-transition:enter-end="opacity-100 transform translate-x-0" style="display: none;">
 @include('admin.drivers.partials.step4-account', ['driver' => null])
 </div>

 {{-- Navigation Buttons --}}
 <div class="flex items-center justify-between pt-8 border-t border-gray-200">
 <button type="button"
 @click="prevStep()"
 x-show="currentStep > 1"
 class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200">
 <i class="fas fa-arrow-left"></i>
 <span>Précédent</span>
 </button>

 <div class="flex items-center gap-4">
 <a href="{{ route('admin.drivers.index') }}"
 class="text-gray-600 hover:text-gray-900 font-semibold transition-colors">
 Annuler
 </a>

 <button type="button"
 @click="nextStep()"
 x-show="currentStep < 4"
 class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md">
 <span>Suivant</span>
 <i class="fas fa-arrow-right"></i>
 </button>

 <button type="submit"
 x-show="currentStep === 4"
 x-cloak
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
</div>
@endsection

@push('scripts')
<script>
function driverCreateFormComponent() {
 return {
 currentStep: {{ old('current_step', 1) }},
 photoPreview: null,
 errors: {
 first_name: '',
 last_name: '',
 status_id: ''
 },
 touched: {
 first_name: false,
 last_name: false,
 status_id: false
 },

 init() {
 this.updateProgressBar();

 @if ($errors->any())
 this.handleValidationErrors();
 @endif
 },

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

 validateField(fieldName, value) {
 this.touched[fieldName] = true;

 switch(fieldName) {
 case 'first_name':
 if (!value || value.trim() === '') {
 this.errors.first_name = 'Le prénom est obligatoire';
 } else if (value.trim().length < 2) {
 this.errors.first_name = 'Le prénom doit contenir au moins 2 caractères';
 } else {
 this.errors.first_name = '';
 }
 break;

 case 'last_name':
 if (!value || value.trim() === '') {
 this.errors.last_name = 'Le nom est obligatoire';
 } else if (value.trim().length < 2) {
 this.errors.last_name = 'Le nom doit contenir au moins 2 caractères';
 } else {
 this.errors.last_name = '';
 }
 break;

 case 'status_id':
 if (!value || value === '' || value === '0') {
 this.errors.status_id = 'Le statut du chauffeur est obligatoire';
 } else {
 this.errors.status_id = '';
 }
 break;
 }
 },

 hasError(fieldName) {
 return this.touched[fieldName] && this.errors[fieldName] !== '';
 },

 validateStep(step) {
 let isValid = true;

 if (step === 1) {
 const firstName = document.getElementById('first_name');
 const lastName = document.getElementById('last_name');

 if (firstName && lastName) {
 this.validateField('first_name', firstName.value);
 this.validateField('last_name', lastName.value);

 if (this.errors.first_name || this.errors.last_name) {
 isValid = false;
 }
 }
 }

 if (step === 2) {
 const statusInput = document.querySelector('input[name="status_id"]');
 if (statusInput) {
 this.validateField('status_id', statusInput.value);

 if (this.errors.status_id) {
 isValid = false;
 }
 }
 }

 return isValid;
 },

 nextStep() {
 if (this.validateStep(this.currentStep)) {
 if (this.currentStep < 4) {
 this.currentStep++;
 this.updateProgressBar();
 }
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

 handleValidationErrors() {
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
 }
 }
}
</script>
@endpush
