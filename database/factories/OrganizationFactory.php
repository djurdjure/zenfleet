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
            'name' => $name,
            'legal_name' => $name . ' ' . $legalForm,
            'organization_type' => $this->faker->randomElement(['enterprise', 'sme', 'startup', 'public', 'ngo', 'cooperative']),
            'industry' => $this->faker->randomElement(['Transport', 'Logistique', 'BTP', 'Agriculture', 'Commerce', 'Services']),
            'description' => $this->faker->realText(200),

            // Informations légales algériennes
            'trade_register' => $this->generateTradeRegister($wilayaCode),
            'nif' => $this->generateNIF(),
            'ai' => $this->generateAI(),
            'nis' => $this->generateNIS(),

            // Contact
            'primary_email' => $this->faker->unique()->companyEmail(),
            'phone_number' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
            'website' => $this->faker->optional(0.7)->url(),

            // Adresse algérienne
            'address' => $this->faker->streetAddress(),
            'city' => $this->generateCommuneName($wilayaName),
            'commune' => $this->faker->optional(0.6)->city(),
            'zip_code' => $wilayaCode . '000',
            'wilaya' => $wilayaCode,

            // Statut
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),

            // Informations du responsable légal
            'manager_first_name' => $this->faker->firstName(),
            'manager_last_name' => $this->faker->lastName(),
            'manager_nin' => $this->generateNIN(),
            'manager_address' => $this->faker->address(),
            'manager_dob' => $this->faker->dateTimeBetween('-65 years', '-25 years'),
            'manager_pob' => $this->faker->city(),
            'manager_phone_number' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
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
     * Génère un nom de commune réaliste pour une wilaya
     */
    private function generateCommuneName(string $wilayaName): string
    {
        $communeTypes = ['Centre', 'Nord', 'Sud', 'Est', 'Ouest'];
        $suffixes = ['El Jadida', 'El Qadima', 'El Kebira', 'El Saghira'];

        if ($this->faker->boolean(70)) {
            return $wilayaName;
        } else if ($this->faker->boolean(50)) {
            return $wilayaName . ' ' . $this->faker->randomElement($communeTypes);
        } else {
            return $this->faker->randomElement($suffixes);
        }
    }

    /**
     * Organisation active
     */
    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
        ]);
    }

    /**
     * Organisation en attente
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Organisation enterprise
     */
    public function enterprise(): static
    {
        return $this->state(fn () => [
            'organization_type' => 'enterprise',
        ]);
    }

    /**
     * Organisation PME
     */
    public function sme(): static
    {
        return $this->state(fn () => [
            'organization_type' => 'sme',
        ]);
    }
}