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
    protected string $apiKey;

    public function __construct()
    {
        $this->serviceUrl = config('services.pdf.url', 'http://pdf-service:3000/generate-pdf');
        $this->healthUrl = config('services.pdf.health_url', 'http://pdf-service:3000/health');
        $this->timeout = (int) config('services.pdf.timeout', 120);
        $this->retries = (int) config('services.pdf.retries', 3);
        $this->apiKey = config('services.pdf.api_key', '');
    }

    public function generateFromHtml(string $html): string
    {
        if (!$this->isServiceHealthy()) {
            throw new \Exception("Le service PDF n'est pas disponible après plusieurs tentatives.");
        }

        $httpClient = Http::timeout($this->timeout);
        
        // Configuration SSL sécurisée pour production vs développement
        if (app()->environment('production')) {
            // En production, vérifier les certificats SSL
            $httpClient = $httpClient->withOptions([
                'verify' => true,
                'cert' => config('services.pdf.client_cert'),
                'ssl_key' => config('services.pdf.client_key'),
            ]);
        } else {
            // En développement local, on peut désactiver la vérification SSL pour les services internes
            $httpClient = $httpClient->withoutVerifying();
        }

        $headers = [];
        if ($this->apiKey) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            $headers['X-API-Key'] = $this->apiKey;
        }

        $response = $httpClient
            ->withHeaders($headers)
            ->post($this->serviceUrl, [
                'html' => $html,
                'options' => [
                    'format' => 'A4',
                    'margin' => [
                        'top' => '20mm',
                        'right' => '15mm',
                        'bottom' => '20mm',
                        'left' => '15mm'
                    ],
                    'printBackground' => true,
                    'preferCSSPageSize' => true
                ]
            ]);

        if (!$response->successful()) {
            Log::error('PDF Generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->serviceUrl
            ]);
            throw new \Exception("Échec de la génération PDF: " . $response->body());
        }

        return $response->body();
    }

    private function isServiceHealthy(): bool
    {
        for ($i = 0; $i < $this->retries; $i++) {
            try {
                $httpClient = Http::timeout(5);
                
                if (app()->environment('production')) {
                    $httpClient = $httpClient->withOptions(['verify' => true]);
                } else {
                    $httpClient = $httpClient->withoutVerifying();
                }

                $response = $httpClient->get($this->healthUrl);

                if ($response->successful() && in_array($response->json('status'), ['OK', 'healthy'])) {
                    return true;
                }
            } catch (RequestException $e) {
                Log::warning('Tentative de connexion au service PDF échouée.', [
                    'attempt' => $i + 1, 
                    'error' => $e->getMessage(),
                    'url' => $this->healthUrl
                ]);
                
                if ($i < $this->retries - 1) {
                    sleep(min(pow(2, $i), 5)); // Backoff exponentiel avec max 5s
                }
            }
        }
        return false;
    }

    /**
     * Génère un PDF depuis une vue Blade
     */
    public function generateFromView(string $view, array $data = []): string
    {
        $html = view($view, $data)->render();
        return $this->generateFromHtml($html);
    }

    /**
     * Sauvegarde un PDF dans le storage
     */
    public function generateAndStore(string $html, string $filename, string $disk = 'local'): string
    {
        $pdfContent = $this->generateFromHtml($html);
        
        $path = 'pdfs/' . date('Y/m/') . $filename;
        \Storage::disk($disk)->put($path, $pdfContent);
        
        return $path;
    }
}
