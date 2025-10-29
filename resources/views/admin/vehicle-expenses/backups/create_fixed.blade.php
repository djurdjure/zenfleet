@extends('layouts.admin')

@section('title', 'Nouvelle dépense véhicule')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        {{-- En-tête avec breadcrumb --}}
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                            <x-iconify icon="lucide:home" class="w-4 h-4" />
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <a href="{{ route('admin.vehicle-expenses.index') }}" class="text-gray-500 hover:text-gray-700">
                                Dépenses
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <x-iconify icon="lucide:chevron-right" class="w-4 h-4 text-gray-400 mx-1" />
                            <span class="text-gray-900 font-medium">Nouvelle dépense</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <x-iconify icon="lucide:receipt" class="w-8 h-8 text-indigo-600" />
                Nouvelle dépense véhicule
            </h1>
            <p class="mt-2 text-gray-600">Enregistrez une nouvelle dépense pour un véhicule de votre flotte</p>
        </div>

        {{-- Messages d'erreur et de succès --}}
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-start gap-3 animate-slide-in-down" role="alert">
                <x-iconify icon="lucide:alert-circle" class="w-5 h-5 mt-0.5 flex-shrink-0" />
                <div>
                    <p class="font-semibold">Erreur</p>
                    <p class="text-sm mt-1">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg animate-slide-in-down" role="alert">
                <div class="flex items-start gap-3">
                    <x-iconify icon="lucide:alert-triangle" class="w-5 h-5 mt-0.5 flex-shrink-0" />
                    <div class="flex-1">
                        <p class="font-semibold">Des erreurs ont été détectées :</p>
                        <ul class="mt-2 space-y-1 text-sm list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire principal --}}
        <form action="{{ route('admin.vehicle-expenses.store') }}" method="POST" enctype="multipart/form-data" x-data="expenseForm()" @submit="validateForm">
            @csrf
            
            {{-- Section 1: Informations principales --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-t-xl">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:info" class="w-5 h-5 text-indigo-600" />
                        Informations principales
                    </h2>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:car" class="w-4 h-4 text-gray-500" />
                            Véhicule <span class="text-red-500">*</span>
                        </label>
                        <select name="vehicle_id" id="vehicle_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('vehicle_id') border-red-300 @enderror"
                            x-model="vehicleId">
                            <option value="">-- Sélectionner un véhicule --</option>
                            @php
                                // Récupération des véhicules depuis la base de données
                                $vehicles = \App\Models\Vehicle::where('organization_id', auth()->user()->organization_id)
                                    ->orderBy('registration_plate')
                                    ->get();
                            @endphp
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Date de dépense --}}
                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:calendar" class="w-4 h-4 text-gray-500" />
                            Date de la dépense <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="expense_date" id="expense_date" required
                            value="{{ old('expense_date', date('Y-m-d')) }}"
                            max="{{ date('Y-m-d') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('expense_date') border-red-300 @enderror">
                        @error('expense_date')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Catégorie de dépense --}}
                    <div>
                        <label for="expense_category" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:layers" class="w-4 h-4 text-gray-500" />
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select name="expense_category" id="expense_category" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('expense_category') border-red-300 @enderror"
                            x-model="category" @change="updateTypes()">
                            <option value="">-- Sélectionner une catégorie --</option>
                            @php
                                $categories = config('expense_categories.categories');
                            @endphp
                            @foreach($categories as $key => $category)
                                <option value="{{ $key }}" {{ old('expense_category') == $key ? 'selected' : '' }}>
                                    {{ $category['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_category')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Type de dépense --}}
                    <div>
                        <label for="expense_type" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:tag" class="w-4 h-4 text-gray-500" />
                            Type de dépense <span class="text-red-500">*</span>
                        </label>
                        <select name="expense_type" id="expense_type" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('expense_type') border-red-300 @enderror"
                            :disabled="!category">
                            <option value="">-- Sélectionner un type --</option>
                            <template x-for="(label, value) in expenseTypes" :key="value">
                                <option :value="value" x-text="label" :selected="value === '{{ old('expense_type') }}'"></option>
                            </template>
                        </select>
                        @error('expense_type')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Montants et TVA --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50 rounded-t-xl">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:calculator" class="w-5 h-5 text-green-600" />
                        Montants et TVA
                    </h2>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Montant HT --}}
                    <div>
                        <label for="amount_ht" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:euro" class="w-4 h-4 text-gray-500" />
                            Montant HT <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount_ht" id="amount_ht" required
                            step="0.01" min="0" max="99999999"
                            value="{{ old('amount_ht') }}"
                            x-model="amountHT" @input="calculateTTC()"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('amount_ht') border-red-300 @enderror"
                            placeholder="0.00">
                        @error('amount_ht')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Taux TVA --}}
                    <div>
                        <label for="tva_rate" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:percent" class="w-4 h-4 text-gray-500" />
                            Taux TVA (%)
                        </label>
                        <select name="tva_rate" id="tva_rate"
                            x-model="tvaRate" @change="calculateTTC()"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sans TVA</option>
                            <option value="5.5">5.5%</option>
                            <option value="10">10%</option>
                            <option value="20" selected>20%</option>
                        </select>
                    </div>

                    {{-- Montant TTC (calculé) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:coins" class="w-4 h-4 text-gray-500" />
                            Montant TTC
                        </label>
                        <div class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 font-medium text-gray-900">
                            <span x-text="formatCurrency(totalTTC)"></span> €
                        </div>
                        <p class="mt-1 text-xs text-gray-500">TVA: <span x-text="formatCurrency(tvaAmount)"></span> €</p>
                    </div>
                </div>
            </div>

            {{-- Section 3: Fournisseur et paiement --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50 rounded-t-xl">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:building-2" class="w-5 h-5 text-purple-600" />
                        Fournisseur et paiement
                    </h2>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Fournisseur --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:truck" class="w-4 h-4 text-gray-500" />
                            Fournisseur
                        </label>
                        <select name="supplier_id" id="supplier_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Aucun fournisseur --</option>
                            @php
                                $suppliers = \App\Models\Supplier::where('organization_id', auth()->user()->organization_id)
                                    ->where('is_active', true)
                                    ->orderBy('company_name')
                                    ->get();
                            @endphp
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Numéro de facture --}}
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:file-text" class="w-4 h-4 text-gray-500" />
                            Numéro de facture
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number"
                            value="{{ old('invoice_number') }}"
                            maxlength="100"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="FAC-2025-001">
                    </div>

                    {{-- Mode de paiement --}}
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:credit-card" class="w-4 h-4 text-gray-500" />
                            Mode de paiement
                        </label>
                        <select name="payment_method" id="payment_method"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Sélectionner --</option>
                            <option value="especes" {{ old('payment_method') == 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="carte" {{ old('payment_method') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            <option value="virement" {{ old('payment_method') == 'virement' ? 'selected' : '' }}>Virement</option>
                            <option value="bon" {{ old('payment_method') == 'bon' ? 'selected' : '' }}>Bon de commande</option>
                            <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>Crédit</option>
                        </select>
                    </div>

                    {{-- Statut de paiement --}}
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:circle-dollar-sign" class="w-4 h-4 text-gray-500" />
                            Statut de paiement
                        </label>
                        <select name="payment_status" id="payment_status"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending" {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partiellement payé</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 4: Description --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-t-xl">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-iconify icon="lucide:file-text" class="w-5 h-5 text-orange-600" />
                        Description et notes
                    </h2>
                </div>
                
                <div class="p-6 space-y-6">
                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:text" class="w-4 h-4 text-gray-500" />
                            Description détaillée <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="3" required
                            minlength="10" maxlength="5000"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror"
                            placeholder="Décrivez la nature de la dépense, les travaux effectués, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="lucide:alert-circle" class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimum 10 caractères</p>
                    </div>

                    {{-- Notes internes --}}
                    <div>
                        <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                            <x-iconify icon="lucide:sticky-note" class="w-4 h-4 text-gray-500" />
                            Notes internes (optionnel)
                        </label>
                        <textarea name="internal_notes" id="internal_notes" rows="2"
                            maxlength="5000"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Notes visibles uniquement en interne">{{ old('internal_notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex justify-between items-center bg-white shadow-sm rounded-xl border border-gray-200 px-6 py-4">
                <a href="{{ route('admin.vehicle-expenses.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 rounded-lg transition-colors">
                    <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                    Annuler
                </a>
                
                <div class="flex gap-3">
                    <button type="submit" name="action" value="save_and_new"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-indigo-600 text-indigo-600 bg-white hover:bg-indigo-50 rounded-lg transition-colors">
                        <x-iconify icon="lucide:save" class="w-4 h-4" />
                        Enregistrer et nouveau
                    </button>
                    
                    <button type="submit" name="action" value="save"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg transition-colors shadow-sm">
                        <x-iconify icon="lucide:check" class="w-4 h-4" />
                        Enregistrer la dépense
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Script Alpine.js pour la gestion du formulaire --}}
<script>
function expenseForm() {
    return {
        vehicleId: '{{ old('vehicle_id', '') }}',
        category: '{{ old('expense_category', '') }}',
        amountHT: {{ old('amount_ht', 0) }},
        tvaRate: {{ old('tva_rate', 20) }},
        tvaAmount: 0,
        totalTTC: 0,
        expenseTypes: {},
        
        // Configuration des types par catégorie
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
            }).format(value);
        },
        
        validateForm(event) {
            // Validation supplémentaire si nécessaire
            return true;
        }
    }
}
</script>

{{-- Styles additionnels pour les animations --}}
<style>
@keyframes slide-in-down {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-in-down {
    animation: slide-in-down 0.3s ease-out;
}

/* Amélioration des champs en erreur */
.border-red-300:focus {
    --tw-ring-color: rgb(239 68 68);
    --tw-border-opacity: 1;
    border-color: rgb(252 165 165 / var(--tw-border-opacity));
}
</style>
@endsection
