<?php

namespace App\Ploi;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ploi
{
    public function __construct(protected string $token) {}

    public function servers(): Collection
    {
        $servers = collect();

        $page = 1;

        while (true) {
            $serversData = $this->api('servers', ['page' => $page++]);

            $servers = $servers->concat($serversData);

            if (count($serversData) < 15) {
                break;
            }
        }

        return $servers;
    }

    public function restartServer(int $serverId): ?array
    {
        return $this->api("servers/{$serverId}/restart", [], 'post');
    }

    public function runScript(int $scriptId, array $serverIds): array
    {
        return $this->api("scripts/{$scriptId}/run", ['servers' => $serverIds], 'post');
    }

    public function api(string $path, array $data = [], string $method = 'get'): ?array
    {
        $path = Str::start($path, '/');

        return Http::withToken($this->token)
            ->{$method}("https://ploi.io/api{$path}", $data)
            ->throw()
            ->json('data');
    }
}
