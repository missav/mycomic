<?php

namespace App\Concerns;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

trait WithPresetComics
{
    #[Computed]
    public function dailyRankComics(): Collection
    {
        return cache()->remember('daily_rank_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('views_1d')->orderBy('id')->take(12)->get()
        );
    }

    #[Computed]
    public function weeklyRankComics(): Collection
    {
        return cache()->remember('weekly_rank_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('views_7d')->orderBy('id')->take(12)->get()
        );
    }

    #[Computed]
    public function monthlyRankComics(): Collection
    {
        return cache()->remember('monthly_rank_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('views_30d')->orderBy('id')->take(12)->get()
        );
    }

    #[Computed]
    public function allTimeRankComics(): Collection
    {
        return cache()->remember('alltime_rank_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('views')->orderBy('id')->take(12)->get()
        );
    }

    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return cache()->remember('recent_updated_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('last_updated_on')->orderBy('id')->take(12)->get()
        );
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return cache()->remember('recent_published_comics', 3600, fn () =>
            Comic::with('recentChapter')->has('chapters')->orderByDesc('id')->take(12)->get()
        );
    }
}
