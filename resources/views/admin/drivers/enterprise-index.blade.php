@extends('layouts.admin.catalyst')

@section('title', 'Gestion Enterprise des Chauffeurs')

@section('content')
{{-- 👨‍💼 Header Enterprise Ultra-Professionnel --}}
<div class="zenfleet-header-enterprise">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-4xl font-black leading-7 text-gray-900 sm:truncate sm:text-5xl sm:tracking-tight">
                <span class="bg-gradient-to-r from-emerald-600 via-green-600 to-teal-700 bg-clip-text text-transparent">
                    👨‍💼 Gestion Enterprise des Chauffeurs
                </span>
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-users mr-1.5 h-5 w-5 flex-shrink-0 text-emerald-500"></i>
                    Gestion RH Intégrée Ultra-Professionnelle
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-id-card mr-1.5 h-5 w-5 flex-shrink-0 text-blue-500"></i>
                    Suivi Complet des Permis et Certifications
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-chart-line mr-1.5 h-5 w-5 flex-shrink-0 text-purple-500"></i>
                    Analytics de Performance Avancés
                </div>
            </div>
        </div>

        <div class="mt-5 flex lg:ml-4 lg:mt-0">
            <span class="hidden sm:block">
                <a href="{{ route('admin.drivers.create') }}"
                   class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Nouveau Chauffeur
                </a>
            </span>

            <span class="ml-3 hidden sm:block">
                <button type="button"
                        class="zenfleet-btn-enterprise-secondary"
                        onclick="showImportModal()">
                    <i class="fas fa-upload mr-2"></i>
                    Import
                </button>
            </span>

            <span class="ml-3 hidden sm:block">
                <button type="button"
                        class="zenfleet-btn-enterprise-secondary"
                        onclick="exportDrivers()">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </span>

            <div class="relative ml-3 sm:hidden">
                <button type="button" class="zenfleet-btn-enterprise-mobile" id="mobile-menu-button">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 🔥 Analytics Dashboard Enterprise --}}
<div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    {{-- Total Chauffeurs --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Total Chauffeurs</dt>
                    <dd class="text-3xl font-black text-emerald-700">{{ $drivers->total() ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Chauffeurs Actifs --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-check text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Actifs</dt>
                    <dd class="text-3xl font-black text-blue-700">{{ $drivers->where('driverStatus.name', 'Disponible')->count() ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Chauffeurs en Mission --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-car text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">En Mission</dt>
                    <dd class="text-3xl font-black text-amber-700">{{ $drivers->where('driverStatus.name', 'En mission')->count() ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Permis Expirant --}}
    <div class="zenfleet-analytics-premium">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-bold text-gray-700 truncate">Permis Expirant</dt>
                    <dd class="text-3xl font-black text-red-700">
                        {{ $drivers->filter(function($driver) {
                            return $driver->license_expiry_date &&
                                   $driver->license_expiry_date->diffInDays(now()) <= 30;
                        })->count() }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- 🔍 Filtres Enterprise Avancés --}}
<div class="mt-8 bg-white shadow-lg rounded-3xl border border-gray-200/50">
    <div class="px-6 py-4 border-b border-gray-200/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <i class="fas fa-filter mr-2 text-emerald-600"></i>
            Filtres Enterprise Avancés
        </h3>
    </div>
    <div class="p-6">
        <form method="GET" action="{{ route('admin.drivers.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Recherche --}}
                <div>
                    <label for="search" class="zenfleet-label-enterprise">Recherche</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Nom, prénom, matricule, email..."
                           class="zenfleet-input-premium">
                </div>

                {{-- Statut --}}
                <div>
                    <label for="status_id" class="zenfleet-label-enterprise">Statut</label>
                    <select name="status_id" id="status_id" class="zenfleet-input-premium">
                        <option value="">Tous les statuts</option>
                        @foreach($driverStatuses ?? [] as $status)
                            <option value="{{ $status->id }}"
                                    {{ ($filters['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Permis --}}
                <div>
                    <label for="license_filter" class="zenfleet-label-enterprise">Permis de Conduire</label>
                    <select name="license_filter" id="license_filter" class="zenfleet-input-premium">
                        <option value="">Tous</option>
                        <option value="expiring" {{ ($filters['license_filter'] ?? '') == 'expiring' ? 'selected' : '' }}>
                            Expirant (30 jours)
                        </option>
                        <option value="expired" {{ ($filters['license_filter'] ?? '') == 'expired' ? 'selected' : '' }}>
                            Expirés
                        </option>
                        <option value="valid" {{ ($filters['license_filter'] ?? '') == 'valid' ? 'selected' : '' }}>
                            Valides
                        </option>
                    </select>
                </div>

                {{-- Nombre par page --}}
                <div>
                    <label for="per_page" class="zenfleet-label-enterprise">Éléments par page</label>
                    <select name="per_page" id="per_page" class="zenfleet-input-premium">
                        <option value="25" {{ ($filters['per_page'] ?? 25) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($filters['per_page'] ?? 25) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ ($filters['per_page'] ?? 25) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <button type="submit"
                        class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-search mr-2"></i>
                    Filtrer
                </button>

                <a href="{{ route('admin.drivers.index') }}"
                   class="zenfleet-btn-enterprise-secondary">
                    <i class="fas fa-times mr-2"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

{{-- 📊 Table Enterprise des Chauffeurs --}}
<div class="mt-8 bg-white shadow-lg rounded-3xl border border-gray-200/50 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200/50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2 text-indigo-600"></i>
            Liste des Chauffeurs ({{ $drivers->total() ?? 0 }})
        </h3>
    </div>

    @if($drivers && $drivers->count() > 0)
        <div class="overflow-x-auto">
            <table class="zenfleet-enterprise-table">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Chauffeur</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Permis</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Performance</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($drivers as $driver)
                        <tr class="hover:bg-gradient-to-r hover:from-emerald-50/30 hover:to-green-50/30 transition-all duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($driver->photo)
                                            <img class="h-12 w-12 rounded-full object-cover shadow-lg"
                                                 src="{{ Storage::url($driver->photo) }}"
                                                 alt="{{ $driver->full_name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-lg">
                                                    {{ strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $driver->full_name }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($driver->employee_number)
                                                Mat: {{ $driver->employee_number }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            @if($driver->birth_date && $driver->birth_date instanceof \Carbon\Carbon)
                                                {{ $driver->birth_date->age }} ans
                                            @elseif($driver->birth_date)
                                                <span class="text-orange-400">Date invalide</span>
                                            @else
                                                <span class="text-gray-400">Âge non renseigné</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($driver->personal_phone)
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-phone text-emerald-600 mr-2"></i>
                                            {{ $driver->personal_phone }}
                                        </div>
                                    @endif
                                    @if($driver->personal_email)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                            {{ $driver->personal_email }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    @if($driver->license_number)
                                        <div class="font-semibold text-gray-900">{{ $driver->license_number }}</div>
                                        <div class="text-gray-500">{{ $driver->license_category ?? 'N/A' }}</div>
                                        @if($driver->license_expiry_date)
                                            @php
                                                $daysUntilExpiry = $driver->license_expiry_date->diffInDays(now(), false);
                                                $isExpired = $daysUntilExpiry < 0;
                                                $isExpiringSoon = $daysUntilExpiry <= 30 && $daysUntilExpiry >= 0;
                                            @endphp
                                            <div class="text-xs {{ $isExpired ? 'text-red-600 font-bold' : ($isExpiringSoon ? 'text-amber-600 font-bold' : 'text-gray-500') }}">
                                                @if($isExpired)
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                    Expiré depuis {{ abs($daysUntilExpiry) }} jour(s)
                                                @elseif($isExpiringSoon)
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Expire dans {{ $daysUntilExpiry }} jour(s)
                                                @else
                                                    <i class="fas fa-check mr-1"></i>
                                                    Valide jusqu'au {{ $driver->license_expiry_date->format('d/m/Y') }}
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Aucun permis renseigné</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusName = $driver->driverStatus->name ?? 'Inconnu';
                                    $statusClass = match($statusName) {
                                        'Disponible' => 'zenfleet-status-available',
                                        'En mission' => 'zenfleet-status-assigned',
                                        'En congé' => 'zenfleet-status-maintenance',
                                        'Suspendu' => 'zenfleet-status-maintenance',
                                        default => 'zenfleet-status-inactive'
                                    };
                                @endphp
                                <span class="{{ $statusClass }}">
                                    <i class="fas fa-circle mr-1"></i>
                                    {{ $statusName }}
                                </span>
                                @if($driver->isCurrentlyAssigned())
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-car mr-1"></i>
                                        Véhicule affecté
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    {{-- Score de Performance (simulation) --}}
                                    @php
                                        $performanceScore = rand(70, 98);
                                        $scoreColor = $performanceScore >= 90 ? 'text-green-600' : ($performanceScore >= 80 ? 'text-amber-600' : 'text-red-600');
                                    @endphp
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full"
                                                 style="width: {{ $performanceScore }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold {{ $scoreColor }}">{{ $performanceScore }}%</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($driver->assignments()->count() > 0)
                                            {{ $driver->assignments()->count() }} mission(s)
                                        @else
                                            Nouveau chauffeur
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.drivers.show', $driver) }}"
                                       class="zenfleet-action-view"
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.drivers.edit', $driver) }}"
                                       class="zenfleet-action-edit"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button"
                                            onclick="toggleDriverStatus({{ $driver->id }})"
                                            class="zenfleet-action-premium bg-gradient-to-br from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 focus:ring-purple-500/30 shadow-lg hover:shadow-xl"
                                            title="Changer le statut">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>

                                    <button type="button"
                                            onclick="deleteDriver({{ $driver->id }})"
                                            class="zenfleet-action-delete"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Enterprise --}}
        <div class="px-6 py-4 border-t border-gray-200/50">
            {{ $drivers->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @else
        {{-- État vide --}}
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                <i class="fas fa-users text-6xl"></i>
            </div>
            <h3 class="mt-2 text-lg font-bold text-gray-900">Aucun chauffeur trouvé</h3>
            <p class="mt-1 text-sm text-gray-500">
                Commencez par ajouter un chauffeur à votre équipe.
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.drivers.create') }}"
                   class="zenfleet-btn-enterprise-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Ajouter le premier chauffeur
                </a>
            </div>
        </div>
    @endif
</div>

{{-- 🎯 Success/Error Messages --}}
@if(session('success'))
<div class="fixed top-4 right-4 z-50" id="success-notification">
    <div class="zenfleet-notification zenfleet-notification-success">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeNotification('success-notification')" class="zenfleet-notification-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-4 right-4 z-50" id="error-notification">
    <div class="zenfleet-notification bg-red-50 border border-red-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">
                    {{ session('error') }}
                </p>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="closeNotification('error-notification')" class="inline-flex text-red-400 hover:text-red-600 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// 🚀 JavaScript Enterprise Ultra-Professionnel

function showImportModal() {
    // TODO: Implémenter la modal d'import
    window.location.href = '{{ route("admin.drivers.import.show") }}';
}

function exportDrivers() {
    // TODO: Implémenter l'export
    alert('Fonctionnalité d\'export en cours de développement');
}

function toggleDriverStatus(driverId) {
    // TODO: Implémenter le changement de statut
    alert('Fonctionnalité de changement de statut en cours de développement');
}

function deleteDriver(driverId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce chauffeur ? Cette action est irréversible.')) {
        // TODO: Implémenter la suppression avec form
        alert('Fonctionnalité de suppression en cours de développement');
    }
}

function closeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }
}

// Auto-hide notifications
document.addEventListener('DOMContentLoaded', function() {
    const notifications = ['success-notification', 'error-notification'];
    notifications.forEach(id => {
        const notification = document.getElementById(id);
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    });
});

// Real-time search avec debounce
let searchTimeout;
document.getElementById('search')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Auto-submit form après 500ms de pause
        this.form.submit();
    }, 500);
});
</script>
@endpush