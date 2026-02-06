@extends('layouts.admin.catalyst')

@section('title', 'Fiche Chauffeur - ' . $driver->first_name . ' ' . $driver->last_name)

@section('content')
{{-- ====================================================================
 👤 FICHE CHAUFFEUR - ENTERPRISE GRADE
 ====================================================================
 
 Design aligné avec vehicles/show et le design system:
 - Fond gris clair (bg-gray-50)
 - Cards avec borders simples
 - x-iconify pour toutes les icônes
 - Layout en colonnes responsive
 - Informations organisées en sections claires
 
 @version 2.0-Enterprise
 @since 2025-01-19
 ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
 <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

 {{-- ===============================================
 HEADER AVEC BREADCRUMB ET ACTIONS
 =============================================== --}}
 <div class="mb-6">
 {{-- Breadcrumb --}}
 <nav class="flex items-center gap-2 text-sm text-gray-600 mb-4">
 <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">
 <x-iconify icon="heroicons:home" class="w-4 h-4" />
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <a href="{{ route('admin.drivers.index') }}" class="hover:text-blue-600 transition-colors">
 Gestion des Chauffeurs
 </a>
 <x-iconify icon="heroicons:chevron-right" class="w-4 h-4 text-gray-400" />
 <span class="font-semibold text-gray-900">{{ $driver->first_name }} {{ $driver->last_name }}</span>
 </nav>

 {{-- Header avec titre et actions --}}
 <div class="flex items-start justify-between">
 <div class="flex items-center gap-4">
 {{-- Avatar --}}
 <div class="flex-shrink-0">
 @if($driver->photo)
 <img src="{{ asset('storage/' . $driver->photo) }}"
 alt="Photo de {{ $driver->first_name }}"
 class="w-20 h-20 rounded-full object-cover ring-4 ring-blue-100 shadow-md">
 @else
 <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-4 ring-blue-100 shadow-md">
 <span class="text-2xl font-bold text-white">
 {{ strtoupper(substr($driver->first_name, 0, 1)) }}{{ strtoupper(substr($driver->last_name, 0, 1)) }}
 </span>
 </div>
 @endif
 </div>

 {{-- Infos principales --}}
 <div>
 <h1 class="text-2xl font-bold text-gray-900 mb-1">
 {{ $driver->first_name }} {{ $driver->last_name }}
 </h1>
 <p class="text-sm text-gray-600">
 @if($driver->employee_number)
 Matricule: {{ $driver->employee_number }} •
 @endif
 {{ $driver->organization->name ?? 'Organisation non définie' }}
 </p>
 {{-- Statut --}}
 <div class="mt-2">
 @if($driver->driverStatus)
 @switch($driver->driverStatus->name)
 @case('Disponible')
 <x-badge type="success">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @case('En mission')
 <x-badge type="warning">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @case('En repos')
 <x-badge type="error">{{ $driver->driverStatus->name }}</x-badge>
 @break
 @default
 <x-badge type="gray">{{ $driver->driverStatus->name }}</x-badge>
 @endswitch
 @else
 <x-badge type="gray">Statut non défini</x-badge>
 @endif
 </div>
 </div>
 </div>

 {{-- Actions --}}
 <div class="flex items-center gap-3">
 @can('drivers.update')
 <a href="{{ route('admin.drivers.edit', $driver) }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm hover:shadow-md">
 <x-iconify icon="heroicons:pencil" class="w-5 h-5" />
 <span class="hidden sm:inline">Modifier</span>
 </a>
 @endcan

 <a href="{{ route('admin.drivers.index') }}"
 class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors duration-200 text-sm shadow-sm">
 <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
 <span class="hidden sm:inline">Retour</span>
 </a>
 </div>
 </div>
 </div>

 {{-- ===============================================
 CONTENU PRINCIPAL - LAYOUT EN COLONNES
 =============================================== --}}
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

 {{-- Colonne principale (2/3) --}}
 <div class="lg:col-span-2 space-y-6">

 {{-- 👤 Informations Personnelles --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:user" class="w-6 h-6 text-blue-600" />
 <h2 class="text-lg font-semibold text-gray-900">Informations Personnelles</h2>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Prénom --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prénom</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->first_name }}</p>
 </div>

 {{-- Nom --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nom</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->last_name }}</p>
 </div>

 {{-- Date de naissance --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date de naissance</label>
 <p class="text-sm font-medium text-gray-900">
 @if($driver->birth_date && $driver->birth_date instanceof \Carbon\Carbon)
 {{ $driver->birth_date->format('d/m/Y') }}
 <span class="text-gray-500 text-xs">({{ $driver->birth_date->age }} ans)</span>
 @elseif($driver->birth_date)
 {{ $driver->birth_date }}
 @else
 <span class="text-gray-400 italic">Non renseignée</span>
 @endif
 </p>
 </div>

 {{-- Groupe sanguin --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Groupe sanguin</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->blood_type ?? '-' }}</p>
 </div>

 {{-- Téléphone --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Téléphone personnel</label>
 <p class="text-sm font-medium text-gray-900 flex items-center gap-1">
 @if($driver->personal_phone)
 <x-iconify icon="heroicons:phone" class="w-4 h-4 text-gray-400" />
 {{ $driver->personal_phone }}
 @else
 <span class="text-gray-400 italic">Non renseigné</span>
 @endif
 </p>
 </div>

 {{-- Email --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email personnel</label>
 <p class="text-sm font-medium text-gray-900 flex items-center gap-1">
 @if($driver->personal_email)
 <x-iconify icon="heroicons:envelope" class="w-4 h-4 text-gray-400" />
 {{ $driver->personal_email }}
 @else
 <span class="text-gray-400 italic">Non renseigné</span>
 @endif
 </p>
 </div>

 {{-- Adresse --}}
 <div class="md:col-span-2">
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Adresse</label>
 <p class="text-sm font-medium text-gray-900">
 {{ $driver->address ?? 'Non renseignée' }}
 </p>
 </div>

 {{-- Contact d'urgence --}}
 @if($driver->emergency_contact_name || $driver->emergency_contact_phone)
 <div class="md:col-span-2 pt-4 border-t border-gray-100">
 <div class="flex items-center gap-2 mb-3">
 <x-iconify icon="heroicons:phone" class="w-5 h-5 text-red-600" />
 <label class="block text-xs font-medium text-gray-700 uppercase tracking-wide">Contact d'urgence</label>
 </div>
 <div class="bg-red-50 border border-red-200 rounded-lg p-4">
 @if($driver->emergency_contact_name)
 <p class="text-sm font-medium text-gray-900">{{ $driver->emergency_contact_name }}</p>
 @endif
 @if($driver->emergency_contact_phone)
 <p class="text-sm text-gray-600 mt-1">{{ $driver->emergency_contact_phone }}</p>
 @endif
 @if($driver->emergency_contact_relationship)
 <p class="text-xs text-gray-500 mt-1">{{ $driver->emergency_contact_relationship }}</p>
 @endif
 </div>
 </div>
 @endif
 </div>
 </x-card>

 {{-- 💼 Informations Professionnelles --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:briefcase" class="w-6 h-6 text-emerald-600" />
 <h2 class="text-lg font-semibold text-gray-900">Informations Professionnelles</h2>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Numéro d'employé --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Matricule</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->employee_number ?? 'Non attribué' }}</p>
 </div>

 {{-- Organisation --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Organisation</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->organization->name ?? 'Non définie' }}</p>
 </div>

 {{-- Date de recrutement --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date de recrutement</label>
 <p class="text-sm font-medium text-gray-900">
 @if($driver->recruitment_date)
 {{ $driver->recruitment_date->format('d/m/Y') }}
 <span class="text-gray-500 text-xs">({{ $driver->recruitment_date->diffForHumans() }})</span>
 @else
 <span class="text-gray-400 italic">Non renseignée</span>
 @endif
 </p>
 </div>

 {{-- Fin de contrat --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Fin de contrat</label>
 <p class="text-sm font-medium text-gray-900">
 @if($driver->contract_end_date)
 {{ $driver->contract_end_date->format('d/m/Y') }}
 @if($driver->contract_end_date->isPast())
 <x-badge type="error" size="sm" class="ml-2">Expiré</x-badge>
 @elseif($driver->contract_end_date->diffInDays(now()) <= 30)
 <x-badge type="warning" size="sm" class="ml-2">Expire bientôt</x-badge>
 @endif
 @else
 CDI ou non renseignée
 @endif
 </p>
 </div>

 {{-- Notes --}}
 @if($driver->notes)
 <div class="md:col-span-2">
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Notes</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->notes }}</p>
 </div>
 @endif
 </div>
 </x-card>

 {{-- 🆔 Permis de Conduire --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:identification" class="w-6 h-6 text-purple-600" />
 <h2 class="text-lg font-semibold text-gray-900">Permis de Conduire</h2>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Numéro de permis --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Numéro de permis</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->license_number ?? 'Non renseigné' }}</p>
 </div>

 {{-- Catégorie --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Catégorie</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->license_category ?? 'Non renseignée' }}</p>
 </div>

 {{-- Date de délivrance --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date de délivrance</label>
 <p class="text-sm font-medium text-gray-900">
 @if($driver->license_issue_date)
 {{ $driver->license_issue_date->format('d/m/Y') }}
 @else
 <span class="text-gray-400 italic">Non renseignée</span>
 @endif
 </p>
 </div>

 {{-- Date d'expiration --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date d'expiration</label>
 <p class="text-sm font-medium text-gray-900">
 @if($driver->license_expiry_date)
 {{ $driver->license_expiry_date->format('d/m/Y') }}
 @if($driver->license_expiry_date->isPast())
 <x-badge type="error" size="sm" class="ml-2">Expiré</x-badge>
 @elseif($driver->license_expiry_date->diffInDays(now()) <= 60)
 <x-badge type="warning" size="sm" class="ml-2">Expire bientôt</x-badge>
 @else
 <x-badge type="success" size="sm" class="ml-2">Valide</x-badge>
 @endif
 @else
 <span class="text-gray-400 italic">Non renseignée</span>
 @endif
 </p>
 </div>

 {{-- Autorité --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Autorité de délivrance</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->license_authority ?? 'Non renseignée' }}</p>
 </div>

 {{-- Vérifié --}}
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Statut de vérification</label>
 <div class="flex items-center gap-2">
 @if($driver->license_verified)
 <x-iconify icon="heroicons:check-badge" class="w-5 h-5 text-green-600" />
 <span class="text-sm font-medium text-green-700">Permis vérifié</span>
 @else
 <x-iconify icon="heroicons:exclamation-triangle" class="w-5 h-5 text-amber-600" />
 <span class="text-sm font-medium text-amber-700">Non vérifié</span>
 @endif
 </div>
 </div>
 </div>
 </x-card>
 </div>

 {{-- Colonne secondaire (1/3) --}}
 <div class="space-y-6">

 {{-- 📊 Statistiques --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:chart-bar" class="w-6 h-6 text-indigo-600" />
 <h2 class="text-lg font-semibold text-gray-900">Statistiques</h2>
 </div>

 @if(isset($stats) && count($stats) > 0)
 <div class="space-y-4">
 <div class="bg-blue-50 rounded-lg p-4 text-center">
 <div class="text-2xl font-bold text-blue-600">{{ $stats['total_assignments'] ?? 0 }}</div>
 <div class="text-xs text-blue-700 uppercase tracking-wide mt-1">Affectations totales</div>
 </div>

 <div class="bg-green-50 rounded-lg p-4 text-center">
 <div class="text-2xl font-bold text-green-600">{{ $stats['active_assignments'] ?? 0 }}</div>
 <div class="text-xs text-green-700 uppercase tracking-wide mt-1">En cours</div>
 </div>

 <div class="bg-amber-50 rounded-lg p-4 text-center">
 <div class="text-2xl font-bold text-amber-600">{{ $stats['completed_trips'] ?? 0 }}</div>
 <div class="text-xs text-amber-700 uppercase tracking-wide mt-1">Trajets complétés</div>
 </div>

 <div class="bg-purple-50 rounded-lg p-4 text-center">
 <div class="text-2xl font-bold text-purple-600">{{ $stats['total_km'] ?? 0 }} km</div>
 <div class="text-xs text-purple-700 uppercase tracking-wide mt-1">Kilométrage total</div>
 </div>
 </div>
 @else
 <x-empty-state
 icon="heroicons:chart-bar"
 title="Aucune statistique"
 description="Les statistiques seront disponibles après les premières affectations."
 />
 @endif
 </x-card>

 {{-- 📅 Activité Récente --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:clock" class="w-6 h-6 text-gray-600" />
 <h2 class="text-lg font-semibold text-gray-900">Activité Récente</h2>
 </div>

 @if(isset($recentActivity) && $recentActivity->isNotEmpty())
 <div class="space-y-3">
 @foreach($recentActivity as $activity)
 <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
 <div class="flex-shrink-0 w-8 h-8 bg-blue-100 border border-blue-200 rounded-full flex items-center justify-center">
 <x-iconify :icon="'heroicons:' . ($activity['icon'] ?? 'circle')" class="w-4 h-4 text-blue-600" />
 </div>
 <div class="flex-1 min-w-0">
 <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
 <p class="text-xs text-gray-500 mt-0.5">{{ $activity['date'] ?? 'Date non disponible' }}</p>
 </div>
 </div>
 @endforeach
 </div>
 @else
 <x-empty-state
 icon="heroicons:clock"
 title="Aucune activité"
 description="L'historique d'activité sera disponible après les premières actions."
 />
 @endif
 </x-card>

 {{-- 🔗 Compte Utilisateur --}}
 @if($driver->user)
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:user-circle" class="w-6 h-6 text-blue-600" />
 <h2 class="text-lg font-semibold text-gray-900">Compte Utilisateur</h2>
 </div>

 <div class="space-y-3">
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nom</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->user->name }}</p>
 </div>
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->user->email }}</p>
 </div>
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Rôle</label>
 <p class="text-sm font-medium text-gray-900">{{ $driver->user->roles->pluck('name')->join(', ') ?: 'Aucun rôle' }}</p>
 </div>
 </div>
 </x-card>
 @endif

 {{-- 📝 Métadonnées --}}
 <x-card>
 <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-200">
 <x-iconify icon="heroicons:information-circle" class="w-6 h-6 text-gray-600" />
 <h2 class="text-lg font-semibold text-gray-900">Métadonnées</h2>
 </div>

 <div class="space-y-3">
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Créé le</label>
 <p class="text-sm font-medium text-gray-900">
 {{ $driver->created_at->format('d/m/Y à H:i') }}
 <span class="text-gray-500 text-xs">({{ $driver->created_at->diffForHumans() }})</span>
 </p>
 </div>
 <div>
 <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Dernière modification</label>
 <p class="text-sm font-medium text-gray-900">
 {{ $driver->updated_at->format('d/m/Y à H:i') }}
 <span class="text-gray-500 text-xs">({{ $driver->updated_at->diffForHumans() }})</span>
 </p>
 </div>
 </div>
 </x-card>
 </div>
 </div>

 </div>
</section>
@endsection
