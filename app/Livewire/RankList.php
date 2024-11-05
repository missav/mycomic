<?php

namespace App\Livewire;

use App\Models\Comic;
use App\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class RankList extends Component
{
    public function render(): View
    {
        Seo::title(__('Ranking'));

        $comics = QueryBuilder::for(Comic::class)
            ->with('authors', 'recentChapter')
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
                AllowedSort::field('day', 'views_1d'),
                AllowedSort::field('week', 'views_7d'),
                AllowedSort::field('month', 'views_30d'),
            ])
            ->has('chapters')
            ->defaultSort('-views_1d')
            ->orderBy('id')
            ->paginate(50);

        return view('livewire.rank-list', [
            'comics' => $comics,
        ]);
    }
}
