@extends('layouts.admin.catalyst')

@section('title', 'Résultats d\'Importation')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
    <div class="max-w-6xl mx-auto">

        {{-- Navigation Breadcrumb Enterprise --}}
        <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
                Gestion des Chauffeurs
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <a href="{{ route('admin.drivers.import.show') }}" class="hover:text-blue-600 transition-colors">
                Importation
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="font-semibold text-gray-900">Résultats</span>
        </nav>

        {{-- En-tête Ultra-Professionnel --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="h-10 w-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                        Résultats d'Importation
                    </h1>
                    <p class="mt-2 text-gray-600 text-lg">
                        Détails complets de votre importation de chauffeurs avec analyse des succès et erreurs
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.drivers.import.show') }}"
                       class="btn-outline px-6 py-3 flex items-center gap-2 font-semibold">
                        <i class="fas fa-plus text-sm"></i>
                        Nouvelle Importation
                    </a>
                    <a href="{{ route('admin.drivers.index') }}"
                       class="btn-primary px-6 py-3 flex items-center gap-2 font-semibold">
                        <i class="fas fa-users text-sm"></i>
                        Voir les Chauffeurs
                    </a>
                </div>
            </div>
        </div>

        {{-- Résumé des Résultats --}}
        @if($successCount || count($errorRows))
            <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Traité --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Traité</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format(($totalRecords ?? ($successCount + count($errorRows)))) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Succès --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Importés</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($successCount) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Mis à jour --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-sync-alt text-amber-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Mis à Jour</p>
                            <p class="text-2xl font-bold text-amber-600">0</p>
                        </div>
                    </div>
                </div>

                {{-- Erreurs --}}
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Erreurs</p>
                            <p class="text-2xl font-bold text-red-600">{{ number_format(count($errorRows)) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphique de Progression --}}
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                        Analyse des Résultats
                    </h3>

                    @php
                        $total = $totalRecords ?? ($successCount + count($errorRows));
                        $successRate = $total > 0 ? round(($successCount / $total) * 100, 1) : 0;
                        $updateRate = 0; // Pas de mise à jour pour les chauffeurs actuellement
                        $errorRate = $total > 0 ? round((count($errorRows) / $total) * 100, 1) : 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Taux de Succès --}}
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center w-24 h-24 mb-4">
                                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-200" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <path class="text-green-500" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="{{ $successRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                </svg>
                                <span class="absolute text-xl font-bold text-green-600">{{ $successRate }}%</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Taux de Succès</p>
                        </div>

                        {{-- Taux de Mise à Jour --}}
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center w-24 h-24 mb-4">
                                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-200" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <path class="text-amber-500" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="{{ $updateRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                </svg>
                                <span class="absolute text-xl font-bold text-amber-600">{{ $updateRate }}%</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Taux de M.A.J</p>
                        </div>

                        {{-- Taux d'Erreur --}}
                        <div class="text-center">
                            <div class="relative inline-flex items-center justify-center w-24 h-24 mb-4">
                                <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                    <path class="text-gray-200" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                    <path class="text-red-500" stroke="currentColor" stroke-width="2" fill="none" stroke-dasharray="{{ $errorRate }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                </svg>
                                <span class="absolute text-xl font-bold text-red-600">{{ $errorRate }}%</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Taux d'Erreur</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Chauffeurs Importés Récemment --}}
                @if($successCount > 0)
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                Chauffeurs Importés ({{ $successCount }} au total)
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center py-8">
                                <div class="h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-users text-green-600 text-2xl"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Importation Réussie</h4>
                                <p class="text-gray-600 mb-4">{{ $successCount }} chauffeur(s) ont été importés avec succès dans la base de données.</p>
                                <a href="{{ route('admin.drivers.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-eye"></i>
                                    Voir les chauffeurs
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Erreurs Détaillées --}}
                @if(!empty($errorRows))
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                Erreurs Détaillées ({{ count($errorRows) }})
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($errorRows as $error)
                                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <div class="h-6 w-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                <i class="fas fa-times text-red-600 text-xs"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-red-800 text-sm">
                                                    Ligne {{ $error['line'] }}
                                                </div>
                                                @if(is_array($error['errors']))
                                                    @foreach($error['errors'] as $err)
                                                        <div class="text-red-700 text-sm mt-1">
                                                            {{ $err }}
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-red-700 text-sm mt-1">
                                                        {{ $error['errors'] ?? $error['error'] ?? 'Erreur inconnue' }}
                                                    </div>
                                                @endif
                                                @if(!empty($error['data']))
                                                    <div class="mt-2 p-2 bg-red-100 rounded text-xs text-red-600 font-mono overflow-x-auto">
                                                        {{ is_array($error['data']) ? json_encode($error['data'], JSON_UNESCAPED_UNICODE) : $error['data'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions de Suivi --}}
            <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-600"></i>
                    Actions Recommandées
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($successCount > 0)
                        <div class="p-6 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex items-center gap-3 mb-3">
                                <i class="fas fa-eye text-green-600 text-lg"></i>
                                <h4 class="font-semibold text-green-800">Vérifier les Importations</h4>
                            </div>
                            <p class="text-green-700 text-sm mb-4">
                                Consultez les chauffeurs importés pour valider les données
                            </p>
                            <a href="{{ route('admin.drivers.index') }}?recent_import=1"
                               class="inline-flex items-center text-green-600 hover:text-green-800 font-medium text-sm">
                                Voir les chauffeurs
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    @endif

                    @if(count($errorRows) > 0)
                        <div class="p-6 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-center gap-3 mb-3">
                                <i class="fas fa-tools text-red-600 text-lg"></i>
                                <h4 class="font-semibold text-red-800">Corriger les Erreurs</h4>
                            </div>
                            <p class="text-red-700 text-sm mb-4">
                                Corrigez les erreurs et réimportez les lignes échouées
                            </p>
                            <a href="{{ route('admin.drivers.import.show') }}"
                               class="inline-flex items-center text-red-600 hover:text-red-800 font-medium text-sm">
                                Nouvelle importation
                                <i class="fas fa-arrow-right ml-2 text-xs"></i>
                            </a>
                        </div>
                    @endif

                    <div class="p-6 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="fas fa-download text-blue-600 text-lg"></i>
                            <h4 class="font-semibold text-blue-800">Exporter les Résultats</h4>
                        </div>
                        <p class="text-blue-700 text-sm mb-4">
                            Téléchargez un rapport détaillé de l'importation
                        </p>
                        <button onclick="downloadReport()"
                                class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Télécharger le rapport
                            <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

        @else
            {{-- Aucun Résultat --}}
            <div class="text-center py-12">
                <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun résultat d'importation</h3>
                <p class="text-gray-600 mb-6">Aucun résultat d'importation n'a été trouvé pour cette session.</p>
                <a href="{{ route('admin.drivers.import.show') }}"
                   class="btn-primary px-6 py-3 inline-flex items-center gap-2 font-semibold">
                    <i class="fas fa-plus text-sm"></i>
                    Démarrer une Importation
                </a>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
function downloadReport() {
    const reportData = {
        successCount: {{ $successCount ?? 0 }},
        errorCount: {{ count($errorRows) }},
        totalRecords: {{ $totalRecords ?? ($successCount + count($errorRows)) }},
        errors: @json($errorRows ?? [])
    };

    const csvContent = generateCSVReport(reportData);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `import_chauffeurs_report_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

function generateCSVReport(data) {
    let csv = 'Type,Nombre,Pourcentage\n';
    const total = data.totalRecords || 0;

    if (total > 0) {
        csv += `Total traité,${total},100%\n`;
        csv += `Importés avec succès,${data.successCount},${((data.successCount / total) * 100).toFixed(1)}%\n`;
        csv += `Échecs,${data.errorCount},${((data.errorCount / total) * 100).toFixed(1)}%\n`;
    }

    if (data.errors && data.errors.length > 0) {
        csv += '\n\nLigne,Erreur,Données\n';
        data.errors.forEach(error => {
            const errorText = Array.isArray(error.errors) ? error.errors.join('; ') : (error.errors || error.error || 'Erreur inconnue');
            csv += `${error.line},"${errorText}","${JSON.stringify(error.data).replace(/"/g, '""')}"\n`;
        });
    }

    return csv;
}

// Animation pour les graphiques circulaires
document.addEventListener('DOMContentLoaded', function() {
    const circles = document.querySelectorAll('svg path[stroke-dasharray]');
    circles.forEach(circle => {
        const dashArray = circle.getAttribute('stroke-dasharray');
        circle.style.strokeDasharray = '0, 100';

        setTimeout(() => {
            circle.style.transition = 'stroke-dasharray 1.5s ease-in-out';
            circle.style.strokeDasharray = dashArray;
        }, 500);
    });
});
</script>
@endpush

@push('styles')
<style>
.btn-primary {
    @apply bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl border border-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200;
}

.btn-outline {
    @apply bg-transparent text-blue-600 rounded-xl border-2 border-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200;
}

/* Animation des graphiques */
svg path {
    transition: stroke-dasharray 1.5s ease-in-out;
}
</style>
@endpush
@endsection