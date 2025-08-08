<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class PdfGenerationService
{
    protected string $serviceUrl;
    protected string $healthUrl;
    protected int $timeout;
    protected int $retries;

    public function __construct()
    {
        $this->serviceUrl = config('services.pdf.url', 'http://node:3000/generate-pdf');
        $this->healthUrl = config('services.pdf.health_url', 'http://node:3000/health');
        $this->timeout = (int) config('services.pdf.timeout', 120);
        $this->retries = (int) config('services.pdf.retries', 3);
    }

    public function generateFromHtml(string $html): string
    {
        
        if (!$this->isServiceHealthy()) {
            throw new \Exception("Le service PDF n'est pas disponible après plusieurs tentatives.");
        }

        $response = Http::timeout($this->timeout)
            // --- CORRECTION DÉFINITIVE ---
            // On désactive la vérification SSL pour les appels internes à Docker
            ->withoutVerifying()
            ->post($this->serviceUrl, [
                'html' => $html,
            ]);

        $response->throw();

        return $response->body();
    }

    private function isServiceHealthy(): bool
    {
        for ($i = 0; $i < $this->retries; $i++) {
            try {
                $response = Http::timeout(5)
                    // --- CORRECTION DÉFINITIVE ---
                    // On désactive la vérification SSL pour les appels internes à Docker
                    ->withoutVerifying()
                    ->get($this->healthUrl);

                if ($response->successful() && $response->json('status') === 'OK') {
                    return true;
                }
            } catch (RequestException $e) {
                Log::warning('Tentative de connexion au service PDF échouée.', ['attempt' => $i + 1, 'error' => $e->getMessage()]);
                // Attendre un peu avant la prochaine tentative
                if ($i < $this->retries - 1) {
                    sleep(1);
                }
            }
        }
        return false;
    }
}