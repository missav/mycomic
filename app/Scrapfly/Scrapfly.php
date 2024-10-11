<?php

namespace App\Scrapfly;

use Illuminate\Support\Facades\Http;

class Scrapfly
{
    const PUBLIC_RESIDENTIAL_POOL = 'public_residential_pool';

    const PUBLIC_DATACENTER_POOL = 'public_datacenter_pool';

    public function __construct(protected string $apiKey) {}

    public function scrap(
        string $url,
        string $method = 'get',
        array $data = [],
        array $headers = [],
        array $cookies = [],
        string $proxyPool = self::PUBLIC_DATACENTER_POOL,
        bool $antiScrapingProtection = false,
        bool $jsRendering = false,
        ?string $country = null,
    ): array
    {
        $method = strtolower($method);

        if ($cookies) {
            $headers['cookie'] = collect($cookies)->map(fn (string $value, string $key) => "{$key}={$value}")->implode(';');
        }

        $apiEndpoint = 'https://api.scrapfly.io/scrape';

        $apiSettings = [
            'key' => $this->apiKey,
            'url' => $url,
            'headers' => $headers,
            'proxy_pool' => $proxyPool,
            'asp' => $antiScrapingProtection ? 'true' : 'false',
            'render_js' => $jsRendering ? 'true' : 'false',
            'timeout' => 150000,
            'country' => $country,
        ];

        $http = Http::connectTimeout(180)->timeout(180);

        $http = match ($method) {
            'get' => $http->get($apiEndpoint, $apiSettings),
            default => $http->asForm()->withOptions(['query' => $apiSettings])->{$method}($apiEndpoint, $data),
        };

        $response = $http->json();

        if (isset($response['error_id'])) {
            throw new ScrapflyRequestException($response);
        }

        if (isset($response['result']['error'])) {
            throw new ScrapflyRequestException($response['result']['error']);
        }

        if (empty($response)) {
            throw new ScrapflyReachUsageLimitException;
        }

        return $response;
    }
}
