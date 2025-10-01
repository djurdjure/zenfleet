{{-- Modal de création de demande --}}
<x-modal wire:model="showCreateModal" max-width="4xl">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Nouvelle Demande de Réparation</h3>
            <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="createRequest" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Véhicule --}}
                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Véhicule *</label>
                    <select wire:model="vehicle_id" id="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionner un véhicule</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Priorité --}}
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priorité *</label>
                    <select wire:model="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="non_urgente">Non urgente</option>
                        <option value="a_prevoir">À prévoir</option>
                        <option value="urgente">Urgente</option>
                    </select>
                    @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description de la panne/réparation *</label>
                <textarea wire:model="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Décrivez en détail le problème constaté, les symptômes, etc."></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Localisation --}}
                <div>
                    <label for="location_description" class="block text-sm font-medium text-gray-700">Localisation du véhicule</label>
                    <input wire:model="location_description" type="text" id="location_description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: Parking bureau, Garage Alger-Centre, etc.">
                    @error('location_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Coût estimé --}}
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700">Coût estimé (DA)</label>
                    <input wire:model="estimated_cost" type="number" step="0.01" min="0" id="estimated_cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0.00">
                    @error('estimated_cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Photos --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Photos (max 5)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="photos" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                <span>Télécharger des photos</span>
                                <input wire:model="photos" id="photos" type="file" class="sr-only" multiple accept="image/*">
                            </label>
                            <p class="pl-1">ou glisser-déposer</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 5MB chacune</p>
                    </div>
                </div>
                @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

                @if($photos)
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        @foreach($photos as $index => $photo)
                            <div class="relative">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-20 object-cover rounded">
                                <button type="button" wire:click="removePhoto({{ $index }})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Pièces jointes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Pièces jointes (max 3)</label>
                <input wire:model="attachments" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt">
                @error('attachments.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    Créer la demande
                </button>
            </div>
        </form>
    </div>
</x-modal>

{{-- Modal d'approbation/validation --}}
<x-modal wire:model="showApprovalModal" max-width="2xl">
    @if($selectedRequest)
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">
                @if($selectedRequest->status === 'en_attente')
                    Traitement de la demande
                @else
                    Validation managériale
                @endif
            </h3>
            <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Détails de la demande --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-700">Véhicule</p>
                    <p class="text-sm text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Priorité</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        @if($selectedRequest->priority === 'urgente') bg-red-100 text-red-800
                        @elseif($selectedRequest->priority === 'a_prevoir') bg-orange-100 text-orange-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $selectedRequest->priority_label }}
                    </span>
                </div>
                <div class="col-span-2">
                    <p class="text-sm font-medium text-gray-700">Description</p>
                    <p class="text-sm text-gray-900">{{ $selectedRequest->description }}</p>
                </div>
                @if($selectedRequest->estimated_cost)
                <div>
                    <p class="text-sm font-medium text-gray-700">Coût estimé</p>
                    <p class="text-sm text-gray-900">{{ number_format($selectedRequest->estimated_cost, 2) }} DA</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Formulaire de décision --}}
        <div class="space-y-4">
            <div>
                <label for="approval_comments" class="block text-sm font-medium text-gray-700">
                    Commentaires @if($selectedRequest->status === 'en_attente')(optionnels)@endif
                </label>
                <textarea wire:model="approvalComments" id="approval_comments" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ajoutez vos commentaires..."></textarea>
                @error('approvalComments') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" wire:click="closeApprovalModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Annuler
                </button>

                @if($selectedRequest->status === 'en_attente')
                    {{-- Actions superviseur --}}
                    <button wire:click="rejectRequest" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                        Rejeter
                    </button>
                    <button wire:click="approveRequest" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                        Approuver
                    </button>
                @elseif($selectedRequest->status === 'accord_initial')
                    {{-- Actions manager --}}
                    <button wire:click="rejectByManager" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
                        Rejeter
                    </button>
                    <button wire:click="validateRequest" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Valider
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</x-modal>

{{-- Modal de détails --}}
<x-modal wire:model="showDetailsModal" max-width="4xl">
    @if($selectedRequest)
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Détails de la demande #{{ $selectedRequest->id }}</h3>
            <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-6">
            {{-- En-tête avec statut --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
                        <p class="text-sm text-gray-600">Demandé par {{ $selectedRequest->requester->name }} le {{ $selectedRequest->requested_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $selectedRequest->status_color }}-100 text-{{ $selectedRequest->status_color }}-800">
                            {{ $selectedRequest->status_label }}
                        </span>
                        <p class="text-sm text-gray-600 mt-1">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($selectedRequest->priority === 'urgente') bg-red-100 text-red-800
                                @elseif($selectedRequest->priority === 'a_prevoir') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $selectedRequest->priority_label }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Description et détails --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Description</h5>
                        <p class="text-sm text-gray-900 bg-white p-3 rounded border">{{ $selectedRequest->description }}</p>
                    </div>

                    @if($selectedRequest->location_description)
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Localisation</h5>
                        <p class="text-sm text-gray-900">{{ $selectedRequest->location_description }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        @if($selectedRequest->estimated_cost)
                        <div>
                            <h5 class="text-sm font-medium text-gray-700">Coût estimé</h5>
                            <p class="text-sm text-gray-900">{{ number_format($selectedRequest->estimated_cost, 2) }} DA</p>
                        </div>
                        @endif

                        @if($selectedRequest->actual_cost)
                        <div>
                            <h5 class="text-sm font-medium text-gray-700">Coût réel</h5>
                            <p class="text-sm text-gray-900">{{ number_format($selectedRequest->actual_cost, 2) }} DA</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Timeline du workflow --}}
                    <div>
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Historique des actions</h5>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">Demande créée</p>
                                                <p class="text-xs text-gray-500">{{ $selectedRequest->requested_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @if($selectedRequest->supervisor_decided_at)
                                <li class="relative pb-8">
                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                                    <div class="relative flex space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $selectedRequest->supervisor_decision === 'accepte' ? 'green' : 'red' }}-500">
                                            @if($selectedRequest->supervisor_decision === 'accepte')
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    {{ $selectedRequest->supervisor_decision === 'accepte' ? 'Approuvée' : 'Rejetée' }} par {{ $selectedRequest->supervisor->name }}
                                                </p>
                                                @if($selectedRequest->supervisor_comments)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->supervisor_comments }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500">{{ $selectedRequest->supervisor_decided_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($selectedRequest->manager_decided_at)
                                <li class="relative pb-8">
                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                                    <div class="relative flex space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $selectedRequest->manager_decision === 'valide' ? 'blue' : 'red' }}-500">
                                            @if($selectedRequest->manager_decision === 'valide')
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    {{ $selectedRequest->manager_decision === 'valide' ? 'Validée' : 'Rejetée' }} par {{ $selectedRequest->manager->name }}
                                                </p>
                                                @if($selectedRequest->manager_comments)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->manager_comments }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500">{{ $selectedRequest->manager_decided_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($selectedRequest->work_started_at)
                                <li class="relative pb-8">
                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                                    <div class="relative flex space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-500">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">Travaux démarrés</p>
                                                @if($selectedRequest->assignedSupplier)
                                                    <p class="text-sm text-gray-600">{{ $selectedRequest->assignedSupplier->company_name }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500">{{ $selectedRequest->work_started_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @if($selectedRequest->work_completed_at)
                                <li class="relative">
                                    <div class="relative flex space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">Travaux terminés</p>
                                                @if($selectedRequest->completion_notes)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $selectedRequest->completion_notes }}</p>
                                                @endif
                                                @if($selectedRequest->final_rating)
                                                    <div class="flex items-center mt-2">
                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600 ml-1">{{ $selectedRequest->final_rating }}/10</span>
                                                    </div>
                                                @endif
                                                <p class="text-xs text-gray-500">{{ $selectedRequest->work_completed_at->format('d/m/Y à H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Photos --}}
            @if($selectedRequest->photos && count($selectedRequest->photos) > 0)
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-3">Photos de la panne</h5>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($selectedRequest->photos as $photo)
                        <img src="{{ Storage::url($photo) }}" alt="Photo" class="w-full h-20 object-cover rounded border">
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Photos de travaux --}}
            @if($selectedRequest->work_photos && count($selectedRequest->work_photos) > 0)
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-3">Photos des travaux</h5>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($selectedRequest->work_photos as $photo)
                        <img src="{{ Storage::url($photo) }}" alt="Photo travaux" class="w-full h-20 object-cover rounded border">
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Pièces jointes --}}
            @if($selectedRequest->attachments && count($selectedRequest->attachments) > 0)
            <div>
                <h5 class="text-sm font-medium text-gray-700 mb-3">Pièces jointes</h5>
                <div class="space-y-2">
                    @foreach($selectedRequest->attachments as $attachment)
                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="flex items-center p-2 border rounded hover:bg-gray-50">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $attachment['name'] }}</span>
                            <span class="text-xs text-gray-500 ml-2">({{ number_format($attachment['size'] / 1024, 0) }} KB)</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="flex justify-end mt-6">
            <button wire:click="closeDetailsModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Fermer
            </button>
        </div>
    </div>
    @endif
</x-modal>

{{-- Modal d'assignation de fournisseur --}}
<x-modal wire:model="showAssignSupplierModal" max-width="2xl">
    @if($selectedRequest)
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Assigner un fournisseur</h3>
            <button wire:click="closeAssignSupplierModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($selectedRequest->description, 150) }}</p>
            </div>

            <div>
                <label for="supplier_select" class="block text-sm font-medium text-gray-700">Sélectionner un fournisseur</label>
                <select wire:model="selectedSupplierId" id="supplier_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choisir un fournisseur...</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->company_name }} - {{ $supplier->supplier_type }}
                            @if($supplier->rating) (Note: {{ $supplier->rating }}/10) @endif
                        </option>
                    @endforeach
                </select>
                @error('selectedSupplierId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" wire:click="closeAssignSupplierModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Annuler
                </button>
                <button wire:click="assignSupplier" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                    Assigner
                </button>
            </div>
        </div>
    </div>
    @endif
</x-modal>

{{-- Modal de complétion des travaux --}}
<x-modal wire:model="showCompleteWorkModal" max-width="3xl">
    @if($selectedRequest)
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Terminer les travaux</h3>
            <button wire:click="closeCompleteWorkModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="completeWork" class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900">{{ $selectedRequest->vehicle->registration_plate }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($selectedRequest->description, 150) }}</p>
                @if($selectedRequest->assignedSupplier)
                    <p class="text-sm text-blue-600 mt-1">Fournisseur: {{ $selectedRequest->assignedSupplier->company_name }}</p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="actual_cost" class="block text-sm font-medium text-gray-700">Coût réel (DA) *</label>
                    <input wire:model="actualCost" type="number" step="0.01" min="0" id="actual_cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('actualCost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="final_rating" class="block text-sm font-medium text-gray-700">Note de satisfaction (1-10)</label>
                    <input wire:model="finalRating" type="number" min="1" max="10" step="0.1" id="final_rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('finalRating') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="completion_notes" class="block text-sm font-medium text-gray-700">Notes de complétion</label>
                <textarea wire:model="completionNotes" id="completion_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Détails des travaux effectués, observations, etc."></textarea>
                @error('completionNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Photos des travaux terminés</label>
                <input wire:model="workPhotos" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" multiple accept="image/*">
                @error('workPhotos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" wire:click="closeCompleteWorkModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
                    Terminer les travaux
                </button>
            </div>
        </form>
    </div>
    @endif
</x-modal>