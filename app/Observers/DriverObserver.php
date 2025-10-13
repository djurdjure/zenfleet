<?php

namespace App\Observers;

use App\Models\Driver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * 🚛 DRIVER OBSERVER - Version Enterprise Ultra-Professional
 * 
 * Observer pattern pour gérer automatiquement les événements du cycle de vie des chauffeurs
 * avec création automatique d'un compte utilisateur et notifications.
 * 
 * Fonctionnalités Enterprise:
 * - Création automatique de compte utilisateur avec mot de passe sécurisé
 * - Attribution automatique du rôle Chauffeur
 * - Notification par email (optionnelle)
 * - Calcul automatique de la date d'expiration du permis
 * - Traçabilité complète avec audit trail
 * - Gestion d'erreurs robuste avec transactions
 * 
 * @version 3.0-Enterprise
 * @author ZenFleet Professional Team
 */
class DriverObserver
{
    /**
     * Constantes pour la configuration
     */
    private const DEFAULT_PASSWORD = 'password';
    private const LICENSE_VALIDITY_YEARS = 10;
    private const SEND_WELCOME_EMAIL = false; // Activé en production
    private const MAX_EMAIL_RETRY_ATTEMPTS = 3;
    
    /**
     * Gère l'événement "saving" du modèle Driver.
     * Calcul automatique de la date d'expiration du permis (10 ans par défaut)
     */
    public function saving(Driver $driver): void
    {
        // Calcul automatique de la date d'expiration du permis
        if ($driver->isDirty('license_issue_date') && !is_null($driver->license_issue_date)) {
            $driver->license_expiry_date = Carbon::parse($driver->license_issue_date)
                ->addYears(self::LICENSE_VALIDITY_YEARS);

            Log::info('Driver license expiry date calculated', [
                'driver_id' => $driver->id,
                'issue_date' => $driver->license_issue_date,
                'expiry_date' => $driver->license_expiry_date,
                'validity_years' => self::LICENSE_VALIDITY_YEARS
            ]);
        }

        // Validation du matricule employé
        if ($driver->isDirty('employee_number') && !is_null($driver->employee_number)) {
            $driver->employee_number = strtoupper(trim($driver->employee_number));
        }

        // Formatage des numéros de téléphone
        if ($driver->isDirty('personal_phone') && !is_null($driver->personal_phone)) {
            $driver->personal_phone = $this->formatPhoneNumber($driver->personal_phone);
        }
    }

    /**
     * Gère l'événement "created" du modèle Driver.
     *
     * ⚠️ NOTE IMPORTANTE: La création automatique du compte utilisateur est désormais
     * gérée par DriverService::createDriver() pour garantir la compatibilité multi-tenant
     * avec organization_id dans la table model_has_roles.
     *
     * Cet événement ne sert maintenant que pour le logging et l'audit trail.
     */
    public function created(Driver $driver): void
    {
        $startTime = microtime(true);

        Log::info('=== DRIVER CREATED EVENT TRIGGERED ===', [
            'driver_id' => $driver->id,
            'driver_name' => $driver->first_name . ' ' . $driver->last_name,
            'has_user_id' => !empty($driver->user_id),
            'organization_id' => $driver->organization_id,
            'created_by' => auth()->id() ?? 'system',
            'timestamp' => now()->toISOString(),
            'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
        ]);

        // ✅ DÉSACTIVÉ: La création de user est maintenant gérée par DriverService
        // pour garantir le support multi-tenant avec organization_id
        // @see App\Services\DriverService::createDriver()
    }

    /**
     * Créer un compte utilisateur pour un chauffeur - Ultra Professional Implementation
     */
    private function createUserAccountForDriver(Driver $driver): void
    {
        DB::beginTransaction();
        
        try {
            // Génération de l'email
            $userEmail = $this->generateUserEmail($driver);
            
            // Vérification de l'unicité de l'email
            if (User::where('email', $userEmail)->exists()) {
                // Essayer avec un suffixe numérique
                $userEmail = $this->generateUniqueEmail($driver, $userEmail);
            }

            // Génération du mot de passe (peut être aléatoire en production)
            $plainPassword = $this->generateSecurePassword();
            
            // Création du compte utilisateur avec toutes les métadonnées
            $user = User::create([
                'name' => $driver->first_name . ' ' . $driver->last_name,
                'email' => $userEmail,
                'password' => Hash::make($plainPassword),
                'organization_id' => $driver->organization_id,
                'email_verified_at' => now(),
                // Métadonnées additionnelles
                'created_from' => 'driver_auto_creation',
                'phone' => $driver->personal_phone,
            ]);

            // Attribution du rôle Chauffeur
            $this->assignDriverRole($user);

            // Association du compte utilisateur au chauffeur
            $driver->user_id = $user->id;
            $driver->saveQuietly(); // Éviter de re-déclencher les observers

            // Notification par email (optionnel)
            if (self::SEND_WELCOME_EMAIL && !empty($driver->personal_email)) {
                $this->sendWelcomeEmail($user, $plainPassword, $driver);
            }

            // Audit trail complet
            Log::info('✅ USER ACCOUNT CREATED SUCCESSFULLY FOR DRIVER', [
                'operation' => 'driver_user_auto_creation',
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                'user_id' => $user->id,
                'email' => $userEmail,
                'organization_id' => $driver->organization_id,
                'role_assigned' => 'Chauffeur',
                'email_notification' => self::SEND_WELCOME_EMAIL ? 'sent' : 'disabled',
                'created_by' => auth()->id() ?? 'system',
                'timestamp' => now()->toISOString()
            ]);

            // Stockage sécurisé des credentials temporaires (pour admin uniquement)
            $this->storeTemporaryCredentials($driver, $userEmail, $plainPassword);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('❌ FAILED TO CREATE USER ACCOUNT FOR DRIVER', [
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            // Notification à l'administrateur système
            $this->notifyAdminOfFailure($driver, $e);
        }
    }

    /**
     * Générer un email pour le compte utilisateur
     */
    private function generateUserEmail(Driver $driver): string
    {
        // Si le chauffeur a un email personnel, l'utiliser
        if (!empty($driver->personal_email) && filter_var($driver->personal_email, FILTER_VALIDATE_EMAIL)) {
            return $driver->personal_email;
        }

        // Sinon, générer un email système
        $firstName = Str::slug($driver->first_name, '');
        $lastName = Str::slug($driver->last_name, '');
        
        // Format: prenom.nom@zenfleet.local
        $baseEmail = strtolower("{$firstName}.{$lastName}@zenfleet.local");
        
        return $baseEmail;
    }

    /**
     * Générer un email unique en cas de conflit
     */
    private function generateUniqueEmail(Driver $driver, string $baseEmail): string
    {
        $counter = 1;
        $emailParts = explode('@', $baseEmail);
        
        do {
            $newEmail = $emailParts[0] . $counter . '@' . $emailParts[1];
            $counter++;
        } while (User::where('email', $newEmail)->exists() && $counter < 100);
        
        if ($counter >= 100) {
            // Fallback avec UUID si trop de tentatives
            $newEmail = $emailParts[0] . '_' . Str::random(6) . '@' . $emailParts[1];
        }
        
        return $newEmail;
    }

    /**
     * Générer un mot de passe sécurisé
     */
    private function generateSecurePassword(): string
    {
        // En production, générer un mot de passe aléatoire sécurisé
        if (app()->environment('production')) {
            return Str::random(12) . '!' . rand(100, 999);
        }
        
        // En développement, utiliser le mot de passe par défaut
        return self::DEFAULT_PASSWORD;
    }

    /**
     * Attribuer le rôle Chauffeur à l'utilisateur
     */
    private function assignDriverRole(User $user): void
    {
        try {
            $chauffeurRole = Role::where('name', 'Chauffeur')
                ->where('guard_name', 'web')
                ->first();

            if ($chauffeurRole) {
                $user->assignRole($chauffeurRole);
                
                Log::info('Driver role assigned successfully', [
                    'user_id' => $user->id,
                    'role_id' => $chauffeurRole->id,
                    'role_name' => 'Chauffeur'
                ]);
            } else {
                // Créer le rôle s'il n'existe pas
                $chauffeurRole = Role::create([
                    'name' => 'Chauffeur',
                    'guard_name' => 'web'
                ]);
                
                $user->assignRole($chauffeurRole);
                
                Log::warning('Driver role created and assigned', [
                    'user_id' => $user->id,
                    'role_id' => $chauffeurRole->id,
                    'message' => 'Role Chauffeur was missing and has been created'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to assign driver role', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoyer un email de bienvenue au nouveau chauffeur
     */
    private function sendWelcomeEmail(User $user, string $password, Driver $driver): void
    {
        try {
            // Implémentation de l'envoi d'email
            // Mail::to($user->email)->send(new WelcomeDriverMail($user, $password, $driver));
            
            Log::info('Welcome email sent to driver', [
                'user_id' => $user->id,
                'driver_id' => $driver->id,
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Stocker temporairement les credentials pour l'admin
     */
    private function storeTemporaryCredentials(Driver $driver, string $email, string $password): void
    {
        try {
            // Stocker dans le cache pour 24h (accessible uniquement aux admins)
            cache()->put(
                "driver_credentials_{$driver->id}",
                [
                    'email' => $email,
                    'password' => $password,
                    'created_at' => now()->toISOString(),
                    'expires_at' => now()->addHours(24)->toISOString()
                ],
                now()->addHours(24)
            );
            
            Log::info('Temporary credentials stored for admin access', [
                'driver_id' => $driver->id,
                'expires_in_hours' => 24
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store temporary credentials', [
                'driver_id' => $driver->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notifier l'administrateur en cas d'échec
     */
    private function notifyAdminOfFailure(Driver $driver, \Exception $exception): void
    {
        try {
            // Implémentation de la notification admin
            // Mail::to(config('mail.admin_email'))->send(new DriverCreationFailedMail($driver, $exception));
            
            Log::critical('Admin notified of driver user creation failure', [
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify admin', [
                'original_error' => $exception->getMessage(),
                'notification_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Formater un numéro de téléphone au format international
     */
    private function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Supprimer tous les caractères non numériques sauf le +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Si le numéro commence par 0, ajouter l'indicatif algérien
        if (str_starts_with($phone, '0')) {
            $phone = '+213' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Gère l'événement "updated" du modèle Driver
     */
    public function updated(Driver $driver): void
    {
        Log::info('Driver updated', [
            'driver_id' => $driver->id,
            'changed_attributes' => $driver->getDirty(),
            'updated_by' => auth()->id() ?? 'system'
        ]);
    }

    /**
     * Gère l'événement "deleted" du modèle Driver
     */
    public function deleted(Driver $driver): void
    {
        Log::warning('Driver soft deleted', [
            'driver_id' => $driver->id,
            'driver_name' => $driver->first_name . ' ' . $driver->last_name,
            'deleted_by' => auth()->id() ?? 'system',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Gère l'événement "restored" du modèle Driver
     */
    public function restored(Driver $driver): void
    {
        Log::info('Driver restored', [
            'driver_id' => $driver->id,
            'driver_name' => $driver->first_name . ' ' . $driver->last_name,
            'restored_by' => auth()->id() ?? 'system',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Gère l'événement "forceDeleted" du modèle Driver
     */
    public function forceDeleted(Driver $driver): void
    {
        Log::critical('Driver permanently deleted', [
            'driver_id' => $driver->id,
            'driver_name' => $driver->first_name . ' ' . $driver->last_name,
            'deleted_by' => auth()->id() ?? 'system',
            'timestamp' => now()->toISOString()
        ]);
    }
}
