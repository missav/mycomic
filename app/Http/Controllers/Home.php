<?php

namespace App\Http\Controllers;

use App\Concerns\WithPresetComics;
use App\LinkHeader;
use App\Models\Comic;
use Illuminate\Contracts\View\View;

class Home
{
    use WithPresetComics;

    public function __invoke(): View
    {
        $featuredComics = $this->featuredComics();

        $featuredComics->each(fn (Comic $comic) =>
            LinkHeader::addPreconnect($comic->coverCdnUrl())
        );

        return view('home', [
            'featuredComics' => $featuredComics,
            'recentUpdatedComics' => $this->recentUpdatedComics(),
            'recentPublishedComics' => $this->recentPublishedComics(),
            'dailyRankComics' => $this->dailyRankComics(),
            'weeklyRankComics' => $this->weeklyRankComics(),
            'allTimeRankComics' => $this->allTimeRankComics(),
        ]);
    }
}
