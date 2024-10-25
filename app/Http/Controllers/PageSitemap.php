<?php

namespace App\Http\Controllers;

use App\Concerns\InteractsWithSitemap;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;

class PageSitemap
{
    use InteractsWithSitemap;

    public function __invoke(): Response
    {
        $sitemap = Sitemap::create();

        $this->addLocalizedRoute($sitemap, 'home');
        $this->addLocalizedRoute($sitemap, 'comics.index');

        return response($sitemap->render(), headers: [
            'Content-Type' => 'text/xml',
        ]);
    }
}
