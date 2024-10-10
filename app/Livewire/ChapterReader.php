<?php

namespace App\Livewire;

use App\Models\Chapter;
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
        return view('livewire.chapter-reader');
    }
}
