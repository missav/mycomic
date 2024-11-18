<?php

namespace App\Bunny;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\Psr18Client;
use ToshY\BunnyNet\BaseAPI;
use ToshY\BunnyNet\Client\BunnyClient;

class BunnyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(BaseAPI::class, function () {
            $bunnyClient = new BunnyClient(
                client: new Psr18Client(),
            );

            return new BaseAPI(
                apiKey: config('services.bunny.token'),
                client: $bunnyClient,
            );
        });
    }

    public function provides(): array
    {
        return [
            BaseAPI::class,
        ];
    }
}
