<div>
{{-- ====================================================================
 📥 DRIVERS IMPORT LIVEWIRE - WORLD-CLASS ENTERPRISE GRADE
 ====================================================================

 Importation ultra-professionnelle avec Livewire:
 ✨ Upload fichier avec prévisualisation
 ✨ Validation temps réel
 ✨ Aperçu des données avant import
 ✨ Progress bar animée
 ✨ Résultats détaillés

 @version 1.0-World-Class
 @since 2025-01-19
 ==================================================================== --}}

{{-- ===============================================
 ÉTAPE 1: UPLOAD
 =============================================== --}}
@if($step === 'upload')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 
 {{-- Sidebar Instructions --}}
 <div class="lg:col-span-1 space-y-6">
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:information-circle" class="w-6 h-6 text-blue-600" />
 <h2 class="text-lg font-semibold text-gray-900">Instructions</h2>
 </div>

 <div class="space-y-4">
 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
 <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Téléchargez le modèle</h3>
 <p class="text-xs text-gray-600 mt-1">Format CSV avec les colonnes requises</p>
 </div>
 </div>

 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
 <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Remplissez vos données</h3>
 <p class="text-xs text-gray-600 mt-1">
 <strong>Obligatoire:</strong> Prénom, Nom, N° Permis
 </p>
 </div>
 </div>

 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
 <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Format des dates</h3>
 <p class="text-xs text-gray-600 mt-1">
 <code class="px-1 py-0.5 bg-white border rounded text-xs">AAAA-MM-JJ</code>
 </p>
 </div>
 </div>

 <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
 <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">4</div>
 <div>
 <h3 class="text-sm font-semibold text-gray-900">Encodage UTF-8</h3>
 <p class="text-xs text-gray-600 mt-1">Séparateur: point-virgule (;)</p>
 </div>
 </div>
 </div>

 <div class="mt-6 pt-6 border-t border-gray-200">
 <button
 wire:click="downloadTemplate"
 class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-down-tray" class="w-5 h-5" />
 Télécharger le Modèle CSV
 </button>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
 <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-gray-600" />
 <h2 class="text-lg font-semibold text-gray-900">Formats Supportés</h2>
 </div>

 <div class="space-y-3">
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-600" />
 <span class="text-gray-700">CSV (.csv)</span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:check-circle" class="w-5 h-5 text-green-600" />
 <span class="text-gray-700">Excel (.xlsx, .xls)</span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-blue-600" />
 <span class="text-gray-700">Max: <strong>10 MB</strong></span>
 </div>
 <div class="flex items-center gap-3 text-sm">
 <x-iconify icon="heroicons:information-circle" class="w-5 h-5 text-blue-600" />
 <span class="text-gray-700">Max: <strong>1000 chauffeurs</strong></span>
 </div>
 </div>
 </x-card>
 </div>

 {{-- Main Content - Upload Zone --}}
 <div class="lg:col-span-2">
 <x-card>
 <div class="mb-6">
 <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
 <x-iconify icon="heroicons:cloud-arrow-up" class="w-6 h-6 text-blue-600" />
 Téléversement du Fichier
 </h2>
 </div>

 {{-- Upload Zone --}}
 <div
 x-data="{ isDragging: false }"
 @dragover.prevent="isDragging = true"
 @dragleave.prevent="isDragging = false"
 @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
 class="relative border-2 border-dashed rounded-xl p-12 text-center transition-all duration-200"
 :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-500 hover:bg-blue-50/50'">
 
 <input
 type="file"
 wire:model="importFile"
 accept=".csv,.xlsx,.xls"
 class="hidden"
 x-ref="fileInput">

 <div x-show="!@this.fileName">
 <label for="fileInput" class="cursor-pointer" @click="$refs.fileInput.click()">
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
 </label>
 </div>

 <div x-show="@this.fileName" x-cloak>
 <div class="inline-flex items-center gap-3 px-6 py-4 bg-white border border-gray-200 rounded-lg">
 <x-iconify icon="heroicons:document-text" class="w-8 h-8 text-blue-600" />
 <div class="text-left">
 <p class="font-medium text-gray-900">{{ $fileName }}</p>
 <p class="text-sm text-gray-500">{{ number_format($fileSize / 1024, 2) }} KB</p>
 </div>
 <button
 type="button"
 wire:click="removeFile"
 class="ml-4 text-red-600 hover:text-red-800 transition-colors">
 <x-iconify icon="heroicons:x-circle" class="w-6 h-6" />
 </button>
 </div>
 </div>

 {{-- Loading Indicator --}}
 <div wire:loading wire:target="importFile" class="mt-4">
 <div class="flex items-center justify-center gap-2">
 <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span class="text-sm text-gray-600">Chargement du fichier...</span>
 </div>
 </div>
 </div>

 @if($errors->has('importFile'))
 <div class="mt-4">
 <x-alert type="error" title="Erreur">{{ $errors->first('importFile') }}</x-alert>
 </div>
 @endif

 {{-- Options Import --}}
 <div class="mt-6 p-6 bg-gray-50 rounded-lg border border-gray-200">
 <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
 <x-iconify icon="heroicons:cog-6-tooth" class="w-5 h-5 text-gray-600" />
 Options d'Importation
 </h3>

 <div class="space-y-4">
 <label class="flex items-start gap-3 cursor-pointer group">
 <input type="checkbox" wire:model="skipDuplicates" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Ignorer les doublons
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Les chauffeurs avec des N° de permis existants seront ignorés
 </p>
 </div>
 </label>

 <label class="flex items-start gap-3 cursor-pointer group">
 <input type="checkbox" wire:model="updateExisting" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Mettre à jour les chauffeurs existants
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Les données des chauffeurs existants seront mises à jour
 </p>
 </div>
 </label>

 <label class="flex items-start gap-3 cursor-pointer group">
 <input type="checkbox" wire:model="dryRun" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Mode test (dry run)
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Vérifier les données sans les importer réellement
 </p>
 </div>
 </label>

 <label class="flex items-start gap-3 cursor-pointer group">
 <input type="checkbox" wire:model="sendNotifications" class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
 <div>
 <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
 Envoyer les notifications
 </span>
 <p class="text-xs text-gray-500 mt-0.5">
 Notifier les chauffeurs de leur ajout au système
 </p>
 </div>
 </label>
 </div>
 </div>

 {{-- Actions --}}
 <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
 <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
 Annuler
 </a>

 <button
 wire:click="analyzeFile"
 :disabled="!@this.fileName"
 wire:loading.attr="disabled"
 wire:target="analyzeFile"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <span wire:loading.remove wire:target="analyzeFile">
 <x-iconify icon="heroicons:arrow-right" class="w-5 h-5" />
 </span>
 <svg wire:loading wire:target="analyzeFile" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span>Analyser le Fichier</span>
 </button>
 </div>
 </x-card>
 </div>

</div>
@endif

{{-- ===============================================
 ÉTAPE 2: PRÉVISUALISATION
 =============================================== --}}
@if($step === 'preview')
<div class="space-y-6">
 
 {{-- Progress Bar --}}
 <x-card>
 <div class="flex items-center gap-4">
 <div class="flex-1">
 <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
 <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
 </div>
 </div>
 <span class="text-sm font-medium text-gray-700">{{ $progress }}%</span>
 </div>
 </x-card>

 {{-- Résumé --}}
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-blue-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Total Lignes</p>
 <p class="text-2xl font-bold text-gray-900">{{ $totalRows }}</p>
 </div>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Valides</p>
 <p class="text-2xl font-bold text-green-600">{{ $validRows }}</p>
 </div>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Invalides</p>
 <p class="text-2xl font-bold text-red-600">{{ $invalidRows }}</p>
 </div>
 </div>
 </x-card>
 </div>

 {{-- Aperçu Données --}}
 <x-card>
 <div class="mb-6">
 <h2 class="text-lg font-semibold text-gray-900">Aperçu des Données (5 premières lignes)</h2>
 </div>

 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
 <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
 <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Permis</th>
 <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
 <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @foreach($previewData as $row)
 <tr class="hover:bg-gray-50">
 <td class="px-4 py-3 text-sm text-gray-900">{{ $row['first_name'] ?? '-' }}</td>
 <td class="px-4 py-3 text-sm text-gray-900">{{ $row['last_name'] ?? '-' }}</td>
 <td class="px-4 py-3 text-sm text-gray-900">{{ $row['license_number'] ?? '-' }}</td>
 <td class="px-4 py-3 text-sm text-gray-600">{{ $row['personal_email'] ?? '-' }}</td>
 <td class="px-4 py-3 text-sm text-gray-600">{{ $row['personal_phone'] ?? '-' }}</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </x-card>

 {{-- Actions --}}
 <div class="flex items-center justify-between">
 <button
 wire:click="resetImport"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
 <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
 Retour
 </button>

 <button
 wire:click="startImport"
 wire:loading.attr="disabled"
 wire:target="startImport"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <span wire:loading.remove wire:target="startImport">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
 </span>
 <svg wire:loading wire:target="startImport" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <span>{{ $dryRun ? 'Tester l\'Importation' : 'Lancer l\'Importation' }}</span>
 </button>
 </div>

</div>
@endif

{{-- ===============================================
 ÉTAPE 3: TRAITEMENT
 =============================================== --}}
@if($step === 'processing')
<x-card>
 <div class="text-center py-12">
 <svg class="animate-spin h-16 w-16 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 <h3 class="text-xl font-semibold text-gray-900 mb-2">Importation en Cours...</h3>
 <p class="text-gray-600 mb-6">Veuillez patienter pendant le traitement des données</p>
 
 <div class="max-w-md mx-auto">
 <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
 <div class="h-full bg-blue-600 rounded-full animate-pulse" style="width: {{ $progress }}%"></div>
 </div>
 <p class="text-sm text-gray-500 mt-2">{{ $progress }}% complété</p>
 </div>
 </div>
</x-card>
@endif

{{-- ===============================================
 ÉTAPE 4: RÉSULTATS
 =============================================== --}}
@if($step === 'complete')
<div class="space-y-6">
 
 {{-- Métriques --}}
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Importés</p>
 <p class="text-2xl font-bold text-green-600">{{ $importResults['successful_imports'] ?? 0 }}</p>
 </div>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:arrow-path" class="w-6 h-6 text-amber-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Mis à jour</p>
 <p class="text-2xl font-bold text-amber-600">{{ $importResults['updated_existing'] ?? 0 }}</p>
 </div>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:forward" class="w-6 h-6 text-gray-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Ignorés</p>
 <p class="text-2xl font-bold text-gray-600">{{ $importResults['skipped_duplicates'] ?? 0 }}</p>
 </div>
 </div>
 </x-card>

 <x-card>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 </div>
 <div>
 <p class="text-sm text-gray-600">Erreurs</p>
 <p class="text-2xl font-bold text-red-600">{{ count($importResults['errors'] ?? []) }}</p>
 </div>
 </div>
 </x-card>
 </div>

 {{-- Erreurs Détaillées --}}
 @if(!empty($importResults['errors']))
 <x-card>
 <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
 <div class="flex items-center gap-2">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 <h2 class="text-lg font-semibold text-gray-900">Erreurs Détaillées</h2>
 </div>
 <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">
 {{ count($importResults['errors']) }}
 </span>
 </div>

 <div class="space-y-3 max-h-96 overflow-y-auto">
 @foreach($importResults['errors'] as $error)
 <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
 <div class="flex items-start gap-3">
 <x-iconify icon="heroicons:x-mark" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
 <div class="flex-1">
 <p class="text-sm font-semibold text-red-800">Ligne {{ $error['row'] }}</p>
 <p class="text-sm text-red-700 mt-1">{{ $error['error'] }}</p>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 </x-card>
 @endif

 {{-- Actions --}}
 <div class="flex items-center justify-center gap-4">
 <button
 wire:click="newImport"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
 Nouvelle Importation
 </button>

 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
 <x-iconify icon="heroicons:view-columns" class="w-5 h-5" />
 Voir les Chauffeurs
 </a>
 </div>

</div>
@endif

</div>

@push('scripts')
<script>
// Gérer le téléchargement du template
document.addEventListener('livewire:init', () => {
 Livewire.on('download-template', (event) => {
 const csv = event.csv;
 const filename = event.filename;
 const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
 const link = document.createElement('a');
 link.href = URL.createObjectURL(blob);
 link.download = filename;
 link.click();
 });
});
</script>
@endpush
