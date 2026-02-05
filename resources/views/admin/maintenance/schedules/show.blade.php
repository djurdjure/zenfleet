{{-- resources/views/admin/maintenance/schedules/show.blade.php --}}
@extends('layouts.admin.catalyst')
@section('title', 'Détails Planification - ZenFleet')

@push('styles')
<style>
.fade-in {
 animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
 from { opacity: 0; transform: translateY(10px); }
 to { opacity: 1; transform: translateY(0); }
}

.detail-card {
 background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
 border: 1px solid #e2e8f0;
 transition: all 0.3s ease;
}

.detail-card:hover {
 box-shadow: 0 2px 8px rgba(0,0,0,0.06);
 border-color: #cbd5e1;
}

.badge-status {
 display: inline-flex;
 align-items: center;
 padding: 0.5rem 1rem;
 border-radius: 9999px;
 font-size: 0.875rem;
 font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="fade-in">
 {{-- En-tête --}}
 <div class="mb-6">
 <nav class="flex items-center space-x-2 text-sm font-medium text-gray-600 mb-4">
 <a href="{{ route('admin.maintenance.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <i class="fas fa-wrench mr-1"></i> Maintenance
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <a href="{{ route('admin.maintenance.schedules.index') }}" class="hover:text-blue-600 transition-colors">
 Planifications
 </a>
 <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
 <span class="text-blue-600 font-semibold">Détails</span>
 </nav>

 <div class="flex items-center justify-between">
 <h1 class="text-2xl font-bold text-gray-900">Détails de la Planification</h1>
 <div class="flex space-x-3">
 <a href="{{ route('admin.maintenance.schedules.edit', $schedule->id) }}"
 class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:border-blue-500 hover:text-blue-600 transition-all">
 <i class="fas fa-edit mr-2"></i>
 Modifier
 </a>
 <a href="{{ route('admin.maintenance.schedules.index') }}"
 class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:border-blue-500 hover:text-blue-600 transition-all">
 <i class="fas fa-arrow-left mr-2"></i>
 Retour
 </a>
 </div>
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 {{-- Colonne principale --}}
 <div class="lg:col-span-2 space-y-6">
 {{-- Informations du véhicule --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-car text-blue-600 mr-2"></i>
 Véhicule
 </h3>
 <div class="grid grid-cols-2 gap-4">
 <div>
 <p class="text-sm text-gray-600">Immatriculation</p>
 <p class="text-base font-semibold text-gray-900">{{ $schedule->vehicle->registration_plate }}</p>
 </div>
 <div>
 <p class="text-sm text-gray-600">Marque & Modèle</p>
 <p class="text-base font-semibold text-gray-900">{{ $schedule->vehicle->brand }} {{ $schedule->vehicle->model }}</p>
 </div>
 @if($schedule->vehicle->current_mileage)
 <div>
 <p class="text-sm text-gray-600">Kilométrage actuel</p>
 <p class="text-base font-semibold text-gray-900">{{ number_format($schedule->vehicle->current_mileage, 0, ',', ' ') }} km</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Type de maintenance --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-wrench text-green-600 mr-2"></i>
 Type de Maintenance
 </h3>
 <div class="grid grid-cols-2 gap-4">
 <div>
 <p class="text-sm text-gray-600">Nom</p>
 <p class="text-base font-semibold text-gray-900">{{ $schedule->maintenanceType->name }}</p>
 </div>
 <div>
 <p class="text-sm text-gray-600">Catégorie</p>
 <p class="text-base font-semibold text-gray-900">{{ ucfirst($schedule->maintenanceType->category) }}</p>
 </div>
 </div>
 </div>

 {{-- Intervalles --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
 Intervalles et Échéances
 </h3>
 <div class="grid grid-cols-2 gap-4">
 @if($schedule->interval_km)
 <div>
 <p class="text-sm text-gray-600">Intervalle kilométrique</p>
 <p class="text-base font-semibold text-gray-900">{{ number_format($schedule->interval_km, 0, ',', ' ') }} km</p>
 </div>
 @endif
 @if($schedule->interval_days)
 <div>
 <p class="text-sm text-gray-600">Intervalle temporel</p>
 <p class="text-base font-semibold text-gray-900">{{ $schedule->interval_days }} jours</p>
 </div>
 @endif
 @if($schedule->next_due_date)
 <div>
 <p class="text-sm text-gray-600">Prochaine échéance (date)</p>
 <p class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->next_due_date)->format('d/m/Y') }}</p>
 </div>
 @endif
 @if($schedule->next_due_km)
 <div>
 <p class="text-sm text-gray-600">Prochaine échéance (km)</p>
 <p class="text-base font-semibold text-gray-900">{{ number_format($schedule->next_due_km, 0, ',', ' ') }} km</p>
 </div>
 @endif
 </div>
 </div>

 {{-- Notes --}}
 @if($schedule->notes)
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
 Notes
 </h3>
 <p class="text-gray-700">{{ $schedule->notes }}</p>
 </div>
 @endif
 </div>

 {{-- Colonne latérale --}}
 <div class="space-y-6">
 {{-- Statut --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4">Statut</h3>
 @if($schedule->is_active)
 <span class="badge-status bg-green-50 text-green-700 border border-green-200">
 <i class="fas fa-check-circle mr-2"></i>
 Active
 </span>
 @else
 <span class="badge-status bg-gray-50 text-gray-700 border border-gray-200">
 <i class="fas fa-pause-circle mr-2"></i>
 Inactive
 </span>
 @endif
 </div>

 {{-- Alertes --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-bell text-orange-600 mr-2"></i>
 Alertes
 </h3>
 <div class="space-y-3">
 @if($schedule->alert_days_before)
 <div class="flex items-center text-sm">
 <i class="fas fa-calendar text-gray-400 mr-2"></i>
 <span class="text-gray-700">{{ $schedule->alert_days_before }} jours avant</span>
 </div>
 @endif
 @if($schedule->alert_km_before)
 <div class="flex items-center text-sm">
 <i class="fas fa-tachometer-alt text-gray-400 mr-2"></i>
 <span class="text-gray-700">{{ number_format($schedule->alert_km_before, 0, ',', ' ') }} km avant</span>
 </div>
 @endif
 </div>
 </div>

 {{-- Fournisseur --}}
 @if($schedule->maintenanceProvider)
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
 <i class="fas fa-building text-blue-600 mr-2"></i>
 Fournisseur
 </h3>
 <p class="text-base font-semibold text-gray-900">{{ $schedule->maintenanceProvider->name }}</p>
 @if($schedule->maintenanceProvider->phone)
 <p class="text-sm text-gray-600 mt-2">
 <i class="fas fa-phone mr-2"></i>{{ $schedule->maintenanceProvider->phone }}
 </p>
 @endif
 </div>
 @endif

 {{-- Dates de création/modification --}}
 <div class="detail-card rounded-lg p-6">
 <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations système</h3>
 <div class="space-y-2 text-sm">
 <div>
 <span class="text-gray-600">Créée le :</span>
 <span class="text-gray-900 font-medium">{{ $schedule->created_at->format('d/m/Y H:i') }}</span>
 </div>
 <div>
 <span class="text-gray-600">Modifiée le :</span>
 <span class="text-gray-900 font-medium">{{ $schedule->updated_at->format('d/m/Y H:i') }}</span>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>
@endsection
