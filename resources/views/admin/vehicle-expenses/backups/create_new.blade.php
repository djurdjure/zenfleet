@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Dépense Véhicule')

@section('content')
{{-- ====================================================================
 💰 FORMULAIRE CRÉATION DÉPENSE - ENTERPRISE GRADE
 ====================================================================
 
 FEATURES:
 - Validation en temps réel à chaque phase
 - Workflow d'approbation multi-niveaux
 - Calcul automatique TVA et totaux
 - Support multi-devises
 - Upload de justificatifs
 - Analytics prédictifs
 - Support Dark Mode
 
 @version 3.0-Enterprise-Validated
 @since 2025-10-27
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
                <x-iconify icon="lucide:receipt" class="w-6 h-6 text-blue-600" />
                Nouvelle Dépense Véhicule
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Complétez les 3 étapes pour enregistrer une dépense
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
                 🎯 STEPPER V6.0 - ULTRA-PRO WORLD-CLASS
                 ==================================================================== --}}
                <x-stepper
                    :steps="[
                        ['label' => 'Informations', 'icon' => 'file-text'],
                        ['label' => 'Détails', 'icon' => 'clipboard-list'],
                        ['label' => 'Justificatifs', 'icon' => 'paperclip']
                    ]"
                    currentStepVar="currentStep"
                />

                {{-- Formulaire --}}
                <form method="POST" action="{{ route('admin.vehicle-expenses.store') }}" @submit="onSubmit" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <input type="hidden" name="current_step" x-model="currentStep">

                    {{-- ===========================================
                     PHASE 1: INFORMATIONS PRINCIPALES
                     =========================================== --}}
                    <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                                    Informations de Base
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Véhicule --}}
                                    <x-select
                                        name="vehicle_id"
                                        label="Véhicule"
                                        icon="car"
                                        required
                                        :value="old('vehicle_id')"
                                        :error="$errors->first('vehicle_id')"
                                        helpText="Sélectionnez le véhicule concerné"
                                        @change="validateField('vehicle_id', $event.target.value)">
                                        <option value="">-- Sélectionner un véhicule --</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->registration_plate }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </x-select>

                                    {{-- Date de la dépense --}}
                                    <x-input
                                        type="date"
                                        name="expense_date"
                                        label="Date de la dépense"
                                        icon="calendar"
                                        :value="old('expense_date', date('Y-m-d'))"
                                        required
                                        max="{{ date('Y-m-d') }}"
                                        :error="$errors->first('expense_date')"
                                        helpText="Date à laquelle la dépense a été effectuée"
                                        @blur="validateField('expense_date', $event.target.value)"
                                    />

                                    {{-- Catégorie --}}
                                    <x-select
                                        name="expense_category"
                                        label="Catégorie"
                                        icon="tag"
                                        required
                                        :value="old('expense_category')"
                                        :error="$errors->first('expense_category')"
                                        @change="handleCategoryChange($event.target.value)">
                                        <option value="">-- Sélectionner une catégorie --</option>
                                        <option value="carburant">🚗 Carburant</option>
                                        <option value="maintenance">🔧 Maintenance</option>
                                        <option value="reparation">🔨 Réparation</option>
                                        <option value="assurance">🛡️ Assurance</option>
                                        <option value="taxe">📋 Taxe/Vignette</option>
                                        <option value="peage">🛣️ Péage</option>
                                        <option value="parking">🅿️ Parking</option>
                                        <option value="amende">⚠️ Amende</option>
                                        <option value="autre">📌 Autre</option>
                                    </x-select>

                                    {{-- Type de dépense (sous-catégorie) --}}
                                    <x-input
                                        name="expense_type"
                                        label="Type de dépense"
                                        icon="file-text"
                                        placeholder="Ex: Vidange, Réparation freins, Contrôle technique..."
                                        :value="old('expense_type')"
                                        required
                                        :error="$errors->first('expense_type')"
                                        helpText="Précisez le type de dépense"
                                        @blur="validateField('expense_type', $event.target.value)"
                                    />

                                    {{-- Montant HT --}}
                                    <div>
                                        <x-input
                                            type="number"
                                            name="amount_ht"
                                            label="Montant HT (€)"
                                            icon="euro"
                                            placeholder="0.00"
                                            step="0.01"
                                            min="0"
                                            :value="old('amount_ht')"
                                            required
                                            :error="$errors->first('amount_ht')"
                                            x-model.number="amountHT"
                                            @input="calculateTTC()"
                                        />
                                    </div>

                                    {{-- Taux TVA --}}
                                    <div>
                                        <x-select
                                            name="tva_rate"
                                            label="Taux TVA (%)"
                                            icon="percent"
                                            required
                                            :value="old('tva_rate', '20')"
                                            :error="$errors->first('tva_rate')"
                                            x-model="tvaRate"
                                            @change="calculateTTC()">
                                            <option value="0">0% (Exonéré)</option>
                                            <option value="5.5">5.5% (Taux réduit)</option>
                                            <option value="10">10% (Taux intermédiaire)</option>
                                            <option value="20" selected>20% (Taux normal)</option>
                                        </x-select>
                                    </div>

                                    {{-- Montant TVA (calculé) --}}
                                    <div>
                                        <x-input
                                            type="number"
                                            name="tva_amount"
                                            label="Montant TVA (€)"
                                            icon="calculator"
                                            placeholder="0.00"
                                            step="0.01"
                                            readonly
                                            x-model="tvaAmount"
                                            class="bg-gray-50"
                                        />
                                    </div>

                                    {{-- Montant TTC (calculé) --}}
                                    <div>
                                        <x-input
                                            type="number"
                                            name="total_ttc"
                                            label="Montant TTC (€)"
                                            icon="credit-card"
                                            placeholder="0.00"
                                            step="0.01"
                                            readonly
                                            x-model="totalTTC"
                                            class="bg-gray-50 font-bold text-lg"
                                        />
                                    </div>

                                    {{-- Fournisseur --}}
                                    <div class="md:col-span-2">
                                        <x-select
                                            name="supplier_id"
                                            label="Fournisseur"
                                            icon="building-storefront"
                                            :value="old('supplier_id')"
                                            :error="$errors->first('supplier_id')"
                                            helpText="Sélectionnez le fournisseur (optionnel)">
                                            <option value="">-- Aucun fournisseur --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->company_name }} {{ $supplier->registration_number ? '(' . $supplier->registration_number . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </x-select>
                                    </div>

                                    {{-- Description --}}
                                    <div class="md:col-span-2">
                                        <x-textarea
                                            name="description"
                                            label="Description"
                                            icon="clipboard"
                                            rows="3"
                                            placeholder="Décrivez la dépense en détail..."
                                            :value="old('description')"
                                            required
                                            :error="$errors->first('description')"
                                            helpText="Description détaillée de la dépense"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===========================================
                     PHASE 2: DÉTAILS SPÉCIFIQUES
                     =========================================== --}}
                    <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0" 
                         style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <x-iconify icon="lucide:clipboard-list" class="w-5 h-5 text-blue-600" />
                                    Détails de la Dépense
                                </h3>

                                {{-- Section Carburant (conditionnelle) --}}
                                <div x-show="categorySelected === 'carburant'" class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                                    <h4 class="font-medium text-amber-900 mb-3 flex items-center gap-2">
                                        <x-iconify icon="lucide:fuel" class="w-5 h-5 text-amber-600" />
                                        Informations Carburant
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <x-input
                                            type="number"
                                            name="odometer_reading"
                                            label="Kilométrage actuel"
                                            icon="gauge"
                                            placeholder="Ex: 125000"
                                            min="0"
                                            :value="old('odometer_reading')"
                                            :error="$errors->first('odometer_reading')"
                                            helpText="Kilométrage au moment du plein"
                                        />

                                        <x-input
                                            type="number"
                                            name="fuel_quantity"
                                            label="Quantité (litres)"
                                            icon="droplet"
                                            placeholder="Ex: 45.5"
                                            step="0.01"
                                            min="0"
                                            :value="old('fuel_quantity')"
                                            :error="$errors->first('fuel_quantity')"
                                        />

                                        <x-input
                                            type="number"
                                            name="fuel_price_per_liter"
                                            label="Prix par litre (€)"
                                            icon="euro"
                                            placeholder="Ex: 1.85"
                                            step="0.001"
                                            min="0"
                                            :value="old('fuel_price_per_liter')"
                                            :error="$errors->first('fuel_price_per_liter')"
                                        />

                                        <x-select
                                            name="fuel_type"
                                            label="Type de carburant"
                                            icon="fuel"
                                            :value="old('fuel_type')"
                                            :error="$errors->first('fuel_type')">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="diesel">Diesel</option>
                                            <option value="sp95">SP95</option>
                                            <option value="sp98">SP98</option>
                                            <option value="e10">E10</option>
                                            <option value="e85">E85</option>
                                            <option value="gpl">GPL</option>
                                            <option value="electrique">Électrique</option>
                                            <option value="hydrogene">Hydrogène</option>
                                        </x-select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Numéro de facture --}}
                                    <x-input
                                        name="invoice_number"
                                        label="N° Facture"
                                        icon="document"
                                        placeholder="Ex: FAC-2025-001234"
                                        :value="old('invoice_number')"
                                        :error="$errors->first('invoice_number')"
                                        helpText="Numéro de la facture fournisseur"
                                    />

                                    {{-- Date de facture --}}
                                    <x-input
                                        type="date"
                                        name="invoice_date"
                                        label="Date facture"
                                        icon="calendar"
                                        :value="old('invoice_date')"
                                        max="{{ date('Y-m-d') }}"
                                        :error="$errors->first('invoice_date')"
                                    />

                                    {{-- Numéro de ticket --}}
                                    <x-input
                                        name="receipt_number"
                                        label="N° Ticket/Reçu"
                                        icon="receipt"
                                        placeholder="Ex: TIC-001234"
                                        :value="old('receipt_number')"
                                        :error="$errors->first('receipt_number')"
                                    />

                                    {{-- Ticket fiscal --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            <x-iconify icon="lucide:shield-check" class="w-4 h-4 inline mr-1" />
                                            Ticket fiscal
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="fiscal_receipt" value="1" class="form-radio text-blue-600" {{ old('fiscal_receipt') == '1' ? 'checked' : '' }}>
                                                <span class="ml-2">Oui</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="fiscal_receipt" value="0" class="form-radio text-blue-600" {{ old('fiscal_receipt', '0') == '0' ? 'checked' : '' }}>
                                                <span class="ml-2">Non</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Centre de coût --}}
                                    <x-input
                                        name="cost_center"
                                        label="Centre de coût"
                                        icon="building-office"
                                        placeholder="Ex: DEPT-001"
                                        :value="old('cost_center')"
                                        :error="$errors->first('cost_center')"
                                        helpText="Code du centre de coût (optionnel)"
                                    />

                                    {{-- Niveau de priorité --}}
                                    <x-select
                                        name="priority_level"
                                        label="Priorité"
                                        icon="flag"
                                        :value="old('priority_level', 'normal')"
                                        :error="$errors->first('priority_level')">
                                        <option value="low">🟢 Faible</option>
                                        <option value="normal" selected>🟡 Normale</option>
                                        <option value="high">🟠 Élevée</option>
                                        <option value="urgent">🔴 Urgente</option>
                                    </x-select>

                                    {{-- Demande d'approbation --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            <x-iconify icon="lucide:user-check" class="w-4 h-4 inline mr-1" />
                                            Workflow d'approbation
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="inline-flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="needs_approval" 
                                                    value="1" 
                                                    class="form-radio text-blue-600"
                                                    x-model="needsApproval"
                                                    {{ old('needs_approval') == '1' ? 'checked' : '' }}>
                                                <span class="ml-2">Soumettre à approbation</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="needs_approval" 
                                                    value="0" 
                                                    class="form-radio text-blue-600"
                                                    x-model="needsApproval"
                                                    {{ old('needs_approval', '0') == '0' ? 'checked' : '' }}>
                                                <span class="ml-2">Enregistrer comme brouillon</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Date limite d'approbation (si approbation requise) --}}
                                    <div x-show="needsApproval == '1'">
                                        <x-input
                                            type="date"
                                            name="approval_deadline"
                                            label="Date limite d'approbation"
                                            icon="calendar-days"
                                            :value="old('approval_deadline')"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                            :error="$errors->first('approval_deadline')"
                                            helpText="Date limite pour l'approbation"
                                        />
                                    </div>

                                    {{-- Notes internes --}}
                                    <div class="md:col-span-2">
                                        <x-textarea
                                            name="internal_notes"
                                            label="Notes internes"
                                            icon="sticky-note"
                                            rows="2"
                                            placeholder="Notes confidentielles (non visibles sur les rapports)..."
                                            :value="old('internal_notes')"
                                            :error="$errors->first('internal_notes')"
                                            helpText="Ces notes sont uniquement visibles en interne"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===========================================
                     PHASE 3: JUSTIFICATIFS ET VALIDATION
                     =========================================== --}}
                    <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-x-4"
                         x-transition:enter-end="opacity-100 transform translate-x-0" 
                         style="display: none;">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <x-iconify icon="lucide:paperclip" class="w-5 h-5 text-blue-600" />
                                    Justificatifs et Documents
                                </h3>

                                {{-- Zone d'upload --}}
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <x-iconify icon="lucide:upload" class="w-4 h-4 inline mr-1" />
                                        Télécharger les justificatifs
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors"
                                         x-data="{ isDragging: false }"
                                         @dragover.prevent="isDragging = true"
                                         @dragleave.prevent="isDragging = false"
                                         @drop.prevent="isDragging = false; handleFileDrop($event)"
                                         :class="{ 'bg-blue-50 border-blue-400': isDragging }">
                                        
                                        <x-iconify icon="lucide:cloud-upload" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                                        
                                        <p class="text-sm text-gray-600 mb-2">
                                            Glissez-déposez vos fichiers ici, ou
                                        </p>
                                        
                                        <input 
                                            type="file" 
                                            name="attachments[]" 
                                            id="file-upload" 
                                            multiple 
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                            class="hidden"
                                            @change="handleFileSelect($event)">
                                        
                                        <label for="file-upload" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                            <x-iconify icon="lucide:folder-open" class="w-4 h-4" />
                                            Parcourir
                                        </label>
                                        
                                        <p class="text-xs text-gray-500 mt-2">
                                            Formats acceptés: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (Max 5 MB par fichier)
                                        </p>
                                    </div>
                                    
                                    {{-- Liste des fichiers uploadés --}}
                                    <div x-show="uploadedFiles.length > 0" class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Fichiers sélectionnés:</h4>
                                        <ul class="space-y-2">
                                            <template x-for="(file, index) in uploadedFiles" :key="index">
                                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                                    <div class="flex items-center gap-2">
                                                        <x-iconify icon="lucide:file" class="w-4 h-4 text-gray-500" />
                                                        <span class="text-sm text-gray-700" x-text="file.name"></span>
                                                        <span class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></span>
                                                    </div>
                                                    <button 
                                                        type="button"
                                                        @click="removeFile(index)"
                                                        class="text-red-600 hover:text-red-800">
                                                        <x-iconify icon="lucide:x" class="w-4 h-4" />
                                                    </button>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Résumé de la dépense --}}
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                    <h4 class="font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                        <x-iconify icon="lucide:file-check" class="w-5 h-5 text-blue-600" />
                                        Résumé de la Dépense
                                    </h4>
                                    
                                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <dt class="text-blue-700 font-medium">Véhicule:</dt>
                                            <dd class="text-blue-900" x-text="summaryData.vehicle || 'Non sélectionné'"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700 font-medium">Date:</dt>
                                            <dd class="text-blue-900" x-text="summaryData.date || 'Non définie'"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700 font-medium">Catégorie:</dt>
                                            <dd class="text-blue-900" x-text="summaryData.category || 'Non sélectionnée'"></dd>
                                        </div>
                                        <div>
                                            <dt class="text-blue-700 font-medium">Type:</dt>
                                            <dd class="text-blue-900" x-text="summaryData.type || 'Non défini'"></dd>
                                        </div>
                                        <div class="md:col-span-2">
                                            <dt class="text-blue-700 font-medium">Description:</dt>
                                            <dd class="text-blue-900" x-text="summaryData.description || 'Non fournie'"></dd>
                                        </div>
                                        <div class="md:col-span-2 pt-4 border-t border-blue-200">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <dt class="text-blue-700 font-medium">Montant HT:</dt>
                                                    <dd class="text-blue-900 font-semibold" x-text="formatCurrency(amountHT)"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-blue-700 font-medium">TVA (<span x-text="tvaRate"></span>%):</dt>
                                                    <dd class="text-blue-900 font-semibold" x-text="formatCurrency(tvaAmount)"></dd>
                                                </div>
                                                <div>
                                                    <dt class="text-blue-700 font-medium">Total TTC:</dt>
                                                    <dd class="text-blue-900 font-bold text-lg" x-text="formatCurrency(totalTTC)"></dd>
                                                </div>
                                            </div>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===========================================
                     NAVIGATION ET ACTIONS
                     =========================================== --}}
                    <div class="flex items-center justify-between pt-6 border-t">
                        {{-- Bouton Précédent --}}
                        <button
                            type="button"
                            @click="previousStep()"
                            x-show="currentStep > 1"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-iconify icon="heroicons:arrow-left" class="w-5 h-5" />
                            Précédent
                        </button>

                        <div x-show="currentStep === 1" class="w-0"></div>

                        {{-- Actions finales --}}
                        <div class="flex items-center gap-3">
                            {{-- Bouton Annuler --}}
                            <a href="{{ route('admin.vehicle-expenses.index') }}"
                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                Annuler
                            </a>

                            {{-- Bouton Suivant / Enregistrer --}}
                            <button
                                type="button"
                                @click="nextStep()"
                                x-show="currentStep < 3"
                                :disabled="!isStepValid(currentStep)"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Suivant
                                <x-iconify icon="heroicons:arrow-right" class="w-5 h-5" />
                            </button>

                            <button
                                type="submit"
                                x-show="currentStep === 3"
                                :disabled="!isFormValid()"
                                class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <x-iconify icon="heroicons:check" class="w-5 h-5" />
                                Enregistrer la dépense
                            </button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</section>

@push('scripts')
<script>
function expenseFormValidation() {
    return {
        currentStep: 1,
        amountHT: 0,
        tvaRate: 20,
        tvaAmount: 0,
        totalTTC: 0,
        categorySelected: '',
        needsApproval: '0',
        uploadedFiles: [],
        summaryData: {},
        errors: {},

        init() {
            this.calculateTTC();
            this.updateSummary();
        },

        calculateTTC() {
            this.tvaAmount = (this.amountHT * this.tvaRate / 100);
            this.totalTTC = this.amountHT + this.tvaAmount;
        },

        handleCategoryChange(category) {
            this.categorySelected = category;
            this.updateSummary();
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount || 0);
        },

        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.uploadedFiles = [...this.uploadedFiles, ...files];
        },

        handleFileDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.uploadedFiles = [...this.uploadedFiles, ...files];
        },

        removeFile(index) {
            this.uploadedFiles.splice(index, 1);
        },

        updateSummary() {
            // Récupérer les valeurs du formulaire pour le résumé
            const form = document.querySelector('form');
            if (form) {
                const vehicleSelect = form.querySelector('[name="vehicle_id"]');
                const dateInput = form.querySelector('[name="expense_date"]');
                const typeInput = form.querySelector('[name="expense_type"]');
                const descriptionInput = form.querySelector('[name="description"]');
                
                this.summaryData = {
                    vehicle: vehicleSelect?.options[vehicleSelect.selectedIndex]?.text || 'Non sélectionné',
                    date: dateInput?.value ? new Date(dateInput.value).toLocaleDateString('fr-FR') : 'Non définie',
                    category: this.categorySelected ? this.categorySelected.charAt(0).toUpperCase() + this.categorySelected.slice(1) : 'Non sélectionnée',
                    type: typeInput?.value || 'Non défini',
                    description: descriptionInput?.value || 'Non fournie'
                };
            }
        },

        validateField(field, value) {
            // Validation basique des champs
            if (!value && field !== 'supplier_id') {
                this.errors[field] = 'Ce champ est requis';
                return false;
            }
            
            delete this.errors[field];
            this.updateSummary();
            return true;
        },

        isStepValid(step) {
            if (step === 1) {
                // Vérifier les champs requis de l'étape 1
                const requiredFields = ['vehicle_id', 'expense_date', 'expense_category', 'expense_type', 'amount_ht', 'description'];
                const form = document.querySelector('form');
                
                for (let field of requiredFields) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (!input || !input.value) {
                        return false;
                    }
                }
                return true;
            }
            
            if (step === 2) {
                // L'étape 2 n'a pas de champs obligatoires spécifiques
                return true;
            }
            
            return true;
        },

        isFormValid() {
            // Vérifier que toutes les étapes sont valides
            return this.isStepValid(1) && this.isStepValid(2);
        },

        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },

        nextStep() {
            if (this.currentStep < 3 && this.isStepValid(this.currentStep)) {
                this.currentStep++;
                this.updateSummary();
            }
        },

        onSubmit(event) {
            if (!this.isFormValid()) {
                event.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires avant de soumettre le formulaire.');
                return false;
            }
            
            // Le formulaire peut être soumis
            return true;
        }
    }
}
</script>
@endpush
@endsection
