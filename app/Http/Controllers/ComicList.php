<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ComicList
{
    public function __invoke(): View
    {
        Seo::title(__('Comic database'));

        $comics = QueryBuilder::for(Comic::class)
            ->when(request('q'), fn (Builder $query, string $keyword) =>
                $query->orWhere('search_keywords', 'like', "%{$keyword}%")->orderBy('id')
            )
            ->allowedFilters([
                AllowedFilter::exact('country'),
                AllowedFilter::exact('audience'),
                AllowedFilter::exact('end', 'is_ended'),
                AllowedFilter::exact('tag', 'tags.slug'),
                AllowedFilter::exact('author', 'authors.name'),
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
            ->has('chapters')
            ->defaultSort('-id')
            ->paginate(30)
            ->appends(request()->query());

        return view('comic-list', [
            'comics' => $comics,
        ]);
    }
}
