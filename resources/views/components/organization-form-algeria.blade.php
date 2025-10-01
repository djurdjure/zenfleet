@props([
    'organization' => null,
    'isEdit' => false,
    'wilayas' => [],
    'organizationTypes' => []
])

<div class="zenfleet-container">
    <form method="POST" action="{{ $isEdit && $organization ? route('admin.organizations.update', $organization) : route('admin.organizations.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <!-- Header -->
        <div class="zenfleet-card">
            <div class="px-8 py-6 border-b border-gray-200/60">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 zenfleet-fade-in">
                            {{ $isEdit ? 'Modifier l\'Organisation' : 'Nouvelle Organisation' }}
                        </h1>
                        <p class="mt-2 text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>
                            Informations pour l'enregistrement en Algérie
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-10">
                <!-- Informations Générales -->
                <div class="zenfleet-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Informations Générales</h2>
                    </div>
                    <div class="zenfleet-grid grid-cols-1 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-algeria-form-field
                                name="name"
                                label="Nom de l'Organisation"
                                :value="old('name', $organization?->name ?? '')"
                                required="true"
                                icon="fa-building"
                                placeholder="Entrez le nom de l'organisation"
                            />
                        </div>

                        <x-algeria-form-field
                            name="legal_name"
                            label="Raison Sociale"
                            :value="old('legal_name', $organization?->legal_name ?? '')"
                            icon="fa-certificate"
                            placeholder="Raison sociale officielle"
                        />

                        <x-algeria-form-field
                            name="organization_type"
                            label="Type d'Organisation"
                            type="select"
                            :value="old('organization_type', $organization?->organization_type ?? '')"
                            :options="$organizationTypes"
                            required="true"
                            icon="fa-tags"
                            placeholder="Sélectionner le type..."
                        />

                        <x-algeria-form-field
                            name="industry"
                            label="Secteur d'Activité"
                            :value="old('industry', $organization?->industry ?? '')"
                            icon="fa-industry"
                            placeholder="Ex: Transport, Logistique..."
                        />

                        <x-algeria-form-field
                            name="email"
                            label="Email Principal"
                            type="email"
                            :value="old('email', $organization?->email ?? '')"
                            required="true"
                            icon="fa-envelope"
                            placeholder="contact@entreprise.dz"
                        />

                        <x-algeria-form-field
                            name="phone_number"
                            label="Téléphone Principal"
                            type="tel"
                            :value="old('phone_number', $organization?->phone_number ?? '')"
                            required="true"
                            icon="fa-phone"
                            placeholder="+213 XX XX XX XX XX"
                            help="Format: +213 suivi de 8 ou 9 chiffres"
                        />

                        <x-algeria-form-field
                            name="website"
                            label="Site Web"
                            type="url"
                            :value="old('website', $organization?->website ?? '')"
                            icon="fa-globe"
                            placeholder="https://www.entreprise.dz"
                        />

                        <x-algeria-form-field
                            name="status"
                            label="Statut"
                            type="select"
                            :value="old('status', $organization?->status ?? 'active')"
                            :options="['active' => 'Actif', 'inactive' => 'Inactif', 'suspended' => 'Suspendu']"
                            icon="fa-toggle-on"
                        />

                        <div class="md:col-span-2">
                            <x-algeria-form-field
                                name="description"
                                label="Description de l'Activité"
                                type="textarea"
                                :value="old('description', $organization?->description ?? '')"
                                icon="fa-align-left"
                                placeholder="Décrivez brièvement l'activité de l'organisation..."
                                :rows="4"
                            />
                        </div>
                    </div>
                </div>

                <!-- Informations Légales -->
                <div class="zenfleet-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-gavel text-green-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Informations Légales Algérie</h2>
                    </div>
                    <div class="zenfleet-grid grid-cols-1 md:grid-cols-2">
                        <x-algeria-form-field
                            name="trade_register"
                            label="Registre de Commerce"
                            :value="old('trade_register', $organization?->trade_register ?? '')"
                            required="true"
                            icon="fa-file-contract"
                            placeholder="Ex: 16/00-1234567B23"
                            help="Numéro du registre de commerce"
                        />

                        <x-algeria-form-field
                            name="nif"
                            label="NIF (Numéro d'Identification Fiscale)"
                            :value="old('nif', $organization?->nif ?? '')"
                            required="true"
                            icon="fa-hashtag"
                            placeholder="123456789012345"
                            help="15 chiffres - Identifiant fiscal unique"
                        />

                        <x-algeria-form-field
                            name="ai"
                            label="AI (Article d'Imposition)"
                            :value="old('ai', $organization?->ai ?? '')"
                            icon="fa-receipt"
                            placeholder="Ex: 16001234"
                            help="Numéro d'article d'imposition"
                        />

                        <x-algeria-form-field
                            name="nis"
                            label="NIS (Numéro d'Identification Statistique)"
                            :value="old('nis', $organization?->nis ?? '')"
                            icon="fa-chart-bar"
                            placeholder="Ex: 12345678901234"
                            help="Identifiant statistique ONS"
                        />
                    </div>
                </div>

                <!-- Adresse -->
                <div class="zenfleet-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-map-marker-alt text-purple-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Adresse du Siège Social</h2>
                    </div>
                    <div class="zenfleet-grid grid-cols-1 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-algeria-form-field
                                name="address"
                                label="Adresse Complète"
                                :value="old('address', $organization?->address ?? '')"
                                required="true"
                                icon="fa-home"
                                placeholder="Ex: Rue Mohamed Khemisti, Bt A, Appt 12"
                                help="Adresse complète du siège social"
                            />
                        </div>

                        <x-algeria-form-field
                            name="wilaya"
                            label="Wilaya"
                            type="select"
                            :value="old('wilaya', $organization?->wilaya ?? '')"
                            :options="$wilayas"
                            required="true"
                            icon="fa-map"
                            placeholder="Sélectionner une wilaya..."
                            help="Division administrative d'Algérie"
                        />

                        <x-algeria-form-field
                            name="city"
                            label="Commune"
                            :value="old('city', $organization?->city ?? '')"
                            required="true"
                            icon="fa-building"
                            placeholder="Ex: Alger Centre"
                            help="Commune de la wilaya"
                        />

                        <x-algeria-form-field
                            name="commune"
                            label="Précision Commune"
                            :value="old('commune', $organization?->commune ?? '')"
                            icon="fa-map-pin"
                            placeholder="Ex: Quartier des Affaires"
                            help="Localisation précise dans la commune"
                        />

                        <x-algeria-form-field
                            name="zip_code"
                            label="Code Postal"
                            :value="old('zip_code', $organization?->zip_code ?? '')"
                            icon="fa-mailbox"
                            placeholder="16000"
                            help="Code postal algérien (5 chiffres)"
                        />
                    </div>
                </div>

                <!-- Représentant Légal -->
                <div class="zenfleet-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <i class="fas fa-user-tie text-amber-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Représentant Légal</h2>
                    </div>
                    <div class="zenfleet-grid grid-cols-1 md:grid-cols-2">
                        <x-algeria-form-field
                            name="manager_first_name"
                            label="Prénom du Représentant"
                            :value="old('manager_first_name', $organization?->manager_first_name ?? '')"
                            required="true"
                            icon="fa-user"
                            placeholder="Ex: Mohamed"
                        />

                        <x-algeria-form-field
                            name="manager_last_name"
                            label="Nom du Représentant"
                            :value="old('manager_last_name', $organization?->manager_last_name ?? '')"
                            required="true"
                            icon="fa-user"
                            placeholder="Ex: Benali"
                        />

                        <x-algeria-form-field
                            name="manager_nin"
                            label="NIN (Numéro d'Identification Nationale)"
                            :value="old('manager_nin', $organization?->manager_nin ?? '')"
                            required="true"
                            icon="fa-id-card"
                            placeholder="123456789012345678"
                            help="18 chiffres - Carte d'identité nationale"
                        />

                        <x-algeria-form-field
                            name="manager_phone_number"
                            label="Téléphone du Représentant"
                            type="tel"
                            :value="old('manager_phone_number', $organization?->manager_phone_number ?? '')"
                            icon="fa-phone"
                            placeholder="+213 XX XX XX XX XX"
                        />

                        <x-algeria-form-field
                            name="manager_dob"
                            label="Date de Naissance"
                            type="date"
                            :value="old('manager_dob', $organization?->manager_dob?->format('Y-m-d') ?? '')"
                            icon="fa-calendar"
                        />

                        <x-algeria-form-field
                            name="manager_pob"
                            label="Lieu de Naissance"
                            :value="old('manager_pob', $organization?->manager_pob ?? '')"
                            icon="fa-map-marker"
                            placeholder="Ex: Alger"
                        />

                        <div class="md:col-span-2">
                            <x-algeria-form-field
                                name="manager_address"
                                label="Adresse du Représentant Légal"
                                :value="old('manager_address', $organization?->manager_address ?? '')"
                                icon="fa-home"
                                placeholder="Adresse personnelle du représentant légal"
                            />
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="zenfleet-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-100 rounded-lg">
                            <i class="fas fa-file-upload text-indigo-600 text-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Documents Justificatifs</h2>
                    </div>
                    <div class="zenfleet-grid grid-cols-1 md:grid-cols-2">
                        <x-algeria-form-field
                            name="scan_nif"
                            label="Scan du NIF"
                            type="file"
                            icon="fa-file-pdf"
                            accept=".pdf,.jpg,.jpeg,.png"
                            accept-text="PDF, JPG, PNG jusqu'à 5MB"
                            help="Document NIF au format PDF ou image"
                        />

                        <x-algeria-form-field
                            name="scan_ai"
                            label="Scan de l'AI"
                            type="file"
                            icon="fa-file-pdf"
                            accept=".pdf,.jpg,.jpeg,.png"
                            accept-text="PDF, JPG, PNG jusqu'à 5MB"
                            help="Document AI au format PDF ou image"
                        />

                        <x-algeria-form-field
                            name="scan_nis"
                            label="Scan du NIS"
                            type="file"
                            icon="fa-file-pdf"
                            accept=".pdf,.jpg,.jpeg,.png"
                            accept-text="PDF, JPG, PNG jusqu'à 5MB"
                            help="Document NIS au format PDF ou image"
                        />

                        <x-algeria-form-field
                            name="manager_id_scan"
                            label="Pièce d'Identité du Représentant"
                            type="file"
                            icon="fa-id-card"
                            accept=".pdf,.jpg,.jpeg,.png"
                            accept-text="PDF, JPG, PNG jusqu'à 5MB"
                            help="Carte d'identité ou passeport"
                        />

                        <div class="md:col-span-2">
                            <x-algeria-form-field
                                name="logo"
                                label="Logo de l'Organisation"
                                type="file"
                                icon="fa-image"
                                accept=".jpg,.jpeg,.png,.svg"
                                accept-text="JPG, PNG, SVG jusqu'à 2MB"
                                help="Logo officiel de l'organisation (optionnel)"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200/60 flex items-center justify-between">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span>Tous les champs marqués d'un * sont obligatoires</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.organizations.index') }}"
                       class="zenfleet-btn-secondary zenfleet-fade-in">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Annuler
                    </a>
                    <button type="submit"
                            class="zenfleet-btn-primary zenfleet-fade-in">
                        <i class="fas {{ $isEdit ? 'fa-save' : 'fa-plus' }} mr-2"></i>
                        {{ $isEdit ? 'Mettre à jour' : 'Créer l\'organisation' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>