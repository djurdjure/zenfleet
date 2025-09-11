<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // en millisecondes
        $memoryUsage = $endMemory - $startMemory;

        // Logger les performances pour les requêtes lentes
        if ($executionTime > 1000 || $memoryUsage > 10 * 1024 * 1024) { // > 1s ou > 10MB
            Log::channel('performance')->warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time_ms' => round($executionTime, 2),
                'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
            ]);
        }

        // Ajouter les headers de performance en développement
        if (app()->environment('local')) {
            $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', round($memoryUsage / 1024 / 1024, 2) . 'MB');
        }

        return $response;
    }
}
