<?php

namespace App\Enums;

/**
 * 🔐 ENUMÉRATION DES PERMISSIONS - ENTERPRISE-GRADE
 *
 * Centralise toutes les permissions de l'application pour éviter
 * les fautes de frappe et améliorer l'auto-complétion IDE
 *
 * @package App\Enums
 * @author ZenFleet Enterprise System
 */
enum Permission: string
{
    // === ORGANISATIONS ===
    case VIEW_ORGANIZATIONS = 'view organizations';
    case CREATE_ORGANIZATIONS = 'create organizations';
    case EDIT_ORGANIZATIONS = 'edit organizations';
    case DELETE_ORGANIZATIONS = 'delete organizations';

    // === GESTION DES RÔLES ===
    case MANAGE_ROLES = 'manage roles';

    // === UTILISATEURS ===
    case VIEW_USERS = 'view users';
    case CREATE_USERS = 'create users';
    case EDIT_USERS = 'edit users';
    case DELETE_USERS = 'delete users';

    // === VÉHICULES ===
    case VIEW_VEHICLES = 'view vehicles';
    case CREATE_VEHICLES = 'create vehicles';
    case EDIT_VEHICLES = 'edit vehicles';
    case DELETE_VEHICLES = 'delete vehicles';
    case RESTORE_VEHICLES = 'restore vehicles';
    case FORCE_DELETE_VEHICLES = 'force delete vehicles';

    // === CHAUFFEURS ===
    case VIEW_DRIVERS = 'view drivers';
    case CREATE_DRIVERS = 'create drivers';
    case EDIT_DRIVERS = 'edit drivers';
    case DELETE_DRIVERS = 'delete drivers';
    case RESTORE_DRIVERS = 'restore drivers';
    case FORCE_DELETE_DRIVERS = 'force delete drivers';

    // === AFFECTATIONS ===
    case VIEW_ASSIGNMENTS = 'view assignments';
    case CREATE_ASSIGNMENTS = 'create assignments';
    case EDIT_ASSIGNMENTS = 'edit assignments';
    case END_ASSIGNMENTS = 'end assignments';

    // === MAINTENANCE ===
    case VIEW_MAINTENANCE = 'view maintenance';
    case MANAGE_MAINTENANCE_PLANS = 'manage maintenance plans';
    case LOG_MAINTENANCE = 'log maintenance';

    // === HANDOVERS (Prise en charge) ===
    case CREATE_HANDOVERS = 'create handovers';
    case VIEW_HANDOVERS = 'view handovers';
    case EDIT_HANDOVERS = 'edit handovers';
    case DELETE_HANDOVERS = 'delete handovers';
    case UPLOAD_SIGNED_HANDOVERS = 'upload signed handovers';

    // === FOURNISSEURS ===
    case VIEW_SUPPLIERS = 'view suppliers';
    case CREATE_SUPPLIERS = 'create suppliers';
    case EDIT_SUPPLIERS = 'edit suppliers';
    case DELETE_SUPPLIERS = 'delete suppliers';

    // === DOCUMENTS ===
    case VIEW_DOCUMENTS = 'view documents';
    case CREATE_DOCUMENTS = 'create documents';
    case EDIT_DOCUMENTS = 'edit documents';
    case DELETE_DOCUMENTS = 'delete documents';
    case MANAGE_DOCUMENT_CATEGORIES = 'manage document_categories';

    /**
     * Retourne toutes les permissions sous forme de tableau de valeurs
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Retourne les permissions groupées par catégorie
     *
     * @return array<string, array<string>>
     */
    public static function grouped(): array
    {
        return [
            'Organisations' => [
                self::VIEW_ORGANIZATIONS->value,
                self::CREATE_ORGANIZATIONS->value,
                self::EDIT_ORGANIZATIONS->value,
                self::DELETE_ORGANIZATIONS->value,
            ],
            'Utilisateurs & Rôles' => [
                self::MANAGE_ROLES->value,
                self::VIEW_USERS->value,
                self::CREATE_USERS->value,
                self::EDIT_USERS->value,
                self::DELETE_USERS->value,
            ],
            'Véhicules' => [
                self::VIEW_VEHICLES->value,
                self::CREATE_VEHICLES->value,
                self::EDIT_VEHICLES->value,
                self::DELETE_VEHICLES->value,
                self::RESTORE_VEHICLES->value,
                self::FORCE_DELETE_VEHICLES->value,
            ],
            'Chauffeurs' => [
                self::VIEW_DRIVERS->value,
                self::CREATE_DRIVERS->value,
                self::EDIT_DRIVERS->value,
                self::DELETE_DRIVERS->value,
                self::RESTORE_DRIVERS->value,
                self::FORCE_DELETE_DRIVERS->value,
            ],
            'Affectations' => [
                self::VIEW_ASSIGNMENTS->value,
                self::CREATE_ASSIGNMENTS->value,
                self::EDIT_ASSIGNMENTS->value,
                self::END_ASSIGNMENTS->value,
            ],
            'Maintenance' => [
                self::VIEW_MAINTENANCE->value,
                self::MANAGE_MAINTENANCE_PLANS->value,
                self::LOG_MAINTENANCE->value,
            ],
            'Handovers' => [
                self::CREATE_HANDOVERS->value,
                self::VIEW_HANDOVERS->value,
                self::EDIT_HANDOVERS->value,
                self::DELETE_HANDOVERS->value,
                self::UPLOAD_SIGNED_HANDOVERS->value,
            ],
            'Fournisseurs' => [
                self::VIEW_SUPPLIERS->value,
                self::CREATE_SUPPLIERS->value,
                self::EDIT_SUPPLIERS->value,
                self::DELETE_SUPPLIERS->value,
            ],
            'Documents' => [
                self::VIEW_DOCUMENTS->value,
                self::CREATE_DOCUMENTS->value,
                self::EDIT_DOCUMENTS->value,
                self::DELETE_DOCUMENTS->value,
                self::MANAGE_DOCUMENT_CATEGORIES->value,
            ],
        ];
    }
}
