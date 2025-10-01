@extends('layouts.admin')

@section('title', 'Nouvelle Demande de Réparation')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Demande de Réparation</h1>
            <p class="mt-2 text-sm text-gray-700">Créez une nouvelle demande de réparation pour un véhicule</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.repair-requests.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <x-lucide-arrow-left class="h-4 w-4 mr-2" />
                Retour à la liste
            </a>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.repair-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de la demande</h3>
                <p class="mt-1 text-sm text-gray-500">Veuillez remplir tous les champs obligatoires</p>
            </div>

            <div class="px-6 py-4 space-y-6">
                {{-- Véhicule --}}
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Véhicule *</label>
                    <select name="vehicle_id" id="vehicle_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('vehicle_id') border-red-300 @enderror">
                        <option value="">Sélectionner un véhicule</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Catégorie --}}
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Catégorie *</label>
                        <select name="category" id="category" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('category') border-red-300 @enderror">
                            <option value="">Sélectionner une catégorie</option>
                            <option value="engine" {{ old('category') === 'engine' ? 'selected' : '' }}>Moteur</option>
                            <option value="brake" {{ old('category') === 'brake' ? 'selected' : '' }}>Freinage</option>
                            <option value="transmission" {{ old('category') === 'transmission' ? 'selected' : '' }}>Transmission</option>
                            <option value="electrical" {{ old('category') === 'electrical' ? 'selected' : '' }}>Électrique</option>
                            <option value="cooling" {{ old('category') === 'cooling' ? 'selected' : '' }}>Refroidissement</option>
                            <option value="suspension" {{ old('category') === 'suspension' ? 'selected' : '' }}>Suspension</option>
                            <option value="bodywork" {{ old('category') === 'bodywork' ? 'selected' : '' }}>Carrosserie</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Priorité --}}
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priorité *</label>
                        <select name="priority" id="priority" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('priority') border-red-300 @enderror">
                            <option value="">Sélectionner une priorité</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('priority')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description du problème *</label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Décrivez le problème en détail..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Coût estimé --}}
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700">Coût estimé (DA)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="number" name="estimated_cost" id="estimated_cost"
                               step="0.01" min="0" value="{{ old('estimated_cost') }}"
                               placeholder="0.00"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('estimated_cost') border-red-300 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">DA</span>
                        </div>
                    </div>
                    @error('estimated_cost')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pièces jointes --}}
                <div>
                    <label for="attachments" class="block text-sm font-medium text-gray-700">Pièces jointes</label>
                    <div class="mt-1">
                        <input type="file" name="attachments[]" id="attachments" multiple
                               accept="image/*,.pdf,.doc,.docx"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Ajoutez des photos, rapports ou documents relatifs au problème (max 10MB par fichier)</p>
                    @error('attachments.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Commentaires additionnels --}}
                <div>
                    <label for="additional_notes" class="block text-sm font-medium text-gray-700">Commentaires additionnels</label>
                    <textarea name="additional_notes" id="additional_notes" rows="3"
                              placeholder="Informations supplémentaires, historique des pannes similaires..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('additional_notes') }}</textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="window.history.back()"
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <x-lucide-save class="h-4 w-4 mr-2" />
                    Créer la demande
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation côté client
    const form = document.querySelector('form');
    const vehicleSelect = document.getElementById('vehicle_id');
    const categorySelect = document.getElementById('category');
    const prioritySelect = document.getElementById('priority');
    const descriptionTextarea = document.getElementById('description');

    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Vérification des champs requis
        if (!vehicleSelect.value) {
            isValid = false;
            vehicleSelect.classList.add('border-red-300');
        } else {
            vehicleSelect.classList.remove('border-red-300');
        }

        if (!categorySelect.value) {
            isValid = false;
            categorySelect.classList.add('border-red-300');
        } else {
            categorySelect.classList.remove('border-red-300');
        }

        if (!prioritySelect.value) {
            isValid = false;
            prioritySelect.classList.add('border-red-300');
        } else {
            prioritySelect.classList.remove('border-red-300');
        }

        if (!descriptionTextarea.value.trim()) {
            isValid = false;
            descriptionTextarea.classList.add('border-red-300');
        } else {
            descriptionTextarea.classList.remove('border-red-300');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires');
        }
    });

    // Prévisualisation des fichiers
    const fileInput = document.getElementById('attachments');
    fileInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            let fileNames = Array.from(files).map(file => file.name).join(', ');
            console.log('Fichiers sélectionnés:', fileNames);
        }
    });
});
</script>
@endpush
@endsection