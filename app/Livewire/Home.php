<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Home extends Component
{
    public Collection $recentUpdatedComics;

    public function mount(): void
    {
        $this->recentUpdatedComics = Comic::orderByDesc('last_updated_on')->take(12)->get();
    }
}
