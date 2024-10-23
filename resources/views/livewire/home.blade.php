<div
    x-data='{
        comics: @json(\App\Http\Resources\ComicResource::collection($recentUpdatedComics)),
    }'
>
    <x-comic-section title="Recent update" :url="localizedRoute('comics.index')"></x-comic-section>
</div>
