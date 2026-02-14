@extends('layouts.admin.catalyst')

@section('title', 'Modifier Opération Maintenance')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-4xl lg:py-6">

        {{-- HEADER --}}
        <div class="mb-6">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.maintenance.operations.index') }}" class="text-gray-600 hover:text-blue-600">
                            <x-iconify icon="lucide:wrench" class="w-4 h-4 mr-2 inline" />
                            Maintenance
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <span class="text-gray-900 font-medium">Modifier Opération #{{ $operation->id }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-xl font-bold text-gray-600">
                <x-iconify icon="lucide:pencil" class="w-6 h-6 text-blue-600" />
                Modifier Opération de Maintenance
            </h1>
            <p class="mt-1 text-xs text-gray-600">
                Mettez à jour les informations de l'opération
            </p>
        </div>

        {{-- Erreurs --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h3 class="text-sm font-semibold text-red-900 mb-2">Erreurs de validation</h3>
                        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- FORMULAIRE --}}
        <form action="{{ route('admin.maintenance.operations.update', $operation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Card Principale --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <x-iconify icon="lucide:settings" class="w-5 h-5 text-blue-600" />
                    Informations de l'Opération
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Véhicule --}}
                    <div class="md:col-span-2">
                        <label for="vehicle_id" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:car" class="w-4 h-4 inline mr-1" />
                            Véhicule <span class="text-red-500">*</span>
                        </label>
                        <select name="vehicle_id" id="vehicle_id" required
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('vehicle_id') border-red-300 @enderror">
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ (old('vehicle_id', $operation->vehicle_id) == $vehicle->id) ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type de Maintenance --}}
                    <div class="md:col-span-2">
                        <label for="maintenance_type_id" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:tag" class="w-4 h-4 inline mr-1" />
                            Type de Maintenance <span class="text-red-500">*</span>
                        </label>
                        <select name="maintenance_type_id" id="maintenance_type_id" required
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('maintenance_type_id') border-red-300 @enderror">
                            <option value="">Sélectionner un type</option>
                            @foreach($maintenanceTypes as $type)
                                <option value="{{ $type->id }}" {{ (old('maintenance_type_id', $operation->maintenance_type_id) == $type->id) ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ ucfirst($type->category) }})
                                </option>
                            @endforeach
                        </select>
                        @error('maintenance_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date Planifiée --}}
                    <div>
                        <label for="scheduled_date" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 inline mr-1" />
                            Date Planifiée <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="scheduled_date" id="scheduled_date" required
                               value="{{ old('scheduled_date', $operation->scheduled_date?->format('Y-m-d')) }}"
                               class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('scheduled_date') border-red-300 @enderror">
                        @error('scheduled_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div>
                        <label for="status" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:info" class="w-4 h-4 inline mr-1" />
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="planned" {{ old('status', $operation->status) == 'planned' ? 'selected' : '' }}>Planifiée</option>
                            <option value="in_progress" {{ old('status', $operation->status) == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ old('status', $operation->status) == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled" {{ old('status', $operation->status) == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    {{-- Fournisseur --}}
                    <div class="md:col-span-2">
                        <label for="provider_id" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:building" class="w-4 h-4 inline mr-1" />
                            Fournisseur (optionnel)
                        </label>
                        <select name="provider_id" id="provider_id"
                                class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Aucun fournisseur</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" {{ (old('provider_id', $operation->provider_id) == $provider->id) ? 'selected' : '' }}>
                                    {{ $provider->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kilométrage --}}
                    <div>
                        <label for="mileage_at_maintenance" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:gauge" class="w-4 h-4 inline mr-1" />
                            Kilométrage Prévu (km)
                        </label>
                        <input type="number" name="mileage_at_maintenance" id="mileage_at_maintenance" 
                               value="{{ old('mileage_at_maintenance', $operation->mileage_at_maintenance) }}"
                               min="0" step="1"
                               placeholder="Ex: 50000"
                               class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('mileage_at_maintenance') border-red-300 @enderror">
                        @error('mileage_at_maintenance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Coût Estimé --}}
                    <div>
                        <label for="total_cost" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:banknote" class="w-4 h-4 inline mr-1" />
                            Coût Estimé (DA)
                        </label>
                        <input type="number" name="total_cost" id="total_cost" 
                               value="{{ old('total_cost', $operation->total_cost) }}"
                               min="0" step="0.01"
                               placeholder="Ex: 15000.00"
                               class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('total_cost') border-red-300 @enderror">
                        @error('total_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Durée Estimée --}}
                    <div>
                        <label for="duration_minutes" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:clock" class="w-4 h-4 inline mr-1" />
                            Durée Estimée (minutes)
                        </label>
                        <input type="number" name="duration_minutes" id="duration_minutes" 
                               value="{{ old('duration_minutes', $operation->duration_minutes) }}"
                               min="1" max="1440"
                               placeholder="Ex: 120"
                               class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('duration_minutes') border-red-300 @enderror">
                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-600">
                            <x-iconify icon="lucide:file-text" class="w-4 h-4 inline mr-1" />
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  placeholder="Décrivez les travaux à effectuer..."
                                  class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror">{{ old('description', $operation->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Maximum 1000 caractères</p>
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <x-iconify icon="lucide:message-square" class="w-4 h-4 inline mr-1" />
                            Notes Internes
                        </label>
                        <textarea name="notes" id="notes" rows="2"
                                  placeholder="Notes additionnelles (visibles uniquement en interne)..."
                                  class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror">{{ old('notes', $operation->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.maintenance.operations.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                        Annuler
                    </a>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                            <x-iconify icon="lucide:check" class="w-4 h-4" />
                            Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Aide Contextuelle --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">Conseils</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Les champs marqués d'un * sont obligatoires</li>
                        <li>Le kilométrage prévu aide à suivre l'historique d'entretien du véhicule</li>
                        <li>Le coût estimé permet de planifier le budget de maintenance</li>
                        <li>Une description claire facilite le suivi des travaux effectués</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Script pour auto-remplissage des champs estimés --}}
@push('scripts')
<script>
document.getElementById('maintenance_type_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    // Vous pouvez ajouter des données de coût/durée estimés dans l'option et les auto-remplir ici
    // Pour l'instant, c'est juste un placeholder pour la fonctionnalité future
});
</script>
@endpush
@endsection
