<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * 🔍 Commande de Diagnostic - Système Relevés Kilométriques
 *
 * Diagnostique le système de relevés kilométriques pour identifier
 * les problèmes d'affichage, de permissions, ou de données.
 *
 * Usage:
 *   php artisan diagnose:mileage-readings
 *   php artisan diagnose:mileage-readings superadmin@zenfleet.dz
 *
 * @version 1.0-Enterprise
 */
class DiagnoseMileageReadings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnose:mileage-readings {user_email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose mileage readings system for a specific user or globally';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Diagnostic Système Relevés Kilométriques ZenFleet');
        $this->newLine();

        // 1. Vérifier les permissions
        $this->checkPermissions();

        // 2. Vérifier les rôles
        $this->checkRoles();

        // 3. Vérifier les données
        $this->checkData();

        // 4. Tester un utilisateur spécifique
        if ($email = $this->argument('user_email')) {
            $this->checkUser($email);
        } else {
            $this->info('💡 Astuce: Utilisez "php artisan diagnose:mileage-readings EMAIL" pour tester un utilisateur spécifique');
        }

        $this->newLine();
        $this->info('✅ Diagnostic terminé');

        return 0;
    }

    /**
     * Vérifier que les permissions mileage existent
     */
    protected function checkPermissions(): void
    {
        $this->info('📋 Vérification des permissions mileage...');

        $permissions = Permission::where('name', 'like', '%mileage%')->get();

        if ($permissions->isEmpty()) {
            $this->error('  ❌ Aucune permission mileage trouvée!');
            $this->warn('  → Solution: php artisan db:seed --class=VehicleMileagePermissionsSeeder');
            return;
        }

        $this->line("  ✅ {$permissions->count()} permissions trouvées:");
        foreach ($permissions as $perm) {
            $this->line("     - {$perm->name}");
        }

        $this->newLine();
    }

    /**
     * Vérifier que les rôles ont les permissions mileage
     */
    protected function checkRoles(): void
    {
        $this->info('👥 Vérification des rôles...');

        $roles = ['Chauffeur', 'Supervisor', 'Gestionnaire Flotte', 'Admin', 'Super Admin'];

        $headers = ['Rôle', 'Permissions Mileage', 'Status'];
        $rows = [];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $rows[] = [$roleName, '0', '❌ Rôle introuvable'];
                continue;
            }

            $mileagePerms = $role->permissions()
                ->where('name', 'like', '%mileage%')
                ->get();

            $count = $mileagePerms->count();
            $status = $count === 0 ? '⚠️  Aucune permission' : '✅ OK';

            $rows[] = [$roleName, $count, $status];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    /**
     * Vérifier que les données existent
     */
    protected function checkData(): void
    {
        $this->info('📊 Vérification des données...');

        $vehiclesCount = Vehicle::count();
        $readingsCount = VehicleMileageReading::count();
        $usersCount = User::count();
        $orgsCount = \App\Models\Organization::count();

        $headers = ['Entité', 'Nombre', 'Status'];
        $rows = [
            ['Organisations', $orgsCount, $orgsCount > 0 ? '✅' : '❌'],
            ['Véhicules', $vehiclesCount, $vehiclesCount > 0 ? '✅' : '⚠️'],
            ['Relevés Kilométriques', $readingsCount, $readingsCount > 0 ? '✅' : '⚠️'],
            ['Utilisateurs', $usersCount, $usersCount > 0 ? '✅' : '❌'],
        ];

        $this->table($headers, $rows);

        if ($vehiclesCount === 0) {
            $this->warn('  ⚠️  Aucun véhicule dans la base!');
            $this->warn('  → Solution: php artisan db:seed --class=AlgeriaFleetSeeder');
        }

        if ($readingsCount === 0) {
            $this->warn('  ⚠️  Aucun relevé kilométrique dans la base!');
            $this->warn('  → Solution: php artisan db:seed --class=VehicleMileageReadingsSeeder');
        }

        $this->newLine();
    }

    /**
     * Diagnostiquer un utilisateur spécifique
     */
    protected function checkUser(string $email): void
    {
        $this->info("🔍 Diagnostic pour utilisateur: {$email}");
        $this->newLine();

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("  ❌ Utilisateur introuvable!");
            $this->newLine();
            $this->line("  Utilisateurs disponibles:");
            User::take(10)->get()->each(function ($u) {
                $this->line("    - {$u->email} ({$u->name})");
            });
            return;
        }

        // Informations de base
        $this->line("  ID: {$user->id}");
        $this->line("  Nom: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Organisation ID: {$user->organization_id}");
        $this->line("  Dépôt ID: " . ($user->depot_id ?? 'N/A'));
        $this->line("  Rôle(s): " . $user->roles->pluck('name')->join(', '));

        $this->newLine();

        // Permissions mileage
        $this->line("  📋 Permissions mileage:");

        $canViewOwn = $user->can('mileage-readings.view.own');
        $canViewTeam = $user->can('mileage-readings.view.team');
        $canViewAll = $user->can('mileage-readings.view.all');
        $canCreate = $user->can('mileage-readings.create');

        $this->line("    mileage-readings.view.own:  " . ($canViewOwn ? '✅ OUI' : '❌ NON'));
        $this->line("    mileage-readings.view.team: " . ($canViewTeam ? '✅ OUI' : '❌ NON'));
        $this->line("    mileage-readings.view.all:  " . ($canViewAll ? '✅ OUI' : '❌ NON'));
        $this->line("    mileage-readings.create:    " . ($canCreate ? '✅ OUI' : '❌ NON'));

        $this->newLine();

        // Simuler la logique du composant MileageReadingsIndex
        $this->line("  📈 Relevés accessibles (simulation logique composant):");

        $query = VehicleMileageReading::where('organization_id', $user->organization_id);

        $scope = 'AUCUN';

        if ($canViewAll) {
            $scope = 'TOUS (organization)';
            // Tous les relevés de l'organisation
        } elseif ($canViewTeam) {
            $scope = 'ÉQUIPE (depot)';
            if ($user->depot_id) {
                $query->whereHas('vehicle', function ($q) use ($user) {
                    $q->where('depot_id', $user->depot_id);
                });
            } else {
                $this->warn("    ⚠️  Utilisateur a 'mileage-readings.view.team' mais pas de depot_id!");
            }
        } elseif ($canViewOwn) {
            $scope = 'PROPRES (recorded_by)';
            $query->where('recorded_by_id', $user->id);
        }

        $count = $query->count();

        $this->line("    Scope: {$scope}");
        $this->line("    Nombre de relevés: {$count}");

        if ($count === 0) {
            $this->warn('    ⚠️  Aucun relevé accessible pour cet utilisateur!');

            if ($canViewOwn && !$canViewTeam && !$canViewAll) {
                $ownCount = VehicleMileageReading::where('recorded_by_id', $user->id)->count();
                $this->line("    → Relevés créés par cet utilisateur: {$ownCount}");

                if ($ownCount === 0) {
                    $this->warn("    → L'utilisateur n'a créé aucun relevé");
                    $this->line("    → Solution: Créer un relevé de test pour cet utilisateur");
                }
            }

            if ($canViewAll) {
                $orgCount = VehicleMileageReading::where('organization_id', $user->organization_id)->count();
                $this->line("    → Relevés dans l'organisation: {$orgCount}");

                if ($orgCount === 0) {
                    $this->warn("    → Aucun relevé dans l'organisation");
                    $this->line("    → Solution: php artisan db:seed --class=VehicleMileageReadingsSeeder");
                }
            }
        } else {
            $this->info("    ✅ {$count} relevés devraient s'afficher dans la page");
        }

        $this->newLine();

        // Test du composant Livewire
        $this->line("  🧪 Test du composant Livewire:");

        try {
            // Simuler l'appel du composant
            $component = new \App\Livewire\Admin\MileageReadingsIndex();

            $this->line("    ✅ Composant Livewire instanciable");

            // Vérifier que les propriétés existent
            $reflection = new \ReflectionClass($component);
            $properties = ['search', 'vehicleFilter', 'methodFilter', 'sortField', 'sortDirection', 'perPage'];

            foreach ($properties as $prop) {
                if ($reflection->hasProperty($prop)) {
                    $this->line("    ✅ Propriété '{$prop}' existe");
                } else {
                    $this->error("    ❌ Propriété '{$prop}' manquante!");
                }
            }

        } catch (\Exception $e) {
            $this->error("    ❌ Erreur lors de l'instanciation du composant:");
            $this->error("       " . $e->getMessage());
        }

        $this->newLine();
    }
}
