<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure the 5 target statuses exist
        $statuses = [
            [
                'name' => 'Disponible',
                'slug' => 'disponible',
                'description' => 'Chauffeur disponible pour nouvelle affectation',
                'color' => '#10b981', // Green
                'icon' => 'fa-check-circle',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'En mission',
                'slug' => 'en_mission',
                'description' => 'Chauffeur actuellement affecté à un véhicule',
                'color' => '#3b82f6', // Blue
                'icon' => 'fa-car',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'En formation',
                'slug' => 'en_formation',
                'description' => 'Chauffeur en période de formation',
                'color' => '#8b5cf6', // Purple
                'icon' => 'fa-graduation-cap',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'En congé',
                'slug' => 'en_conge',
                'description' => 'Chauffeur en congé',
                'color' => '#f59e0b', // Amber
                'icon' => 'fa-umbrella-beach',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Autre',
                'slug' => 'autre',
                'description' => 'Autre statut ou inactif',
                'color' => '#6b7280', // Gray
                'icon' => 'fa-question-circle',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('driver_statuses')->updateOrInsert(
                ['slug' => $status['slug']],
                array_merge($status, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // 2. Migrate existing drivers to new statuses
        $this->migrateDriverStatus('actif', 'disponible');
        $this->migrateDriverStatus('active', 'disponible'); // English variant
        $this->migrateDriverStatus('available', 'disponible');

        $this->migrateDriverStatus('en-mission', 'en_mission'); // Fix slug format
        $this->migrateDriverStatus('en_service', 'en_mission');

        $this->migrateDriverStatus('formation', 'en_formation');

        // 'en_conge' stays 'en_conge', but we might catch duplicate slugs if any
        $this->migrateDriverStatus('conge', 'en_conge');

        // Catch-all for others to 'autre'
        $allowedSlugs = ['disponible', 'en_mission', 'en_formation', 'en_conge', 'autre'];

        // Find all statuses NOT in the allowed list
        $legacyStatuses = DB::table('driver_statuses')
            ->whereNotIn('slug', $allowedSlugs)
            ->pluck('id', 'slug');

        $autreStatusId = DB::table('driver_statuses')->where('slug', 'autre')->value('id');

        foreach ($legacyStatuses as $slug => $id) {
            DB::table('drivers')
                ->where('status_id', $id)
                ->update(['status_id' => $autreStatusId]);
        }

        // 3. Delete legacy statuses
        DB::table('driver_statuses')
            ->whereNotIn('slug', $allowedSlugs)
            ->delete();
    }

    /**
     * Helper to migrate drivers from one status slug to another.
     */
    protected function migrateDriverStatus(string $fromSlug, string $toSlug): void
    {
        $fromId = DB::table('driver_statuses')->where('slug', $fromSlug)->value('id');
        $toId = DB::table('driver_statuses')->where('slug', $toSlug)->value('id');

        if ($fromId && $toId) {
            DB::table('drivers')
                ->where('status_id', $fromId)
                ->update(['status_id' => $toId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily restore the deleted statuses without a backup.
        // This migration is considered destructive for legacy statuses.
    }
};
