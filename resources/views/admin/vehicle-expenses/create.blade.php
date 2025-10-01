@extends('layouts.admin')

@section('title', 'Nouvelle Dépense Véhicule')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Dépense Véhicule</h1>
            <p class="mt-2 text-sm text-gray-700">Enregistrez une nouvelle dépense avec conformité fiscale algérienne</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.vehicle-expenses.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <x-lucide-arrow-left class="h-4 w-4 mr-2" />
                Retour à la liste
            </a>
        </div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('admin.vehicle-expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Informations principales --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations principales</h3>
                <p class="mt-1 text-sm text-gray-500">Détails de base de la dépense</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Véhicule *</label>
                        <select name="vehicle_id" id="vehicle_id" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('vehicle_id') border-red-300 @enderror">
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fournisseur --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Fournisseur</label>
                        <select name="supplier_id" id="supplier_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('supplier_id') border-red-300 @enderror">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div>
                        <label for="expense_category" class="block text-sm font-medium text-gray-700">Catégorie *</label>
                        <select name="expense_category" id="expense_category" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('expense_category') border-red-300 @enderror">
                            <option value="">Sélectionner une catégorie</option>
                            <option value="fuel" {{ old('expense_category') === 'fuel' ? 'selected' : '' }}>Carburant</option>
                            <option value="maintenance" {{ old('expense_category') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="repair" {{ old('expense_category') === 'repair' ? 'selected' : '' }}>Réparation</option>
                            <option value="insurance" {{ old('expense_category') === 'insurance' ? 'selected' : '' }}>Assurance</option>
                            <option value="tolls" {{ old('expense_category') === 'tolls' ? 'selected' : '' }}>Péages</option>
                            <option value="parking" {{ old('expense_category') === 'parking' ? 'selected' : '' }}>Stationnement</option>
                            <option value="fines" {{ old('expense_category') === 'fines' ? 'selected' : '' }}>Amendes</option>
                            <option value="other" {{ old('expense_category') === 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('expense_category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type de dépense --}}
                    <div>
                        <label for="expense_type" class="block text-sm font-medium text-gray-700">Type de dépense</label>
                        <select name="expense_type" id="expense_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Sélectionner un type</option>
                            {{-- Les options seront mises à jour dynamiquement selon la catégorie --}}
                        </select>
                    </div>

                    {{-- Date de la dépense --}}
                    <div>
                        <label for="expense_date" class="block text-sm font-medium text-gray-700">Date de la dépense *</label>
                        <input type="date" name="expense_date" id="expense_date" required
                               value="{{ old('expense_date', now()->toDateString()) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('expense_date') border-red-300 @enderror">
                        @error('expense_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Relevé kilométrique --}}
                    <div>
                        <label for="odometer_reading" class="block text-sm font-medium text-gray-700">Relevé kilométrique</label>
                        <input type="number" name="odometer_reading" id="odometer_reading" min="0"
                               value="{{ old('odometer_reading') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Kilométrage actuel du véhicule</p>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                    <textarea name="description" id="description" rows="3" required
                              placeholder="Décrivez la dépense en détail..."
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Informations financières --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations financières</h3>
                <p class="mt-1 text-sm text-gray-500">Montants et calculs fiscaux (TVA 19%)</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    {{-- Montant HT --}}
                    <div>
                        <label for="amount_ht" class="block text-sm font-medium text-gray-700">Montant HT (DA) *</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="amount_ht" id="amount_ht" required
                                   step="0.01" min="0" value="{{ old('amount_ht') }}"
                                   placeholder="0.00"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('amount_ht') border-red-300 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">DA</span>
                            </div>
                        </div>
                        @error('amount_ht')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Taux TVA --}}
                    <div>
                        <label for="tva_rate" class="block text-sm font-medium text-gray-700">Taux TVA (%)</label>
                        <select name="tva_rate" id="tva_rate"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="19" {{ old('tva_rate', '19') == '19' ? 'selected' : '' }}>19% (Standard)</option>
                            <option value="9" {{ old('tva_rate') == '9' ? 'selected' : '' }}>9% (Réduit)</option>
                            <option value="0" {{ old('tva_rate') == '0' ? 'selected' : '' }}>0% (Exonéré)</option>
                        </select>
                    </div>

                    {{-- Montant TVA (calculé automatiquement) --}}
                    <div>
                        <label for="tva_amount" class="block text-sm font-medium text-gray-700">Montant TVA (DA)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="tva_amount" id="tva_amount" readonly
                                   step="0.01" value="{{ old('tva_amount', '0.00') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">DA</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Montant TTC (calculé automatiquement) --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-900">Montant Total TTC :</span>
                        <span id="total_ttc_display" class="text-2xl font-bold text-indigo-600">0.00 DA</span>
                    </div>
                    <input type="hidden" name="total_ttc" id="total_ttc" value="{{ old('total_ttc', '0.00') }}">
                </div>
            </div>
        </div>

        {{-- Quantité et mesures --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Quantité et Mesures</h3>
                <p class="mt-1 text-sm text-gray-500">Pour le calcul de consommation et l'analyse de performance</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    {{-- Quantité --}}
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantité</label>
                        <input type="number" name="quantity" id="quantity" step="0.01" min="0"
                               value="{{ old('quantity') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Unité --}}
                    <div>
                        <label for="unit_type" class="block text-sm font-medium text-gray-700">Unité</label>
                        <select name="unit_type" id="unit_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Sélectionner une unité</option>
                            <option value="liters" {{ old('unit_type') === 'liters' ? 'selected' : '' }}>Litres</option>
                            <option value="pieces" {{ old('unit_type') === 'pieces' ? 'selected' : '' }}>Pièces</option>
                            <option value="hours" {{ old('unit_type') === 'hours' ? 'selected' : '' }}>Heures</option>
                            <option value="kilometers" {{ old('unit_type') === 'kilometers' ? 'selected' : '' }}>Kilomètres</option>
                            <option value="services" {{ old('unit_type') === 'services' ? 'selected' : '' }}>Services</option>
                        </select>
                    </div>

                    {{-- Prix unitaire (calculé automatiquement) --}}
                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-gray-700">Prix unitaire (DA)</label>
                        <input type="number" name="unit_price" id="unit_price" readonly
                               step="0.01" value="{{ old('unit_price', '0.00') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- Informations de facturation --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de Facturation</h3>
                <p class="mt-1 text-sm text-gray-500">Références et documents justificatifs</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Référence facture --}}
                    <div>
                        <label for="invoice_reference" class="block text-sm font-medium text-gray-700">Référence facture</label>
                        <input type="text" name="invoice_reference" id="invoice_reference"
                               value="{{ old('invoice_reference') }}"
                               placeholder="FCT-2025-001"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Méthode de paiement --}}
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Méthode de paiement *</label>
                        <select name="payment_method" id="payment_method" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_method') border-red-300 @enderror">
                            <option value="">Sélectionner une méthode</option>
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Chèque</option>
                            <option value="corporate_card" {{ old('payment_method') === 'corporate_card' ? 'selected' : '' }}>Carte d'entreprise</option>
                            <option value="credit" {{ old('payment_method') === 'credit' ? 'selected' : '' }}>Crédit fournisseur</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date d'échéance paiement --}}
                    <div>
                        <label for="payment_due_date" class="block text-sm font-medium text-gray-700">Date d'échéance paiement</label>
                        <input type="date" name="payment_due_date" id="payment_due_date"
                               value="{{ old('payment_due_date') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    {{-- Référence de paiement --}}
                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700">Référence de paiement</label>
                        <input type="text" name="payment_reference" id="payment_reference"
                               value="{{ old('payment_reference') }}"
                               placeholder="CHQ-001, VIR-2025-001, etc."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                {{-- Reçu/Facture --}}
                <div>
                    <label for="receipt_file" class="block text-sm font-medium text-gray-700">Reçu ou Facture</label>
                    <div class="mt-1">
                        <input type="file" name="receipt_file" id="receipt_file"
                               accept="image/*,.pdf"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Joignez le reçu ou la facture (JPG, PNG, PDF - max 5MB)</p>
                    @error('receipt_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Géolocalisation --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Géolocalisation (Optionnel)</h3>
                <p class="mt-1 text-sm text-gray-500">Lieu de la dépense pour traçabilité</p>
            </div>
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" name="latitude" id="latitude" step="0.000001"
                               value="{{ old('latitude') }}"
                               placeholder="36.7538"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" name="longitude" id="longitude" step="0.000001"
                               value="{{ old('longitude') }}"
                               placeholder="3.0588"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <button type="button" onclick="getCurrentLocation()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <x-lucide-map-pin class="h-4 w-4 mr-2" />
                    Utiliser ma position actuelle
                </button>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="window.history.back()"
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                Annuler
            </button>
            <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <x-lucide-save class="h-4 w-4 mr-2" />
                Enregistrer la dépense
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountHtInput = document.getElementById('amount_ht');
    const tvaRateSelect = document.getElementById('tva_rate');
    const tvaAmountInput = document.getElementById('tva_amount');
    const totalTtcInput = document.getElementById('total_ttc');
    const totalTtcDisplay = document.getElementById('total_ttc_display');
    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');

    function calculateAmounts() {
        const amountHt = parseFloat(amountHtInput.value) || 0;
        const tvaRate = parseFloat(tvaRateSelect.value) || 0;

        const tvaAmount = amountHt * (tvaRate / 100);
        const totalTtc = amountHt + tvaAmount;

        tvaAmountInput.value = tvaAmount.toFixed(2);
        totalTtcInput.value = totalTtc.toFixed(2);
        totalTtcDisplay.textContent = new Intl.NumberFormat('fr-DZ', {
            style: 'currency',
            currency: 'DZD',
            minimumFractionDigits: 2
        }).format(totalTtc).replace('DZD', 'DA');

        // Calculer le prix unitaire si quantité disponible
        const quantity = parseFloat(quantityInput.value) || 0;
        if (quantity > 0) {
            const unitPrice = amountHt / quantity;
            unitPriceInput.value = unitPrice.toFixed(2);
        }
    }

    function calculateFromQuantity() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;

        if (quantity > 0 && unitPrice > 0) {
            const amountHt = quantity * unitPrice;
            amountHtInput.value = amountHt.toFixed(2);
            calculateAmounts();
        }
    }

    // Event listeners
    amountHtInput.addEventListener('input', calculateAmounts);
    tvaRateSelect.addEventListener('change', calculateAmounts);
    quantityInput.addEventListener('input', calculateFromQuantity);

    // Calcul initial
    calculateAmounts();

    // Types de dépense selon catégorie
    const expenseTypes = {
        'fuel': [
            { value: 'carburant_diesel', label: 'Carburant Diesel' },
            { value: 'carburant_essence', label: 'Carburant Essence' },
            { value: 'carburant_gpl', label: 'Carburant GPL' }
        ],
        'maintenance': [
            { value: 'vidange_huile', label: 'Vidange huile' },
            { value: 'changement_filtres', label: 'Changement filtres' },
            { value: 'revision_periodique', label: 'Révision périodique' },
            { value: 'controle_technique', label: 'Contrôle technique' }
        ],
        'repair': [
            { value: 'reparation_moteur', label: 'Réparation moteur' },
            { value: 'reparation_freins', label: 'Réparation freins' },
            { value: 'reparation_transmission', label: 'Réparation transmission' },
            { value: 'reparation_carrosserie', label: 'Réparation carrosserie' }
        ],
        'insurance': [
            { value: 'assurance_tous_risques', label: 'Assurance tous risques' },
            { value: 'assurance_responsabilite', label: 'Assurance responsabilité civile' }
        ],
        'other': [
            { value: 'autre_depense', label: 'Autre dépense' }
        ]
    };

    const categorySelect = document.getElementById('expense_category');
    const typeSelect = document.getElementById('expense_type');

    categorySelect.addEventListener('change', function() {
        const category = this.value;
        typeSelect.innerHTML = '<option value="">Sélectionner un type</option>';

        if (category && expenseTypes[category]) {
            expenseTypes[category].forEach(type => {
                const option = document.createElement('option');
                option.value = type.value;
                option.textContent = type.label;
                typeSelect.appendChild(option);
            });
        }
    });

    // Déclencher le changement initial si une catégorie est sélectionnée
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
            document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
        }, function(error) {
            alert('Erreur de géolocalisation: ' + error.message);
        });
    } else {
        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
    }
}
</script>
@endpush
@endsection