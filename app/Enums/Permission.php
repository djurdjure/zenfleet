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

    // === DEMANDES DE RÉPARATION ===
    case VIEW_OWN_REPAIR_REQUESTS = 'view own repair requests';
    case VIEW_TEAM_REPAIR_REQUESTS = 'view team repair requests';
    case VIEW_ALL_REPAIR_REQUESTS = 'view all repair requests';
    case CREATE_REPAIR_REQUESTS = 'create repair requests';
    case UPDATE_OWN_REPAIR_REQUESTS = 'update own repair requests';
    case UPDATE_ANY_REPAIR_REQUESTS = 'update any repair requests';
    case APPROVE_REPAIR_REQUESTS_L1 = 'approve repair requests level 1';
    case REJECT_REPAIR_REQUESTS_L1 = 'reject repair requests level 1';
    case APPROVE_REPAIR_REQUESTS_L2 = 'approve repair requests level 2';
    case REJECT_REPAIR_REQUESTS_L2 = 'reject repair requests level 2';
    case DELETE_REPAIR_REQUESTS = 'delete repair requests';
    case FORCE_DELETE_REPAIR_REQUESTS = 'force delete repair requests';
    case RESTORE_REPAIR_REQUESTS = 'restore repair requests';
    case VIEW_REPAIR_REQUEST_HISTORY = 'view repair request history';
    case VIEW_REPAIR_REQUEST_NOTIFICATIONS = 'view repair request notifications';
    case EXPORT_REPAIR_REQUESTS = 'export repair requests';
    case MANAGE_VEHICLE_CATEGORIES = 'manage vehicle categories';
    case MANAGE_VEHICLE_DEPOTS = 'manage vehicle depots';

    // === RELEVÉS KILOMÉTRIQUES ===
    case VIEW_OWN_MILEAGE_READINGS = 'view own mileage readings';
    case VIEW_TEAM_MILEAGE_READINGS = 'view team mileage readings';
    case VIEW_ALL_MILEAGE_READINGS = 'view all mileage readings';
    case CREATE_MILEAGE_READINGS = 'create mileage readings';
    case UPDATE_OWN_MILEAGE_READINGS = 'update own mileage readings';
    case UPDATE_ANY_MILEAGE_READINGS = 'update any mileage readings';
    case DELETE_MILEAGE_READINGS = 'delete mileage readings';
    case FORCE_DELETE_MILEAGE_READINGS = 'force delete mileage readings';
    case RESTORE_MILEAGE_READINGS = 'restore mileage readings';
    case MANAGE_AUTOMATIC_MILEAGE_READINGS = 'manage automatic mileage readings';
    case EXPORT_MILEAGE_READINGS = 'export mileage readings';
    case VIEW_MILEAGE_STATISTICS = 'view mileage statistics';
    case VIEW_MILEAGE_READING_HISTORY = 'view mileage reading history';

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
            'Demandes de Réparation' => [
                self::VIEW_OWN_REPAIR_REQUESTS->value,
                self::VIEW_TEAM_REPAIR_REQUESTS->value,
                self::VIEW_ALL_REPAIR_REQUESTS->value,
                self::CREATE_REPAIR_REQUESTS->value,
                self::UPDATE_OWN_REPAIR_REQUESTS->value,
                self::UPDATE_ANY_REPAIR_REQUESTS->value,
                self::APPROVE_REPAIR_REQUESTS_L1->value,
                self::REJECT_REPAIR_REQUESTS_L1->value,
                self::APPROVE_REPAIR_REQUESTS_L2->value,
                self::REJECT_REPAIR_REQUESTS_L2->value,
                self::DELETE_REPAIR_REQUESTS->value,
                self::FORCE_DELETE_REPAIR_REQUESTS->value,
                self::RESTORE_REPAIR_REQUESTS->value,
                self::VIEW_REPAIR_REQUEST_HISTORY->value,
                self::VIEW_REPAIR_REQUEST_NOTIFICATIONS->value,
                self::EXPORT_REPAIR_REQUESTS->value,
                self::MANAGE_VEHICLE_CATEGORIES->value,
                self::MANAGE_VEHICLE_DEPOTS->value,
            ],
            'Relevés Kilométriques' => [
                self::VIEW_OWN_MILEAGE_READINGS->value,
                self::VIEW_TEAM_MILEAGE_READINGS->value,
                self::VIEW_ALL_MILEAGE_READINGS->value,
                self::CREATE_MILEAGE_READINGS->value,
                self::UPDATE_OWN_MILEAGE_READINGS->value,
                self::UPDATE_ANY_MILEAGE_READINGS->value,
                self::DELETE_MILEAGE_READINGS->value,
                self::FORCE_DELETE_MILEAGE_READINGS->value,
                self::RESTORE_MILEAGE_READINGS->value,
                self::MANAGE_AUTOMATIC_MILEAGE_READINGS->value,
                self::EXPORT_MILEAGE_READINGS->value,
                self::VIEW_MILEAGE_STATISTICS->value,
                self::VIEW_MILEAGE_READING_HISTORY->value,
            ],
        ];
    }
}
