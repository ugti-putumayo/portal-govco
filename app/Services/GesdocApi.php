<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GesdocApi
{
    public function __construct(
        private string $base = '',
        private string $secret = '',
        private string $prefix = '',
        private ?string $apiKey = null
    ) {
        $this->base   = rtrim(config('services.gesdoc.base', env('GESDOC_API_BASE')), '/');
        $this->prefix = rtrim((string) config('services.gesdoc.prefix', ''), '/');
        $this->secret = (string) config('services.gesdoc.secret', env('GESDOC_SHARED_SECRET'));
        $this->apiKey = config('services.gesdoc.key', env('GESDOC_API_KEY'));
    }

    private function signaturePath(string $endpoint): string
    {
        $p = $this->prefix ? '/'.ltrim($this->prefix, '/') : '';
        return rtrim($p, '/') . $endpoint;
    }

    public function reporteLista(array $filtro): array
    {
        $endpoint = '/reportes/report_pqrds/json';
        
        $path_para_firma = $this->signaturePath($endpoint);

        $body  = json_encode(['filtro' => $filtro], JSON_UNESCAPED_UNICODE);
        $ts    = (string) time();
        $nonce = (string) \Illuminate\Support\Str::uuid();
        $sha   = hash('sha256', $body);

        $canonical = implode("\n", ['POST', $path_para_firma, $ts, $nonce, strtolower($sha)]);
        $signature = base64_encode(hash_hmac('sha256', $canonical, $this->secret, true));

        $headers = [
            'X-Timestamp'      => $ts,
            'X-Nonce'          => $nonce,
            'X-Content-Sha256' => $sha,
            'X-Signature'      => $signature,
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
        ];
        if (!empty($this->apiKey)) $headers['X-Api-Key'] = $this->apiKey;
        
        Log::error('HMAC OUT', ['path_call' => $endpoint, 'path_sign' => $path_para_firma, 'ts'=>$ts,'nonce'=>$nonce,'sha'=>$sha,'sig'=>$signature]);
        
        $resp = \Illuminate\Support\Facades\Http::timeout(60)
            ->withHeaders($headers)
            ->baseUrl($this->base)
            ->withBody($body, 'application/json')
            ->post($endpoint); 

        if (!$resp->ok()) {
            throw new \RuntimeException("GesDoc error {$resp->status()}: " . substr($resp->body(), 0, 500));
        }
        return $resp->json();
    }

    public function reporteResumen(array $filtro): array
    {
        $endpoint = '/reportes/report_pqrds/summary';
        
        $path_para_firma = $this->signaturePath($endpoint);

        $body  = json_encode(['filtro' => $filtro], JSON_UNESCAPED_UNICODE);
        $ts    = (string) time();
        $nonce = (string) \Illuminate\Support\Str::uuid();
        $sha   = hash('sha256', $body);

        $canonical = implode("\n", ['POST', $path_para_firma, $ts, $nonce, strtolower($sha)]);
        $signature = base64_encode(hash_hmac('sha256', $canonical, $this->secret, true));

        $headers = [
            'X-Timestamp'      => $ts,
            'X-Nonce'          => $nonce,
            'X-Content-Sha256' => $sha,
            'X-Signature'      => $signature,
            'Accept'           => 'application/json',
            'Content-Type'     => 'application/json',
        ];
        if (!empty($this->apiKey)) $headers['X-Api-Key'] = $this->apiKey;

        $resp = \Illuminate\Support\Facades\Http::timeout(60)
            ->withHeaders($headers)
            ->baseUrl($this->base)
            ->withBody($body, 'application/json')
            ->post($endpoint);

        if (!$resp->ok()) {
            throw new \RuntimeException("GesDoc summary error {$resp->status()}: " . substr($resp->body(), 0, 500));
        }
        return $resp->json();
    }
}