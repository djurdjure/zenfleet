<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        $algerianCompanyTypes = ['SARL', 'SPA', 'SNC', 'EURL', 'EI'];
        $legalForm = $this->faker->randomElement($algerianCompanyTypes);

        // Wilayas algériennes
        $wilayas = [
            '01' => 'Adrar', '02' => 'Chlef', '03' => 'Laghouat', '04' => 'Oum El Bouaghi',
            '05' => 'Batna', '06' => 'Béjaïa', '07' => 'Biskra', '08' => 'Béchar',
            '09' => 'Blida', '10' => 'Bouira', '11' => 'Tamanrasset', '12' => 'Tébessa',
            '13' => 'Tlemcen', '14' => 'Tiaret', '15' => 'Tizi Ouzou', '16' => 'Alger',
            '17' => 'Djelfa', '18' => 'Jijel', '19' => 'Sétif', '20' => 'Saïda',
            '21' => 'Skikda', '22' => 'Sidi Bel Abbès', '23' => 'Annaba', '24' => 'Guelma',
            '25' => 'Constantine', '26' => 'Médéa', '27' => 'Mostaganem', '28' => 'M\'Sila',
            '29' => 'Mascara', '30' => 'Ouargla', '31' => 'Oran', '32' => 'El Bayadh',
            '33' => 'Illizi', '34' => 'Bordj Bou Arréridj', '35' => 'Boumerdès', '36' => 'El Tarf',
            '37' => 'Tindouf', '38' => 'Tissemsilt', '39' => 'El Oued', '40' => 'Khenchela',
            '41' => 'Souk Ahras', '42' => 'Tipaza', '43' => 'Mila', '44' => 'Aïn Defla',
            '45' => 'Naâma', '46' => 'Aïn Témouchent', '47' => 'Ghardaïa', '48' => 'Relizane'
        ];

        $wilayaCode = $this->faker->randomElement(array_keys($wilayas));
        $wilayaName = $wilayas[$wilayaCode];

        return [
            // Identification principale
            'uuid' => (string) Str::uuid(),
            'slug' => Str::slug($name),
            'name' => $name,
            'legal_name' => $name . ' ' . $legalForm,
            'display_name' => $name,
            'organization_type' => $this->faker->randomElement(['enterprise', 'sme', 'startup', 'public']),
            'industry' => $this->faker->randomElement(['Transport', 'Logistique', 'BTP', 'Agriculture', 'Commerce', 'Services']),
            'description' => $this->faker->realText(200),
            'tags' => json_encode(['transport', 'flotte', 'algérie']),

            // Informations légales algériennes
            'nif' => $this->generateNIF(),
            'ai' => $this->generateAI(),
            'nis' => $this->generateNIS(),
            'trade_register' => $this->generateTradeRegister($wilayaCode),
            'legal_form' => $legalForm,
            'registration_date' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'tax_id' => $this->faker->numerify('##########'),

            // Contact
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
            'fax' => $this->faker->optional(0.3)->numerify('+213 ## ## ## ## ##'),
            'website' => $this->faker->optional(0.7)->url(),

            // Adresse algérienne
            'address' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional(0.3)->secondaryAddress(),
            'city' => $wilayaName,
            'postal_code' => $wilayaCode . '000',
            'state_province' => $wilayaName,
            'country' => 'DZ',
            'wilaya' => $wilayaCode,
            'latitude' => $this->faker->latitude(18.96, 37.12), // Coordonnées Algérie
            'longitude' => $this->faker->longitude(-8.67, 11.98),

            // Adresse de facturation (optionnelle)
            'billing_address' => $this->faker->optional(0.2)->streetAddress(),
            'billing_city' => $this->faker->optional(0.2)->city(),
            'billing_postal_code' => $this->faker->optional(0.2)->postcode(),
            'billing_country' => 'DZ',

            // Localisation
            'timezone' => 'Africa/Algiers',
            'currency' => 'DZD',
            'language' => 'fr',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'number_format' => '1 234,56',

            // Abonnement et limites
            'subscription_plan' => $this->faker->randomElement(['trial', 'basic', 'professional', 'enterprise']),
            'subscription_starts_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'subscription_expires_at' => $this->faker->dateTimeBetween('now', '+2 years'),
            'billing_cycle' => $this->faker->randomElement(['monthly', 'quarterly', 'annually']),
            'monthly_cost' => $this->faker->randomFloat(2, 100, 5000),

            // Limites opérationnelles
            'max_users' => $this->faker->numberBetween(10, 500),
            'max_vehicles' => $this->faker->numberBetween(25, 1000),
            'max_drivers' => $this->faker->numberBetween(25, 1000),
            'max_storage_mb' => $this->faker->numberBetween(1024, 10240),
            'max_api_calls_per_hour' => $this->faker->numberBetween(1000, 10000),

            // Compteurs actuels
            'current_users' => $this->faker->numberBetween(1, 50),
            'current_vehicles' => $this->faker->numberBetween(5, 100),
            'current_drivers' => $this->faker->numberBetween(5, 100),
            'current_storage_mb' => $this->faker->numberBetween(100, 2048),
            'api_calls_this_hour' => $this->faker->numberBetween(0, 500),

            // Statut
            'status' => $this->faker->randomElement(['active', 'pending', 'suspended']),
            'activated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),

            // Paramètres opérationnels
            'working_days' => json_encode([1, 2, 3, 4, 5]), // Lundi à Vendredi
            'business_hours_start' => '08:00',
            'business_hours_end' => '17:00',

            // Informations du responsable légal
            'manager_name' => $this->faker->name(),
            'manager_nin' => $this->generateNIN(),
            'manager_function' => $this->faker->randomElement(['gerant', 'directeur_general', 'president']),
            'manager_email' => $this->faker->safeEmail(),

            // Sécurité
            'two_factor_required' => $this->faker->boolean(30),
            'password_expiry_days' => 90,
            'session_timeout_minutes' => 480,
            'audit_log_enabled' => true,
            'data_retention_period' => '3years',

            // Métadonnées
            'last_activity_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'total_logins' => $this->faker->numberBetween(50, 5000),
        ];
    }

    /**
     * Génère un NIF algérien (15 chiffres)
     */
    private function generateNIF(): string
    {
        return $this->faker->numerify('###############');
    }

    /**
     * Génère un AI algérien (14 chiffres)
     */
    private function generateAI(): string
    {
        return $this->faker->numerify('##############');
    }

    /**
     * Génère un NIS algérien (15 chiffres)
     */
    private function generateNIS(): string
    {
        return $this->faker->numerify('###############');
    }

    /**
     * Génère un numéro de registre de commerce algérien
     */
    private function generateTradeRegister(string $wilayaCode): string
    {
        $year = $this->faker->numberBetween(10, 25); // 2010-2025
        $sequence = $this->faker->numberBetween(100000, 999999);
        $type = $this->faker->randomElement(['A', 'B']); // A = Commerçant, B = Société
        $section = $this->faker->numberBetween(10, 99);

        return "{$wilayaCode}/{$year}-{$sequence} {$type} {$section}";
    }

    /**
     * Génère un NIN algérien (18 chiffres)
     */
    private function generateNIN(): string
    {
        return $this->faker->numerify('##################');
    }

    /**
     * Organisation active
     */
    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'activated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Organisation en attente
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'activated_at' => null,
        ]);
    }

    /**
     * Organisation enterprise
     */
    public function enterprise(): static
    {
        return $this->state(fn () => [
            'organization_type' => 'enterprise',
            'subscription_plan' => 'enterprise',
            'max_users' => 500,
            'max_vehicles' => 1000,
            'max_drivers' => 1000,
            'max_storage_mb' => 10240,
        ]);
    }

    /**
     * Organisation PME
     */
    public function sme(): static
    {
        return $this->state(fn () => [
            'organization_type' => 'sme',
            'subscription_plan' => 'professional',
            'max_users' => 50,
            'max_vehicles' => 100,
            'max_drivers' => 100,
            'max_storage_mb' => 2048,
        ]);
    }

    /**
     * Organisation avec responsable légal complet
     */
    public function withLegalManager(): static
    {
        return $this->state(fn () => [
            'manager_name' => $this->faker->name(),
            'manager_nin' => $this->generateNIN(),
            'manager_function' => 'gerant',
            'manager_email' => $this->faker->safeEmail(),
        ]);
    }
}