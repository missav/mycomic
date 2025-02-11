<?php

use App\Http\Middleware\AddEarlyHintsLinkHeaders;
use App\Http\Middleware\EncryptCookiesExceptUserUuid;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            AddEarlyHintsLinkHeaders::class,
        ], replace: [
            EncryptCookies::class => EncryptCookiesExceptUserUuid::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        \Spatie\LaravelFlare\Facades\Flare::handles($exceptions);
    })->create();
