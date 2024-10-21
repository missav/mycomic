<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ComicList extends Component
{
    use WithPagination;

    public function render(): View
    {
        return view('livewire.comic-list', [
            'comics' => Comic::orderByDesc('id')->paginate(30),
        ]);
    }
}
