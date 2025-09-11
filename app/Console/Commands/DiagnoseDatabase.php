<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

class DiagnoseDatabase extends Command
{
    protected $signature = 'zenfleet:diagnose {--full : Diagnostic complet avec tests de performance}';
    protected $description = 'Diagnostic complet de l\'état du système ZenFleet';

    public function handle(): int
    {
        $this->info('🔍 Diagnostic complet du système ZenFleet...');
        
        $this->checkDatabase();
        $this->checkRedis();
        $this->checkPdfService();
        $this->checkFileSystem();
        
        if ($this->option('full')) {
            $this->checkPerformance();
        }
        
        $this->info("\n✅ Diagnostic terminé !");
        return 0;
    }

    private function checkDatabase(): void
    {
        $this->info("\n📊 État de la base de données :");
        
        try {
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            $this->info("   ✅ Connexion PostgreSQL : OK");
            
            // Vérifier les tables critiques
            $requiredTables = [
                'users', 'organizations', 'vehicles', 'drivers', 
                'assignments', 'maintenance_plans', 'maintenance_logs'
            ];

            foreach ($requiredTables as $table) {
                if (Schema::hasTable($table)) {
                    $count = DB::table($table)->count();
                    $this->info("   ✅ {$table} : {$count} enregistrements");
                } else {
                    $this->error("   ❌ {$table} : Table manquante");
                }
            }

            // Vérifier l'intégrité des données
            $orphanedDrivers = DB::table('drivers')
                ->leftJoin('organizations', 'drivers.organization_id', '=', 'organizations.id')
                ->whereNull('organizations.id')
                ->count();
            
            if ($orphanedDrivers > 0) {
                $this->warn("   ⚠️  {$orphanedDrivers} chauffeurs orphelins détectés");
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Erreur base de données : " . $e->getMessage());
        }
    }

    private function checkRedis(): void
    {
        $this->info("\n🗄️ État du cache Redis :");
        
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            
            $this->info("   ✅ Connexion Redis : OK");
            $this->info("   📈 Mémoire utilisée : " . ($info['used_memory_human'] ?? 'N/A'));
            $this->info("   📊 Clients connectés : " . ($info['connected_clients'] ?? 'N/A'));
            
            // Test de performance du cache
            $start = microtime(true);
            $redis->set('zenfleet:health:test', 'ok', 'EX', 60);
            $result = $redis->get('zenfleet:health:test');
            $time = (microtime(true) - $start) * 1000;
            
            if ($result === 'ok') {
                $this->info("   ⚡ Performance cache : {$time}ms");
            } else {
                $this->error("   ❌ Test cache échoué");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur Redis : " . $e->getMessage());
        }
    }

    private function checkPdfService(): void
    {
        $this->info("\n📄 État du service PDF :");
        
        try {
            $healthUrl = config('services.pdf.health_url', 'http://pdf-service:3000/health');
            $response = Http::timeout(5)->get($healthUrl);
            
            if ($response->successful() && $response->json('status') === 'OK') {
                $this->info("   ✅ Service PDF : Disponible");
                $this->info("   🕐 Temps de réponse : {$response->handlerStats()['total_time']}s");
            } else {
                $this->error("   ❌ Service PDF indisponible");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur service PDF : " . $e->getMessage());
        }
    }

    private function checkFileSystem(): void
    {
        $this->info("\n💾 État du système de fichiers :");
        
        $storagePath = storage_path();
        $logsPath = storage_path('logs');
        
        $this->info("   📁 Espace disque : " . $this->formatBytes(disk_free_space($storagePath)));
        
        if (is_writable($logsPath)) {
            $this->info("   ✅ Répertoire logs : Accessible en écriture");
        } else {
            $this->error("   ❌ Répertoire logs : Problème d'écriture");
        }

        // Vérifier les permissions critiques
        $criticalPaths = [
            'storage/app',
            'storage/logs',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views'
        ];

        foreach ($criticalPaths as $path) {
            $fullPath = base_path($path);
            if (is_writable($fullPath)) {
                $this->info("   ✅ {$path} : OK");
            } else {
                $this->error("   ❌ {$path} : Problème de permissions");
            }
        }
    }

    private function checkPerformance(): void
    {
        $this->info("\n⚡ Tests de performance :");
        
        // Test requête simple
        $start = microtime(true);
        DB::table('users')->count();
        $dbTime = (microtime(true) - $start) * 1000;
        $this->info("   📊 Requête DB simple : {$dbTime}ms");
        
        // Test requête complexe
        $start = microtime(true);
        DB::table('vehicles')
            ->join('organizations', 'vehicles.organization_id', '=', 'organizations.id')
            ->select('vehicles.*', 'organizations.name as org_name')
            ->limit(100)
            ->get();
        $complexTime = (microtime(true) - $start) * 1000;
        $this->info("   🔄 Requête DB complexe : {$complexTime}ms");
        
        if ($dbTime > 100 || $complexTime > 500) {
            $this->warn("   ⚠️  Performances DB dégradées détectées");
        }
    }

    private function formatBytes(int $size, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
