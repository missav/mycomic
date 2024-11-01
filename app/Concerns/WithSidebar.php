<?php

namespace App\Concerns;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;

trait WithSidebar
{
    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('last_updated_on')->orderBy('id')->take(10)->get();
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('id')->take(10)->get();
    }
}
