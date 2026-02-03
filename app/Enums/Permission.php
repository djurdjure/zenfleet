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
    case VIEW_ORGANIZATIONS = 'organizations.view';
    case CREATE_ORGANIZATIONS = 'organizations.create';
    case EDIT_ORGANIZATIONS = 'organizations.update';
    case DELETE_ORGANIZATIONS = 'organizations.delete';

    // === GESTION DES RÔLES ===
    case MANAGE_ROLES = 'roles.manage';

    // === UTILISATEURS ===
    case VIEW_USERS = 'users.view';
    case CREATE_USERS = 'users.create';
    case EDIT_USERS = 'users.update';
    case DELETE_USERS = 'users.delete';

    // === VÉHICULES ===
    case VIEW_VEHICLES = 'vehicles.view';
    case CREATE_VEHICLES = 'vehicles.create';
    case EDIT_VEHICLES = 'vehicles.update';
    case DELETE_VEHICLES = 'vehicles.delete';
    case RESTORE_VEHICLES = 'vehicles.restore';
    case FORCE_DELETE_VEHICLES = 'vehicles.force-delete';

    // === CHAUFFEURS ===
    case VIEW_DRIVERS = 'drivers.view';
    case CREATE_DRIVERS = 'drivers.create';
    case EDIT_DRIVERS = 'drivers.update';
    case DELETE_DRIVERS = 'drivers.delete';
    case RESTORE_DRIVERS = 'drivers.restore';
    case FORCE_DELETE_DRIVERS = 'drivers.force-delete';

    // === AFFECTATIONS ===
    case VIEW_ASSIGNMENTS = 'assignments.view';
    case CREATE_ASSIGNMENTS = 'assignments.create';
    case EDIT_ASSIGNMENTS = 'assignments.update';
    case END_ASSIGNMENTS = 'assignments.end';

    // === MAINTENANCE ===
    case VIEW_MAINTENANCE = 'maintenance.view';
    case MANAGE_MAINTENANCE_PLANS = 'maintenance.plans.manage';
    case LOG_MAINTENANCE = 'maintenance.log';

    // === HANDOVERS (Prise en charge) ===
    case CREATE_HANDOVERS = 'handovers.create';
    case VIEW_HANDOVERS = 'handovers.view';
    case EDIT_HANDOVERS = 'handovers.update';
    case DELETE_HANDOVERS = 'handovers.delete';
    case UPLOAD_SIGNED_HANDOVERS = 'handovers.signed.upload';

    // === FOURNISSEURS ===
    case VIEW_SUPPLIERS = 'suppliers.view';
    case CREATE_SUPPLIERS = 'suppliers.create';
    case EDIT_SUPPLIERS = 'suppliers.update';
    case DELETE_SUPPLIERS = 'suppliers.delete';

    // === DOCUMENTS ===
    case VIEW_DOCUMENTS = 'documents.view';
    case CREATE_DOCUMENTS = 'documents.create';
    case EDIT_DOCUMENTS = 'documents.update';
    case DELETE_DOCUMENTS = 'documents.delete';
    case MANAGE_DOCUMENT_CATEGORIES = 'document-categories.manage';

    // === DEMANDES DE RÉPARATION ===
    case VIEW_OWN_REPAIR_REQUESTS = 'repair-requests.view.own';
    case VIEW_TEAM_REPAIR_REQUESTS = 'repair-requests.view.team';
    case VIEW_ALL_REPAIR_REQUESTS = 'repair-requests.view.all';
    case CREATE_REPAIR_REQUESTS = 'repair-requests.create';
    case UPDATE_OWN_REPAIR_REQUESTS = 'repair-requests.update.own';
    case UPDATE_ANY_REPAIR_REQUESTS = 'repair-requests.update.any';
    case APPROVE_REPAIR_REQUESTS_L1 = 'repair-requests.approve.level1';
    case REJECT_REPAIR_REQUESTS_L1 = 'repair-requests.reject.level1';
    case APPROVE_REPAIR_REQUESTS_L2 = 'repair-requests.approve.level2';
    case REJECT_REPAIR_REQUESTS_L2 = 'repair-requests.reject.level2';
    case DELETE_REPAIR_REQUESTS = 'repair-requests.delete';
    case FORCE_DELETE_REPAIR_REQUESTS = 'repair-requests.force-delete';
    case RESTORE_REPAIR_REQUESTS = 'repair-requests.restore';
    case VIEW_REPAIR_REQUEST_HISTORY = 'repair-requests.view.history';
    case VIEW_REPAIR_REQUEST_NOTIFICATIONS = 'repair-requests.view.notifications';
    case EXPORT_REPAIR_REQUESTS = 'repair-requests.export';
    case MANAGE_VEHICLE_CATEGORIES = 'vehicle-categories.manage';
    case MANAGE_VEHICLE_DEPOTS = 'depots.manage';

    // === RELEVÉS KILOMÉTRIQUES ===
    case VIEW_OWN_MILEAGE_READINGS = 'mileage-readings.view.own';
    case VIEW_TEAM_MILEAGE_READINGS = 'mileage-readings.view.team';
    case VIEW_ALL_MILEAGE_READINGS = 'mileage-readings.view.all';
    case CREATE_MILEAGE_READINGS = 'mileage-readings.create';
    case UPDATE_OWN_MILEAGE_READINGS = 'mileage-readings.update.own';
    case UPDATE_ANY_MILEAGE_READINGS = 'mileage-readings.update.any';
    case DELETE_MILEAGE_READINGS = 'mileage-readings.delete';
    case FORCE_DELETE_MILEAGE_READINGS = 'mileage-readings.force-delete';
    case RESTORE_MILEAGE_READINGS = 'mileage-readings.restore';
    case MANAGE_AUTOMATIC_MILEAGE_READINGS = 'mileage-readings.manage.automatic';
    case EXPORT_MILEAGE_READINGS = 'mileage-readings.export';
    case VIEW_MILEAGE_STATISTICS = 'mileage-readings.view.statistics';
    case VIEW_MILEAGE_READING_HISTORY = 'mileage-readings.view.history';

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
