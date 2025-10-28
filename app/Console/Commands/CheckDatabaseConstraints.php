<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ====================================================================
 * 🔍 CHECK DATABASE CONSTRAINTS - ENTERPRISE GRADE
 * ====================================================================
 * 
 * Commande pour vérifier les contraintes NOT NULL et suggérer des
 * corrections pour éviter les erreurs de violation de contraintes.
 * 
 * Usage: php artisan db:check-constraints [--table=suppliers]
 * 
 * @package App\Console\Commands
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
class CheckDatabaseConstraints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check-constraints 
                            {--table= : Specific table to check}
                            {--fix : Apply automatic fixes where possible}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check database constraints and suggest fixes for potential issues';

    /**
     * Tables critiques à vérifier
     */
    private const CRITICAL_TABLES = [
        'suppliers',
        'vehicles',
        'users',
        'vehicle_expenses',
        'repair_requests',
        'maintenances'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('====================================================================');
        $this->info('🔍 DATABASE CONSTRAINTS CHECKER - ENTERPRISE GRADE');
        $this->info('====================================================================');
        $this->newLine();

        $table = $this->option('table');
        $autoFix = $this->option('fix');

        if ($table) {
            $this->checkTable($table, $autoFix);
        } else {
            foreach (self::CRITICAL_TABLES as $table) {
                if (Schema::hasTable($table)) {
                    $this->checkTable($table, $autoFix);
                }
            }
        }

        $this->newLine();
        $this->info('✅ Vérification terminée!');
        
        return Command::SUCCESS;
    }

    /**
     * Vérifier une table spécifique
     */
    private function checkTable(string $table, bool $autoFix = false): void
    {
        $this->info("📋 Vérification de la table: $table");
        $this->line(str_repeat('-', 70));

        // Récupérer les informations des colonnes
        $columns = DB::select("
            SELECT 
                column_name,
                is_nullable,
                column_default,
                data_type,
                character_maximum_length,
                numeric_precision,
                numeric_scale
            FROM information_schema.columns
            WHERE table_schema = 'public'
            AND table_name = ?
            ORDER BY ordinal_position
        ", [$table]);

        $issues = [];
        $warnings = [];
        $suggestions = [];

        foreach ($columns as $column) {
            // Vérifier les colonnes NOT NULL sans valeur par défaut
            if ($column->is_nullable === 'NO' && is_null($column->column_default)) {
                // Exceptions pour les colonnes qui doivent être NOT NULL
                $exceptions = ['id', 'created_at', 'updated_at', 'organization_id'];
                
                if (!in_array($column->column_name, $exceptions)) {
                    // Vérifier si c'est un champ numérique
                    if (in_array($column->data_type, ['integer', 'bigint', 'smallint', 'decimal', 'numeric', 'real', 'double precision'])) {
                        $issues[] = [
                            'column' => $column->column_name,
                            'type' => 'NOT_NULL_NO_DEFAULT',
                            'data_type' => $column->data_type,
                            'message' => "Colonne NOT NULL sans valeur par défaut",
                            'fix' => $this->getSuggestedDefault($column)
                        ];
                    }
                    // Vérifier les champs de scoring spécifiques
                    if (str_contains($column->column_name, 'score') || 
                        str_contains($column->column_name, 'rating')) {
                        $issues[] = [
                            'column' => $column->column_name,
                            'type' => 'SCORE_WITHOUT_DEFAULT',
                            'data_type' => $column->data_type,
                            'message' => "Colonne de score sans valeur par défaut",
                            'fix' => $this->getSuggestedDefault($column)
                        ];
                    }
                }
            }

            // Vérifier les colonnes DECIMAL avec mauvaise précision
            if ($column->data_type === 'numeric' || $column->data_type === 'decimal') {
                if (str_contains($column->column_name, 'score') && $column->numeric_precision < 5) {
                    $warnings[] = [
                        'column' => $column->column_name,
                        'type' => 'PRECISION_TOO_LOW',
                        'message' => "Précision insuffisante pour un score (actuellement: {$column->numeric_precision},{$column->numeric_scale})"
                    ];
                }
            }
        }

        // Afficher les résultats
        if (count($issues) > 0) {
            $this->error("❌ Problèmes détectés: " . count($issues));
            
            $headers = ['Colonne', 'Type', 'Problème', 'Solution suggérée'];
            $rows = [];
            
            foreach ($issues as $issue) {
                $rows[] = [
                    $issue['column'],
                    $issue['data_type'],
                    $issue['message'],
                    $issue['fix']['description'] ?? 'N/A'
                ];
            }
            
            $this->table($headers, $rows);

            // Appliquer les corrections si demandé
            if ($autoFix) {
                $this->info("🔧 Application des corrections automatiques...");
                $this->applyFixes($table, $issues);
            } else {
                $this->newLine();
                $this->warn("💡 Pour appliquer les corrections automatiques, utilisez: --fix");
                $this->generateFixSQL($table, $issues);
            }
        } else {
            $this->info("✅ Aucun problème détecté");
        }

        if (count($warnings) > 0) {
            $this->newLine();
            $this->warn("⚠️  Avertissements: " . count($warnings));
            foreach ($warnings as $warning) {
                $this->line("   - {$warning['column']}: {$warning['message']}");
            }
        }

        $this->newLine();
    }

    /**
     * Obtenir la valeur par défaut suggérée
     */
    private function getSuggestedDefault(object $column): array
    {
        $columnName = $column->column_name;
        $dataType = $column->data_type;

        // Scores et ratings
        if (str_contains($columnName, 'quality_score') || 
            str_contains($columnName, 'reliability_score')) {
            return [
                'value' => '75.00',
                'description' => 'Score par défaut de 75/100',
                'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT 75.00"
            ];
        }

        if (str_contains($columnName, 'rating')) {
            return [
                'value' => '3.75',
                'description' => 'Rating par défaut de 3.75/5',
                'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT 3.75"
            ];
        }

        // Numériques
        if (in_array($dataType, ['integer', 'bigint', 'smallint'])) {
            if (str_contains($columnName, 'count') || 
                str_contains($columnName, 'total') ||
                str_contains($columnName, 'quantity')) {
                return [
                    'value' => '0',
                    'description' => 'Zéro par défaut pour compteur',
                    'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT 0"
                ];
            }
        }

        if (in_array($dataType, ['decimal', 'numeric', 'real', 'double precision'])) {
            if (str_contains($columnName, 'amount') || 
                str_contains($columnName, 'price') ||
                str_contains($columnName, 'cost')) {
                return [
                    'value' => '0.00',
                    'description' => '0.00 par défaut pour montant',
                    'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT 0.00"
                ];
            }
        }

        // Booléens
        if ($dataType === 'boolean') {
            if (str_contains($columnName, 'is_active')) {
                return [
                    'value' => 'true',
                    'description' => 'Actif par défaut',
                    'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT true"
                ];
            }
            return [
                'value' => 'false',
                'description' => 'False par défaut',
                'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT false"
            ];
        }

        // Texte
        if (in_array($dataType, ['character varying', 'text'])) {
            return [
                'value' => "''",
                'description' => 'Chaîne vide par défaut',
                'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName SET DEFAULT ''"
            ];
        }

        return [
            'value' => 'NULL',
            'description' => 'Rendre nullable',
            'sql' => "ALTER TABLE {table} ALTER COLUMN $columnName DROP NOT NULL"
        ];
    }

    /**
     * Générer le SQL de correction
     */
    private function generateFixSQL(string $table, array $issues): void
    {
        $this->info("📝 SQL de correction suggéré:");
        $this->line(str_repeat('-', 70));
        
        foreach ($issues as $issue) {
            if (isset($issue['fix']['sql'])) {
                $sql = str_replace('{table}', $table, $issue['fix']['sql']);
                $this->line($sql . ';');
            }
        }
        
        $this->line(str_repeat('-', 70));
    }

    /**
     * Appliquer les corrections automatiques
     */
    private function applyFixes(string $table, array $issues): void
    {
        $fixed = 0;
        $failed = 0;

        foreach ($issues as $issue) {
            if (isset($issue['fix']['sql'])) {
                $sql = str_replace('{table}', $table, $issue['fix']['sql']);
                
                try {
                    DB::statement($sql);
                    $this->info("   ✅ {$issue['column']} corrigé");
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("   ❌ {$issue['column']}: " . $e->getMessage());
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info("📊 Résultats: $fixed corrigés, $failed échecs");
    }
}
