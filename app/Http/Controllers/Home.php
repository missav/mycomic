<?php

namespace App\Http\Controllers;

use App\Concerns\WithPresetComics;
use Illuminate\Contracts\View\View;

class Home
{
    use WithPresetComics;

    public function __invoke(): View
    {
        return view('home', [
            'featuredComics' => $this->featuredComics(),
            'recentUpdatedComics' => $this->recentUpdatedComics(),
            'recentPublishedComics' => $this->recentPublishedComics(),
            'dailyRankComics' => $this->dailyRankComics(),
            'weeklyRankComics' => $this->weeklyRankComics(),
            'allTimeRankComics' => $this->allTimeRankComics(),
        ]);
    }
}
