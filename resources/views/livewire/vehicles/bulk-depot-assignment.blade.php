<div>
    {{-- Modal Affectation par Lot --}}
    <x-modal name="bulk-depot-assignment" title="Affectation par Lot aux Dépôts" maxWidth="3xl">
        <div class="p-6">
            {{-- Messages Flash --}}
            @if (session()->has('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">
                        <x-iconify icon="lucide:check-circle" class="w-5 h-5 text-green-600 mr-3" />
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">
                        <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3" />
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endif

            @if (session()->has('warning'))
                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">
                        <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-yellow-600 mr-3" />
                        <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                        <button @click="show = false" class="ml-auto text-yellow-600 hover:text-yellow-800">
                            <x-iconify icon="lucide:x" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if (!empty($validationErrors))
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <x-iconify icon="lucide:alert-circle" class="w-5 h-5 text-red-600 mr-3 mt-0.5" />
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-red-800 mb-2">Erreurs de validation</h4>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($validationErrors as $error)
                                    <li class="text-sm text-red-700">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Assignment Result --}}
            @if ($assignmentResult)
                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600 mr-3 mt-0.5" />
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-blue-800 mb-2">Résultat de l'affectation</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="text-gray-700">
                                    <span class="font-medium">Total demandé:</span> {{ $assignmentResult['total_requested'] }}
                                </div>
                                <div class="text-green-700">
                                    <span class="font-medium">Affectés:</span> {{ $assignmentResult['assigned'] }}
                                </div>
                                <div class="text-yellow-700">
                                    <span class="font-medium">Ignorés:</span> {{ $assignmentResult['skipped'] }}
                                </div>
                                <div class="text-red-700">
                                    <span class="font-medium">Échoués:</span> {{ $assignmentResult['failed'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Véhicules Sélectionnés --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                    <x-iconify icon="lucide:truck" class="w-5 h-5 mr-2 text-blue-600" />
                    Véhicules Sélectionnés ({{ count($selectedVehiclesData) }})
                </h3>

                <div class="bg-gray-50 rounded-lg border border-gray-200 max-h-48 overflow-y-auto">
                    @forelse ($selectedVehiclesData as $vehicle)
                        <div class="flex items-center justify-between p-3 border-b border-gray-200 last:border-b-0 hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <x-iconify icon="lucide:truck" class="w-5 h-5 text-blue-600" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $vehicle['registration_plate'] }}</p>
                                    <p class="text-xs text-gray-600">{{ $vehicle['make_name'] }} {{ $vehicle['model_name'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Dépôt actuel:</p>
                                <p class="text-sm font-medium {{ $vehicle['current_depot_id'] ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ $vehicle['current_depot_name'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            <x-iconify icon="lucide:info" class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                            <p class="text-sm">Aucun véhicule sélectionné</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Formulaire d'Affectation --}}
            <form wire:submit.prevent="assignVehicles">
                {{-- Sélection du Dépôt de Destination --}}
                <div class="mb-6">
                    <label for="targetDepotId" class="block text-sm font-medium text-gray-900 mb-2">
                        <x-iconify icon="lucide:building-2" class="w-4 h-4 inline mr-1 text-blue-600" />
                        Dépôt de Destination *
                    </label>

                    <select
                        id="targetDepotId"
                        wire:model.live="targetDepotId"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        {{ $isProcessing ? 'disabled' : '' }}
                    >
                        <option value="">-- Sélectionnez un dépôt --</option>
                        @foreach ($availableDepots as $depot)
                            <option value="{{ $depot['id'] }}" {{ !$depot['has_space'] ? 'disabled' : '' }}>
                                {{ $depot['name'] }}
                                @if ($depot['code'])
                                    ({{ $depot['code'] }})
                                @endif
                                - {{ $depot['city'] ?? 'N/A' }}
                                - {{ $depot['current_count'] }}/{{ $depot['capacity'] }} véhicules
                                ({{ number_format($depot['occupancy_percentage'], 1) }}%)
                                @if (!$depot['has_space'])
                                    - COMPLET
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('targetDepotId')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Aperçu de la Capacité --}}
                @if ($capacityPreview)
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-4">
                        <h4 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                            <x-iconify icon="lucide:bar-chart-2" class="w-4 h-4 mr-2" />
                            Aperçu de la Capacité - {{ $capacityPreview['depot_name'] }}
                        </h4>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Avant Affectation --}}
                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                <p class="text-xs font-medium text-gray-600 mb-2">Avant affectation</p>
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-gray-900">{{ $capacityPreview['current_count'] }}</p>
                                        <p class="text-xs text-gray-500">/ {{ $capacityPreview['capacity'] }} véhicules</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-blue-600">{{ number_format($capacityPreview['occupancy_before'], 1) }}%</p>
                                        <p class="text-xs text-gray-500">occupation</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Après Affectation --}}
                            <div class="bg-white rounded-lg p-3 border {{ $capacityPreview['sufficient_capacity'] ? 'border-green-200' : 'border-red-200' }}">
                                <p class="text-xs font-medium text-gray-600 mb-2">Après affectation</p>
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-2xl font-bold {{ $capacityPreview['sufficient_capacity'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $capacityPreview['current_count'] + $capacityPreview['vehicles_to_assign'] }}
                                        </p>
                                        <p class="text-xs text-gray-500">/ {{ $capacityPreview['capacity'] }} véhicules</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $capacityPreview['sufficient_capacity'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($capacityPreview['occupancy_after'], 1) }}%
                                        </p>
                                        <p class="text-xs text-gray-500">occupation</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Véhicules à Affecter --}}
                        <div class="mt-3 flex items-center justify-between bg-white rounded-lg p-2 border border-blue-100">
                            <span class="text-sm text-gray-700">
                                <x-iconify icon="lucide:arrow-right-circle" class="w-4 h-4 inline mr-1 text-blue-600" />
                                Véhicules à affecter:
                            </span>
                            <span class="text-sm font-bold text-blue-600">{{ $capacityPreview['vehicles_to_assign'] }}</span>
                        </div>

                        {{-- Warning si capacité insuffisante --}}
                        @if (!$capacityPreview['sufficient_capacity'])
                            <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3 flex items-start">
                                <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 text-red-600 mr-2 mt-0.5" />
                                <div class="text-sm text-red-800">
                                    <p class="font-semibold">Capacité insuffisante !</p>
                                    <p class="text-xs mt-1">
                                        Le dépôt n'a que {{ $capacityPreview['available_before'] }} place(s) disponible(s),
                                        mais vous tentez d'affecter {{ $capacityPreview['vehicles_to_assign'] }} véhicule(s).
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Notes d'Affectation --}}
                <div class="mb-6">
                    <label for="assignmentNotes" class="block text-sm font-medium text-gray-900 mb-2">
                        <x-iconify icon="lucide:message-square-text" class="w-4 h-4 inline mr-1 text-gray-600" />
                        Notes (optionnel)
                    </label>
                    <textarea
                        id="assignmentNotes"
                        wire:model="assignmentNotes"
                        rows="3"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Raison de l'affectation, contexte, etc."
                        {{ $isProcessing ? 'disabled' : '' }}
                    ></textarea>
                    @error('assignmentNotes')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <x-button
                        type="button"
                        @click="$dispatch('close-modal', 'bulk-depot-assignment')"
                        variant="secondary"
                        :disabled="$isProcessing"
                    >
                        Annuler
                    </x-button>

                    <x-button
                        type="submit"
                        variant="primary"
                        icon="check"
                        :disabled="$isProcessing || !$targetDepotId || ($capacityPreview && !$capacityPreview['sufficient_capacity'])"
                    >
                        @if ($isProcessing)
                            <x-iconify icon="lucide:loader-2" class="w-4 h-4 mr-2 animate-spin" />
                            Affectation en cours...
                        @else
                            Affecter les Véhicules
                        @endif
                    </x-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
