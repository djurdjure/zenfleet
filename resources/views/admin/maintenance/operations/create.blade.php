{{-- ====================================================================
🔧 FORMULAIRE CRÉATION MAINTENANCE - ENTERPRISE GRADE V6 ULTRA-OPTIMISÉ
====================================================================

Version 6.0 - Optimisations enterprise-grade:
✅ ZenFleetSelect (SlimSelect wrapper) avec initialisation robuste
✅ Retry mechanism pour éviter conflits de chargement
✅ Double initialization prevention
✅ Auto-complétion intelligente (kilométrage, coût, durée)
✅ Gestion erreurs élégante avec notifications
✅ Logging détaillé pour debug
✅ Performance optimale
✅ Compatible avec architecture Vite + Alpine.js
✅ Rendu professionnel surpassant Fleetio/Samsara

Nouveautés V6:
🚀 Initialisation avec retry mechanism (3 tentatives)
🚀 Prévention double initialisation
🚀 Notifications d'erreur intégrées
🚀 Logging console structuré
🚀 Recherche conditionnelle (>5 éléments)

@version 6.0-Enterprise-Ultra-Optimized
@since 2025-11-23
@author ZenFleet Architecture Team
==================================================================== --}}

@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Opération de Maintenance')

@section('content')
<section class="bg-gray-50 min-h-screen">
    <div class="py-4 px-4 mx-auto max-w-4xl lg:py-6">

        {{-- HEADER --}}
        <div class="mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.maintenance.operations.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                            <x-iconify icon="lucide:wrench" class="w-4 h-4 mr-2 inline" />
                            Maintenance
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <span class="text-gray-900 font-medium">Nouvelle Opération</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2.5">
                <x-iconify icon="lucide:plus-circle" class="w-6 h-6 text-blue-600" />
                Nouvelle Opération de Maintenance
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Planifiez une intervention avec recherche intelligente et auto-complétion
            </p>
        </div>

        {{-- Erreurs --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes :
                <ul class="mt-2 ml-5 list-disc text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- Debug Info (dev only) --}}
        @if(config('app.debug'))
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs">
                <strong>📊 Debug Info:</strong>
                Véhicules: {{ $vehicles->count() }} |
                Types: {{ $maintenanceTypes->count() }} |
                Fournisseurs: {{ $providers->count() }}
            </div>
        @endif

        {{-- FORMULAIRE --}}
        <div x-data="maintenanceFormData()" x-init="init()">
            <form action="{{ route('admin.maintenance.operations.store') }}" method="POST" @submit="onSubmit" class="space-y-6">
                @csrf

                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                        <x-iconify icon="lucide:settings" class="w-5 h-5 text-blue-600" />
                        Informations de l'Opération
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- VÉHICULE - SLIMSELECT OPTIMISÉ --}}
                        <div class="md:col-span-2">
                            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:car" class="w-4 h-4 inline mr-1" />
                                Véhicule <span class="text-red-500">*</span>
                            </label>
                            <div x-ref="vehicleWrapper">
                                <select name="vehicle_id"
                                        id="vehicle_id"
                                        required
                                        x-ref="vehicleSelect"
                                        @change="onVehicleChange($event.target.value)"
                                        class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition-colors @error('vehicle_id') border-red-300 @enderror">
                                    <option value="">-- Sélectionner un véhicule --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}"
                                                data-mileage="{{ $vehicle->current_mileage }}"
                                                data-brand="{{ $vehicle->brand }}"
                                                data-model="{{ $vehicle->model }}"
                                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->display_text }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vehicle_id')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            @if($vehicles->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">⚠️ Aucun véhicule disponible. <a href="{{ route('admin.vehicles.create') }}" class="underline">Ajouter un véhicule</a></p>
                            @endif
                        </div>

                        {{-- TYPE DE MAINTENANCE --}}
                        <div class="md:col-span-2">
                            <label for="maintenance_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:tag" class="w-4 h-4 inline mr-1" />
                                Type de Maintenance <span class="text-red-500">*</span>
                            </label>
                            <select name="maintenance_type_id"
                                    id="maintenance_type_id"
                                    required
                                    @change="onTypeChange($event.target.value)"
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 transition-colors @error('maintenance_type_id') border-red-300 @enderror">
                                <option value="">-- Sélectionner un type --</option>
                                @foreach($maintenanceTypes as $type)
                                    <option value="{{ $type->id }}"
                                            data-category="{{ $type->category }}"
                                            data-duration-hours="{{ $type->estimated_duration_hours ?? '' }}"
                                            data-duration-minutes="{{ $type->estimated_duration_minutes ?? '' }}"
                                            data-cost="{{ $type->estimated_cost ?? '' }}"
                                            data-description="{{ $type->description ?? '' }}"
                                            {{ old('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->display_text }}
                                    </option>
                                @endforeach
                            </select>
                            @error('maintenance_type_id')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <x-iconify icon="lucide:alert-circle" class="w-4 h-4" />
                                    {{ $message }}
                                </p>
                            @enderror
                            <p x-show="selectedType.description"
                               x-text="selectedType.description"
                               class="mt-2 text-xs text-gray-600 italic"></p>
                            @if($maintenanceTypes->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">⚠️ Aucun type de maintenance disponible.</p>
                            @endif
                        </div>

                        {{-- DATE --}}
                        <div>
                            <x-datepicker
                                name="scheduled_date"
                                label="Date Planifiée"
                                :value="old('scheduled_date', now()->format('d/m/Y'))"
                                :minDate="now()->format('d/m/Y')"
                                required
                                :error="$errors->first('scheduled_date')"
                                helpText="Date de l'intervention"
                            />
                        </div>

                        {{-- STATUT --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:info" class="w-4 h-4 inline mr-1" />
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                <option value="planned" selected>📅 Planifiée</option>
                                <option value="in_progress">🔧 En cours</option>
                            </select>
                        </div>

                        {{-- FOURNISSEUR - SLIMSELECT OPTIMISÉ --}}
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <label for="provider_id" class="block text-sm font-medium text-gray-700">
                                    <x-iconify icon="lucide:building" class="w-4 h-4 inline mr-1" />
                                    Fournisseur
                                </label>
                                <a href="{{ route('admin.suppliers.create') }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                    <x-iconify icon="lucide:plus-circle" class="w-3.5 h-3.5" />
                                    Ajouter un fournisseur
                                </a>
                            </div>
                            <div x-ref="providerWrapper">
                                <select name="provider_id"
                                        id="provider_id"
                                        x-ref="providerSelect"
                                        class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                                    <option value="">Aucun fournisseur (maintenance interne)</option>
                                    @foreach($providers as $provider)
                                        <option value="{{ $provider->id }}"
                                                data-type="{{ $provider->supplier_type ?? '' }}"
                                                data-rating="{{ $provider->rating ?? '' }}"
                                                {{ old('provider_id') == $provider->id ? 'selected' : '' }}>
                                            {{ $provider->display_text }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Sélectionnez un prestataire externe ou laissez vide pour maintenance interne
                            </p>
                            @if($providers->isEmpty())
                                <p class="mt-1 text-sm text-yellow-600">⚠️ Aucun fournisseur disponible. <a href="{{ route('admin.suppliers.create') }}" target="_blank" class="underline">Ajouter un fournisseur</a></p>
                            @endif
                        </div>

                        {{-- KILOMÉTRAGE --}}
                        <div>
                            <label for="mileage_at_maintenance" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:gauge" class="w-4 h-4 inline mr-1" />
                                Kilométrage Actuel (km)
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="mileage_at_maintenance"
                                       id="mileage_at_maintenance"
                                       x-model="currentMileage"
                                       min="0"
                                       step="1"
                                       placeholder="Ex: 50000"
                                       class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 pr-10 @error('mileage_at_maintenance') border-red-300 @enderror">
                                <div x-show="currentMileage > 0" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <x-iconify icon="lucide:check-circle" class="w-4 h-4 text-green-500" />
                                </div>
                            </div>
                            @error('mileage_at_maintenance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-blue-600" x-show="currentMileage > 0 && autoFilledMileage">
                                    <x-iconify icon="lucide:zap" class="w-3 h-3 inline" />
                                    Auto-rempli depuis le véhicule
                                </p>
                            @enderror
                        </div>

                        {{-- COÛT --}}
                        <div>
                            <label for="total_cost" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:banknote" class="w-4 h-4 inline mr-1" />
                                Coût Estimé (DA)
                            </label>
                            <input type="number"
                                   name="total_cost"
                                   id="total_cost"
                                   x-model="estimatedCost"
                                   min="0"
                                   step="0.01"
                                   placeholder="Ex: 15000"
                                   class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 @error('total_cost') border-red-300 @enderror">
                            @error('total_cost')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500" x-show="estimatedCost > 0 && autoFilledCost">
                                    <x-iconify icon="lucide:zap" class="w-3 h-3 inline" />
                                    Auto-rempli depuis le type
                                </p>
                            @enderror
                        </div>

                        {{-- DURÉE --}}
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:clock" class="w-4 h-4 inline mr-1" />
                                Durée Estimée (heures)
                            </label>
                            <div class="relative">
                                <input type="number"
                                       id="duration_hours"
                                       x-model="durationHours"
                                       @input="updateDurationMinutes"
                                       min="0.1"
                                       max="100"
                                       step="0.5"
                                       placeholder="Ex: 2.5"
                                       class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 pr-20">
                                <input type="hidden" name="duration_minutes" :value="durationMinutes">
                                <div x-show="durationHours > 0" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-xs text-gray-500 font-medium" x-text="durationMinutes + ' min'"></span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" x-show="durationHours > 0 && autoFilledDuration">
                                <x-iconify icon="lucide:zap" class="w-3 h-3 inline" />
                                Auto-rempli depuis le type
                            </p>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:file-text" class="w-4 h-4 inline mr-1" />
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      placeholder="Décrivez les travaux à effectuer..."
                                      class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500">Maximum 1000 caractères</p>
                            @enderror
                        </div>

                        {{-- NOTES --}}
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                <x-iconify icon="lucide:message-square" class="w-4 h-4 inline mr-1" />
                                Notes Internes
                            </label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="2"
                                      placeholder="Notes additionnelles..."
                                      class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-2.5 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <a href="{{ route('admin.maintenance.operations.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                            Annuler
                        </a>

                        <div class="flex items-center gap-3">
                            <button type="submit" name="action" value="save_and_continue"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-blue-300 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-50 transition-colors">
                                <x-iconify icon="lucide:save" class="w-4 h-4" />
                                Enregistrer et Créer Autre
                            </button>

                            <button type="submit" name="action" value="save"
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <x-iconify icon="lucide:check" class="w-4 h-4" />
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- AIDE --}}
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <x-iconify icon="lucide:sparkles" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">✨ Fonctionnalités Intelligentes</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• <strong>Recherche rapide</strong> : Tapez pour filtrer les véhicules et fournisseurs</li>
                            <li>• <strong>Auto-complétion</strong> : Le kilométrage, coût et durée se remplissent automatiquement</li>
                            <li>• <strong>Durée en heures</strong> : Convertie automatiquement en minutes pour le système</li>
                            <li>• <strong>Ajout rapide</strong> : Créez un fournisseur sans quitter cette page</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- SCRIPTS --}}
@push('scripts')
<script>
/**
 * 🎯 FORMULAIRE MAINTENANCE - ALPINE.JS + ZENFLEET SELECT V6
 * Version Enterprise-Grade Ultra-Optimisée
 *
 * @version 6.0-Enterprise-Optimized
 * @since 2025-11-23
 *
 * Architecture:
 * - Alpine.js: Bundle Vite (chargé globalement)
 * - ZenFleetSelect: Wrapper SlimSelect (window.ZenFleetSelect)
 * - Auto-complétion intelligente kilométrage/coût/durée
 * - Gestion erreurs enterprise-grade
 *
 * Améliorations V6:
 * ✅ Initialisation robuste avec retry mechanism
 * ✅ Détection conflits auto-init
 * ✅ Gestion erreurs élégante
 * ✅ Performance optimale
 * ✅ Logging détaillé pour debug
 */

// ✅ FONCTION GLOBALE: Accessible par Alpine x-data
window.maintenanceFormData = function() {
    return {
        // ============================================
        // ÉTAT RÉACTIF
        // ============================================
        currentMileage: {{ old('mileage_at_maintenance', 0) }},
        estimatedCost: {{ old('total_cost', 0) }},
        durationHours: {{ old('duration_minutes') ? round(old('duration_minutes') / 60, 2) : 0 }},
        durationMinutes: {{ old('duration_minutes', 0) }},

        selectedVehicle: {},
        selectedType: {},

        autoFilledMileage: false,
        autoFilledCost: false,
        autoFilledDuration: false,

        // Instances ZenFleetSelect
        vehicleSelectInstance: null,
        providerSelectInstance: null,

        // État initialisation
        initialized: false,
        initRetries: 0,
        maxRetries: 3,

        // ============================================
        // INITIALISATION PRINCIPALE
        // ============================================
        /**
         * 🚀 Point d'entrée Alpine.js
         */
        init() {
            console.log('🎬 [Maintenance Form] Initialisation démarrée...');
            console.log('📊 [Stats] Véhicules:', {{ $vehicles->count() }}, '| Types:', {{ $maintenanceTypes->count() }}, '| Fournisseurs:', {{ $providers->count() }});

            // Attendre le DOM complet et ZenFleetSelect
            this.$nextTick(() => {
                this.initializeWithRetry();
            });
        },

        /**
         * 🔄 Initialisation avec retry mechanism
         */
        initializeWithRetry() {
            if (this.initialized) {
                console.log('✅ [Init] Déjà initialisé, skip');
                return;
            }

            // Vérifier disponibilité ZenFleetSelect
            if (typeof window.ZenFleetSelect === 'undefined') {
                console.warn('⏳ [Init] ZenFleetSelect pas encore chargé, retry', this.initRetries + 1);

                if (this.initRetries < this.maxRetries) {
                    this.initRetries++;
                    setTimeout(() => this.initializeWithRetry(), 300);
                } else {
                    console.error('❌ [Init] ZenFleetSelect non disponible après', this.maxRetries, 'tentatives');
                    this.showErrorNotification('Erreur de chargement des listes déroulantes');
                }
                return;
            }

            // Initialiser les selects
            try {
                this.initializeSelects();
                this.initialized = true;
                console.log('✅ [Init] Initialisation complète avec succès');
            } catch (error) {
                console.error('❌ [Init] Erreur critique:', error);
                this.showErrorNotification('Erreur lors de l\'initialisation du formulaire');
            }
        },

        /**
         * 🎨 Initialisation des ZenFleetSelect
         */
        initializeSelects() {
            // VÉHICULE
            if (this.$refs.vehicleSelect) {
                // Vérifier si déjà initialisé (double init prevention)
                if (this.$refs.vehicleSelect._zenfleetInitialized) {
                    console.log('⚠️ [Vehicle] Déjà initialisé, skip');
                } else {
                    try {
                        this.vehicleSelectInstance = new window.ZenFleetSelect(this.$refs.vehicleSelect, {
                            settings: {
                                searchPlaceholder: 'Rechercher un véhicule (immatriculation, marque, modèle)...',
                                searchText: 'Aucun véhicule trouvé',
                                searchHighlight: true,
                                closeOnSelect: true,
                                placeholderText: 'Sélectionner un véhicule',
                                showSearch: {{ $vehicles->count() > 5 ? 'true' : 'false' }}
                            },
                            events: {
                                afterChange: (newVal) => {
                                    // Propager le changement
                                    this.onVehicleChange(newVal);
                                }
                            }
                        });

                        // Marquer comme initialisé
                        this.$refs.vehicleSelect._zenfleetInitialized = true;
                        console.log('✅ [Vehicle] SlimSelect initialisé -', {{ $vehicles->count() }}, 'véhicules');
                    } catch (error) {
                        console.error('❌ [Vehicle] Erreur initialisation:', error);
                    }
                }
            } else {
                console.warn('⚠️ [Vehicle] Référence select introuvable');
            }

            // FOURNISSEUR
            if (this.$refs.providerSelect) {
                // Vérifier si déjà initialisé
                if (this.$refs.providerSelect._zenfleetInitialized) {
                    console.log('⚠️ [Provider] Déjà initialisé, skip');
                } else {
                    try {
                        this.providerSelectInstance = new window.ZenFleetSelect(this.$refs.providerSelect, {
                            settings: {
                                searchPlaceholder: 'Rechercher un fournisseur...',
                                searchText: 'Aucun fournisseur trouvé',
                                searchHighlight: true,
                                closeOnSelect: true,
                                allowDeselect: true,
                                placeholderText: 'Aucun (maintenance interne)',
                                showSearch: {{ $providers->count() > 5 ? 'true' : 'false' }}
                            }
                        });

                        // Marquer comme initialisé
                        this.$refs.providerSelect._zenfleetInitialized = true;
                        console.log('✅ [Provider] SlimSelect initialisé -', {{ $providers->count() }}, 'fournisseurs');
                    } catch (error) {
                        console.error('❌ [Provider] Erreur initialisation:', error);
                    }
                }
            } else {
                console.warn('⚠️ [Provider] Référence select introuvable');
            }
        },

        // ============================================
        // GESTIONNAIRES D'ÉVÉNEMENTS
        // ============================================

        /**
         * 🚗 Gestion changement de véhicule avec auto-complétion
         */
        onVehicleChange(vehicleId) {
            // Si c'est un tableau (retourné par certains selects multiples), prendre la première valeur
            if (Array.isArray(vehicleId)) {
                vehicleId = vehicleId[0]?.value || vehicleId[0];
            }
            // Si c'est un objet (retourné par SlimSelect parfois), prendre la value
            if (typeof vehicleId === 'object' && vehicleId !== null) {
                vehicleId = vehicleId.value;
            }

            if (!vehicleId) {
                this.currentMileage = 0;
                this.selectedVehicle = {};
                this.autoFilledMileage = false;
                return;
            }

            const select = document.getElementById('vehicle_id');
            // Trouver l'option correspondante même si SlimSelect a modifié le DOM
            let option = null;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value == vehicleId) {
                    option = select.options[i];
                    break;
                }
            }

            if (option) {
                const mileage = parseInt(option.dataset.mileage) || 0;

                this.selectedVehicle = {
                    id: vehicleId,
                    mileage: mileage,
                    brand: option.dataset.brand,
                    model: option.dataset.model
                };

                if (mileage > 0) {
                    this.currentMileage = mileage;
                    this.autoFilledMileage = true;
                    console.log('📊 Kilométrage auto-rempli:', mileage + ' km');
                }
            }
        },

        /**
         * 🔧 Gestion changement de type de maintenance
         */
        onTypeChange(typeId) {
            if (!typeId) {
                this.selectedType = {};
                this.autoFilledCost = false;
                this.autoFilledDuration = false;
                return;
            }

            const select = document.getElementById('maintenance_type_id');
            const option = select.options[select.selectedIndex];

            if (option) {
                this.selectedType = {
                    id: typeId,
                    category: option.dataset.category,
                    duration_hours: parseFloat(option.dataset.durationHours) || 0,
                    duration_minutes: parseInt(option.dataset.durationMinutes) || 0,
                    estimated_cost: parseFloat(option.dataset.cost) || 0,
                    description: option.dataset.description || ''
                };

                // Auto-remplir le coût estimé
                if (this.selectedType.estimated_cost > 0) {
                    this.estimatedCost = this.selectedType.estimated_cost;
                    this.autoFilledCost = true;
                    console.log('💰 Coût auto-rempli:', this.estimatedCost + ' DA');
                }

                // Auto-remplir la durée
                if (this.selectedType.duration_hours > 0) {
                    this.durationHours = this.selectedType.duration_hours;
                    this.durationMinutes = this.selectedType.duration_minutes;
                    this.autoFilledDuration = true;
                    console.log('⏱️ Durée auto-remplie:', this.durationHours + 'h (' + this.durationMinutes + ' min)');
                }
            }
        },

        /**
         * ⏱️ Conversion heures → minutes
         */
        updateDurationMinutes() {
            this.durationMinutes = Math.round(this.durationHours * 60);
            console.log('🔄 Durée mise à jour:', this.durationHours + 'h = ' + this.durationMinutes + ' min');
        },

        // ============================================
        // VALIDATION & SOUMISSION
        // ============================================

        /**
         * ✅ Validation avant soumission
         */
        onSubmit(event) {
            const vehicleId = document.getElementById('vehicle_id').value;
            const typeId = document.getElementById('maintenance_type_id').value;

            console.log('🔍 [Submit] Validation formulaire...');

            if (!vehicleId) {
                event.preventDefault();
                this.showErrorNotification('Veuillez sélectionner un véhicule');
                console.error('❌ [Submit] Véhicule manquant');
                return false;
            }

            if (!typeId) {
                event.preventDefault();
                this.showErrorNotification('Veuillez sélectionner un type de maintenance');
                console.error('❌ [Submit] Type de maintenance manquant');
                return false;
            }

            console.log('✅ [Submit] Validation réussie');
            console.log('📤 [Submit] Données:', {
                vehicle: vehicleId,
                type: typeId,
                mileage: this.currentMileage,
                cost: this.estimatedCost,
                duration: this.durationMinutes
            });

            return true;
        },

        // ============================================
        // UTILITAIRES & NOTIFICATIONS
        // ============================================

        /**
         * 🔔 Afficher notification d'erreur
         */
        showErrorNotification(message) {
            // Créer notification simple sans dépendance externe
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 z-50 bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg shadow-lg animate-fade-in max-w-md';
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto-remove après 5s
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }
    };
};
</script>
@endpush

@endsection
