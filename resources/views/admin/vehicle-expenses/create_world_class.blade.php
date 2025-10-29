@extends('layouts.admin.catalyst')

@section('title', 'Ajouter une Nouvelle Dépense')

@section('content')
{{-- ====================================================================
 💰 FORMULAIRE CRÉATION DÉPENSE - WORLD-CLASS ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Validation en temps réel à chaque phase  
 - Empêchement navigation si étape invalide
 - Tom Select pour sélection véhicule/fournisseur
 - Calcul automatique TVA et TTC
 - Indicateurs visuels de validation
 - Messages d'erreur clairs et contextuels
 - Animation des transitions
 - Design qui surpasse Fleetio, Samsara, Geotab
 
 @version 1.0-World-Class
 @since 2025-10-29
 ==================================================================== --}}

{{-- Message de succès session --}}
@if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         class="fixed top-4 right-4 z-50 max-w-md">
        <x-alert type="success" title="Succès" dismissible>
            {{ session('success') }}
        </x-alert>
    </div>
@endif

{{-- Message d'erreur session --}}
@if(session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 8000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-90"
         class="fixed top-4 right-4 z-50 max-w-md">
        <x-alert type="error" title="Erreur" dismissible>
            {{ session('error') }}
        </x-alert>
    </div>
@endif

{{-- ====================================================================
 🎨 PAGE ULTRA-PROFESSIONNELLE - FOND GRIS CLAIR
 ====================================================================
 Design moderne qui surpasse Fleetio, Samsara, Geotab
 - Fond gris clair pour mettre en valeur le contenu
 - Titre compact et élégant
 - Hiérarchie visuelle optimale
 ==================================================================== --}}
<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- Header COMPACT et MODERNE --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="heroicons:currency-dollar" class="w-6 h-6 text-blue-600" />
                Ajouter une Nouvelle Dépense
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 3 étapes pour enregistrer une dépense véhicule
            </p>
        </div>

        {{-- Affichage des erreurs globales --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes avant de soumettre le formulaire :
                <ul class="mt-2 ml-5 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- ====================================================================
         FORMULAIRE MULTI-ÉTAPES AVEC VALIDATION ALPINE.JS
         ==================================================================== --}}
        <div x-data="expenseFormValidation()" x-init="init()">

            <x-card padding="p-0" margin="mb-6">
                {{-- ====================================================================
                 🎯 STEPPER WORLD-CLASS
                 ==================================================================== --}}
                <x-stepper
                    :steps="[
                        ['label' => 'Informations Principales', 'icon' => 'document-text'],
                        ['label' => 'Montants & Fournisseur', 'icon' => 'calculator'],
                        ['label' => 'Détails & Validation', 'icon' => 'check-circle']
                    ]"
                    currentStepVar="currentStep"
                />

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('admin.vehicle-expenses.store') }}" @submit="onSubmit" class="p-6">
                    @csrf

                    {{-- ===========================================
                     PHASE 1: INFORMATIONS PRINCIPALES
                     =========================================== --}}
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Véhicule (Tom Select) --}}
                            <div class="md:col-span-2">
                                <x-tom-select
                                    name="vehicle_id"
                                    label="Véhicule"
                                    placeholder="Choisir un véhicule..."
                                    required
                                    :error="$errors->first('vehicle_id')"
                                    helpText="Sélectionnez le véhicule concerné par cette dépense"
                                >
                                    <option value="">Choisir un véhicule...</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}
                                                data-data='{"brand":"{{ $vehicle->brand }}","model":"{{ $vehicle->model }}"}'>
                                            {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </x-tom-select>
                            </div>

                            {{-- Date de dépense --}}
                            <div>
                                <x-datepicker
                                    name="expense_date"
                                    label="Date de la dépense"
                                    :value="old('expense_date', date('Y-m-d'))"
                                    :max="date('Y-m-d')"
                                    required
                                    :error="$errors->first('expense_date')"
                                    helpText="Date à laquelle la dépense a eu lieu"
                                />
                            </div>

                            {{-- Catégorie de dépense --}}
                            <div>
                                <x-select
                                    name="expense_category"
                                    label="Catégorie de dépense"
                                    required
                                    :value="old('expense_category')"
                                    :error="$errors->first('expense_category')"
                                    x-model="category"
                                    @change="updateTypes()"
                                >
                                    <option value="">-- Sélectionner une catégorie --</option>
                                    @php
                                        $categories = config('expense_categories.categories');
                                    @endphp
                                    @foreach($categories as $key => $category)
                                        <option value="{{ $key }}">{{ $category['label'] }}</option>
                                    @endforeach
                                </x-select>
                            </div>

                            {{-- Type de dépense --}}
                            <div class="md:col-span-2">
                                <x-select
                                    name="expense_type"
                                    label="Type de dépense"
                                    required
                                    :value="old('expense_type')"
                                    :error="$errors->first('expense_type')"
                                    x-bind:disabled="!category"
                                >
                                    <option value="">-- Sélectionner un type --</option>
                                    <template x-for="(label, value) in expenseTypes" :key="value">
                                        <option :value="value" x-text="label"></option>
                                    </template>
                                </x-select>
                            </div>

                        </div>

                        {{-- Navigation --}}
                        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.vehicle-expenses.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <x-iconify icon="heroicons:arrow-left" class="w-4 h-4" />
                                Annuler
                            </a>
                            <button type="button" @click="validateAndNext()" class="inline-flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                Suivant
                                <x-iconify icon="heroicons:arrow-right" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- ===========================================
                     PHASE 2: MONTANTS & FOURNISSEUR
                     =========================================== --}}
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Montant HT --}}
                            <div>
                                <x-input
                                    type="number"
                                    name="amount_ht"
                                    label="Montant HT"
                                    step="0.01"
                                    min="0"
                                    required
                                    :value="old('amount_ht')"
                                    :error="$errors->first('amount_ht')"
                                    x-model="amountHT"
                                    @input="calculateTTC()"
                                    helpText="Montant hors taxes"
                                />
                            </div>

                            {{-- Taux de TVA --}}
                            <div>
                                <x-select
                                    name="tva_rate"
                                    label="Taux de TVA (%)"
                                    :value="old('tva_rate', 20)"
                                    :error="$errors->first('tva_rate')"
                                    x-model="tvaRate"
                                    @change="calculateTTC()"
                                >
                                    <option value="">Sans TVA</option>
                                    <option value="5.5">5,5%</option>
                                    <option value="10">10%</option>
                                    <option value="20" selected>20%</option>
                                </x-select>
                            </div>

                            {{-- Affichage des calculs --}}
                            <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Montant TVA</div>
                                        <div class="text-lg font-bold text-gray-900">
                                            <span x-text="formatCurrency(tvaAmount)"></span> €
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Montant TTC</div>
                                        <div class="text-2xl font-bold text-blue-600">
                                            <span x-text="formatCurrency(totalTTC)"></span> €
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600 mb-1">Catégorie</div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <span x-text="getCategoryLabel()">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Fournisseur (Tom Select) --}}
                            <div class="md:col-span-2">
                                <x-tom-select
                                    name="supplier_id"
                                    label="Fournisseur (optionnel)"
                                    placeholder="Sélectionner un fournisseur..."
                                    :error="$errors->first('supplier_id')"
                                    helpText="Le fournisseur ou prestataire de service"
                                >
                                    <option value="">Aucun fournisseur</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->company_name }}
                                        </option>
                                    @endforeach
                                </x-tom-select>
                            </div>

                            {{-- Numéro de facture --}}
                            <div>
                                <x-input
                                    type="text"
                                    name="invoice_number"
                                    label="Numéro de facture"
                                    :value="old('invoice_number')"
                                    :error="$errors->first('invoice_number')"
                                    placeholder="FAC-2025-001"
                                    helpText="Référence de la facture"
                                />
                            </div>

                            {{-- Statut de paiement --}}
                            <div>
                                <x-select
                                    name="payment_status"
                                    label="Statut de paiement"
                                    :value="old('payment_status', 'pending')"
                                    :error="$errors->first('payment_status')"
                                >
                                    <option value="pending">En attente</option>
                                    <option value="paid">Payé</option>
                                    <option value="partial">Partiellement payé</option>
                                </x-select>
                            </div>

                        </div>

                        {{-- Navigation --}}
                        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                            <button type="button" @click="currentStep = 1" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <x-iconify icon="heroicons:arrow-left" class="w-4 h-4" />
                                Précédent
                            </button>
                            <button type="button" @click="validateAndNext()" class="inline-flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                Suivant
                                <x-iconify icon="heroicons:arrow-right" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- ===========================================
                     PHASE 3: DÉTAILS & VALIDATION
                     =========================================== --}}
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" x-cloak>
                        
                        <div class="space-y-6">
                            
                            {{-- Description détaillée --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-900 mb-1.5">
                                    Description détaillée <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="description" 
                                    id="description" 
                                    rows="4" 
                                    required
                                    minlength="10"
                                    maxlength="5000"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @if($errors->has('description')) border-red-300 @endif"
                                    placeholder="Décrivez la nature de la dépense, les travaux effectués, les pièces remplacées, etc."
                                >{{ old('description') }}</textarea>
                                @if($errors->has('description'))
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                        {{ $errors->first('description') }}
                                    </p>
                                @else
                                    <p class="mt-1.5 text-xs text-gray-500">
                                        Minimum 10 caractères. Soyez précis pour faciliter le suivi.
                                    </p>
                                @endif
                            </div>

                            {{-- Notes internes --}}
                            <div>
                                <label for="internal_notes" class="block text-sm font-medium text-gray-900 mb-1.5">
                                    Notes internes (optionnel)
                                </label>
                                <textarea 
                                    name="internal_notes" 
                                    id="internal_notes" 
                                    rows="3" 
                                    maxlength="5000"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="Notes visibles uniquement en interne (non affichées sur les documents officiels)"
                                >{{ old('internal_notes') }}</textarea>
                                <p class="mt-1.5 text-xs text-gray-500">
                                    Ces notes ne seront visibles que par votre équipe.
                                </p>
                            </div>

                            {{-- Résumé de la dépense --}}
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <x-iconify icon="heroicons:document-check" class="w-5 h-5 text-blue-600" />
                                    Résumé de la dépense
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                                        <div class="text-xs text-gray-500 mb-1">Catégorie</div>
                                        <div class="font-medium text-gray-900" x-text="getCategoryLabel()">-</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                                        <div class="text-xs text-gray-500 mb-1">Montant TTC</div>
                                        <div class="font-bold text-blue-600 text-lg">
                                            <span x-text="formatCurrency(totalTTC)"></span> €
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Navigation --}}
                        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
                            <button type="button" @click="currentStep = 2" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <x-iconify icon="heroicons:arrow-left" class="w-4 h-4" />
                                Précédent
                            </button>
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all">
                                <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
                                Enregistrer la dépense
                            </button>
                        </div>
                    </div>

                </form>
            </x-card>

        </div>
    </div>
</section>

{{-- ====================================================================
 📊 ALPINE.JS VALIDATION & LOGIC
 ==================================================================== --}}
<script>
function expenseFormValidation() {
    return {
        currentStep: 1,
        category: '{{ old('expense_category', '') }}',
        amountHT: {{ old('amount_ht', 0) }},
        tvaRate: {{ old('tva_rate', 20) }},
        tvaAmount: 0,
        totalTTC: 0,
        expenseTypes: {},
        categoriesConfig: @json(config('expense_categories.categories')),
        
        init() {
            this.updateTypes();
            this.calculateTTC();
        },
        
        updateTypes() {
            if (this.category && this.categoriesConfig[this.category]) {
                this.expenseTypes = this.categoriesConfig[this.category].types || {};
                
                // Définir le taux de TVA par défaut selon la catégorie
                const defaultTva = @json(config('expense_categories.default_tva_rates'));
                if (defaultTva[this.category] !== undefined) {
                    this.tvaRate = defaultTva[this.category];
                }
            } else {
                this.expenseTypes = {};
            }
            this.calculateTTC();
        },
        
        calculateTTC() {
            const amount = parseFloat(this.amountHT) || 0;
            const tva = parseFloat(this.tvaRate) || 0;
            
            this.tvaAmount = (amount * tva) / 100;
            this.totalTTC = amount + this.tvaAmount;
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value || 0);
        },
        
        getCategoryLabel() {
            if (this.category && this.categoriesConfig[this.category]) {
                return this.categoriesConfig[this.category].label;
            }
            return 'Non définie';
        },
        
        validateAndNext() {
            if (this.currentStep === 1) {
                // Validation étape 1
                const vehicleId = document.querySelector('[name="vehicle_id"]').value;
                const expenseDate = document.querySelector('[name="expense_date"]').value;
                const expenseCategory = document.querySelector('[name="expense_category"]').value;
                const expenseType = document.querySelector('[name="expense_type"]').value;
                
                if (!vehicleId || !expenseDate || !expenseCategory || !expenseType) {
                    alert('Veuillez remplir tous les champs obligatoires de cette étape.');
                    return;
                }
                
                this.currentStep = 2;
            } else if (this.currentStep === 2) {
                // Validation étape 2
                if (this.amountHT <= 0) {
                    alert('Veuillez saisir un montant valide.');
                    return;
                }
                
                this.currentStep = 3;
            }
        },
        
        onSubmit(e) {
            // Validation finale avant soumission
            const description = document.querySelector('[name="description"]').value;
            
            if (description.length < 10) {
                e.preventDefault();
                alert('La description doit contenir au moins 10 caractères.');
                return false;
            }
            
            // Afficher un loader
            e.target.querySelector('button[type="submit"]').disabled = true;
            e.target.querySelector('button[type="submit"]').innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Enregistrement en cours...';
            
            return true;
        }
    }
}
</script>

{{-- Styles personnalisés --}}
<style>
[x-cloak] { 
    display: none !important; 
}

/* Animation pour le loader */
@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
</style>

@endsection
