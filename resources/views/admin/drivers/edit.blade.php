<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le Chauffeur : <span class="text-violet-700">{{ $driver->first_name }} {{ $driver->last_name }}</span></h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900" x-data="{ currentStep: 1 }">
                                        {{-- Indicateur d'Étapes (Stepper) --}}
                    {{-- Indicateur d'étapes (Stepper) - Identique à la vue de création --}}
                    <div class="mb-8">
                        <ol class="flex items-center w-full">
                            <li class="flex w-full items-center text-violet-600 after:content-[''] after:w-full after:h-1 after:border-b after:border-violet-600 after:border-3 after:inline-block">
                                <span class="flex items-center justify-center w-10 h-10 bg-violet-100 rounded-full lg:h-12 lg:w-12 shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                            </li>
                            <li class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-3 after:inline-block" :class="{ 'text-violet-600 after:border-violet-600': currentStep >= 2 }">
                                <span class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-violet-100': currentStep >= 2, 'bg-gray-100': currentStep < 2 }">
                                    <span x-show="currentStep < 2">2</span>
                                    <svg x-show="currentStep >= 2" class="w-4 h-4 lg:w-6 lg:h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                            </li>
                            <li class="flex items-center" :class="{ 'text-violet-600': currentStep === 3 }">
                                <span class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0" :class="{ 'bg-violet-100': currentStep === 3, 'bg-gray-100': currentStep < 3 }">3</span>
                            </li>
                        </ol>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Veuillez corriger les erreurs ci-dessous :</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- Étape 1: Informations Personnelles --}}

                                                {{-- Étape 1: Informations Personnelles --}}
                        <section x-show="currentStep === 1" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-6">Étape 1: Informations Personnelles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                 {{-- Champ pour la photo avec affichage de l'actuelle --}}
                                <div class="md:col-span-2">
                                    <label for="photo" class="block font-medium text-sm text-gray-700">Photo</label>
                                    <div class="mt-2 flex items-center space-x-4">
                                        @if ($driver->photo_path)
                                            <img src="{{ asset('storage/' . $driver->photo_path) }}" alt="Photo de {{ $driver->first_name }}" class="h-20 w-20 rounded-full object-cover">
                                        @else
                                            <span class="inline-block h-20 w-20 overflow-hidden rounded-full bg-gray-100">
                                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 20.993V24H0v-2.997A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </span>
                                        @endif
                                        <input id="photo" name="photo" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                                    </div>
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="first_name" class="block font-medium text-sm text-gray-700">Prénom <span class="text-red-500">*</span></label>
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name',$driver->first_name)" required autofocus />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="last_name" class="block font-medium text-sm text-gray-700">Nom <span class="text-red-500">*</span></label>
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name',$driver->last_name)" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="birth_date" class="block font-medium text-sm text-gray-700">Date de Naissance</label>
                                    <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" :value="old('birth_date',$driver->birth_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="blood_type" class="block font-medium text-sm text-gray-700">Groupe Sanguin</label>
                                    <x-text-input id="blood_type" class="block mt-1 w-full" type="text" placeholder="Ex: O+" name="blood_type" :value="old('blood_type',$driver->blood_type)" />
                                    <x-input-error :messages="$errors->get('blood_type',$driver->blood_type)" class="mt-2" />
                                </div>
                                <div>
                                    <label for="personal_phone" class="block font-medium text-sm text-gray-700">Téléphone Personnel</label>
                                    <x-text-input id="personal_phone" class="block mt-1 w-full" type="text" name="personal_phone" :value="old('personal_phone',$driver->personal_phone)" />
                                    <x-input-error :messages="$errors->get('personal_phone')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="personal_email" class="block font-medium text-sm text-gray-700">Adresse Email</label>
                                    <x-text-input id="personal_email" class="block mt-1 w-full" type="email" name="personal_email" :value="old('personal_email',$driver->personal_email)" />
                                    <x-input-error :messages="$errors->get('personal_email')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="block font-medium text-sm text-gray-700">Adresse</label>
                                    <textarea id="address" name="address" rows="3" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" >{{ old('address',$driver->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                            </div>
                        </section>

                        {{-- ///////////////////////////////LA SUITE  --}}
                                                {{-- Étape 2: Informations Professionnelles --}}
                        <section x-show="currentStep === 2" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-6">Étape 2: Informations Professionnelles</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="employee_number" class="block font-medium text-sm text-gray-700">Matricule</label>
                                    <x-text-input id="employee_number" class="block mt-1 w-full" type="text" name="employee_number" :value="old('employee_number', $driver->employee_number)" />
                                    <x-input-error :messages="$errors->get('employee_number')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="recruitment_date" class="block font-medium text-sm text-gray-700">Date de Recrutement</label>
                                    <x-text-input id="recruitment_date" class="block mt-1 w-full" type="date" name="recruitment_date" :value="old('recruitment_date',$driver->recruitment_date?->format('Y-m-d'))" />
                                    <x-input-error :messages="$errors->get('recruitment_date',$driver->recruitment_date)" class="mt-2" />
                                </div>
                                <div>
                                    <label for="contract_end_date">Date de Fin de Contrat</label>
                                    <x-text-input id="contract_end_date" name="contract_end_date" :value="old('contract_end_date', $driver->contract_end_date?->format('Y-m-d'))" type="date" class="mt-1 w-full"/>
                                </div>
                                <div>
                                    <label for="status_id" class="block font-medium text-sm text-gray-700">Statut <span class="text-red-500">*</span></label>
                                    <select name="status_id" id="status_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm" required>
                                        <option value="">Sélectionnez un statut</option>
                                        @foreach($driverStatuses as $status)<option value="{{ $status->id }}" @selected(old('status_id',$driver->status_id) == $status->id)>{{ $status->name }}</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="user_id" class="block font-medium text-sm text-gray-700">Lier à un Compte Utilisateur (Optionnel)</label>
                                    <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-violet-500 focus:ring-violet-500 rounded-md shadow-sm">
                                        <option value="">Ne pas lier de compte</option>
                                        @foreach($linkableUsers as $user)<option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>@endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                                </div>
                            </div>
                        </section>

                                                {{-- Étape 3: Permis & Urgence --}}
                        <section x-show="currentStep === 3" style="display: none;" class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-6">Étape 3: Permis & Contact d'Urgence</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="license_number" class="block font-medium text-sm text-gray-700">Numéro de Permis</label>
                                    <x-text-input id="license_number" class="block mt-1 w-full" type="text" name="license_number" :value="old('license_number',$driver->license_number)" />
                                    <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="license_category" class="block font-medium text-sm text-gray-700">Catégorie(s) de Permis</label>
                                    <x-text-input id="license_category" class="block mt-1 w-full" type="text" name="license_category" :value="old('license_category',$driver->license_category)" placeholder="Ex: B, C1" />
                                    <x-input-error :messages="$errors->get('license_category')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="license_issue_date">Date de Délivrance</label>
                                    <x-text-input id="license_issue_date" name="license_issue_date" :value="old('license_issue_date', $driver->license_issue_date?->format('Y-m-d'))" type="date" class="mt-1 w-full"/>
                                </div>
                                <div>
                                    <label for="license_authority">Délivré par</label>
                                    <x-text-input id="license_authority" name="license_authority" :value="old('license_authority', $driver->license_authority)" class="mt-1 w-full"/>
                                </div>
                                {{-- Affichage de la date d'expiration calculée --}}
                                @if($driver->license_expiry_date)
                                <div class="md:col-span-2 mt-4 p-4 bg-violet-50 border border-violet-200 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700">Date d'Expiration du Permis (Calculée)</p>
                                    <p class="text-lg font-semibold text-violet-700">{{ $driver->license_expiry_date->format('d/m/Y') }}</p>
                                </div>
                                @endif



                                <div>
                                    <label for="emergency_contact_name" class="block font-medium text-sm text-gray-700">Nom du Contact d'Urgence</label>
                                    <x-text-input id="emergency_contact_name" class="block mt-1 w-full" type="text" name="emergency_contact_name" :value="old('emergency_contact_name',$driver->emergency_contact_name)" />
                                    <x-input-error :messages="$errors->get('emergency_contact_name')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="emergency_contact_phone" class="block font-medium text-sm text-gray-700">Téléphone d'Urgence</label>
                                    <x-text-input id="emergency_contact_phone" class="block mt-1 w-full" type="text" name="emergency_contact_phone" :value="old('emergency_contact_phone',$driver->emergency_contact_phone)" />
                                    <x-input-error :messages="$errors->get('emergency_contact_phone')" class="mt-2" />
                                </div>
                            </div>
                        </section>


                                             {{-- Boutons de Navigation --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
                            <div>
                                <button type="button" x-show="currentStep > 1" @click="currentStep--" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                    Précédent
                                </button>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.drivers.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Annuler</a>
                                <button type="button" x-show="currentStep < 3" @click="currentStep++" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700">
                                    Suivant
                                </button>
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
