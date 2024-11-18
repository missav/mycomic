<?php

namespace App\Cloudflare;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Cloudflare
{
    protected ?string $token = null;

    protected ?string $email = null;

    protected ?string $key = null;

    public function __construct(string $token, ?string $key = null)
    {
        if ($key) {
            $this->email = $token;
            $this->key = $key;
        } else {
            $this->token = $token;
        }
    }

    public function purge(string $zoneId, string|array $files): array
    {
        $files = (array) $files;

        return collect($files)
            ->chunk(30)
            ->map(fn (Collection $chunkedFiles) => $this->api("zones/{$zoneId}/purge_cache", [
                'files' => $chunkedFiles->values()->all(),
            ], 'post'))
            ->all();
    }

    public function purgeAll(string $zoneId): array
    {
        return $this->api("zones/{$zoneId}/purge_cache", [
            'purge_everything' => true,
        ], 'post');
    }

    public function api(string $path, array $data = [], string $method = 'get'): array
    {
        $path = Str::start($path, '/');

        $http = Http::connectTimeout(30)->timeout(30)->asJson();

        if ($this->token) {
            $http = $http->withToken($this->token);
        } else {
            $http = $http->withHeaders([
                'X-Auth-Email' => $this->email,
                'X-Auth-Key' => $this->key,
            ]);
        }

        return $http->{$method}("https://api.cloudflare.com/client/v4{$path}", $data)
            ->throw()
            ->json() ?? [];
    }
}
