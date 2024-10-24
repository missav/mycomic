<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ComicDetail extends Component
{
    public Comic $comic;

    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('last_updated_on')->take(10)->get();
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return Comic::with('recentChapter')->orderByDesc('id')->take(12)->get();
    }

    public function view(): void
    {
        $this->comic->increment('views');
    }
}
