@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Dépense Véhicule')

@push('styles')
<style>
/* 🎨 ZENFLEET EXPENSE MODULE - ENTERPRISE ULTRA-PRO V3.0 */

/* Tom-Select Premium Styling */
.ts-control {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 0.5rem 0.75rem !important;
    min-height: 42px !important;
    background-color: #fff !important;
    transition: all 0.15s ease-in-out !important;
}

.ts-control:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    background-color: #fff !important;
}

.ts-control.required-empty {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

.ts-dropdown {
    border-radius: 0.5rem !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    margin-top: 4px !important;
}

.ts-dropdown .option {
    padding: 0.5rem 0.75rem !important;
    transition: background-color 0.15s !important;
}

.ts-dropdown .option:hover {
    background-color: #f3f4f6 !important;
}

.ts-dropdown .active {
    background-color: #eff6ff !important;
    color: #1d4ed8 !important;
}

/* Amount Input Styling */
.amount-input {
    text-align: right;
    font-weight: 600;
    font-size: 1.125rem;
}

.currency-symbol {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    font-weight: 500;
    pointer-events: none;
}

/* Total Display Premium */
.total-display {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 2px solid #3b82f6;
    position: relative;
    overflow: hidden;
}

.total-display::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: rotate(45deg);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

/* Section Headers */
.section-header {
    position: relative;
    padding-left: 2rem;
}

.section-header::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
    border-radius: 2px;
}

/* File Upload Area */
.file-upload-area {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 2px dashed #9ca3af;
    transition: all 0.3s ease;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
}

.file-upload-area.dragging {
    border-color: #1d4ed8;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    transform: scale(1.01);
}

/* Validation States */
.field-valid {
    border-color: #10b981 !important;
}

.field-invalid {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
}

/* Loading States */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Success Animation */
@keyframes successPulse {
    0% { transform: scale(0.95); opacity: 0; }
    50% { transform: scale(1.05); opacity: 1; }
    100% { transform: scale(1); opacity: 1; }
}

.success-message {
    animation: successPulse 0.5s ease-out;
}
</style>
@endpush

@section('content')
{{-- ====================================================================
 💰 FORMULAIRE DÉPENSE VÉHICULE - ENTERPRISE ULTRA-PRO V3.0
 ====================================================================
 
 🚀 FEATURES ENTERPRISE-GRADE:
 ✅ Pas de présélection de véhicule (option vide par défaut)
 ✅ Date de facture vraiment optionnelle
 ✅ Validation intelligente côté client et serveur
 ✅ UX/UI niveau Fortune 500
 ✅ Compatible multi-tenant
 ✅ Conformité fiscale Algérie (TVA 19%)
 
 @version 3.0-Enterprise-UltraPro
 @since 2025-10-28
 ==================================================================== --}}

{{-- Loading Overlay --}}
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="text-center">
        <x-iconify icon="lucide:loader-2" class="w-12 h-12 text-blue-600 spinner mb-4" />
        <p class="text-gray-600 font-medium">Enregistrement en cours...</p>
    </div>
</div>

{{-- Success Message --}}
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
         class="fixed top-4 right-4 z-50 max-w-md success-message">
        <x-alert type="success" title="Succès" dismissible>
            {{ session('success') }}
        </x-alert>
    </div>
@endif

<section class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="py-8 px-4 mx-auto max-w-7xl">

        {{-- Header Premium --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <x-iconify icon="lucide:receipt" class="w-7 h-7 text-blue-600" />
                        </div>
                        Nouvelle Dépense Véhicule
                    </h1>
                    <p class="text-gray-600 ml-14">
                        Enregistrez une nouvelle dépense avec conformité fiscale et traçabilité complète
                    </p>
                </div>
                
                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="window.history.back()" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                        <x-iconify icon="lucide:arrow-left" class="w-4 h-4" />
                        Retour
                    </button>
                </div>
            </div>
        </div>

        {{-- Error Display --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                <ul class="mt-2 ml-5 list-disc text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- Main Form --}}
        <div x-data="expenseFormUltraPro()" x-init="init()">
            <form method="POST" 
                  action="{{ route('admin.vehicle-expenses.store') }}" 
                  enctype="multipart/form-data" 
                  @submit.prevent="handleSubmit"
                  id="expenseForm">
                @csrf

                {{-- Section 1: Informations Principales --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="section-header px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                            Informations Principales
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 ml-7">Détails essentiels de la dépense</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- 🎯 VÉHICULE (Sans présélection) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Véhicule <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="vehicle_id"
                                    id="vehicle_id"
                                    class="tom-select-vehicle"
                                    required
                                    x-model="vehicleId"
                                    @change="onVehicleChange">
                                    <option value="">-- Sélectionner un véhicule --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-start">
                                        <x-iconify icon="lucide:alert-circle" class="w-4 h-4 mr-1 mt-0.5" />
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Sélectionnez le véhicule concerné par cette dépense
                                </p>
                            </div>

                            {{-- Date de la dépense --}}
                            <div>
                                <x-datepicker-pro
                                    name="expense_date"
                                    label="Date de la dépense"
                                    placeholder="JJ/MM/AAAA"
                                    :value="old('expense_date')"
                                    required
                                    :maxDate="date('Y-m-d')"
                                    :error="$errors->first('expense_date')"
                                    helpText="Date à laquelle la dépense a été effectuée"
                                    :defaultToday="true"
                                />
                            </div>

                            {{-- 🎯 FOURNISSEUR (Optionnel) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fournisseur <span class="text-gray-400">(optionnel)</span>
                                </label>
                                <select 
                                    name="supplier_id"
                                    id="supplier_id"
                                    class="tom-select-supplier"
                                    x-model="supplierId">
                                    <option value="">-- Aucun fournisseur / Dépense occasionnelle --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->company_name }} 
                                            @if($supplier->supplier_type_label)
                                                ({{ $supplier->supplier_type_label }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-start">
                                        <x-iconify icon="lucide:alert-circle" class="w-4 h-4 mr-1 mt-0.5" />
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Laissez vide pour une dépense ponctuelle sans fournisseur régulier
                                </p>
                            </div>

                            {{-- Catégorie --}}
                            <div>
                                <x-select-pro
                                    name="expense_category"
                                    label="Catégorie de dépense"
                                    :options="[
                                        'Catégories principales' => [
                                            'carburant' => '⛽ Carburant',
                                            'maintenance' => '🔧 Maintenance',
                                            'reparation' => '🔨 Réparation'
                                        ],
                                        'Frais administratifs' => [
                                            'assurance' => '🛡️ Assurance',
                                            'taxe' => '📋 Taxe/Vignette',
                                            'controle_technique' => '🔍 Contrôle technique'
                                        ],
                                        'Frais d\'exploitation' => [
                                            'peage' => '🛣️ Péage',
                                            'parking' => '🅿️ Parking',
                                            'amende' => '⚠️ Amende/Contravention',
                                            'lavage' => '🚿 Lavage'
                                        ],
                                        'Autres' => [
                                            'piece' => '⚙️ Pièce détachée',
                                            'autre' => '📦 Autre'
                                        ]
                                    ]"
                                    required
                                    :value="old('expense_category')"
                                    :error="$errors->first('expense_category')"
                                    x-model="category"
                                    @change="onCategoryChange"
                                />
                            </div>

                            {{-- Type de dépense --}}
                            <div class="md:col-span-2">
                                <x-input
                                    name="expense_type"
                                    label="Type de dépense"
                                    icon="tag"
                                    placeholder="Ex: Vidange, Plein d'essence, Réparation freins..."
                                    required
                                    :value="old('expense_type')"
                                    :error="$errors->first('expense_type')"
                                    helpText="Décrivez précisément le type de dépense"
                                />
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    Description détaillée <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="3"
                                    required
                                    placeholder="Décrivez la dépense en détail..."
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Informations Financières --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="section-header px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:calculator" class="w-5 h-5 text-green-600" />
                            Informations Financières
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 ml-7">Montants et calcul TVA (19% Algérie)</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Montant HT --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant HT (DA) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="amount_ht"
                                        id="amount_ht"
                                        required
                                        step="0.01"
                                        min="0"
                                        value="{{ old('amount_ht', 0) }}"
                                        x-model.number="amountHT"
                                        @input="calculateTTC"
                                        class="amount-input block w-full pr-12 border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0.00"
                                    />
                                    <span class="currency-symbol">DA</span>
                                </div>
                                @error('amount_ht')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Taux TVA --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Taux TVA (%)
                                </label>
                                <select
                                    name="tva_rate"
                                    id="tva_rate"
                                    x-model.number="tvaRate"
                                    @change="calculateTTC"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sans TVA</option>
                                    <option value="19" {{ old('tva_rate') == '19' ? 'selected' : '' }}>19% (Standard)</option>
                                    <option value="9" {{ old('tva_rate') == '9' ? 'selected' : '' }}>9% (Réduit)</option>
                                    <option value="0" {{ old('tva_rate') == '0' ? 'selected' : '' }}>0% (Exonéré)</option>
                                </select>
                            </div>

                            {{-- Montant TVA --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant TVA (DA)
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="tva_amount"
                                        readonly
                                        x-model="tvaAmount"
                                        class="amount-input block w-full pr-12 bg-gray-50 border-gray-300 rounded-lg cursor-not-allowed"
                                    />
                                    <span class="currency-symbol">DA</span>
                                </div>
                            </div>
                        </div>

                        {{-- Total TTC --}}
                        <div class="mt-6">
                            <div class="total-display rounded-xl p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Total TTC</label>
                                        <p class="text-xs text-gray-600 mt-0.5">Montant total toutes taxes comprises</p>
                                    </div>
                                    <div class="text-right">
                                        <input type="hidden" name="total_ttc" x-model="totalTTC">
                                        <div class="text-3xl font-bold text-blue-600" x-text="formatCurrency(totalTTC)"></div>
                                        <p class="text-sm text-gray-600 mt-1">Dinars Algériens</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Documents (OPTIONNELS) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                    <div class="section-header px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <x-iconify icon="lucide:paperclip" class="w-5 h-5 text-purple-600" />
                            Documents et Justificatifs
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 ml-7">Tous les champs sont optionnels</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-2">
                                <x-iconify icon="lucide:info-circle" class="w-5 h-5 text-blue-600 mt-0.5" />
                                <div>
                                    <p class="text-sm text-blue-800 font-medium">Information importante</p>
                                    <p class="text-xs text-blue-700 mt-1">
                                        La date de facture n'est requise que si vous joignez un fichier de facture.
                                        Si vous n'avez pas de facture, laissez ces champs vides.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- 🎯 Numéro de facture (OPTIONNEL) --}}
                            <div>
                                <x-input
                                    name="invoice_number"
                                    label="N° Facture (optionnel)"
                                    icon="document"
                                    placeholder="Ex: FAC-2025-001234"
                                    :value="old('invoice_number')"
                                    :error="$errors->first('invoice_number')"
                                    x-model="invoiceNumber"
                                />
                            </div>

                            {{-- 🎯 Date de facture (OPTIONNEL - NON REQUIS) --}}
                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date facture <span class="text-gray-400">(optionnel)</span>
                                </label>
                                <input
                                    type="date"
                                    name="invoice_date"
                                    id="invoice_date"
                                    value="{{ old('invoice_date') }}"
                                    max="{{ date('Y-m-d') }}"
                                    x-model="invoiceDate"
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                                @error('invoice_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Uniquement si vous avez une facture
                                </p>
                            </div>

                            {{-- Méthode de paiement --}}
                            <div>
                                <x-select
                                    name="payment_method"
                                    label="Mode de paiement"
                                    :options="[
                                        '' => '-- Sélectionner --',
                                        'especes' => '💵 Espèces',
                                        'cheque' => '📄 Chèque',
                                        'virement' => '💳 Virement',
                                        'carte' => '💳 Carte bancaire',
                                        'credit' => '📊 Crédit fournisseur'
                                    ]"
                                    :value="old('payment_method')"
                                    :error="$errors->first('payment_method')"
                                />
                            </div>

                            {{-- Statut paiement --}}
                            <div>
                                <x-select
                                    name="payment_status"
                                    label="Statut paiement"
                                    :options="[
                                        'pending' => '⏳ En attente',
                                        'paid' => '✅ Payé',
                                        'partial' => '⚠️ Partiel'
                                    ]"
                                    :value="old('payment_status', 'pending')"
                                    :error="$errors->first('payment_status')"
                                />
                            </div>

                            {{-- Upload de fichiers (OPTIONNEL) --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Justificatifs <span class="text-gray-400">(optionnel)</span>
                                </label>
                                
                                <div class="file-upload-area rounded-lg p-6 text-center"
                                     x-data="{ isDragging: false }"
                                     @dragover.prevent="isDragging = true"
                                     @dragleave.prevent="isDragging = false"
                                     @drop.prevent="isDragging = false; handleFileDrop($event)"
                                     :class="{ 'dragging': isDragging }">
                                    
                                    <x-iconify icon="lucide:cloud-upload" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                    
                                    <p class="text-sm text-gray-600 mb-2">
                                        Glissez vos fichiers ici ou
                                    </p>
                                    
                                    <input 
                                        type="file" 
                                        name="attachments[]" 
                                        id="file-upload" 
                                        multiple 
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                        class="hidden"
                                        @change="handleFileSelect($event)">
                                    
                                    <label for="file-upload" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                        <x-iconify icon="lucide:folder-open" class="w-4 h-4" />
                                        Parcourir
                                    </label>
                                    
                                    <p class="text-xs text-gray-500 mt-3">
                                        PDF, JPG, PNG, DOC, DOCX (max 5MB par fichier)
                                    </p>
                                </div>

                                {{-- Liste des fichiers uploadés --}}
                                <div x-show="uploadedFiles.length > 0" class="mt-4 space-y-2">
                                    <template x-for="(file, index) in uploadedFiles" :key="index">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <x-iconify icon="lucide:file" class="w-5 h-5 text-gray-500" />
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700" x-text="file.name"></p>
                                                    <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                                <x-iconify icon="lucide:x" class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">
                            <span class="text-red-500">*</span> Champs obligatoires
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" 
                                onclick="window.history.back()"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            Annuler
                        </button>
                        
                        <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <x-iconify icon="lucide:save" class="w-5 h-5" />
                            Enregistrer la dépense
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
/**
 * ====================================================================
 * 🚀 EXPENSE FORM ULTRA-PRO V3.0 - ZENFLEET ENTERPRISE
 * ====================================================================
 * 
 * Features:
 * ✅ Pas de présélection de véhicule
 * ✅ Date facture vraiment optionnelle
 * ✅ Validation intelligente
 * ✅ UX/UI Fortune 500
 * 
 * @version 3.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
function expenseFormUltraPro() {
    return {
        // State
        vehicleId: '',
        supplierId: '',
        category: '',
        amountHT: 0,
        tvaRate: null,
        tvaAmount: 0,
        totalTTC: 0,
        invoiceNumber: '',
        invoiceDate: '',
        uploadedFiles: [],
        vehicleSelect: null,
        supplierSelect: null,
        isSubmitting: false,

        init() {
            // Initialiser les valeurs old()
            this.amountHT = {{ old('amount_ht', 0) }};
            this.tvaRate = {{ old('tva_rate') ?: 'null' }};
            this.category = '{{ old('expense_category', '') }}';
            this.vehicleId = '{{ old('vehicle_id', '') }}';
            this.supplierId = '{{ old('supplier_id', '') }}';
            
            // Calculer les montants
            this.calculateTTC();
            
            // Initialiser TomSelect pour véhicule
            this.$nextTick(() => {
                const vehicleEl = document.getElementById('vehicle_id');
                if (vehicleEl) {
                    this.vehicleSelect = new TomSelect(vehicleEl, {
                        plugins: ['clear_button'],
                        maxOptions: 200,
                        placeholder: '-- Sélectionner un véhicule --',
                        allowEmptyOption: true,
                        create: false,
                        onChange: (value) => {
                            this.vehicleId = value;
                            this.validateVehicle();
                        }
                    });
                    
                    // ⚡ IMPORTANT: Ne pas présélectionner si pas de old value
                    if (!this.vehicleId) {
                        this.vehicleSelect.clear();
                    }
                }
                
                // Initialiser TomSelect pour fournisseur
                const supplierEl = document.getElementById('supplier_id');
                if (supplierEl) {
                    this.supplierSelect = new TomSelect(supplierEl, {
                        plugins: ['clear_button'],
                        maxOptions: 200,
                        placeholder: '-- Aucun fournisseur / Dépense occasionnelle --',
                        allowEmptyOption: true,
                        create: false,
                        onChange: (value) => {
                            this.supplierId = value;
                        }
                    });
                    
                    if (!this.supplierId) {
                        this.supplierSelect.clear();
                    }
                }
            });
        },

        // Calcul TVA et TTC
        calculateTTC() {
            if (!this.amountHT || this.amountHT <= 0) {
                this.tvaAmount = 0;
                this.totalTTC = 0;
                return;
            }

            if (this.tvaRate !== null && this.tvaRate !== '' && this.tvaRate >= 0) {
                this.tvaAmount = (this.amountHT * this.tvaRate / 100);
                this.totalTTC = this.amountHT + this.tvaAmount;
            } else {
                this.tvaAmount = 0;
                this.totalTTC = this.amountHT;
            }

            // Arrondir
            this.tvaAmount = Math.round(this.tvaAmount * 100) / 100;
            this.totalTTC = Math.round(this.totalTTC * 100) / 100;
        },

        // Format monnaie
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-DZ', {
                style: 'currency',
                currency: 'DZD',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0).replace('DZD', 'DA');
        },

        // Format taille fichier
        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        // Gestion fichiers
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.addFiles(files);
        },

        handleFileDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.addFiles(files);
        },

        addFiles(files) {
            const validFiles = files.filter(file => {
                const validTypes = [
                    'application/pdf', 
                    'image/jpeg', 
                    'image/jpg', 
                    'image/png', 
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!validTypes.includes(file.type)) {
                    this.showNotification('error', `Le fichier ${file.name} n'est pas d'un type accepté`);
                    return false;
                }
                
                if (file.size > maxSize) {
                    this.showNotification('error', `Le fichier ${file.name} dépasse la taille maximale de 5MB`);
                    return false;
                }
                
                return true;
            });
            
            this.uploadedFiles = [...this.uploadedFiles, ...validFiles];
        },

        removeFile(index) {
            this.uploadedFiles.splice(index, 1);
        },

        // Validation véhicule
        validateVehicle() {
            const control = this.vehicleSelect?.control;
            if (control) {
                if (!this.vehicleId) {
                    control.classList.add('required-empty');
                } else {
                    control.classList.remove('required-empty');
                }
            }
        },

        // Events
        onVehicleChange() {
            console.log('Véhicule sélectionné:', this.vehicleId);
            this.validateVehicle();
        },

        onCategoryChange() {
            console.log('Catégorie sélectionnée:', this.category);
        },

        // Notification
        showNotification(type, message) {
            // Créer une notification temporaire
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg ${
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'error' ? 
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>' :
                            '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        }
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        },

        // 🚀 SOUMISSION ULTRA-PRO (Sans validation de date facture obligatoire)
        handleSubmit(event) {
            // Empêcher double soumission
            if (this.isSubmitting) {
                event.preventDefault();
                return false;
            }

            // Validation basique
            if (!this.vehicleId) {
                this.showNotification('error', 'Veuillez sélectionner un véhicule');
                this.validateVehicle();
                event.preventDefault();
                return false;
            }

            if (!this.category) {
                this.showNotification('error', 'Veuillez sélectionner une catégorie');
                event.preventDefault();
                return false;
            }

            if (this.amountHT <= 0) {
                this.showNotification('error', 'Le montant HT doit être supérieur à 0');
                event.preventDefault();
                return false;
            }

            // ⚡ PAS DE VALIDATION pour invoice_date - c'est optionnel !
            // La date de facture n'est requise QUE si un fichier est uploadé
            // et cette validation est faite côté serveur

            // Si carburant, vérifier les champs spécifiques
            if (this.category === 'carburant') {
                const odometerInput = document.querySelector('input[name="odometer_reading"]');
                const fuelQuantityInput = document.querySelector('input[name="fuel_quantity"]');
                
                if (odometerInput && !odometerInput.value) {
                    this.showNotification('error', 'Le kilométrage est requis pour une dépense de carburant');
                    event.preventDefault();
                    return false;
                }
                
                if (fuelQuantityInput && !fuelQuantityInput.value) {
                    this.showNotification('error', 'La quantité de carburant est requise');
                    event.preventDefault();
                    return false;
                }
            }

            // Afficher loading
            this.isSubmitting = true;
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Soumettre le formulaire
            event.target.submit();
        }
    }
}
</script>
@endpush
@endsection
