<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::unguard();

        Http::macro('proxy', function (): PendingRequest {
            return Http::withoutVerifying()->withOptions([
                'proxy' => [
                    'http'  => config('app.proxy_url'),
                    'https'  => config('app.proxy_url'),
                ],
            ]);
        });
    }
}
