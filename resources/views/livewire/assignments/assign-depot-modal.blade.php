<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('show') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    wire:click="close"
                ></div>

                {{-- Modal --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg"
                >
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                @if($action === 'transfer')
                                    Transférer le véhicule
                                @elseif($action === 'unassign')
                                    Retirer du dépôt
                                @else
                                    Affecter à un dépôt
                                @endif
                            </h3>
                            @if($vehicle)
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $vehicle->registration_plate }}
                                    @if($vehicle->make || $vehicle->model)
                                        - {{ $vehicle->make->name ?? '' }} {{ $vehicle->model->name ?? '' }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                            <x-iconify icon="mdi:close" class="w-6 h-6" />
                        </button>
                    </div>

                    {{-- Statut actuel --}}
                    @if($vehicle && $vehicle->depot)
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <x-iconify icon="mdi:office-building" class="w-5 h-5 text-blue-600 mr-2" />
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Dépôt actuel</p>
                                    <p class="text-sm text-blue-700">{{ $vehicle->depot->name }} ({{ $vehicle->depot->code }})</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabs Actions --}}
                    @if($vehicle && $vehicle->depot_id)
                        <div class="flex border-b border-gray-200 mb-6">
                            <button
                                wire:click="setAction('transfer')"
                                class="px-4 py-2 text-sm font-medium {{ $action === 'transfer' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900' }}"
                            >
                                <x-iconify icon="mdi:swap-horizontal" class="w-4 h-4 inline mr-1" />
                                Transférer
                            </button>
                            <button
                                wire:click="setAction('unassign')"
                                class="px-4 py-2 text-sm font-medium {{ $action === 'unassign' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900' }}"
                            >
                                <x-iconify icon="mdi:close-circle" class="w-4 h-4 inline mr-1" />
                                Retirer
                            </button>
                        </div>
                    @endif

                    <form wire:submit="assign">
                        {{-- Sélection dépôt (si assign ou transfer) --}}
                        @if($action !== 'unassign' && $availableDepots)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Sélectionner un dépôt</label>

                                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                    @foreach($availableDepots as $depot)
                                        @php
                                            $isCurrentDepot = $vehicle && $vehicle->depot_id === $depot->id;
                                            $statusBadge = $this->getDepotStatusBadge($depot);
                                            $canSelect = $depot->can_assign && !$isCurrentDepot;
                                        @endphp

                                        <label
                                            class="block cursor-pointer"
                                            for="depot_{{ $depot->id }}"
                                        >
                                            <div class="border-2 rounded-lg p-4 transition-all
                                                {{ $selectedDepotId == $depot->id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}
                                                {{ !$canSelect ? 'opacity-50 cursor-not-allowed' : '' }}
                                                {{ $isCurrentDepot ? 'bg-gray-50' : '' }}
                                            ">
                                                <div class="flex items-start">
                                                    {{-- Radio --}}
                                                    <input
                                                        type="radio"
                                                        id="depot_{{ $depot->id }}"
                                                        wire:model="selectedDepotId"
                                                        value="{{ $depot->id }}"
                                                        class="mt-1 {{ !$canSelect ? 'cursor-not-allowed' : '' }}"
                                                        {{ !$canSelect ? 'disabled' : '' }}
                                                    />

                                                    {{-- Info dépôt --}}
                                                    <div class="ml-3 flex-1">
                                                        <div class="flex justify-between items-start mb-2">
                                                            <div>
                                                                <p class="font-semibold text-gray-900">{{ $depot->name }}</p>
                                                                <p class="text-sm text-gray-600">{{ $depot->code }}</p>
                                                            </div>

                                                            {{-- Badge statut --}}
                                                            <span class="px-2 py-1 text-xs rounded-full
                                                                {{ $statusBadge['color'] === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $statusBadge['color'] === 'orange' ? 'bg-orange-100 text-orange-800' : '' }}
                                                                {{ $statusBadge['color'] === 'red' ? 'bg-red-100 text-red-800' : '' }}
                                                                {{ $statusBadge['color'] === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}
                                                            ">
                                                                {{ $statusBadge['text'] }}
                                                            </span>
                                                        </div>

                                                        {{-- Localisation --}}
                                                        @if($depot->city || $depot->wilaya)
                                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                                <x-iconify icon="mdi:map-marker" class="w-4 h-4 mr-1" />
                                                                {{ $depot->city }}{{ $depot->wilaya ? ', ' . $depot->wilaya : '' }}
                                                            </div>
                                                        @endif

                                                        {{-- Distance --}}
                                                        @if($depot->distance !== null)
                                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                                <x-iconify icon="mdi:navigation" class="w-4 h-4 mr-1" />
                                                                {{ $depot->distance }} km
                                                            </div>
                                                        @endif

                                                        {{-- Capacité --}}
                                                        @if($depot->capacity)
                                                            <div class="mt-2">
                                                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                                    <span>Occupation</span>
                                                                    <span>{{ $depot->current_count }} / {{ $depot->capacity }}</span>
                                                                </div>
                                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                                    @php
                                                                        $percentage = $depot->capacity > 0 ? ($depot->current_count / $depot->capacity) * 100 : 0;
                                                                        $colorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-orange-500' : 'bg-green-500');
                                                                    @endphp
                                                                    <div class="{{ $colorClass }} h-1.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Raison impossibilité --}}
                                                        @if($isCurrentDepot)
                                                            <p class="text-sm text-gray-500 mt-2 italic">Dépôt actuel</p>
                                                        @elseif(!$depot->can_assign)
                                                            <p class="text-sm text-red-600 mt-2">
                                                                <x-iconify icon="mdi:alert-circle" class="w-4 h-4 inline mr-1" />
                                                                Dépôt complet
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('selectedDepotId')
                                    <span class="text-sm text-red-600 mt-2">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        {{-- Message pour unassign --}}
                        @if($action === 'unassign')
                            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="flex">
                                    <x-iconify icon="mdi:alert" class="w-5 h-5 text-orange-600 mr-2 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-medium text-orange-900">Attention</p>
                                        <p class="text-sm text-orange-700 mt-1">
                                            Le véhicule sera retiré du dépôt "{{ $vehicle->depot->name ?? '' }}" et n'aura plus d'affectation de dépôt.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Notes / Raison
                                <span class="text-gray-500 font-normal">(optionnel)</span>
                            </label>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                class="w-full border-gray-300 rounded-lg"
                                placeholder="Ajoutez des notes ou la raison de cette opération..."
                            ></textarea>
                            @error('notes') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="close" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Annuler
                            </button>

                            @if($action === 'unassign')
                                <button
                                    type="button"
                                    wire:click="unassign"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                >
                                    <x-iconify icon="mdi:close-circle" class="w-4 h-4 inline mr-1" />
                                    Retirer du dépôt
                                </button>
                            @else
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                >
                                    <x-iconify icon="mdi:check" class="w-4 h-4 inline mr-1" />
                                    {{ $action === 'transfer' ? 'Transférer' : 'Affecter' }}
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
