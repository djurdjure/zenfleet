<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();

        return [
            'name' => "{$first} {$last}",
            'first_name' => $first,
            'last_name' => $last,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => '+213 ' . $this->faker->numerify('## ## ## ## ##'),
        ];
    }

    /**
     * Utilisateur non vérifié
     */
    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Super Administrateur
     */
    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'name' => 'Super Administrateur',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@zenfleet.dz',
        ]);
    }

    /**
     * Administrateur d'organisation
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'Admin Organisation',
            'first_name' => 'Admin',
            'last_name' => 'Organisation',
        ]);
    }

    /**
     * Gestionnaire de flotte
     */
    public function fleetManager(): static
    {
        return $this->state(fn () => [
            'name' => 'Gestionnaire Flotte',
            'first_name' => 'Gestionnaire',
            'last_name' => 'Flotte',
        ]);
    }

    /**
     * Superviseur
     */
    public function supervisor(): static
    {
        return $this->state(fn () => [
            'name' => 'Superviseur',
            'first_name' => 'Superviseur',
            'last_name' => 'Équipe',
        ]);
    }

    /**
     * Chauffeur
     */
    public function driver(): static
    {
        return $this->state(fn () => [
            'name' => 'Chauffeur',
            'first_name' => 'Chauffeur',
            'last_name' => 'Professionnel',
        ]);
    }

    /**
     * Utilisateur avec une organisation spécifique
     */
    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn () => [
            // On n'ajoute pas organization_id ici car la relation n'existe peut-être pas encore
        ]);
    }
}