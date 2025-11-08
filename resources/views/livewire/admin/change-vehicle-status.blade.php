<div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
    {{-- Header --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Changer le statut du véhicule
        </h3>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Véhicule: <span class="font-medium">{{ $vehicle->registration_plate }}</span>
            <span class="ml-2">{!! $vehicle->statusBadge() !!}</span>
        </p>
    </div>

    {{-- Messages de feedback --}}
    @if($successMessage)
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">✅ Succès!</span> {{ $successMessage }}
        </div>
    @endif

    @if($errorMessage)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <span class="font-medium">❌ Erreur!</span> {{ $errorMessage }}
        </div>
    @endif

    {{-- Formulaire --}}
    <form wire:submit.prevent="changeStatus">
        {{-- Sélection du nouveau statut --}}
        <div class="mb-4">
            <label for="selectedStatus" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Nouveau statut <span class="text-red-500">*</span>
            </label>
            <select
                wire:model.live="selectedStatus"
                id="selectedStatus"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required
            >
                <option value="">-- Sélectionner un statut --</option>
                @foreach($availableStatuses as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('selectedStatus')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Champ raison (conditionnel) --}}
        @if($showReasonField)
            <div class="mb-4">
                <label for="reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Raison du changement <span class="text-red-500">*</span>
                </label>
                <textarea
                    wire:model="reason"
                    id="reason"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Expliquez la raison du changement de statut..."
                    required
                ></textarea>
                @error('reason')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{-- Métadonnées pour statut REFORMÉ --}}
        @if($showMetadataFields)
            <div class="p-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">
                    Informations de réforme
                </h4>

                <div class="mb-3">
                    <label for="reform_reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Raison de la réforme
                    </label>
                    <select
                        wire:model="metadata.reform_reason"
                        id="reform_reason"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    >
                        <option value="">-- Sélectionner --</option>
                        <option value="age">Âge du véhicule</option>
                        <option value="mileage">Kilométrage trop élevé</option>
                        <option value="unrepairable">Non réparable</option>
                        <option value="accident">Accident total</option>
                        <option value="other">Autre</option>
                    </select>
                </div>

                <div>
                    <label for="reform_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Date de réforme
                    </label>
                    <input
                        type="date"
                        wire:model="metadata.reform_date"
                        id="reform_date"
                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    />
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex justify-end space-x-3">
            <button
                type="button"
                wire:click="cancel"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
            >
                Annuler
            </button>
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                wire:loading.attr="disabled"
                wire:target="changeStatus"
            >
                <span wire:loading.remove wire:target="changeStatus">Changer le statut</span>
                <span wire:loading wire:target="changeStatus">
                    <svg class="inline w-4 h-4 mr-2 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Traitement...
                </span>
            </button>
        </div>
    </form>

    {{-- Historique récent --}}
    @if($vehicle->recentStatusHistory && $vehicle->recentStatusHistory->count() > 0)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">
                📊 Historique récent des changements
            </h4>
            <div class="space-y-2">
                @foreach($vehicle->recentStatusHistory->take(5) as $history)
                    <div class="flex items-center justify-between p-3 text-sm bg-gray-50 rounded-lg dark:bg-gray-700">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 dark:text-white">
                                    @if($history->from_status)
                                        <span class="font-medium">{{ ucfirst($history->from_status) }}</span>
                                        <svg class="inline w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                    <span class="font-medium">{{ ucfirst($history->to_status) }}</span>
                                </p>
                                @if($history->reason)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $history->reason }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $history->changed_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
