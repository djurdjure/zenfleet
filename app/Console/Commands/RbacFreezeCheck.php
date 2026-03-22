<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RbacFreezeCheck extends Command
{
    protected $signature = 'rbac:freeze-check
        {--base-path= : Base path to scan, defaults to the Laravel project root}
        {--json : Output JSON payload}
        {--fail-on-warning : Exit with failure when warnings are present}';

    protected $description = 'Phase 0 RBAC freeze guardrail for active entrypoints and governance drift.';

    public function handle(): int
    {
        $basePath = $this->resolveBasePath();
        $config = config('rbac_freeze');

        $errors = [];
        $warnings = [];

        $this->checkOfficialRoles($config['official_roles'] ?? [], $errors);
        $this->checkDeprecatedSeedersInEntrypoints(
            $basePath,
            $config['protected_entrypoints'] ?? [],
            $config['deprecated_seeders'] ?? [],
            $errors
        );
        $this->checkDeprecatedSeederFilesPresent(
            $basePath,
            $config['deprecated_seeders'] ?? [],
            $warnings
        );
        $this->checkExpectedPolicyFiles(
            $basePath,
            $config['policy_file_expectations'] ?? [],
            $warnings
        );

        $report = [
            'status' => empty($errors) && (empty($warnings) || !$this->option('fail-on-warning'))
                ? 'ok'
                : 'failed',
            'base_path' => $basePath,
            'errors' => $errors,
            'warnings' => $warnings,
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->info('RBAC freeze check');
            $this->line("Base path: {$basePath}");
            $this->newLine();

            if ($errors !== []) {
                $this->error('Errors');
                foreach ($errors as $error) {
                    $this->line("- {$error}");
                }
                $this->newLine();
            }

            if ($warnings !== []) {
                $this->warn('Warnings');
                foreach ($warnings as $warning) {
                    $this->line("- {$warning}");
                }
                $this->newLine();
            }

            if ($errors === [] && $warnings === []) {
                $this->info('No RBAC freeze drift detected.');
            }
        }

        if ($errors !== []) {
            return self::FAILURE;
        }

        if ($warnings !== [] && $this->option('fail-on-warning')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function resolveBasePath(): string
    {
        $basePath = $this->option('base-path');
        if (is_string($basePath) && trim($basePath) !== '') {
            return rtrim($basePath, DIRECTORY_SEPARATOR);
        }

        return base_path();
    }

    private function checkOfficialRoles(array $officialRoles, array &$errors): void
    {
        $normalized = collect($officialRoles)
            ->map(fn ($role) => is_string($role) ? trim($role) : '')
            ->filter()
            ->values();

        if ($normalized->isEmpty()) {
            $errors[] = 'RBAC freeze configuration has no official roles.';
            return;
        }

        $duplicates = $normalized
            ->countBy()
            ->filter(fn (int $count) => $count > 1)
            ->keys()
            ->values();

        foreach ($duplicates as $duplicateRole) {
            $errors[] = "Duplicate official role detected in freeze config: {$duplicateRole}";
        }
    }

    private function checkDeprecatedSeedersInEntrypoints(
        string $basePath,
        array $entrypoints,
        array $deprecatedSeeders,
        array &$errors
    ): void {
        foreach ($entrypoints as $relativePath) {
            $absolutePath = $basePath . DIRECTORY_SEPARATOR . $relativePath;

            if (!File::exists($absolutePath)) {
                continue;
            }

            $contents = File::get($absolutePath);

            foreach ($deprecatedSeeders as $deprecatedSeeder) {
                if (str_contains($contents, $deprecatedSeeder)) {
                    $errors[] = "Deprecated RBAC seeder referenced from active entrypoint {$relativePath}: {$deprecatedSeeder}";
                }
            }
        }
    }

    private function checkDeprecatedSeederFilesPresent(
        string $basePath,
        array $deprecatedSeeders,
        array &$warnings
    ): void {
        foreach ($deprecatedSeeders as $deprecatedSeeder) {
            $relativePath = 'database/seeders/' . $deprecatedSeeder . '.php';
            $absolutePath = $basePath . DIRECTORY_SEPARATOR . $relativePath;

            if (File::exists($absolutePath)) {
                $warnings[] = "Historical RBAC seeder still present in repository: {$relativePath}";
            }
        }
    }

    private function checkExpectedPolicyFiles(
        string $basePath,
        array $policyExpectations,
        array &$warnings
    ): void {
        foreach ($policyExpectations as $className => $relativePath) {
            $absolutePath = $basePath . DIRECTORY_SEPARATOR . $relativePath;

            if (!File::exists($absolutePath)) {
                $warnings[] = "Expected policy mapping target missing from repository: {$className} ({$relativePath})";
            }
        }
    }
}
