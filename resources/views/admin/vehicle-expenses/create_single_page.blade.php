@extends('layouts.admin.catalyst')

@section('title', 'Ajouter une Nouvelle Depense')

@section('content')
@php
    $categories = config('expense_categories.categories', []);
@endphp

@if(session('success'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition
    class="fixed top-4 right-4 z-50 max-w-md">
    <x-alert type="success" title="Succes" dismissible>
        {{ session('success') }}
    </x-alert>
</div>
@endif

@if(session('error'))
<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 8000)"
    x-transition
    class="fixed top-4 right-4 z-50 max-w-md">
    <x-alert type="error" title="Erreur" dismissible>
        {{ session('error') }}
    </x-alert>
</div>
@endif

<section class="zf-page min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-600">Ajouter une nouvelle depense</h1>
            <p class="text-xs text-gray-600">Enregistrez une depense vehicule avec validation et tracabilite centralisees.</p>
        </div>

        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les champs en erreur.
                <ul class="mt-2 ml-5 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('admin.vehicle-expenses.store') }}" x-data="expenseForm()" @submit="onSubmit" class="space-y-8">
            @csrf

            <x-form-section
                title="Informations principales"
                icon="heroicons:document-text"
                subtitle="Vehicule, date et classification de la depense">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-slim-select
                        name="vehicle_id"
                        label="Vehicule"
                        placeholder="Choisir un vehicule..."
                        required
                        :error="$errors->first('vehicle_id')"
                        helpText="Selectionnez le vehicule concerne">
                        <option value="" data-placeholder="true">Choisir un vehicule...</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(old('vehicle_id') == $vehicle->id)>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                        @endforeach
                    </x-slim-select>

                    <x-datepicker
                        name="expense_date"
                        label="Date de la depense"
                        :value="old('expense_date', date('Y-m-d'))"
                        :maxDate="date('Y-m-d')"
                        required
                        :error="$errors->first('expense_date')"
                        helpText="Date a laquelle la depense a eu lieu" />

                    <x-select
                        name="expense_category"
                        label="Categorie de depense"
                        required
                        :value="old('expense_category')"
                        :error="$errors->first('expense_category')"
                        x-model="category"
                        @change="updateTypes()">
                        <option value="">Selectionner une categorie</option>
                        @foreach($categories as $key => $categoryData)
                            <option value="{{ $key }}">{{ $categoryData['label'] }}</option>
                        @endforeach
                    </x-select>

                    <x-select
                        name="expense_type"
                        label="Type de depense"
                        required
                        :value="old('expense_type')"
                        :error="$errors->first('expense_type')"
                        x-bind:disabled="!category">
                        <option value="">Selectionner un type</option>
                        <template x-for="(label, value) in expenseTypes" :key="value">
                            <option :value="value" x-text="label" :selected="value === @js(old('expense_type'))"></option>
                        </template>
                    </x-select>
                </div>
            </x-form-section>

            <x-form-section
                title="Montants et TVA"
                icon="heroicons:calculator"
                subtitle="Calcul automatique du total TTC">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-input
                        type="number"
                        name="amount_ht"
                        label="Montant HT (DA)"
                        step="0.01"
                        min="0"
                        required
                        :value="old('amount_ht')"
                        :error="$errors->first('amount_ht')"
                        x-model="amountHT"
                        @input="calculateTTC()"
                        helpText="Montant hors taxes en Dinar Algerien" />

                    <div>
                        <x-select
                            name="tva_rate"
                            label="Taux de TVA (%)"
                            :value="old('tva_rate', '19')"
                            :error="$errors->first('tva_rate')"
                            x-model="tvaRate"
                            @change="calculateTTC()">
                            <option value="0">Sans TVA (0%)</option>
                            <option value="9">TVA reduite (9%)</option>
                            <option value="19">TVA normale (19%)</option>
                        </x-select>
                        <p class="mt-2 text-xs text-gray-600">Taux TVA applicables en Algerie</p>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Montant TTC</label>
                        <div class="h-10 px-3 flex items-center rounded-lg border border-[#0c90ee]/30 bg-blue-50">
                            <span class="text-base font-semibold text-[#0c90ee]">
                                <span x-text="formatCurrency(totalTTC)">0,00</span> DA
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">
                            TVA: <span x-text="formatCurrency(tvaAmount)">0,00</span> DA
                        </p>
                        <input type="hidden" name="tva_amount" :value="Number.isFinite(tvaAmount) ? tvaAmount.toFixed(2) : '0.00'">
                        <input type="hidden" name="total_ttc" :value="Number.isFinite(totalTTC) ? totalTTC.toFixed(2) : '0.00'">
                    </div>
                </div>
            </x-form-section>

            <x-form-section
                title="Fournisseur et paiement"
                icon="heroicons:building-storefront"
                subtitle="Informations de facturation et suivi du reglement">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-slim-select
                        name="supplier_id"
                        label="Fournisseur (optionnel)"
                        placeholder="Selectionner un fournisseur..."
                        :error="$errors->first('supplier_id')"
                        helpText="Prestataire ou fournisseur associe">
                        <option value="" data-placeholder="true">Aucun fournisseur</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                {{ $supplier->company_name ?? $supplier->name }}
                            </option>
                        @endforeach
                    </x-slim-select>

                    <x-input
                        type="text"
                        name="invoice_number"
                        label="Numero de facture"
                        :value="old('invoice_number')"
                        :error="$errors->first('invoice_number')"
                        placeholder="FAC-2026-001"
                        helpText="Reference facture fournisseur" />

                    <x-select
                        name="payment_status"
                        label="Statut de paiement"
                        :value="old('payment_status', 'pending')"
                        :error="$errors->first('payment_status')"
                        x-model="paymentStatus">
                        <option value="pending">En attente</option>
                        <option value="paid">Paye</option>
                        <option value="partial">Partiellement paye</option>
                    </x-select>

                    <div x-show="paymentStatus === 'paid' || paymentStatus === 'partial'" x-transition>
                        <x-datepicker
                            name="payment_date"
                            label="Date de paiement"
                            :value="old('payment_date')"
                            :maxDate="date('Y-m-d')"
                            :error="$errors->first('payment_date')"
                            helpText="Date effective du reglement" />
                    </div>
                </div>
            </x-form-section>

            <x-form-section
                title="Description et notes"
                icon="heroicons:clipboard-document-list"
                subtitle="Detaillez la depense pour faciliter le suivi et l'audit">
                <div class="space-y-6">
                    <div>
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-600">
                            Description detaillee <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            required
                            minlength="10"
                            maxlength="5000"
                            class="block w-full rounded-lg border bg-gray-50 px-3 py-2.5 text-sm text-gray-900 transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] @error('description') border-red-500 @else border-gray-300 @enderror"
                            placeholder="Decrivez la nature de la depense, les travaux effectues, les pieces remplacees...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <x-iconify icon="heroicons:exclamation-circle" class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @else
                            <p class="mt-2 text-xs text-gray-600">Minimum 10 caracteres pour une tracabilite exploitable.</p>
                        @enderror
                    </div>

                    <div>
                        <label for="internal_notes" class="block mb-2 text-sm font-medium text-gray-600">Notes internes (optionnel)</label>
                        <textarea
                            name="internal_notes"
                            id="internal_notes"
                            rows="3"
                            maxlength="5000"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 transition-all duration-200 hover:border-gray-400 focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee]"
                            placeholder="Notes visibles uniquement en interne...">{{ old('internal_notes') }}</textarea>
                        <p class="mt-2 text-xs text-gray-600">Ces notes ne sont visibles que par l'equipe interne.</p>
                    </div>
                </div>
            </x-form-section>

            <div class="relative pl-14">
                <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a
                            href="{{ route('admin.vehicle-expenses.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center h-10 px-4 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 focus:border-[#0c90ee] transition-all duration-200">
                            Annuler
                        </a>

                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto inline-flex items-center justify-center h-10 gap-2 px-6 rounded-lg border border-[#0c90ee] bg-[#0c90ee] text-sm font-medium text-white transition-all duration-200 hover:bg-[#0a7fd1] hover:border-[#0a7fd1] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20 disabled:opacity-60 disabled:cursor-not-allowed">
                            <x-iconify icon="heroicons:check" class="w-5 h-5" />
                            <span x-text="isSubmitting ? 'Enregistrement...' : 'Enregistrer la depense'"></span>
                        </button>
                    </div>
                </section>
            </div>
        </form>
    </div>
</section>

<script>
    function expenseForm() {
        return {
            category: @js(old('expense_category', '')),
            amountHT: Number(@js(old('amount_ht', 0))) || 0,
            tvaRate: Number(@js(old('tva_rate', 19))) || 0,
            tvaAmount: 0,
            totalTTC: 0,
            paymentStatus: @js(old('payment_status', 'pending')),
            expenseTypes: {},
            categoriesConfig: @json($categories),
            selectedType: @js(old('expense_type', '')),
            isSubmitting: false,

            init() {
                this.updateTypes();
                this.calculateTTC();
            },

            updateTypes() {
                const config = this.categoriesConfig?.[this.category];
                this.expenseTypes = config?.types || {};
            },

            calculateTTC() {
                const amount = Number(this.amountHT) || 0;
                const tva = Number(this.tvaRate) || 0;
                this.tvaAmount = (amount * tva) / 100;
                this.totalTTC = amount + this.tvaAmount;
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(Number(value) || 0);
            },

            onSubmit(event) {
                const description = document.getElementById('description')?.value || '';
                if (description.trim().length < 10) {
                    event.preventDefault();
                    this.isSubmitting = false;
                    return false;
                }

                if ((this.paymentStatus === 'paid' || this.paymentStatus === 'partial')) {
                    const paymentDate = document.querySelector('[name=\"payment_date\"]')?.value;
                    if (!paymentDate) {
                        event.preventDefault();
                        this.isSubmitting = false;
                        return false;
                    }
                }

                this.isSubmitting = true;
                return true;
            }
        };
    }
</script>
@endsection
