<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

/**
 * 📑 Enterprise PDF Generation Service
 *
 * Architecture hybride avec:
 * - Microservice Node.js (Puppeteer) comme solution primaire
 * - DomPDF comme fallback fiable pour développement local
 *
 * @package App\Services
 * @version 3.0 - Hybrid Architecture with DomPDF Fallback
 */
class PdfGenerationService
{
    protected string $serviceUrl;
    protected string $healthUrl;
    protected int $timeout;
    protected int $retries;
    protected string $apiKey;
    protected bool $useFallback;

    public function __construct()
    {
        $this->serviceUrl = config('services.pdf.url', 'http://pdf-service:3000/generate-pdf');
        $this->healthUrl = config('services.pdf.health_url', 'http://pdf-service:3000/health');
        $this->timeout = (int) config('services.pdf.timeout', 60);
        $this->retries = (int) config('services.pdf.retries', 2);
        $this->apiKey = config('services.pdf.api_key', '');
        // Allow forcing fallback via env variable
        $this->useFallback = config('services.pdf.use_fallback', false);
    }

    /**
     * Generate PDF from HTML content
     * 
     * Strategy:
     * 1. Try microservice if available and not in fallback mode
     * 2. Use DomPDF as fallback
     */
    public function generateFromHtml(string $html, array $options = []): string
    {
        // If fallback is forced or microservice is unhealthy, use DomPDF directly
        if ($this->useFallback || !$this->isServiceHealthy()) {
            Log::info('PDF Generation: Using DomPDF fallback');
            return $this->generateWithDomPdf($html, $options);
        }

        try {
            return $this->generateWithMicroservice($html, $options);
        } catch (\Exception $e) {
            Log::warning('Microservice PDF failed, falling back to DomPDF', [
                'error' => $e->getMessage()
            ]);
            return $this->generateWithDomPdf($html, $options);
        }
    }

    /**
     * Generate PDF using DomPDF (Laravel package)
     */
    protected function generateWithDomPdf(string $html, array $options = []): string
    {
        $pdf = Pdf::loadHTML($html);

        // Configure for A4 professional output
        $format = $options['format'] ?? 'a4';
        $orientation = $options['orientation'] ?? ($options['landscape'] ?? false ? 'landscape' : 'portrait');

        $pdf->setPaper($format, $orientation);
        $pdf->setOption([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
            'dpi' => 150,
            'debugKeepTemp' => false,
        ]);

        return $pdf->output();
    }

    /**
     * Generate PDF using Microservice (Puppeteer/Chrome)
     */
    protected function generateWithMicroservice(string $html, array $options = []): string
    {
        $httpClient = Http::timeout($this->timeout);

        if (app()->environment('production')) {
            $httpClient = $httpClient->withOptions([
                'verify' => true,
                'cert' => config('services.pdf.client_cert'),
                'ssl_key' => config('services.pdf.client_key'),
            ]);
        } else {
            $httpClient = $httpClient->withoutVerifying();
        }

        $headers = [];
        if ($this->apiKey) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            $headers['X-API-Key'] = $this->apiKey;
        }

        // Merge default options with passed options
        $pdfOptions = array_merge([
            'format' => 'A4',
            'margin' => [
                'top' => '15mm',
                'right' => '12mm',
                'bottom' => '15mm',
                'left' => '12mm'
            ],
            'printBackground' => true,
            'preferCSSPageSize' => true,
            'landscape' => $options['landscape'] ?? false
        ], $options);

        $response = $httpClient
            ->withHeaders($headers)
            ->post($this->serviceUrl, [
                'html' => $html,
                'options' => $pdfOptions
            ]);

        if (!$response->successful()) {
            throw new \Exception("Microservice PDF failed: " . $response->body());
        }

        return $response->body();
    }

    /**
     * Check if microservice is healthy (with quick timeout)
     */
    private function isServiceHealthy(): bool
    {
        try {
            $httpClient = Http::timeout(3); // Quick timeout for health check

            if (!app()->environment('production')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get($this->healthUrl);

            if ($response->successful()) {
                $status = $response->json('status');
                return in_array($status, ['OK', 'healthy', 'ok']);
            }
        } catch (\Exception $e) {
            Log::debug('PDF Microservice health check failed', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Generate PDF from a Blade view
     */
    public function generateFromView(string $view, array $data = []): string
    {
        $html = view($view, $data)->render();
        return $this->generateFromHtml($html);
    }

    /**
     * Generate and store PDF in filesystem
     */
    public function generateAndStore(string $html, string $filename, string $disk = 'local'): string
    {
        $pdfContent = $this->generateFromHtml($html);

        $path = 'pdfs/' . date('Y/m/') . $filename;
        \Storage::disk($disk)->put($path, $pdfContent);

        return $path;
    }
}
