@extends('layouts.admin.catalyst')
@section('title', 'Importation Chauffeurs - ZenFleet Enterprise')

@push('styles')
<style>
/* Enterprise-grade animations et styles ultra-modernes */
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

.hover-scale {
 transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
 transform: scale(1.02);
}

.upload-zone {
 border: 2px dashed #e2e8f0;
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 transition: all 0.3s ease;
}

.upload-zone:hover {
 border-color: #6366f1;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.upload-zone.dragover {
 border-color: #4f46e5;
 background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
 transform: scale(1.02);
}

.step-indicator {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.step-indicator.active {
 background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
 color: white;
 border-color: #4f46e5;
}

.instruction-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.instruction-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
 border-color: #cbd5e1;
}

.file-input {
 position: relative;
 overflow: hidden;
 display: inline-block;
 width: 100%;
}

.file-input input[type=file] {
 position: absolute;
 left: -9999px;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
 <div class="fade-in space-y-8">
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
 <span class="font-semibold text-gray-900">Importation</span>
 </nav>

 <!-- Hero Content -->
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-6">
 <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
 <i class="fas fa-file-import text-white text-2xl"></i>
 </div>
 <div>
 <h1 class="text-4xl font-bold text-gray-900">Importation Chauffeurs</h1>
 <p class="text-gray-600 text-lg mt-2">
 Importez vos chauffeurs en masse via fichier CSV
 </p>
 </div>
 </div>

 <!-- Progress Steps -->
 <div class="hidden md:flex items-center gap-4">
 <div class="step-indicator active rounded-lg px-4 py-2 text-sm font-semibold">
 <i class="fas fa-upload mr-2"></i>
 1. Upload
 </div>
 <div class="w-8 h-0.5 bg-gray-300"></div>
 <div class="step-indicator rounded-lg px-4 py-2 text-sm font-semibold">
 <i class="fas fa-cogs mr-2"></i>
 2. Traitement
 </div>
 <div class="w-8 h-0.5 bg-gray-300"></div>
 <div class="step-indicator rounded-lg px-4 py-2 text-sm font-semibold">
 <i class="fas fa-check mr-2"></i>
 3. Résultats
 </div>
 </div>
 </div>
 </div>
 </div>

 <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
 <!-- Instructions Section -->
 <div class="lg:col-span-1 space-y-6">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6">
 <h3 class="text-xl font-bold text-gray-900 mb-4">
 <i class="fas fa-info-circle text-blue-600 mr-2"></i>
 Instructions
 </h3>

 <div class="space-y-4">
 <div class="instruction-card rounded-xl p-4">
 <div class="flex items-start gap-3">
 <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">1</div>
 <div>
 <h4 class="font-semibold text-gray-900 mb-1">Téléchargez le modèle</h4>
 <p class="text-sm text-gray-600">Utilisez notre fichier modèle pour voir le format exact des colonnes.</p>
 </div>
 </div>
 </div>

 <div class="instruction-card rounded-xl p-4">
 <div class="flex items-start gap-3">
 <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
 <div>
 <h4 class="font-semibold text-gray-900 mb-1">Remplissez vos données</h4>
 <p class="text-sm text-gray-600">Les colonnes <strong>nom</strong>, <strong>prenom</strong> et <strong>date_naissance</strong> sont obligatoires.</p>
 </div>
 </div>
 </div>

 <div class="instruction-card rounded-xl p-4">
 <div class="flex items-start gap-3">
 <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
 <div>
 <h4 class="font-semibold text-gray-900 mb-1">Format des données</h4>
 <p class="text-sm text-gray-600">Dates au format <strong>AAAA-MM-JJ</strong> et numéros de permis uniques.</p>
 </div>
 </div>
 </div>

 <div class="instruction-card rounded-xl p-4">
 <div class="flex items-start gap-3">
 <div class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">4</div>
 <div>
 <h4 class="font-semibold text-gray-900 mb-1">Encodage UTF-8</h4>
 <p class="text-sm text-gray-600">Enregistrez en CSV avec séparateur virgule et encodage UTF-8.</p>
 </div>
 </div>
 </div>
 </div>

 <!-- Download Template Button -->
 <div class="mt-6 pt-6 border-t border-gray-200">
 <a href="{{ route('admin.drivers.import.template') }}"
 class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md hover:scale-105">
 <i class="fas fa-download mr-2"></i>
 Télécharger le Modèle CSV
 </a>
 </div>
 </div>

 <!-- Supported Formats -->
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-6">
 <h3 class="text-lg font-bold text-gray-900 mb-4">
 <i class="fas fa-file-alt text-gray-600 mr-2"></i>
 Formats Supportés
 </h3>
 <div class="space-y-3">
 <div class="flex items-center gap-3">
 <i class="fas fa-check-circle text-green-600"></i>
 <span class="text-sm text-gray-700">Fichiers CSV (.csv)</span>
 </div>
 <div class="flex items-center gap-3">
 <i class="fas fa-check-circle text-green-600"></i>
 <span class="text-sm text-gray-700">Fichiers texte (.txt)</span>
 </div>
 <div class="flex items-center gap-3">
 <i class="fas fa-info-circle text-blue-600"></i>
 <span class="text-sm text-gray-700">Taille max: 10 MB</span>
 </div>
 <div class="flex items-center gap-3">
 <i class="fas fa-info-circle text-blue-600"></i>
 <span class="text-sm text-gray-700">Max: 1000 chauffeurs</span>
 </div>
 </div>
 </div>
 </div>

 <!-- Upload Section -->
 <div class="lg:col-span-2">
 <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-100 p-8">
 <h3 class="text-2xl font-bold text-gray-900 mb-6">
 <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>
 Téléversement du Fichier
 </h3>

 @if(session('error'))
 <div class="mb-6 p-4 bg-red-50 rounded-xl border border-red-200">
 <div class="flex items-center">
 <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
 <span class="text-red-800 font-medium">{{ session('error') }}</span>
 </div>
 </div>
 @endif

 <form action="{{ route('admin.drivers.import.handle') }}" method="POST" enctype="multipart/form-data" id="upload-form">
 @csrf

 <!-- Upload Zone -->
 <div class="upload-zone rounded-2xl p-8 text-center" id="upload-zone">
 <div class="file-input">
 <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required>
 <label for="csv_file" class="cursor-pointer">
 <div class="mb-4">
 <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
 </div>
 <div class="mb-4">
 <p class="text-xl font-semibold text-gray-700 mb-2">
 Glissez-déposez votre fichier CSV ici
 </p>
 <p class="text-gray-500">
 ou <span class="text-blue-600 font-semibold">cliquez pour parcourir</span>
 </p>
 </div>
 <div class="text-sm text-gray-500">
 Formats supportés: CSV, TXT • Taille max: 10 MB
 </div>
 </label>
 </div>
 </div>

 <!-- File Info -->
 <div id="file-info" class="hidden mt-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
 <div class="flex items-center gap-3">
 <i class="fas fa-file-csv text-blue-600 text-2xl"></i>
 <div>
 <p class="font-semibold text-blue-900" id="file-name"></p>
 <p class="text-sm text-blue-700" id="file-size"></p>
 </div>
 <button type="button" id="remove-file" class="ml-auto text-red-600 hover:text-red-800">
 <i class="fas fa-times"></i>
 </button>
 </div>
 </div>

 <!-- Import Options -->
 <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
 <h4 class="font-semibold text-gray-900 mb-3">Options d'importation</h4>
 <div class="space-y-3">
 <div class="flex items-center">
 <input type="checkbox" name="skip_duplicates" id="skip_duplicates" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <label for="skip_duplicates" class="ml-3 text-sm text-gray-700">
 <span class="font-medium">Ignorer les doublons</span>
 <p class="text-gray-500">Les chauffeurs avec des emails ou matricules existants seront ignorés</p>
 </label>
 </div>
 <div class="flex items-center">
 <input type="checkbox" name="update_existing" id="update_existing" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <label for="update_existing" class="ml-3 text-sm text-gray-700">
 <span class="font-medium">Mettre à jour les chauffeurs existants</span>
 <p class="text-gray-500">Les données des chauffeurs existants seront mises à jour</p>
 </label>
 </div>
 </div>
 </div>

 @error('csv_file')
 <div class="mt-4 p-4 bg-red-50 rounded-xl border border-red-200">
 <p class="text-red-800">{{ $message }}</p>
 </div>
 @enderror

 <!-- Actions -->
 <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-all duration-200">
 <i class="fas fa-arrow-left"></i>
 <span>Retour à la liste</span>
 </a>

 <button type="submit" id="submit-btn" disabled
 class="inline-flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
 <i class="fas fa-rocket"></i>
 <span>Lancer l'Importation</span>
 </button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
 const uploadZone = document.getElementById('upload-zone');
 const fileInput = document.getElementById('csv_file');
 const fileInfo = document.getElementById('file-info');
 const fileName = document.getElementById('file-name');
 const fileSize = document.getElementById('file-size');
 const removeBtn = document.getElementById('remove-file');
 const submitBtn = document.getElementById('submit-btn');

 // File input change handler
 fileInput.addEventListener('change', function(e) {
 const file = e.target.files[0];
 if (file) {
 displayFileInfo(file);
 }
 });

 // Drag and drop handlers
 uploadZone.addEventListener('dragover', function(e) {
 e.preventDefault();
 uploadZone.classList.add('dragover');
 });

 uploadZone.addEventListener('dragleave', function(e) {
 e.preventDefault();
 uploadZone.classList.remove('dragover');
 });

 uploadZone.addEventListener('drop', function(e) {
 e.preventDefault();
 uploadZone.classList.remove('dragover');

 const files = e.dataTransfer.files;
 if (files.length > 0) {
 fileInput.files = files;
 displayFileInfo(files[0]);
 }
 });

 // Remove file handler
 removeBtn.addEventListener('click', function() {
 fileInput.value = '';
 fileInfo.classList.add('hidden');
 submitBtn.disabled = true;
 uploadZone.classList.remove('dragover');
 });

 function displayFileInfo(file) {
 fileName.textContent = file.name;
 fileSize.textContent = formatFileSize(file.size);
 fileInfo.classList.remove('hidden');
 submitBtn.disabled = false;
 }

 function formatFileSize(bytes) {
 if (bytes === 0) return '0 Bytes';
 const k = 1024;
 const sizes = ['Bytes', 'KB', 'MB', 'GB'];
 const i = Math.floor(Math.log(bytes) / Math.log(k));
 return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
 }
});
</script>
@endpush
@endsection