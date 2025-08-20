<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Affectations') }}
        </h2>
    </x-slot>

    <div x-data="{
            activeTab: 'table',
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
                    
                    {{-- En-tête avec onglets --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                                Affectations
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $assignments->total() }}
                                </span>
                            </h3>
                        </div>
                        
                        {{-- Onglets de vue --}}
                        <div class="flex items-center space-x-2">
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 flex items-center">
                                    <x-lucide-table class="w-4 h-4 mr-2"/>
                                    Tableau
                                </button>
                                <button @click="activeTab = 'calendar'" :class="activeTab === 'calendar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 flex items-center">
                                    <x-lucide-calendar class="w-4 h-4 mr-2"/>
                                    Calendrier
                                </button>
                            </div>
                            
                            @can('create assignments')
                                <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                    <x-lucide-plus-circle class="w-4 h-4 mr-2"/>
                                    Nouvelle Affectation
                                </a>
                            @endcan
                        </div>
                    </div>

                    {{-- Vue Tableau --}}
                    <div x-show="activeTab === 'table'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
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
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($assignment->driver?->photo_path)
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $assignment->driver->photo_path) }}" alt="Photo de {{ $assignment->driver->first_name }}">
                                                        @else
                                                            <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <x-lucide-user class="h-6 w-6 text-gray-400"/>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900">{{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $assignment->driver?->personal_phone ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $assignment->vehicle?->registration_plate }}</div>
                                                <div class="text-xs text-primary-600 font-semibold mt-1 flex items-center">
                                                    <x-lucide-bar-chart class="w-4 h-4 mr-1"/>
                                                    {{ number_format($assignment->vehicle?->current_mileage, 0, ',', ' ') }} km
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <x-lucide-calendar class="w-4 h-4 mr-2 text-gray-400"/>
                                                    <div>
                                                        <div>{{ $assignment->start_datetime->format('d/m/Y H:i') }}</div>
                                                        <div class="text-xs">→ @if($assignment->end_datetime) {{ $assignment->end_datetime->format('d/m/Y H:i') }} @else En cours @endif</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if(is_null($assignment->end_datetime))
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <x-lucide-play-circle class="w-4 h-4 mr-1.5"/>
                                                        En cours
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <x-lucide-check-circle class="w-4 h-4 mr-1.5"/>
                                                        Terminée
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    @can('create handovers')
                                                        @if($assignment->handoverForm)
                                                            <a href="{{ route('admin.handovers.vehicles.show', $assignment->handoverForm) }}" title="Voir la Fiche de Remise" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600 transition-colors duration-200">
                                                                <x-lucide-file-text class="h-5 w-5"/>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.handovers.vehicles.create', ['assignment' => $assignment->id]) }}" title="Créer Fiche de Remise" class="p-2 rounded-full text-gray-400 hover:bg-blue-100 hover:text-blue-600 transition-colors duration-200">
                                                                <x-lucide-file-plus class="h-5 w-5"/>
                                                            </a>
                                                        @endif
                                                    @endcan
                                                    @can('edit assignments')
                                                        <a href="{{ route('admin.assignments.edit', $assignment) }}" title="Modifier les notes/motif" class="p-2 rounded-full text-gray-400 hover:bg-primary-100 hover:text-primary-600 transition-colors duration-200">
                                                            <x-lucide-file-pen-line class="h-5 w-5"/>
                                                        </a>
                                                    @endcan
                                                    @if(is_null($assignment->end_datetime))
                                                        @can('end assignments')
                                                            <button type="button" @click="openEndModal($event)" data-assignment='@json($assignment->load('vehicle'))' data-url="{{ route('admin.assignments.end', $assignment) }}" title="Terminer l'affectation" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600 transition-colors duration-200">
                                                                <x-lucide-stop-circle class="h-5 w-5"/>
                                                            </button>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <x-lucide-calendar-x class="w-12 h-12 text-gray-300 mb-4"/>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Aucune affectation trouvée</p>
                                                <p class="text-gray-500">Commencez par créer une nouvelle affectation.</p>
                                            </div>
                                        </td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">{{ $assignments->withQueryString()->links() }}</div>
                    </div>

                    {{-- Vue Calendrier --}}
                    <div x-show="activeTab === 'calendar'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        @include('admin.assignments.partials.calendar-view', ['assignments' => $assignments->items()])
                    </div>
                </div>
            </div>
        </div>

        {{-- Modale pour terminer l'affectation --}}
        <div x-show="showEndModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showEndModal = false" class="bg-white rounded-lg shadow-xl p-6 sm:p-8 w-full max-w-lg mx-auto transform transition-all" x-show="showEndModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Terminer l'affectation</h3>
                <p class="text-sm text-gray-600 mb-2">
                    Véhicule: <strong x-text="`${assignmentToEnd.vehicle?.brand} ${assignmentToEnd.vehicle?.model} (${assignmentToEnd.vehicle?.registration_plate})`"></strong>
                </p>
                <p class="text-sm text-gray-600 mb-6">
                    Début: <span x-text="new Date(assignmentToEnd.start_datetime).toLocaleString('fr-FR')"></span>
                    avec <span x-text="assignmentToEnd.start_mileage ? assignmentToEnd.start_mileage.toLocaleString('fr-FR') : 'N/A'"></span> km.
                </p>

                <form x-ref="endForm" @submit.prevent="submitEndForm">
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="space-y-4">
                        <div>
                            <x-datetime-picker
                                id="end_datetime"
                                name="end_datetime"
                                label="Date et heure de fin"
                            />
                            <template x-if="modalErrors.end_datetime"><p class="text-xs text-red-600 mt-1" x-text="modalErrors.end_datetime[0]"></p></template>
                        </div>
                        <div>
                            <x-input-label for="end_mileage" value="Kilométrage de fin" />
                            <x-text-input id="end_mileage" name="end_mileage" type="number" class="mt-1 block w-full" placeholder="Kilométrage actuel" />
                            <template x-if="modalErrors.end_mileage"><p class="text-xs text-red-600 mt-1" x-text="modalErrors.end_mileage[0]"></p></template>
                        </div>
                    </div>
                    <template x-if="modalErrors.general"><p class="text-sm text-red-600 mt-4" x-text="modalErrors.general[0]"></p></template>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="showEndModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Annuler</button>
                        <button type="submit" :disabled="isSubmitting" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 disabled:opacity-50">
                            <span x-show="!isSubmitting">Terminer</span>
                            <span x-show="isSubmitting">Traitement...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

