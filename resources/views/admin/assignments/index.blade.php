<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Affectations') }}
        </h2>
    </x-slot>

    <div x-data="{
            showEndModal: false,
            assignmentToEnd: {},
            endFormUrl: '',
            modalErrors: {},
            isSubmitting: false,
            openEndModal(event) {
                const button = event.currentTarget;
                this.assignmentToEnd = JSON.parse(button.dataset.assignment);
                this.endFormUrl = button.dataset.url;
                this.modalErrors = {};
                this.showEndModal = true;
            },
            async submitEndForm() {
                this.isSubmitting = true;
                this.modalErrors = {};
                const formData = new FormData(this.$refs.endForm);
                try {
                    const response = await fetch(this.endFormUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        if (response.status === 422) { this.modalErrors = data.errors; }
                        else { throw new Error(data.message || 'Une erreur serveur est survenue.'); }
                    } else {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                type: 'success',
                                message: 'Affectation Terminée',
                                description: 'Le statut du véhicule et du chauffeur a été mis à jour.'
                            }
                        }));
                        this.showEndModal = false;
                        setTimeout(() => window.location.reload(), 1500);
                    }
                } catch (error) {
                    this.modalErrors = { general: [error.message] };
                } finally {
                    this.isSubmitting = false;
                }
            }
        }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Section des Filtres --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.assignments.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Immat, marque, chauffeur..." class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm text-sm">
                                @foreach(['15', '30', '50'] as $value)
                                    <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0 flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Filtrer</button>
                            <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">Historique des Affectations</h3>
                        @can('create assignments')
                            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                <x-heroicon-o-plus-circle class="w-4 h-4 mr-2"/>
                                Nouvelle Affectation
                            </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chauffeur</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Véhicule</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($assignments as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($assignment->driver?->photo_path)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $assignment->driver->photo_path) }}" alt="Photo de {{ $assignment->driver->first_name }}">
                                                    @else
                                                        <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <x-heroicon-s-user class="h-6 w-6 text-gray-400"/>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $assignment->driver?->personal_phone ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $assignment->vehicle?->registration_plate }}</div>
                                            <div class="text-xs text-primary-600 font-semibold mt-1 flex items-center">
                                                {{-- CORRECTION DÉFINITIVE : Remplacement par une icône universelle. --}}
                                                {{-- Après la mise à jour du package, vous pourrez utiliser <x-heroicon-s-gauge /> si vous préférez. --}}
                                                <x-heroicon-s-chart-bar class="w-4 h-4 mr-1"/>
                                                {{ number_format($assignment->vehicle?->current_mileage, 0, ',', ' ') }} km
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <div>De: {{ $assignment->start_datetime->format('d/m/Y H:i') }}</div>
                                            <div>À: @if($assignment->end_datetime) {{ $assignment->end_datetime->format('d/m/Y H:i') }} @else - @endif</div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            @if(is_null($assignment->end_datetime))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <x-heroicon-s-play-circle class="w-4 h-4 mr-1.5"/>
                                                    En cours
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <x-heroicon-s-check-circle class="w-4 h-4 mr-1.5"/>
                                                    Terminée
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @can('create handovers')
                                                    @if($assignment->handoverForm)
                                                        <a href="{{ route('admin.handovers.vehicles.show', $assignment->handoverForm) }}" title="Voir la Fiche de Remise" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                            <x-heroicon-o-document-text class="h-5 w-5"/>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.handovers.vehicles.create', ['assignment' => $assignment->id]) }}" title="Créer Fiche de Remise" class="p-2 rounded-full text-gray-400 hover:bg-blue-100 hover:text-blue-600">
                                                            <x-heroicon-o-document-plus class="h-5 w-5"/>
                                                        </a>
                                                    @endif
                                                @endcan
                                                @can('edit assignments')
                                                    <a href="{{ route('admin.assignments.edit', $assignment) }}" title="Modifier les notes/motif" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600">
                                                        <x-heroicon-o-pencil-square class="h-5 w-5"/>
                                                    </a>
                                                @endcan
                                                @if(is_null($assignment->end_datetime))
                                                    @can('end assignments')
                                                        <button type="button" @click="openEndModal($event)" data-assignment='@json($assignment->load('vehicle'))' data-url="{{ route('admin.assignments.end', $assignment) }}" title="Terminer l'affectation" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                            <x-heroicon-o-stop-circle class="h-5 w-5"/>
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucune affectation trouvée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $assignments->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>

        {{-- Modale pour terminer l'affectation --}}
        <div x-show="showEndModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            {{-- Contenu de la modale reste inchangé --}}
        </div>
    </div>
</x-app-layout>