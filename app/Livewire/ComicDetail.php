<?php

namespace App\Livewire;

use App\Models\Comic;
use App\Seo;
use Illuminate\Contracts\View\View;
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

    public function render(): View
    {
        Seo::title($this->comic->name());
        Seo::description($this->comic->description());
        Seo::keywords($this->comic->keywords()->all());
        Seo::authors($this->comic->authors->pluck('name')->all());
        Seo::image($this->comic->coverCdnUrl());

        return view('livewire.comic-detail');
    }
}
