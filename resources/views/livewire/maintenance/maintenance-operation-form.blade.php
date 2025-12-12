<div class="space-y-6" x-data="{
    isValidating: @entangle('isValidating')
}">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ $isEdit ? 'Modifier l\'opération' : 'Nouvelle opération de maintenance' }}
            </h3>
            <p class="mt-1 text-sm text-gray-600">
                {{ $isEdit ? 'Modifiez les détails de l\'opération de maintenance' : 'Enregistrez une nouvelle opération de maintenance pour un véhicule' }}
            </p>
        </div>

        {{-- Indicateur de validation --}}
        <div x-show="isValidating" class="flex items-center space-x-2 text-blue-600">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm">Traitement...</span>
        </div>
    </div>

    {{-- Formulaire --}}
    <form wire:submit="save" class="space-y-6">

        {{-- Section 1: Informations Principales --}}
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    {{-- Véhicule --}}
                    <div class="sm:col-span-3">
                        <label for="vehicle_id" class="block text-sm font-medium leading-6 text-gray-900">
                            Véhicule <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2" wire:ignore>
                            {{-- <x-zenfleet-select 
                                wire:model.live="vehicle_id" 
                                id="vehicle_id" 
                                placeholder="Sélectionner un véhicule"
                                :options="$vehicles"
                                option-label="label"
                                option-value="id"
                                option-description="details"
                            /> --}}
                        </div>
                        @error('vehicle_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type de Maintenance --}}
                    <div class="sm:col-span-3">
                        <label for="maintenance_type_id" class="block text-sm font-medium leading-6 text-gray-900">
                            Type de Maintenance <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2" wire:ignore>
                            {{-- <x-zenfleet-select
                                wire:model.live="maintenance_type_id"
                                id="maintenance_type_id"
                                placeholder="Sélectionner un type"
                                :options="$maintenanceTypes"
                                option-label="label"
                                option-value="id"
                                option-description="category" /> --}}
                        </div>
                        @error('maintenance_type_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fournisseur --}}
                    <div class="sm:col-span-3">
                        <label for="provider_id" class="block text-sm font-medium leading-6 text-gray-900">
                            Fournisseur <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2" wire:ignore>
                            {{-- <x-zenfleet-select
                                wire:model.live="provider_id"
                                id="provider_id"
                                placeholder="Sélectionner un fournisseur"
                                :options="$providers"
                                option-label="label"
                                option-value="id"
                                option-description="details" /> --}}
                        </div>
                        @error('provider_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm font-medium leading-6 text-gray-900">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <select wire:model.live="status" id="status" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                <option value="planned">Planifiée</option>
                                <option value="in_progress">En cours</option>
                                <option value="completed">Terminée</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                        </div>
                        @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Planification et Coûts --}}
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    {{-- Date --}}
                    <div class="sm:col-span-2">
                        <label for="scheduled_date" class="block text-sm font-medium leading-6 text-gray-900">
                            Date prévue <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-2">
                            <input type="date" wire:model.live="scheduled_date" id="scheduled_date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('scheduled_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Heure début --}}
                    <div class="sm:col-span-2">
                        <label for="start_time" class="block text-sm font-medium leading-6 text-gray-900">
                            Heure début
                        </label>
                        <div class="mt-2">
                            <input type="time" wire:model.live="start_time" id="start_time" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('start_time')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Coût Total --}}
                    <div class="sm:col-span-2">
                        <label for="total_cost" class="block text-sm font-medium leading-6 text-gray-900">
                            Coût Total (HT)
                        </label>
                        <div class="relative mt-2 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">€</span>
                            </div>
                            <input type="number" wire:model.live="total_cost" id="total_cost" step="0.01" min="0" class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" placeholder="0.00">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">EUR</span>
                            </div>
                        </div>
                        @error('total_cost')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-span-full">
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                            Description / Notes
                        </label>
                        <div class="mt-2">
                            <textarea wire:model.live="description" id="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"></textarea>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Détails supplémentaires sur l'intervention.</p>
                        @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-x-6">
            <button type="button" wire:click="cancel" class="text-sm font-semibold leading-6 text-gray-900">Annuler</button>
            <button type="submit"
                class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'opération' }}</span>
                <span wire:loading>Enregistrement...</span>
            </button>
        </div>
    </form>
</div>