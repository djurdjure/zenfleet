<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::first();

        if (!$organization) {
            $this->command->error('No organization found.');
            return;
        }

        // Create simple admin for testing
        $admin = User::firstOrCreate(
            ['email' => 'admin@zenfleet.dz'],
            [
                'name' => 'Admin Simple',
                'first_name' => 'Admin',
                'last_name' => 'Simple',
                'email' => 'admin@zenfleet.dz',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'organization_id' => $organization->id,
                'is_super_admin' => true,
            ]
        );

        $this->command->info('Simple admin created:');
        $this->command->info('Email: admin@zenfleet.dz');
        $this->command->info('Password: admin123');
    }
}
