@extends('layouts.admin.catalyst')

@section('title', 'Résultats d\'Importation - ZenFleet')

@section('content')
{{-- ====================================================================
 📊 RÉSULTATS IMPORTATION - WORLD-CLASS ENTERPRISE GRADE
 ====================================================================

 Design surpassant Airbnb, Stripe, Salesforce, Fleetio:
 ✨ Visualisations de données interactives
 ✨ Graphiques circulaires animés
 ✨ Liste détaillée des succès et erreurs
 ✨ Actions rapides et export de rapport
 ✨ Design épuré et professionnel

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
 <a href="{{ route('admin.vehicles.import.show') }}" class="hover:text-blue-600 transition-colors">
 Importation
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <span class="font-semibold text-gray-900">Résultats</span>
 </nav>

 {{-- Header --}}
 <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
 <div class="flex items-center gap-4">
 <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
 <x-iconify icon="heroicons:chart-bar-square" class="w-8 h-8 text-white" />
 </div>
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Résultats d'Importation</h1>
 <p class="text-gray-600 mt-1">Analyse détaillée de l'importation de véhicules</p>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center gap-3">
 <a href="{{ route('admin.vehicles.import.show') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
 <x-iconify icon="heroicons:plus-circle" class="w-5 h-5" />
 Nouvelle Importation
 </a>
 @if(isset($result))
 <a href="{{ route('admin.vehicles.import.report') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
 <x-iconify icon="heroicons:document-arrow-down" class="w-5 h-5 text-gray-600" />
 Télécharger le Rapport
 </a>
 @endif
 <a href="{{ route('admin.vehicles.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:view-columns" class="w-5 h-5" />
 Voir les Véhicules
 </a>
 </div>
 </div>
 </div>

 {{-- ===============================================
 RÉSUMÉ DES RÉSULTATS
 =============================================== --}}
 @if(isset($result))
 @php
 $successCount = $result['successful_imports'] ?? 0;
 $errorRows = $result['errors'] ?? [];
 $totalRecords = $result['total_processed'] ?? 0;
 $updateCount = $result['updated_existing'] ?? 0;
 $errorCount = count($errorRows);
 $successRate = $totalRecords > 0 ? round(($successCount / $totalRecords) * 100, 1) : 0;
 $updateRate = $totalRecords > 0 ? round(($updateCount / $totalRecords) * 100, 1) : 0;
 $errorRate = $totalRecords > 0 ? round(($errorCount / $totalRecords) * 100, 1) : 0;
 @endphp

 {{-- Cards Métriques --}}
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 {{-- Total Traité --}}
 <div class="bg-blue-50 rounded-lg border border-blue-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Total Traité</p>
 <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalRecords) }}</p>
 </div>
 <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
 <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-blue-600" />
 </div>
 </div>
 </div>

 {{-- Succès --}}
 <div class="bg-green-50 rounded-lg border border-green-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Importés</p>
 <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($successCount) }}</p>
 </div>
 <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 </div>
 </div>
 </div>

 {{-- Mis à Jour --}}
 <div class="bg-amber-50 rounded-lg border border-amber-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Mis à Jour</p>
 <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($updateCount) }}</p>
 </div>
 <div class="w-12 h-12 bg-amber-100 border border-amber-300 rounded-full flex items-center justify-center">
 <x-iconify icon="heroicons:arrow-path" class="w-6 h-6 text-amber-600" />
 </div>
 </div>
 </div>

 {{-- Erreurs --}}
 <div class="bg-red-50 rounded-lg border border-red-200 p-6 hover:shadow-lg transition-shadow duration-300">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm font-medium text-gray-600">Erreurs</p>
 <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($errorCount) }}</p>
 </div>
 <div class="w-12 h-12 bg-red-100 border border-red-300 rounded-full flex items-center justify-center">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 </div>
 </div>
 </div>
 </div>

 {{-- ===============================================
 VISUALISATION DES DONNÉES
 =============================================== --}}
 <x-card margin="mb-8">
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:chart-pie" class="w-6 h-6 text-blue-600" />
 <h2 class="text-xl font-semibold text-gray-900">Analyse des Résultats</h2>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
 {{-- Succès --}}
 <div class="text-center">
 <div class="relative inline-flex items-center justify-center w-32 h-32 mb-4">
 <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
 <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
 <path class="text-green-500" stroke="currentColor" stroke-width="3" fill="none" 
 stroke-dasharray="{{ $successRate }}, 100" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
 stroke-linecap="round"></path>
 </svg>
 <span class="absolute text-2xl font-bold text-green-600">{{ $successRate }}%</span>
 </div>
 <p class="text-sm font-semibold text-gray-900">Taux de Succès</p>
 <p class="text-xs text-gray-500 mt-1">{{ $successCount }} véhicules importés</p>
 </div>

 {{-- Mises à Jour --}}
 <div class="text-center">
 <div class="relative inline-flex items-center justify-center w-32 h-32 mb-4">
 <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
 <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
 <path class="text-amber-500" stroke="currentColor" stroke-width="3" fill="none" 
 stroke-dasharray="{{ $updateRate }}, 100" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
 stroke-linecap="round"></path>
 </svg>
 <span class="absolute text-2xl font-bold text-amber-600">{{ $updateRate }}%</span>
 </div>
 <p class="text-sm font-semibold text-gray-900">Taux de Mise à Jour</p>
 <p class="text-xs text-gray-500 mt-1">{{ $updateCount }} véhicules mis à jour</p>
 </div>

 {{-- Erreurs --}}
 <div class="text-center">
 <div class="relative inline-flex items-center justify-center w-32 h-32 mb-4">
 <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
 <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
 <path class="text-red-500" stroke="currentColor" stroke-width="3" fill="none" 
 stroke-dasharray="{{ $errorRate }}, 100" 
 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
 stroke-linecap="round"></path>
 </svg>
 <span class="absolute text-2xl font-bold text-red-600">{{ $errorRate }}%</span>
 </div>
 <p class="text-sm font-semibold text-gray-900">Taux d'Erreur</p>
 <p class="text-xs text-gray-500 mt-1">{{ $errorCount }} erreurs détectées</p>
 </div>
 </div>
 </x-card>

 {{-- ===============================================
 DÉTAILS DES RÉSULTATS
 =============================================== --}}
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

 {{-- Véhicules Importés --}}
 @if($successCount > 0)
 <x-card>
 <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
 <div class="flex items-center gap-2">
 <x-iconify icon="heroicons:check-circle" class="w-6 h-6 text-green-600" />
 <h2 class="text-lg font-semibold text-gray-900">Véhicules Importés</h2>
 </div>
 <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
 {{ $successCount }}
 </span>
 </div>

 @if(isset($recentlyImported) && $recentlyImported->count() > 0)
 <div class="space-y-3 max-h-96 overflow-y-auto">
 @foreach($recentlyImported as $vehicle)
 <div class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-green-100 border border-green-300 rounded-full flex items-center justify-center">
 <x-iconify icon="heroicons:truck" class="w-5 h-5 text-green-600" />
 </div>
 <div>
 <p class="font-semibold text-gray-900">{{ $vehicle->registration_plate }}</p>
 <p class="text-sm text-gray-600">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
 </div>
 </div>
 <a href="{{ route('admin.vehicles.show', $vehicle) }}"
 class="text-blue-600 hover:text-blue-800 font-medium text-sm">
 Voir
 </a>
 </div>
 @endforeach
 </div>
 @else
 <div class="text-center py-8">
 <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
 <x-iconify icon="heroicons:check-circle" class="w-8 h-8 text-green-600" />
 </div>
 <h3 class="text-lg font-semibold text-gray-900 mb-2">Importation Réussie</h3>
 <p class="text-gray-600 mb-4">{{ $successCount }} véhicule(s) ont été importés avec succès.</p>
 <a href="{{ route('admin.vehicles.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
 <x-iconify icon="heroicons:eye" class="w-5 h-5" />
 Voir les véhicules
 </a>
 </div>
 @endif
 </x-card>
 @endif

 {{-- Erreurs Détaillées --}}
 @if($errorCount > 0)
 <x-card>
 <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
 <div class="flex items-center gap-2">
 <x-iconify icon="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
 <h2 class="text-lg font-semibold text-gray-900">Erreurs Détaillées</h2>
 </div>
 <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">
 {{ $errorCount }}
 </span>
 </div>

 <div class="space-y-3 max-h-96 overflow-y-auto">
 @foreach($errorRows as $error)
 <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
 <div class="flex items-start gap-3">
 <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
 <x-iconify icon="heroicons:x-mark" class="w-4 h-4 text-red-600" />
 </div>
 <div class="flex-1 min-w-0">
 <p class="text-sm font-semibold text-red-800">
 Ligne {{ $error['row'] ?? $error['line'] ?? 'N/A' }}
 </p>
 <p class="text-sm text-red-700 mt-1">
 {{ $error['error'] ?? 'Erreur inconnue' }}
 </p>
 @if(!empty($error['data']))
 <div class="mt-2 p-2 bg-white border border-red-200 rounded text-xs text-gray-600 font-mono overflow-x-auto">
 {{ is_array($error['data']) ? json_encode($error['data'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $error['data'] }}
 </div>
 @endif
 </div>
 </div>
 </div>
 @endforeach
 </div>

 {{-- Export Erreurs --}}
 @if($errorCount > 5)
 <div class="mt-4 pt-4 border-t border-gray-200">
 <button
 type="button"
 onclick="exportErrors()"
 class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
 <x-iconify icon="heroicons:arrow-down-tray" class="w-5 h-5" />
 Exporter les Erreurs (CSV)
 </button>
 </div>
 @endif
 </x-card>
 @endif

 </div>

 {{-- ===============================================
 ACTIONS RAPIDES
 =============================================== --}}
 <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
 <a href="{{ route('admin.vehicles.import.show') }}"
 class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:arrow-up-tray" class="w-5 h-5" />
 Importer Plus de Véhicules
 </a>
 
 <a href="{{ route('admin.vehicles.index') }}"
 class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
 <x-iconify icon="heroicons:view-columns" class="w-5 h-5" />
 Voir la Liste Complète
 </a>

 <button 
 @click="printReport()"
 class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
 <x-iconify icon="heroicons:printer" class="w-5 h-5" />
 Imprimer le Rapport
 </button>
 </div>

 @else
 {{-- État Vide --}}
 <x-empty-state
 icon="heroicons:document-chart-bar"
 title="Aucun résultat d'importation"
 description="Aucune importation n'a été effectuée récemment."
 actionUrl="{{ route('admin.vehicles.import.show') }}"
 actionText="Importer des Véhicules"
 actionIcon="arrow-up-tray"
 />
 @endif

 </div>
</section>

@push('scripts')
<script>
function exportErrors() {
 // Préparer les données d'erreurs pour l'export CSV
 const errors = @json($errorRows ?? []);
 
 let csvContent = "data:text/csv;charset=utf-8,";
 csvContent += "Ligne,Erreur,Plaque,Marque,Modèle,VIN,Données\n";
 
 errors.forEach(error => {
 const row = error.row || error.line || 'N/A';
 const errorMsg = (error.error || 'Erreur inconnue').replace(/"/g, '""');
 const dataPayload = error.data || {};
 const plate = (dataPayload.registration_plate || dataPayload.plaque || '').toString().replace(/"/g, '""');
 const brand = (dataPayload.brand || '').toString().replace(/"/g, '""');
 const model = (dataPayload.model || '').toString().replace(/"/g, '""');
 const vin = (dataPayload.vin || '').toString().replace(/"/g, '""');
 const data = error.data ? JSON.stringify(error.data).replace(/"/g, '""') : '';
 csvContent += `"${row}","${errorMsg}","${plate}","${brand}","${model}","${vin}","${data}"\n`;
 });

 const encodedUri = encodeURI(csvContent);
 const link = document.createElement("a");
 link.setAttribute("href", encodedUri);
 link.setAttribute("download", `erreurs-importation-${Date.now()}.csv`);
 document.body.appendChild(link);
 link.click();
 document.body.removeChild(link);
}

function printReport() {
 window.print();
}
</script>
@endpush
@endsection
