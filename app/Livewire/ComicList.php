<?php

namespace App\Livewire;

use App\Models\Comic;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ComicList extends Component
{
    public function render(): View
    {
        $comics = QueryBuilder::for(Comic::class)
            ->when(request('q'), fn (Builder $query, string $keyword) =>
                $query->where('name', 'LIKE', "%{$keyword}%")
            )
            ->allowedFilters([
                AllowedFilter::exact('country'),
                AllowedFilter::exact('audience'),
                AllowedFilter::exact('end', 'is_ended'),
                AllowedFilter::exact('tag', 'tags.slug'),
                AllowedFilter::callback('year', fn (Builder $query, string $value) =>
                    str_ends_with($value, 'x') ?
                        $query->where('year', 'LIKE', substr($value, 0, -1) . '%') :
                        $query->where('year', $value)
                ),
            ])
            ->allowedSorts([
                AllowedSort::field('publish', 'id'),
                AllowedSort::field('update', 'last_updated_on'),
                AllowedSort::field('views', 'views'),
            ])
            ->defaultSort('-id')
            ->orderByDesc('id')
            ->paginate(30)
            ->appends(request()->query());

        return view('livewire.comic-list', [
            'comics' => $comics,
        ]);
    }
}
