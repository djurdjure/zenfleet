{{-- resources/views/admin/maintenance/surveillance/index.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Surveillance Maintenance - ZenFleet')

@push('styles')
<style>
/* Enterprise-grade animations et styles ultra-modernes - similaires à la page chauffeurs */
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

.metric-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.metric-card:hover {
 transform: translateY(-2px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
 border-color: #cbd5e1;
}

.data-table {
 border-collapse: separate;
 border-spacing: 0;
}

.data-table th {
 position: sticky;
 top: 0;
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
 backdrop-filter: blur(10px);
 z-index: 10;
}

.data-table tbody tr {
 transition: all 0.2s ease-in-out;
}

.data-table tbody tr:hover {
 background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
 transform: translateY(-1px);
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.search-input {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 2px solid #e2e8f0;
 transition: all 0.3s ease;
}

.search-input:focus {
 border-color: #6366f1;
 box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
 background: white;
}

.action-button {
 transition: all 0.2s ease;
}

.action-button:hover {
 transform: translateY(-1px);
 box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stats-grid {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
 gap: 1.5rem;
}
</style>
@endpush

@section('content')
<div class="fade-in">
 {{-- Messages de notification --}}
 @if(session('success'))
 <div id="success-alert" class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm fade-in mb-6">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <i class="fas fa-check-circle text-green-400 text-lg"></i>
 </div>
 <div class="ml-3">
 <p class="text-green-700 font-medium">{{ session('success') }}</p>
 </div>
 <div class="ml-auto pl-3">
 <button onclick="document.getElementById('success-alert').remove()"
 class="text-green-400 hover:text-green-600 transition-colors">
 <i class="fas fa-times"></i>
 </button>
 </div>
 </div>
 </div>
 @endif

 {{-- En-tête compact --}}
 <div class="mb-8">
 <div class="md:flex md:items-center md:justify-between">
 <div class="flex-1 min-w-0">
 <h1 class="text-xl font-semibold leading-6 text-gray-900">Surveillance Maintenance</h1>
 <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
 <div class="mt-2 flex items-center text-sm text-gray-500">
 <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 Surveillance en temps réel des opérations de maintenance
 </div>
 </div>
 </div>
 <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
 <a href="{{ route('admin.maintenance.operations.create') }}" class="action-button inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
 </svg>
 Nouvelle opération
 </a>
 </div>
 </div>
 </div>

 {{-- Statistiques compactes --}}
 <div class="stats-grid mb-8">
 <div class="metric-card hover-scale rounded-lg p-6">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-blue-500 border border-blue-600 rounded-full flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">En cours</p>
 <p class="text-2xl font-bold text-blue-600">{{ $stats['en_cours'] }}</p>
 </div>
 </div>
 </div>

 <div class="metric-card hover-scale rounded-lg p-6">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-orange-500 border border-orange-600 rounded-full flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">Proches (7 jours)</p>
 <p class="text-2xl font-bold text-orange-600">{{ $stats['proches'] }}</p>
 </div>
 </div>
 </div>

 <div class="metric-card hover-scale rounded-lg p-6">
 <div class="flex items-center">
 <div class="flex-shrink-0">
 <div class="w-8 h-8 bg-red-500 border border-red-600 rounded-full flex items-center justify-center">
 <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 </div>
 <div class="ml-4 flex-1">
 <p class="text-sm font-medium text-gray-500">À échéance</p>
 <p class="text-2xl font-bold text-red-600">{{ $stats['echeance'] }}</p>
 </div>
 </div>
 </div>
 </div>

 {{-- Filtres compacts --}}
 <div class="bg-white shadow rounded-lg mb-8">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Filtres de surveillance</h3>
 </div>
 <div class="px-6 py-4">
 <form method="GET" action="{{ route('admin.maintenance.surveillance.index') }}">
 <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
 <div>
 <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Période</label>
 <select name="period" id="period" class="block w-full rounded-md text-sm px-3 py-2 border-2 border-gray-200 focus:border-indigo-500 focus:outline-none">
 <option value="all" {{ $filterPeriod == 'all' ? 'selected' : '' }}>Toutes</option>
 <option value="today" {{ $filterPeriod == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
 <option value="week" {{ $filterPeriod == 'week' ? 'selected' : '' }}>Cette semaine</option>
 <option value="month" {{ $filterPeriod == 'month' ? 'selected' : '' }}>Ce mois</option>
 <option value="overdue" {{ $filterPeriod == 'overdue' ? 'selected' : '' }}>En retard</option>
 </select>
 </div>
 <div>
 <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
 <select name="status" id="status" class="block w-full rounded-md text-sm px-3 py-2 border-2 border-gray-200 focus:border-indigo-500 focus:outline-none">
 <option value="all" {{ $filterStatus == 'all' ? 'selected' : '' }}>Tous</option>
 <option value="terminées" {{ $filterStatus == 'terminées' ? 'selected' : '' }}>Terminées</option>
 <option value="en_retard" {{ $filterStatus == 'en_retard' ? 'selected' : '' }}>En retard</option>
 <option value="en_cours" {{ $filterStatus == 'en_cours' ? 'selected' : '' }}>En cours</option>
 <option value="planifiées" {{ $filterStatus == 'planifiées' ? 'selected' : '' }}>Planifiées</option>
 </select>
 </div>
 <div class="flex items-end">
 <button type="submit" class="action-button w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
 </svg>
 Filtrer
 </button>
 </div>
 </div>
 @if(request()->hasAny(['period', 'status']))
 <div class="mt-3">
 <a href="{{ route('admin.maintenance.surveillance.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
 <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
 </svg>
 Réinitialiser
 </a>
 </div>
 @endif
 </form>
 </div>
 </div>

 {{-- Tableau des maintenances --}}
 <div class="bg-white shadow rounded-lg overflow-hidden">
 <div class="min-w-full overflow-x-auto">
 <table class="data-table min-w-full divide-y divide-gray-200">
 <thead>
 <tr>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Urgence
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Type Maintenance
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Échéance
 </th>
 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Jours restants
 </th>
 <th scope="col" class="relative px-6 py-3">
 <span class="sr-only">Actions</span>
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse($maintenances as $maintenance)
 <tr class="hover:bg-gray-50">
 <td class="px-6 py-4 whitespace-nowrap">
 @if($maintenance->urgency_level === 'critical')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">
 <span class="w-2 h-2 bg-red-500 rounded-full mr-1 animate-pulse"></span>
 Critique
 </span>
 @elseif($maintenance->urgency_level === 'urgent')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
 <span class="w-2 h-2 bg-orange-500 rounded-full mr-1"></span>
 Urgent
 </span>
 @elseif($maintenance->urgency_level === 'warning')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
 <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
 Attention
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
 <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
 Normal
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="flex-shrink-0 h-10 w-10">
 <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
 <span class="text-sm font-medium text-white">
 {{ substr($maintenance->vehicle->registration_plate, 0, 2) }}
 </span>
 </div>
 </div>
 <div class="ml-4">
 <div class="text-sm font-medium text-gray-900">
 {{ $maintenance->vehicle->registration_plate }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $maintenance->vehicle->brand }} {{ $maintenance->vehicle->model }}
 </div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
 {{ $maintenance->maintenanceType->name ?? 'Non défini' }}
 </span>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($maintenance->status === 'completed')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
 Terminé
 </span>
 @elseif($maintenance->status === 'in_progress')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
 En cours
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
 Planifié
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
 {{ $maintenance->next_due_date ? $maintenance->next_due_date->format('d/m/Y') : 'Non définie' }}
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 @if($maintenance->days_remaining_int < 0)
 <span class="text-sm font-medium text-red-600">
 En retard de {{ abs($maintenance->days_remaining_int) }} jour(s)
 </span>
 @elseif($maintenance->days_remaining_int === 0)
 <span class="text-sm font-medium text-orange-600">
 Aujourd'hui
 </span>
 @else
 <span class="text-sm font-medium text-gray-900">
 {{ $maintenance->days_remaining_int }} jour(s)
 </span>
 @endif
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
 <div class="flex items-center space-x-2 justify-end">
 <a href="{{ route('admin.maintenance.schedules.show', $maintenance) }}"
 class="text-indigo-600 hover:text-indigo-900 transition-colors">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
 <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
 </svg>
 </a>
 <a href="{{ route('admin.maintenance.schedules.edit', $maintenance) }}"
 class="text-green-600 hover:text-green-900 transition-colors">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
 </svg>
 </a>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
 <div class="flex flex-col items-center py-12">
 <svg class="w-12 h-12 text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
 </svg>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune maintenance trouvée</h3>
 <p class="text-gray-500 mb-4">Aucune maintenance ne correspond aux critères de filtre sélectionnés.</p>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 {{-- Pagination --}}
 @if($maintenances->hasPages())
 <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
 <div class="flex-1 flex justify-between sm:hidden">
 @if($maintenances->onFirstPage())
 <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
 Précédent
 </span>
 @else
 <a href="{{ $maintenances->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 Précédent
 </a>
 @endif

 @if($maintenances->hasMorePages())
 <a href="{{ $maintenances->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 Suivant
 </a>
 @else
 <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
 Suivant
 </span>
 @endif
 </div>
 <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
 <div>
 <p class="text-sm text-gray-700">
 Affichage de
 <span class="font-medium">{{ $maintenances->firstItem() ?? 0 }}</span>
 à
 <span class="font-medium">{{ $maintenances->lastItem() ?? 0 }}</span>
 sur
 <span class="font-medium">{{ $maintenances->total() }}</span>
 résultats
 </p>
 </div>
 <div>
 {{ $maintenances->withQueryString()->links() }}
 </div>
 </div>
 </div>
 @endif
 </div>
</div>

@push('scripts')
<script>
// Auto-hide success messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
 const successAlert = document.getElementById('success-alert');
 if (successAlert) {
 setTimeout(function() {
 successAlert.style.transition = 'opacity 0.5s ease-out';
 successAlert.style.opacity = '0';
 setTimeout(function() {
 successAlert.remove();
 }, 500);
 }, 5000);
 }
});
</script>
@endpush
@endsection