<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\User;
use App\Repositories\Interfaces\DriverRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DriverService
{
    protected DriverRepositoryInterface $driverRepository;
    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }
    public function getFilteredDrivers(array $filters): LengthAwarePaginator
    {
        return $this->driverRepository->getFiltered($filters);
    }

    /**
     * 🚀 CRÉATION ENTERPRISE DE CHAUFFEUR AVEC USER AUTO
     *
     * Logique métier :
     * - Si user_id est NULL → Créer automatiquement un compte User
     * - Générer email : prenom.nom@zenfleet.dz
     * - Générer mot de passe : Chauffeur@2025 + 4 chiffres aléatoires
     * - Attribuer le rôle "Chauffeur"
     * - Assigner l'organisation du chauffeur
     *
     * @param array $data Données du formulaire validées
     * @return array ['driver' => Driver, 'user' => User, 'password' => string|null, 'was_created' => bool]
     */
    public function createDriver(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $generatedPassword = null;
            $userWasCreated = false;
            $user = null;

            // 📸 GESTION PHOTO
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $photoPath = $data['photo']->store('drivers/photos', 'public');
                $data['photo'] = $photoPath;
            }

            // 👤 CRÉATION AUTOMATIQUE DE USER SI NÉCESSAIRE
            if (empty($data['user_id'])) {
                // Générer email unique: prenom.nom@zenfleet.dz
                $baseEmail = Str::slug($data['first_name'] . '.' . $data['last_name']) . '@zenfleet.dz';
                $email = $baseEmail;
                $counter = 1;

                // Vérifier unicité email
                while (User::where('email', $email)->exists()) {
                    $email = Str::slug($data['first_name'] . '.' . $data['last_name']) . $counter . '@zenfleet.dz';
                    $counter++;
                }

                // Générer mot de passe : 1ère lettre prénom (maj) + Nom (1ère lettre maj) + @ + année (YYYY)
                $firstInitial = Str::upper(Str::substr(trim((string) $data['first_name']), 0, 1));
                $lastName = trim((string) $data['last_name']);
                $lastName = $lastName !== '' ? (Str::upper(Str::substr($lastName, 0, 1)) . Str::substr($lastName, 1)) : '';
                $generatedPassword = $firstInitial . $lastName . '@' . now()->format('Y');

                // Créer l'utilisateur
                $user = User::create([
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $email,
                    'phone' => $data['personal_phone'] ?? null,
                    'password' => Hash::make($generatedPassword),
                    'organization_id' => $data['organization_id'],
                    'email_verified_at' => now(), // ✅ Auto-vérifier pour éviter problèmes de connexion
                ]);

                // ✅ CORRECTION ENTERPRISE: Attribuer le rôle avec organization_id pour Spatie multi-tenant
                // Trouver le rôle Chauffeur pour cette organisation
                $role = \Spatie\Permission\Models\Role::where('name', 'Chauffeur')
                    ->where('organization_id', $data['organization_id'])
                    ->first();

                if (!$role) {
                    // Fallback: rôle global sans organization_id
                    $role = \Spatie\Permission\Models\Role::where('name', 'Chauffeur')
                        ->whereNull('organization_id')
                        ->first();
                }

                if ($role) {
                    // Assigner directement dans la table pivot avec organization_id
                    DB::table('model_has_roles')->insert([
                        'role_id' => $role->id,
                        'model_type' => User::class,
                        'model_id' => $user->id,
                        'organization_id' => $data['organization_id'],
                    ]);

                    // Refresh permissions cache
                    $user->load('roles');
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                }

                $data['user_id'] = $user->id;
                $userWasCreated = true;
            } else {
                // Récupérer l'utilisateur existant
                $user = User::find($data['user_id']);
            }

            // ✅ FIX: S'assurer que license_categories est bien un array propre
            if (isset($data['license_categories'])) {
                if (!is_array($data['license_categories'])) {
                    $data['license_categories'] = json_decode($data['license_categories'], true) ?? [];
                }
                // Nettoyer le tableau : supprimer les valeurs vides et réindexer
                $data['license_categories'] = array_values(array_filter($data['license_categories'], fn($v) => !empty($v)));
            }

            // 🚗 CRÉER LE CHAUFFEUR
            $driver = $this->driverRepository->create($data);

            return [
                'driver' => $driver->load(['user', 'driverStatus', 'organization']),
                'user' => $user,
                'password' => $generatedPassword,
                'was_created' => $userWasCreated,
            ];
        });
    }

    public function updateDriver(Driver $driver, array $data): Driver
    {
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            $photoPath = $data['photo']->store('drivers/photos', 'public');
            $data['photo'] = $photoPath;
        }

        // ✅ FIX: S'assurer que license_categories est bien un array
        if (isset($data['license_categories']) && !is_array($data['license_categories'])) {
            $data['license_categories'] = json_decode($data['license_categories'], true) ?? [];
        }

        // ✅ FIX: Gérer la checkbox license_verified (si non coché, mettre false)
        $data['license_verified'] = $data['license_verified'] ?? false;

        $this->driverRepository->update($driver, $data);
        return $driver->fresh(); // Retourne l'objet Driver mis à jour
    }

    public function archiveDriver(Driver $driver): bool
    {
        // RÈGLE MÉTIER : On ne peut pas archiver un chauffeur avec des affectations EN COURS.
        // On autorise l'archivage si le chauffeur a seulement des affectations passées (terminées).
        $hasActiveAssignments = $driver->assignments()
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', now());
            })
            ->exists();

        if ($hasActiveAssignments) {
            return false;
        }
        return $this->driverRepository->delete($driver);
    }

    public function restoreDriver(int $driverId): ?Driver
    {
        $driver = $this->driverRepository->findTrashed($driverId);
        if ($driver && $this->driverRepository->restore($driver)) {
            return $driver->fresh(); // Retourne l'objet Driver restauré
        }
        return null;
    }

    public function forceDeleteDriver(int $driverId): bool
    {
        $driver = $this->driverRepository->findTrashed($driverId);

        if ($driver) {
            return DB::transaction(function () use ($driver) {
                // ⚠️ SUPPRESSION EN CASCADE - TOUS LES ENREGISTREMENTS LIÉS

                // 1. Supprimer les affectations (assignments)
                if ($driver->assignments()->exists()) {
                    \Log::info('Deleting assignments for driver', [
                        'driver_id' => $driver->id,
                        'assignments_count' => $driver->assignments()->count()
                    ]);
                    $driver->assignments()->forceDelete();
                }

                // 2. Supprimer les sanctions (driver_sanctions)
                if (method_exists($driver, 'sanctions') && $driver->sanctions()->exists()) {
                    \Log::info('Deleting sanctions for driver', [
                        'driver_id' => $driver->id,
                        'sanctions_count' => $driver->sanctions()->count()
                    ]);
                    $driver->sanctions()->forceDelete();
                }

                // 3. Supprimer les demandes de réparation (repair_requests)
                if ($driver->repairRequests()->exists()) {
                    \Log::info('Deleting repair requests for driver', [
                        'driver_id' => $driver->id,
                        'repair_requests_count' => $driver->repairRequests()->count()
                    ]);
                    $driver->repairRequests()->forceDelete();
                }

                // 4. Supprimer la photo si elle existe
                if ($driver->photo) {
                    Storage::disk('public')->delete($driver->photo);
                    \Log::info('Photo deleted for driver', [
                        'driver_id' => $driver->id,
                        'photo_path' => $driver->photo
                    ]);
                }

                // 5. Suppression définitive du chauffeur
                $deleted = $this->driverRepository->forceDelete($driver);

                if ($deleted) {
                    \Log::warning('Driver force deleted with all related records', [
                        'driver_id' => $driver->id,
                        'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                        'deleted_by' => auth()->id()
                    ]);
                }

                return $deleted;
            });
        }
        return false;
    }
}
