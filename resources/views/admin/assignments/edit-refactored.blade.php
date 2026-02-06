{{-- resources/views/admin/assignments/edit-refactored.blade.php --}}
@extends('layouts.admin.catalyst')

@section('title', 'Modifier l\'Affectation')

@section('content')
{{-- ====================================================================
✏️ MODIFIER AFFECTATION - ULTRA-PROFESSIONAL ENTERPRISE GRADE
====================================================================

DESIGN PRINCIPLES:
✨ Fond gris clair (bg-gray-50) pour la page
✨ Header avec icône + titre
✨ Info cards: Résumé de l'affectation existante
✨ Formulaire simple pour modifications
✨ Validation en temps réel
✨ Cohérence totale avec pages Véhicules

@version 1.0-Ultra-Pro-Enterprise-Standard
@since 2025-10-20
==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
<div x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    class="fixed top-4 right-4 z-50 max-w-md">
    <x-alert type="success" title="Succès" dismissible>
        {{ session('success') }}
    </x-alert>
</div>
@endif

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- ====================================================================
 HEADER - ULTRA-PRO DESIGN
 ===================================================================== --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="lucide:pencil" class="w-6 h-6 text-blue-600" />
                Modifier l'Affectation
                <span class="ml-2 text-sm font-normal text-gray-500">
                    #{{ $assignment->id }}
                </span>
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Modifiez les détails de l'affectation
            </p>
        </div>

        {{-- ====================================================================
 RÉSUMÉ AFFECTATION - INFO CARDS
 ===================================================================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- Chauffeur --}}
            <x-card padding="p-6" margin="mb-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 border border-blue-300 rounded-full flex items-center justify-center flex-shrink-0">
                        <x-iconify icon="lucide:user" class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Chauffeur</p>
                        <p class="text-lg font-semibold text-gray-900 mt-0.5">
                            {{ $assignment->driver?->name ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $assignment->driver?->phone ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- Véhicule --}}
            <x-card padding="p-6" margin="mb-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 border border-green-300 rounded-full flex items-center justify-center flex-shrink-0">
                        <x-iconify icon="lucide:car" class="w-6 h-6 text-green-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Véhicule</p>
                        <p class="text-lg font-semibold text-gray-900 mt-0.5">
                            {{ $assignment->vehicle?->registration_plate ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $assignment->vehicle?->brand ?? '' }} {{ $assignment->vehicle?->model ?? '' }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- Dates --}}
            <x-card padding="p-6" margin="mb-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 border border-orange-300 rounded-full flex items-center justify-center flex-shrink-0">
                        <x-iconify icon="lucide:calendar-check" class="w-6 h-6 text-orange-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Dates</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">
                            Du {{ $assignment->start_date?->format('d/m/Y') ?? 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            au {{ $assignment->end_date?->format('d/m/Y') ?? 'En cours' }}
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- Statut --}}
            <x-card padding="p-6" margin="mb-0">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 border border-purple-300 rounded-full flex items-center justify-center flex-shrink-0">
                        <x-iconify icon="lucide:info" class="w-6 h-6 text-purple-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-600">Statut</p>
                        @php
                        $statusConfig = [
                        'scheduled' => ['badge' => 'bg-purple-50 text-purple-700 border border-purple-200', 'label' => 'Planifiée'],
                        'active' => ['badge' => 'bg-green-50 text-green-700 border border-green-200', 'label' => 'Active'],
                        'completed' => ['badge' => 'bg-blue-50 text-blue-700 border border-blue-200', 'label' => 'Complétée'],
                        ];
                        $status = $statusConfig[$assignment->status] ?? ['badge' => 'bg-gray-50 text-gray-700 border border-gray-200', 'label' => $assignment->status];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['badge'] }} mt-2">
                            {{ $status['label'] }}
                        </span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- ====================================================================
 FORMULAIRE MODIFICATION - ULTRA-PRO DESIGN
 ===================================================================== --}}
        <x-card padding="p-6" margin="mb-6">
            <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Raison de l'Affectation --}}
                <x-input
                    name="reason"
                    label="Raison de l'Affectation"
                    icon="message-square"
                    placeholder="Ex: Trajet commercial, Service..."
                    :value="old('reason', $assignment->reason)"
                    :error="$errors->first('reason')"
                    helpText="Motif de cette affectation" />

                {{-- Statut --}}
                <div>
                    <x-slim-select
                        name="status"
                        label="Statut"
                        :options="[
 'scheduled' => 'Planifiée',
 'active' => 'Active',
 'completed' => 'Complétée'
 ]"
                        :selected="old('status', $assignment->status)"
                        placeholder="Sélectionnez un statut..."
                        required
                        :error="$errors->first('status')" />
                </div>

                {{-- Dates Modifiables --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-datepicker
                        name="start_date"
                        label="Date de Début"
                        format="d/m/Y"
                        :minDate="date('Y-m-d', strtotime('-30 days'))"
                        :value="old('start_date', $assignment->start_date?->format('d/m/Y'))"
                        placeholder="JJ/MM/AAAA"
                        :error="$errors->first('start_date')" />

                    <x-datepicker
                        name="end_date"
                        label="Date de Fin"
                        format="d/m/Y"
                        :value="old('end_date', $assignment->end_date?->format('d/m/Y'))"
                        placeholder="JJ/MM/AAAA (Optionnel)"
                        :error="$errors->first('end_date')" />
                </div>

                {{-- Notes --}}
                <x-textarea
                    name="notes"
                    label="Notes Complémentaires"
                    rows="4"
                    placeholder="Informations supplémentaires..."
                    :value="old('notes', $assignment->notes)"
                    :error="$errors->first('notes')"
                    helpText="Notes internes sur l'affectation" />

                {{-- ===================================
 ACTIONS FOOTER
 =================================== --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                    <div>
                        <a href="{{ route('admin.assignments.index') }}"
                            class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                            Retour à la liste
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.assignments.show', $assignment) }}"
                            class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                            Annuler
                        </a>

                        <x-button
                            type="submit"
                            variant="success"
                            icon="check-circle">
                            Enregistrer les Modifications
                        </x-button>
                    </div>
                </div>
            </form>
        </x-card>

        {{-- ====================================================================
 ZONE DANGER - ACTIONS SUPPRESSION
 ===================================================================== --}}
        <x-card padding="p-6" margin="mb-0" title="Zone Danger" icon="alert-triangle">
            <p class="text-sm text-gray-600 mb-4">
                Supprimer cette affectation ne peut pas être annulé. Assurez-vous que vous souhaiter vraiment la supprimer.
            </p>

            <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" class="inline"
                onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cette affectation ?');">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700
 text-white font-medium rounded-lg transition-colors duration-200
 text-sm shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:trash-2" class="w-5 h-5" />
                    <span>Supprimer l'Affectation</span>
                </button>
            </form>
        </x-card>

    </div>
</section>

@endsection