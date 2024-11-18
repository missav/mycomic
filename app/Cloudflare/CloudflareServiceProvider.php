<?php

namespace App\Cloudflare;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CloudflareServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Cloudflare::class, function () {
            return new Cloudflare(config('services.cloudflare.token'));
        });
    }

    public function provides(): array
    {
        return [
            Cloudflare::class,
        ];
    }
}
