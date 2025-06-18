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
                        this.showEndModal = false;
                        window.location.reload();
                    }
                } catch (error) {
                    this.modalErrors = { general: [error.message] };
                } finally {
                    this.isSubmitting = false;
                }
            }
        }"
         class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SECTION FILTRES, RECHERCHE ET PAGINATION --}}
            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.assignments.index') }}" method="GET">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-2 md:space-y-0">
                        <div class="flex-grow">
                            <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Immat, marque, chauffeur..." class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                        </div>
                        <div class="flex-shrink-0">
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                <option value="">Tous</option>
                                <option value="en_cours" @selected(($filters['status'] ?? '') == 'en_cours')>En cours</option>
                                <option value="terminee" @selected(($filters['status'] ?? '') == 'terminee')>Terminées</option>
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <label for="per_page" class="block text-sm font-medium text-gray-700">Par page</label>
                            <select name="per_page" id="per_page" onchange="this.form.submit()" class="mt-1 block w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm text-sm">
                                @foreach(['15', '30', '50'] as $value)
                                    <option value="{{ $value }}" @selected(($filters['per_page'] ?? '15') == $value)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-shrink-0 flex space-x-2">
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700">Filtrer</button>
                            <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert"><p class="font-bold">{{ session('success') }}</p></div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-700">Historique des Affectations</h3>
                        @can('create assignments')
                            <a href="{{ route('admin.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
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
                                                        <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center"><svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg></span>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-gray-900">{{ $assignment->driver?->first_name }} {{ $assignment->driver?->last_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $assignment->driver?->personal_phone ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap"><div class="text-sm font-medium text-gray-900">{{ $assignment->vehicle?->brand }} {{ $assignment->vehicle?->model }}</div><div class="text-xs text-gray-500 font-mono">{{ $assignment->vehicle?->registration_plate }}</div></td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">
                                            <div>De: {{ $assignment->start_datetime->format('d/m/Y H:i') }}</div>
                                            <div>À: @if($assignment->end_datetime) {{ $assignment->end_datetime->format('d/m/Y H:i') }} @else - @endif</div>
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm">
                                            @if(is_null($assignment->end_datetime))
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">En cours</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Terminée</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">

                                        {{-- Bouton pour la Fiche de Remise --}}
                                        @can('create assignments')
                                            @if($assignment->handoverForm)
                                                <a href="{{ route('admin.handovers.vehicles.show', $assignment->handoverForm) }}" title="Voir la Fiche de Remise" class="p-2 rounded-full text-violet-600 bg-violet-100 hover:bg-violet-200">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </a>
                                            @else
                                                {{-- CORRECTION : Le lien pointe maintenant vers la bonne route avec le bon paramètre --}}
                                                <a href="{{ route('admin.handovers.vehicles.create', ['assignment' => $assignment->id]) }}" title="Créer Fiche de Remise" class="p-2 rounded-full text-gray-400 hover:bg-blue-100 hover:text-blue-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </a>
                                            @endif
                                        @endcan
                                        
                                        @can('edit assignments')
                                            <a href="{{ route('admin.assignments.edit', $assignment) }}" title="Modifier les notes" class="p-2 rounded-full text-gray-400 hover:bg-violet-100 hover:text-violet-600">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.536L16.732 3.732z" /></svg>
                                            </a>
                                        @endcan



                                        @if(is_null($assignment->end_datetime))
                                            @can('end assignments')
                                                <button type="button" @click="openEndModal($event)" data-assignment='@json($assignment->load('vehicle'))' data-url="{{ route('admin.assignments.end', $assignment) }}" title="Terminer l'affectation" class="p-2 rounded-full text-gray-400 hover:bg-yellow-100 hover:text-yellow-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Aucune affectation trouvée pour les critères sélectionnés.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $assignments->withQueryString()->links() }}</div>
                </div>
            </div>
        </div>

        {{-- La modale pour terminer l'affectation est identique à la version précédente et fonctionnelle --}}
        <div x-show="showEndModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60" style="display: none;">
            <div @click.away="showEndModal = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg mx-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-violet-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                         <h3 class="text-base font-semibold leading-6 text-gray-900">Terminer l'Affectation</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">
                                Veuillez renseigner les informations de fin pour le véhicule <strong x-text="assignmentToEnd.vehicle?.registration_plate"></strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Zone d'affichage des erreurs de validation AJAX --}}
                <div x-show="Object.keys(modalErrors).length > 0" class="mt-4 bg-red-50 p-3 rounded-md text-sm" style="display: none;">
                    <ul class="list-disc list-inside text-red-600">
                        <template x-for="errorMessages in Object.values(modalErrors)">
                            <template x-for="message in errorMessages">
                                <li x-text="message"></li>
                            </template>
                        </template>
                    </ul>
                </div>

                <form @submit.prevent="submitEndForm" x-ref="endForm" class="mt-4 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label for="end_datetime" class="block font-medium text-sm text-gray-700">Date et Heure de Fin <span class="text-red-500">*</span></label>
                        <input id="end_datetime" type="datetime-local" name="end_datetime" required class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="end_mileage" class="block font-medium text-sm text-gray-700">Kilométrage de Fin <span class="text-red-500">*</span></label>
                        <input id="end_mileage" type="number" name="end_mileage" :min="assignmentToEnd.start_mileage" required class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                    </div>
                    <div class="mt-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="isSubmitting" class="inline-flex w-full justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 sm:ml-3 sm:w-auto disabled:opacity-50">
                            <span x-show="!isSubmitting">Confirmer et Terminer</span>
                            <span x-show="isSubmitting">Enregistrement...</span>
                        </button>
                        <button type="button" @click="showEndModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
