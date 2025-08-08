<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le Chauffeur : <span class="text-primary-600">{{ $driver->first_name }} {{ $driver->last_name }}</span></h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{
                    currentStep: {{ old('current_step', 1) }},
                    photoPreview: '{{ $driver->photo_path ? asset('storage/' . $driver->photo_path) : null }}',
                    init() {
                        const tomConfig = (selectedValue) => ({
                            create: false,
                            placeholder: 'Sélectionnez...',
                            items: [selectedValue]
                        });
                        new TomSelect(this.$refs.status_id, tomConfig('{{ old('status_id', $driver->status_id) }}'));
                        new TomSelect(this.$refs.user_id, tomConfig('{{ old('user_id', $driver->user_id) }}'));
                    },
                    updatePhotoPreview(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.photoPreview = URL.createObjectURL(file);
                        }
                    }
                }" x-init="
                    init();
                    @if ($errors->any())
                        let errors = {{ json_encode($errors->messages()) }};
                        let firstErrorStep = null;
                        const fieldToStepMap = { 'first_name': 1, 'last_name': 1, 'birth_date': 1, 'personal_phone': 1, 'address': 1, 'blood_type': 1, 'personal_email': 1, 'photo': 1, 'employee_number': 2, 'recruitment_date': 2, 'contract_end_date': 2, 'status_id': 2, 'user_id': 2, 'license_number': 3, 'license_category': 3, 'license_issue_date': 3, 'license_authority': 3, 'emergency_contact_name': 3, 'emergency_contact_phone': 3 };
                        for (const field in fieldToStepMap) {
                            if (errors.hasOwnProperty(field)) { firstErrorStep = fieldToStepMap[field]; break; }
                        }
                        if (firstErrorStep) { currentStep = firstErrorStep; }
                    @endif
                ">

                    <ol class="flex items-center w-full mb-8">
                        <li :class="currentStep >= 1 ? 'text-primary-600' : 'text-gray-500'" class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block" :class="currentStep > 1 ? 'after:border-primary-600' : 'after:border-gray-200'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep >= 1 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-heroicon-s-user-circle class="w-5 h-5"/>
                            </span>
                        </li>
                        <li :class="currentStep >= 2 ? 'text-primary-600' : 'text-gray-500'" class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block" :class="currentStep > 2 ? 'after:border-primary-600' : 'after:border-gray-200'">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep >= 2 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-heroicon-s-briefcase class="w-5 h-5"/>
                            </span>
                        </li>
                        <li :class="currentStep === 3 ? 'text-primary-600' : 'text-gray-500'" class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" :class="currentStep === 3 ? 'bg-primary-100' : 'bg-gray-100'">
                                <x-heroicon-s-identification class="w-5 h-5"/>
                            </span>
                        </li>
                    </ol>

                    <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="current_step" x-model="currentStep">

                        <fieldset x-show="currentStep === 1" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 1: Informations Personnelles</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="photo" value="Photo" />
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span x-show="!photoPreview" class="inline-block h-20 w-20 overflow-hidden rounded-full bg-gray-100">
                                            <x-heroicon-s-user class="h-full w-full text-gray-300"/>
                                        </span>
                                        <img x-show="photoPreview" :src="photoPreview" class="h-20 w-20 rounded-full object-cover">
                                        <input id="photo" name="photo" type="file" @change="updatePhotoPreview" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"/>
                                    </div>
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="first_name" value="Prénom" required />
                                    <x-text-input id="first_name" name="first_name" :value="old('first_name', $driver->first_name)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="last_name" value="Nom" required />
                                    <x-text-input id="last_name" name="last_name" :value="old('last_name', $driver->last_name)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="birth_date" value="Date de Naissance" />
                                    <x-text-input id="birth_date" type="date" name="birth_date" :value="old('birth_date', $driver->birth_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                                {{-- CHAMP CORRIGÉ --}}
                                <div>
                                    <x-input-label for="blood_type" value="Groupe Sanguin" />
                                    <x-text-input id="blood_type" name="blood_type" :value="old('blood_type', $driver->blood_type)" placeholder="Ex: O+" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('blood_type')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="personal_phone" value="Téléphone Personnel" />
                                    <x-text-input id="personal_phone" name="personal_phone" :value="old('personal_phone', $driver->personal_phone)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('personal_phone')" class="mt-2" />
                                </div>
                                {{-- CHAMP CORRIGÉ --}}
                                
                                <div>
                                    <label for="personal_email" class="block font-medium text-sm text-gray-700">Email Personnelle</label>
                                    <x-text-input id="personal_email" name="personal_email" type="email" :value="old('personal_email', $driver->personal_email)" class="mt-1 w-full" />
                                    <x-input-error :messages="$errors->get('personal_email')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="address" value="Adresse" />
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">{{ old('address', $driver->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset x-show="currentStep === 2" style="display: none;" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 2: Informations Professionnelles</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="employee_number" value="Matricule" />
                                    <x-text-input id="employee_number" name="employee_number" :value="old('employee_number', $driver->employee_number)" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('employee_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="status_id" value="Statut" required />
                                    <select x-ref="status_id" name="status_id" id="status_id">
                                        @foreach($driverStatuses as $status)<option value="{{ $status->id }}">{{ $status->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="recruitment_date" value="Date de Recrutement" />
                                    <x-text-input id="recruitment_date" type="date" name="recruitment_date" :value="old('recruitment_date', $driver->recruitment_date?->format('Y-m-d'))" class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('recruitment_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="contract_end_date" value="Date de Fin de Contrat" />
                                    <x-text-input id="contract_end_date" name="contract_end_date" :value="old('contract_end_date', $driver->contract_end_date?->format('Y-m-d'))" type="date" class="mt-1 block w-full"/>
                                    <x-input-error :messages="$errors->get('contract_end_date')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="user_id" value="Lier à un Compte Utilisateur (Optionnel)" />
                                    <select x-ref="user_id" name="user_id" id="user_id">
                                         <option value="">Ne pas lier de compte</option>
                                        @foreach($linkableUsers as $user)<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset x-show="currentStep === 3" style="display: none;" class="border border-gray-200 p-6 rounded-lg">
                             <legend class="text-lg font-semibold text-gray-800 px-2">Étape 3: Permis & Contact d'Urgence</legend>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="license_number" value="Numéro de Permis" />
                                    <x-text-input id="license_number" name="license_number" :value="old('license_number', $driver->license_number)" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="license_category" value="Catégorie(s)" />
                                    <x-text-input id="license_category" name="license_category" :value="old('license_category', $driver->license_category)" placeholder="Ex: B, C1E" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('license_category')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="license_issue_date" value="Date de Délivrance" />
                                    <x-text-input id="license_issue_date" name="license_issue_date" :value="old('license_issue_date', $driver->license_issue_date?->format('Y-m-d'))" type="date" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('license_issue_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="license_authority" value="Délivré par" />
                                    <x-text-input id="license_authority" name="license_authority" :value="old('license_authority', $driver->license_authority)" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('license_authority')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="emergency_contact_name" value="Nom du Contact d'Urgence" />
                                    <x-text-input id="emergency_contact_name" name="emergency_contact_name" :value="old('emergency_contact_name', $driver->emergency_contact_name)" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="emergency_contact_phone" value="Téléphone d'Urgence" />
                                    <x-text-input id="emergency_contact_phone" name="emergency_contact_phone" :value="old('emergency_contact_phone', $driver->emergency_contact_phone)" class="mt-1 w-full"/>
                                    <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
                                </div>
                            </div>
                        </fieldset>

                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-between">
                            <x-secondary-button type="button" x-show="currentStep > 1" @click="currentStep--">Précédent</x-secondary-button>
                            <div class="flex-grow"></div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.drivers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <x-primary-button type="button" x-show="currentStep < 3" @click="currentStep++">Suivant</x-primary-button>
                                <button type="submit" x-show="currentStep === 3" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>