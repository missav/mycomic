<?php

namespace App\Livewire;

use App\Models\Comic;
use Livewire\Component;

class ComicDetail extends Component
{
    public Comic $comic;

    public function view(): void
    {
        $this->comic->increment('views');
    }
}
