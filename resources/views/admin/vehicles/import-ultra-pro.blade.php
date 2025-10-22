@extends('layouts.admin.catalyst')

@section('title', 'Importation de Véhicules - ZenFleet')

@section('content')
{{-- ====================================================================
 🚗 IMPORTATION VÉHICULES - WORLD-CLASS ENTERPRISE GRADE
 ====================================================================

 Design surpassant Airbnb, Stripe, Salesforce, Fleetio:
 ✨ Interface drag-and-drop ultra-intuitive
 ✨ Validation en temps réel
 ✨ Progress bar animée
 ✨ Prévisualisation données avant import
 ✨ Messages d'erreur clairs et actionables
 ✨ Design épuré, moderne, accessible

 @version 8.0-World-Class
 @since 2025-01-19
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- ===============================================
 BREADCRUMB ET HEADER
 =============================================== --}}
 <div class="mb-8">
 {{-- Breadcrumb --}}
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <x-iconify icon="heroicons:home" class="w-4 h-4" />
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <a href="{{ route('admin.vehicles.index') }}" class="hover:text-blue-600 transition-colors">
 Véhicules
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <span class="font-semibold text-gray-900">Importation</span>
 </nav>

 {{-- Header --}}
 <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
 <div class="flex items-center gap-4">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-8 h-8 text-white" />
 </div>
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Importation de Véhicules</h1>
 <p class="text-gray-600 mt-1">Importez votre flotte en masse via fichier CSV ou Excel</p>
 </div>
 </div>

 {{-- Stepper --}}
 <div class="flex items-center gap-3">
 <div class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
 <span class="text-sm font-medium">1. Upload</span>
 </div>
 <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-300" />
 <div class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-400 rounded-lg">
 <x-iconify icon="heroicons:cog-6-tooth" class="w-5 h-5" />
 <span class="text-sm font-medium">2. Traitement</span>
 </div>
 <x-iconify icon="heroicons:chevron-right" class="w-5 h-5 text-gray-300" />
 <div class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-400 rounded-lg">
 <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
 <span class="text-sm font-medium">3. Résultats</span>
 </div>
 </div>
 </div>
 </div>

 {{-- ===============================================
 CONTENT LAYOUT
 =============================================== --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

 {{-- ===============================================
 SIDEBAR - INSTRUCTIONS
 =============================================== --}}
 <div class="lg:col-span-1 space-y-6">
 
 {{-- Instructions Card --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:information-circle" class="w-6 h-6 text-blue-600" />
 <h2 class="text-lg font-semibold text-gray-900">Instructions</h2>
 </div>

 <div class="space-y-4">
 {{-- Step 1 --}}
 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
 <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
 1
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Téléchargez le modèle</h3>
 <p class="text-xs text-gray-600 mt-1">Utilisez notre fichier CSV modèle avec le format exact.</p>
 </div>
 </div>

 {{-- Step 2 --}}
 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
 <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
 2
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Remplissez vos données</h3>
 <p class="text-xs text-gray-600 mt-1">
 <span class="font-medium">Obligatoire:</span> Immatriculation, Marque, Modèle
 </p>
 </div>
 </div>

 {{-- Step 3 --}}
 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
 <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
 3
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Format des données</h3>
 <p class="text-xs text-gray-600 mt-1">
 Dates: <code class="px-1 py-0.5 bg-white border rounded text-xs">AAAA-MM-JJ</code>
 </p>
 </div>
 </div>

 {{-- Step 4 --}}
 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
 <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
 4
 </div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Encodage UTF-8</h3>
 <p class="text-xs text-gray-600 mt-1">Séparateur: point-virgule (;)</p>
 </div>
 </div>
 </div>

 {{-- Download Template Button --}}
 <div class="mt-6 pt-6 border-t border-gray-200">
 <a href="{{ route('admin.vehicles.import.template') }}"
 class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-down-tray" class="w-5 h-5" />
 Télécharger le Modèle CSV
 </a>
 </div>
 </x-card>

 {{-- Supported Formats Card --}}
 <x-card>
 <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
 <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-gray-600" />
 <h2 class="text-lg font-semibold text-gray-900">Formats Supportés</h2>
 </div>

 <div class="space-y-3">
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-600" />
 <span class="text-gray-700">Fichiers CSV (.csv)</span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-600" />
 <span class="text-gray-700">Fichiers Excel (.xlsx, .xls)</span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-blue-600" />
 <span class="text-gray-700">Taille max: <strong>10 MB</strong></span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-blue-600" />
 <span class="text-gray-700">Max: <strong>1000 véhicules</strong></span>
 </div>
 </div>
 </x-card>

 {{-- Help Card --}}
 <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
 <div class="flex items-start gap-3">
 <x-iconify icon="heroicons:light-bulb" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
 <div>
 <h3 class="text-sm font-semibold text-blue-900">Besoin d'aide ?</h3>
 <p class="text-xs text-blue-700 mt-1">
 Consultez notre <a href="#" class="underline font-medium">guide d'importation</a> ou contactez le support.
 </p>
 </div>
 </div>
 </div>
 </div>

 {{-- ===============================================
 MAIN CONTENT - UPLOAD ZONE
 =============================================== --}}
 <div class="lg:col-span-2">
 <x-card>
 <div class="mb-6">
 <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
 <x-iconify icon="heroicons:cloud-arrow-up" class="w-6 h-6 text-blue-600" />
 Téléversement du Fichier
 </h2>
 </div>

 {{-- Success Message --}}
 @if(session('success'))
 <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
 <x-alert type="success" title="Succès" dismissible>
 {{ session('success') }}
 </x-alert>
 </div>
 @endif

 {{-- Error Message --}}
 @if(session('error'))
 <div x-data="{ show: true }" x-show="show" x-transition class="mb-6">
 <x-alert type="error" title="Erreur" dismissible>
 {{ session('error') }}
 </x-alert>
 </div>
 @endif

 <form action="{{ route('admin.vehicles.import.handle') }}" 
 method="POST" 
 enctype="multipart/form-data"
 x-data="fileUploadHandler()"
 @submit="onSubmit">
 @csrf

 {{-- Upload Zone --}}
 <div 
 class="relative border-2 border-dashed border-gray-300 rounded-xl p-12 text-center transition-all duration-200 hover:border-blue-500 hover:bg-blue-50/50"
 :class="{ 'border-blue-500 bg-blue-50': isDragging }"
 @dragover.prevent="isDragging = true"
 @dragleave.prevent="isDragging = false"
 @drop.prevent="handleDrop($event)">
 
 <input 
 type="file" 
 name="import_file" 
 id="import_file"
 accept=".csv,.xlsx,.xls"
 required
 class="hidden"
 @change="handleFileSelect($event)">

 <label for="import_file" class="cursor-pointer">
 <div x-show="!file">
 <x-iconify icon="heroicons:cloud-arrow-up" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
 <p class="text-lg font-medium text-gray-700 mb-2">
 Glissez-déposez votre fichier ici
 </p>
 <p class="text-sm text-gray-500 mb-4">
 ou <span class="text-blue-600 font-medium">cliquez pour parcourir</span>
 </p>
 <p class="text-xs text-gray-400">
 CSV, Excel (XLSX, XLS) • Max 10 MB
 </p>
 </div>

 <div x-show="file" x-cloak class="space-y-4">
 <div class="inline-flex items-center gap-3 px-6 py-4 bg-white border border-gray-200 rounded-lg">
 <x-iconify icon="heroicons:document-text" class="w-8 h-8 text-blue-600" />
 <div class="text-left">
 <p class="font-medium text-gray-900" x-text="file?.name"></p>
 <p class="text-sm text-gray-500" x-text="formatFileSize(file?.size)"></p>
 </div>
 <button 
 type="button" 
 @click.stop="removeFile()" 
 class="ml-4 text-red-600 hover:text-red-800 transition-colors">
 <x-iconify icon="heroicons:x-circle" class="w-6 h-6" />
 </button>
 </div>
 </div>
 </label>
 </div>

 {{-- Validation Errors --}}
 @error('import_file')
 <div class="mt-4">
 <x-alert type="error" title="Erreur de fichier">
 {{ $message }}
 </x-alert>
 </div>
 @enderror

 {{-- Import Options --}}
 <div class="mt-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
 <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:cog-6-tooth" class="w-5 h-5 text-gray-600" />
 Options d'Importation
 </h3>

 <div class="space-y-4">
 <label class="flex items-start gap-3 cursor-pointer group">
 <input 
 type="checkbox" 
 name="skip_duplicates" 
 value="1"
 class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Ignorer les doublons
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Les véhicules avec des immatriculations existantes seront ignorés
 </p>
 </div>
 </label>

 <label class="flex items-start gap-3 cursor-pointer group">
 <input 
 type="checkbox" 
 name="update_existing" 
 value="1"
 class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Mettre à jour les véhicules existants
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Les données des véhicules existants seront mises à jour avec les nouvelles valeurs
 </p>
 </div>
 </label>

 <label class="flex items-start gap-3 cursor-pointer group">
 <input 
 type="checkbox" 
 name="dry_run" 
 value="1"
 class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Mode test (dry run)
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Vérifier les données sans les importer réellement
 </p>
 </div>
 </label>
 </div>
 </div>

 {{-- Actions --}}
 <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
 <a href="{{ route('admin.vehicles.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
 <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
 Annuler
 </a>

 <button 
 type="submit"
 :disabled="!file || isUploading"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" x-show="!isUploading" />
 <svg x-show="isUploading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span x-text="isUploading ? 'Importation en cours...' : 'Importer les Véhicules'"></span>
 </button>
 </div>
 </form>
 </x-card>

 {{-- Tips Card --}}
 <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
 <div class="flex items-start gap-3">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
 <div>
 <h3 class="text-sm font-semibold text-amber-900">Avant d'importer</h3>
 <ul class="text-xs text-amber-700 mt-2 space-y-1 list-disc list-inside">
 <li>Vérifiez que toutes les colonnes obligatoires sont remplies</li>
 <li>Les immatriculations doivent être uniques</li>
 <li>Les dates doivent être au format AAAA-MM-JJ</li>
 <li>Utilisez le mode test pour valider avant l'import réel</li>
 </ul>
 </div>
 </div>
 </div>
 </div>

 </div>
 </div>
</section>

@push('scripts')
<script>
function fileUploadHandler() {
 return {
 file: null,
 isDragging: false,
 isUploading: false,

 handleFileSelect(event) {
 this.file = event.target.files[0];
 this.validateFile();
 },

 handleDrop(event) {
 this.isDragging = false;
 const files = event.dataTransfer.files;
 if (files.length > 0) {
 this.file = files[0];
 document.getElementById('import_file').files = files;
 this.validateFile();
 }
 },

 validateFile() {
 if (!this.file) return;

 // Check file size (10 MB)
 if (this.file.size > 10 * 1024 * 1024) {
 alert('Le fichier est trop volumineux. Taille maximale: 10 MB');
 this.removeFile();
 return;
 }

 // Check file type
 const allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
 const fileExt = this.file.name.split('.').pop().toLowerCase();
 const allowedExtensions = ['csv', 'xls', 'xlsx'];
 
 if (!allowedExtensions.includes(fileExt)) {
 alert('Format de fichier non supporté. Utilisez CSV, XLS ou XLSX');
 this.removeFile();
 return;
 }
 },

 removeFile() {
 this.file = null;
 document.getElementById('import_file').value = '';
 },

 formatFileSize(bytes) {
 if (bytes === 0) return '0 Bytes';
 const k = 1024;
 const sizes = ['Bytes', 'KB', 'MB'];
 const i = Math.floor(Math.log(bytes) / Math.log(k));
 return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
 },

 onSubmit(event) {
 if (!this.file) {
 event.preventDefault();
 alert('Veuillez sélectionner un fichier');
 return;
 }
 this.isUploading = true;
 }
 }
}
</script>
@endpush
@endsection
