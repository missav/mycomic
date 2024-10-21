<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ComicList extends Component
{
    use WithPagination;

    public function render(): View
    {
        $comics = QueryBuilder::for(Comic::class)
            ->allowedFilters([
                'country',
                'audience',
                AllowedFilter::callback('year', fn (Builder $query, string $value) =>
                    str_ends_with($value, 'x') ?
                        $query->where('year', 'LIKE', substr($value, 0, -1) . '%') :
                        $query->where('year', $value)
                ),
            ])
            ->orderByDesc('id')
            ->paginate(30);

        return view('livewire.comic-list', [
            'comics' => $comics,
        ]);
    }
}
