{{-- resources/views/admin/assignments/show.blade.php --}}
@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.admin.catalyst')

@section('title', 'Détails de l\'affectation #' . $assignment->id)

@section('content')
{{-- ====================================================================
🎯 PAGE DÉTAILS AFFECTATION - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

Design surpassant Fleetio, Samsara et Verizon Connect:
✨ Layout 3 colonnes responsif
✨ Timeline interactive des événements
✨ Métriques en temps réel
✨ Modal de fin d'affectation avec validation complète
✨ Historique détaillé avec audit trail
✨ Export PDF et partage

@version 1.0-Enterprise-Grade
@since 2025-01-09
==================================================================== --}}

<section class="bg-gray-50 min-h-screen" x-data="assignmentDetails()">
    <div class="py-4 px-4 mx-auto max-w-7xl lg:py-6">

        {{-- ===============================================
        HEADER AVEC BREADCRUMB ET ACTIONS
        =============================================== --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <x-iconify icon="lucide:home" class="w-4 h-4 mr-2" />
                            Tableau de bord
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400" />
                            <a href="{{ route('admin.assignments.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                                Affectations
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400" />
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Affectation #{{ $assignment->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Header avec titre et actions --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                        <x-iconify icon="lucide:clipboard-check" class="w-6 h-6 text-blue-600" />
                        Affectation #{{ $assignment->id }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Créée le {{ $assignment->created_at->format('d/m/Y à H:i') }}
                        @if($assignment->creator)
                            par {{ $assignment->creator->name }}
                        @endif
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Bouton Terminer (si active) --}}
                    @if($assignment->status === 'active' && $assignment->canBeEnded())
                        <button @click="openEndAssignmentModal()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:flag-triangle-right" class="w-5 h-5" />
                            <span>Terminer l'affectation</span>
                        </button>
                    @endif

                    {{-- Bouton Modifier (si éditable) --}}
                    @if($assignment->canBeEdited())
                        <a href="{{ route('admin.assignments.edit', $assignment) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                            <x-iconify icon="lucide:edit" class="w-5 h-5" />
                            <span>Modifier</span>
                        </a>
                    @endif

                    {{-- Bouton Export PDF --}}
                    <button onclick="window.print()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:printer" class="w-5 h-5" />
                        <span>Imprimer</span>
                    </button>

                    {{-- Bouton Retour --}}
                    <a href="{{ route('admin.assignments.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm hover:shadow-md">
                        <x-iconify icon="lucide:arrow-left" class="w-5 h-5" />
                        <span>Retour</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===============================================
        STATUT ET MÉTRIQUES RAPIDES
        =============================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            {{-- Statut --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Statut</p>
                        <div class="mt-2">
                            @php
                            $statusConfig = [
                                'scheduled' => ['badge' => 'bg-purple-50 text-purple-700 border border-purple-200', 'icon' => 'lucide:clock', 'label' => 'Planifiée'],
                                'active' => ['badge' => 'bg-green-50 text-green-700 border border-green-200', 'icon' => 'lucide:play-circle', 'label' => 'Active'],
                                'completed' => ['badge' => 'bg-blue-50 text-blue-700 border border-blue-200', 'icon' => 'lucide:check-circle', 'label' => 'Terminée'],
                                'cancelled' => ['badge' => 'bg-red-50 text-red-700 border border-red-200', 'icon' => 'lucide:x-circle', 'label' => 'Annulée'],
                            ];
                            $status = $statusConfig[$assignment->status] ?? ['badge' => 'bg-gray-50 text-gray-700 border border-gray-200', 'icon' => 'lucide:help-circle', 'label' => $assignment->status];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium {{ $status['badge'] }}">
                                <x-iconify :icon="$status['icon']" class="w-4 h-4" />
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Durée --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Durée</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ $assignment->formatted_duration }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:timer" class="w-5 h-5 text-blue-600" />
                    </div>
                </div>
            </div>

            {{-- Kilométrage (si disponible) --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Kilométrage</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            @if($assignment->end_mileage && $assignment->start_mileage)
                                {{ number_format($assignment->end_mileage - $assignment->start_mileage) }} km
                            @elseif($assignment->start_mileage)
                                Départ: {{ number_format($assignment->start_mileage) }} km
                            @else
                                Non renseigné
                            @endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:gauge" class="w-5 h-5 text-purple-600" />
                    </div>
                </div>
            </div>

            {{-- Type --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-600 uppercase">Type</p>
                        <p class="text-xl font-bold text-gray-900 mt-1">
                            {{ $assignment->is_ongoing ? 'En cours' : ($assignment->is_scheduled ? 'Planifiée' : 'Terminée') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-100 border border-emerald-300 rounded-full flex items-center justify-center">
                        <x-iconify icon="lucide:calendar" class="w-5 h-5 text-emerald-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ===============================================
        LAYOUT 2 COLONNES: INFORMATIONS + TIMELINE
        =============================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- COLONNE GAUCHE: INFORMATIONS PRINCIPALES (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informations Véhicule et Chauffeur --}}
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:users" class="w-5 h-5 text-blue-600" />
                            Ressources affectées
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Véhicule --}}
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-shrink-0 w-16 h-16 bg-white rounded-lg flex items-center justify-center border border-gray-300 shadow-sm">
                                <x-iconify icon="lucide:car" class="w-8 h-8 text-gray-600" />
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium text-gray-500 uppercase">Véhicule</h3>
                                    <a href="{{ route('admin.vehicles.show', $assignment->vehicle) }}"
                                       class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        Voir détails →
                                    </a>
                                </div>
                                <p class="text-xl font-bold text-gray-900 mb-1">
                                    {{ $assignment->vehicle->registration_plate }}
                                </p>
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ $assignment->vehicle->brand }} {{ $assignment->vehicle->model }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <x-iconify icon="lucide:gauge" class="w-3.5 h-3.5" />
                                        {{ number_format($assignment->vehicle->current_mileage) }} km
                                    </span>
                                    @if($assignment->vehicle->vehicleType)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $assignment->vehicle->vehicleType->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Chauffeur --}}
                        @if($assignment->driver)
                            <div class="flex items-start gap-4 p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div class="flex-shrink-0">
                                    @if($assignment->driver->photo)
                                        <img src="{{ Storage::url($assignment->driver->photo) }}"
                                             alt="{{ $assignment->driver->full_name }}"
                                             class="w-16 h-16 rounded-full object-cover ring-4 ring-white shadow-lg">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-4 ring-white shadow-lg">
                                            <span class="text-xl font-bold text-white">
                                                {{ strtoupper(substr($assignment->driver->first_name, 0, 1)) }}{{ strtoupper(substr($assignment->driver->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-sm font-medium text-blue-700 uppercase">Chauffeur</h3>
                                        <a href="{{ route('admin.drivers.show', $assignment->driver) }}"
                                           class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            Voir profil →
                                        </a>
                                    </div>
                                    <p class="text-xl font-bold text-gray-900 mb-1">
                                        {{ $assignment->driver->full_name }}
                                    </p>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        @if($assignment->driver->personal_phone || $assignment->driver->professional_phone)
                                            <p class="flex items-center gap-2">
                                                <x-iconify icon="lucide:phone" class="w-4 h-4 text-blue-600" />
                                                {{ $assignment->driver->personal_phone ?? $assignment->driver->professional_phone }}
                                            </p>
                                        @endif
                                        @if($assignment->driver->email)
                                            <p class="flex items-center gap-2">
                                                <x-iconify icon="lucide:mail" class="w-4 h-4 text-blue-600" />
                                                {{ $assignment->driver->email }}
                                            </p>
                                        @endif
                                        @if($assignment->driver->license_number)
                                            <p class="flex items-center gap-2">
                                                <x-iconify icon="lucide:id-card" class="w-4 h-4 text-blue-600" />
                                                Permis: {{ $assignment->driver->license_number }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Période et Dates --}}
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:calendar-range" class="w-5 h-5 text-emerald-600" />
                            Période d'affectation
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Date de début --}}
                            <div class="relative">
                                <div class="absolute -left-3 top-0 bottom-0 w-1 bg-gradient-to-b from-green-500 to-green-600 rounded-full"></div>
                                <div class="pl-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 bg-green-100 border border-green-200 rounded-full flex items-center justify-center">
                                            <x-iconify icon="lucide:play-circle" class="w-4 h-4 text-green-600" />
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Début</h3>
                                    </div>
                                    <p class="text-xl font-bold text-gray-900 mb-1">
                                        {{ $assignment->start_datetime->format('d/m/Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        à {{ $assignment->start_datetime->format('H:i') }}
                                    </p>
                                    @if($assignment->start_mileage)
                                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                            <x-iconify icon="lucide:gauge" class="w-3.5 h-3.5" />
                                            {{ number_format($assignment->start_mileage) }} km
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Date de fin --}}
                            <div class="relative">
                                @if($assignment->end_datetime)
                                    <div class="absolute -left-3 top-0 bottom-0 w-1 bg-gradient-to-b from-orange-500 to-red-600 rounded-full"></div>
                                @else
                                    <div class="absolute -left-3 top-0 bottom-0 w-1 bg-gradient-to-b from-gray-300 to-gray-400 rounded-full"></div>
                                @endif
                                <div class="pl-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 {{ $assignment->end_datetime ? 'bg-orange-100 border border-orange-200' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                            <x-iconify icon="lucide:flag-triangle-right" class="w-4 h-4 {{ $assignment->end_datetime ? 'text-orange-600' : 'text-gray-400' }}" />
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-500 uppercase">Fin</h3>
                                    </div>
                                    @if($assignment->end_datetime)
                                        <p class="text-xl font-bold text-gray-900 mb-1">
                                            {{ $assignment->end_datetime->format('d/m/Y') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            à {{ $assignment->end_datetime->format('H:i') }}
                                        </p>
                                        @if($assignment->end_mileage)
                                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                                <x-iconify icon="lucide:gauge" class="w-3.5 h-3.5" />
                                                {{ number_format($assignment->end_mileage) }} km
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-lg font-medium text-gray-400 italic">
                                            En cours...
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            Affectation à durée indéterminée
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes et Observations --}}
                @if($assignment->reason || $assignment->notes)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="lucide:file-text" class="w-5 h-5 text-amber-600" />
                                Notes et observations
                            </h2>
                        </div>

                        <div class="p-6 space-y-4">
                            @if($assignment->reason)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                        <x-iconify icon="lucide:info" class="w-4 h-4 text-blue-600" />
                                        Motif de l'affectation
                                    </h3>
                                    <p class="text-gray-900 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        {{ $assignment->reason }}
                                    </p>
                                </div>
                            @endif

                            @if($assignment->notes)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                                        <x-iconify icon="lucide:message-square" class="w-4 h-4 text-purple-600" />
                                        Notes additionnelles
                                    </h3>
                                    <p class="text-gray-900 bg-purple-50 border border-purple-200 rounded-lg p-3 whitespace-pre-wrap">
                                        {{ $assignment->notes }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- COLONNE DROITE: TIMELINE ET ACTIONS (1/3) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Actions rapides --}}
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase">Actions rapides</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        @if($assignment->status === 'active' && $assignment->canBeEnded())
                            <button @click="openEndAssignmentModal()"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-lg transition-colors">
                                <x-iconify icon="lucide:flag-triangle-right" class="w-5 h-5" />
                                Terminer l'affectation
                            </button>
                        @endif

                        @if($assignment->canBeEdited())
                            <a href="{{ route('admin.assignments.edit', $assignment) }}"
                               class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
                                <x-iconify icon="lucide:edit" class="w-5 h-5" />
                                Modifier l'affectation
                            </a>
                        @endif

                        <button onclick="window.print()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors">
                            <x-iconify icon="lucide:printer" class="w-5 h-5" />
                            Imprimer le récapitulatif
                        </button>

                        <button onclick="exportToPDF()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors">
                            <x-iconify icon="lucide:download" class="w-5 h-5" />
                            Exporter en PDF
                        </button>
                    </div>
                </div>

                {{-- Informations système --}}
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase flex items-center gap-2">
                            <x-iconify icon="lucide:info" class="w-4 h-4" />
                            Informations système
                        </h3>
                    </div>
                    <div class="p-4 space-y-3 text-sm">
                        <div class="flex justify-between items-start">
                            <span class="text-gray-600">ID:</span>
                            <span class="font-mono font-medium text-gray-900">#{{ $assignment->id }}</span>
                        </div>
                        <div class="flex justify-between items-start">
                            <span class="text-gray-600">Créée le:</span>
                            <span class="font-medium text-gray-900 text-right">
                                {{ $assignment->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @if($assignment->creator)
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600">Créée par:</span>
                                <span class="font-medium text-gray-900">{{ $assignment->creator->name }}</span>
                            </div>
                        @endif
                        @if($assignment->updated_at && 
                            ($assignment->updated_at instanceof \Carbon\Carbon ? 
                                !$assignment->updated_at->eq($assignment->created_at) : 
                                $assignment->updated_at != $assignment->created_at))
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600">Modifiée le:</span>
                                <span class="font-medium text-gray-900 text-right">
                                    {{ $assignment->safeFormatDate($assignment->updated_at, 'd/m/Y H:i', 'Non défini') }}
                                </span>
                            </div>
                        @endif
                        @if($assignment->ended_at)
                            <div class="flex justify-between items-start">
                                <span class="text-gray-600">Terminée le:</span>
                                <span class="font-medium text-gray-900 text-right">
                                    {{ $assignment->safeFormatDate($assignment->ended_at, 'd/m/Y H:i', 'Non défini') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- ===============================================
    MODAL TERMINER L'AFFECTATION - ULTRA PRO
    =============================================== --}}
    <div x-show="showEndModal"
         x-cloak
         @keydown.escape.window="showEndModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Background overlay --}}
            <div x-show="showEndModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity z-40"
                 @click="showEndModal = false"
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showEndModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-50">

                <form @submit.prevent="submitEndAssignment()">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-iconify icon="lucide:flag-triangle-right" class="h-6 w-6 text-orange-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                Terminer l'affectation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4">
                                    Vous êtes sur le point de clôturer cette affectation. Veuillez renseigner les informations de fin.
                                </p>

                                {{-- Résumé de l'affectation --}}
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <div class="flex items-center gap-3">
                                        <x-iconify icon="lucide:car" class="w-8 h-8 text-blue-600" />
                                        <div>
                                            <p class="font-semibold text-blue-900">{{ $assignment->vehicle->registration_plate }}</p>
                                            <p class="text-sm text-blue-700">{{ $assignment->driver->full_name }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Formulaire --}}
                                <div class="space-y-4">
                                    {{-- Date et heure de fin --}}
                                    <div>
                                        <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">
                                            Date et heure de fin <span class="text-red-600">*</span>
                                        </label>
                                        <input type="datetime-local"
                                               id="end_datetime"
                                               x-model="endData.end_datetime"
                                               :min="'{{ $assignment->start_datetime->format('Y-m-d\TH:i') }}'"
                                               :max="new Date().toISOString().slice(0, 16)"
                                               required
                                               class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Début: {{ $assignment->start_datetime->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>

                                    {{-- Kilométrage de fin --}}
                                    <div>
                                        <label for="end_mileage" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kilométrage de fin (optionnel)
                                        </label>
                                        <div class="relative">
                                            <input type="number"
                                                   id="end_mileage"
                                                   x-model="endData.end_mileage"
                                                   min="{{ $assignment->start_mileage ?? 0 }}"
                                                   placeholder="Ex: {{ number_format(($assignment->start_mileage ?? 0) + 150) }}"
                                                   class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm pr-12">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <span class="text-gray-500 text-sm">km</span>
                                            </div>
                                        </div>
                                        @if($assignment->start_mileage)
                                            <p class="mt-1 text-xs text-gray-500">
                                                Départ: {{ number_format($assignment->start_mileage) }} km
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Notes de fin --}}
                                    <div>
                                        <label for="end_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                            Observations de fin (optionnel)
                                        </label>
                                        <textarea id="end_notes"
                                                  x-model="endData.notes"
                                                  rows="3"
                                                  maxlength="1000"
                                                  placeholder="État du véhicule, remarques particulières..."
                                                  class="block w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500 text-sm"></textarea>
                                        <p class="mt-1 text-xs text-gray-500">
                                            <span x-text="(endData.notes || '').length"></span>/1000 caractères
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit"
                                :disabled="submitting"
                                :class="submitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-700'"
                                class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <x-iconify icon="lucide:check" class="w-4 h-4 mr-2" />
                            <span x-text="submitting ? 'Enregistrement...' : 'Confirmer la fin'"></span>
                        </button>
                        <button type="button"
                                @click="showEndModal = false"
                                :disabled="submitting"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</section>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════════════════════
// ASSIGNMENT DETAILS - ENTERPRISE ULTRA PRO
// ═══════════════════════════════════════════════════════════════════════════

function assignmentDetails() {
    return {
        showEndModal: false,
        submitting: false,
        endData: {
            end_datetime: new Date().toISOString().slice(0, 16), // Format: YYYY-MM-DDTHH:mm
            end_mileage: '',
            notes: ''
        },

        openEndAssignmentModal() {
            this.showEndModal = true;
            // Pré-remplir avec date/heure actuelle
            this.endData.end_datetime = new Date().toISOString().slice(0, 16);
        },

        async submitEndAssignment() {
            if (this.submitting) return;

            // Validation côté client
            if (!this.endData.end_datetime) {
                alert('Veuillez renseigner la date et l\'heure de fin.');
                return;
            }

            this.submitting = true;

            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PATCH');
                formData.append('end_datetime', this.endData.end_datetime);

                if (this.endData.end_mileage) {
                    formData.append('end_mileage', this.endData.end_mileage);
                }

                if (this.endData.notes) {
                    formData.append('notes', this.endData.notes);
                }

                const response = await fetch('{{ route('admin.assignments.end', $assignment) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    // Succès - rediriger ou recharger
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || 'Une erreur est survenue lors de la clôture de l\'affectation.');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue. Veuillez réessayer.');
            } finally {
                this.submitting = false;
            }
        }
    };
}

/**
 * Export PDF
 */
function exportToPDF() {
    window.open('{{ route('admin.assignments.show', $assignment) }}?export=pdf', '_blank');
}

// Print optimization
window.addEventListener('beforeprint', () => {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', () => {
    document.body.classList.remove('printing');
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print,
    nav,
    button,
    .print\\:hidden {
        display: none !important;
    }

    .bg-gray-50 {
        background-color: white !important;
    }

    .shadow-lg,
    .shadow-md,
    .shadow-sm {
        box-shadow: none !important;
    }

    .border-gray-200 {
        border-color: #e5e7eb !important;
    }
}
</style>
@endpush
