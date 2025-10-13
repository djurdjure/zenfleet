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
    public function __construct(DriverRepositoryInterface $driverRepository) { $this->driverRepository = $driverRepository; }
    public function getFilteredDrivers(array $filters): LengthAwarePaginator { return $this->driverRepository->getFiltered($filters); }

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

                // Générer mot de passe fort : Chauffeur@2025 + 4 chiffres
                $generatedPassword = 'Chauffeur@2025' . rand(1000, 9999);

                // Créer l'utilisateur
                $user = User::create([
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $email,
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
        $this->driverRepository->update($driver, $data);
        return $driver->fresh(); // Retourne l'objet Driver mis à jour
    }

    public function archiveDriver(Driver $driver): bool
    {
        // RÈGLE MÉTIER : On ne peut pas archiver un chauffeur avec des affectations.
        if ($driver->assignments()->exists()) {
            return false;
        }
        return $this->driverRepository->delete($driver);
    }

    public function restoreDriver(int $driverId): bool
    {
        $driver = $this->driverRepository->findTrashed($driverId);
        return $driver ? $this->driverRepository->restore($driver) : false;
    }

   public function forceDeleteDriver(int $driverId): bool
    {
        $driver = $this->driverRepository->findTrashed($driverId);

        if ($driver) {
            // RÈGLE MÉTIER : On vérifie si le chauffeur a des affectations
            if ($driver->assignments()->exists()) {
                // Si oui, on refuse la suppression
                return false;
            }

            // Si non, on procède à la suppression
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            return $this->driverRepository->forceDelete($driver);
        }
        return false;
    }
}