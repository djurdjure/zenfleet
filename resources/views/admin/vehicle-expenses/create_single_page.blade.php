@extends('layouts.admin.catalyst')

@section('title', 'Ajouter une Nouvelle Dépense')

@section('content')
{{-- ====================================================================
 💰 FORMULAIRE CRÉATION DÉPENSE - SINGLE PAGE ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Formulaire sur une seule page (pas de steps)
 - Design inspiré de components-demo.blade.php
 - Validation côté client et serveur
 - Calcul automatique TVA et TTC
 - Tom Select pour véhicule et fournisseur
 - Messages d'erreur contextualisés
 
 @version 1.0-SinglePage
 @since 2025-10-29
 ==================================================================== --}}

{{-- Messages de notification --}}
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

@if(session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 10000)"
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

{{-- Page Container --}}
<section class="bg-white">
    <div class="py-8 px-4 mx-auto max-w-7xl lg:py-16">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                <x-iconify icon="heroicons:currency-dollar" class="w-8 h-8 text-blue-600" />
                Ajouter une Nouvelle Dépense
            </h1>
            <p class="text-gray-600">
                Enregistrez une dépense pour un véhicule de votre flotte
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

        {{-- Formulaire --}}
        <form method="POST" action="{{ route('admin.vehicle-expenses.store') }}" x-data="expenseForm()" @submit="onSubmit">
            @csrf

            {{-- Section 1: Informations Principales --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-blue-600" />
                    Informations Principales
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Véhicule --}}
                    <div>
                        <x-tom-select
                            name="vehicle_id"
                            label="Véhicule"
                            placeholder="Choisir un véhicule..."
                            required
                            :error="$errors->first('vehicle_id')"
                            helpText="Sélectionnez le véhicule concerné"
                        >
                            <option value="">Choisir un véhicule...</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
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
                                $categories = config('expense_categories.categories', []);
                            @endphp
                            @foreach($categories as $key => $categoryData)
                                <option value="{{ $key }}">{{ $categoryData['label'] }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Type de dépense --}}
                    <div>
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
                                <option :value="value" x-text="label" :selected="value === '{{ old('expense_type') }}'"></option>
                            </template>
                        </x-select>
                    </div>

                </div>
            </div>

            {{-- Section 2: Montants et TVA --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <x-iconify icon="heroicons:calculator" class="w-6 h-6 text-green-600" />
                    Montants et TVA
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    
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
                            :value="old('tva_rate', '20')"
                            :error="$errors->first('tva_rate')"
                            x-model="tvaRate"
                            @change="calculateTTC()"
                        >
                            <option value="0">Sans TVA (0%)</option>
                            <option value="5.5">TVA réduite (5,5%)</option>
                            <option value="10">TVA intermédiaire (10%)</option>
                            <option value="20" selected>TVA normale (20%)</option>
                        </x-select>
                    </div>

                    {{-- Montant TTC calculé --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-2">
                            Montant TTC
                        </label>
                        <div class="h-10 px-3 flex items-center bg-blue-50 border border-blue-200 rounded-lg">
                            <span class="text-lg font-bold text-blue-600">
                                <span x-text="formatCurrency(totalTTC)">0,00</span> €
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            TVA: <span x-text="formatCurrency(tvaAmount)">0,00</span> €
                        </p>
                    </div>

                </div>
            </div>

            {{-- Section 3: Fournisseur et Paiement --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <x-iconify icon="heroicons:building-storefront" class="w-6 h-6 text-purple-600" />
                    Fournisseur et Paiement
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Fournisseur --}}
                    <div>
                        <x-tom-select
                            name="supplier_id"
                            label="Fournisseur (optionnel)"
                            placeholder="Sélectionner un fournisseur..."
                            :error="$errors->first('supplier_id')"
                            helpText="Le fournisseur ou prestataire"
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
                            x-model="paymentStatus"
                        >
                            <option value="pending">En attente</option>
                            <option value="paid">Payé</option>
                            <option value="partial">Partiellement payé</option>
                        </x-select>
                    </div>

                    {{-- Date de paiement (si payé) --}}
                    <div x-show="paymentStatus === 'paid' || paymentStatus === 'partial'">
                        <x-datepicker
                            name="payment_date"
                            label="Date de paiement"
                            :value="old('payment_date')"
                            :max="date('Y-m-d')"
                            :error="$errors->first('payment_date')"
                            helpText="Date à laquelle le paiement a été effectué"
                        />
                    </div>

                </div>
            </div>

            {{-- Section 4: Description et Notes --}}
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6 border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                    <x-iconify icon="heroicons:document-text" class="w-6 h-6 text-orange-600" />
                    Description et Notes
                </h2>

                <div class="space-y-6">
                    
                    {{-- Description détaillée --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-900 mb-2">
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
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $errors->first('description') }}
                            </p>
                        @else
                            <p class="mt-2 text-xs text-gray-500">
                                Minimum 10 caractères. Soyez précis pour faciliter le suivi.
                            </p>
                        @endif
                    </div>

                    {{-- Notes internes --}}
                    <div>
                        <label for="internal_notes" class="block text-sm font-medium text-gray-900 mb-2">
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
                        <p class="mt-2 text-xs text-gray-500">
                            Ces notes ne seront visibles que par votre équipe.
                        </p>
                    </div>

                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    
                    {{-- Annuler --}}
                    <a href="{{ route('admin.vehicle-expenses.index') }}" class="w-full sm:w-auto">
                        <x-button variant="ghost" size="lg" icon="arrow-left" iconPosition="left" class="w-full">
                            Annuler
                        </x-button>
                    </a>

                    {{-- Enregistrer --}}
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-iconify icon="heroicons:check-circle" class="w-5 h-5" />
                        <span x-text="isSubmitting ? 'Enregistrement...' : 'Enregistrer la dépense'">Enregistrer la dépense</span>
                    </button>

                </div>
            </div>

        </form>

    </div>
</section>

{{-- ====================================================================
 📊 ALPINE.JS LOGIC
 ==================================================================== --}}
<script>
function expenseForm() {
    return {
        category: '{{ old('expense_category', '') }}',
        amountHT: {{ old('amount_ht', 0) }},
        tvaRate: {{ old('tva_rate', 20) }},
        tvaAmount: 0,
        totalTTC: 0,
        paymentStatus: '{{ old('payment_status', 'pending') }}',
        expenseTypes: {},
        categoriesConfig: @json(config('expense_categories.categories', [])),
        isSubmitting: false,
        
        init() {
            this.updateTypes();
            this.calculateTTC();
        },
        
        updateTypes() {
            if (this.category && this.categoriesConfig[this.category]) {
                this.expenseTypes = this.categoriesConfig[this.category].types || {};
                
                // Définir le taux de TVA par défaut selon la catégorie
                const defaultTva = @json(config('expense_categories.default_tva_rates', []));
                if (defaultTva[this.category] !== undefined) {
                    this.tvaRate = defaultTva[this.category];
                    this.calculateTTC();
                }
            } else {
                this.expenseTypes = {};
            }
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
        
        onSubmit(e) {
            // Validation finale
            const description = document.querySelector('[name="description"]').value;
            
            if (description.length < 10) {
                e.preventDefault();
                alert('La description doit contenir au moins 10 caractères.');
                return false;
            }
            
            // Désactiver le bouton pendant la soumission
            this.isSubmitting = true;
            
            // Si le statut est payé, s'assurer qu'une date de paiement est fournie
            if (this.paymentStatus === 'paid' || this.paymentStatus === 'partial') {
                const paymentDate = document.querySelector('[name="payment_date"]').value;
                if (!paymentDate) {
                    e.preventDefault();
                    alert('Veuillez indiquer la date de paiement.');
                    this.isSubmitting = false;
                    return false;
                }
            }
            
            return true;
        }
    }
}
</script>

@endsection
