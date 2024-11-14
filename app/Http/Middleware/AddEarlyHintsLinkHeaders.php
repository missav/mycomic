<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use App\LinkHeader;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Symfony\Component\HttpFoundation\Response;

class AddEarlyHintsLinkHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET') || $request->expectsJson()) {
            return $response;
        }

        LinkHeader::addPreconnect(Vite::asset('resources/css/app.css'));
        LinkHeader::addPreconnect(Vite::asset('resources/js/app.js'));
        LinkHeader::addPreconnect(url(Str::betweenFirst(app('flux')->scripts(), 'src="', '"')));
        LinkHeader::addPreconnect(url(Str::betweenFirst(FrontendAssets::js([]), 'src="', '"')));
        LinkHeader::addPreconnect('https://fonts.bunny.net/css?family=inter:400,500,600&display=swap');

        foreach (Locale::cases() as $locale) {
            LinkHeader::addPreconnect($locale->flagUrl());
        }

        $response->withHeaders(LinkHeader::all());

        return $response;
    }
}
