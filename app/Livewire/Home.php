<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Home extends Component
{
    #[Computed]
    public function recentUpdatedComics(): Collection
    {
        return Comic::orderByDesc('last_updated_on')->take(12)->get();
    }

    #[Computed]
    public function recentPublishedComics(): Collection
    {
        return Comic::orderByDesc('id')->take(12)->get();
    }
}
