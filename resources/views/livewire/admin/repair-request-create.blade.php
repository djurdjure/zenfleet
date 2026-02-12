@php
    $attachmentsHasError = $errors->has('attachments') || $errors->has('attachments.*');
@endphp

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12 lg:mx-0">
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                    <x-iconify icon="heroicons:wrench-screwdriver" class="w-6 h-6 text-blue-600" />
                    Nouvelle Demande de Réparation
                </h1>
                <p class="text-sm text-gray-600 ml-8.5">
                    Créez une demande avec un format harmonisé aux formulaires ZenFleet.
                </p>
            </div>

            <a href="{{ route('admin.repair-requests.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <x-iconify icon="heroicons:arrow-left" class="w-4 h-4" />
                Retour à la liste
            </a>
        </div>

        @if(session('success'))
            <x-alert type="success" title="Succès" dismissible class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" title="Erreur" dismissible class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        @error('form')
            <x-alert type="error" title="Erreur" dismissible class="mb-6">
                {{ $message }}
            </x-alert>
        @enderror

        <x-form-error-summary />

        <div wire:loading.flex wire:target="submit,vehicle_id,driver_id,attachments"
             class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm items-center justify-center z-50">
            <div class="bg-white p-4 rounded-lg shadow-xl flex items-center gap-3">
                <x-iconify icon="lucide:loader-2" class="w-6 h-6 text-blue-600 animate-spin" />
                <span class="text-gray-700 font-medium">Chargement...</span>
            </div>
        </div>

        <form wire:submit.prevent="submit" class="space-y-8">
            <x-form-section
                title="Informations du véhicule et du chauffeur"
                icon="heroicons:user-circle"
                subtitle="Sélection des entités concernées par la demande."
                eyebrow="Affectation">
                <x-field-group :columns="2">
                    <div class="space-y-2">
                        <x-select
                            name="vehicle_id"
                            label="Véhicule"
                            required
                            :error="$errors->first('vehicle_id')"
                            :disabled="$isDriverScoped"
                            wire:model.live="vehicle_id">
                            <option value="">Sélectionner un véhicule</option>
                            @forelse($vehicles as $vehicle)
                                <option value="{{ $vehicle['id'] }}" @selected((string) $vehicle_id === (string) $vehicle['id'])>
                                    {{ $vehicle['registration_plate'] }} - {{ $vehicle['brand'] ?? '' }} {{ $vehicle['model'] ?? '' }}
                                </option>
                            @empty
                                <option value="" disabled>Aucun véhicule disponible</option>
                            @endforelse
                        </x-select>

                        <p class="text-xs text-gray-600">
                            {{ count($vehicles) }} véhicule(s) disponible(s).
                        </p>

                        @if($isDriverScoped)
                            <p class="text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-md px-2.5 py-1.5">
                                Véhicule verrouillé selon votre affectation active.
                            </p>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <x-select
                            name="driver_id"
                            label="Chauffeur"
                            required
                            :error="$errors->first('driver_id')"
                            :disabled="$isDriverScoped"
                            wire:model.live="driver_id">
                            <option value="">Sélectionner un chauffeur</option>
                            @forelse($drivers as $driver)
                                <option value="{{ $driver['id'] }}" @selected((string) $driver_id === (string) $driver['id'])>
                                    {{ $driver['name'] }} - {{ $driver['license_number'] }}
                                </option>
                            @empty
                                <option value="" disabled>Aucun chauffeur disponible</option>
                            @endforelse
                        </x-select>

                        <p class="text-xs text-gray-600">
                            {{ count($drivers) }} chauffeur(s) disponible(s).
                        </p>

                        @if($isDriverScoped)
                            <p class="text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-md px-2.5 py-1.5">
                                Profil chauffeur verrouillé pour garantir l'isolement des données.
                            </p>
                        @endif
                    </div>
                </x-field-group>

                @if($isDriverScoped && empty($vehicles))
                    <x-alert type="warning" title="Affectation requise" class="mt-4">
                        Aucun véhicule actif ne vous est affecté. La création de demande est bloquée jusqu'à affectation.
                    </x-alert>
                @endif
            </x-form-section>

            <x-form-section
                title="Description de la demande"
                icon="heroicons:clipboard-document-list"
                subtitle="Nature du problème, priorité et classification."
                eyebrow="Diagnostic">
                <div class="space-y-6">
                    <x-input
                        name="title"
                        label="Titre de la demande"
                        icon="document-text"
                        placeholder="Ex: Problème de freinage avant droit"
                        required
                        :value="$title"
                        :error="$errors->first('title')"
                        wire:model.blur="title" />

                    <x-textarea
                        name="description"
                        label="Description détaillée"
                        placeholder="Décrivez en détail le problème rencontré..."
                        required
                        :rows="5"
                        :value="$description"
                        :error="$errors->first('description')"
                        wire:model.defer="description" />

                    <x-field-group :columns="2">
                        <x-select
                            name="category_id"
                            label="Catégorie"
                            :selected="$category_id"
                            :error="$errors->first('category_id')"
                            wire:model.live="category_id">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}" @selected((string) $category_id === (string) $category['id'])>
                                    {{ $category['name'] }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select
                            name="urgency"
                            label="Niveau d'urgence"
                            required
                            :selected="$urgency"
                            :error="$errors->first('urgency')"
                            wire:model.live="urgency"
                            :options="[
                                'low' => 'Faible',
                                'normal' => 'Normal',
                                'high' => 'Eleve',
                                'critical' => 'Critique',
                            ]" />
                    </x-field-group>
                </div>
            </x-form-section>

            <x-form-section
                title="Informations complémentaires"
                icon="heroicons:chart-bar"
                subtitle="Données kilométriques et coût prévisionnel."
                eyebrow="Contexte">
                <x-field-group :columns="2">
                    <div>
                        <x-input
                            type="number"
                            name="current_mileage"
                            label="Kilométrage actuel"
                            icon="chart-bar"
                            placeholder="Ex: 45000"
                            :value="$current_mileage"
                            :error="$errors->first('current_mileage')"
                            min="0"
                            wire:model.live="current_mileage" />
                        <p class="mt-2 text-xs text-gray-600">
                            Le kilométrage est chargé automatiquement après sélection du véhicule.
                        </p>
                    </div>

                    <x-input
                        type="number"
                        name="estimated_cost"
                        label="Coût estimé (DZD)"
                        icon="currency-dollar"
                        placeholder="Ex: 15000"
                        :value="$estimated_cost"
                        :error="$errors->first('estimated_cost')"
                        min="0"
                        step="0.01"
                        wire:model.live="estimated_cost" />
                </x-field-group>
            </x-form-section>

            <x-form-section
                title="Pièces jointes"
                icon="heroicons:paper-clip"
                subtitle="Ajoutez des preuves visuelles ou documents techniques."
                eyebrow="Documents">
                <div>
                    <label for="attachments" class="block mb-2 text-sm font-medium text-gray-900">
                        Photos ou documents
                    </label>

                    <div class="rounded-lg border-2 border-dashed px-6 py-8 text-center transition-colors
                                {{ $attachmentsHasError ? 'border-red-400 bg-red-50/60' : 'border-gray-300 bg-gray-50 hover:border-gray-400' }}">
                        <x-iconify icon="heroicons:cloud-arrow-up" class="w-10 h-10 mx-auto text-gray-400 mb-2" />
                        <p class="text-sm text-gray-700 mb-2">Cliquez pour sélectionner des fichiers.</p>
                        <p class="text-xs text-gray-500 mb-4">Formats acceptés: images, PDF, DOC, DOCX (10MB max/fichier)</p>

                        <input
                            id="attachments"
                            name="attachments"
                            wire:model="attachments"
                            type="file"
                            multiple
                            accept="image/*,.pdf,.doc,.docx"
                            class="block mx-auto text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    @if(!empty($attachments))
                        <p class="mt-2 text-xs text-blue-700">
                            {{ count($attachments) }} fichier(s) sélectionné(s).
                        </p>
                    @endif

                    @error('attachments')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </x-form-section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.repair-requests.index') }}"
                   class="inline-flex items-center px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Annuler
                </a>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-sm font-semibold text-white shadow-sm hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                    <x-iconify icon="heroicons:check" class="w-4 h-4" />
                    Créer la demande
                </button>
            </div>
        </form>
    </div>
</section>
