<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouvelle Affectation') }}
            </h2>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                            <x-lucide-table class="w-4 h-4 mr-2"/>
                            Affectations
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-lucide-chevron-right class="w-4 h-4 text-gray-400"/>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nouvelle</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- En-tête avec icône --}}
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-primary-100 rounded-xl">
                        <x-lucide-plus class="w-8 h-8 text-primary-600"/>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Créer une nouvelle affectation</h1>
                        <p class="text-gray-600 mt-1">Assignez un véhicule à un conducteur pour une période donnée</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-8">
                @csrf

                {{-- Section Conducteur --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-900 flex items-center">
                            <x-lucide-user class="w-5 h-5 mr-3"/>
                            Sélection du conducteur
                        </h3>
                        <p class="text-sm text-primary-700 mt-1">Choisissez le conducteur qui sera assigné au véhicule</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Conducteur <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="driver_id" name="driver_id" required
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('driver_id') border-red-300 @enderror">
                                        <option value="">Sélectionnez un conducteur</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->first_name }} {{ $driver->last_name }} - {{ $driver->personal_phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-lucide-user class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                @error('driver_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div id="driverInfo" class="hidden">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Informations du conducteur</h4>
                                    <div id="driverDetails" class="space-y-2 text-sm text-gray-600">
                                        {{-- Rempli par JavaScript --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Véhicule --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-900 flex items-center">
                            <x-lucide-car class="w-5 h-5 mr-3"/>
                            Sélection du véhicule
                        </h3>
                        <p class="text-sm text-primary-700 mt-1">Choisissez le véhicule à assigner au conducteur</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Véhicule <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="vehicle_id" name="vehicle_id" required
                                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('vehicle_id') border-red-300 @enderror">
                                        <option value="">Sélectionnez un véhicule</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}
                                                    data-brand="{{ $vehicle->brand }}"
                                                    data-model="{{ $vehicle->model }}"
                                                    data-plate="{{ $vehicle->registration_plate }}"
                                                    data-mileage="{{ $vehicle->current_mileage }}"
                                                    data-status="{{ $vehicle->status }}">
                                                {{ $vehicle->brand }} {{ $vehicle->model }} - {{ $vehicle->registration_plate }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-lucide-car class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                @error('vehicle_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div id="vehicleInfo" class="hidden">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Informations du véhicule</h4>
                                    <div id="vehicleDetails" class="space-y-2 text-sm text-gray-600">
                                        {{-- Rempli par JavaScript --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Période --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-900 flex items-center">
                            <x-lucide-calendar class="w-5 h-5 mr-3"/>
                            Période d'affectation
                        </h3>
                        <p class="text-sm text-primary-700 mt-1">Définissez la période pendant laquelle le véhicule sera assigné</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date et heure de début <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" id="start_datetime" name="start_datetime"
                                           value="{{ old('start_datetime') }}" required
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('start_datetime') border-red-300 @enderror">
                                    <x-lucide-clock class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                @error('start_datetime')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date et heure de fin
                                </label>
                                <div class="relative">
                                    <input type="datetime-local" id="end_datetime" name="end_datetime"
                                           value="{{ old('end_datetime') }}"
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('end_datetime') border-red-300 @enderror">
                                    <x-lucide-clock class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Laissez vide pour une affectation en cours</p>
                                @error('end_datetime')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Détails --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-primary-200">
                        <h3 class="text-lg font-semibold text-primary-900 flex items-center">
                            <x-lucide-file-text class="w-5 h-5 mr-3"/>
                            Détails de l'affectation
                        </h3>
                        <p class="text-sm text-primary-700 mt-1">Ajoutez des informations complémentaires sur l'affectation</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Motif de l'affectation
                                </label>
                                <div class="relative">
                                    <input type="text" id="reason" name="reason"
                                           value="{{ old('reason') }}"
                                           placeholder="Ex: Mission commerciale, déplacement professionnel..."
                                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('reason') border-red-300 @enderror">
                                    <x-lucide-tag class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                @error('reason')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes complémentaires
                                </label>
                                <div class="relative">
                                    <textarea id="notes" name="notes" rows="4"
                                              placeholder="Ajoutez des notes ou instructions particulières..."
                                              class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm resize-none @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                                    <x-lucide-message-square class="absolute left-3 top-3.5 h-4 w-4 text-gray-400"/>
                                </div>
                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <x-lucide-alert-circle class="w-4 h-4 mr-1"/>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('admin.assignments.index') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                            <x-lucide-x class="w-4 h-4 mr-2"/>
                            Annuler
                        </a>

                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                            <x-lucide-check class="w-4 h-4 mr-2"/>
                            Créer l'affectation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const driverSelect = document.getElementById('driver_id');
            const vehicleSelect = document.getElementById('vehicle_id');
            const driverInfo = document.getElementById('driverInfo');
            const vehicleInfo = document.getElementById('vehicleInfo');
            const driverDetails = document.getElementById('driverDetails');
            const vehicleDetails = document.getElementById('vehicleDetails');

            // Données des conducteurs (à passer depuis le contrôleur)
            const driversData = @json($drivers->keyBy('id'));

            // Gestion de la sélection du conducteur
            driverSelect.addEventListener('change', function() {
                const driverId = this.value;

                if (driverId && driversData[driverId]) {
                    const driver = driversData[driverId];

                    driverDetails.innerHTML = `
                        <div class="flex items-center space-x-3 mb-3">
                            ${driver.photo_path ?
                                `<img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200" src="/storage/${driver.photo_path}" alt="Photo">` :
                                `<div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-200">
                                    <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>`
                            }
                            <div>
                                <p class="font-medium text-gray-900">${driver.first_name} ${driver.last_name}</p>
                                <p class="text-sm text-gray-500">${driver.personal_phone || 'Téléphone non renseigné'}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p><span class="font-medium">Email:</span> ${driver.email || 'Non renseigné'}</p>
                            <p><span class="font-medium">Permis:</span> ${driver.license_number || 'Non renseigné'}</p>
                            <p><span class="font-medium">Date d'embauche:</span> ${driver.hire_date ? new Date(driver.hire_date).toLocaleDateString('fr-FR') : 'Non renseignée'}</p>
                        </div>
                    `;

                    driverInfo.classList.remove('hidden');
                } else {
                    driverInfo.classList.add('hidden');
                }
            });

            // Gestion de la sélection du véhicule
            vehicleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    const brand = selectedOption.dataset.brand;
                    const model = selectedOption.dataset.model;
                    const plate = selectedOption.dataset.plate;
                    const mileage = selectedOption.dataset.mileage;
                    const status = selectedOption.dataset.status;

                    vehicleDetails.innerHTML = `
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">${brand} ${model}</p>
                                <p class="text-sm text-gray-500">${plate}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p><span class="font-medium">Kilométrage:</span> ${parseInt(mileage).toLocaleString()} km</p>
                            <p><span class="font-medium">Statut:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${status === 'available' ? 'bg-success-100 text-success-800' : 'bg-warning-100 text-warning-800'}">
                                    ${status === 'available' ? 'Disponible' : 'En cours d\'utilisation'}
                                </span>
                            </p>
                        </div>
                    `;

                    vehicleInfo.classList.remove('hidden');
                } else {
                    vehicleInfo.classList.add('hidden');
                }
            });

            // Validation des dates
            const startDatetime = document.getElementById('start_datetime');
            const endDatetime = document.getElementById('end_datetime');

            startDatetime.addEventListener('change', function() {
                if (endDatetime.value && this.value >= endDatetime.value) {
                    endDatetime.value = '';
                }
                endDatetime.min = this.value;
            });

            endDatetime.addEventListener('change', function() {
                if (startDatetime.value && this.value <= startDatetime.value) {
                    alert('La date de fin doit être postérieure à la date de début');
                    this.value = '';
                }
            });

            // Définir la date/heure actuelle par défaut
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            startDatetime.value = now.toISOString().slice(0, 16);
        });
    </script>
    @endpush
</x-app-layout>