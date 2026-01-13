{{-- ====================================================================
 💰 FORMULAIRE CRÉATION DÉPENSE - ULTRA-PROFESSIONAL ENTERPRISE-GRADE
 ====================================================================

 🚀 Design cohérent avec formulaires véhicules et affectations:
 ✅ Tom Select pour listes déroulantes enterprise
 ✅ Validation temps réel
 ✅ Calcul automatique TVA
 ✅ Devise: Dinar Algérien (DA)
 ✅ Upload de justificatifs
 ✅ Design ultra-pro avec animations

 @version 1.0-World-Class
 @since 2025-10-29
 @author Expert Fullstack Developer (20+ ans)
==================================================================== --}}

@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Dépense - ZenFleet')

@push('styles')

<style>
    /* Ultra-Pro Enterprise Expense Creation Styles */

    /* Global animations */
    .fade-in {
        animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.98);
            filter: blur(2px);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }
    }

    /* Ultra-modern section cards */
    .form-section {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }

    .form-section:hover::before {
        left: 100%;
    }

    .form-section:hover {
        transform: translateY(-2px) scale(1.005);
        border-color: rgba(99, 102, 241, 0.2);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06), 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .form-section h3 {
        color: #1e293b;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 3px solid transparent;
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
        background-size: 100% 3px;
        background-repeat: no-repeat;
        background-position: bottom;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #dc2626;
        margin-left: 0.25rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid rgba(229, 231, 235, 0.8);
        border-radius: 12px;
        font-size: 0.875rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        backdrop-filter: blur(5px);
    }

    .form-input:focus {
        transform: scale(1.01);
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1), 0 4px 12px rgba(0, 0, 0, 0.06);
        background: white;
        outline: none;
    }

    .form-textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        resize: vertical;
        min-height: 100px;
    }

    .form-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background: white;
        outline: none;
    }



    /* Currency badge styling */
    .currency-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-weight: 600;
        border-radius: 8px;
        font-size: 0.875rem;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    /* Upload zone styling */
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: #6366f1;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        transform: scale(1.02);
    }

    /* Alert styling */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #fca5a5;
        color: #991b1b;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ===============================================
            HEADER ULTRA-COMPACT
        =============================================== --}}
        <div class="mb-6 fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                            <x-iconify icon="lucide:receipt-text" class="w-6 h-6 text-white" />
                        </div>
                        Nouvelle Dépense
                    </h1>
                    <p class="text-gray-600 mt-2 text-sm">Enregistrez une nouvelle dépense véhicule avec justificatif</p>
                </div>
                <a href="{{ route('admin.expenses.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:arrow-left" class="w-5 h-5 text-gray-600" />
                    <span class="font-medium text-gray-700">Retour</span>
                </a>
            </div>
        </div>

        {{-- ===============================================
            ERRORS DISPLAY
        =============================================== --}}
        @if ($errors->any())
        <div class="alert alert-danger fade-in">
            <x-iconify icon="lucide:alert-circle" class="w-5 h-5 flex-shrink-0" />
            <div>
                <h3 class="font-semibold mb-1">Erreurs de validation :</h3>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- ===============================================
            FORMULAIRE PRINCIPAL
        =============================================== --}}
        <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" id="expenseForm">
            @csrf

            {{-- Section 1: Informations Véhicule --}}
            <div class="form-section fade-in" style="animation-delay: 0.1s">
                <h3>
                    <x-iconify icon="lucide:car" class="w-6 h-6 text-blue-600" />
                    Informations Véhicule & Chauffeur
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Véhicule --}}
                    {{-- Véhicule --}}
                    <div class="form-group">
                        <label for="vehicle-select" class="form-label">
                            Véhicule <span class="required">*</span>
                        </label>
                        <x-slim-select name="vehicle_id" id="vehicle-select" placeholder="Sélectionner un véhicule..." required>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                            </option>
                            @endforeach
                        </x-slim-select>
                        <p class="text-xs text-gray-500 mt-1">Sélectionnez le véhicule concerné par cette dépense</p>
                    </div>

                    {{-- Chauffeur (optionnel) --}}
                    <div class="form-group">
                        <label for="driver-select" class="form-label">
                            Chauffeur <span class="text-gray-400">(optionnel)</span>
                        </label>
                        <x-slim-select name="driver_id" id="driver-select" placeholder="Aucun chauffeur...">
                            <option value="">Aucun chauffeur...</option>
                            @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                {{ $driver->first_name }} {{ $driver->last_name }} ({{ $driver->employee_number ?? 'N/A' }})
                            </option>
                            @endforeach
                        </x-slim-select>
                        <p class="text-xs text-gray-500 mt-1">Chauffeur responsable au moment de la dépense</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Type & Catégorie de Dépense --}}
            <div class="form-section fade-in" style="animation-delay: 0.2s">
                <h3>
                    <x-iconify icon="lucide:tags" class="w-6 h-6 text-purple-600" />
                    Type & Catégorie de Dépense
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Catégorie de dépense --}}
                    <div class="form-group">
                        <label for="expense-category" class="form-label">
                            Catégorie de dépense <span class="required">*</span>
                        </label>
                        <select id="expense-category" name="expense_type" class="form-input" required>
                            <option value="">Sélectionner une catégorie...</option>
                            @php
                            $categories = \App\Models\VehicleExpense::getExpenseCategories();
                            @endphp
                            @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('expense_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Type de dépense effectuée</p>
                    </div>

                    {{-- Date de la dépense --}}
                    <div class="form-group">
                        <label for="expense-date" class="form-label">
                            Date de la dépense <span class="required">*</span>
                        </label>
                        <input type="date"
                            id="expense-date"
                            name="expense_date"
                            class="form-input"
                            max="{{ date('Y-m-d') }}"
                            value="{{ old('expense_date', date('Y-m-d')) }}"
                            required>
                        <p class="text-xs text-gray-500 mt-1">Date à laquelle la dépense a été effectuée</p>
                    </div>
                </div>
            </div>

            {{-- Section 3: Montants & TVA --}}
            <div class="form-section fade-in" style="animation-delay: 0.3s">
                <h3>
                    <x-iconify icon="lucide:banknote" class="w-6 h-6 text-green-600" />
                    Montants & Fiscalité
                    <span class="ml-auto currency-badge">
                        <x-iconify icon="lucide:coins" class="w-4 h-4 mr-1" />
                        Dinar Algérien (DA)
                    </span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Montant HT --}}
                    <div class="form-group">
                        <label for="amount-ht" class="form-label">
                            Montant HT (DA) <span class="required">*</span>
                        </label>
                        <div class="relative">
                            <input type="number"
                                id="amount-ht"
                                name="amount"
                                class="form-input pr-12"
                                step="0.01"
                                min="0.01"
                                max="999999.99"
                                value="{{ old('amount') }}"
                                placeholder="0.00"
                                required
                                oninput="calculateTTC()">
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">DA</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Montant hors taxes</p>
                    </div>

                    {{-- Taux de TVA --}}
                    <div class="form-group">
                        <label for="tax-rate" class="form-label">
                            Taux TVA (%) <span class="required">*</span>
                        </label>
                        <select id="tax-rate" name="tax_rate" class="form-input" required onchange="calculateTTC()">
                            <option value="0" {{ old('tax_rate', 19) == 0 ? 'selected' : '' }}>0% (Exonéré)</option>
                            <option value="9" {{ old('tax_rate', 19) == 9 ? 'selected' : '' }}>9% (Taux réduit)</option>
                            <option value="19" {{ old('tax_rate', 19) == 19 ? 'selected' : '' }}>19% (Taux normal)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Taux de TVA applicable en Algérie</p>
                    </div>

                    {{-- Montant TTC (calculé automatiquement) --}}
                    <div class="form-group">
                        <label for="amount-ttc" class="form-label">
                            Montant TTC (DA)
                        </label>
                        <div class="relative">
                            <input type="text"
                                id="amount-ttc"
                                class="form-input pr-12 bg-gray-100 font-bold text-green-700"
                                value="0.00"
                                readonly>
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-green-700 font-bold">DA</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Calculé automatiquement</p>
                    </div>
                </div>

                {{-- Affichage détaillé des montants --}}
                <div id="amount-breakdown" class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl hidden">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">Montant HT:</span>
                        <span class="font-semibold" id="display-ht">0.00 DA</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-gray-700">TVA (<span id="display-tax-rate">19</span>%):</span>
                        <span class="font-semibold text-blue-600" id="display-tva">0.00 DA</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-bold mt-3 pt-3 border-t-2 border-blue-300">
                        <span class="text-gray-900">TOTAL TTC:</span>
                        <span class="text-green-700 text-xl" id="display-ttc">0.00 DA</span>
                    </div>
                </div>
            </div>

            {{-- Section 4: Détails Fournisseur & Facture --}}
            <div class="form-section fade-in" style="animation-delay: 0.4s">
                <h3>
                    <x-iconify icon="lucide:file-text" class="w-6 h-6 text-orange-600" />
                    Détails Fournisseur & Facture
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Nom fournisseur --}}
                    <div class="form-group">
                        <label for="supplier-name" class="form-label">
                            Nom du fournisseur
                        </label>
                        <input type="text"
                            id="supplier-name"
                            name="supplier_name"
                            class="form-input"
                            maxlength="255"
                            value="{{ old('supplier_name') }}"
                            placeholder="Ex: Garage Al-Baraka, Station Total...">
                    </div>

                    {{-- Numéro de facture --}}
                    <div class="form-group">
                        <label for="invoice-number" class="form-label">
                            Numéro de facture
                        </label>
                        <input type="text"
                            id="invoice-number"
                            name="invoice_number"
                            class="form-input"
                            maxlength="100"
                            value="{{ old('invoice_number') }}"
                            placeholder="Ex: F-2025-001234">
                    </div>

                    {{-- Kilométrage au moment de la dépense --}}
                    <div class="form-group">
                        <label for="mileage" class="form-label">
                            Kilométrage (km)
                        </label>
                        <input type="number"
                            id="mileage"
                            name="mileage_at_expense"
                            class="form-input"
                            min="0"
                            value="{{ old('mileage_at_expense') }}"
                            placeholder="Ex: 125000">
                        <p class="text-xs text-gray-500 mt-1">Kilométrage du véhicule au moment de la dépense</p>
                    </div>
                </div>
            </div>

            {{-- Section 5: Description & Justificatif --}}
            <div class="form-section fade-in" style="animation-delay: 0.5s">
                <h3>
                    <x-iconify icon="lucide:file-image" class="w-6 h-6 text-red-600" />
                    Description & Justificatif
                </h3>

                <div class="grid grid-cols-1 gap-6">
                    {{-- Description --}}
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Description de la dépense <span class="required">*</span>
                        </label>
                        <textarea id="description"
                            name="description"
                            class="form-textarea"
                            maxlength="1000"
                            required
                            placeholder="Décrivez en détail la nature de cette dépense...">{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Maximum 1000 caractères</p>
                    </div>

                    {{-- Upload justificatif --}}
                    <div class="form-group">
                        <label for="receipt-file" class="form-label">
                            Justificatif (Facture/Reçu) <span class="text-gray-400">(optionnel)</span>
                        </label>
                        <div class="upload-zone" onclick="document.getElementById('receipt-file').click()">
                            <input type="file"
                                id="receipt-file"
                                name="receipt_file"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                                onchange="updateFileName(this)">
                            <x-iconify icon="lucide:upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                            <p class="text-sm font-medium text-gray-700">Cliquez pour télécharger un fichier</p>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG ou PNG (max 10 MB)</p>
                            <p id="file-name" class="text-sm font-semibold text-blue-600 mt-2 hidden"></p>
                        </div>
                    </div>

                    {{-- Notes internes --}}
                    <div class="form-group">
                        <label for="notes" class="form-label">
                            Notes internes <span class="text-gray-400">(optionnel)</span>
                        </label>
                        <textarea id="notes"
                            name="notes"
                            class="form-textarea"
                            maxlength="2000"
                            placeholder="Notes additionnelles pour usage interne uniquement...">{{ old('notes') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Ces notes ne seront visibles que par les administrateurs</p>
                    </div>
                </div>
            </div>

            {{-- Actions Buttons --}}
            <div class="flex items-center justify-between gap-4 fade-in" style="animation-delay: 0.6s">
                <a href="{{ route('admin.expenses.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-iconify icon="lucide:x" class="w-5 h-5 text-gray-600" />
                    <span class="font-semibold text-gray-700">Annuler</span>
                </a>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <x-iconify icon="lucide:save" class="w-5 h-5" />
                    <span>Enregistrer la dépense</span>
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    < script src = "https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js" >
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {



        // =====================================================
        // CALCUL AUTOMATIQUE TTC
        // =====================================================
        window.calculateTTC = function() {
            const amountHT = parseFloat(document.getElementById('amount-ht').value) || 0;
            const taxRate = parseFloat(document.getElementById('tax-rate').value) || 0;

            const taxAmount = (amountHT * taxRate) / 100;
            const totalTTC = amountHT + taxAmount;

            // Mise à jour affichage
            document.getElementById('amount-ttc').value = totalTTC.toFixed(2);
            document.getElementById('display-ht').textContent = amountHT.toFixed(2) + ' DA';
            document.getElementById('display-tax-rate').textContent = taxRate.toFixed(0);
            document.getElementById('display-tva').textContent = taxAmount.toFixed(2) + ' DA';
            document.getElementById('display-ttc').textContent = totalTTC.toFixed(2) + ' DA';

            // Afficher le breakdown si montant > 0
            const breakdown = document.getElementById('amount-breakdown');
            if (amountHT > 0) {
                breakdown.classList.remove('hidden');
            } else {
                breakdown.classList.add('hidden');
            }
        };

        // =====================================================
        // UPLOAD FILENAME DISPLAY
        // =====================================================
        window.updateFileName = function(input) {
            const fileNameDisplay = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `📎 ${fileName} (${fileSize} MB)`;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        };

        // Calcul initial au chargement
        calculateTTC();

        console.log('✅ Formulaire de création de dépense initialisé avec succès');
    });
</script>
@endpush