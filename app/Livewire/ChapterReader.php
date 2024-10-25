<?php

namespace App\Livewire;

use App\Models\Chapter;
use App\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ChapterReader extends Component
{
    public Chapter $chapter;

    #[Computed]
    public function previousUrl(): ?string
    {
        return $this->chapter->previous()?->url();
    }

    #[Computed]
    public function nextUrl(): ?string
    {
        return $this->chapter->next()?->url();
    }

    public function render(): View
    {
        Seo::title($this->chapter->comic->name());
        Seo::description($this->chapter->comic->description());
        Seo::keywords($this->chapter->comic->keywords()->all());
        Seo::authors($this->chapter->comic->authors->pluck('name')->all());
        Seo::image($this->chapter->comic->coverCdnUrl());

        return view('livewire.chapter-reader');
    }
}
