<?php

namespace App\Http\Requests;

use App\Rules\ActiveSupplierInOrganization;
use Illuminate\Foundation\Http\FormRequest;

/**
 * ====================================================================
 * 🚀 VEHICLE EXPENSE REQUEST - ENTERPRISE ULTRA-PRO V1.0
 * ====================================================================
 * 
 * FormRequest pour validation des dépenses véhicules
 * avec messages en français et gestion avancée
 * 
 * @package App\Http\Requests
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
class VehicleExpenseRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête
     */
    public function authorize(): bool
    {
        return true; // La vérification des permissions est gérée dans le contrôleur
    }

    /**
     * Préparation des données avant validation
     * Nettoie les valeurs vides et convertit les types
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        // Mapper les anciennes catégories vers les nouvelles
        if (isset($data['expense_category'])) {
            $legacyMapping = config('expense_categories.legacy_mapping');
            if (isset($legacyMapping[$data['expense_category']])) {
                $data['expense_category'] = $legacyMapping[$data['expense_category']];
            }
        }

        // Nettoyer supplier_id si vide (convertir '' en null)
        if (isset($data['supplier_id']) && $data['supplier_id'] === '') {
            $data['supplier_id'] = null;
        }

        // Nettoyer expense_group_id si vide
        if (isset($data['expense_group_id']) && $data['expense_group_id'] === '') {
            $data['expense_group_id'] = null;
        }

        // Nettoyer driver_id si vide
        if (isset($data['driver_id']) && $data['driver_id'] === '') {
            $data['driver_id'] = null;
        }

        // ⚡ IMPORTANT: Convertir le format de date DD/MM/YYYY vers Y-m-d pour Laravel
        // Traiter expense_date
        if (isset($data['expense_date']) && $data['expense_date']) {
            $data['expense_date'] = $this->convertDateFormat($data['expense_date']);
        }

        // Traiter invoice_date
        if (isset($data['invoice_date']) && $data['invoice_date']) {
            $data['invoice_date'] = $this->convertDateFormat($data['invoice_date']);
        }

        // Traiter approval_deadline
        if (isset($data['approval_deadline']) && $data['approval_deadline']) {
            $data['approval_deadline'] = $this->convertDateFormat($data['approval_deadline']);
        }

        // Convertir les montants en nombres
        if (isset($data['amount_ht'])) {
            $data['amount_ht'] = str_replace(',', '.', $data['amount_ht']);
        }

        if (isset($data['tva_rate'])) {
            $data['tva_rate'] = $data['tva_rate'] === '' ? null : str_replace(',', '.', $data['tva_rate']);
        }

        if (isset($data['fuel_quantity'])) {
            $data['fuel_quantity'] = str_replace(',', '.', $data['fuel_quantity']);
        }

        if (isset($data['fuel_price_per_liter'])) {
            $data['fuel_price_per_liter'] = str_replace(',', '.', $data['fuel_price_per_liter']);
        }

        $this->merge($data);
    }

    /**
     * Convertir le format de date DD/MM/YYYY vers Y-m-d
     * 
     * @param string $date
     * @return string|null
     */
    private function convertDateFormat(string $date): ?string
    {
        // Vérifier si la date est déjà au bon format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Si la date est au format DD/MM/YYYY, la convertir
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];
            
            // Vérifier que la date est valide
            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "$year-$month-$day";
            }
        }
        
        // Essayer de parser avec DateTime pour plus de flexibilité
        try {
            $dateTime = \DateTime::createFromFormat('d/m/Y', $date);
            if ($dateTime !== false) {
                return $dateTime->format('Y-m-d');
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs et retourner la date originale
        }
        
        return $date; // Retourner la date originale si on ne peut pas la convertir
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        $isUpdate = $this->route('expense') !== null;

        // Récupérer les catégories valides depuis la configuration
        $validCategories = array_keys(config('expense_categories.categories'));

        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'supplier_id' => ['nullable', new ActiveSupplierInOrganization()],
            'driver_id' => 'nullable|exists:users,id',
            'expense_group_id' => 'nullable|exists:expense_groups,id',
            'expense_category' => 'required|string|in:' . implode(',', $validCategories),
            'expense_type' => 'required|string|max:100',
            'expense_subtype' => 'nullable|string|max:100',
            'amount_ht' => 'required|numeric|min:0|max:99999999',
            'tva_rate' => 'nullable|numeric|min:0|max:100',
            'tva_amount' => 'nullable|numeric|min:0',
            'total_ttc' => 'nullable|numeric|min:0',
            'invoice_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date|before_or_equal:today',
            'receipt_number' => 'nullable|string|max:100',
            'fiscal_receipt' => 'boolean',
            'payment_method' => 'nullable|in:especes,cheque,virement,carte,bon,credit',
            'payment_status' => 'nullable|in:pending,paid,partial',
            'payment_date' => 'nullable|date|before_or_equal:today',
            'odometer_reading' => 'nullable|integer|min:0|max:9999999',
            'fuel_quantity' => 'nullable|numeric|min:0|max:9999',
            'fuel_price_per_liter' => 'nullable|numeric|min:0|max:999',
            'fuel_type' => 'nullable|string|max:50',
            'expense_date' => 'required|date|before_or_equal:today',
            'description' => 'required|string|min:10|max:5000',
            'internal_notes' => 'nullable|string|max:5000',
            'needs_approval' => 'boolean',
            'priority_level' => 'nullable|in:low,normal,high,urgent',
            'is_urgent' => 'boolean',
            'approval_deadline' => 'nullable|date|after:today',
            'cost_center' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ];

        // Règles conditionnelles pour les dépenses de carburant
        if ($this->input('expense_category') === 'carburant') {
            $rules['odometer_reading'] = 'required|integer|min:0|max:9999999';
            $rules['fuel_quantity'] = 'required|numeric|min:0|max:9999';
            $rules['fuel_price_per_liter'] = 'required|numeric|min:0|max:999';
            $rules['fuel_type'] = 'required|string|max:50|in:essence,essence_super,essence_sans_plomb,gasoil,gpl,electrique,hybride';
        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés en français
     */
    public function messages(): array
    {
        return [
            // Vehicle
            'vehicle_id.required' => 'Vous devez sélectionner un véhicule.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas dans la base de données.',
            
            // Supplier
            'supplier_id.exists' => 'Le fournisseur sélectionné n\'existe pas ou n\'est plus actif.',
            
            // Driver
            'driver_id.exists' => 'Le chauffeur sélectionné n\'existe pas.',
            
            // Expense Group
            'expense_group_id.exists' => 'Le groupe de dépenses sélectionné n\'existe pas.',
            
            // Category & Type
            'expense_category.required' => 'La catégorie de dépense est obligatoire.',
            'expense_category.in' => 'La catégorie de dépense sélectionnée n\'est pas valide.',
            'expense_type.required' => 'Le type de dépense est obligatoire.',
            'expense_type.max' => 'Le type de dépense ne doit pas dépasser :max caractères.',
            
            // Amounts
            'amount_ht.required' => 'Le montant hors taxes est obligatoire.',
            'amount_ht.numeric' => 'Le montant HT doit être un nombre valide.',
            'amount_ht.min' => 'Le montant HT ne peut pas être négatif.',
            'amount_ht.max' => 'Le montant HT est trop élevé (max: 99 999 999 DA).',
            
            'tva_rate.numeric' => 'Le taux de TVA doit être un nombre.',
            'tva_rate.min' => 'Le taux de TVA ne peut pas être négatif.',
            'tva_rate.max' => 'Le taux de TVA ne peut pas dépasser 100%.',
            
            // Dates
            'expense_date.required' => 'La date de la dépense est obligatoire.',
            'expense_date.date' => 'La date de la dépense n\'est pas valide.',
            'expense_date.before_or_equal' => 'La date de la dépense ne peut pas être dans le futur.',
            
            'invoice_date.date' => 'La date de facture n\'est pas valide.',
            'invoice_date.before_or_equal' => 'La date de facture ne peut pas être dans le futur.',
            
            'approval_deadline.date' => 'La date limite d\'approbation n\'est pas valide.',
            'approval_deadline.after' => 'La date limite d\'approbation doit être dans le futur.',
            
            // Invoice & Receipt
            'invoice_number.max' => 'Le numéro de facture ne doit pas dépasser :max caractères.',
            'receipt_number.max' => 'Le numéro de reçu ne doit pas dépasser :max caractères.',
            
            // Payment
            'payment_method.in' => 'La méthode de paiement sélectionnée n\'est pas valide.',
            'payment_status.in' => 'Le statut de paiement sélectionné n\'est pas valide.',
            
            // Description
            'description.required' => 'Une description détaillée de la dépense est obligatoire.',
            'description.min' => 'La description doit contenir au moins :min caractères.',
            'description.max' => 'La description ne doit pas dépasser :max caractères.',
            
            'internal_notes.max' => 'Les notes internes ne doivent pas dépasser :max caractères.',
            
            // Fuel specific
            'odometer_reading.required' => 'Le kilométrage est obligatoire pour une dépense de carburant.',
            'odometer_reading.integer' => 'Le kilométrage doit être un nombre entier.',
            'odometer_reading.min' => 'Le kilométrage ne peut pas être négatif.',
            'odometer_reading.max' => 'Le kilométrage semble incorrect (max: 9 999 999 km).',
            
            'fuel_quantity.required' => 'La quantité de carburant est obligatoire.',
            'fuel_quantity.numeric' => 'La quantité de carburant doit être un nombre.',
            'fuel_quantity.min' => 'La quantité de carburant doit être supérieure à 0.',
            'fuel_quantity.max' => 'La quantité de carburant semble incorrecte (max: 9999 litres).',
            
            'fuel_price_per_liter.required' => 'Le prix par litre est obligatoire pour une dépense de carburant.',
            'fuel_price_per_liter.numeric' => 'Le prix par litre doit être un nombre.',
            'fuel_price_per_liter.min' => 'Le prix par litre doit être supérieur à 0.',
            'fuel_price_per_liter.max' => 'Le prix par litre semble incorrect (max: 999 DA).',
            
            'fuel_type.required' => 'Le type de carburant est obligatoire.',
            'fuel_type.in' => 'Le type de carburant sélectionné n\'est pas valide.',
            
            // Priority
            'priority_level.in' => 'Le niveau de priorité sélectionné n\'est pas valide.',
            
            // Cost Center
            'cost_center.max' => 'Le centre de coût ne doit pas dépasser :max caractères.',
            
            // Attachments
            'attachments.array' => 'Les pièces jointes doivent être un tableau de fichiers.',
            'attachments.*.file' => 'Chaque pièce jointe doit être un fichier valide.',
            'attachments.*.mimes' => 'Les pièces jointes doivent être de type : JPG, PNG, PDF, DOC ou DOCX.',
            'attachments.*.max' => 'Chaque pièce jointe ne doit pas dépasser 5 MB.',
        ];
    }

    /**
     * Noms d'attributs personnalisés
     */
    public function attributes(): array
    {
        return [
            'supplier_id' => 'fournisseur',
            'vehicle_id' => 'véhicule',
            'driver_id' => 'chauffeur',
            'expense_group_id' => 'groupe de dépenses',
            'expense_category' => 'catégorie',
            'expense_type' => 'type de dépense',
            'expense_subtype' => 'sous-type',
            'amount_ht' => 'montant HT',
            'tva_rate' => 'taux TVA',
            'tva_amount' => 'montant TVA',
            'total_ttc' => 'total TTC',
            'expense_date' => 'date de dépense',
            'description' => 'description',
            'invoice_number' => 'numéro de facture',
            'invoice_date' => 'date de facture',
            'receipt_number' => 'numéro de reçu',
            'payment_method' => 'mode de paiement',
            'payment_status' => 'statut de paiement',
            'odometer_reading' => 'kilométrage',
            'fuel_quantity' => 'quantité de carburant',
            'fuel_price_per_liter' => 'prix par litre',
            'fuel_type' => 'type de carburant',
            'internal_notes' => 'notes internes',
            'approval_deadline' => 'date limite d\'approbation',
            'priority_level' => 'niveau de priorité',
            'cost_center' => 'centre de coût',
            'attachments' => 'pièces jointes',
        ];
    }
}
