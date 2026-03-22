<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class RbacFreezeCheckCommandTest extends TestCase
{
    public function test_it_fails_when_a_deprecated_seeder_is_referenced_by_an_active_entrypoint(): void
    {
        $basePath = $this->makeFixtureDirectory();

        File::ensureDirectoryExists($basePath . '/database/seeders');
        File::put(
            $basePath . '/database/seeders/DatabaseSeeder.php',
            "<?php\nclass DatabaseSeeder { protected array \$seeders = ['RolesAndPermissionsSeeder']; }\n"
        );

        Config::set('rbac_freeze.official_roles', ['Admin', 'Super Admin']);
        Config::set('rbac_freeze.protected_entrypoints', ['database/seeders/DatabaseSeeder.php']);
        Config::set('rbac_freeze.deprecated_seeders', ['RolesAndPermissionsSeeder']);
        Config::set('rbac_freeze.policy_file_expectations', []);

        $this->artisan('rbac:freeze-check', ['--base-path' => $basePath])
            ->expectsOutputToContain('Deprecated RBAC seeder referenced')
            ->assertExitCode(1);
    }

    public function test_it_reports_warnings_without_failing_by_default(): void
    {
        $basePath = $this->makeFixtureDirectory();

        Config::set('rbac_freeze.official_roles', ['Admin', 'Super Admin']);
        Config::set('rbac_freeze.protected_entrypoints', []);
        Config::set('rbac_freeze.deprecated_seeders', ['LegacySeeder']);
        Config::set('rbac_freeze.policy_file_expectations', [
            'App\\Policies\\MissingPolicy' => 'app/Policies/MissingPolicy.php',
        ]);

        $this->artisan('rbac:freeze-check', ['--base-path' => $basePath])
            ->expectsOutputToContain('Warnings')
            ->assertExitCode(0);
    }

    private function makeFixtureDirectory(): string
    {
        $directory = sys_get_temp_dir() . '/zenfleet-rbac-freeze-' . bin2hex(random_bytes(8));

        File::deleteDirectory($directory);
        File::ensureDirectoryExists($directory);

        return $directory;
    }
}
