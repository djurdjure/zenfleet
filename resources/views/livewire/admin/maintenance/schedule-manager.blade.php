<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 -m-6 p-6">
 <div>

 {{-- En-tête avec statistiques --}}
 <div class="mb-8">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
 <div>
 <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
 <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
 <i class="fas fa-calendar-alt text-white text-lg"></i>
 </div>
 Planification Maintenance
 </h1>
 <p class="mt-2 text-gray-600 text-lg">
 Gestion enterprise-grade des planifications de maintenance avec alertes automatiques
 </p>
 </div>
 <div class="mt-4 lg:mt-0 flex gap-3">
 <button wire:click="create"
 class="btn-primary px-6 py-3 flex items-center gap-2 font-semibold">
 <i class="fas fa-plus text-sm"></i>
 Nouvelle Planification
 </button>
 <button wire:click="export"
 class="btn-outline px-6 py-3 flex items-center gap-2 font-semibold">
 <i class="fas fa-download text-sm"></i>
 Exporter
 </button>
 </div>
 </div>

 {{-- Statistiques du tableau de bord --}}
 <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center">
 <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Total Planifications</p>
 <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center">
 <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">En Retard</p>
 <p class="text-2xl font-bold text-red-600">{{ number_format($stats['overdue']) }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center">
 <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-clock text-orange-600 text-xl"></i>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Bientôt Dues</p>
 <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['due_soon']) }}</p>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center">
 <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
 <i class="fas fa-calendar-plus text-green-600 text-xl"></i>
 </div>
 <div class="ml-4">
 <p class="text-sm font-medium text-gray-600">Planifiées</p>
 <p class="text-2xl font-bold text-green-600">{{ number_format($stats['scheduled']) }}</p>
 </div>
 </div>
 </div>
 </div>
 </div>

 {{-- Barre d'outils et filtres --}}
 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 mb-8">
 <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
 {{-- Modes de vue --}}
 <div class="flex bg-gray-100 rounded-lg p-1">
 <button wire:click="setViewMode('list')"
 class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors
 {{ $viewMode === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
 <i class="fas fa-list"></i>
 Liste
 </button>
 <button wire:click="setViewMode('calendar')"
 class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors
 {{ $viewMode === 'calendar' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
 <i class="fas fa-calendar"></i>
 Calendrier
 </button>
 </div>

 {{-- Barre de recherche --}}
 <div class="flex-1 max-w-md">
 <div class="relative">
 <input wire:model.live.debounce.500ms="search"
 type="text"
 placeholder="Rechercher par véhicule ou type..."
 wire:loading.attr="aria-busy"
 wire:target="search"
 class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <i class="fas fa-search text-gray-400"></i>
 </div>
 @if($search)
 <button wire:click="$set('search', '')"
 class="absolute inset-y-0 right-0 pr-3 flex items-center">
 <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
 </button>
 @endif
 </div>
 </div>

 {{-- Filtres rapides --}}
 <div class="flex flex-wrap gap-2">
 <select wire:model.live="statusFilter"
 class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
 <option value="all">Tous les statuts</option>
 <option value="overdue">En retard</option>
 <option value="due_soon">Bientôt dues</option>
 <option value="scheduled">Planifiées</option>
 <option value="inactive">Inactives</option>
 </select>

 <select wire:model.live="vehicleFilter"
 class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
 <option value="all">Tous les véhicules</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }}</option>
 @endforeach
 </select>

 <select wire:model.live="typeFilter"
 class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
 <option value="all">Tous les types</option>
 @foreach($maintenanceTypes as $type)
 <option value="{{ $type->id }}">{{ $type->name }}</option>
 @endforeach
 </select>
 </div>
 </div>

 {{-- Actions en lot --}}
 @if(!empty($selectedSchedules))
 <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
 <div class="flex items-center justify-between">
 <span class="text-sm font-medium text-blue-800">
 {{ count($selectedSchedules) }} planification(s) sélectionnée(s)
 </span>
 <div class="flex gap-2">
 <button wire:click="bulkAction('activate')"
 class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
 Activer
 </button>
 <button wire:click="bulkAction('deactivate')"
 class="px-3 py-1 bg-orange-600 text-white rounded text-sm hover:bg-orange-700">
 Désactiver
 </button>
 <button wire:click="bulkAction('create_alerts')"
 class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
 Créer Alertes
 </button>
 <button wire:click="bulkAction('delete')"
 class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700"
 onclick="return confirm('Êtes-vous sûr de vouloir supprimer ces planifications ?')">
 Supprimer
 </button>
 </div>
 </div>
 </div>
 @endif
 </div>

 {{-- Contenu principal selon le mode de vue --}}
 @if($viewMode === 'list')
 {{-- Vue Liste --}}
 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
 <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
 <div class="flex items-center justify-between">
 <h3 class="text-lg font-semibold text-gray-900">Planifications de Maintenance</h3>
 <div class="flex items-center gap-2">
 <label class="flex items-center gap-2 text-sm">
 <input type="checkbox"
 wire:model.live="selectAll"
 class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
 Tout sélectionner
 </label>
 </div>
 </div>
 </div>

 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-gray-200">
 <thead class="bg-gray-50">
 <tr>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 <input type="checkbox" wire:model.live="selectAll"
 class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Véhicule
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Type de Maintenance
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Prochaine Échéance
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Statut
 </th>
 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
 Actions
 </th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-gray-200">
 @forelse($schedules as $schedule)
 <tr class="hover:bg-gray-50 transition-colors">
 <td class="px-6 py-4 whitespace-nowrap">
 <input type="checkbox"
 wire:model.live="selectedSchedules"
 value="{{ $schedule->id }}"
 class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="flex items-center">
 <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
 <i class="fas fa-car text-blue-600"></i>
 </div>
 <div class="ml-3">
 <div class="text-sm font-medium text-gray-900">
 {{ $schedule->vehicle->registration_plate }}
 </div>
 <div class="text-sm text-gray-500">
 {{ $schedule->vehicle->brand }} {{ $schedule->vehicle->model }}
 </div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="text-sm font-medium text-gray-900">
 {{ $schedule->maintenanceType->name }}
 </div>
 <div class="text-sm text-gray-500">
 {!! $schedule->maintenanceType->getCategoryBadge() !!}
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 <div class="space-y-1">
 @if($schedule->next_due_date)
 <div class="text-sm text-gray-900">
 📅 {{ $schedule->next_due_date->format('d/m/Y') }}
 @if($schedule->days_remaining !== null)
 <span class="text-xs {{ $schedule->days_remaining < 0 ? 'text-red-600' : 'text-gray-500' }}">
 ({{ $schedule->days_remaining < 0 ? 'En retard de' : 'Dans' }} {{ abs($schedule->days_remaining) }} j)
 </span>
 @endif
 </div>
 @endif
 @if($schedule->next_due_mileage)
 <div class="text-sm text-gray-900">
 🛣️ {{ number_format($schedule->next_due_mileage, 0, ',', ' ') }} km
 @if($schedule->kilometers_remaining !== null)
 <span class="text-xs {{ $schedule->kilometers_remaining < 0 ? 'text-red-600' : 'text-gray-500' }}">
 ({{ $schedule->kilometers_remaining < 0 ? 'Dépassé de' : 'Reste' }} {{ number_format(abs($schedule->kilometers_remaining), 0, ',', ' ') }} km)
 </span>
 @endif
 </div>
 @endif
 </div>
 </td>
 <td class="px-6 py-4 whitespace-nowrap">
 {!! $schedule->getStatusBadge() !!}
 </td>
 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
 <div class="flex items-center gap-2">
 <button wire:click="edit({{ $schedule->id }})"
 class="text-blue-600 hover:text-blue-900 transition-colors">
 <i class="fas fa-edit"></i>
 </button>
 <button wire:click="toggleStatus({{ $schedule->id }})"
 class="text-{{ $schedule->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $schedule->is_active ? 'orange' : 'green' }}-900 transition-colors">
 <i class="fas fa-{{ $schedule->is_active ? 'pause' : 'play' }}"></i>
 </button>
 <button wire:click="createAlert({{ $schedule->id }})"
 class="text-purple-600 hover:text-purple-900 transition-colors">
 <i class="fas fa-bell"></i>
 </button>
 <button wire:click="delete({{ $schedule->id }})"
 class="text-red-600 hover:text-red-900 transition-colors"
 onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette planification ?')">
 <i class="fas fa-trash"></i>
 </button>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="6" class="px-6 py-12 text-center">
 <div class="flex flex-col items-center">
 <div class="h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
 <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
 </div>
 <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune planification trouvée</h3>
 <p class="text-gray-500 mb-4">Créez votre première planification de maintenance.</p>
 <button wire:click="create"
 class="btn-primary px-6 py-2 flex items-center gap-2">
 <i class="fas fa-plus"></i>
 Créer une planification
 </button>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 </div>

 {{-- Pagination --}}
 <div class="mt-4">
 <x-pagination :paginator="$schedules" :records-per-page="$perPage" wire:model.live="perPage" />
 </div>
 </div>
 @else
 {{-- Vue Calendrier --}}
 <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
 <div class="flex items-center justify-between mb-6">
 <h3 class="text-lg font-semibold text-gray-900">Calendrier des Maintenances</h3>
 <div class="flex items-center gap-2">
 <button wire:click="navigateMonth('previous')"
 class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
 <i class="fas fa-chevron-left"></i>
 </button>
 <span class="text-sm font-medium text-gray-900 min-w-32 text-center">
 {{ \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y') }}
 </span>
 <button wire:click="navigateMonth('next')"
 class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
 <i class="fas fa-chevron-right"></i>
 </button>
 </div>
 </div>

 {{-- Calendrier (implémentation simplifiée pour la démonstration) --}}
 <div class="border border-gray-200 rounded-lg overflow-hidden">
 <div class="grid grid-cols-7 bg-gray-50">
 @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
 <div class="px-4 py-3 text-sm font-medium text-gray-700 text-center border-r border-gray-200 last:border-r-0">
 {{ $day }}
 </div>
 @endforeach
 </div>
 <div class="h-96 flex items-center justify-center text-gray-500">
 <div class="text-center">
 <i class="fas fa-calendar-alt text-4xl mb-4"></i>
 <p>Vue calendrier en cours de développement</p>
 <p class="text-sm mt-2">Utilisez la vue liste pour le moment</p>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Modal de création/édition --}}
 @if($showModal)
 <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
 <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
 <div class="px-6 py-4 border-b border-gray-200">
 <h3 class="text-lg font-semibold text-gray-900">
 {{ $editMode ? 'Modifier la Planification' : 'Nouvelle Planification' }}
 </h3>
 </div>

 <form wire:submit.prevent="save" class="p-6 space-y-6">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Véhicule --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Véhicule *
 </label>
 <select wire:model.live="vehicle_id"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('vehicle_id') border-red-500 @enderror">
 <option value="">Sélectionner un véhicule</option>
 @foreach($vehicles as $vehicle)
 <option value="{{ $vehicle->id }}">
 {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
 </option>
 @endforeach
 </select>
 @error('vehicle_id')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>

 {{-- Type de maintenance --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Type de Maintenance *
 </label>
 <select wire:model.live="maintenance_type_id"
 wire:change="loadMaintenanceTypeDefaults"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('maintenance_type_id') border-red-500 @enderror">
 <option value="">Sélectionner un type</option>
 @foreach($maintenanceTypes as $type)
 <option value="{{ $type->id }}">
 {{ $type->name }} ({{ $type->category_name }})
 </option>
 @endforeach
 </select>
 @error('maintenance_type_id')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Prochaine échéance date --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Prochaine Échéance (Date)
 </label>
 <input wire:model="next_due_date"
 type="date"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('next_due_date') border-red-500 @enderror">
 @error('next_due_date')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>

 {{-- Prochaine échéance kilométrage --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Prochaine Échéance (Kilométrage)
 </label>
 <input wire:model="next_due_mileage"
 type="number"
 placeholder="Ex: 50000"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('next_due_mileage') border-red-500 @enderror">
 @error('next_due_mileage')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Intervalle kilomètres --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Intervalle (Kilomètres)
 </label>
 <input wire:model="interval_km"
 type="number"
 placeholder="Ex: 10000"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('interval_km') border-red-500 @enderror">
 @error('interval_km')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>

 {{-- Intervalle jours --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Intervalle (Jours)
 </label>
 <input wire:model="interval_days"
 type="number"
 placeholder="Ex: 365"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('interval_days') border-red-500 @enderror">
 @error('interval_days')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Alerte kilomètres avant --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Alerte (Kilomètres avant)
 </label>
 <input wire:model="alert_km_before"
 type="number"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alert_km_before') border-red-500 @enderror">
 @error('alert_km_before')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>

 {{-- Alerte jours avant --}}
 <div>
 <label class="block text-sm font-medium text-gray-700 mb-2">
 Alerte (Jours avant)
 </label>
 <input wire:model="alert_days_before"
 type="number"
 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('alert_days_before') border-red-500 @enderror">
 @error('alert_days_before')
 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
 @enderror
 </div>
 </div>

 {{-- Statut actif --}}
 <div>
 <label class="flex items-center gap-2">
 <input wire:model="is_active"
 type="checkbox"
 class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
 <span class="text-sm font-medium text-gray-700">Planification active</span>
 </label>
 </div>

 <div class="flex justify-end gap-3 pt-4">
 <button type="button"
 wire:click="closeModal"
 class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
 Annuler
 </button>
 <button type="submit"
 class="btn-primary px-6 py-2">
 {{ $editMode ? 'Mettre à jour' : 'Créer' }}
 </button>
 </div>
 </form>
 </div>
 </div>
 @endif
 </div>

 {{-- Styles personnalisés --}}
 @push('styles')
 <style>
 .btn-primary {
 @apply bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl border border-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200;
 }

 .btn-outline {
 @apply bg-transparent text-blue-600 rounded-xl border-2 border-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200;
 }
 </style>
 @endpush
</div>
