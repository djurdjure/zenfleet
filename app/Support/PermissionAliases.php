<?php

namespace App\Support;

final class PermissionAliases
{
    /**
     * Canonical permission (dot notation) => legacy aliases (space/other formats).
     *
     * Keep this list explicit to avoid accidental privilege escalation.
     */
    private const MAP = [
        // Organizations
        'organizations.view' => ['view organizations'],
        'organizations.create' => ['create organizations'],
        'organizations.update' => ['edit organizations', 'edit_organizations'],
        'organizations.delete' => ['delete organizations'],

        // Roles & permissions
        'roles.manage' => ['manage roles'],
        'roles.view' => ['view roles'],
        'permissions.manage' => ['manage roles'],

        // Users
        'users.view' => ['view users'],
        'users.create' => ['create users'],
        'users.update' => ['edit users', 'update users'],
        'users.delete' => ['delete users'],
        'users.export' => ['export users'],

        // Vehicles
        'vehicles.view' => ['view vehicles'],
        'vehicles.create' => ['create vehicles'],
        'vehicles.update' => ['edit vehicles', 'update vehicles', 'edit_vehicles'],
        'vehicles.delete' => ['delete vehicles'],
        'vehicles.restore' => ['restore vehicles'],
        'vehicles.force-delete' => ['force delete vehicles'],
        'vehicles.export' => ['export vehicles', 'export_vehicles'],
        'vehicles.import' => ['import vehicles'],
        'vehicles.manage' => ['manage vehicles'],
        'vehicles.status.update' => ['update-vehicle-status'],
        'vehicles.history.view' => ['view vehicle history'],
        'vehicles.maintenance.manage' => ['manage vehicle maintenance'],
        'vehicles.documents.manage' => ['manage vehicle documents'],
        'vehicles.assign' => ['assign vehicles'],
        'vehicles.track' => ['track vehicles'],

        // Drivers
        'drivers.view' => ['view drivers'],
        'drivers.create' => ['create drivers'],
        'drivers.update' => ['edit drivers', 'update drivers'],
        'drivers.delete' => ['delete drivers'],
        'drivers.restore' => ['restore drivers'],
        'drivers.force-delete' => ['force delete drivers', 'force-delete drivers'],
        'drivers.export' => ['export drivers'],
        'drivers.import' => ['import drivers'],
        'drivers.manage' => ['manage drivers'],
        'drivers.status.update' => ['update-driver-status'],

        // Assignments
        'assignments.view' => ['view assignments'],
        'assignments.create' => ['create assignments'],
        'assignments.update' => ['edit assignments', 'assignments.edit'],
        'assignments.delete' => ['delete assignments', 'assignments.delete'],
        'assignments.restore' => ['restore assignments', 'assignments.restore'],
        'assignments.force-delete' => ['force delete assignments', 'assignments.force-delete'],
        'assignments.end' => ['end assignments', 'assignments.end'],
        'assignments.export' => ['export assignments', 'assignments.export'],
        'assignments.view-gantt' => ['view gantt', 'assignments.view-gantt'],
        'assignments.view-stats' => ['view assignment statistics', 'assignments.view-stats'],
        'assignments.create-batch' => ['create batch assignments', 'assignments.create-batch'],
        'assignments.view-conflicts' => ['view conflicts', 'assignments.view-conflicts'],

        // Documents
        'documents.view' => ['view documents'],
        'documents.create' => ['create documents'],
        'documents.update' => ['edit documents'],
        'documents.delete' => ['delete documents'],
        'documents.download' => ['download documents'],
        'document-categories.manage' => ['manage document_categories'],

        // Depots
        'depots.view' => ['view depots'],
        'depots.create' => ['create depots'],
        'depots.update' => ['edit depots'],
        'depots.delete' => ['delete depots'],
        'depots.restore' => ['restore depots'],
        'depots.export' => ['export depots'],

        // Suppliers
        'suppliers.view' => ['view suppliers'],
        'suppliers.create' => ['create suppliers'],
        'suppliers.update' => ['edit suppliers'],
        'suppliers.delete' => ['delete suppliers'],

        // Maintenance
        'maintenance.view' => ['view maintenance'],
        'maintenance.plans.manage' => ['manage maintenance plans'],
        'maintenance.log' => ['log maintenance'],
        'maintenance.operations.view' => ['view maintenance_operations'],
        'maintenance.operations.create' => ['create maintenance operations'],
        'maintenance.operations.update' => ['edit maintenance operations'],
        'maintenance.operations.delete' => ['delete maintenance operations'],
        'maintenance.logs.view' => ['view maintenance logs'],
        'maintenance.logs.update' => ['edit maintenance logs'],

        // Handovers
        'handovers.create' => ['create handovers'],
        'handovers.view' => ['view handovers'],
        'handovers.update' => ['edit handovers'],
        'handovers.delete' => ['delete handovers'],
        'handovers.signed.upload' => ['upload signed handovers'],

        // Repair requests
        'repair-requests.view.own' => ['view own repair requests'],
        'repair-requests.view.team' => ['view team repair requests'],
        'repair-requests.view.all' => ['view all repair requests'],
        'repair-requests.create' => ['create repair requests'],
        'repair-requests.update.own' => ['update own repair requests'],
        'repair-requests.update.any' => ['update any repair requests'],
        'repair-requests.delete' => ['delete repair requests'],
        'repair-requests.restore' => ['restore repair requests'],
        'repair-requests.force-delete' => ['force delete repair requests'],
        'repair-requests.approve.level1' => ['approve repair requests level 1'],
        'repair-requests.reject.level1' => ['reject repair requests level 1'],
        'repair-requests.approve.level2' => ['approve repair requests level 2'],
        'repair-requests.reject.level2' => ['reject repair requests level 2'],
        'repair-requests.export' => ['export repair requests'],
        'repair-requests.view.history' => ['view repair request history'],
        'repair-requests.view.notifications' => ['view repair request notifications'],

        // Mileage readings
        'mileage-readings.view.own' => ['view own mileage readings'],
        'mileage-readings.view.team' => ['view team mileage readings'],
        'mileage-readings.view.all' => ['view all mileage readings'],
        'mileage-readings.create' => ['create mileage readings'],
        'mileage-readings.update.own' => ['update own mileage readings'],
        'mileage-readings.update.any' => ['update any mileage readings'],
        'mileage-readings.delete' => ['delete mileage readings'],
        'mileage-readings.restore' => ['restore mileage readings'],
        'mileage-readings.force-delete' => ['force delete mileage readings'],
        'mileage-readings.export' => ['export mileage readings'],
        'mileage-readings.manage.automatic' => ['manage automatic mileage readings'],
        'mileage-readings.view.statistics' => ['view mileage statistics'],
        'mileage-readings.view.history' => ['view mileage reading history'],

        // Driver sanctions
        'driver-sanctions.view.own' => ['view own driver sanctions'],
        'driver-sanctions.view.team' => ['view team driver sanctions'],
        'driver-sanctions.view.all' => ['view all driver sanctions'],
        'driver-sanctions.create' => ['create driver sanctions'],
        'driver-sanctions.update.own' => ['update own driver sanctions'],
        'driver-sanctions.update.any' => ['update any driver sanctions'],
        'driver-sanctions.delete' => ['delete driver sanctions'],
        'driver-sanctions.restore' => ['restore driver sanctions'],
        'driver-sanctions.force-delete' => ['force delete driver sanctions'],
        'driver-sanctions.archive' => ['archive driver sanctions'],
        'driver-sanctions.unarchive' => ['unarchive driver sanctions'],

        // Expenses
        'expenses.view' => ['view expenses', 'view expense'],
        'expenses.view.any' => ['view any expenses'],
        'expenses.view.all' => ['view all organization expenses'],
        'expenses.create' => ['create expenses'],
        'expenses.update' => ['edit expenses', 'update expenses'],
        'expenses.delete' => ['delete expenses'],
        'expenses.restore' => ['restore expenses'],
        'expenses.force-delete' => ['force delete expenses'],
        'expenses.update.approved' => ['edit approved expenses'],
        'expenses.delete.approved' => ['delete approved expenses'],
        'expenses.approval.bypass' => ['bypass expense approval'],
        'expenses.approve' => ['approve expenses'],
        'expenses.approve.level1' => ['approve expenses level1'],
        'expenses.approve.level2' => ['approve expenses level2'],
        'expenses.reject' => ['reject expenses'],
        'expenses.mark-paid' => ['mark expenses as paid'],
        'expenses.pay' => ['pay vehicle expenses'],
        'expenses.export' => ['export expenses'],
        'expenses.import' => ['import expenses'],
        'expenses.analytics.view' => ['view expense analytics'],
        'expenses.dashboard.view' => ['view expense dashboard'],
        'expenses.audit.view' => ['view expense audit logs'],
        'expenses.groups.manage' => ['manage expense groups'],
        'expenses.budgets.manage' => ['manage expense budgets'],

        // Misc
        'alerts.view' => ['view alerts'],
        'analytics.view' => ['view analytics'],
        'audit-logs.view' => ['view audit logs'],
        'system.view' => ['view organizations'],
    ];

    private static ?array $legacyToCanonical = null;

    public static function isLegacy(string $permission): bool
    {
        return array_key_exists($permission, self::getLegacyToCanonicalMap());
    }

    public static function canonicalFor(string $permission): ?string
    {
        if (isset(self::MAP[$permission])) {
            return $permission;
        }

        $legacyMap = self::getLegacyToCanonicalMap();

        return $legacyMap[$permission] ?? null;
    }

    public static function normalize(array $permissions): array
    {
        $normalized = [];

        foreach ($permissions as $permission) {
            $permission = trim($permission);
            if ($permission === '') {
                continue;
            }

            $canonical = self::canonicalFor($permission) ?? $permission;
            $normalized[] = $canonical;
        }

        return self::unique($normalized);
    }

    private static function getLegacyToCanonicalMap(): array
    {
        if (self::$legacyToCanonical !== null) {
            return self::$legacyToCanonical;
        }

        $map = [];
        foreach (self::MAP as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                $map[$alias] = $canonical;
            }
        }

        self::$legacyToCanonical = $map;

        return self::$legacyToCanonical;
    }

    public static function resolve(string $permission): array
    {
        $permission = trim($permission);

        if (isset(self::MAP[$permission])) {
            return self::unique([$permission, ...self::MAP[$permission]]);
        }

        foreach (self::MAP as $canonical => $aliases) {
            if (in_array($permission, $aliases, true)) {
                return self::unique([$permission, $canonical, ...$aliases]);
            }
        }

        return [$permission];
    }

    public static function isRelevant(string $permission): bool
    {
        if (isset(self::MAP[$permission])) {
            return true;
        }

        foreach (self::MAP as $aliases) {
            if (in_array($permission, $aliases, true)) {
                return true;
            }
        }

        return false;
    }

    public static function legacyMap(): array
    {
        return self::MAP;
    }

    private static function unique(array $items): array
    {
        return array_values(array_unique($items));
    }
}
