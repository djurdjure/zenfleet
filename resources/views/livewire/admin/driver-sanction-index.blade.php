<div>
    {{-- Header avec titre et bouton d'ajout --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                {{-- Titre --}}
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-gavel text-red-600"></i>
                        Sanctions Chauffeurs
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Gestion des sanctions disciplinaires appliquées aux chauffeurs
                    </p>
                </div>

                {{-- Bouton Ajouter --}}
                @can('create', App\Models\DriverSanction::class)
                <button
                    wire:click="create"
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Ajouter une sanction
                </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Messages flash --}}
        @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex">
                <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Barre de recherche et filtres --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            {{-- Barre de recherche --}}
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Recherche --}}
                    <div class="flex-1">
                        <div class="relative">
                            <input
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                placeholder="Rechercher par raison ou nom du chauffeur..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    {{-- Bouton Filtres --}}
                    <button
                        wire:click="$toggle('showFilters')"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Filtres
                        @if($showFilters)
                        <span class="ml-2">⬆</span>
                        @else
                        <span class="ml-2">⬇</span>
                        @endif
                    </button>

                    {{-- Bouton Réinitialiser --}}
                    @if($search || $filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo || $filterArchived !== 'active')
                    <button
                        wire:click="resetFilters"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times-circle mr-2"></i>
                        Réinitialiser
                    </button>
                    @endif
                </div>
            </div>

            {{-- Panel des filtres --}}
            @if($showFilters)
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Filtre Type de sanction --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-gavel text-red-500 mr-1"></i>
                            Type de sanction
                        </label>
                        <select
                            wire:model.live="filterSanctionType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Tous les types</option>
                            @foreach($sanctionTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Chauffeur --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-500 mr-1"></i>
                            Chauffeur
                        </label>
                        <select
                            wire:model.live="filterDriverId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Tous les chauffeurs</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Date début --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-green-500 mr-1"></i>
                            Date début
                        </label>
                        <input
                            wire:model.live="filterDateFrom"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    {{-- Filtre Date fin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check text-green-500 mr-1"></i>
                            Date fin
                        </label>
                        <input
                            wire:model.live="filterDateTo"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    {{-- Filtre Statut archivé --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-archive text-gray-500 mr-1"></i>
                            Statut
                        </label>
                        <select
                            wire:model.live="filterArchived"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="active">Actives</option>
                            <option value="archived">Archivées</option>
                            <option value="all">Toutes</option>
                        </select>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Tableau des sanctions --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Chauffeur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type de sanction
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Raison
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Superviseur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sanctions as $sanction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Chauffeur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $sanction->driver->first_name }} {{ $sanction->driver->last_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type de sanction --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $sanction->getSanctionTypeColor() }}-100 text-{{ $sanction->getSanctionTypeColor() }}-800">
                                    <i class="fas {{ $sanction->getSanctionTypeIcon() }} mr-2"></i>
                                    {{ $sanction->getSanctionTypeLabel() }}
                                </span>
                            </td>

                            {{-- Raison --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $sanction->reason }}">
                                    {{ Str::limit($sanction->reason, 60) }}
                                </div>
                                @if($sanction->attachment_path)
                                <a href="{{ $sanction->getAttachmentUrl() }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 flex items-center mt-1">
                                    <i class="fas fa-paperclip mr-1"></i>
                                    Pièce jointe
                                </a>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $sanction->sanction_date->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Il y a {{ $sanction->getDaysSinceSanction() }} jours
                                </div>
                            </td>

                            {{-- Superviseur --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $sanction->supervisor->name }}
                                </div>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sanction->isArchived())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-archive mr-1"></i>
                                    Archivée
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Modifier --}}
                                    @can('update', $sanction)
                                    <button
                                        wire:click="edit({{ $sanction->id }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                        title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcan

                                    {{-- Archiver/Désarchiver --}}
                                    @if($sanction->isArchived())
                                        @can('unarchive', $sanction)
                                        <button
                                            wire:click="unarchive({{ $sanction->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                            title="Désarchiver">
                                            <i class="fas fa-box-open"></i>
                                        </button>
                                        @endcan
                                    @else
                                        @can('archive', $sanction)
                                        <button
                                            wire:click="archive({{ $sanction->id }})"
                                            class="text-gray-600 hover:text-gray-900 transition-colors"
                                            title="Archiver">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                        @endcan
                                    @endif

                                    {{-- Supprimer --}}
                                    @can('delete', $sanction)
                                    <button
                                        wire:click="confirmDelete({{ $sanction->id }})"
                                        class="text-red-600 hover:text-red-900 transition-colors"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium">Aucune sanction trouvée</p>
                                    <p class="text-gray-400 text-sm mt-2">
                                        @if($search || $filterSanctionType || $filterDriverId || $filterDateFrom || $filterDateTo)
                                        Essayez de modifier vos filtres de recherche
                                        @else
                                        Commencez par ajouter une nouvelle sanction
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($sanctions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sanctions->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Modal de création/édition --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            {{-- Centrage du modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenu du modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="save">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white flex items-center">
                                <i class="fas fa-gavel mr-3"></i>
                                {{ $editMode ? 'Modifier la sanction' : 'Nouvelle sanction' }}
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        {{-- Chauffeur --}}
                        <div>
                            <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                Chauffeur <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="driver_id"
                                id="driver_id"
                                class="w-full px-4 py-3 bg-white border @error('driver_id') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                                <option value="">Sélectionner un chauffeur</option>
                                @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                                @endforeach
                            </select>
                            @error('driver_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type de sanction --}}
                        <div>
                            <label for="sanction_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-gavel text-red-500 mr-2"></i>
                                Type de sanction <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="sanction_type"
                                id="sanction_type"
                                class="w-full px-4 py-3 bg-white border @error('sanction_type') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                                <option value="">Sélectionner un type</option>
                                @foreach($sanctionTypes as $key => $type)
                                <option value="{{ $key }}">
                                    {{ $type['label'] }} (Sévérité: {{ $type['severity'] }})
                                </option>
                                @endforeach
                            </select>
                            @error('sanction_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date de sanction --}}
                        <div>
                            <label for="sanction_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                Date de sanction <span class="text-red-500">*</span>
                            </label>
                            <input
                                wire:model="sanction_date"
                                type="date"
                                id="sanction_date"
                                max="{{ now()->format('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-white border @error('sanction_date') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                            @error('sanction_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Raison --}}
                        <div>
                            <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-alt text-orange-500 mr-2"></i>
                                Raison détaillée <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="reason"
                                id="reason"
                                rows="5"
                                placeholder="Décrivez en détail les motifs de la sanction..."
                                class="w-full px-4 py-3 bg-white border @error('reason') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all resize-none"></textarea>
                            @error('reason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Minimum 10 caractères, maximum 5000 caractères</p>
                        </div>

                        {{-- Pièce jointe existante --}}
                        @if($editMode && $existingAttachmentPath)
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-paperclip text-blue-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Pièce jointe existante</p>
                                        <a href="{{ Storage::url($existingAttachmentPath) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                            Voir le fichier
                                        </a>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeExistingAttachment"
                                    class="text-red-600 hover:text-red-800 transition-colors"
                                    title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endif

                        {{-- Upload de pièce jointe --}}
                        <div>
                            <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-paperclip text-purple-500 mr-2"></i>
                                Pièce jointe (optionnelle)
                            </label>
                            <input
                                wire:model="attachment"
                                type="file"
                                id="attachment"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full px-4 py-3 bg-white border @error('attachment') border-red-500 @else border-gray-200 @enderror rounded-xl focus:border-red-400 focus:ring-4 focus:ring-red-50 transition-all">
                            @error('attachment')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (max 5 MB)</p>

                            {{-- Indicateur de chargement --}}
                            <div wire:loading wire:target="attachment" class="mt-2 text-sm text-blue-600">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Chargement du fichier...
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                        <button
                            type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save mr-2"></i>
                                {{ $editMode ? 'Mettre à jour' : 'Créer la sanction' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Enregistrement...
                            </span>
                        </button>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal de confirmation de suppression --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelDelete"></div>

            {{-- Centrage du modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Contenu du modal --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Confirmer la suppression
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer cette sanction ? Cette action est irréversible et supprimera également la pièce jointe associée.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 flex flex-col sm:flex-row-reverse gap-3">
                    <button
                        type="button"
                        wire:click="delete"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Supprimer
                    </button>
                    <button
                        type="button"
                        wire:click="cancelDelete"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
