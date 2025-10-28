<?php

namespace App\Rules;

use App\Models\Supplier;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

/**
 * ====================================================================
 * 🚀 RÈGLE DE VALIDATION - FOURNISSEUR ACTIF DANS L'ORGANISATION
 * ====================================================================
 * 
 * Vérifie que le fournisseur :
 * - Existe dans la base de données
 * - Appartient à la même organisation que l'utilisateur
 * - Est actif (is_active = true)
 * 
 * @package App\Rules
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
class ActiveSupplierInOrganization implements Rule
{
    /**
     * ID de l'organisation de l'utilisateur actuel
     */
    protected int $organizationId;

    /**
     * Message d'erreur spécifique
     */
    protected string $errorMessage;

    /**
     * Constructeur
     * 
     * @param int|null $organizationId ID de l'organisation (optionnel, utilise l'utilisateur actuel par défaut)
     */
    public function __construct(?int $organizationId = null)
    {
        $this->organizationId = $organizationId ?? Auth::user()->organization_id ?? 0;
        $this->errorMessage = 'Le fournisseur sélectionné n\'existe pas ou n\'est plus actif.';
    }

    /**
     * Détermine si la règle de validation passe
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // Si la valeur est vide, c'est valide (supplier_id est optionnel)
        if (empty($value)) {
            return true;
        }

        // Vérifier que l'organisation est définie
        if (!$this->organizationId) {
            $this->errorMessage = 'Impossible de vérifier le fournisseur : organisation non définie.';
            return false;
        }

        // Rechercher le fournisseur
        $supplier = Supplier::find($value);

        // Vérifier que le fournisseur existe
        if (!$supplier) {
            $this->errorMessage = 'Le fournisseur sélectionné n\'existe pas dans la base de données.';
            return false;
        }

        // Vérifier que le fournisseur appartient à la même organisation
        if ($supplier->organization_id != $this->organizationId) {
            $this->errorMessage = 'Le fournisseur sélectionné n\'appartient pas à votre organisation.';
            return false;
        }

        // Vérifier que le fournisseur est actif
        if (!$supplier->is_active) {
            $this->errorMessage = 'Le fournisseur sélectionné n\'est plus actif. Veuillez en choisir un autre.';
            return false;
        }

        return true;
    }

    /**
     * Obtenir le message d'erreur de validation
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
