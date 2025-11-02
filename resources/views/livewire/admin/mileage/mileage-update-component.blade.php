{{-- ====================================================================
    🚀 MILEAGE UPDATE PAGE - ENTERPRISE ULTRA-PRO V2
    ====================================================================
    
    Design System ZenFleet Compliant:
    - Structure section > container > cards
    - Icônes Heroicons
    - Composants <x-button>
    - Couleurs et espacements standardisés
    
    @version 2.0-Enterprise-ZenFleet-Compliant
    @since 2025-11-02
    ==================================================================== --}}

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">
        
        {{-- ====================================================================
            EN-TÊTE DE LA PAGE - STYLE ZENFLEET
        ==================================================================== --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                        <x-iconify icon="heroicons:chart-bar" class="w-6 h-6 text-blue-600" />
                        Mise à Jour du Kilométrage
                    </h1>
                    <p class="text-sm text-gray-600 ml-8.5">
                        Enregistrez le nouveau kilométrage d'un véhicule de manière simple et rapide
                    </p>
                </div>
                <x-button 
                    href="{{ route('admin.mileage-readings.index') }}" 
                    variant="secondary" 
                    icon="list-bullet"
                    size="sm">
                    Voir l'historique
                </x-button>
            </div>
        </div>

        {{-- ====================================================================
            MESSAGES FLASH - STYLE ZENFLEET ALERT COMPONENT
        ==================================================================== --}}
        @if (session('success'))
            <x-alert type="success" title="Succès" dismissible class="mb-6">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert type="error" title="Erreur" dismissible class="mb-6">
                {{ session('error') }}
            </x-alert>
        @endif

        {{-- ====================================================================
            LAYOUT PRINCIPAL - 2 COLONNES
        ==================================================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLONNE PRINCIPALE - FORMULAIRE --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    
                    {{-- En-tête de la carte - STYLE ZENFLEET --}}
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:pencil-square" class="w-5 h-5 text-blue-600" />
                            Enregistrer un Relevé Kilométrique
                        </h2>
                    </div>

                    {{-- Corps du formulaire --}}
                    <form wire:submit.prevent="save" class="p-6 space-y-6">
                        
                        {{-- 1. SÉLECTION DU VÉHICULE --}}
                        <div>
                            <x-tom-select
                                name="vehicle_id"
                                wire:model.live="vehicle_id"
                                label="Véhicule"
                                placeholder="Rechercher un véhicule (Immatriculation ou Modèle)..."
                                :error="$errors->first('vehicle_id')"
                                required
                            >
                                <option value="">-- Sélectionner un véhicule --</option>
                                @foreach($availableVehicles as $vehicle)
                                    <option value="{{ $vehicle['id'] }}">
                                        {{ $vehicle['label'] }}
                                    </option>
                                @endforeach
                            </x-tom-select>
                        </div>

                        {{-- INFORMATIONS DU VÉHICULE SÉLECTIONNÉ --}}
                        @if($vehicleData)
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <x-iconify icon="heroicons:truck" class="w-6 h-6 text-blue-600" />
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-blue-900">
                                                {{ $vehicleData['registration_plate'] }}
                                            </span>
                                            <span class="text-sm text-blue-700">
                                                {{ $vehicleData['brand'] }} {{ $vehicleData['model'] }}
                                            </span>
                                            @if($vehicleData['manufacturing_year'])
                                                <span class="px-2 py-0.5 bg-blue-200 text-blue-800 text-xs rounded-full">
                                                    {{ $vehicleData['manufacturing_year'] }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                            @if($vehicleData['category_name'])
                                                <div>
                                                    <span class="text-blue-600 font-medium">Catégorie:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['category_name'] }}</span>
                                                </div>
                                            @endif
                                            @if($vehicleData['fuel_type'])
                                                <div>
                                                    <span class="text-blue-600 font-medium">Carburant:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['fuel_type'] }}</span>
                                                </div>
                                            @endif
                                            @if($vehicleData['depot_name'])
                                                <div class="col-span-2">
                                                    <span class="text-blue-600 font-medium">Dépôt:</span>
                                                    <span class="text-blue-900">{{ $vehicleData['depot_name'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="pt-2 border-t border-blue-200">
                                            <div class="flex items-center gap-2">
                                                <x-iconify icon="heroicons:chart-bar" class="w-5 h-5 text-blue-600" />
                                                <span class="text-sm text-blue-700 font-medium">Kilométrage actuel:</span>
                                                <span class="text-lg font-bold text-blue-900">
                                                    {{ number_format($vehicleData['current_mileage'], 0, ',', ' ') }} km
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 2. DATE ET HEURE DE LA LECTURE --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-datepicker
                                    name="date"
                                    wire:model.live="date"
                                    label="Date de la lecture"
                                    :maxDate="date('Y-m-d')"
                                    :minDate="date('Y-m-d', strtotime('-30 days'))"
                                    :error="$errors->first('date')"
                                    required
                                />
                            </div>
                            <div>
                                <x-time-picker
                                    name="time"
                                    wire:model.live="time"
                                    label="Heure de la lecture"
                                    :error="$errors->first('time')"
                                    required
                                />
                            </div>
                        </div>

                        {{-- 3. NOUVEAU KILOMÉTRAGE --}}
                        <div>
                            <x-input
                                type="number"
                                name="mileage"
                                wire:model.live="mileage"
                                label="Nouveau kilométrage (km)"
                                placeholder="Ex: 125000"
                                :error="$errors->first('mileage')"
                                icon="chart-bar"
                                required
                                min="0"
                                step="1"
                            />
                            
                            {{-- Message de validation en temps réel --}}
                            @if($validationMessage && $vehicleData)
                                <div class="mt-2 p-3 rounded-lg {{ 
                                    $validationType === 'success' ? 'bg-green-50 border border-green-200' : 
                                    ($validationType === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 
                                    'bg-red-50 border border-red-200')
                                }}">
                                    <p class="text-sm font-medium {{ 
                                        $validationType === 'success' ? 'text-green-800' : 
                                        ($validationType === 'warning' ? 'text-yellow-800' : 
                                        'text-red-800')
                                    }}">
                                        {{ $validationMessage }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- 4. NOTES OPTIONNELLES --}}
                        <div>
                            <x-textarea
                                name="notes"
                                wire:model="notes"
                                label="Notes (optionnel)"
                                placeholder="Commentaires ou observations particulières..."
                                :error="$errors->first('notes')"
                                rows="3"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                Maximum 500 caractères
                            </p>
                        </div>

                        {{-- BOUTONS D'ACTION - STYLE ZENFLEET --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <x-button
                                type="button"
                                wire:click="resetForm"
                                variant="secondary"
                                icon="arrow-path"
                                size="md">
                                Réinitialiser
                            </x-button>

                            <x-button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="save"
                                variant="primary"
                                icon="check"
                                size="md">
                                <span wire:loading.remove wire:target="save">Enregistrer la Lecture</span>
                                <span wire:loading wire:target="save">Enregistrement...</span>
                            </x-button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- COLONNE LATÉRALE - INFORMATIONS --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- STATISTIQUES DU VÉHICULE --}}
                @if($vehicleData && $vehicleStats)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:chart-bar" class="w-4 h-4 text-gray-600" />
                                Statistiques
                            </h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Moyenne journalière</span>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($vehicleStats['daily_average'], 0, ',', ' ') }} km/jour
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Moyenne mensuelle</span>
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($vehicleStats['monthly_average'], 0, ',', ' ') }} km/mois
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Ce mois-ci</span>
                                <span class="text-sm font-bold text-blue-600">
                                    {{ number_format($vehicleStats['km_this_month'], 0, ',', ' ') }} km
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600">Dernier relevé</span>
                                <span class="text-xs text-gray-500">
                                    {{ $vehicleStats['last_reading_date'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- HISTORIQUE RÉCENT --}}
                @if($vehicleData && $recentReadings->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <x-iconify icon="heroicons:clock" class="w-4 h-4 text-gray-600" />
                                Historique Récent
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($recentReadings as $reading)
                                <div class="p-3 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ number_format($reading->mileage, 0, ',', ' ') }} km
                                                </span>
                                                @if($reading->recording_method === 'manual')
                                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">
                                                        Manuel
                                                    </span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 text-xs rounded">
                                                        Auto
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $reading->recorded_at->format('d/m/Y à H:i') }}
                                            </p>
                                            @if($reading->recordedBy)
                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    Par {{ $reading->recordedBy->name }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($reading->notes)
                                        <p class="mt-2 text-xs text-gray-600 italic">
                                            "{{ Str::limit($reading->notes, 50) }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- AIDE / INSTRUCTIONS --}}
                <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 overflow-hidden">
                    <div class="px-4 py-3 bg-blue-100 border-b border-blue-200">
                        <h3 class="text-sm font-semibold text-blue-900 flex items-center gap-2">
                            <x-iconify icon="heroicons:information-circle" class="w-4 h-4" />
                            Instructions
                        </h3>
                    </div>
                    <div class="p-4 space-y-2 text-sm text-blue-900">
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>Le kilométrage doit être <strong>supérieur</strong> au dernier relevé</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>La date ne peut pas dépasser <strong>30 jours dans le passé</strong></p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:check-circle" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" />
                            <p>Les notes sont optionnelles mais recommandées pour les relevés inhabituels</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <x-iconify icon="heroicons:exclamation-triangle" class="w-4 h-4 text-orange-600 mt-0.5 flex-shrink-0" />
                            <p>Une augmentation de <strong>+10 000 km</strong> déclenchera un avertissement</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
