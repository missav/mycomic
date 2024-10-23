<?php

namespace App\Recombee;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Recombee\RecommApi\Client;

class RecombeeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return new Client(
                config('services.recombee.database'),
                config('services.recombee.private_token'),
                ['baseUri' => config('services.recombee.base_uri')],
            );
        });
    }

    public function provides(): array
    {
        return [Client::class];
    }
}
