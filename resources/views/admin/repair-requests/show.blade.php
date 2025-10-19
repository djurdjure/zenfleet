@extends('layouts.admin.catalyst')

@section('title', 'Demande de Réparation #' . $repairRequest->id)

@section('content')
<div class="space-y-6">
 {{-- Header avec actions --}}
 <div class="sm:flex sm:items-center sm:justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900">Demande de Réparation #{{ $repairRequest->id }}</h1>
 <p class="mt-2 text-sm text-gray-700">{{ $repairRequest->vehicle->registration_plate }} - {{ $repairRequest->category_label }}</p>
 </div>
 <div class="mt-4 sm:mt-0 sm:flex sm:space-x-3">
 <a href="{{ route('admin.repair-requests.index') }}"
 class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
 <x-iconify icon="heroicons:arrow-left" class="h-4 w-4 mr-2" / />
 Retour à la liste
 </a>
 @if($repairRequest->canBeEdited())
 <a href="{{ route('admin.repair-requests.edit', $repairRequest) }}"
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
 <x-iconify icon="heroicons:pencil" class="h-4 w-4 mr-2" / />
 Modifier
 </a>
 @endif
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
 {{-- Colonne principale --}}
 <div class="lg:col-span-2 space-y-6">
 {{-- Informations principales --}}
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de la demande</h3>
 </div>
 <div class="px-6 py-4 space-y-4">
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div>
 <dt class="text-sm font-medium text-gray-500">Véhicule</dt>
 <dd class="mt-1 text-sm text-gray-900">
 <a href="{{ route('admin.vehicles.show', $repairRequest->vehicle) }}" class="text-indigo-600 hover:text-indigo-500">
 {{ $repairRequest->vehicle->registration_plate }}
 </a>
 <div class="text-gray-500">{{ $repairRequest->vehicle->brand }} {{ $repairRequest->vehicle->model }}</div>
 </dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Statut</dt>
 <dd class="mt-1">
 <x-status-badge :status="$repairRequest->status" :type="'repair'" />
 </dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
 <dd class="mt-1 text-sm text-gray-900">{{ $repairRequest->category_label }}</dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Priorité</dt>
 <dd class="mt-1">
 <x-priority-badge :priority="$repairRequest->priority" />
 </dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Demandé par</dt>
 <dd class="mt-1 text-sm text-gray-900">{{ $repairRequest->requestedBy->name }}</dd>
 </div>
 <div>
 <dt class="text-sm font-medium text-gray-500">Date de création</dt>
 <dd class="mt-1 text-sm text-gray-900">{{ $repairRequest->created_at->format('d/m/Y H:i') }}</dd>
 </div>
 </div>

 <div>
 <dt class="text-sm font-medium text-gray-500">Description du problème</dt>
 <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $repairRequest->description }}</dd>
 </div>

 @if($repairRequest->additional_notes)
 <div>
 <dt class="text-sm font-medium text-gray-500">Notes additionnelles</dt>
 <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $repairRequest->additional_notes }}</dd>
 </div>
 @endif
 </div>
 </div>

 {{-- Coûts --}}
 @if($repairRequest->estimated_cost || $repairRequest->actual_cost)
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Informations financières</h3>
 </div>
 <div class="px-6 py-4">
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 @if($repairRequest->estimated_cost)
 <div>
 <dt class="text-sm font-medium text-gray-500">Coût estimé</dt>
 <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($repairRequest->estimated_cost, 2) }} DA</dd>
 </div>
 @endif
 @if($repairRequest->actual_cost)
 <div>
 <dt class="text-sm font-medium text-gray-500">Coût réel</dt>
 <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($repairRequest->actual_cost, 2) }} DA</dd>
 </div>
 @endif
 </div>
 </div>
 </div>
 @endif

 {{-- Pièces jointes --}}
 @if($repairRequest->attachments && count($repairRequest->attachments) > 0)
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Pièces jointes</h3>
 </div>
 <div class="px-6 py-4">
 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
 @foreach($repairRequest->attachments as $attachment)
 <div class="border border-gray-200 rounded-lg p-4">
 <div class="flex items-center space-x-3">
 @if(str_contains($attachment['mime_type'], 'image'))
 <x-iconify icon="heroicons:photo" class="h-8 w-8 text-green-500" / />
 @elseif(str_contains($attachment['mime_type'], 'pdf'))
 <x-iconify icon="heroicons:document"-text class="h-8 w-8 text-red-500" / />
 @else
 <x-iconify icon="heroicons:document" class="h-8 w-8 text-gray-500" / />
 @endif
 <div class="flex-1 min-w-0">
 <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
 <p class="text-sm text-gray-500">{{ formatBytes($attachment['size']) }}</p>
 </div>
 </div>
 <div class="mt-3">
 <a href="{{ Storage::url($attachment['file_path']) }}" target="_blank"
 class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
 Télécharger
 </a>
 </div>
 </div>
 @endforeach
 </div>
 </div>
 </div>
 @endif
 </div>

 {{-- Colonne latérale --}}
 <div class="space-y-6">
 {{-- Actions workflow --}}
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Actions</h3>
 </div>
 <div class="px-6 py-4 space-y-3">
 @if($repairRequest->status === 'en_attente' && auth()->user()->hasRole(['Superviseur']))
 <button onclick="approveRequest()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
 <x-iconify icon="heroicons:check" class="h-4 w-4 mr-2" / />
 Approuver
 </button>
 <button onclick="rejectRequest()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
 <x-iconify icon="heroicons:x-mark" class="h-4 w-4 mr-2" / />
 Rejeter
 </button>
 @endif

 @if($repairRequest->status === 'accord_initial' && auth()->user()->hasRole(['Gestionnaire Flotte', 'Admin']))
 <button onclick="validateRequest()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
 <x-iconify icon="heroicons:check-circle" class="h-4 w-4 mr-2" / />
 Valider définitivement
 </button>
 <button onclick="rejectByManager()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
 <x-iconify icon="heroicons:x-circle" class="h-4 w-4 mr-2" / />
 Refuser
 </button>
 @endif

 @if(in_array($repairRequest->status, ['accordee', 'en_cours']) && auth()->user()->hasRole(['Gestionnaire Flotte', 'Admin']))
 <button onclick="updateProgress()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
 <x-iconify icon="heroicons:pencil" class="h-4 w-4 mr-2" / />
 Mettre à jour le statut
 </button>
 @endif
 </div>
 </div>

 {{-- Historique workflow --}}
 <div class="bg-white shadow rounded-lg">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg leading-6 font-medium text-gray-900">Historique</h3>
 </div>
 <div class="px-6 py-4">
 <div class="flow-root">
 <ul class="-mb-8">
 <li>
 <div class="relative pb-8">
 <div class="relative flex space-x-3">
 <div>
 <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
 <x-iconify icon="heroicons:plus" class="h-4 w-4 text-white" / />
 </span>
 </div>
 <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
 <div>
 <p class="text-sm text-gray-500">Demande créée par <span class="font-medium text-gray-900">{{ $repairRequest->requestedBy->name }}</span></p>
 </div>
 <div class="text-right text-sm whitespace-nowrap text-gray-500">
 {{ $repairRequest->created_at->format('d/m/Y H:i') }}
 </div>
 </div>
 </div>
 </div>
 </li>

 @if($repairRequest->supervisor_id)
 <li>
 <div class="relative pb-8">
 <div class="relative flex space-x-3">
 <div>
 <span class="h-8 w-8 rounded-full {{ $repairRequest->supervisor_decision === 'accept' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white">
 @if($repairRequest->supervisor_decision === 'accept')
 <x-iconify icon="heroicons:check" class="h-4 w-4 text-white" / />
 @else
 <x-iconify icon="heroicons:x-mark" class="h-4 w-4 text-white" / />
 @endif
 </span>
 </div>
 <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
 <div>
 <p class="text-sm text-gray-500">
 {{ $repairRequest->supervisor_decision === 'accept' ? 'Approuvée' : 'Rejetée' }} par superviseur
 <span class="font-medium text-gray-900">{{ $repairRequest->supervisor->name }}</span>
 </p>
 @if($repairRequest->supervisor_comments)
 <p class="text-sm text-gray-700 mt-1">{{ $repairRequest->supervisor_comments }}</p>
 @endif
 </div>
 <div class="text-right text-sm whitespace-nowrap text-gray-500">
 {{ $repairRequest->supervisor_decision_at?->format('d/m/Y H:i') }}
 </div>
 </div>
 </div>
 </div>
 </li>
 @endif

 @if($repairRequest->manager_id)
 <li>
 <div class="relative">
 <div class="relative flex space-x-3">
 <div>
 <span class="h-8 w-8 rounded-full {{ $repairRequest->manager_decision === 'validate' ? 'bg-indigo-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white">
 @if($repairRequest->manager_decision === 'validate')
 <x-iconify icon="heroicons:check-circle" class="h-4 w-4 text-white" / />
 @else
 <x-iconify icon="heroicons:x-circle" class="h-4 w-4 text-white" / />
 @endif
 </span>
 </div>
 <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
 <div>
 <p class="text-sm text-gray-500">
 {{ $repairRequest->manager_decision === 'validate' ? 'Validée' : 'Refusée' }} par gestionnaire
 <span class="font-medium text-gray-900">{{ $repairRequest->manager->name }}</span>
 </p>
 @if($repairRequest->manager_comments)
 <p class="text-sm text-gray-700 mt-1">{{ $repairRequest->manager_comments }}</p>
 @endif
 </div>
 <div class="text-right text-sm whitespace-nowrap text-gray-500">
 {{ $repairRequest->manager_decision_at?->format('d/m/Y H:i') }}
 </div>
 </div>
 </div>
 </div>
 </li>
 @endif
 </ul>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>

{{-- Modales pour les actions --}}
@include('admin.repair-requests.partials.modals')

@push('scripts')
<script>
function approveRequest() {
 document.getElementById('approveModal').style.display = 'block';
}

function rejectRequest() {
 document.getElementById('rejectModal').style.display = 'block';
}

function validateRequest() {
 document.getElementById('validateModal').style.display = 'block';
}

function rejectByManager() {
 document.getElementById('rejectByManagerModal').style.display = 'block';
}

function updateProgress() {
 document.getElementById('updateProgressModal').style.display = 'block';
}

function closeModal(modalId) {
 document.getElementById(modalId).style.display = 'none';
}

// Fermer modal en cliquant à l'extérieur
window.onclick = function(event) {
 const modals = ['approveModal', 'rejectModal', 'validateModal', 'rejectByManagerModal', 'updateProgressModal'];
 modals.forEach(modalId => {
 const modal = document.getElementById(modalId);
 if (event.target === modal) {
 modal.style.display = 'none';
 }
 });
}
</script>
@endpush
@endsection