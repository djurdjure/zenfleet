@extends('layouts.admin.catalyst')

@section('title', 'Nouvelle Dépense Véhicule')

@push('styles')
<style>
/* Styles pour TomSelect */
.ts-control {
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    padding: 0.5rem 0.75rem !important;
    min-height: 42px !important;
}
.ts-control:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}
.ts-dropdown {
    border-radius: 0.5rem !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}
.amount-input {
    text-align: right;
    font-weight: 600;
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
.total-display {
    background: linear-gradient(to right, #eff6ff, #f0f9ff);
    border: 2px solid #3b82f6;
}
</style>
@endpush

@section('content')
{{-- ====================================================================
 💰 FORMULAIRE CRÉATION DÉPENSE - ENTERPRISE ULTRA-PRO V2.0
 ====================================================================
 
 FEATURES AVANCÉES:
 - TomSelect pour sélection véhicules/fournisseurs
 - Monnaie locale DZD (Dinar Algérien)
 - TVA optionnelle avec calcul automatique
 - Fournisseur optionnel
 - Validation temps réel Alpine.js
 - Design cohérent avec components standards
 
 @version 2.0-Enterprise-Algeria
 @since 2025-10-28
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

<section class="bg-gray-50 min-h-screen">
    <div class="py-6 px-4 mx-auto max-w-7xl lg:py-12">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2.5">
                <x-iconify icon="lucide:receipt" class="w-6 h-6 text-blue-600" />
                Nouvelle Dépense Véhicule
            </h1>
            <p class="text-sm text-gray-600 ml-8.5">
                Enregistrez une nouvelle dépense pour votre flotte
            </p>
        </div>

        {{-- Affichage des erreurs globales --}}
        @if ($errors->any())
            <x-alert type="error" title="Erreurs de validation" dismissible class="mb-6">
                Veuillez corriger les erreurs suivantes :
                <ul class="mt-2 ml-5 list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        {{-- Formulaire principal --}}
        <div x-data="expenseFormHandler()" x-init="init()">
            <form method="POST" action="{{ route('admin.vehicle-expenses.store') }}" 
                  enctype="multipart/form-data" 
                  @submit.prevent="submitForm">
                @csrf

                {{-- Card principale --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    
                    {{-- Section 1: Informations de base --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <x-iconify icon="lucide:info" class="w-5 h-5 text-blue-600" />
                            Informations générales
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Véhicule avec TomSelect --}}
                            <div>
                                <x-tom-select
                                    name="vehicle_id"
                                    label="Véhicule"
                                    :options="$vehicles->pluck('registration_plate', 'id')->map(function($plate, $id) use ($vehicles) {
                                        $vehicle = $vehicles->find($id);
                                        return $plate . ' - ' . $vehicle->brand . ' ' . $vehicle->model;
                                    })->toArray()"
                                    placeholder="Rechercher un véhicule..."
                                    required
                                    :value="old('vehicle_id')"
                                    :error="$errors->first('vehicle_id')"
                                    helpText="Sélectionnez le véhicule concerné"
                                    x-model="vehicleId"
                                    @change="onVehicleChange"
                                />
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

                            {{-- Fournisseur avec TomSelect (optionnel) --}}
                            <div>
                                <x-tom-select
                                    name="supplier_id"
                                    label="Fournisseur"
                                    :options="array_merge(
                                        ['' => '-- Aucun fournisseur / Dépense occasionnelle --'],
                                        $suppliers->pluck('company_name', 'id')->map(function($name, $id) use ($suppliers) {
                                            $supplier = $suppliers->find($id);
                                            $type = $supplier->supplier_type_label ?? '';
                                            return $name . ($type ? ' (' . $type . ')' : '');
                                        })->toArray()
                                    )"
                                    placeholder="Rechercher un fournisseur (optionnel)..."
                                    :value="old('supplier_id')"
                                    :error="$errors->first('supplier_id')"
                                    helpText="Laissez vide pour une dépense occasionnelle"
                                    x-model="supplierId"
                                />
                            </div>

                            {{-- Catégorie de dépense --}}
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
                                            'autre' => '📌 Autre'
                                        ]
                                    ]"
                                    placeholder="-- Choisir une catégorie --"
                                    required
                                    :value="old('expense_category')"
                                    :error="$errors->first('expense_category')"
                                    helpText="Sélectionnez la catégorie qui correspond le mieux à votre dépense"
                                    emptyMessage="Veuillez sélectionner une catégorie de dépense"
                                    icon="lucide:layers"
                                    x-model="category"
                                    @change="onCategoryChange"
                                />
                            </div>

                            {{-- Type/Description de dépense --}}
                            <div class="md:col-span-2">
                                <x-input
                                    name="expense_type"
                                    label="Description de la dépense"
                                    icon="file-text"
                                    placeholder="Ex: Vidange, Changement de pneus, Réparation freins..."
                                    :value="old('expense_type')"
                                    required
                                    :error="$errors->first('expense_type')"
                                    helpText="Décrivez précisément la nature de la dépense"
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Montants et TVA --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <x-iconify icon="lucide:calculator" class="w-5 h-5 text-green-600" />
                            Montants et fiscalité
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Montant HT --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Montant HT <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="amount_ht"
                                        id="amount_ht"
                                        placeholder="0.00"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('amount_ht') }}"
                                        required
                                        x-model.number="amountHT"
                                        @input="calculateTTC"
                                        class="amount-input block w-full pr-12 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount_ht') border-red-500 @enderror"
                                    />
                                    <span class="currency-symbol">DA</span>
                                </div>
                                @error('amount_ht')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Taux TVA (optionnel) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Taux TVA (%)
                                    <span class="text-xs text-gray-500 ml-1">(optionnel)</span>
                                </label>
                                <input
                                    type="number"
                                    name="tva_rate"
                                    id="tva_rate"
                                    placeholder="Ex: 19"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    value="{{ old('tva_rate') }}"
                                    x-model.number="tvaRate"
                                    @input="calculateTTC"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Laissez vide si pas de TVA (fournisseur non assujetti)
                                </p>
                            </div>

                            {{-- Montant TVA (calculé) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Montant TVA
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="tva_amount"
                                        id="tva_amount"
                                        step="0.01"
                                        readonly
                                        x-model="tvaAmount"
                                        class="amount-input block w-full pr-12 bg-gray-50 border-gray-300 rounded-lg cursor-not-allowed"
                                    />
                                    <span class="currency-symbol">DA</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Calculé automatiquement</p>
                            </div>
                        </div>

                        {{-- Total TTC --}}
                        <div class="mt-6">
                            <div class="total-display rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Total TTC</label>
                                        <p class="text-xs text-gray-600 mt-0.5">Montant total à payer</p>
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

                    {{-- Section 3: Informations carburant (conditionnelle) --}}
                    <div x-show="category === 'carburant'" x-transition class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <x-iconify icon="lucide:fuel" class="w-5 h-5 text-amber-600" />
                            Informations carburant
                        </h3>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
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
                                </div>

                                <div>
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
                                </div>

                                <div>
                                    <x-input
                                        type="number"
                                        name="fuel_price_per_liter"
                                        label="Prix par litre (DA)"
                                        icon="tag"
                                        placeholder="Ex: 45.00"
                                        step="0.01"
                                        min="0"
                                        :value="old('fuel_price_per_liter')"
                                        :error="$errors->first('fuel_price_per_liter')"
                                    />
                                </div>

                                <div>
                                    <x-select
                                        name="fuel_type"
                                        label="Type de carburant"
                                        :options="[
                                            '' => '-- Sélectionner --',
                                            'essence' => 'Essence',
                                            'essence_super' => 'Essence Super',
                                            'essence_sans_plomb' => 'Essence Sans Plomb',
                                            'gasoil' => 'Gasoil',
                                            'gpl' => 'GPL',
                                            'electrique' => 'Électrique',
                                            'hybride' => 'Hybride'
                                        ]"
                                        :value="old('fuel_type')"
                                        :error="$errors->first('fuel_type')"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Documents et justificatifs --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                            <x-iconify icon="lucide:paperclip" class="w-5 h-5 text-purple-600" />
                            Documents et justificatifs
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Numéro de facture --}}
                            <div>
                                <x-input
                                    name="invoice_number"
                                    label="N° Facture"
                                    icon="document"
                                    placeholder="Ex: FAC-2025-001234"
                                    :value="old('invoice_number')"
                                    :error="$errors->first('invoice_number')"
                                />
                            </div>

                            {{-- Date de facture --}}
                            <div>
                                <x-datepicker-pro
                                    name="invoice_date"
                                    label="Date facture"
                                    placeholder="JJ/MM/AAAA"
                                    :value="old('invoice_date')"
                                    :maxDate="date('Y-m-d')"
                                    :error="$errors->first('invoice_date')"
                                    :defaultToday="false"
                                    helpText="Date de la facture (optionnel)"
                                />
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
                                        'bon' => '🎫 Bon d\'achat',
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

                            {{-- Upload de justificatifs --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Justificatifs
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
                                     x-data="{ isDragging: false }"
                                     @dragover.prevent="isDragging = true"
                                     @dragleave.prevent="isDragging = false"
                                     @drop.prevent="isDragging = false; handleFileDrop($event)"
                                     :class="{ 'bg-blue-50 border-blue-400': isDragging }">
                                    
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
                                    
                                    <p class="text-xs text-gray-500 mt-2">
                                        PDF, JPG, PNG, DOC (Max 5 MB par fichier)
                                    </p>
                                </div>
                                
                                {{-- Liste des fichiers uploadés --}}
                                <div x-show="uploadedFiles.length > 0" class="mt-3">
                                    <template x-for="(file, index) in uploadedFiles" :key="index">
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg mb-2">
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
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Notes/Description détaillée --}}
                            <div class="md:col-span-2">
                                <x-textarea
                                    name="description"
                                    label="Notes / Description détaillée"
                                    rows="3"
                                    placeholder="Ajoutez des détails supplémentaires sur cette dépense..."
                                    :value="old('description')"
                                    required
                                    :error="$errors->first('description')"
                                    helpText="Minimum 10 caractères"
                                />
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.vehicle-expenses.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <x-iconify icon="lucide:arrow-left" class="w-5 h-5" />
                            Annuler
                        </a>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                name="action"
                                value="draft"
                                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <x-iconify icon="lucide:save" class="w-5 h-5" />
                                Enregistrer comme brouillon
                            </button>

                            <button
                                type="submit"
                                name="action"
                                value="submit"
                                class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <x-iconify icon="lucide:check" class="w-5 h-5" />
                                Enregistrer la dépense
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
function expenseFormHandler() {
    return {
        // Données du formulaire
        vehicleId: '',
        supplierId: '',
        category: '',
        amountHT: 0,
        tvaRate: null,
        tvaAmount: 0,
        totalTTC: 0,
        uploadedFiles: [],

        init() {
            // Initialiser avec les anciennes valeurs si présentes
            this.amountHT = {{ old('amount_ht', 0) }};
            this.tvaRate = {{ old('tva_rate') ?: 'null' }};
            this.category = '{{ old('expense_category', '') }}';
            
            // Calculer les montants initiaux
            this.calculateTTC();
        },

        // Calcul automatique de la TVA et du TTC
        calculateTTC() {
            // Si pas de montant HT, tout à zéro
            if (!this.amountHT || this.amountHT <= 0) {
                this.tvaAmount = 0;
                this.totalTTC = 0;
                return;
            }

            // Si TVA renseignée, calculer
            if (this.tvaRate !== null && this.tvaRate !== '' && this.tvaRate >= 0) {
                this.tvaAmount = (this.amountHT * this.tvaRate / 100);
                this.totalTTC = this.amountHT + this.tvaAmount;
            } else {
                // Pas de TVA
                this.tvaAmount = 0;
                this.totalTTC = this.amountHT;
            }

            // Arrondir à 2 décimales
            this.tvaAmount = Math.round(this.tvaAmount * 100) / 100;
            this.totalTTC = Math.round(this.totalTTC * 100) / 100;
        },

        // Formater en monnaie
        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-DZ', {
                style: 'currency',
                currency: 'DZD',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0).replace('DZD', 'DA');
        },

        // Formater la taille de fichier
        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        // Gestion des fichiers
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            this.addFiles(files);
        },

        handleFileDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.addFiles(files);
        },

        addFiles(files) {
            // Vérifier la taille et le type
            const validFiles = files.filter(file => {
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 
                                   'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!validTypes.includes(file.type)) {
                    alert(`Le fichier ${file.name} n'est pas d'un type accepté`);
                    return false;
                }
                
                if (file.size > maxSize) {
                    alert(`Le fichier ${file.name} dépasse la taille maximale de 5MB`);
                    return false;
                }
                
                return true;
            });
            
            this.uploadedFiles = [...this.uploadedFiles, ...validFiles];
        },

        removeFile(index) {
            this.uploadedFiles.splice(index, 1);
        },

        // Événements
        onVehicleChange() {
            console.log('Véhicule sélectionné:', this.vehicleId);
        },

        onCategoryChange() {
            console.log('Catégorie sélectionnée:', this.category);
        },

        // Soumission du formulaire
        submitForm(event) {
            // Validation basique
            if (!this.vehicleId) {
                alert('Veuillez sélectionner un véhicule');
                event.preventDefault();
                return false;
            }

            if (!this.category) {
                alert('Veuillez sélectionner une catégorie');
                event.preventDefault();
                return false;
            }

            if (this.amountHT <= 0) {
                alert('Le montant HT doit être supérieur à 0');
                event.preventDefault();
                return false;
            }

            // Si carburant, vérifier les champs spécifiques
            if (this.category === 'carburant') {
                const odometerInput = document.querySelector('input[name="odometer_reading"]');
                const fuelQuantityInput = document.querySelector('input[name="fuel_quantity"]');
                
                if (odometerInput && !odometerInput.value) {
                    alert('Le kilométrage est requis pour une dépense de carburant');
                    event.preventDefault();
                    return false;
                }
                
                if (fuelQuantityInput && !fuelQuantityInput.value) {
                    alert('La quantité de carburant est requise');
                    event.preventDefault();
                    return false;
                }
            }

            // Soumettre le formulaire
            event.target.submit();
        }
    }
}
</script>
@endpush
@endsection
